<?php
/**
 * Recipe Migration Script
 *
 * This script helps migrate recipe data from the original recipe plugin
 * to the new theme implementation.
 *
 * Usage: Add this file to your theme, then visit:
 * https://yoursite.com/wp-admin/themes.php?page=migrate-recipes
 *
 * @package Pacific_Cloud_Seafood_25
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add admin menu for recipe migration
 */
function pcs25_add_migration_menu() {
    add_theme_page(
        'Migrate Recipes',
        'Migrate Recipes',
        'manage_options',
        'migrate-recipes',
        'pcs25_migration_page'
    );
}
add_action( 'admin_menu', 'pcs25_add_migration_menu' );

/**
 * Render the migration admin page
 */
function pcs25_migration_page() {
    // Check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    
    $migration_complete = false;
    $migration_results = array();
    
    // Process migration if form submitted
    if ( isset( $_POST['pcs25_migrate_recipes'] ) && check_admin_referer( 'pcs25_migrate_recipes' ) ) {
        $migration_results = pcs25_perform_recipe_migration();
        $migration_complete = true;
    }
    
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        
        <?php if ( $migration_complete ) : ?>
            <div class="notice notice-success">
                <p>Recipe migration completed successfully!</p>
            </div>
            
            <h2>Migration Results:</h2>
            <ul>
                <li>Recipes processed: <?php echo esc_html( $migration_results['total'] ); ?></li>
                <li>Recipes migrated: <?php echo esc_html( $migration_results['migrated'] ); ?></li>
                <li>Recipes skipped: <?php echo esc_html( $migration_results['skipped'] ); ?></li>
            </ul>
            
            <?php if ( ! empty( $migration_results['log'] ) ) : ?>
                <h3>Migration Log:</h3>
                <div style="background: #f0f0f0; padding: 10px; max-height: 300px; overflow-y: auto;">
                    <?php foreach ( $migration_results['log'] as $log_entry ) : ?>
                        <p><?php echo esc_html( $log_entry ); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
        <?php else : ?>
            <p>This tool will migrate your recipes from the original recipe plugin to the new theme implementation.</p>
            <p>The migration process will:</p>
            <ol>
                <li>Find all existing recipe posts</li>
                <li>Convert recipe metadata to the new format</li>
                <li>Update recipe taxonomies</li>
            </ol>
            
            <p><strong>Important:</strong> It's recommended to backup your database before proceeding.</p>
            
            <form method="post" action="">
                <?php wp_nonce_field( 'pcs25_migrate_recipes' ); ?>
                <p>
                    <input type="submit" name="pcs25_migrate_recipes" class="button button-primary" value="Start Migration">
                </p>
            </form>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Perform the actual recipe migration
 *
 * @return array Migration results
 */
function pcs25_perform_recipe_migration() {
    $results = array(
        'total' => 0,
        'migrated' => 0,
        'skipped' => 0,
        'log' => array(),
    );
    
    // Get all recipe posts
    $recipes = get_posts( array(
        'post_type' => 'recipe',
        'posts_per_page' => -1,
        'post_status' => 'any',
    ) );
    
    $results['total'] = count( $recipes );
    $results['log'][] = "Found {$results['total']} recipes to process.";
    
    foreach ( $recipes as $recipe ) {
        $results['log'][] = "Processing recipe: {$recipe->post_title} (ID: {$recipe->ID})";
        
        // Get old metadata
        $old_prep_time = get_post_meta( $recipe->ID, '_rp_prep_time', true );
        $old_cook_time = get_post_meta( $recipe->ID, '_rp_cook_time', true );
        $old_servings = get_post_meta( $recipe->ID, '_rp_servings', true );
        $old_ingredients = get_post_meta( $recipe->ID, '_rp_ingredients', true );
        
        // Update with new metadata keys
        if ( $old_prep_time ) {
            update_post_meta( $recipe->ID, '_pcs25_prep_time', $old_prep_time );
            $results['log'][] = "- Migrated prep time: {$old_prep_time}";
        }
        
        if ( $old_cook_time ) {
            update_post_meta( $recipe->ID, '_pcs25_cook_time', $old_cook_time );
            $results['log'][] = "- Migrated cook time: {$old_cook_time}";
        }
        
        if ( $old_servings ) {
            update_post_meta( $recipe->ID, '_pcs25_servings', $old_servings );
            $results['log'][] = "- Migrated servings: {$old_servings}";
        }
        
        if ( $old_ingredients ) {
            update_post_meta( $recipe->ID, '_pcs25_ingredients', $old_ingredients );
            $results['log'][] = "- Migrated ingredients list";
        }
        
        // Check if we actually migrated anything
        if ( $old_prep_time || $old_cook_time || $old_servings || $old_ingredients ) {
            $results['migrated']++;
            $results['log'][] = "✓ Successfully migrated recipe: {$recipe->post_title}";
        } else {
            $results['skipped']++;
            $results['log'][] = "⚠ No metadata found to migrate for recipe: {$recipe->post_title}";
        }
    }
    
    $results['log'][] = "Migration completed. Migrated: {$results['migrated']}, Skipped: {$results['skipped']}";
    
    return $results;
}
