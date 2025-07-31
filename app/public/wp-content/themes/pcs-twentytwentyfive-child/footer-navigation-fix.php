<?php
/**
 * Footer Navigation Fix - Direct Database Update
 * This script updates the footer navigation to use the correct page links
 */

// Connect to WordPress
require_once('/Users/ryanhorwath/Local Sites/pcsnew/app/public/wp-config.php');

// Get the current footer navigation menu
$footer_menus = wp_get_nav_menus();

// Look for footer menu (could be named "Footer menu", "Footer", or "Footer navigation 2")
$footer_menu = null;
foreach ($footer_menus as $menu) {
    if (stripos($menu->name, 'footer') !== false) {
        $footer_menu = $menu;
        break;
    }
}

echo "=== FOOTER NAVIGATION FIX SCRIPT ===\n";
echo "Current date: " . date('Y-m-d H:i:s') . "\n";
echo "WordPress root: " . ABSPATH . "\n";

if ($footer_menu) {
    echo "Found footer menu: " . $footer_menu->name . " (ID: " . $footer_menu->term_id . ")\n";
    
    // Get current menu items
    $current_items = wp_get_nav_menu_items($footer_menu->term_id);
    
    echo "Current menu items:\n";
    foreach ($current_items as $item) {
        echo "  - " . $item->title . " -> " . $item->url . "\n";
    }
    
    // Define the correct navigation structure
    $correct_pages = [
        'Home' => home_url('/'),
        'About Us' => home_url('/about-us/'),
        'Recipes' => home_url('/recipes/'),
        'Blog' => home_url('/blog/'),
        'FAQs' => home_url('/faqs/'),
        'Newsletter' => home_url('/newsletter/')
    ];
    
    echo "\nTarget navigation structure:\n";
    foreach ($correct_pages as $title => $url) {
        echo "  - " . $title . " -> " . $url . "\n";
    }
    
    // Clear existing menu items
    if ($current_items) {
        foreach ($current_items as $item) {
            wp_delete_post($item->ID, true);
        }
        echo "\nCleared existing menu items.\n";
    }
    
    // Add new menu items
    $menu_order = 1;
    foreach ($correct_pages as $title => $url) {
        $menu_item_data = [
            'menu-item-title' => $title,
            'menu-item-url' => $url,
            'menu-item-type' => 'custom',
            'menu-item-status' => 'publish',
            'menu-item-position' => $menu_order
        ];
        
        $item_id = wp_update_nav_menu_item($footer_menu->term_id, 0, $menu_item_data);
        
        if ($item_id && !is_wp_error($item_id)) {
            echo "Added menu item: " . $title . " -> " . $url . "\n";
        } else {
            echo "ERROR adding menu item: " . $title . "\n";
        }
        
        $menu_order++;
    }
    
    echo "\n=== FOOTER NAVIGATION FIX COMPLETED ===\n";
    echo "Please refresh your website to see the changes.\n";
    
} else {
    echo "No footer menu found. Available menus:\n";
    foreach ($footer_menus as $menu) {
        echo "  - " . $menu->name . " (ID: " . $menu->term_id . ")\n";
    }
    
    echo "\nCreating new footer menu...\n";
    
    // Create new footer menu
    $new_menu_id = wp_create_nav_menu('Footer Menu');
    
    if ($new_menu_id && !is_wp_error($new_menu_id)) {
        echo "Created new footer menu (ID: " . $new_menu_id . ")\n";
        
        // Add menu items to new menu
        $correct_pages = [
            'Home' => home_url('/'),
            'About Us' => home_url('/about-us/'),
            'Recipes' => home_url('/recipes/'),
            'Blog' => home_url('/blog/'),
            'FAQs' => home_url('/faqs/'),
            'Newsletter' => home_url('/newsletter/')
        ];
        
        $menu_order = 1;
        foreach ($correct_pages as $title => $url) {
            $menu_item_data = [
                'menu-item-title' => $title,
                'menu-item-url' => $url,
                'menu-item-type' => 'custom',
                'menu-item-status' => 'publish',
                'menu-item-position' => $menu_order
            ];
            
            $item_id = wp_update_nav_menu_item($new_menu_id, 0, $menu_item_data);
            
            if ($item_id && !is_wp_error($item_id)) {
                echo "Added menu item: " . $title . " -> " . $url . "\n";
            } else {
                echo "ERROR adding menu item: " . $title . "\n";
            }
            
            $menu_order++;
        }
        
        echo "\n=== FOOTER NAVIGATION CREATED ===\n";
        echo "New footer menu created with correct navigation links.\n";
        echo "You may need to assign this menu to the footer location in WordPress admin.\n";
    } else {
        echo "ERROR: Could not create footer menu.\n";
    }
}

echo "\n=== SCRIPT COMPLETED ===\n";
?>