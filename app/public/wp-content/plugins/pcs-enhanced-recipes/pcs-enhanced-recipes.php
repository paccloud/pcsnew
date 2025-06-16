<?php
/**
 * Plugin Name: PCS Enhanced Recipes
 * Plugin URI: https://pacificcloudseafood.com/
 * Description: Enhanced recipe plugin with features inspired by WP Recipe Maker including improved recipe cards, SEO optimization, adjustable servings, and responsive design.
 * Version: 1.0.0
 * Author: Pacific Cloud Seafood
 * Author URI: https://pacificcloudseafood.com/
 * Text Domain: pcs-enhanced-recipes
 * Domain Path: /languages
 * 
 * @package PCS_Enhanced_Recipes
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('PCS_ER_VERSION', '1.0.0');
define('PCS_ER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PCS_ER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PCS_ER_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
class PCS_Enhanced_Recipes {
    /**
     * Instance of this class
     *
     * @var object
     */
    private static $instance;

    /**
     * Get the instance of this class
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        // Include required files
        $this->includes();

        // Hook into WordPress
        $this->init_hooks();
        
        // Register activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Include required files
     */
    private function includes() {
        // Core functionality
        $this->include_file('includes/class-recipe-post-type.php');
        $this->include_file('includes/class-recipe-meta.php');
        $this->include_file('includes/class-recipe-template.php');
        $this->include_file('includes/class-recipe-schema.php');
        
        // Frontend functionality
        $this->include_file('includes/class-recipe-assets.php');
        $this->include_file('includes/class-recipe-shortcodes.php');
        
        // Admin functionality
        if (is_admin()) {
            $this->include_file('includes/admin/class-admin.php');
        }
    }

    /**
     * Include a file
     */
    private function include_file($file) {
        require_once PCS_ER_PLUGIN_DIR . $file;
    }

    /**
     * Hook into WordPress
     */
    private function init_hooks() {
        // Initialize components
        add_action('init', array($this, 'init_components'), 0);
        
        // Load text domain
        add_action('plugins_loaded', array($this, 'load_textdomain'));
    }
    
    /**
     * Initialize components
     */
    public function init_components() {
        // Initialize post type
        if (class_exists('PCS_Recipe_Post_Type')) {
            $this->post_type = new PCS_Recipe_Post_Type();
        }
        
        // Initialize meta fields
        if (class_exists('PCS_Recipe_Meta')) {
            $this->meta = new PCS_Recipe_Meta();
        }
        
        // Initialize template system
        if (class_exists('PCS_Recipe_Template')) {
            $this->template = new PCS_Recipe_Template();
        }
        
        // Initialize schema
        if (class_exists('PCS_Recipe_Schema')) {
            $this->schema = new PCS_Recipe_Schema();
        }
        
        // Initialize assets
        if (class_exists('PCS_Recipe_Assets')) {
            $this->assets = new PCS_Recipe_Assets();
        }
        
        // Initialize shortcodes
        if (class_exists('PCS_Recipe_Shortcodes')) {
            $this->shortcodes = new PCS_Recipe_Shortcodes();
        }

        // Clear template cache on plugin load
        $this->clear_template_cache();
    }

    /**
     * Clear template cache
     */
    private function clear_template_cache() {
        // Clear template cache
        if (function_exists('wp_cache_delete')) {
            wp_cache_delete('block_templates', 'theme');
            wp_cache_delete('block_template_parts', 'theme');
        }
    }
    
    /**
     * Load plugin text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain('pcs-enhanced-recipes', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    /**
     * Force template refresh
     */
    public function force_template_refresh() {
        if (!get_transient('pcs_recipe_template_refreshed')) {
            flush_rewrite_rules();
            pcs_clear_recipe_cache();
            set_transient('pcs_recipe_template_refreshed', true, HOUR_IN_SECONDS);
        }
    }
}

// Initialize the plugin
function pcs_enhanced_recipes() {
    return PCS_Enhanced_Recipes::get_instance();
}

// Start the plugin
add_action('plugins_loaded', 'pcs_enhanced_recipes');
