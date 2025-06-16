<?php
/**
 * Recipe Schema Class
 * 
 * Handles recipe schema.org markup
 * 
 * @package PCS_Enhanced_Recipes
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Recipe Schema Class
 */
class PCS_Recipe_Schema {
    /**
     * Constructor
     */
    public function __construct() {
        // Initialize hooks
        add_action('wp_head', array($this, 'output_recipe_schema'), 10);
    }
    
    /**
     * Output recipe schema
     */
    public function output_recipe_schema() {
        if (!is_singular('recipe')) {
            return;
        }
        
        global $post;
        
        // Get recipe data
        $prep_time = get_post_meta($post->ID, '_pcs_prep_time', true);
        $cook_time = get_post_meta($post->ID, '_pcs_cook_time', true);
        $total_time = get_post_meta($post->ID, '_pcs_total_time', true);
        $servings = get_post_meta($post->ID, '_pcs_servings', true);
        $calories = get_post_meta($post->ID, '_pcs_calories', true);
        
        // Build schema
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Recipe',
            'name' => get_the_title(),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author_meta('display_name')
            ),
            'datePublished' => get_the_date('c'),
            'description' => get_the_excerpt(),
            'image' => get_the_post_thumbnail_url($post->ID, 'full')
        );
        
        // Add prep time if available
        if (!empty($prep_time)) {
            $schema['prepTime'] = 'PT' . $prep_time . 'M';
        }
        
        // Add cook time if available
        if (!empty($cook_time)) {
            $schema['cookTime'] = 'PT' . $cook_time . 'M';
        }
        
        // Add total time if available
        if (!empty($total_time)) {
            $schema['totalTime'] = 'PT' . $total_time . 'M';
        }
        
        // Add servings if available
        if (!empty($servings)) {
            $schema['recipeYield'] = $servings . ' servings';
        }
        
        // Add nutrition if calories are available
        if (!empty($calories)) {
            $schema['nutrition'] = array(
                '@type' => 'NutritionInformation',
                'calories' => $calories . ' calories'
            );
        }
        
        // Output schema
        echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>';
    }
}
