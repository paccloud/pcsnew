<?php
require_once( __DIR__ . '/wp-load.php' );

function create_recipe($title, $content) {
    $my_post = array(
        'post_title'    => $title,
        'post_content'  => $content,
        'post_status'   => 'publish',
        'post_author'   => 1,
        'post_type'     => 'recipe'
    );

    $post_id = wp_insert_post( $my_post );

    if ($post_id) {
        return "Recipe created successfully! Post ID: " . $post_id;
    } else {
        return "Error creating recipe.";
    }
}

// Example usage:
// echo create_recipe('My Awesome Recipe', 'This is the content of my awesome recipe.');
?>
