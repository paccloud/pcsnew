<?php
/**
 * Recipe Template Class
 * 
 * Handles recipe template functionality
 * Compatible with block themes
 * 
 * @package PCS_Enhanced_Recipes
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Recipe Template Class
 */
class PCS_Recipe_Template {
    /**
     * Constructor
     */
    public function __construct() {
        // For block themes, add template handling
        // For block themes, rely on default template hierarchy so that the active theme (or other filters)
        // can handle header/footer output. The custom block-theme override caused header/footer-only output.
        // add_filter('template_include', array($this, 'block_theme_template'), 999);
        
        // For classic themes, use template filters
        add_filter('single_template', array($this, 'recipe_single_template'));
        add_filter('archive_template', array($this, 'recipe_archive_template'));
        add_filter('taxonomy_template', array($this, 'recipe_taxonomy_template'));
        
        // Enqueue scripts and styles for all theme types
        add_action('wp_enqueue_scripts', array($this, 'enqueue_recipe_assets'));

        // Add AJAX handlers for unit conversion
        add_action('wp_ajax_convert_recipe_units', array($this, 'convert_recipe_units'));
        add_action('wp_ajax_nopriv_convert_recipe_units', array($this, 'convert_recipe_units'));
    }
    
    /**
     * Check if using a block theme
     */
    public function is_using_block_theme() {
        // WordPress 5.9+ has function to check for block themes
        if (function_exists('wp_is_block_theme')) {
            return wp_is_block_theme();
        }
        
        // Fallback check for older WordPress versions
        return get_theme_support('block-templates');
    }
    
    /**
     * Register recipe meta in REST API
     */
    public function register_recipe_meta_in_rest() {
        register_post_meta('recipe', '_pcs_prep_time', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
        ));
        
        register_post_meta('recipe', '_pcs_cook_time', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
        ));
        
        register_post_meta('recipe', '_pcs_servings', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
        ));
        
        register_post_meta('recipe', '_pcs_ingredients', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
        ));
    }
    
    /**
     * Enqueue recipe assets (scripts and styles)
     */
    public function enqueue_recipe_assets() {
        // Only enqueue on recipe pages
        if (!is_singular('recipe') && !is_post_type_archive('recipe') && !is_tax(array('recipe_category', 'recipe_tag'))) {
            return;
        }
        
        // Enqueue styles
        wp_enqueue_style(
            'pcs-recipe-styles',
            PCS_ER_PLUGIN_URL . 'assets/css/recipe-styles.css',
            array(),
            PCS_ER_VERSION
        );
        
        // Enqueue scripts
        wp_enqueue_script(
            'pcs-recipe-scripts',
            PCS_ER_PLUGIN_URL . 'assets/js/recipe-scripts.js',
            array('jquery'),
            PCS_ER_VERSION,
            true
        );
        
        // Localize script with data
        wp_localize_script(
            'pcs-recipe-scripts',
            'pcsRecipeData',
            array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('pcs-recipe-nonce'),
                'isBlockTheme' => $this->is_using_block_theme() ? '1' : '0',
            )
        );
    }
    
    /**
     * Single recipe template (for both classic and block themes)
     */
    public function recipe_single_template($template) {
        if (is_singular('recipe')) {
            $custom_template = PCS_ER_PLUGIN_DIR . 'templates/single-recipe.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        return $template;
    }
    
    /**
     * Recipe archive template (for both classic and block themes)
     */
    public function recipe_archive_template($template) {
        // If using a block theme, let the theme (or wp-template hierarchy) handle the archive.
        if ( $this->is_using_block_theme() ) {
            return $template;
        }
        if (is_post_type_archive('recipe')) {
            $custom_template = PCS_ER_PLUGIN_DIR . 'templates/archive-recipe.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        return $template;
    }
    
    /**
     * Recipe taxonomy template (for both classic and block themes)
     */
    public function recipe_taxonomy_template($template) {
        if (is_tax('recipe_category') || is_tax('recipe_tag')) {
            $custom_template = PCS_ER_PLUGIN_DIR . 'templates/taxonomy-recipe.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        return $template;
    }
    
    /**
     * Block theme template handler
     */
    public function block_theme_template($template) {
        if (is_singular('recipe')) {
            $content_template = PCS_ER_PLUGIN_DIR . 'templates/single-recipe-content.php';
        } elseif (is_post_type_archive('recipe')) {
            $content_template = PCS_ER_PLUGIN_DIR . 'templates/archive-recipe.php';
        } else {
            return $template;
        }

        if (!file_exists($content_template)) {
            return $template;
        }

        // Check if required block theme functions exist
        if (!function_exists('get_block_template_html')) {
            // Fallback to classic template if block theme functions aren't available
            return $content_template;
        }

        // Get block template parts
        $header_template = get_block_template_html('header');
        $footer_template = get_block_template_html('footer');
        
        // If either template part is missing, fallback to classic template
        if (empty($header_template) || empty($footer_template)) {
            return $content_template;
        }
        
        // Start output buffering
        ob_start();
        
        // Include our recipe template
        include $content_template;
        
        // Get the recipe content
        $recipe_content = ob_get_clean();
        
        // Output the complete page
        echo $header_template;
        echo $recipe_content;
        echo $footer_template;
        
        // Exit to prevent double output
        exit;
    }
    
    /**
     * Convert recipe units
     */
    private function convert_units($value, $from_unit, $to_system = 'metric') {
        $conversions = array(
            'us_to_metric' => array(
                'cup' => array('ml', 236.588),
                'cups' => array('ml', 236.588),
                'tbsp' => array('ml', 14.7868),
                'tsp' => array('ml', 4.92892),
                'oz' => array('g', 28.3495),
                'pound' => array('g', 453.592),
                'pounds' => array('g', 453.592),
                'lb' => array('g', 453.592),
                'lbs' => array('g', 453.592),
                'inch' => array('cm', 2.54),
                'inches' => array('cm', 2.54),
                'in' => array('cm', 2.54),
                'fahrenheit' => array('celsius', 'special'),
            ),
            'metric_to_us' => array(
                'ml' => array('cups', 0.00422675),
                'g' => array('oz', 0.035274),
                'kg' => array('pounds', 2.20462),
                'cm' => array('inches', 0.393701),
                'celsius' => array('fahrenheit', 'special'),
            )
        );

        // Handle temperature conversion specially
        if ($from_unit === 'fahrenheit' && $to_system === 'metric') {
            return round(($value - 32) * 5/9) . '°C';
        } elseif ($from_unit === 'celsius' && $to_system === 'us') {
            return round(($value * 9/5) + 32) . '°F';
        }

        // Handle regular unit conversions
        $conversion_table = $to_system === 'metric' ? $conversions['us_to_metric'] : $conversions['metric_to_us'];
        
        if (isset($conversion_table[$from_unit])) {
            list($to_unit, $factor) = $conversion_table[$from_unit];
            $converted = $value * $factor;
            
            // Round appropriately
            if ($converted < 1) {
                $converted = round($converted, 2);
            } else if ($converted < 10) {
                $converted = round($converted, 1);
            } else {
                $converted = round($converted);
            }
            
            return array(
                'value' => $converted,
                'unit' => $to_unit
            );
        }
        
        return false;
    }

    /**
     * AJAX handler for unit conversion
     */
    public function convert_recipe_units() {
        check_ajax_referer('pcs-recipe-nonce', 'nonce');
        
        $value = floatval($_POST['value']);
        $unit = sanitize_text_field($_POST['unit']);
        $to_system = sanitize_text_field($_POST['to_system']);
        
        $result = $this->convert_units($value, $unit, $to_system);
        
        if ($result) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error('Invalid conversion');
        }
        
        wp_die();
    }
    
    /**
     * Register recipe block template parts
     */
    public function register_recipe_block_template_parts() {
        $templates = array(
            'single-recipe' => array(
                'title' => __('Single Recipe', 'pcs-enhanced-recipes'),
                'content' => '<!-- wp:template-part {"slug":"header"} /-->
                <!-- wp:group {"tagName":"main","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
                <main class="wp-block-group" style="margin-top:var(--wp--preset--spacing--50)">
                    <!-- wp:post-title {"level":1,"align":"wide"} /-->
                    <!-- wp:post-content {"align":"wide"} /-->
                </main>
                <!-- /wp:group -->
                <!-- wp:template-part {"slug":"footer"} /-->'
            )
        );

        foreach ($templates as $slug => $template) {
            $template_path = 'wp-content/plugins/pcs-enhanced-recipes/templates/' . $slug . '.html';
            
            // Register the template
            register_block_pattern(
                'pcs-enhanced-recipes/' . $slug,
                array(
                    'title' => $template['title'],
                    'content' => $template['content'],
                    'categories' => array('recipe'),
                )
            );
        }
    }
}
