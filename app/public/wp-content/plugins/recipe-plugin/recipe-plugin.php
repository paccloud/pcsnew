<?php
/*
Plugin Name: Recipe Plugin
Description: A custom plugin to manage and display recipes on your WordPress site.
Version: 1.0
Author: Your Name
*/

function rp_register_recipe_post_type() {
    $args = array(
        'public' => true,
        'label'  => 'Recipes',
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'menu_icon' => 'dashicons-food',
        'has_archive' => true,
        'rewrite' => array('slug' => 'recipe'),
        'publicly_queryable' => true,
        'query_var' => true,
        'show_in_nav_menus' => true,
        'show_in_rest' => true,
        'template' => array(
            array('core/paragraph', array(
                'placeholder' => 'Add recipe description...'
            ))
        ),
        'template_lock' => 'all'
    );
    register_post_type('recipe', $args);
}
add_action('init', 'rp_register_recipe_post_type');


function rp_add_recipe_meta_box() {
    add_meta_box(
        'rp_recipe_details',
        'Recipe Details',
        'rp_recipe_meta_box_callback',
        'recipe',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'rp_add_recipe_meta_box');

function rp_recipe_meta_box_callback($post) {
    wp_nonce_field('rp_save_recipe_data', 'rp_recipe_meta_nonce');
    
    $prep_time = get_post_meta($post->ID, '_pcs25_prep_time', true);
    $cook_time = get_post_meta($post->ID, '_pcs25_cook_time', true);
    $servings = get_post_meta($post->ID, '_pcs25_servings', true);
    $ingredients = get_post_meta($post->ID, '_pcs25_ingredients', true);
    
    echo '<p><label for="rp_prep_time">Prep Time:</label> ';
    echo '<input type="text" id="rp_prep_time" name="rp_prep_time" value="' . esc_attr($prep_time) . '" /></p>';
    
    echo '<p><label for="rp_cook_time">Cook Time:</label> ';
    echo '<input type="text" id="rp_cook_time" name="rp_cook_time" value="' . esc_attr($cook_time) . '" /></p>';
    
    echo '<p><label for="rp_servings">Servings:</label> ';
    echo '<input type="text" id="rp_servings" name="rp_servings" value="' . esc_attr($servings) . '" /></p>';
    
    echo '<p><label for="rp_ingredients">Ingredients (one per line):</label><br>';
    echo '<textarea id="rp_ingredients" name="rp_ingredients" rows="5" style="width: 100%;">' . esc_textarea($ingredients) . '</textarea></p>';
}

function rp_save_recipe_data($post_id) {
    if (!isset($_POST['rp_recipe_meta_nonce']) || !wp_verify_nonce($_POST['rp_recipe_meta_nonce'], 'rp_save_recipe_data')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (isset($_POST['rp_prep_time'])) {
        update_post_meta($post_id, '_pcs25_prep_time', sanitize_text_field($_POST['rp_prep_time']));
    }
    if (isset($_POST['rp_cook_time'])) {
        update_post_meta($post_id, '_pcs25_cook_time', sanitize_text_field($_POST['rp_cook_time']));
    }
    if (isset($_POST['rp_servings'])) {
        update_post_meta($post_id, '_pcs25_servings', sanitize_text_field($_POST['rp_servings']));
    }
    if (isset($_POST['rp_ingredients'])) {
        update_post_meta($post_id, '_pcs25_ingredients', sanitize_textarea_field($_POST['rp_ingredients']));
    }
}
add_action('save_post', 'rp_save_recipe_data');

function rp_enqueue_styles() {
    wp_enqueue_style('rp-styles', plugins_url('style.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'rp_enqueue_styles');
