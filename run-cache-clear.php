<?php
require_once( __DIR__ . '/app/public/wp-load.php' );
require_once( __DIR__ . '/app/public/wp-content/plugins/pcs-enhanced-recipes/includes/clear-cache.php' );

if (function_exists('pcs_clear_recipe_cache')) {
    pcs_clear_recipe_cache();
    echo "Recipe cache cleared successfully.\n";
} else {
    echo "Error: pcs_clear_recipe_cache() function not found.\n";
}
?>
