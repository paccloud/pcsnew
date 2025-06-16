<?php
/**
 * Pacific Cloud Seafood 25 functions and definitions
 * Updated to work with PCS Enhanced Recipes plugin
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Pacific_Cloud_Seafood_25
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue scripts and styles.
 */
function pcs25_enqueue_styles() {
    // Parent theme stylesheet is imported in style.css
    
    // Enqueue child theme stylesheet
    wp_enqueue_style( 'pcs25-style', 
        get_stylesheet_directory_uri() . '/style.css',
        array(), 
        wp_get_theme()->get( 'Version' )
    );
    
    // Enqueue custom navigation styles
    wp_enqueue_style( 'pcs25-custom-navigation', 
        get_stylesheet_directory_uri() . '/assets/css/custom-navigation.css',
        array(), 
        wp_get_theme()->get( 'Version' )
    );
    
    // Enqueue recipe styles
        // Select2 for filter dropdowns
    if ( is_post_type_archive( 'recipe' ) ) {
        wp_enqueue_style( 'select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), '4.1.0' );
        wp_enqueue_script( 'select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array( 'jquery' ), '4.1.0', true );
    }

    wp_enqueue_style( 'pcs25-recipe-styles', 
        get_stylesheet_directory_uri() . '/assets/css/recipe-styles.css',
        array(), 
        wp_get_theme()->get( 'Version' )
    );
    
    // Enqueue recipe print script for recipe pages
    if (is_singular('recipe') || is_post_type_archive('recipe')) {
        wp_enqueue_script(
            'pcs25-recipe-print',
            get_stylesheet_directory_uri() . '/assets/js/recipe-print.js',
            array('jquery'),
            wp_get_theme()->get('Version'),
            true
        );
    }
}
add_action( 'wp_enqueue_scripts', 'pcs25_enqueue_styles' );

/**
 * Init Select2 for archive filter
 */
function pcs25_init_select2_scripts() {
    if ( is_post_type_archive( 'recipe' ) ) {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if ( window.jQuery && jQuery.fn.select2 ) {
                    jQuery('.pcs-select2').select2({
                        width: 'resolve'
                    });
                }
            });
        </script>
        <?php
    }
}
add_action( 'wp_footer', 'pcs25_init_select2_scripts', 20 );

/**
 * Theme setup
 */
function pcs25_theme_setup() {
    // Add support for block patterns
    add_theme_support('core-block-patterns');
    
    // Add support for post thumbnails for recipes
    add_theme_support('post-thumbnails');
    
    // Add image sizes for recipes
    add_image_size('recipe-card', 400, 300, true);
    add_image_size('recipe-hero', 800, 400, true);
}
add_action('after_setup_theme', 'pcs25_theme_setup');

/**
 * Add print styles to head for recipe pages
 */
function pcs25_add_print_styles() {
    if (is_singular('recipe')) {
        ?>
        <style type="text/css" media="print">
            /* Hide non-recipe content when printing */
            header, footer, .site-header, .site-footer, 
            .comments-section, .sidebar, .wp-block-navigation,
            .recipe-social-share, .more-recipes, .recipe-actions,
            .recipe-taxonomies {
                display: none !important;
            }
            
            /* Format recipe content for printing */
            .wp-site-blocks {
                padding: 0 !important;
                margin: 0 !important;
            }
            
            .wp-block-post-content {
                width: 100% !important;
                max-width: 100% !important;
            }
            
            /* Style adjustments for print */
            .recipe-meta {
                background: none !important;
                border: 1px solid #ccc;
                page-break-inside: avoid;
            }
            
            .recipe-meta-item {
                display: inline-block;
                margin-right: 15px;
            }
            
            .recipe-ingredients,
            .recipe-instructions {
                page-break-inside: avoid;
            }
            
            .recipe-ingredients ul {
                padding-left: 20px;
            }
            
            .recipe-instructions ol {
                padding-left: 30px;
            }
            
            body {
                font-size: 12pt;
                line-height: 1.5;
                color: #000;
            }
            
            h1 {
                font-size: 18pt;
                margin-bottom: 10px;
            }
            
            h2, h3 {
                font-size: 14pt;
                margin: 15px 0 10px;
            }
            
            .recipe-featured-image {
                max-height: 200px;
                overflow: hidden;
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'pcs25_add_print_styles');

/**
 * Add print button functionality for recipes
 */
function pcs25_add_recipe_print_script() {
    if (is_singular('recipe')) {
        ?>
        <script>
        jQuery(document).ready(function($) {
            // Add print button to recipe if it doesn't exist
            if ($('.recipe-print-button').length === 0) {
                var printButton = '<div class="recipe-print-button-container" style="margin: 1rem 0; text-align: center;">' +
                    '<button class="recipe-print-button" onclick="window.print()" style="background: #d65734; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">' +
                    '🖨️ Print Recipe' +
                    '</button>' +
                    '</div>';
                $('.recipe-meta').after(printButton);
            }
        });
        </script>
        <?php
    }
}
add_action('wp_footer', 'pcs25_add_recipe_print_script');

/**
 * Improve recipe archive query
 */
function pcs25_recipe_archive_query($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (is_post_type_archive('recipe')) {
            $query->set('posts_per_page', 12);
            $query->set('orderby', 'date');
            $query->set('order', 'DESC');
        }
    }
}
add_action('pre_get_posts', 'pcs25_recipe_archive_query');

/**
 * Add body class for recipe pages
 */
function pcs25_recipe_body_class($classes) {
    if (is_singular('recipe')) {
        $classes[] = 'recipe-single';
    }
    if (is_post_type_archive('recipe')) {
        $classes[] = 'recipe-archive';
    }
    return $classes;
}
add_filter('body_class', 'pcs25_recipe_body_class');

/**
 * Recipe excerpt length
 */
function pcs25_recipe_excerpt_length($length) {
    if (is_post_type_archive('recipe') || get_post_type() === 'recipe') {
        return 20;
    }
    return $length;
}
add_filter('excerpt_length', 'pcs25_recipe_excerpt_length');

/**
 * Add recipe structured data (JSON-LD) for SEO
 */
function pcs25_add_recipe_structured_data() {
    if (is_singular('recipe')) {
        global $post;
        
        $prep_time = get_post_meta($post->ID, '_pcs_prep_time', true);
        $cook_time = get_post_meta($post->ID, '_pcs_cook_time', true);
        $servings = get_post_meta($post->ID, '_pcs_servings', true);
        $ingredients = get_post_meta($post->ID, '_pcs_ingredients', true);
        $instructions = get_post_meta($post->ID, '_pcs_instructions', true);
        $calories = get_post_meta($post->ID, '_pcs_calories', true);
        
        // Parse ingredients
        $ingredient_list = array();
        if ($ingredients) {
            preg_match_all('/<li>(.*?)<\/li>/', $ingredients, $matches);
            if (!empty($matches[1])) {
                $ingredient_list = array_map('strip_tags', $matches[1]);
            }
        }
        
        // Parse instructions
        $instruction_list = array();
        if ($instructions) {
            preg_match_all('/<li>(.*?)<\/li>/', $instructions, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $index => $instruction) {
                    $instruction_list[] = array(
                        '@type' => 'HowToStep',
                        'name' => 'Step ' . ($index + 1),
                        'text' => strip_tags($instruction)
                    );
                }
            }
        }
        
        $schema = array(
            '@context' => 'https://schema.org/',
            '@type' => 'Recipe',
            'name' => get_the_title(),
            'description' => get_the_excerpt(),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author()
            ),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'prepTime' => $prep_time ? 'PT' . $prep_time . 'M' : null,
            'cookTime' => $cook_time ? 'PT' . $cook_time . 'M' : null,
            'recipeYield' => $servings ? $servings . ' servings' : null,
            'recipeIngredient' => $ingredient_list,
            'recipeInstructions' => $instruction_list,
            'nutrition' => $calories ? array(
                '@type' => 'NutritionInformation',
                'calories' => $calories . ' calories'
            ) : null,
        );
        
        // Add image if available
        if (has_post_thumbnail()) {
            $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
            if ($image) {
                $schema['image'] = $image[0];
            }
        }
        
        // Remove null values
        $schema = array_filter($schema, function($value) {
            return $value !== null && $value !== array();
        });
        
        echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>';
    }
}
add_action('wp_head', 'pcs25_add_recipe_structured_data');

/**
 * Fix navigation menu to ensure Recipe archive link works
 */
function pcs25_fix_recipe_menu_link($items, $args) {
    // Replace any "Recipes" page links with recipe archive link
    $items = str_replace(
        'href="' . home_url('/recipes/') . '"',
        'href="' . home_url('/recipe/') . '"',
        $items
    );
    return $items;
}
add_filter('wp_nav_menu_items', 'pcs25_fix_recipe_menu_link', 10, 2);

/**
 * Custom walker to highlight recipe archive in navigation
 */
function pcs25_nav_menu_css_class($classes, $item, $args) {
    if (is_post_type_archive('recipe') && $item->url === home_url('/recipe/')) {
        $classes[] = 'current-menu-item';
    }
    return $classes;
}
add_filter('nav_menu_css_class', 'pcs25_nav_menu_css_class', 10, 3);

/**
 * Redirect /recipes/ to /recipe/
 * This ensures users always see the dynamic recipe archive
 */
function pcs25_redirect_recipes_to_recipe() {
    if (is_404()) {
        $current_url = $_SERVER['REQUEST_URI'];
        
        // Check if we're on /recipes/ (with or without trailing slash)
        if (preg_match('/^\/recipes\/?$/', $current_url)) {
            // Redirect to the correct recipe archive URL
            wp_redirect(home_url('/recipe/'), 301);
            exit;
        }
    }
}
add_action('template_redirect', 'pcs25_redirect_recipes_to_recipe');

/**
 * Alternative redirect using init hook for earlier execution
 * This catches the request before WordPress decides it's a 404
 */
function pcs25_early_recipe_redirect() {
    $request_uri = $_SERVER['REQUEST_URI'];
    
    // Remove query string for comparison
    $path = parse_url($request_uri, PHP_URL_PATH);
    
    // Check if we're on /recipes/ (with or without trailing slash)
    if ($path === '/recipes' || $path === '/recipes/') {
        // Redirect to the correct recipe archive URL
        wp_redirect(home_url('/recipe/'), 301);
        exit;
    }
}
add_action('init', 'pcs25_early_recipe_redirect', 1);

/**
 * Add rewrite rule to handle /recipes/ -> /recipe/ redirect
 * This is a more robust solution that works with WordPress rewrite system
 */
function pcs25_add_recipe_rewrite_rules() {
    add_rewrite_rule('^recipes/?$', 'index.php?post_type=recipe', 'top');
}
add_action('init', 'pcs25_add_recipe_rewrite_rules');

/**
 * Flush rewrite rules on theme activation
 * This ensures our custom rewrite rules are registered
 */
function pcs25_flush_rewrite_rules() {
    pcs25_add_recipe_rewrite_rules();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'pcs25_flush_rewrite_rules');

/**
 * Add Recipes link to navigation using JavaScript
 * This is a temporary fix to ensure the Recipes link appears in the navigation
 */
function pcs25_add_recipes_to_nav() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Find the navigation block
        const navBlock = document.querySelector('.wp-block-navigation__container');
        if (navBlock) {
            // Check if Recipes link already exists
            const links = navBlock.querySelectorAll('a');
            let hasRecipes = false;
            links.forEach(link => {
                if (link.textContent.toLowerCase().includes('recipe')) {
                    hasRecipes = true;
                }
            });
            
            // If no recipes link, add it after About Us
            if (!hasRecipes) {
                const aboutLink = Array.from(links).find(link => link.textContent.includes('About'));
                if (aboutLink && aboutLink.parentElement) {
                    const recipesLi = document.createElement('li');
                    recipesLi.className = 'wp-block-navigation-item wp-block-navigation-link';
                    recipesLi.innerHTML = '<a class="wp-block-navigation-item__content" href="/recipe/"><span class="wp-block-navigation-item__label">Recipes</span></a>';
                    
                    // Insert after About Us
                    aboutLink.parentElement.parentElement.insertBefore(recipesLi, aboutLink.parentElement.nextSibling);
                }
            }
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'pcs25_add_recipes_to_nav');

/**
 * Filter navigation block to add Recipes link
 * This modifies the navigation block output to include the Recipes link
 */
function pcs25_filter_navigation_block($block_content, $block) {
    // Only modify navigation blocks
    if ($block['blockName'] === 'core/navigation') {
        // Check if this is the main navigation (not social links)
        if (strpos($block_content, 'About Us') !== false && strpos($block_content, 'Recipes') === false) {
            // Add Recipes link after About Us
            $recipes_link = '<li class="wp-block-navigation-item wp-block-navigation-link"><a class="wp-block-navigation-item__content" href="' . home_url('/recipe/') . '"><span class="wp-block-navigation-item__label">Recipes</span></a></li>';
            
            // Find the About Us link and insert Recipes after it
            $pattern = '/<li[^>]*>.*?About Us.*?<\/li>/i';
            $replacement = '$0' . $recipes_link;
            $block_content = preg_replace($pattern, $replacement, $block_content);
        }
    }
    
    return $block_content;
}
add_filter('render_block', 'pcs25_filter_navigation_block', 10, 2);
