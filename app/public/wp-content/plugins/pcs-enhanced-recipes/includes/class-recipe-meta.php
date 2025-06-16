<?php
/**
 * Recipe Meta Class
 * 
 * Handles recipe meta fields and data
 * 
 * @package PCS_Enhanced_Recipes
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Recipe Meta Class
 */
class PCS_Recipe_Meta {
    /**
     * Constructor
     */
    public function __construct() {
        // Initialize hooks
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'), 10, 2);
    }
    
    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'pcs_recipe_details',
            __('Recipe Details', 'pcs-enhanced-recipes'),
            array($this, 'render_recipe_details_meta_box'),
            'recipe',
            'normal',
            'high'
        );
    }
    
    /**
     * Render recipe details meta box
     */
    public function render_recipe_details_meta_box($post) {
        // Add nonce for security
        wp_nonce_field('pcs_recipe_details_nonce', 'pcs_recipe_details_nonce');
        
        // Get current values
        $prep_time = get_post_meta($post->ID, '_pcs_prep_time', true);
        $cook_time = get_post_meta($post->ID, '_pcs_cook_time', true);
        $total_time = get_post_meta($post->ID, '_pcs_total_time', true);
        $servings = get_post_meta($post->ID, '_pcs_servings', true);
        $calories = get_post_meta($post->ID, '_pcs_calories', true);
        $protein = get_post_meta($post->ID, '_pcs_protein', true);
        $carbohydrates = get_post_meta($post->ID, '_pcs_carbohydrates', true);
        $fat = get_post_meta($post->ID, '_pcs_fat', true);
        $saturated_fat = get_post_meta($post->ID, '_pcs_saturated_fat', true);
        $cholesterol = get_post_meta($post->ID, '_pcs_cholesterol', true);
        $sodium = get_post_meta($post->ID, '_pcs_sodium', true);
        $fiber = get_post_meta($post->ID, '_pcs_fiber', true);
        $sugar = get_post_meta($post->ID, '_pcs_sugar', true);
        
        // Output fields
        ?>
        <div class="pcs-recipe-meta-box">
            <div class="pcs-recipe-field">
                <label for="pcs_prep_time"><?php _e('Prep Time (minutes)', 'pcs-enhanced-recipes'); ?></label>
                <input type="number" id="pcs_prep_time" name="pcs_prep_time" value="<?php echo esc_attr($prep_time); ?>" min="0" />
            </div>
            
            <div class="pcs-recipe-field">
                <label for="pcs_cook_time"><?php _e('Cook Time (minutes)', 'pcs-enhanced-recipes'); ?></label>
                <input type="number" id="pcs_cook_time" name="pcs_cook_time" value="<?php echo esc_attr($cook_time); ?>" min="0" />
            </div>
            
            <div class="pcs-recipe-field">
                <label for="pcs_total_time"><?php _e('Total Time (minutes)', 'pcs-enhanced-recipes'); ?></label>
                <input type="number" id="pcs_total_time" name="pcs_total_time" value="<?php echo esc_attr($total_time); ?>" min="0" />
            </div>
            
            <div class="pcs-recipe-field">
                <label for="pcs_servings"><?php _e('Servings', 'pcs-enhanced-recipes'); ?></label>
                <input type="number" id="pcs_servings" name="pcs_servings" value="<?php echo esc_attr($servings); ?>" min="1" />
            </div>
            
            <div class="pcs-recipe-field">
                <label for="pcs_calories"><?php _e('Calories (per serving)', 'pcs-enhanced-recipes'); ?></label>
                <input type="number" id="pcs_calories" name="pcs_calories" value="<?php echo esc_attr($calories); ?>" min="0" />
            </div>
            
            <h3><?php _e('Nutrition Facts', 'pcs-enhanced-recipes'); ?></h3>
            <p class="description"><?php _e('All nutrition values are per serving', 'pcs-enhanced-recipes'); ?></p>
            
            <div class="pcs-recipe-field">
                <label for="pcs_protein"><?php _e('Protein (g)', 'pcs-enhanced-recipes'); ?></label>
                <input type="number" id="pcs_protein" name="pcs_protein" value="<?php echo esc_attr($protein); ?>" min="0" step="0.1" />
            </div>
            
            <div class="pcs-recipe-field">
                <label for="pcs_carbohydrates"><?php _e('Carbohydrates (g)', 'pcs-enhanced-recipes'); ?></label>
                <input type="number" id="pcs_carbohydrates" name="pcs_carbohydrates" value="<?php echo esc_attr($carbohydrates); ?>" min="0" step="0.1" />
            </div>
            
            <div class="pcs-recipe-field">
                <label for="pcs_fat"><?php _e('Total Fat (g)', 'pcs-enhanced-recipes'); ?></label>
                <input type="number" id="pcs_fat" name="pcs_fat" value="<?php echo esc_attr($fat); ?>" min="0" step="0.1" />
            </div>
            
            <div class="pcs-recipe-field">
                <label for="pcs_saturated_fat"><?php _e('Saturated Fat (g)', 'pcs-enhanced-recipes'); ?></label>
                <input type="number" id="pcs_saturated_fat" name="pcs_saturated_fat" value="<?php echo esc_attr($saturated_fat); ?>" min="0" step="0.1" />
            </div>
            
            <div class="pcs-recipe-field">
                <label for="pcs_cholesterol"><?php _e('Cholesterol (mg)', 'pcs-enhanced-recipes'); ?></label>
                <input type="number" id="pcs_cholesterol" name="pcs_cholesterol" value="<?php echo esc_attr($cholesterol); ?>" min="0" step="0.1" />
            </div>
            
            <div class="pcs-recipe-field">
                <label for="pcs_sodium"><?php _e('Sodium (mg)', 'pcs-enhanced-recipes'); ?></label>
                <input type="number" id="pcs_sodium" name="pcs_sodium" value="<?php echo esc_attr($sodium); ?>" min="0" step="0.1" />
            </div>
            
            <div class="pcs-recipe-field">
                <label for="pcs_fiber"><?php _e('Dietary Fiber (g)', 'pcs-enhanced-recipes'); ?></label>
                <input type="number" id="pcs_fiber" name="pcs_fiber" value="<?php echo esc_attr($fiber); ?>" min="0" step="0.1" />
            </div>
            
            <div class="pcs-recipe-field">
                <label for="pcs_sugar"><?php _e('Sugars (g)', 'pcs-enhanced-recipes'); ?></label>
                <input type="number" id="pcs_sugar" name="pcs_sugar" value="<?php echo esc_attr($sugar); ?>" min="0" step="0.1" />
            </div>
        </div>
        <?php
    }
    
    /**
     * Save meta box data
     */
    public function save_meta_boxes($post_id, $post) {
        // Check if our nonce is set
        if (!isset($_POST['pcs_recipe_details_nonce'])) {
            return;
        }
        
        // Verify that the nonce is valid
        if (!wp_verify_nonce($_POST['pcs_recipe_details_nonce'], 'pcs_recipe_details_nonce')) {
            return;
        }
        
        // If this is an autosave, our form has not been submitted, so we don't want to do anything
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check the user's permissions
        if ('recipe' === $_POST['post_type']) {
            if (!current_user_can('edit_post', $post_id)) {
                return;
            }
        }
        
        // Save the data
        if (isset($_POST['pcs_prep_time'])) {
            update_post_meta($post_id, '_pcs_prep_time', sanitize_text_field($_POST['pcs_prep_time']));
        }
        
        if (isset($_POST['pcs_cook_time'])) {
            update_post_meta($post_id, '_pcs_cook_time', sanitize_text_field($_POST['pcs_cook_time']));
        }
        
        if (isset($_POST['pcs_total_time'])) {
            update_post_meta($post_id, '_pcs_total_time', sanitize_text_field($_POST['pcs_total_time']));
        }
        
        if (isset($_POST['pcs_servings'])) {
            update_post_meta($post_id, '_pcs_servings', sanitize_text_field($_POST['pcs_servings']));
        }
        
        if (isset($_POST['pcs_calories'])) {
            update_post_meta($post_id, '_pcs_calories', sanitize_text_field($_POST['pcs_calories']));
        }
        
        // Save nutrition facts
        if (isset($_POST['pcs_protein'])) {
            update_post_meta($post_id, '_pcs_protein', sanitize_text_field($_POST['pcs_protein']));
        }
        
        if (isset($_POST['pcs_carbohydrates'])) {
            update_post_meta($post_id, '_pcs_carbohydrates', sanitize_text_field($_POST['pcs_carbohydrates']));
        }
        
        if (isset($_POST['pcs_fat'])) {
            update_post_meta($post_id, '_pcs_fat', sanitize_text_field($_POST['pcs_fat']));
        }
        
        if (isset($_POST['pcs_saturated_fat'])) {
            update_post_meta($post_id, '_pcs_saturated_fat', sanitize_text_field($_POST['pcs_saturated_fat']));
        }
        
        if (isset($_POST['pcs_cholesterol'])) {
            update_post_meta($post_id, '_pcs_cholesterol', sanitize_text_field($_POST['pcs_cholesterol']));
        }
        
        if (isset($_POST['pcs_sodium'])) {
            update_post_meta($post_id, '_pcs_sodium', sanitize_text_field($_POST['pcs_sodium']));
        }
        
        if (isset($_POST['pcs_fiber'])) {
            update_post_meta($post_id, '_pcs_fiber', sanitize_text_field($_POST['pcs_fiber']));
        }
        
        if (isset($_POST['pcs_sugar'])) {
            update_post_meta($post_id, '_pcs_sugar', sanitize_text_field($_POST['pcs_sugar']));
        }
    }
}
