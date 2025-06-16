<?php
/**
 * Recipe Shortcodes Class
 * 
 * Handles recipe shortcodes
 * 
 * @package PCS_Enhanced_Recipes
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Recipe Shortcodes Class
 */
class PCS_Recipe_Shortcodes {
    /**
     * Constructor
     */
    public function __construct() {
        // Register shortcodes
        add_shortcode('pcs_recipe', array($this, 'recipe_shortcode'));
        add_shortcode('pcs_recipe_grid', array($this, 'recipe_grid_shortcode'));
    }
    
    /**
     * Recipe shortcode
     */
    public function recipe_shortcode($atts) {
        $atts = shortcode_atts(
            array(
                'id' => 0,
                'show_image' => 'yes',
                'show_details' => 'yes',
                'show_ingredients' => 'yes',
                'show_instructions' => 'yes'
            ),
            $atts,
            'pcs_recipe'
        );
        
        // Get recipe post
        $recipe = get_post($atts['id']);
        
        if (!$recipe || 'recipe' !== $recipe->post_type) {
            return '<p>' . __('Recipe not found.', 'pcs-enhanced-recipes') . '</p>';
        }
        
        // Get recipe data
        $prep_time = get_post_meta($recipe->ID, '_pcs_prep_time', true);
        $cook_time = get_post_meta($recipe->ID, '_pcs_cook_time', true);
        $total_time = get_post_meta($recipe->ID, '_pcs_total_time', true);
        $servings = get_post_meta($recipe->ID, '_pcs_servings', true);
        
        // Start output
        ob_start();
        
        echo '<div class="pcs-recipe-card">';
        
        // Recipe title
        echo '<h2 class="pcs-recipe-title">' . esc_html($recipe->post_title) . '</h2>';
        
        // Recipe image
        if ('yes' === $atts['show_image'] && has_post_thumbnail($recipe->ID)) {
            echo '<div class="pcs-recipe-image">';
            echo get_the_post_thumbnail($recipe->ID, 'large');
            echo '</div>';
        }
        
        // Recipe details
        if ('yes' === $atts['show_details']) {
            echo '<div class="pcs-recipe-details">';
            
            if (!empty($prep_time)) {
                echo '<div class="pcs-recipe-detail">';
                echo '<span class="pcs-recipe-detail-label">' . __('Prep Time:', 'pcs-enhanced-recipes') . '</span> ';
                echo '<span class="pcs-recipe-detail-value">' . esc_html($prep_time) . ' ' . __('minutes', 'pcs-enhanced-recipes') . '</span>';
                echo '</div>';
            }
            
            if (!empty($cook_time)) {
                echo '<div class="pcs-recipe-detail">';
                echo '<span class="pcs-recipe-detail-label">' . __('Cook Time:', 'pcs-enhanced-recipes') . '</span> ';
                echo '<span class="pcs-recipe-detail-value">' . esc_html($cook_time) . ' ' . __('minutes', 'pcs-enhanced-recipes') . '</span>';
                echo '</div>';
            }
            
            if (!empty($total_time)) {
                echo '<div class="pcs-recipe-detail">';
                echo '<span class="pcs-recipe-detail-label">' . __('Total Time:', 'pcs-enhanced-recipes') . '</span> ';
                echo '<span class="pcs-recipe-detail-value">' . esc_html($total_time) . ' ' . __('minutes', 'pcs-enhanced-recipes') . '</span>';
                echo '</div>';
            }
            
            if (!empty($servings)) {
                echo '<div class="pcs-recipe-detail">';
                echo '<span class="pcs-recipe-detail-label">' . __('Servings:', 'pcs-enhanced-recipes') . '</span> ';
                echo '<span class="pcs-recipe-detail-value">' . esc_html($servings) . '</span>';
                echo '</div>';
            }
            
            echo '</div>';
        }
        
        // Recipe content
        echo '<div class="pcs-recipe-content">';
        echo apply_filters('the_content', $recipe->post_content);
        echo '</div>';
        
        echo '</div>';
        
        return ob_get_clean();
    }
    
    /**
     * Recipe grid shortcode
     */
    public function recipe_grid_shortcode($atts) {
        $atts = shortcode_atts(
            array(
                'limit' => 6,
                'columns' => 3,
                'category' => '',
                'tag' => '',
                'orderby' => 'date',
                'order' => 'DESC'
            ),
            $atts,
            'pcs_recipe_grid'
        );
        
        // Query args
        $args = array(
            'post_type' => 'recipe',
            'posts_per_page' => $atts['limit'],
            'orderby' => $atts['orderby'],
            'order' => $atts['order']
        );
        
        // Add category if specified
        if (!empty($atts['category'])) {
            $args['tax_query'][] = array(
                'taxonomy' => 'recipe_category',
                'field' => 'slug',
                'terms' => explode(',', $atts['category'])
            );
        }
        
        // Add tag if specified
        if (!empty($atts['tag'])) {
            $args['tax_query'][] = array(
                'taxonomy' => 'recipe_tag',
                'field' => 'slug',
                'terms' => explode(',', $atts['tag'])
            );
        }
        
        // Get recipes
        $recipes = new WP_Query($args);
        
        // Start output
        ob_start();
        
        if ($recipes->have_posts()) {
            echo '<div class="pcs-recipe-grid pcs-recipe-grid-' . esc_attr($atts['columns']) . '-col">';
            
            while ($recipes->have_posts()) {
                $recipes->the_post();
                
                echo '<div class="pcs-recipe-grid-item">';
                
                // Recipe image
                if (has_post_thumbnail()) {
                    echo '<div class="pcs-recipe-grid-image">';
                    echo '<a href="' . get_permalink() . '">';
                    the_post_thumbnail('medium');
                    echo '</a>';
                    echo '</div>';
                }
                
                // Recipe title
                echo '<h3 class="pcs-recipe-grid-title">';
                echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
                echo '</h3>';
                
                // Recipe excerpt
                echo '<div class="pcs-recipe-grid-excerpt">';
                the_excerpt();
                echo '</div>';
                
                // Read more link
                echo '<a href="' . get_permalink() . '" class="pcs-recipe-grid-link">';
                _e('View Recipe', 'pcs-enhanced-recipes');
                echo '</a>';
                
                echo '</div>';
            }
            
            echo '</div>';
        } else {
            echo '<p>' . __('No recipes found.', 'pcs-enhanced-recipes') . '</p>';
        }
        
        wp_reset_postdata();
        
        return ob_get_clean();
    }
}
