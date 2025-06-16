<?php
/**
 * Recipe Query Class
 * 
 * Handles recipe custom query modification for filtering
 * 
 * @package PCS_Enhanced_Recipes
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Recipe Query Class
 */
class PCS_Recipe_Query {
    /**
     * Constructor
     */
    public function __construct() {
        // Initialize hooks
        add_action('pre_get_posts', array($this, 'modify_recipe_query'));
    }
    
    /**
     * Modify the recipe query to handle custom filtering
     *
     * @param WP_Query $query The WordPress query object
     */
    public function modify_recipe_query($query) {
        // Only run on the main query for recipe archives and taxonomies
        if (!is_admin() && $query->is_main_query() && 
            ($query->is_post_type_archive('recipe') || 
             $query->is_tax('recipe_category') || 
             $query->is_tax('recipe_tag') || 
             $query->is_tax('recipe_fish_type') || 
             $query->is_tax('recipe_meal_type') || 
             $query->is_tax('recipe_technique') || 
             $query->is_tax('recipe_difficulty'))) {
            
            // Handle keyword search
            if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
                $query->set('s', sanitize_text_field($_GET['keyword']));
            }
            
            // Initialize tax query
            $tax_query = array();
            
            // Default relation for taxonomy filters
            $tax_query['relation'] = 'AND';
            
            // Handle fish type filter
            if (isset($_GET['fish_type']) && !empty($_GET['fish_type'])) {
                $fish_types = array_map('sanitize_text_field', (array) $_GET['fish_type']);
                if (!empty($fish_types) && $fish_types[0] !== '') {
                    $tax_query[] = array(
                        'taxonomy' => 'recipe_fish_type',
                        'field'    => 'slug',
                        'terms'    => $fish_types,
                        'operator' => 'IN',
                    );
                }
            }
            
            // Handle meal type filter
            if (isset($_GET['meal_type']) && !empty($_GET['meal_type'])) {
                $meal_types = array_map('sanitize_text_field', (array) $_GET['meal_type']);
                if (!empty($meal_types) && $meal_types[0] !== '') {
                    $tax_query[] = array(
                        'taxonomy' => 'recipe_meal_type',
                        'field'    => 'slug',
                        'terms'    => $meal_types,
                        'operator' => 'IN',
                    );
                }
            }
            
            // Handle technique filter
            if (isset($_GET['technique']) && !empty($_GET['technique'])) {
                $techniques = array_map('sanitize_text_field', (array) $_GET['technique']);
                if (!empty($techniques) && $techniques[0] !== '') {
                    $tax_query[] = array(
                        'taxonomy' => 'recipe_technique',
                        'field'    => 'slug',
                        'terms'    => $techniques,
                        'operator' => 'IN',
                    );
                }
            }
            
            // Handle difficulty filter
            if (isset($_GET['difficulty']) && !empty($_GET['difficulty'])) {
                $difficulties = array_map('sanitize_text_field', (array) $_GET['difficulty']);
                if (!empty($difficulties) && $difficulties[0] !== '') {
                    $tax_query[] = array(
                        'taxonomy' => 'recipe_difficulty',
                        'field'    => 'slug',
                        'terms'    => $difficulties,
                        'operator' => 'IN',
                    );
                }
            }
            
            // Set the tax_query if we have filters
            if (count($tax_query) > 1) {
                $query->set('tax_query', $tax_query);
            }
            
            // Set posts per page
            $query->set('posts_per_page', 12);
        }
    }
}
