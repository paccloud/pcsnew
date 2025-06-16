<?php
/**
 * Recipe Assets Class
 * 
 * Handles recipe CSS and JS assets
 * Compatible with block themes
 * 
 * @package PCS_Enhanced_Recipes
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Recipe Assets Class
 */
class PCS_Recipe_Assets {
    /**
     * Constructor
     */
    public function __construct() {
        // Initialize hooks
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }
    
    /**
     * Check if using a block theme
     */
    private function is_using_block_theme() {
        // WordPress 5.9+ has function to check for block themes
        if (function_exists('wp_is_block_theme')) {
            return wp_is_block_theme();
        }
        
        // Fallback check for older WordPress versions
        return get_theme_support('block-templates');
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        // Only load on recipe pages
        if (!is_singular('recipe') && !is_post_type_archive('recipe') && !is_tax('recipe_category') && !is_tax('recipe_tag') && !is_tax('recipe_fish_type') && !is_tax('recipe_meal_type') && !is_tax('recipe_technique') && !is_tax('recipe_difficulty')) {
            return;
        }
        
        // For block themes, we'll use a more targeted approach to avoid conflicts
        if ($this->is_using_block_theme()) {
            // Enqueue block-compatible styles with high specificity and limited scope
            wp_enqueue_style(
                'pcs-recipe-block-styles',
                PCS_ER_PLUGIN_URL . 'assets/css/recipe-styles.css',
                array(),
                PCS_ER_VERSION
            );
            
            // Add inline CSS to restrict styles to recipe content only and avoid header/layout conflicts
            $inline_css = "
                /* Restrict plugin styles to recipe content only */
                .pcs-recipe-card,
                .pcs-recipe-grid,
                .pcs-recipe-details {
                    margin: 0 auto;
                    width: 100%;
                    box-sizing: border-box;
                }
                

            ";
            
            wp_add_inline_style('pcs-recipe-block-styles', $inline_css);
        } else {
            // For classic themes, use the original approach
            wp_enqueue_style(
                'pcs-recipe-styles',
                PCS_ER_PLUGIN_URL . 'assets/css/recipe-styles.css',
                array(),
                PCS_ER_VERSION
            );
        }
        
        // Enqueue Select2 CSS and JS for recipe archive and taxonomy pages
        if (is_post_type_archive('recipe') || is_tax(array('recipe_category', 'recipe_tag', 'recipe_fish_type', 'recipe_meal_type', 'recipe_technique', 'recipe_difficulty'))) {
            // Select2 CSS
            wp_enqueue_style(
                'select2-css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                array(),
                '4.1.0-rc.0'
            );
            
            // Select2 JS
            wp_enqueue_script(
                'select2-js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                array('jquery'),
                '4.1.0-rc.0',
                true
            );
            
            // Recipe filter JS
            wp_enqueue_script(
                'pcs-recipe-filter',
                PCS_ER_PLUGIN_URL . 'assets/js/recipe-filter.js',
                array('jquery', 'select2-js'),
                PCS_ER_VERSION,
                true
            );
        }
        
        // Enqueue JS
        wp_enqueue_script(
            'pcs-recipe-scripts',
            PCS_ER_PLUGIN_URL . 'assets/js/recipe-scripts.js',
            array('jquery'),
            PCS_ER_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script(
            'pcs-recipe-scripts',
            'pcsRecipeData',
            array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('pcs-recipe-nonce'),
                'isBlockTheme' => $this->is_using_block_theme(),
            )
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        global $post_type;
        
        // Only load on recipe edit screens
        if (!('post.php' === $hook || 'post-new.php' === $hook) || 'recipe' !== $post_type) {
            return;
        }
        
        // Enqueue CSS
        wp_enqueue_style(
            'pcs-recipe-admin-styles',
            PCS_ER_PLUGIN_URL . 'assets/css/recipe-admin-styles.css',
            array(),
            PCS_ER_VERSION
        );
        
        // Enqueue JS
        wp_enqueue_script(
            'pcs-recipe-admin-scripts',
            PCS_ER_PLUGIN_URL . 'assets/js/recipe-admin-scripts.js',
            array('jquery'),
            PCS_ER_VERSION,
            true
        );
    }
}