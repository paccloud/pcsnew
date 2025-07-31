<?php
/**
 * Footer Navigation Fix for Block Themes
 * This script updates the footer navigation block content directly
 */

// Connect to WordPress
require_once('/Users/ryanhorwath/Local Sites/pcsnew/app/public/wp-config.php');

echo "=== FOOTER NAVIGATION BLOCK FIX SCRIPT ===\n";
echo "Current date: " . date('Y-m-d H:i:s') . "\n";

// Get all navigation posts (wp_navigation post type)
$navigation_posts = get_posts([
    'post_type' => 'wp_navigation',
    'post_status' => 'publish',
    'numberposts' => -1
]);

echo "Found " . count($navigation_posts) . " navigation posts:\n";
foreach ($navigation_posts as $nav_post) {
    echo "  - " . $nav_post->post_title . " (ID: " . $nav_post->ID . ")\n";
}

// Look for footer navigation
$footer_nav = null;
foreach ($navigation_posts as $nav_post) {
    if (stripos($nav_post->post_title, 'footer') !== false) {
        $footer_nav = $nav_post;
        break;
    }
}

// Create the correct navigation block content
$navigation_content = '<!-- wp:navigation {"ref":' . ($footer_nav ? $footer_nav->ID : 'null') . ',"layout":{"type":"flex","justifyContent":"center"}} -->
<!-- wp:page-list {"isNavigationChild":true} -->
<!-- wp:navigation-link {"label":"Home","type":"custom","url":"' . home_url('/') . '","kind":"custom"} /-->
<!-- wp:navigation-link {"label":"About Us","type":"custom","url":"' . home_url('/about-us/') . '","kind":"custom"} /-->
<!-- wp:navigation-link {"label":"Recipes","type":"custom","url":"' . home_url('/recipes/') . '","kind":"custom"} /-->
<!-- wp:navigation-link {"label":"Blog","type":"custom","url":"' . home_url('/blog/') . '","kind":"custom"} /-->
<!-- wp:navigation-link {"label":"FAQs","type":"custom","url":"' . home_url('/faqs/') . '","kind":"custom"} /-->
<!-- wp:navigation-link {"label":"Newsletter","type":"custom","url":"' . home_url('/newsletter/') . '","kind":"custom"} /-->
<!-- /wp:page-list -->
<!-- /wp:navigation -->';

if ($footer_nav) {
    echo "\nUpdating existing footer navigation (ID: " . $footer_nav->ID . ")...\n";
    
    $update_result = wp_update_post([
        'ID' => $footer_nav->ID,
        'post_content' => $navigation_content
    ]);
    
    if ($update_result && !is_wp_error($update_result)) {
        echo "Successfully updated footer navigation content.\n";
    } else {
        echo "Error updating footer navigation: " . ($update_result ? $update_result->get_error_message() : 'Unknown error') . "\n";
    }
} else {
    echo "\nCreating new footer navigation...\n";
    
    $new_nav_id = wp_insert_post([
        'post_title' => 'Footer Navigation',
        'post_content' => $navigation_content,
        'post_status' => 'publish',
        'post_type' => 'wp_navigation'
    ]);
    
    if ($new_nav_id && !is_wp_error($new_nav_id)) {
        echo "Created new footer navigation (ID: " . $new_nav_id . ").\n";
    } else {
        echo "Error creating footer navigation: " . ($new_nav_id ? $new_nav_id->get_error_message() : 'Unknown error') . "\n";
    }
}

// Now update the footer template part to use the correct navigation
$footer_template_parts = get_posts([
    'post_type' => 'wp_template_part',
    'post_status' => 'publish',
    'numberposts' => -1,
    'meta_query' => [
        [
            'key' => '_wp_theme_slug',
            'value' => 'pcs-twentytwentyfive-child'
        ]
    ]
]);

echo "\nLooking for footer template parts...\n";
$footer_template = null;

foreach ($footer_template_parts as $template_part) {
    if (stripos($template_part->post_name, 'footer') !== false) {
        $footer_template = $template_part;
        echo "Found footer template part: " . $template_part->post_title . " (ID: " . $template_part->ID . ")\n";
        break;
    }
}

if ($footer_template) {
    echo "Current footer template content preview:\n";
    echo substr($footer_template->post_content, 0, 200) . "...\n";
    
    // Create new footer template content with correct navigation
    $new_footer_content = '<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">
    <!-- wp:site-logo {"width":100,"shouldSyncIcon":false} /-->
    
    <!-- wp:navigation {"ref":' . ($footer_nav ? $footer_nav->ID : ($new_nav_id ? $new_nav_id : 'null')) . ',"layout":{"type":"flex","justifyContent":"center"}} -->
    <!-- wp:navigation-link {"label":"Home","type":"custom","url":"' . home_url('/') . '","kind":"custom"} /-->
    <!-- wp:navigation-link {"label":"About Us","type":"custom","url":"' . home_url('/about-us/') . '","kind":"custom"} /-->
    <!-- wp:navigation-link {"label":"Recipes","type":"custom","url":"' . home_url('/recipes/') . '","kind":"custom"} /-->
    <!-- wp:navigation-link {"label":"Blog","type":"custom","url":"' . home_url('/blog/') . '","kind":"custom"} /-->
    <!-- wp:navigation-link {"label":"FAQs","type":"custom","url":"' . home_url('/faqs/') . '","kind":"custom"} /-->
    <!-- wp:navigation-link {"label":"Newsletter","type":"custom","url":"' . home_url('/newsletter/') . '","kind":"custom"} /-->
    <!-- /wp:navigation -->
    
    <!-- wp:paragraph {"align":"center","fontSize":"small"} -->
    <p class="has-text-align-center has-small-font-size">© 2025 Pacific Cloud Seafoods. All rights reserved.</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->';
    
    $update_result = wp_update_post([
        'ID' => $footer_template->ID,
        'post_content' => $new_footer_content
    ]);
    
    if ($update_result && !is_wp_error($update_result)) {
        echo "Successfully updated footer template part with correct navigation.\n";
    } else {
        echo "Error updating footer template part: " . ($update_result ? $update_result->get_error_message() : 'Unknown error') . "\n";
    }
} else {
    echo "No footer template part found.\n";
}

echo "\n=== SCRIPT COMPLETED ===\n";
echo "Footer navigation has been updated with the correct links.\n";
echo "Please check your website to see the changes.\n";
?>