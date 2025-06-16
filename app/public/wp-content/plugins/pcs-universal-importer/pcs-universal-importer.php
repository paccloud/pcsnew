<?php
/**
 * Plugin Name: PCS Universal Content Importer
 * Description: Import any type of post content with block-based formatting
 * Version: 1.0
 * Author: Pacific Cloud Seafood
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class PCS_Universal_Importer {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'handle_import'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function enqueue_scripts($hook) {
        // Only load on our import page
        if (strpos($hook, 'pcs-universal-import') === false) {
            return;
        }
        
        wp_enqueue_style('pcs-universal-importer', plugin_dir_url(__FILE__) . 'style.css', array(), '1.0.0');
        wp_enqueue_script('pcs-universal-importer', plugin_dir_url(__FILE__) . 'script.js', array('jquery'), '1.0.0', true);
    }

    public function add_admin_menu() {
        add_menu_page(
            'PCS Content Importer',       // Page title
            'Content Importer',           // Menu title
            'manage_options',             // Capability
            'pcs-universal-import',       // Menu slug
            array($this, 'import_page'),  // Callback function
            'dashicons-upload',           // Icon
            30                            // Position
        );
    }

    public function import_page() {
        // Get all registered post types
        $post_types = get_post_types(array(
            'public'   => true,
            '_builtin' => false
        ), 'objects');
        
        // Add default post types
        $post_types = array_merge(array(
            'post' => get_post_type_object('post'),
            'page' => get_post_type_object('page')
        ), $post_types);
        
        // Directory to look for content files
        $content_dir = WP_CONTENT_DIR . '/uploads/content/';
        $content_url = content_url('/uploads/content/');
        
        // Create directory if it doesn't exist
        if (!file_exists($content_dir)) {
            wp_mkdir_p($content_dir);
        }
        
        // Get a list of files in the directory
        $content_files = array();
        if (is_dir($content_dir)) {
            $content_files = glob($content_dir . '*.php');
        }
        
        ?>
        <div class="wrap pcs-universal-importer">
            <h1>Pacific Cloud Content Importer</h1>
            <p>Import any type of content with block-based formatting compatible with WordPress 2025 themes.</p>
            
            <div class="pcs-importer-tabs">
                <div class="nav-tab-wrapper">
                    <a href="#directory-import" class="nav-tab nav-tab-active">Import from Directory</a>
                    <a href="#file-import" class="nav-tab">Upload Content File</a>
                    <a href="#create-content" class="nav-tab">Create Content</a>
                </div>
                
                <div id="directory-import" class="tab-content active">
                    <h2>Import from Directory</h2>
                    <p>Import content files from <code><?php echo esc_html($content_dir); ?></code></p>
                    
                    <?php if (empty($content_files)): ?>
                        <div class="notice notice-info">
                            <p>No content files found in the directory. Please upload files to <code><?php echo esc_html($content_dir); ?></code> or use the file uploader tab.</p>
                        </div>
                    <?php else: ?>
                        <form method="post" action="">
                            <?php wp_nonce_field('pcs_import_content_nonce', 'pcs_import_content_nonce'); ?>
                            <input type="hidden" name="pcs_import_content" value="1">
                            <input type="hidden" name="import_type" value="directory">
                            
                            <table class="widefat striped">
                                <thead>
                                    <tr>
                                        <th class="check-column"><input type="checkbox" id="select-all" /></th>
                                        <th>File</th>
                                        <th>Post Type</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($content_files as $index => $file): 
                                        $filename = basename($file);
                                        // Try to get content data
                                        $content_data = $this->get_content_data($file);
                                        $suggested_type = isset($content_data['post_type']) ? $content_data['post_type'] : 'post';
                                        $title = isset($content_data['title']) ? $content_data['title'] : $filename;
                                    ?>
                                    <tr>
                                        <td><input type="checkbox" name="selected_files[]" value="<?php echo esc_attr($filename); ?>" /></td>
                                        <td><?php echo esc_html($title); ?> <span class="description">(<?php echo esc_html($filename); ?>)</span></td>
                                        <td>
                                            <select name="post_type[<?php echo esc_attr($filename); ?>]">
                                                <?php foreach ($post_types as $type => $type_obj): ?>
                                                    <option value="<?php echo esc_attr($type); ?>" <?php selected($suggested_type, $type); ?>>
                                                        <?php echo esc_html($type_obj->labels->singular_name); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <label>
                                                <input type="checkbox" name="force_import[<?php echo esc_attr($filename); ?>]" value="1">
                                                Force import (override existing or restore deleted)
                                            </label>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            
                            <p>
                                <input type="submit" class="button button-primary" value="Import Selected Content">
                            </p>
                        </form>
                    <?php endif; ?>
                </div>
                
                <div id="file-import" class="tab-content">
                    <h2>Upload Content File</h2>
                    <p>Upload a PHP file containing content data. The file should return an array with content information.</p>
                    
                    <form method="post" action="" enctype="multipart/form-data">
                        <?php wp_nonce_field('pcs_import_content_file_nonce', 'pcs_import_content_file_nonce'); ?>
                        <input type="hidden" name="pcs_import_content_file" value="1">
                        <input type="hidden" name="import_type" value="file">
                        
                        <div class="form-field">
                            <label for="content_file">Content File (PHP):</label>
                            <input type="file" name="content_file" id="content_file" accept=".php" required>
                            <p class="description">The file should return an array with content information.</p>
                        </div>
                        
                        <div class="form-field">
                            <label for="file_post_type">Post Type:</label>
                            <select name="file_post_type" id="file_post_type">
                                <?php foreach ($post_types as $type => $type_obj): ?>
                                    <option value="<?php echo esc_attr($type); ?>">
                                        <?php echo esc_html($type_obj->labels->singular_name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-field">
                            <label>
                                <input type="checkbox" name="file_force_import" value="1">
                                Force import (override existing content or restore deleted content)
                            </label>
                        </div>
                        
                        <p>
                            <input type="submit" class="button button-primary" value="Upload and Import">
                        </p>
                    </form>
                </div>
                
                <div id="create-content" class="tab-content">
                    <h2>Create Content Template</h2>
                    <p>Generate a content template file that you can download, customize, and import later.</p>
                    
                    <form method="post" action="">
                        <?php wp_nonce_field('pcs_generate_template_nonce', 'pcs_generate_template_nonce'); ?>
                        <input type="hidden" name="pcs_generate_template" value="1">
                        
                        <div class="form-field">
                            <label for="template_post_type">Post Type:</label>
                            <select name="template_post_type" id="template_post_type">
                                <?php foreach ($post_types as $type => $type_obj): ?>
                                    <option value="<?php echo esc_attr($type); ?>">
                                        <?php echo esc_html($type_obj->labels->singular_name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-field">
                            <label for="template_filename">Template Filename:</label>
                            <input type="text" name="template_filename" id="template_filename" value="content-template.php" required>
                            <p class="description">The filename for the generated template file.</p>
                        </div>
                        
                        <p>
                            <input type="submit" class="button button-primary" value="Generate Template">
                        </p>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }

    public function handle_import() {
        // Check for directory import
        if (isset($_POST['pcs_import_content']) && $_POST['pcs_import_content'] === '1' && 
            isset($_POST['import_type']) && $_POST['import_type'] === 'directory') {
            
            $this->handle_directory_import();
        }
        
        // Check for file upload import
        if (isset($_POST['pcs_import_content_file']) && $_POST['pcs_import_content_file'] === '1' && 
            isset($_POST['import_type']) && $_POST['import_type'] === 'file') {
            
            $this->handle_file_upload_import();
        }
        
        // Check for template generation
        if (isset($_POST['pcs_generate_template']) && $_POST['pcs_generate_template'] === '1') {
            $this->handle_template_generation();
        }
    }
    
    private function handle_directory_import() {
        // Verify nonce
        if (!isset($_POST['pcs_import_content_nonce']) || !wp_verify_nonce($_POST['pcs_import_content_nonce'], 'pcs_import_content_nonce')) {
            wp_die('Security check failed');
        }

        // Check user permissions
        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to access this page.');
        }
        
        // Check if files are selected
        if (!isset($_POST['selected_files']) || empty($_POST['selected_files'])) {
            wp_die('No files selected for import.');
        }

        // Path to content directory
        $content_dir = WP_CONTENT_DIR . '/uploads/content/';
        
        // Check if directory exists
        if (!is_dir($content_dir)) {
            wp_die('Content directory not found: ' . $content_dir);
        }

        $imported_count = 0;
        $errors = array();
        $post_types = isset($_POST['post_type']) ? $_POST['post_type'] : array();
        $force_import = isset($_POST['force_import']) ? $_POST['force_import'] : array();

        // Loop through selected files
        foreach ($_POST['selected_files'] as $filename) {
            $file_path = $content_dir . $filename;
            
            if (!file_exists($file_path)) {
                $errors[] = 'File not found: ' . $filename;
                continue;
            }
            
            // Get post type for this file
            $post_type = isset($post_types[$filename]) ? $post_types[$filename] : 'post';
            
            // Check if force import is set for this file
            $force = isset($force_import[$filename]) && $force_import[$filename] === '1';
            
            // Process the content file
            $result = $this->process_content_file($file_path, $post_type, $force);
            
            if ($result === true) {
                $imported_count++;
            } else {
                $errors[] = $result; // Error message
            }
        }

        // Set admin notice
        if ($imported_count > 0) {
            $message = sprintf('Successfully imported %d content items.', $imported_count);
            if (!empty($errors)) {
                $message .= ' Errors: ' . implode(', ', $errors);
            }
            set_transient('pcs_content_import_notice', $message, 60);
        } else {
            set_transient('pcs_content_import_notice', 'No content was imported. Errors: ' . implode(', ', $errors), 60);
        }

        // Redirect back to the importer page
        wp_redirect(admin_url('admin.php?page=pcs-universal-import'));
        exit;
    }
    
    private function handle_file_upload_import() {
        // Verify nonce
        if (!isset($_POST['pcs_import_content_file_nonce']) || !wp_verify_nonce($_POST['pcs_import_content_file_nonce'], 'pcs_import_content_file_nonce')) {
            wp_die('Security check failed');
        }

        // Check user permissions
        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to access this page.');
        }
        
        // Check if file was uploaded successfully
        if (!isset($_FILES['content_file']) || $_FILES['content_file']['error'] !== UPLOAD_ERR_OK) {
            wp_die('Error uploading file. Please try again.');
        }
        
        // Check file type
        $file_ext = pathinfo($_FILES['content_file']['name'], PATHINFO_EXTENSION);
        if ($file_ext !== 'php') {
            wp_die('Invalid file type. Please upload a PHP file.');
        }
        
        // Get post type
        $post_type = isset($_POST['file_post_type']) ? $_POST['file_post_type'] : 'post';
        
        // Check if force import is set
        $force = isset($_POST['file_force_import']) && $_POST['file_force_import'] === '1';
        
        // Create temporary directory if it doesn't exist
        $upload_dir = wp_upload_dir();
        $temp_dir = $upload_dir['basedir'] . '/content-imports/';
        
        if (!file_exists($temp_dir)) {
            mkdir($temp_dir, 0755, true);
        }
        
        // Save uploaded file to temporary location
        $temp_file = $temp_dir . basename($_FILES['content_file']['name']);
        move_uploaded_file($_FILES['content_file']['tmp_name'], $temp_file);
        
        // Process the content file
        $result = $this->process_content_file($temp_file, $post_type, $force);
        
        // Set admin notice
        if ($result === true) {
            $message = 'Successfully imported content.';
            set_transient('pcs_content_import_notice', $message, 60);
        } else {
            set_transient('pcs_content_import_notice', 'Error importing content: ' . $result, 60);
        }
        
        // Delete temporary file
        @unlink($temp_file);
        
        // Redirect back to the importer page
        wp_redirect(admin_url('admin.php?page=pcs-universal-import'));
        exit;
    }
    
    private function handle_template_generation() {
        // Verify nonce
        if (!isset($_POST['pcs_generate_template_nonce']) || !wp_verify_nonce($_POST['pcs_generate_template_nonce'], 'pcs_generate_template_nonce')) {
            wp_die('Security check failed');
        }

        // Check user permissions
        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to access this page.');
        }
        
        // Get post type and filename
        $post_type = isset($_POST['template_post_type']) ? $_POST['template_post_type'] : 'post';
        $filename = isset($_POST['template_filename']) ? sanitize_file_name($_POST['template_filename']) : 'content-template.php';
        
        // Generate template content based on post type
        $template_content = $this->generate_template_content($post_type);
        
        // Set headers for download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($template_content));
        
        // Output file content
        echo $template_content;
        exit;
    }
    
    private function generate_template_content($post_type) {
        // Get post type object
        $post_type_obj = get_post_type_object($post_type);
        $post_type_name = $post_type_obj ? $post_type_obj->labels->singular_name : 'Post';
        
        // Generate different templates based on post type
        if ($post_type === 'recipe') {
            $template = $this->get_recipe_template();
        } elseif ($post_type === 'product') {
            $template = $this->get_product_template();
        } else {
            $template = $this->get_default_template($post_type, $post_type_name);
        }
        
        return $template;
    }
    
    private function get_recipe_template() {
        return '<?php
/**
 * Content Template: Recipe
 * 
 * A template for creating recipe content for import
 */

$content = [
    // Basic post information
    \'title\' => \'Recipe Title\',
    \'post_type\' => \'recipe\',
    \'featured_image\' => \'/wp-content/uploads/2025/03/image-name.jpg\', // Path to featured image
    \'description\' => \'A detailed description of the recipe. This will appear at the top of the recipe post.\',
    
    // Recipe specific metadata
    \'prep_time\' => \'15 minutes\',
    \'cook_time\' => \'30 minutes\',
    \'total_time\' => \'45 minutes\',
    \'servings\' => \'4\',
    \'calories\' => \'350\',
    \'species\' => \'Salmon\', // For seafood recipes
    \'difficulty\' => \'Easy\',
    \'cuisine\' => \'American\',
    \'diet\' => \'Gluten Free\',
    \'season\' => \'Summer\',
    
    // Recipe components
    \'ingredients\' => [
        \'1 lb ingredient one\',
        \'2 tablespoons ingredient two\',
        \'1 teaspoon ingredient three\',
        // Add more ingredients as needed
    ],
    
    \'instructions\' => [
        \'Step one instructions.\',
        \'Step two instructions.\',
        \'Step three instructions.\',
        // Add more steps as needed
    ],
    
    \'notes\' => [
        \'Optional note one.\',
        \'Optional note two.\',
        // Add more notes as needed
    ],
    
    \'nutrition\' => [
        \'calories\' => \'350\',
        \'fat\' => \'12g\',
        \'carbohydrates\' => \'42g\',
        \'protein\' => \'28g\',
        \'sodium\' => \'380mg\',
    ],
    
    // Taxonomy terms
    \'terms\' => [
        \'recipe_category\' => [\'Dinner\', \'Main Course\'],
        \'recipe_tag\' => [\'Quick\', \'Easy\', \'Family Friendly\'],
    ],
];

return $content;';
    }
    
    private function get_product_template() {
        return '<?php
/**
 * Content Template: Product
 * 
 * A template for creating product content for import
 */

$content = [
    // Basic post information
    \'title\' => \'Product Title\',
    \'post_type\' => \'product\',
    \'featured_image\' => \'/wp-content/uploads/2025/03/image-name.jpg\', // Path to featured image
    \'description\' => \'A detailed description of the product. This will appear on the product page.\',
    
    // Product specific metadata
    \'regular_price\' => \'29.99\',
    \'sale_price\' => \'24.99\',
    \'sku\' => \'PROD-001\',
    \'stock_quantity\' => 100,
    \'weight\' => \'1.5\', // in kg
    \'dimensions\' => [
        \'length\' => \'10\',
        \'width\' => \'5\',
        \'height\' => \'2\',
    ],
    
    // Additional product fields
    \'short_description\' => \'A brief description of the product for product listings.\',
    \'attributes\' => [
        [
            \'name\' => \'Color\',
            \'options\' => [\'Red\', \'Blue\', \'Green\'],
            \'visible\' => true,
        ],
        [
            \'name\' => \'Size\',
            \'options\' => [\'Small\', \'Medium\', \'Large\'],
            \'visible\' => true,
        ],
    ],
    
    // Gallery images
    \'gallery_images\' => [
        \'/wp-content/uploads/2025/03/gallery-image-1.jpg\',
        \'/wp-content/uploads/2025/03/gallery-image-2.jpg\',
    ],
    
    // Taxonomy terms
    \'terms\' => [
        \'product_cat\' => [\'Category One\', \'Category Two\'],
        \'product_tag\' => [\'Tag One\', \'Tag Two\'],
    ],
];

return $content;';
    }
    
    private function get_default_template($post_type, $post_type_name) {
        return '<?php
/**
 * Content Template: ' . $post_type_name . '
 * 
 * A template for creating ' . strtolower($post_type_name) . ' content for import
 */

$content = [
    // Basic post information
    \'title\' => \'' . $post_type_name . ' Title\',
    \'post_type\' => \'' . $post_type . '\',
    \'featured_image\' => \'/wp-content/uploads/2025/03/image-name.jpg\', // Path to featured image
    \'content\' => \'
        <!-- wp:paragraph -->
        <p>This is the first paragraph of content. Replace this with your actual content.</p>
        <!-- /wp:paragraph -->

        <!-- wp:heading -->
        <h2>Section Heading</h2>
        <!-- /wp:heading -->

        <!-- wp:paragraph -->
        <p>This is another paragraph with more content. You can use block formatting to structure your content.</p>
        <!-- /wp:paragraph -->

        <!-- wp:list -->
        <ul>
            <li>List item one</li>
            <li>List item two</li>
            <li>List item three</li>
        </ul>
        <!-- /wp:list -->
    \',
    
    // Custom fields (these will vary based on post type)
    \'custom_fields\' => [
        \'field_one\' => \'Value one\',
        \'field_two\' => \'Value two\',
    ],
    
    // Taxonomy terms (these will vary based on post type)
    \'terms\' => [
        \'category\' => [\'Category One\', \'Category Two\'],
        \'post_tag\' => [\'Tag One\', \'Tag Two\'],
    ],
];

return $content;';
    }
    
    private function get_content_data($file_path) {
        // Load the content file to get its data
        $content = include $file_path;
        return is_array($content) ? $content : array();
    }
    
    private function process_content_file($file_path, $post_type, $force = false) {
        // Include the content file to get the content array
        $content = include $file_path;
        
        if (!is_array($content) || empty($content['title'])) {
            return 'Invalid content format in file: ' . basename($file_path);
        }

        // Override post type if specified
        if ($post_type) {
            $content['post_type'] = $post_type;
        }

        // Check if content already exists by title
        $existing = $this->get_post_by_title($content['title'], $content['post_type']);
        
        if ($existing && !$force) {
            return 'Content already exists: ' . $content['title'];
        }
        
        // Create post content based on post type
        if ($content['post_type'] === 'recipe') {
            $post_content = $this->create_recipe_content($content);
        } else {
            $post_content = isset($content['content']) ? $content['content'] : '';
        }

        // Create post array
        $post_data = array(
            'post_title'    => $content['title'],
            'post_content'  => $post_content,
            'post_status'   => 'publish',
            'post_type'     => $content['post_type'],
        );
        
        // If we're forcing import and a post exists, update it instead of creating new
        if ($existing && $force) {
            $post_data['ID'] = $existing->ID;
            
            // If post is in trash, restore it first
            if ($existing->post_status === 'trash') {
                wp_untrash_post($existing->ID);
            }
        }

        // Insert or update the post
        $post_id = wp_insert_post($post_data);
        
        if (is_wp_error($post_id)) {
            return 'Error creating content: ' . $content['title'] . ' - ' . $post_id->get_error_message();
        }

        // Add featured image if specified
        if (!empty($content['featured_image'])) {
            // Extract the path from the URL
            $image_path = preg_replace('/^\/wp-content\/uploads\//', '', $content['featured_image']);
            
            // Find the attachment ID based on path
            $attachment_id = $this->get_attachment_id_from_path($image_path);
            
            if ($attachment_id) {
                set_post_thumbnail($post_id, $attachment_id);
            }
        }

        // Handle post type specific meta fields
        $this->process_post_meta($post_id, $content);
        
        // Handle taxonomy terms
        if (!empty($content['terms']) && is_array($content['terms'])) {
            foreach ($content['terms'] as $taxonomy => $terms) {
                if (!empty($terms) && is_array($terms)) {
                    $term_ids = array();
                    
                    foreach ($terms as $term_name) {
                        $term = term_exists($term_name, $taxonomy);
                        
                        if (!$term) {
                            $term = wp_insert_term($term_name, $taxonomy);
                        }
                        
                        if (!is_wp_error($term)) {
                            $term_ids[] = (int)$term['term_id'];
                        }
                    }
                    
                    if (!empty($term_ids)) {
                        wp_set_object_terms($post_id, $term_ids, $taxonomy);
                    }
                }
            }
        }

        return true;
    }
    
    /**
     * Process post meta fields based on post type
     */
    private function process_post_meta($post_id, $content) {
        if ($content['post_type'] === 'recipe') {
            $this->process_recipe_meta($post_id, $content);
        } elseif ($content['post_type'] === 'product') {
            $this->process_product_meta($post_id, $content);
        } else {
            // Process custom fields for other post types
            if (!empty($content['custom_fields']) && is_array($content['custom_fields'])) {
                foreach ($content['custom_fields'] as $meta_key => $meta_value) {
                    update_post_meta($post_id, $meta_key, $meta_value);
                }
            }
        }
    }
    
    /**
     * Process recipe specific meta
     */
    private function process_recipe_meta($post_id, $content) {
        $meta_fields = array(
            'prep_time', 'cook_time', 'total_time', 'servings', 'calories',
            'difficulty', 'ingredients', 'instructions', 'notes', 'nutrition'
        );

        foreach ($meta_fields as $field) {
            if (isset($content[$field])) {
                if (is_array($content[$field])) {
                    update_post_meta($post_id, '_recipe_' . $field, $content[$field]);
                } else {
                    update_post_meta($post_id, '_recipe_' . $field, sanitize_text_field($content[$field]));
                }
            }
        }
        
        // Handle species and other taxonomy-like fields
        $taxonomies = array(
            'species'  => 'recipe_species',
            'cuisine'  => 'recipe_cuisine',
            'diet'     => 'recipe_diet',
            'season'   => 'recipe_season'
        );

        foreach ($taxonomies as $meta_key => $taxonomy) {
            if (!empty($content[$meta_key])) {
                $term = term_exists($content[$meta_key], $taxonomy);
                
                if (!$term) {
                    $term = wp_insert_term($content[$meta_key], $taxonomy);
                }
                
                if (!is_wp_error($term)) {
                    wp_set_object_terms($post_id, (int)$term['term_id'], $taxonomy);
                }
            }
        }
    }
    
    /**
     * Process product specific meta
     */
    private function process_product_meta($post_id, $content) {
        // Process WooCommerce product meta
        if (function_exists('wc_get_product')) {
            $product = wc_get_product($post_id);
            
            if ($product) {
                // Set product data
                if (isset($content['regular_price'])) {
                    $product->set_regular_price($content['regular_price']);
                }
                
                if (isset($content['sale_price'])) {
                    $product->set_sale_price($content['sale_price']);
                }
                
                if (isset($content['sku'])) {
                    $product->set_sku($content['sku']);
                }
                
                if (isset($content['stock_quantity'])) {
                    $product->set_stock_quantity($content['stock_quantity']);
                    $product->set_manage_stock(true);
                }
                
                if (isset($content['weight'])) {
                    $product->set_weight($content['weight']);
                }
                
                if (isset($content['dimensions']) && is_array($content['dimensions'])) {
                    if (isset($content['dimensions']['length'])) {
                        $product->set_length($content['dimensions']['length']);
                    }
                    
                    if (isset($content['dimensions']['width'])) {
                        $product->set_width($content['dimensions']['width']);
                    }
                    
                    if (isset($content['dimensions']['height'])) {
                        $product->set_height($content['dimensions']['height']);
                    }
                }
                
                if (isset($content['short_description'])) {
                    $product->set_short_description($content['short_description']);
                }
                
                // Save product
                $product->save();
                
                // Set product attributes
                if (!empty($content['attributes']) && is_array($content['attributes'])) {
                    $this->set_product_attributes($post_id, $content['attributes']);
                }
                
                // Set gallery images
                if (!empty($content['gallery_images']) && is_array($content['gallery_images'])) {
                    $gallery_ids = array();
                    
                    foreach ($content['gallery_images'] as $image_path) {
                        $image_path = preg_replace('/^\/wp-content\/uploads\//', '', $image_path);
                        $attachment_id = $this->get_attachment_id_from_path($image_path);
                        
                        if ($attachment_id) {
                            $gallery_ids[] = $attachment_id;
                        }
                    }
                    
                    update_post_meta($post_id, '_product_image_gallery', implode(',', $gallery_ids));
                }
            }
        }
    }
    
    /**
     * Set product attributes
     */
    private function set_product_attributes($product_id, $attributes) {
        if (function_exists('wc_get_product')) {
            $product = wc_get_product($product_id);
            
            if ($product) {
                $product_attributes = array();
                
                foreach ($attributes as $attribute) {
                    $attribute_name = wc_sanitize_taxonomy_name($attribute['name']);
                    $attribute_id = wc_attribute_taxonomy_id_by_name($attribute_name);
                    
                    if (!$attribute_id) {
                        // Create attribute taxonomy if it doesn't exist
                        $attribute_id = wc_create_attribute(array(
                            'name' => $attribute['name'],
                            'slug' => $attribute_name,
                            'type' => 'select',
                            'order_by' => 'menu_order',
                            'has_archives' => false,
                        ));
                    }
                    
                    $taxonomy = wc_attribute_taxonomy_name($attribute_name);
                    
                    // Add terms
                    $term_ids = array();
                    foreach ($attribute['options'] as $option) {
                        $term = term_exists($option, $taxonomy);
                        
                        if (!$term) {
                            $term = wp_insert_term($option, $taxonomy);
                        }
                        
                        if (!is_wp_error($term)) {
                            $term_ids[] = $term['term_id'];
                        }
                    }
                    
                    // Set terms for product
                    wp_set_object_terms($product_id, $term_ids, $taxonomy);
                    
                    // Add to product attributes array
                    $product_attributes[$taxonomy] = array(
                        'name' => $taxonomy,
                        'value' => '',
                        'position' => count($product_attributes),
                        'is_visible' => isset($attribute['visible']) ? (bool)$attribute['visible'] : true,
                        'is_variation' => isset($attribute['variation']) ? (bool)$attribute['variation'] : false,
                        'is_taxonomy' => true,
                    );
                }
                
                update_post_meta($product_id, '_product_attributes', $product_attributes);
            }
        }
    }

    /**
     * Get a post by title, including those in trash
     */
    private function get_post_by_title($title, $post_type = 'post') {
        // First check for published/draft posts
        $existing = get_page_by_title($title, OBJECT, $post_type);
        if ($existing) {
            return $existing;
        }
        
        // Check for trashed posts
        global $wpdb;
        $post = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT ID, post_status FROM $wpdb->posts WHERE post_title = %s AND post_type = %s",
                $title,
                $post_type
            )
        );
        
        return $post ? get_post($post->ID) : null;
    }
    
    /**
     * Create block-based content for recipes
     */
    private function create_recipe_content($recipe) {
        // Start with the description as a paragraph
        $content = '<!-- wp:paragraph {"className":"recipe-description"} -->
<p class="recipe-description">' . esc_html($recipe['description']) . '</p>
<!-- /wp:paragraph -->';
        
        // No need to add other recipe elements here since they're handled by the template patterns
        
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
new PCS_Universal_Importer();

// Add admin notice for import results
function pcs_universal_importer_admin_notice() {
    $notice = get_transient('pcs_content_import_notice');
    
    if ($notice) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($notice) . '</p></div>';
        delete_transient('pcs_content_import_notice');
    }
}
add_action('admin_notices', 'pcs_universal_importer_admin_notice');
