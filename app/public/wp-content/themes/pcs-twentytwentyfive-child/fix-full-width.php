<?php
/**
 * Full Width Header and Footer Fix
 * 
 * This script ensures the header and footer stretch the full width of the screen
 * by clearing caches and forcing theme refresh
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main full-width fix function
 */
function pcs_fix_full_width() {
    $results = array();
    
    // 1. Clear theme and template caches
    $results[] = pcs_clear_theme_caches();
    
    // 2. Force theme refresh
    $results[] = pcs_force_theme_refresh();
    
    // 3. Clear any WordPress caches
    $results[] = pcs_clear_wp_caches();
    
    return $results;
}

/**
 * Clear theme-related caches
 */
function pcs_clear_theme_caches() {
    // Clear theme mods
    delete_option('theme_mods_' . get_option('stylesheet'));
    delete_option('theme_mods_' . get_option('template'));
    
    // Clear theme-related transients
    delete_transient('theme_roots');
    delete_site_transient('theme_roots');
    
    // Clear template part caches
    wp_cache_delete('template_parts', 'themes');
    
    return "✅ Theme caches cleared";
}

/**
 * Force theme refresh
 */
function pcs_force_theme_refresh() {
    // Update theme version to force refresh
    $theme = wp_get_theme();
    $current_version = $theme->get('Version');
    
    // Clear any theme-related transients
    delete_transient('theme_roots');
    delete_site_transient('theme_roots');
    
    // Clear block template caches
    if (function_exists('wp_cache_flush_group')) {
        wp_cache_flush_group('themes');
        wp_cache_flush_group('theme-json');
    }
    
    return "✅ Theme refresh forced (v{$current_version})";
}

/**
 * Clear WordPress caches
 */
function pcs_clear_wp_caches() {
    // Clear object cache if available
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
    
    // Clear any rewrite rules
    flush_rewrite_rules();
    
    // Clear template hierarchy cache
    wp_cache_delete('template_hierarchy', 'themes');
    
    return "✅ WordPress caches cleared";
}

/**
 * Check if full-width styles are applied
 */
function pcs_check_full_width_status() {
    $results = array();
    
    // Check if child theme is active
    $current_theme = wp_get_theme();
    $results[] = "Current theme: " . $current_theme->get('Name') . " v" . $current_theme->get('Version');
    
    // Check if header template part exists
    $header_file = get_stylesheet_directory() . '/parts/header.html';
    if (file_exists($header_file)) {
        $header_content = file_get_contents($header_file);
        if (strpos($header_content, 'alignfull') !== false) {
            $results[] = "✅ Header template uses full-width layout";
        } else {
            $results[] = "❌ Header template needs full-width layout";
        }
    } else {
        $results[] = "❌ Header template part not found";
    }
    
    // Check if footer template part exists
    $footer_file = get_stylesheet_directory() . '/parts/footer.html';
    if (file_exists($footer_file)) {
        $footer_content = file_get_contents($footer_file);
        if (strpos($footer_content, 'alignfull') !== false) {
            $results[] = "✅ Footer template uses full-width layout";
        } else {
            $results[] = "❌ Footer template needs full-width layout";
        }
    } else {
        $results[] = "❌ Footer template part not found";
    }
    
    // Check if CSS styles are present
    $style_file = get_stylesheet_directory() . '/style.css';
    if (file_exists($style_file)) {
        $style_content = file_get_contents($style_file);
        if (strpos($style_content, 'Full-width Header and Footer') !== false) {
            $results[] = "✅ Full-width CSS styles are present";
        } else {
            $results[] = "❌ Full-width CSS styles missing";
        }
    }
    
    return $results;
}

// If called directly via admin or WP-CLI
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('pcs fix-full-width', function() {
        $results = pcs_fix_full_width();
        foreach ($results as $result) {
            WP_CLI::line($result);
        }
    });
}
