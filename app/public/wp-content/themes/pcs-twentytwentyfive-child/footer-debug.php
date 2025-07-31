<?php
/**
 * Footer Debug Script
 * 
 * This script helps debug footer display issues
 * Run this from WordPress admin or via WP-CLI to check footer status
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Debug footer template parts and patterns
 */
function pcs_debug_footer() {
    echo "<h2>Footer Debug Information</h2>\n";
    
    // Check if child theme is active
    $current_theme = wp_get_theme();
    echo "<h3>Current Theme</h3>\n";
    echo "<p>Theme: " . $current_theme->get('Name') . " v" . $current_theme->get('Version') . "</p>\n";
    echo "<p>Template: " . $current_theme->get('Template') . "</p>\n";
    echo "<p>Stylesheet: " . get_stylesheet() . "</p>\n";
    
    // Check footer template part
    echo "<h3>Footer Template Part</h3>\n";
    $footer_file = get_stylesheet_directory() . '/parts/footer.html';
    if (file_exists($footer_file)) {
        echo "<p>✅ Footer template part exists: " . $footer_file . "</p>\n";
        echo "<p>File size: " . filesize($footer_file) . " bytes</p>\n";
        echo "<p>Last modified: " . date('Y-m-d H:i:s', filemtime($footer_file)) . "</p>\n";
    } else {
        echo "<p>❌ Footer template part missing: " . $footer_file . "</p>\n";
    }
    
    // Check footer pattern
    echo "<h3>Footer Pattern</h3>\n";
    $pattern_file = get_stylesheet_directory() . '/patterns/footer.php';
    if (file_exists($pattern_file)) {
        echo "<p>✅ Footer pattern exists: " . $pattern_file . "</p>\n";
        echo "<p>File size: " . filesize($pattern_file) . " bytes</p>\n";
        echo "<p>Last modified: " . date('Y-m-d H:i:s', filemtime($pattern_file)) . "</p>\n";
    } else {
        echo "<p>❌ Footer pattern missing: " . $pattern_file . "</p>\n";
    }
    
    // Check registered patterns
    echo "<h3>Registered Block Patterns</h3>\n";
    $patterns = WP_Block_Patterns_Registry::get_instance()->get_all_registered();
    $footer_patterns = array_filter($patterns, function($pattern) {
        return strpos($pattern['name'], 'footer') !== false;
    });
    
    if (!empty($footer_patterns)) {
        echo "<p>Found " . count($footer_patterns) . " footer patterns:</p>\n";
        echo "<ul>\n";
        foreach ($footer_patterns as $pattern) {
            echo "<li>" . $pattern['name'] . " - " . $pattern['title'] . "</li>\n";
        }
        echo "</ul>\n";
    } else {
        echo "<p>❌ No footer patterns found</p>\n";
    }
    
    // Check template hierarchy
    echo "<h3>Template Hierarchy</h3>\n";
    $template = get_page_template();
    echo "<p>Current template: " . $template . "</p>\n";
    
    // Check if footer is being called
    echo "<h3>Footer Template Part Check</h3>\n";
    $template_parts = get_block_templates(array('slug__in' => array('footer')), 'wp_template_part');
    if (!empty($template_parts)) {
        echo "<p>✅ Footer template part found in database</p>\n";
        foreach ($template_parts as $part) {
            echo "<p>Source: " . $part->source . "</p>\n";
            echo "<p>Theme: " . $part->theme . "</p>\n";
            echo "<p>Content length: " . strlen($part->content) . " characters</p>\n";
        }
    } else {
        echo "<p>❌ No footer template part found in database</p>\n";
    }
    
    // Check for caching issues
    echo "<h3>Caching Check</h3>\n";
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
        echo "<p>✅ Object cache flushed</p>\n";
    }
    
    if (function_exists('wp_cache_delete')) {
        wp_cache_delete('theme_roots', 'themes');
        echo "<p>✅ Theme cache cleared</p>\n";
    }
    
    // Check for common issues
    echo "<h3>Common Issues Check</h3>\n";
    
    // Check if functions.php is loading
    if (function_exists('pcs_register_patterns')) {
        echo "<p>✅ Custom functions loaded</p>\n";
    } else {
        echo "<p>❌ Custom functions not loaded</p>\n";
    }
    
    // Check for PHP errors
    $error_log = ini_get('error_log');
    if ($error_log && file_exists($error_log)) {
        $recent_errors = tail($error_log, 10);
        if (!empty($recent_errors)) {
            echo "<p>⚠️ Recent PHP errors found (check error log)</p>\n";
        } else {
            echo "<p>✅ No recent PHP errors</p>\n";
        }
    }
    
    echo "<h3>Recommendations</h3>\n";
    echo "<ol>\n";
    echo "<li>Clear any caching plugins</li>\n";
    echo "<li>Check WordPress Site Editor (Appearance → Site Editor → Template Parts → Footer)</li>\n";
    echo "<li>Verify theme is properly activated</li>\n";
    echo "<li>Check for plugin conflicts</li>\n";
    echo "<li>Ensure Local by Flywheel services are running</li>\n";
    echo "</ol>\n";
}

/**
 * Simple tail function to read last lines of a file
 */
function tail($file, $lines = 10) {
    if (!file_exists($file)) {
        return array();
    }
    
    $handle = fopen($file, "r");
    if (!$handle) {
        return array();
    }
    
    $linecounter = $lines;
    $pos = -2;
    $beginning = false;
    $text = array();
    
    while ($linecounter > 0) {
        $t = " ";
        while ($t != "\n") {
            if (fseek($handle, $pos, SEEK_END) == -1) {
                $beginning = true;
                break;
            }
            $t = fgetc($handle);
            $pos--;
        }
        $linecounter--;
        if ($beginning) {
            rewind($handle);
        }
        $text[$lines - $linecounter - 1] = fgets($handle);
        if ($beginning) break;
    }
    fclose($handle);
    return array_reverse($text);
}

// If called directly (for testing)
if (defined('WP_CLI') && WP_CLI) {
    pcs_debug_footer();
} elseif (isset($_GET['debug_footer']) && current_user_can('manage_options')) {
    pcs_debug_footer();
}
?>
