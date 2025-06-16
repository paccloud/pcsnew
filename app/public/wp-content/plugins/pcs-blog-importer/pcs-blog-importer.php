<?php
/**
 * Plugin Name: PCS Blog Importer
 * Description: Import blog posts from Shopify to WordPress with block-based formatting
 * Version: 1.0
 * Author: Pacific Cloud Seafood
 * Text Domain: pcs-blog-importer
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('PCS_BI_VERSION', '1.0.0');
define('PCS_BI_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PCS_BI_PLUGIN_URL', plugin_dir_url(__FILE__));

class PCS_Blog_Importer {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'handle_import'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function enqueue_scripts($hook) {
        // Only load on our import page
        if ($hook != 'tools_page_pcs-blog-import') {
            return;
        }

        wp_enqueue_style('pcs-blog-importer', plugin_dir_url(__FILE__) . 'style.css', array(), PCS_BI_VERSION);
    }

    public function add_admin_menu() {
        add_submenu_page(
            'tools.php',                  // Parent slug (Tools)
            'Import Blog Posts',          // Page title
            'Import Blog Posts',          // Menu title
            'manage_options',             // Capability
            'pcs-blog-import',            // Menu slug
            array($this, 'import_page')   // Callback function
        );
    }

    public function import_page() {
        ?>
        <div class="wrap">
            <h1>Import Blog Posts</h1>

            <div class="pcs-blog-import-tabs">
                <div class="nav-tab-wrapper">
                    <a href="#shopify-import" class="nav-tab nav-tab-active">Import from Shopify</a>
                    <a href="#file-import" class="nav-tab">Upload Blog File</a>
                </div>

                <div id="shopify-import" class="tab-content active">
                    <h2>Import from Shopify</h2>
                    <p>Import blog posts from Shopify export files.</p>

                    <form method="post" enctype="multipart/form-data">
                        <?php wp_nonce_field('pcs_blog_import_action', 'pcs_blog_import_nonce'); ?>
                        <input type="hidden" name="pcs_blog_import_action" value="shopify_import">

                        <table class="form-table">
                            <tr>
                                <th scope="row"><label for="shopify_file">Shopify Export File</label></th>
                                <td>
                                    <input type="file" name="shopify_file" id="shopify_file">
                                    <p class="description">Upload a Shopify blog export file (JSON format).</p>
                                </td>
                            </tr>
                        </table>

                        <?php submit_button('Import from Shopify'); ?>
                    </form>
                </div>

                <div id="file-import" class="tab-content">
                    <h2>Upload Blog File</h2>
                    <p>Upload individual blog post files in Markdown or HTML format.</p>

                    <form method="post" enctype="multipart/form-data">
                        <?php wp_nonce_field('pcs_blog_import_action', 'pcs_blog_import_nonce'); ?>
                        <input type="hidden" name="pcs_blog_import_action" value="file_import">

                        <table class="form-table">
                            <tr>
                                <th scope="row"><label for="blog_file">Blog Post File</label></th>
                                <td>
                                    <input type="file" name="blog_file" id="blog_file">
                                    <p class="description">Upload a blog post file (Markdown or HTML format).</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="post_category">Category</label></th>
                                <td>
                                    <?php wp_dropdown_categories(array(
                                        'name' => 'post_category',
                                        'show_option_none' => '-- Select Category --',
                                        'option_none_value' => '',
                                        'hierarchical' => true,
                                    )); ?>
                                </td>
                            </tr>
                        </table>

                        <?php submit_button('Import Blog Post'); ?>
                    </form>
                </div>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            // Tab functionality
            $('.nav-tab').on('click', function(e) {
                e.preventDefault();

                // Get the tab ID
                var tabId = $(this).attr('href');

                // Remove active class from all tabs and content
                $('.nav-tab').removeClass('nav-tab-active');
                $('.tab-content').removeClass('active');

                // Add active class to current tab and content
                $(this).addClass('nav-tab-active');
                $(tabId).addClass('active');
            });
        });
        </script>
        <?php
    }

    public function handle_import() {
        // Check if we're on the right page and have a form submission
        if (!isset($_POST['pcs_blog_import_nonce']) || !wp_verify_nonce($_POST['pcs_blog_import_nonce'], 'pcs_blog_import_action')) {
            return;
        }

        // Check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }

        // Handle different import actions
        $action = isset($_POST['pcs_blog_import_action']) ? $_POST['pcs_blog_import_action'] : '';

        switch ($action) {
            case 'shopify_import':
                $this->handle_shopify_import();
                break;

            case 'file_import':
                $this->handle_file_import();
                break;
        }
    }

    private function handle_shopify_import() {
        // Check if a file was uploaded
        if (!isset($_FILES['shopify_file']) || $_FILES['shopify_file']['error'] !== UPLOAD_ERR_OK) {
            add_settings_error('pcs_blog_import', 'file_upload', 'Error uploading file. Please try again.', 'error');
            return;
        }

        // Validate file type
        $file_info = pathinfo($_FILES['shopify_file']['name']);
        $extension = strtolower($file_info['extension']);

        if ($extension !== 'json' && $extension !== 'md') {
            add_settings_error('pcs_blog_import', 'file_type', 'Invalid file type. Please upload a JSON or Markdown file.', 'error');
            return;
        }

        // Get the file content
        $file_content = file_get_contents($_FILES['shopify_file']['tmp_name']);

        if (!$file_content) {
            add_settings_error('pcs_blog_import', 'file_read', 'Error reading file content. Please try again.', 'error');
            return;
        }

        // Include the Shopify to WordPress converter
        require_once PCS_BI_PLUGIN_DIR . 'shopify-to-wordpress.php';

        // Create a temporary directory for the import
        $upload_dir = wp_upload_dir();
        $import_dir = $upload_dir['basedir'] . '/shopify-import/';

        if (!file_exists($import_dir)) {
            mkdir($import_dir, 0755, true);
        }

        // Handle based on file type
        if ($extension === 'md') {
            // It's a markdown file, process it directly
            $import_file = $import_dir . sanitize_file_name($_FILES['shopify_file']['name']);
            file_put_contents($import_file, $file_content);

            $importer = new PCS_Shopify_Blog_Importer();
            $result = $importer->process_markdown_file($import_file);

            if ($result) {
                add_settings_error('pcs_blog_import', 'import_success', 'Successfully imported blog post: ' . $result['title'], 'success');
            } else {
                add_settings_error('pcs_blog_import', 'import_error', 'Error importing blog post.', 'error');
            }
        } else {
            // It's a JSON file, process it as Shopify export
            $import_file = $import_dir . 'blog-posts.json';
            file_put_contents($import_file, $file_content);

            // Run the importer
            $importer = new PCS_Shopify_Blog_Importer($import_file);
            $result = $importer->import_blog_posts();

            if (is_array($result) && isset($result['success'])) {
                if ($result['success']) {
                    add_settings_error('pcs_blog_import', 'import_success', sprintf('Successfully imported %d blog posts.', $result['imported']), 'success');
                } else {
                    add_settings_error('pcs_blog_import', 'import_error', 'Error importing blog posts: ' . $result['message'], 'error');
                }
            } else {
                add_settings_error('pcs_blog_import', 'import_error', 'Error: Importer did not return a valid result.', 'error');
            }
        }
    }

    private function handle_file_import() {
        // Check if a file was uploaded
        if (!isset($_FILES['blog_file']) || $_FILES['blog_file']['error'] !== UPLOAD_ERR_OK) {
            add_settings_error('pcs_blog_import', 'file_upload', 'Error uploading file. Please try again.', 'error');
            return;
        }

        // Get the file extension
        $file_info = pathinfo($_FILES['blog_file']['name']);
        $extension = strtolower($file_info['extension']);

        // Validate file type
        if (!in_array($extension, array('md', 'html', 'htm', 'json'))) {
            add_settings_error('pcs_blog_import', 'file_type', 'Unsupported file type. Please upload a Markdown (.md), HTML (.html), or JSON (.json) file.', 'error');
            return;
        }

        // Include the Shopify to WordPress converter
        require_once PCS_BI_PLUGIN_DIR . 'shopify-to-wordpress.php';

        // Create a temporary directory for the import
        $upload_dir = wp_upload_dir();
        $import_dir = $upload_dir['basedir'] . '/blog-import/';

        if (!file_exists($import_dir)) {
            mkdir($import_dir, 0755, true);
        }

        // Save the uploaded file
        $import_file = $import_dir . sanitize_file_name($_FILES['blog_file']['name']);
        if (!move_uploaded_file($_FILES['blog_file']['tmp_name'], $import_file)) {
            add_settings_error('pcs_blog_import', 'file_move', 'Error saving uploaded file. Please try again.', 'error');
            return;
        }

        // Get category ID
        $category_id = isset($_POST['post_category']) ? intval($_POST['post_category']) : 0;

        // Process the file based on its extension
        switch ($extension) {
            case 'md':
                // Process Markdown file
                $importer = new PCS_Shopify_Blog_Importer();
                $result = $importer->process_markdown_file($import_file);
                break;

            case 'html':
            case 'htm':
                // Process HTML file
                $file_content = file_get_contents($import_file);
                $importer = new PCS_Shopify_Blog_Importer();
                $result = $importer->process_html_content($file_content, $category_id);
                break;

            case 'json':
                // Process JSON file as Shopify export
                $importer = new PCS_Shopify_Blog_Importer($import_file);
                $result = $importer->import_blog_posts();

                if (is_array($result) && isset($result['success'])) {
                    if ($result['success']) {
                        add_settings_error('pcs_blog_import', 'import_success', sprintf('Successfully imported %d blog posts.', $result['imported']), 'success');
                    } else {
                        add_settings_error('pcs_blog_import', 'import_error', 'Error importing blog posts: ' . $result['message'], 'error');
                    }
                    return;
                } else {
                    add_settings_error('pcs_blog_import', 'import_error', 'Error: Importer did not return a valid result.', 'error');
                    return;
                }
                break;
        }

        // Handle result for MD/HTML files
        if ($result) {
            if (is_array($result) && isset($result['title'])) {
                add_settings_error('pcs_blog_import', 'import_success', 'Successfully imported blog post: ' . $result['title'], 'success');
            } else {
                add_settings_error('pcs_blog_import', 'import_success', 'Successfully imported blog post.', 'success');
            }
        } else {
            add_settings_error('pcs_blog_import', 'import_error', 'Error importing blog post.', 'error');
        }
    }
}

// Initialize the plugin
$pcs_blog_importer = new PCS_Blog_Importer();

// Include the Shopify to WordPress converter if accessed directly
if (defined('WP_CLI') || (isset($_GET['page']) && $_GET['page'] === 'pcs-blog-import')) {
    require_once PCS_BI_PLUGIN_DIR . 'shopify-to-wordpress.php';
}
