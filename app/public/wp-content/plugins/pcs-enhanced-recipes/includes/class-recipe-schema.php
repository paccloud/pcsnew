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
        
        // Get nutrition data
        $calories = get_post_meta($post->ID, '_pcs_calories', true);
        $protein = get_post_meta($post->ID, '_pcs_protein', true);
        $carbohydrates = get_post_meta($post->ID, '_pcs_carbohydrates', true);
        $fat = get_post_meta($post->ID, '_pcs_fat', true);
        $saturated_fat = get_post_meta($post->ID, '_pcs_saturated_fat', true);
        $cholesterol = get_post_meta($post->ID, '_pcs_cholesterol', true);
        $sodium = get_post_meta($post->ID, '_pcs_sodium', true);
        $fiber = get_post_meta($post->ID, '_pcs_fiber', true);
        $sugar = get_post_meta($post->ID, '_pcs_sugar', true);
        
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
        
        // Add nutrition information
        $nutrition = array('@type' => 'NutritionInformation');
        $has_nutrition = false;
        
        if (!empty($calories)) {
            $nutrition['calories'] = $calories . ' calories';
            $has_nutrition = true;
        }
        
        if (!empty($protein)) {
            $nutrition['proteinContent'] = $protein . ' g';
            $has_nutrition = true;
        }
        
        if (!empty($carbohydrates)) {
            $nutrition['carbohydrateContent'] = $carbohydrates . ' g';
            $has_nutrition = true;
        }
        
        if (!empty($fat)) {
            $nutrition['fatContent'] = $fat . ' g';
            $has_nutrition = true;
        }
        
        if (!empty($saturated_fat)) {
            $nutrition['saturatedFatContent'] = $saturated_fat . ' g';
            $has_nutrition = true;
        }
        
        if (!empty($cholesterol)) {
            $nutrition['cholesterolContent'] = $cholesterol . ' mg';
            $has_nutrition = true;
        }
        
        if (!empty($sodium)) {
            $nutrition['sodiumContent'] = $sodium . ' mg';
            $has_nutrition = true;
        }
        
        if (!empty($fiber)) {
            $nutrition['fiberContent'] = $fiber . ' g';
            $has_nutrition = true;
        }
        
        if (!empty($sugar)) {
            $nutrition['sugarContent'] = $sugar . ' g';
            $has_nutrition = true;
        }
        
        // Only add nutrition to schema if we have at least one nutrition value
        if ($has_nutrition) {
            $schema['nutrition'] = $nutrition;
        }
        
        // Output schema
        echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>';
    }
}
