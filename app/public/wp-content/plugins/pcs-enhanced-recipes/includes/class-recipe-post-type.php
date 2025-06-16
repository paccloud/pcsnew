<?php
/**
 * Recipe Post Type Class
 * 
 * Handles recipe post type registration and functionality
 * 
 * @package PCS_Enhanced_Recipes
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Recipe Post Type Class
 */
class PCS_Recipe_Post_Type {
    /**
     * Constructor
     */
    public function __construct() {
        // Register post type and taxonomies
        add_action('init', array($this, 'register_post_type'));
        add_action('init', array($this, 'register_taxonomies'));
        
        // Add columns to admin list
        add_filter('manage_recipe_posts_columns', array($this, 'add_admin_columns'));
        add_action('manage_recipe_posts_custom_column', array($this, 'render_admin_columns'), 10, 2);
        
        // Make columns sortable
        add_filter('manage_edit-recipe_sortable_columns', array($this, 'sortable_columns'));
    }
    
    /**
     * Register recipe post type
     */
    public function register_post_type() {
        $labels = array(
            'name'               => _x('Recipes', 'post type general name', 'pcs-enhanced-recipes'),
            'singular_name'      => _x('Recipe', 'post type singular name', 'pcs-enhanced-recipes'),
            'menu_name'          => _x('Recipes', 'admin menu', 'pcs-enhanced-recipes'),
            'name_admin_bar'     => _x('Recipe', 'add new on admin bar', 'pcs-enhanced-recipes'),
            'add_new'            => _x('Add New', 'recipe', 'pcs-enhanced-recipes'),
            'add_new_item'       => __('Add New Recipe', 'pcs-enhanced-recipes'),
            'new_item'           => __('New Recipe', 'pcs-enhanced-recipes'),
            'edit_item'          => __('Edit Recipe', 'pcs-enhanced-recipes'),
            'view_item'          => __('View Recipe', 'pcs-enhanced-recipes'),
            'all_items'          => __('All Recipes', 'pcs-enhanced-recipes'),
            'search_items'       => __('Search Recipes', 'pcs-enhanced-recipes'),
            'parent_item_colon'  => __('Parent Recipes:', 'pcs-enhanced-recipes'),
            'not_found'          => __('No recipes found.', 'pcs-enhanced-recipes'),
            'not_found_in_trash' => __('No recipes found in Trash.', 'pcs-enhanced-recipes')
        );
        
        $args = array(
            'labels'             => $labels,
            'description'        => __('Recipe post type for displaying recipes.', 'pcs-enhanced-recipes'),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'recipes'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-food',
            'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
            'show_in_rest'       => true
        );
        
        register_post_type('recipe', $args);
    }
    
    /**
     * Register taxonomies
     */
    public function register_taxonomies() {
        // Recipe Category
        $category_labels = array(
            'name'                       => _x('Recipe Categories', 'taxonomy general name', 'pcs-enhanced-recipes'),
            'singular_name'              => _x('Recipe Category', 'taxonomy singular name', 'pcs-enhanced-recipes'),
            'search_items'               => __('Search Recipe Categories', 'pcs-enhanced-recipes'),
            'popular_items'              => __('Popular Recipe Categories', 'pcs-enhanced-recipes'),
            'all_items'                  => __('All Recipe Categories', 'pcs-enhanced-recipes'),
            'parent_item'                => __('Parent Recipe Category', 'pcs-enhanced-recipes'),
            'parent_item_colon'          => __('Parent Recipe Category:', 'pcs-enhanced-recipes'),
            'edit_item'                  => __('Edit Recipe Category', 'pcs-enhanced-recipes'),
            'update_item'                => __('Update Recipe Category', 'pcs-enhanced-recipes'),
            'add_new_item'               => __('Add New Recipe Category', 'pcs-enhanced-recipes'),
            'new_item_name'              => __('New Recipe Category Name', 'pcs-enhanced-recipes'),
            'separate_items_with_commas' => __('Separate recipe categories with commas', 'pcs-enhanced-recipes'),
            'add_or_remove_items'        => __('Add or remove recipe categories', 'pcs-enhanced-recipes'),
            'choose_from_most_used'      => __('Choose from the most used recipe categories', 'pcs-enhanced-recipes'),
            'not_found'                  => __('No recipe categories found.', 'pcs-enhanced-recipes'),
            'menu_name'                  => __('Categories', 'pcs-enhanced-recipes'),
        );
        
        $category_args = array(
            'hierarchical'      => true,
            'labels'            => $category_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'recipe-category'),
            'show_in_rest'      => true
        );
        
        register_taxonomy('recipe_category', 'recipe', $category_args);
        
        // Recipe Tag
        $tag_labels = array(
            'name'                       => _x('Recipe Tags', 'taxonomy general name', 'pcs-enhanced-recipes'),
            'singular_name'              => _x('Recipe Tag', 'taxonomy singular name', 'pcs-enhanced-recipes'),
            'search_items'               => __('Search Recipe Tags', 'pcs-enhanced-recipes'),
            'popular_items'              => __('Popular Recipe Tags', 'pcs-enhanced-recipes'),
            'all_items'                  => __('All Recipe Tags', 'pcs-enhanced-recipes'),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => __('Edit Recipe Tag', 'pcs-enhanced-recipes'),
            'update_item'                => __('Update Recipe Tag', 'pcs-enhanced-recipes'),
            'add_new_item'               => __('Add New Recipe Tag', 'pcs-enhanced-recipes'),
            'new_item_name'              => __('New Recipe Tag Name', 'pcs-enhanced-recipes'),
            'separate_items_with_commas' => __('Separate recipe tags with commas', 'pcs-enhanced-recipes'),
            'add_or_remove_items'        => __('Add or remove recipe tags', 'pcs-enhanced-recipes'),
            'choose_from_most_used'      => __('Choose from the most used recipe tags', 'pcs-enhanced-recipes'),
            'not_found'                  => __('No recipe tags found.', 'pcs-enhanced-recipes'),
            'menu_name'                  => __('Tags', 'pcs-enhanced-recipes'),
        );
        
        $tag_args = array(
            'hierarchical'      => false,
            'labels'            => $tag_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'recipe-tag'),
            'show_in_rest'      => true
        );
        
        register_taxonomy('recipe_tag', 'recipe', $tag_args);
        
        // Fish Type
        $fish_type_labels = array(
            'name'                       => _x('Fish Types', 'taxonomy general name', 'pcs-enhanced-recipes'),
            'singular_name'              => _x('Fish Type', 'taxonomy singular name', 'pcs-enhanced-recipes'),
            'search_items'               => __('Search Fish Types', 'pcs-enhanced-recipes'),
            'popular_items'              => __('Popular Fish Types', 'pcs-enhanced-recipes'),
            'all_items'                  => __('All Fish Types', 'pcs-enhanced-recipes'),
            'parent_item'                => __('Parent Fish Type', 'pcs-enhanced-recipes'),
            'parent_item_colon'          => __('Parent Fish Type:', 'pcs-enhanced-recipes'),
            'edit_item'                  => __('Edit Fish Type', 'pcs-enhanced-recipes'),
            'update_item'                => __('Update Fish Type', 'pcs-enhanced-recipes'),
            'add_new_item'               => __('Add New Fish Type', 'pcs-enhanced-recipes'),
            'new_item_name'              => __('New Fish Type Name', 'pcs-enhanced-recipes'),
            'separate_items_with_commas' => __('Separate fish types with commas', 'pcs-enhanced-recipes'),
            'add_or_remove_items'        => __('Add or remove fish types', 'pcs-enhanced-recipes'),
            'choose_from_most_used'      => __('Choose from the most used fish types', 'pcs-enhanced-recipes'),
            'not_found'                  => __('No fish types found.', 'pcs-enhanced-recipes'),
            'menu_name'                  => __('Fish Types', 'pcs-enhanced-recipes'),
        );
        
        $fish_type_args = array(
            'hierarchical'      => true,
            'labels'            => $fish_type_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'fish-type'),
            'show_in_rest'      => true
        );
        
        register_taxonomy('recipe_fish_type', 'recipe', $fish_type_args);
        
        // Meal Type
        $meal_type_labels = array(
            'name'                       => _x('Meal Types', 'taxonomy general name', 'pcs-enhanced-recipes'),
            'singular_name'              => _x('Meal Type', 'taxonomy singular name', 'pcs-enhanced-recipes'),
            'search_items'               => __('Search Meal Types', 'pcs-enhanced-recipes'),
            'popular_items'              => __('Popular Meal Types', 'pcs-enhanced-recipes'),
            'all_items'                  => __('All Meal Types', 'pcs-enhanced-recipes'),
            'parent_item'                => __('Parent Meal Type', 'pcs-enhanced-recipes'),
            'parent_item_colon'          => __('Parent Meal Type:', 'pcs-enhanced-recipes'),
            'edit_item'                  => __('Edit Meal Type', 'pcs-enhanced-recipes'),
            'update_item'                => __('Update Meal Type', 'pcs-enhanced-recipes'),
            'add_new_item'               => __('Add New Meal Type', 'pcs-enhanced-recipes'),
            'new_item_name'              => __('New Meal Type Name', 'pcs-enhanced-recipes'),
            'separate_items_with_commas' => __('Separate meal types with commas', 'pcs-enhanced-recipes'),
            'add_or_remove_items'        => __('Add or remove meal types', 'pcs-enhanced-recipes'),
            'choose_from_most_used'      => __('Choose from the most used meal types', 'pcs-enhanced-recipes'),
            'not_found'                  => __('No meal types found.', 'pcs-enhanced-recipes'),
            'menu_name'                  => __('Meal Types', 'pcs-enhanced-recipes'),
        );
        
        $meal_type_args = array(
            'hierarchical'      => true,
            'labels'            => $meal_type_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'meal-type'),
            'show_in_rest'      => true
        );
        
        register_taxonomy('recipe_meal_type', 'recipe', $meal_type_args);
        
        // Cooking Technique
        $technique_labels = array(
            'name'                       => _x('Cooking Techniques', 'taxonomy general name', 'pcs-enhanced-recipes'),
            'singular_name'              => _x('Cooking Technique', 'taxonomy singular name', 'pcs-enhanced-recipes'),
            'search_items'               => __('Search Cooking Techniques', 'pcs-enhanced-recipes'),
            'popular_items'              => __('Popular Cooking Techniques', 'pcs-enhanced-recipes'),
            'all_items'                  => __('All Cooking Techniques', 'pcs-enhanced-recipes'),
            'parent_item'                => __('Parent Cooking Technique', 'pcs-enhanced-recipes'),
            'parent_item_colon'          => __('Parent Cooking Technique:', 'pcs-enhanced-recipes'),
            'edit_item'                  => __('Edit Cooking Technique', 'pcs-enhanced-recipes'),
            'update_item'                => __('Update Cooking Technique', 'pcs-enhanced-recipes'),
            'add_new_item'               => __('Add New Cooking Technique', 'pcs-enhanced-recipes'),
            'new_item_name'              => __('New Cooking Technique Name', 'pcs-enhanced-recipes'),
            'separate_items_with_commas' => __('Separate cooking techniques with commas', 'pcs-enhanced-recipes'),
            'add_or_remove_items'        => __('Add or remove cooking techniques', 'pcs-enhanced-recipes'),
            'choose_from_most_used'      => __('Choose from the most used cooking techniques', 'pcs-enhanced-recipes'),
            'not_found'                  => __('No cooking techniques found.', 'pcs-enhanced-recipes'),
            'menu_name'                  => __('Techniques', 'pcs-enhanced-recipes'),
        );
        
        $technique_args = array(
            'hierarchical'      => true,
            'labels'            => $technique_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'cooking-technique'),
            'show_in_rest'      => true
        );
        
        register_taxonomy('recipe_technique', 'recipe', $technique_args);
        
        // Recipe Difficulty
        $difficulty_labels = array(
            'name'                       => _x('Difficulty Levels', 'taxonomy general name', 'pcs-enhanced-recipes'),
            'singular_name'              => _x('Difficulty Level', 'taxonomy singular name', 'pcs-enhanced-recipes'),
            'search_items'               => __('Search Difficulty Levels', 'pcs-enhanced-recipes'),
            'popular_items'              => __('Popular Difficulty Levels', 'pcs-enhanced-recipes'),
            'all_items'                  => __('All Difficulty Levels', 'pcs-enhanced-recipes'),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => __('Edit Difficulty Level', 'pcs-enhanced-recipes'),
            'update_item'                => __('Update Difficulty Level', 'pcs-enhanced-recipes'),
            'add_new_item'               => __('Add New Difficulty Level', 'pcs-enhanced-recipes'),
            'new_item_name'              => __('New Difficulty Level Name', 'pcs-enhanced-recipes'),
            'separate_items_with_commas' => __('Separate difficulty levels with commas', 'pcs-enhanced-recipes'),
            'add_or_remove_items'        => __('Add or remove difficulty levels', 'pcs-enhanced-recipes'),
            'choose_from_most_used'      => __('Choose from the most used difficulty levels', 'pcs-enhanced-recipes'),
            'not_found'                  => __('No difficulty levels found.', 'pcs-enhanced-recipes'),
            'menu_name'                  => __('Difficulty', 'pcs-enhanced-recipes'),
        );
        
        $difficulty_args = array(
            'hierarchical'      => true,
            'labels'            => $difficulty_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'difficulty-level'),
            'show_in_rest'      => true
        );
        
        register_taxonomy('recipe_difficulty', 'recipe', $difficulty_args);
    }
    
    /**
     * Add admin columns
     */
    public function add_admin_columns($columns) {
        $new_columns = array();
        
        // Insert thumbnail after checkbox
        $new_columns['cb'] = $columns['cb'];
        $new_columns['thumbnail'] = __('Image', 'pcs-enhanced-recipes');
        
        // Add remaining columns
        $new_columns['title'] = $columns['title'];
        $new_columns['recipe_category'] = __('Categories', 'pcs-enhanced-recipes');
        $new_columns['recipe_tag'] = __('Tags', 'pcs-enhanced-recipes');
        $new_columns['prep_time'] = __('Prep Time', 'pcs-enhanced-recipes');
        $new_columns['cook_time'] = __('Cook Time', 'pcs-enhanced-recipes');
        $new_columns['date'] = $columns['date'];
        
        return $new_columns;
    }
    
    /**
     * Render admin columns
     */
    public function render_admin_columns($column, $post_id) {
        switch ($column) {
            case 'thumbnail':
                if (has_post_thumbnail($post_id)) {
                    echo get_the_post_thumbnail($post_id, array(50, 50));
                } else {
                    echo '<div style="width:50px;height:50px;background:#f0f0f0;display:flex;align-items:center;justify-content:center;"><span class="dashicons dashicons-food"></span></div>';
                }
                break;
                
            case 'recipe_category':
                $terms = get_the_terms($post_id, 'recipe_category');
                if (!empty($terms) && !is_wp_error($terms)) {
                    $term_links = array();
                    foreach ($terms as $term) {
                        $term_links[] = '<a href="' . esc_url(admin_url('edit.php?post_type=recipe&recipe_category=' . $term->slug)) . '">' . esc_html($term->name) . '</a>';
                    }
                    echo implode(', ', $term_links);
                } else {
                    echo '—';
                }
                break;
                
            case 'recipe_tag':
                $terms = get_the_terms($post_id, 'recipe_tag');
                if (!empty($terms) && !is_wp_error($terms)) {
                    $term_links = array();
                    foreach ($terms as $term) {
                        $term_links[] = '<a href="' . esc_url(admin_url('edit.php?post_type=recipe&recipe_tag=' . $term->slug)) . '">' . esc_html($term->name) . '</a>';
                    }
                    echo implode(', ', $term_links);
                } else {
                    echo '—';
                }
                break;
                
            case 'prep_time':
                $prep_time = get_post_meta($post_id, '_pcs_prep_time', true);
                if (!empty($prep_time)) {
                    echo esc_html($prep_time) . ' ' . __('min', 'pcs-enhanced-recipes');
                } else {
                    echo '—';
                }
                break;
                
            case 'cook_time':
                $cook_time = get_post_meta($post_id, '_pcs_cook_time', true);
                if (!empty($cook_time)) {
                    echo esc_html($cook_time) . ' ' . __('min', 'pcs-enhanced-recipes');
                } else {
                    echo '—';
                }
                break;
        }
    }
    
    /**
     * Make columns sortable
     */
    public function sortable_columns($columns) {
        $columns['prep_time'] = 'prep_time';
        $columns['cook_time'] = 'cook_time';
        return $columns;
    }
}
