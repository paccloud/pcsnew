<?php
/**
 * Admin Class
 * 
 * Handles admin functionality
 * 
 * @package PCS_Enhanced_Recipes
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin Class
 */
class PCS_Recipe_Admin {
    /**
     * Constructor
     */
    public function __construct() {
        // Add settings page
        add_action('admin_menu', array($this, 'add_settings_page'));
        
        // Register settings
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    /**
     * Add settings page
     */
    public function add_settings_page() {
        add_submenu_page(
            'edit.php?post_type=recipe',
            __('Recipe Settings', 'pcs-enhanced-recipes'),
            __('Settings', 'pcs-enhanced-recipes'),
            'manage_options',
            'pcs-recipe-settings',
            array($this, 'render_settings_page')
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('pcs_recipe_settings', 'pcs_recipe_settings');
        
        add_settings_section(
            'pcs_recipe_general_section',
            __('General Settings', 'pcs-enhanced-recipes'),
            array($this, 'render_general_section'),
            'pcs_recipe_settings'
        );
        
        add_settings_field(
            'recipe_slug',
            __('Recipe Slug', 'pcs-enhanced-recipes'),
            array($this, 'render_recipe_slug_field'),
            'pcs_recipe_settings',
            'pcs_recipe_general_section'
        );
        
        add_settings_field(
            'display_settings',
            __('Display Settings', 'pcs-enhanced-recipes'),
            array($this, 'render_display_settings_field'),
            'pcs_recipe_settings',
            'pcs_recipe_general_section'
        );
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('pcs_recipe_settings');
                do_settings_sections('pcs_recipe_settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Render general section
     */
    public function render_general_section() {
        echo '<p>' . __('Configure general settings for the recipe plugin.', 'pcs-enhanced-recipes') . '</p>';
    }
    
    /**
     * Render recipe slug field
     */
    public function render_recipe_slug_field() {
        $options = get_option('pcs_recipe_settings');
        $slug = isset($options['recipe_slug']) ? $options['recipe_slug'] : 'recipe';
        ?>
        <input type="text" name="pcs_recipe_settings[recipe_slug]" value="<?php echo esc_attr($slug); ?>" />
        <p class="description"><?php _e('The slug used for recipe URLs. After changing this, go to Settings > Permalinks and click Save to update rewrite rules.', 'pcs-enhanced-recipes'); ?></p>
        <?php
    }
    
    /**
     * Render display settings field
     */
    public function render_display_settings_field() {
        $options = get_option('pcs_recipe_settings');
        $show_featured_image = isset($options['show_featured_image']) ? $options['show_featured_image'] : 1;
        $show_prep_time = isset($options['show_prep_time']) ? $options['show_prep_time'] : 1;
        $show_cook_time = isset($options['show_cook_time']) ? $options['show_cook_time'] : 1;
        $show_total_time = isset($options['show_total_time']) ? $options['show_total_time'] : 1;
        $show_servings = isset($options['show_servings']) ? $options['show_servings'] : 1;
        ?>
        <p>
            <input type="checkbox" id="show_featured_image" name="pcs_recipe_settings[show_featured_image]" value="1" <?php checked(1, $show_featured_image); ?> />
            <label for="show_featured_image"><?php _e('Show featured image', 'pcs-enhanced-recipes'); ?></label>
        </p>
        <p>
            <input type="checkbox" id="show_prep_time" name="pcs_recipe_settings[show_prep_time]" value="1" <?php checked(1, $show_prep_time); ?> />
            <label for="show_prep_time"><?php _e('Show prep time', 'pcs-enhanced-recipes'); ?></label>
        </p>
        <p>
            <input type="checkbox" id="show_cook_time" name="pcs_recipe_settings[show_cook_time]" value="1" <?php checked(1, $show_cook_time); ?> />
            <label for="show_cook_time"><?php _e('Show cook time', 'pcs-enhanced-recipes'); ?></label>
        </p>
        <p>
            <input type="checkbox" id="show_total_time" name="pcs_recipe_settings[show_total_time]" value="1" <?php checked(1, $show_total_time); ?> />
            <label for="show_total_time"><?php _e('Show total time', 'pcs-enhanced-recipes'); ?></label>
        </p>
        <p>
            <input type="checkbox" id="show_servings" name="pcs_recipe_settings[show_servings]" value="1" <?php checked(1, $show_servings); ?> />
            <label for="show_servings"><?php _e('Show servings', 'pcs-enhanced-recipes'); ?></label>
        </p>
        <?php
    }
}
