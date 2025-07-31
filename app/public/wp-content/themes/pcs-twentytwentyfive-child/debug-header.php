<?php
/**
 * Header Debug Script
 * 
 * This script helps debug header template part issues
 * Run this from WordPress admin to check header status
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Debug header template parts and check what's being used
 */
function pcs_debug_header() {
    echo "<h2>Header Debug Information</h2>\n";
    
    // Check if child theme is active
    $current_theme = wp_get_theme();
    echo "<h3>Current Theme</h3>\n";
    echo "<p>Theme: " . $current_theme->get('Name') . " v" . $current_theme->get('Version') . "</p>\n";
    echo "<p>Template: " . $current_theme->get('Template') . "</p>\n";
    echo "<p>Stylesheet: " . get_stylesheet() . "</p>\n";
    
    // Check for header template part files
    echo "<h3>Header Template Part Files</h3>\n";
    $child_header = get_stylesheet_directory() . '/parts/header.html';
    $parent_header = get_template_directory() . '/parts/header.html';
    
    if (file_exists($child_header)) {
        echo "<p>✅ Child theme header: " . $child_header . "</p>\n";
        $content = file_get_contents($child_header);
        echo "<p>File size: " . strlen($content) . " characters</p>\n";
        echo "<p>Contains 'alignfull': " . (strpos($content, 'alignfull') !== false ? '✅ Yes' : '❌ No') . "</p>\n";
        echo "<p>First 200 characters:</p>\n";
        echo "<pre>" . htmlspecialchars(substr($content, 0, 200)) . "...</pre>\n";
    } else {
        echo "<p>❌ Child theme header not found</p>\n";
    }
    
    if (file_exists($parent_header)) {
        echo "<p>✅ Parent theme header: " . $parent_header . "</p>\n";
    } else {
        echo "<p>❌ Parent theme header not found</p>\n";
    }
    
    // Check for database-stored template parts
    echo "<h3>Database Template Parts</h3>\n";
    $header_template_parts = get_block_templates(array('slug__in' => array('header')), 'wp_template_part');
    
    if (!empty($header_template_parts)) {
        echo "<p>⚠️ Found " . count($header_template_parts) . " header template part(s) in database:</p>\n";
        foreach ($header_template_parts as $part) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>\n";
            echo "<p><strong>ID:</strong> " . $part->id . "</p>\n";
            echo "<p><strong>Source:</strong> " . $part->source . "</p>\n";
            echo "<p><strong>Theme:</strong> " . $part->theme . "</p>\n";
            echo "<p><strong>Status:</strong> " . $part->status . "</p>\n";
            echo "<p><strong>Content length:</strong> " . strlen($part->content) . " characters</p>\n";
            echo "<p><strong>Contains 'alignfull':</strong> " . (strpos($part->content, 'alignfull') !== false ? '✅ Yes' : '❌ No') . "</p>\n";
            echo "<p><strong>Content preview:</strong></p>\n";
            echo "<pre style='background: #f5f5f5; padding: 10px; overflow-x: auto;'>" . htmlspecialchars(substr($part->content, 0, 300)) . "...</pre>\n";
            echo "</div>\n";
        }
    } else {
        echo "<p>✅ No header template parts found in database (using file-based templates)</p>\n";
    }
    
    // Check template hierarchy
    echo "<h3>Template Hierarchy</h3>\n";
    $template = get_page_template();
    echo "<p>Current template: " . $template . "</p>\n";
    
    // Check what template part would be loaded
    echo "<h3>Template Part Resolution</h3>\n";
    $theme = get_stylesheet();
    $template_part_id = $theme . '//header';
    echo "<p>Looking for template part ID: " . $template_part_id . "</p>\n";
    
    // Check for custom header template parts
    $custom_headers = get_block_templates(array('area' => 'header'), 'wp_template_part');
    if (!empty($custom_headers)) {
        echo "<p>Found " . count($custom_headers) . " header template parts:</p>\n";
        foreach ($custom_headers as $header) {
            echo "<p>- " . $header->slug . " (" . $header->source . ")</p>\n";
        }
    }
    
    // Check CSS
    echo "<h3>CSS Check</h3>\n";
    $style_file = get_stylesheet_directory() . '/style.css';
    if (file_exists($style_file)) {
        $style_content = file_get_contents($style_file);
        if (strpos($style_content, 'Full-width Header and Footer') !== false) {
            echo "<p>✅ Full-width CSS styles are present</p>\n";
        } else {
            echo "<p>❌ Full-width CSS styles missing</p>\n";
        }
        
        // Check for specific CSS rules
        if (strpos($style_content, '.wp-site-blocks > header') !== false) {
            echo "<p>✅ Header CSS rules found</p>\n";
        } else {
            echo "<p>❌ Header CSS rules missing</p>\n";
        }
    }
}

/**
 * Clear database-stored header template parts
 */
function pcs_clear_database_header_templates() {
    $results = array();
    
    // Get all header template parts from database
    $header_templates = get_posts(array(
        'post_type' => 'wp_template_part',
        'post_status' => array('publish', 'auto-draft', 'draft'),
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_wp_theme',
                'value' => get_stylesheet(),
                'compare' => '='
            )
        ),
        'tax_query' => array(
            array(
                'taxonomy' => 'wp_template_part_area',
                'field' => 'slug',
                'terms' => 'header'
            )
        )
    ));
    
    if (!empty($header_templates)) {
        foreach ($header_templates as $template) {
            wp_delete_post($template->ID, true);
            $results[] = "✅ Deleted database header template: " . $template->post_title . " (ID: " . $template->ID . ")";
        }
    } else {
        $results[] = "ℹ️ No database header templates found to delete";
    }
    
    // Also try a more direct approach
    global $wpdb;
    $deleted = $wpdb->delete(
        $wpdb->posts,
        array(
            'post_type' => 'wp_template_part',
            'post_name' => 'header'
        )
    );
    
    if ($deleted) {
        $results[] = "✅ Deleted {$deleted} header template part(s) from database";
    }
    
    // Clear caches
    wp_cache_flush();
    delete_transient('theme_roots');
    delete_site_transient('theme_roots');
    
    $results[] = "✅ Cleared template caches";
    
    return $results;
}
