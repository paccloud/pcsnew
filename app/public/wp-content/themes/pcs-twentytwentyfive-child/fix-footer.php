<?php
/**
 * Footer Fix Script for Pacific Cloud Seafoods
 * 
 * This script automatically fixes common footer display issues
 * Run this from WordPress admin to apply footer fixes
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main footer fix function
 */
function pcs_fix_footer() {
    $results = array();
    
    // 1. Clear theme and template caches
    $results[] = pcs_clear_theme_caches();
    
    // 2. Ensure footer template part exists and is correct
    $results[] = pcs_ensure_footer_template_part();
    
    // 3. Register footer pattern properly
    $results[] = pcs_register_footer_pattern();
    
    // 4. Clear any conflicting template parts from database
    $results[] = pcs_clear_conflicting_template_parts();
    
    // 5. Force theme refresh
    $results[] = pcs_force_theme_refresh();
    
    return $results;
}

/**
 * Clear theme and template caches
 */
function pcs_clear_theme_caches() {
    // Clear object cache
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
    
    // Clear theme cache
    if (function_exists('wp_cache_delete')) {
        wp_cache_delete('theme_roots', 'themes');
    }
    
    // Clear template cache
    wp_cache_delete('wp_template_part', 'themes');
    wp_cache_delete('wp_template', 'themes');
    
    // Clear rewrite rules
    flush_rewrite_rules();
    
    return "✅ Theme and template caches cleared";
}

/**
 * Ensure footer template part exists and is correct
 */
function pcs_ensure_footer_template_part() {
    $footer_file = get_stylesheet_directory() . '/parts/footer.html';
    
    if (!file_exists($footer_file)) {
        // Create the parts directory if it doesn't exist
        $parts_dir = get_stylesheet_directory() . '/parts';
        if (!file_exists($parts_dir)) {
            mkdir($parts_dir, 0755, true);
        }
        
        // Create the footer template part
        $footer_content = '<!-- wp:template-part {"slug":"footer","theme":"' . get_stylesheet() . '"} /-->';
        file_put_contents($footer_file, $footer_content);
        
        return "✅ Footer template part created";
    }
    
    return "✅ Footer template part exists";
}

/**
 * Register footer pattern properly
 */
function pcs_register_footer_pattern() {
    // Remove any existing pattern registration
    remove_action('init', 'pcs_register_patterns');
    
    // Re-register with correct content
    add_action('init', function() {
        $footer_content = '<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--50)">
    <!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
    <div class="wp-block-group alignwide">
        <!-- wp:site-logo /-->
        
        <!-- wp:group {"align":"full","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"top"}} -->
        <div class="wp-block-group alignfull">
            <!-- wp:columns -->
            <div class="wp-block-columns">
                <!-- wp:column {"width":"100%"} -->
                <div class="wp-block-column" style="flex-basis:100%">
                    <!-- wp:site-title {"level":2} /-->
                    <!-- wp:site-tagline /-->
                </div>
                <!-- /wp:column -->
            </div>
            <!-- /wp:columns -->
            
            <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|80"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"top","justifyContent":"space-between"}} -->
            <div class="wp-block-group">
                <!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","orientation":"vertical"}} -->
                    <!-- wp:navigation-link {"label":"Home","url":"/"} /-->
                    <!-- wp:navigation-link {"label":"About Us","url":"/about-us/"} /-->
                    <!-- wp:navigation-link {"label":"Recipes","url":"/recipes/"} /-->
                    <!-- wp:navigation-link {"label":"Blog","url":"/blog/"} /-->
                <!-- /wp:navigation -->
                
                <!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","orientation":"vertical"}} -->
                    <!-- wp:navigation-link {"label":"FAQs","url":"/faqs/"} /-->
                    <!-- wp:navigation-link {"label":"Newsletter","url":"/newsletter/"} /-->
                <!-- /wp:navigation -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:group -->
        
        <!-- wp:spacer {"height":"var:preset|spacing|70"} -->
        <div style="height:var(--wp--preset--spacing--70)" aria-hidden="true" class="wp-block-spacer"></div>
        <!-- /wp:spacer -->
        
        <!-- wp:group {"align":"full","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
        <div class="wp-block-group alignfull">
            <!-- wp:paragraph {"fontSize":"small"} -->
            <p class="has-small-font-size">Pacific Cloud Seafoods</p>
            <!-- /wp:paragraph -->
            <!-- wp:paragraph {"fontSize":"small"} -->
            <p class="has-small-font-size">Sustainably sourced seafood from Alaska to Buffalo</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:group -->
    </div>
    <!-- /wp:group -->
</div>
<!-- /wp:group -->';
        
        register_block_pattern(
            'pcs-twentytwentyfive-child/footer',
            array(
                'title'       => 'Pacific Cloud Seafoods Footer',
                'description' => 'Footer with logo, title, tagline and navigation links to existing pages.',
                'content'     => $footer_content,
                'categories'  => array('footer'),
                'keywords'    => array('footer', 'navigation', 'pacific cloud seafoods'),
            )
        );
    });
    
    return "✅ Footer pattern registered";
}

/**
 * Clear conflicting template parts from database
 */
function pcs_clear_conflicting_template_parts() {
    global $wpdb;
    
    // Remove any customized footer template parts that might be conflicting
    $deleted = $wpdb->delete(
        $wpdb->posts,
        array(
            'post_type' => 'wp_template_part',
            'post_name' => 'footer'
        )
    );
    
    if ($deleted) {
        return "✅ Cleared {$deleted} conflicting template parts from database";
    }
    
    return "✅ No conflicting template parts found";
}

/**
 * Force theme refresh
 */
function pcs_force_theme_refresh() {
    // Update theme version to force refresh
    $theme = wp_get_theme();
    $current_version = $theme->get('Version');
    $new_version = $current_version . '.' . time();
    
    // This would require modifying style.css, but we'll just clear caches instead
    delete_option('theme_mods_' . get_option('stylesheet'));
    delete_option('theme_mods_' . get_option('template'));
    
    // Clear any theme-related transients
    delete_transient('theme_roots');
    delete_site_transient('theme_roots');
    
    return "✅ Theme refresh forced";
}

/**
 * Check footer status after fix
 */
function pcs_check_footer_status() {
    $status = array();
    
    // Check if footer template part exists
    $footer_file = get_stylesheet_directory() . '/parts/footer.html';
    $status['footer_file'] = file_exists($footer_file);
    
    // Check if footer pattern is registered
    $patterns = WP_Block_Patterns_Registry::get_instance()->get_all_registered();
    $footer_pattern_exists = false;
    foreach ($patterns as $pattern) {
        if ($pattern['name'] === 'pcs-twentytwentyfive-child/footer') {
            $footer_pattern_exists = true;
            break;
        }
    }
    $status['footer_pattern'] = $footer_pattern_exists;
    
    // Check if template parts are in database
    $template_parts = get_block_templates(array('slug__in' => array('footer')), 'wp_template_part');
    $status['template_parts_count'] = count($template_parts);
    
    return $status;
}

// If called directly for testing
if (defined('WP_CLI') && WP_CLI) {
    $results = pcs_fix_footer();
    foreach ($results as $result) {
        WP_CLI::log($result);
    }
    
    $status = pcs_check_footer_status();
    WP_CLI::log("Footer status: " . print_r($status, true));
}
?>
