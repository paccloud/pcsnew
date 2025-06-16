<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Clear plugin-specific caches and force template refresh
 */
function pcs_clear_recipe_cache() {
    // Clear all transients
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '%_transient_pcs_recipe%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '%_transient_timeout_pcs_recipe%'");
    
    // Force template refresh
    delete_option('pcs_recipe_template_version');
    
    // Clear object cache if exists
    if (wp_using_ext_object_cache()) {
        wp_cache_flush();
    }
}

// Hook into plugin activation
add_action('activated_plugin', function($plugin) {
    if ($plugin === plugin_basename(PCS_ER_PLUGIN_DIR . 'pcs-enhanced-recipes.php')) {
        pcs_clear_recipe_cache();
    }
});
