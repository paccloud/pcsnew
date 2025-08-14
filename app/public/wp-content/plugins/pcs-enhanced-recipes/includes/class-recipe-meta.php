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
        
        add_meta_box(
            'pcs_recipe_ingredients',
            __('Recipe Ingredients', 'pcs-enhanced-recipes'),
            array($this, 'render_recipe_ingredients_meta_box'),
            'recipe',
            'normal',
            'high'
        );
        
        add_meta_box(
            'pcs_recipe_instructions',
            __('Recipe Instructions', 'pcs-enhanced-recipes'),
            array($this, 'render_recipe_instructions_meta_box'),
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
     * Render recipe ingredients meta box
     */
    public function render_recipe_ingredients_meta_box($post) {
        // Add nonce for security
        wp_nonce_field('pcs_recipe_ingredients_nonce', 'pcs_recipe_ingredients_nonce');

        // Get current values
        $ingredients = get_post_meta($post->ID, '_pcs_ingredients', true);
        $structured_ingredients = get_post_meta($post->ID, '_pcs_structured_ingredients', true);

        // Output fields
        ?>
        <div class="pcs-recipe-meta-box">
            <div class="pcs-recipe-field">
                <label for="pcs_ingredients"><?php _e('Ingredients', 'pcs-enhanced-recipes'); ?></label>
                <textarea id="pcs_ingredients" name="pcs_ingredients" rows="10" cols="50" style="width: 100%;"><?php echo esc_textarea($ingredients); ?></textarea>
                <p class="description"><?php _e('Enter each ingredient on a new line. Use format: "2 cups flour" or "1/2 teaspoon salt"', 'pcs-enhanced-recipes'); ?></p>
            </div>

            <div class="pcs-recipe-field" style="margin-top: 20px;">
                <label><?php _e('Structured Ingredients (Auto-generated)', 'pcs-enhanced-recipes'); ?></label>
                <div id="pcs-structured-ingredients-preview" style="background: #f9f9f9; padding: 10px; border: 1px solid #ddd; min-height: 100px;">
                    <?php if ($structured_ingredients): ?>
                        <pre><?php echo esc_html(json_encode($structured_ingredients, JSON_PRETTY_PRINT)); ?></pre>
                    <?php else: ?>
                        <em><?php _e('Save the recipe to see structured ingredients preview', 'pcs-enhanced-recipes'); ?></em>
                    <?php endif; ?>
                </div>
                <p class="description"><?php _e('This shows how ingredients will be parsed for serving size calculations.', 'pcs-enhanced-recipes'); ?></p>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('#pcs_ingredients').on('input', function() {
                // Could add real-time preview here in the future
            });
        });
        </script>
        <?php
    }

    /**
     * Render recipe instructions meta box
     */
    public function render_recipe_instructions_meta_box($post) {
        // Add nonce for security
        wp_nonce_field('pcs_recipe_instructions_nonce', 'pcs_recipe_instructions_nonce');

        // Get current values
        $instructions = get_post_meta($post->ID, '_pcs_instructions', true);

        // Output fields
        ?>
        <div class="pcs-recipe-meta-box">
            <div class="pcs-recipe-field">
                <label for="pcs_instructions"><?php _e('Instructions', 'pcs-enhanced-recipes'); ?></label>
                <textarea id="pcs_instructions" name="pcs_instructions" rows="15" cols="50" style="width: 100%;"><?php echo esc_textarea($instructions); ?></textarea>
                <p class="description"><?php _e('Enter each instruction step on a new line or separate with double line breaks.', 'pcs-enhanced-recipes'); ?></p>
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

        // Save ingredients and parse them into structured format
        if (isset($_POST['pcs_ingredients'])) {
            $ingredients = sanitize_textarea_field($_POST['pcs_ingredients']);
            update_post_meta($post_id, '_pcs_ingredients', $ingredients);

            // Parse ingredients into structured format
            $structured_ingredients = $this->parse_ingredients($ingredients);
            update_post_meta($post_id, '_pcs_structured_ingredients', $structured_ingredients);
        }

        // Save instructions
        if (isset($_POST['pcs_instructions'])) {
            update_post_meta($post_id, '_pcs_instructions', sanitize_textarea_field($_POST['pcs_instructions']));
        }
    }

    /**
     * Parse ingredients text into structured format for serving size calculations
     */
    private function parse_ingredients($ingredients_text) {
        if (empty($ingredients_text)) {
            return array();
        }

        $lines = explode("\n", $ingredients_text);
        $structured = array();

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            $parsed = $this->parse_single_ingredient($line);
            if ($parsed) {
                $structured[] = $parsed;
            }
        }

        return $structured;
    }

    /**
     * Parse a single ingredient line
     */
    private function parse_single_ingredient($line) {
        // Remove any leading dashes or bullets
        $line = preg_replace('/^[-•*]\s*/', '', $line);

        // Pattern to match: amount unit ingredient
        // Examples: "2 cups flour", "1/2 teaspoon salt", "1 large egg"
        $pattern = '/^(\d+(?:\/\d+)?(?:\.\d+)?|\d*\s*\d+\/\d+)\s*([a-zA-Z]+(?:\s+[a-zA-Z]+)*?)?\s+(.+)$/';

        if (preg_match($pattern, $line, $matches)) {
            $amount = trim($matches[1]);
            $unit = isset($matches[2]) ? trim($matches[2]) : '';
            $ingredient = trim($matches[3]);

            // Convert fractions to decimals for calculations
            $decimal_amount = $this->fraction_to_decimal($amount);

            return array(
                'original' => $line,
                'amount' => $amount,
                'amount_decimal' => $decimal_amount,
                'unit' => $unit,
                'ingredient' => $ingredient,
                'scalable' => $decimal_amount !== null
            );
        }

        // If no amount pattern found, treat as non-scalable ingredient
        return array(
            'original' => $line,
            'amount' => '',
            'amount_decimal' => null,
            'unit' => '',
            'ingredient' => $line,
            'scalable' => false
        );
    }

    /**
     * Convert fraction strings to decimal numbers
     */
    private function fraction_to_decimal($fraction_str) {
        $fraction_str = trim($fraction_str);

        // Handle mixed numbers like "1 1/2"
        if (preg_match('/^(\d+)\s+(\d+)\/(\d+)$/', $fraction_str, $matches)) {
            $whole = intval($matches[1]);
            $numerator = intval($matches[2]);
            $denominator = intval($matches[3]);
            return $whole + ($numerator / $denominator);
        }

        // Handle simple fractions like "1/2"
        if (preg_match('/^(\d+)\/(\d+)$/', $fraction_str, $matches)) {
            $numerator = intval($matches[1]);
            $denominator = intval($matches[2]);
            return $numerator / $denominator;
        }

        // Handle decimal numbers
        if (is_numeric($fraction_str)) {
            return floatval($fraction_str);
        }

        return null;
    }
}
