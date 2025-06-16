<?php
/**
 * Plugin Name: PCS Recipe Importer
 * Description: Adds an import option for PCS recipes with file upload support
 * Version: 1.1
 * Author: Pacific Cloud Seafood
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class PCS_Recipe_Importer {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'handle_import'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function enqueue_scripts($hook) {
        // Only load on our import page
        if ($hook != 'recipe_page_pcs-recipe-import') {
            return;
        }
        
        wp_enqueue_style('pcs-recipe-importer', plugin_dir_url(__FILE__) . 'style.css', array(), '1.0.0');
    }

    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=recipe',  // Parent slug (Recipes)
            'Import Recipes',             // Page title
            'Import Recipes',             // Menu title
            'manage_options',             // Capability
            'pcs-recipe-import',          // Menu slug
            array($this, 'import_page')   // Callback function
        );
    }

    public function import_page() {
        ?>
        <div class="wrap">
            <h1>Import PCS Recipes</h1>
            
            <div class="pcs-recipe-import-tabs">
                <div class="nav-tab-wrapper">
                    <a href="#directory-import" class="nav-tab nav-tab-active">Import from Directory</a>
                    <a href="#file-import" class="nav-tab">Upload Recipe File</a>
                </div>
                
                <div id="directory-import" class="tab-content active">
                    <h2>Import from Directory</h2>
                    <p>Import recipes from the <code>/wp-content/uploads/recipes/</code> directory.</p>
                    
                    <form method="post" action="">
                        <?php wp_nonce_field('pcs_import_recipes_nonce', 'pcs_import_recipes_nonce'); ?>
                        <input type="hidden" name="pcs_import_recipes" value="1">
                        <input type="hidden" name="import_type" value="directory">
                        
                        <div class="form-field">
                            <label>
                                <input type="checkbox" name="force_import" value="1">
                                Force import (override existing recipes or restore deleted ones)
                            </label>
                        </div>
                        
                        <?php submit_button('Import Recipes', 'primary', 'submit', true); ?>
                    </form>
                </div>
                
                <div id="file-import" class="tab-content">
                    <h2>Upload Recipe File</h2>
                    <p>Upload a recipe PHP file to import. The file should return a recipe array.</p>
                    
                    <form method="post" action="" enctype="multipart/form-data">
                        <?php wp_nonce_field('pcs_import_recipe_file_nonce', 'pcs_import_recipe_file_nonce'); ?>
                        <input type="hidden" name="pcs_import_recipe_file" value="1">
                        <input type="hidden" name="import_type" value="file">
                        
                        <div class="form-field">
                            <label for="recipe_file">Recipe File (PHP):</label>
                            <input type="file" name="recipe_file" id="recipe_file" accept=".php">
                        </div>
                        
                        <div class="form-field">
                            <label>
                                <input type="checkbox" name="force_import" value="1">
                                Force import (override existing recipe or restore deleted one)
                            </label>
                        </div>
                        
                        <?php submit_button('Upload and Import', 'primary', 'submit', true); ?>
                    </form>
                </div>
            </div>
        </div>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabs = document.querySelectorAll('.nav-tab');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Remove active class from all tabs and contents
                    tabs.forEach(t => t.classList.remove('nav-tab-active'));
                    tabContents.forEach(c => c.classList.remove('active'));
                    
                    // Add active class to current tab
                    this.classList.add('nav-tab-active');
                    
                    // Show corresponding content
                    const target = this.getAttribute('href').substring(1);
                    document.getElementById(target).classList.add('active');
                });
            });
        });
        </script>
        <?php
    }

    public function handle_import() {
        // Check for directory import
        if (isset($_POST['pcs_import_recipes']) && $_POST['pcs_import_recipes'] === '1' && 
            isset($_POST['import_type']) && $_POST['import_type'] === 'directory') {
            
            $this->handle_directory_import();
        }
        
        // Check for file upload import
        if (isset($_POST['pcs_import_recipe_file']) && $_POST['pcs_import_recipe_file'] === '1' && 
            isset($_POST['import_type']) && $_POST['import_type'] === 'file') {
            
            $this->handle_file_upload_import();
        }
    }
    
    private function handle_directory_import() {
        // Verify nonce
        if (!isset($_POST['pcs_import_recipes_nonce']) || !wp_verify_nonce($_POST['pcs_import_recipes_nonce'], 'pcs_import_recipes_nonce')) {
            wp_die('Security check failed');
        }

        // Check user permissions
        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to access this page.');
        }

        // Path to recipes directory
        $recipes_dir = WP_CONTENT_DIR . '/uploads/recipes/';
        
        // Check if directory exists
        if (!is_dir($recipes_dir)) {
            wp_die('Recipes directory not found: ' . $recipes_dir);
        }

        // Get all PHP files in directory
        $recipe_files = glob($recipes_dir . '*.php');
        
        if (empty($recipe_files)) {
            wp_die('No recipe files found in: ' . $recipes_dir);
        }

        $imported_count = 0;
        $errors = array();
        $force_import = isset($_POST['force_import']) && $_POST['force_import'] === '1';

        // Loop through recipe files
        foreach ($recipe_files as $recipe_file) {
            // Process the recipe file
            $result = $this->process_recipe_file($recipe_file, $force_import);
            
            if ($result === true) {
                $imported_count++;
            } else {
                $errors[] = $result; // Error message
            }
        }

        // Set admin notice
        if ($imported_count > 0) {
            $message = sprintf('Successfully imported %d recipes.', $imported_count);
            if (!empty($errors)) {
                $message .= ' Errors: ' . implode(', ', $errors);
            }
            set_transient('pcs_recipe_import_notice', $message, 60);
        } else {
            set_transient('pcs_recipe_import_notice', 'No recipes were imported. Errors: ' . implode(', ', $errors), 60);
        }

        // Redirect to recipes list
        wp_redirect(admin_url('edit.php?post_type=recipe'));
        exit;
    }
    
    private function handle_file_upload_import() {
        // Verify nonce
        if (!isset($_POST['pcs_import_recipe_file_nonce']) || !wp_verify_nonce($_POST['pcs_import_recipe_file_nonce'], 'pcs_import_recipe_file_nonce')) {
            wp_die('Security check failed');
        }

        // Check user permissions
        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to access this page.');
        }
        
        // Check if file was uploaded successfully
        if (!isset($_FILES['recipe_file']) || $_FILES['recipe_file']['error'] !== UPLOAD_ERR_OK) {
            wp_die('Error uploading file. Please try again.');
        }
        
        // Check file type
        $file_ext = pathinfo($_FILES['recipe_file']['name'], PATHINFO_EXTENSION);
        if ($file_ext !== 'php') {
            wp_die('Invalid file type. Please upload a PHP file.');
        }
        
        // Create temporary directory if it doesn't exist
        $upload_dir = wp_upload_dir();
        $temp_dir = $upload_dir['basedir'] . '/recipe-imports/';
        
        if (!file_exists($temp_dir)) {
            mkdir($temp_dir, 0755, true);
        }
        
        // Save uploaded file to temporary location
        $temp_file = $temp_dir . basename($_FILES['recipe_file']['name']);
        move_uploaded_file($_FILES['recipe_file']['tmp_name'], $temp_file);
        
        $force_import = isset($_POST['force_import']) && $_POST['force_import'] === '1';
        
        // Process the recipe file
        $result = $this->process_recipe_file($temp_file, $force_import);
        
        // Set admin notice
        if ($result === true) {
            $message = 'Successfully imported recipe.';
            set_transient('pcs_recipe_import_notice', $message, 60);
        } else {
            set_transient('pcs_recipe_import_notice', 'Error importing recipe: ' . $result, 60);
        }
        
        // Delete temporary file
        @unlink($temp_file);
        
        // Redirect to recipes list
        wp_redirect(admin_url('edit.php?post_type=recipe'));
        exit;
    }
    
    private function process_recipe_file($recipe_file, $force_import = false) {
        // Include the recipe file to get the $recipe array
        $recipe = include $recipe_file;
        
        if (!is_array($recipe) || empty($recipe['title'])) {
            return 'Invalid recipe format in file: ' . basename($recipe_file);
        }

        // Check if recipe already exists by title
        $existing = $this->get_recipe_by_title($recipe['title']);
        
        if ($existing && !$force_import) {
            return 'Recipe already exists: ' . $recipe['title'];
        }
        
        // Create the content using blocks to match the site style
        $post_content = $this->create_block_content($recipe);

        // Create post array
        $post_data = array(
            'post_title'    => $recipe['title'],
            'post_content'  => $post_content,
            'post_status'   => 'publish',
            'post_type'     => 'recipe',
        );
        
        // If we're forcing import and a recipe exists, update it instead of creating new
        if ($existing && $force_import) {
            $post_data['ID'] = $existing->ID;
            
            // If post is in trash, restore it first
            if ($existing->post_status === 'trash') {
                wp_untrash_post($existing->ID);
            }
        }

        // Insert or update the post
        $post_id = wp_insert_post($post_data);
        
        if (is_wp_error($post_id)) {
            return 'Error creating recipe: ' . $recipe['title'] . ' - ' . $post_id->get_error_message();
        }

        // Add featured image if specified
        if (!empty($recipe['featured_image'])) {
            // Extract the path from the URL
            $image_path = preg_replace('/^\/wp-content\/uploads\//', '', $recipe['featured_image']);
            
            // Find the attachment ID based on path
            $attachment_id = $this->get_attachment_id_from_path($image_path);
            
            if ($attachment_id) {
                set_post_thumbnail($post_id, $attachment_id);
            }
        }

        // Add post meta
        $meta_fields = array(
            'prep_time', 'cook_time', 'total_time', 'servings', 'calories',
            'difficulty', 'ingredients', 'instructions', 'notes', 'nutrition'
        );

        foreach ($meta_fields as $field) {
            if (isset($recipe[$field])) {
                if (is_array($recipe[$field])) {
                    update_post_meta($post_id, '_recipe_' . $field, $recipe[$field]);
                } else {
                    update_post_meta($post_id, '_recipe_' . $field, sanitize_text_field($recipe[$field]));
                }
            }
        }

        // Set taxonomies
        $taxonomies = array(
            'species'  => 'recipe_species',
            'cuisine'  => 'recipe_cuisine',
            'diet'     => 'recipe_diet',
            'season'   => 'recipe_season'
        );

        foreach ($taxonomies as $meta_key => $taxonomy) {
            if (!empty($recipe[$meta_key])) {
                $term = term_exists($recipe[$meta_key], $taxonomy);
                
                if (!$term) {
                    $term = wp_insert_term($recipe[$meta_key], $taxonomy);
                }
                
                if (!is_wp_error($term)) {
                    wp_set_object_terms($post_id, (int)$term['term_id'], $taxonomy);
                }
            }
        }

        return true;
    }

    /**
     * Get recipe post by title, including those in trash
     */
    private function get_recipe_by_title($title) {
        // First check for published/draft posts
        $existing = get_page_by_title($title, OBJECT, 'recipe');
        if ($existing) {
            return $existing;
        }
        
        // Check for trashed posts
        global $wpdb;
        $post = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT ID, post_status FROM $wpdb->posts WHERE post_title = %s AND post_type = %s",
                $title,
                'recipe'
            )
        );
        
        return $post ? get_post($post->ID) : null;
    }

    /**
     * Create block-based content for the recipe to match site style
     */
    private function create_block_content($recipe) {
        // Start with the description as a paragraph
        $content = '<!-- wp:paragraph {"className":"recipe-description"} -->
<p class="recipe-description">' . esc_html($recipe['description']) . '</p>
<!-- /wp:paragraph -->';
        
        // Recipe details block (times, servings, etc.)
        $content .= '
<!-- wp:group {"className":"recipe-details-block"} -->
<div class="wp-block-group recipe-details-block">
    <!-- wp:columns -->
    <div class="wp-block-columns">
        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:heading {"level":4} -->
            <h4>Prep Time</h4>
            <!-- /wp:heading -->
            <!-- wp:paragraph -->
            <p>' . esc_html($recipe['prep_time']) . '</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->
        
        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:heading {"level":4} -->
            <h4>Cook Time</h4>
            <!-- /wp:heading -->
            <!-- wp:paragraph -->
            <p>' . esc_html($recipe['cook_time']) . '</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->
        
        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:heading {"level":4} -->
            <h4>Total Time</h4>
            <!-- /wp:heading -->
            <!-- wp:paragraph -->
            <p>' . esc_html($recipe['total_time']) . '</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->
        
        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:heading {"level":4} -->
            <h4>Servings</h4>
            <!-- /wp:heading -->
            <!-- wp:paragraph -->
            <p>' . esc_html($recipe['servings']) . '</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->';

        // Recipe metadata (species, difficulty, etc.)
        $content .= '
<!-- wp:group {"className":"recipe-metadata-block"} -->
<div class="wp-block-group recipe-metadata-block">
    <!-- wp:columns -->
    <div class="wp-block-columns">
        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:heading {"level":4} -->
            <h4>Species</h4>
            <!-- /wp:heading -->
            <!-- wp:paragraph -->
            <p>' . esc_html($recipe['species']) . '</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->
        
        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:heading {"level":4} -->
            <h4>Difficulty</h4>
            <!-- /wp:heading -->
            <!-- wp:paragraph -->
            <p>' . esc_html($recipe['difficulty']) . '</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->
        
        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:heading {"level":4} -->
            <h4>Cuisine</h4>
            <!-- /wp:heading -->
            <!-- wp:paragraph -->
            <p>' . esc_html($recipe['cuisine']) . '</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->
        
        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:heading {"level":4} -->
            <h4>Season</h4>
            <!-- /wp:heading -->
            <!-- wp:paragraph -->
            <p>' . esc_html($recipe['season']) . '</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->';

        // Ingredients
        $content .= '
<!-- wp:group {"className":"recipe-ingredients-block"} -->
<div class="wp-block-group recipe-ingredients-block">
    <!-- wp:heading {"level":3} -->
    <h3>Ingredients</h3>
    <!-- /wp:heading -->
    
    <!-- wp:list {"className":"recipe-ingredients-list"} -->
    <ul class="recipe-ingredients-list">';
        
        foreach ($recipe['ingredients'] as $ingredient) {
            $content .= '
        <!-- wp:list-item -->
        <li>' . esc_html($ingredient) . '</li>
        <!-- /wp:list-item -->';
        }
        
        $content .= '
    </ul>
    <!-- /wp:list -->
</div>
<!-- /wp:group -->';

        // Instructions
        $content .= '
<!-- wp:group {"className":"recipe-instructions-block"} -->
<div class="wp-block-group recipe-instructions-block">
    <!-- wp:heading {"level":3} -->
    <h3>Instructions</h3>
    <!-- /wp:heading -->
    
    <!-- wp:list {"ordered":true,"className":"recipe-instructions-list"} -->
    <ol class="recipe-instructions-list">';
        
        foreach ($recipe['instructions'] as $instruction) {
            $content .= '
        <!-- wp:list-item -->
        <li>' . esc_html($instruction) . '</li>
        <!-- /wp:list-item -->';
        }
        
        $content .= '
    </ol>
    <!-- /wp:list -->
</div>
<!-- /wp:group -->';

        // Recipe Notes
        if (!empty($recipe['notes'])) {
            $content .= '
<!-- wp:group {"className":"recipe-notes-block"} -->
<div class="wp-block-group recipe-notes-block">
    <!-- wp:heading {"level":3} -->
    <h3>Recipe Notes</h3>
    <!-- /wp:heading -->
    
    <!-- wp:list {"className":"recipe-notes-list"} -->
    <ul class="recipe-notes-list">';
            
            foreach ($recipe['notes'] as $note) {
                $content .= '
            <!-- wp:list-item -->
            <li>' . esc_html($note) . '</li>
            <!-- /wp:list-item -->';
            }
            
            $content .= '
    </ul>
    <!-- /wp:list -->
</div>
<!-- /wp:group -->';
        }

        // Nutrition Information
        if (!empty($recipe['nutrition'])) {
            $content .= '
<!-- wp:group {"className":"recipe-nutrition-block"} -->
<div class="wp-block-group recipe-nutrition-block">
    <!-- wp:heading {"level":3} -->
    <h3>Nutrition Information</h3>
    <!-- /wp:heading -->
    
    <!-- wp:table {"className":"recipe-nutrition-table"} -->
    <figure class="wp-block-table recipe-nutrition-table"><table><tbody>';
            
            foreach ($recipe['nutrition'] as $key => $value) {
                $label = ucfirst($key);
                $content .= '
                <tr><td>' . esc_html($label) . '</td><td>' . esc_html($value) . '</td></tr>';
            }
            
            $content .= '
    </tbody></table></figure>
    <!-- /wp:table -->
</div>
<!-- /wp:group -->';
        }

        return $content;
    }

    /**
     * Get attachment ID from file path
     */
    private function get_attachment_id_from_path($path) {
        global $wpdb;
        
        // Try to find the attachment ID from the file path
        $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid LIKE '%s';", '%' . $path . '%'));
        
        // Return the first attachment ID found
        return !empty($attachment[0]) ? $attachment[0] : 0;
    }
}

// Initialize the plugin
new PCS_Recipe_Importer();

// Add admin notice for import results
function pcs_recipe_importer_admin_notice() {
    $notice = get_transient('pcs_recipe_import_notice');
    
    if ($notice) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($notice) . '</p></div>';
        delete_transient('pcs_recipe_import_notice');
    }
}
add_action('admin_notices', 'pcs_recipe_importer_admin_notice');
