<?php
/**
 * Plugin Name: Recipe System Fixer
 * Description: Fixes the recipe system by creating sample recipes and ensuring proper functionality
 * Version: 1.0
 * Author: Pacific Cloud Seafood
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Recipe System Fixer Class
 */
class RecipeSystemFixer {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'handle_fix_request'));
    }
    
    /**
     * Add admin menu item
     */
    public function add_admin_menu() {
        add_management_page(
            'Fix Recipe System',
            'Fix Recipe System',
            'manage_options',
            'fix-recipe-system',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Admin page content
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1>Recipe System Fixer</h1>
            
            <?php if (isset($_GET['fixed']) && $_GET['fixed'] === 'true') : ?>
                <div class="notice notice-success">
                    <p><strong>Recipe system has been fixed!</strong></p>
                    <ul>
                        <li>✓ Sample recipes created</li>
                        <li>✓ Permalinks flushed</li>
                        <li>✓ Taxonomies populated</li>
                    </ul>
                    <p><a href="<?php echo home_url('/recipe/'); ?>" target="_blank">View Recipe Archive</a></p>
                </div>
            <?php endif; ?>
            
            <div class="card">
                <h2>Fix Recipe System Issues</h2>
                <p>This tool will fix the following issues:</p>
                <ul>
                    <li>Create sample recipe posts if they don't exist</li>
                    <li>Set up proper taxonomy terms</li>
                    <li>Flush permalink structure</li>
                    <li>Ensure recipe archive works properly</li>
                </ul>
                
                <form method="post" action="">
                    <?php wp_nonce_field('fix_recipe_system', 'fix_recipe_nonce'); ?>
                    <p>
                        <input type="submit" name="fix_recipes" class="button-primary" value="Fix Recipe System" />
                    </p>
                </form>
            </div>
            
            <div class="card">
                <h2>Current Status</h2>
                <?php $this->show_current_status(); ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Show current recipe system status
     */
    public function show_current_status() {
        // Check if recipe post type exists
        $recipe_post_type = post_type_exists('recipe');
        echo '<p><strong>Recipe Post Type:</strong> ' . ($recipe_post_type ? '✓ Registered' : '✗ Not found') . '</p>';
        
        // Count existing recipes
        $recipe_count = wp_count_posts('recipe');
        $published_recipes = isset($recipe_count->publish) ? $recipe_count->publish : 0;
        echo '<p><strong>Published Recipes:</strong> ' . $published_recipes . '</p>';
        
        // Check taxonomies
        $taxonomies = array('recipe_fish_type', 'recipe_meal_type', 'recipe_technique', 'recipe_difficulty');
        foreach ($taxonomies as $taxonomy) {
            $exists = taxonomy_exists($taxonomy);
            $term_count = $exists ? wp_count_terms(array('taxonomy' => $taxonomy)) : 0;
            echo '<p><strong>' . ucwords(str_replace('_', ' ', $taxonomy)) . ':</strong> ' . 
                 ($exists ? "✓ {$term_count} terms" : '✗ Not found') . '</p>';
        }
        
        // Check for conflicting pages
        $recipes_page = get_page_by_path('recipes');
        if ($recipes_page) {
            echo '<p><strong>⚠ Warning:</strong> Static "Recipes" page found (ID: ' . $recipes_page->ID . ') - may conflict with recipe archive</p>';
        } else {
            echo '<p><strong>Static Recipes Page:</strong> ✓ No conflicts</p>';
        }
    }
    
    /**
     * Handle fix request
     */
    public function handle_fix_request() {
        if (!isset($_POST['fix_recipes']) || !wp_verify_nonce($_POST['fix_recipe_nonce'], 'fix_recipe_system')) {
            return;
        }
        
        if (!current_user_can('manage_options')) {
            wp_die('Access denied');
        }
        
        $this->fix_recipe_system();
        
        // Redirect with success message
        wp_redirect(add_query_arg('fixed', 'true', admin_url('tools.php?page=fix-recipe-system')));
        exit;
    }
    
    /**
     * Fix the recipe system
     */
    public function fix_recipe_system() {
        // Step 1: Flush rewrite rules
        flush_rewrite_rules();
        
        // Step 2: Create taxonomy terms
        $this->create_taxonomy_terms();
        
        // Step 3: Create sample recipes if needed
        $this->create_sample_recipes();
        
        // Step 4: Final flush
        flush_rewrite_rules();
    }
    
    /**
     * Create taxonomy terms
     */
    public function create_taxonomy_terms() {
        $taxonomies_terms = array(
            'recipe_fish_type' => array('salmon', 'crab', 'rockfish', 'cod', 'halibut', 'scallops'),
            'recipe_meal_type' => array('breakfast', 'lunch', 'dinner', 'appetizer', 'snack'),
            'recipe_technique' => array('pan-searing', 'steaming', 'grilling', 'baking', 'glazing'),
            'recipe_difficulty' => array('easy', 'medium', 'hard')
        );
        
        foreach ($taxonomies_terms as $taxonomy => $terms) {
            if (taxonomy_exists($taxonomy)) {
                foreach ($terms as $term) {
                    if (!term_exists($term, $taxonomy)) {
                        wp_insert_term($term, $taxonomy);
                    }
                }
            }
        }
    }
    
    /**
     * Create sample recipes
     */
    public function create_sample_recipes() {
        // Check if recipes already exist
        $existing_recipes = get_posts(array(
            'post_type' => 'recipe',
            'posts_per_page' => 1,
            'post_status' => 'publish'
        ));
        
        if (!empty($existing_recipes)) {
            return; // Recipes already exist
        }
        
        $sample_recipes = array(
            array(
                'title' => 'Pan-Seared Wild Alaskan Salmon with Lemon Butter',
                'content' => 'A simple yet elegant dish featuring our wild-caught Alaskan salmon, served with a rich lemon butter sauce. This recipe highlights the natural flavors of sustainably caught salmon.',
                'ingredients' => '<ul>
                    <li>1.5 lbs wild Alaskan salmon fillets</li>
                    <li>3 tablespoons olive oil</li>
                    <li>4 tablespoons butter</li>
                    <li>2 lemons (juiced)</li>
                    <li>3 cloves garlic (minced)</li>
                    <li>Salt and pepper to taste</li>
                    <li>Fresh dill for garnish</li>
                </ul>',
                'instructions' => '<ol>
                    <li>Pat salmon fillets dry and season with salt and pepper.</li>
                    <li>Heat olive oil in a large skillet over medium-high heat.</li>
                    <li>Cook salmon skin-side down for 4-5 minutes until crispy.</li>
                    <li>Flip and cook for another 3-4 minutes.</li>
                    <li>Remove salmon and add butter, garlic, and lemon juice to pan.</li>
                    <li>Cook for 1 minute until fragrant.</li>
                    <li>Return salmon to pan and spoon sauce over fillets.</li>
                    <li>Garnish with fresh dill and serve immediately.</li>
                </ol>',
                'meta' => array(
                    '_pcs_prep_time' => '15',
                    '_pcs_cook_time' => '15',
                    '_pcs_total_time' => '30',
                    '_pcs_servings' => '4',
                    '_pcs_calories' => '320',
                    '_pcs_course' => 'Main Course',
                    '_pcs_cuisine' => 'American'
                ),
                'taxonomies' => array(
                    'recipe_fish_type' => 'salmon',
                    'recipe_meal_type' => 'dinner',
                    'recipe_technique' => 'pan-searing',
                    'recipe_difficulty' => 'easy'
                )
            ),
            array(
                'title' => 'Garlic Butter Wild Alaskan Crab Legs',
                'content' => 'Enjoy the sweet flavors of Alaskan crab legs enhanced with a decadent garlic butter sauce. Perfect for special occasions.',
                'ingredients' => '<ul>
                    <li>2 lbs wild Alaskan crab legs</li>
                    <li>6 tablespoons butter</li>
                    <li>4 cloves garlic (minced)</li>
                    <li>1 lemon (juiced)</li>
                    <li>2 tablespoons fresh parsley (chopped)</li>
                    <li>Salt and pepper to taste</li>
                    <li>Lemon wedges for serving</li>
                </ul>',
                'instructions' => '<ol>
                    <li>Steam crab legs for 5-7 minutes until heated through.</li>
                    <li>Meanwhile, melt butter in a small saucepan.</li>
                    <li>Add minced garlic and cook for 1 minute.</li>
                    <li>Stir in lemon juice and parsley.</li>
                    <li>Season with salt and pepper.</li>
                    <li>Serve crab legs with garlic butter sauce.</li>
                    <li>Provide crackers and picks for easy eating.</li>
                </ol>',
                'meta' => array(
                    '_pcs_prep_time' => '10',
                    '_pcs_cook_time' => '10',
                    '_pcs_total_time' => '20',
                    '_pcs_servings' => '4',
                    '_pcs_calories' => '220',
                    '_pcs_course' => 'Main Course',
                    '_pcs_cuisine' => 'American'
                ),
                'taxonomies' => array(
                    'recipe_fish_type' => 'crab',
                    'recipe_meal_type' => 'dinner',
                    'recipe_technique' => 'steaming',
                    'recipe_difficulty' => 'easy'
                )
            ),
            array(
                'title' => 'Pan-Seared Black Rockfish with Lemon Caper Sauce',
                'content' => 'A delicious preparation of wild Alaskan black rockfish featuring a bright and flavorful lemon caper sauce.',
                'ingredients' => '<ul>
                    <li>2 lbs black rockfish fillets</li>
                    <li>3 tablespoons olive oil</li>
                    <li>4 tablespoons butter</li>
                    <li>2 lemons (juiced)</li>
                    <li>3 tablespoons capers</li>
                    <li>2 shallots (minced)</li>
                    <li>Fresh parsley</li>
                    <li>Salt and pepper</li>
                </ul>',
                'instructions' => '<ol>
                    <li>Season rockfish with salt and pepper.</li>
                    <li>Heat oil in large skillet over medium-high heat.</li>
                    <li>Cook fish 4-5 minutes per side until golden.</li>
                    <li>Remove fish and keep warm.</li>
                    <li>Add shallots to pan and cook 1 minute.</li>
                    <li>Add lemon juice and capers, simmer 2 minutes.</li>
                    <li>Whisk in butter and parsley.</li>
                    <li>Serve fish with caper sauce.</li>
                </ol>',
                'meta' => array(
                    '_pcs_prep_time' => '15',
                    '_pcs_cook_time' => '15',
                    '_pcs_total_time' => '30',
                    '_pcs_servings' => '4',
                    '_pcs_calories' => '290',
                    '_pcs_course' => 'Main Course',
                    '_pcs_cuisine' => 'Mediterranean'
                ),
                'taxonomies' => array(
                    'recipe_fish_type' => 'rockfish',
                    'recipe_meal_type' => 'dinner',
                    'recipe_technique' => 'pan-searing',
                    'recipe_difficulty' => 'medium'
                )
            )
        );
        
        foreach ($sample_recipes as $recipe_data) {
            // Create the post
            $post_id = wp_insert_post(array(
                'post_title' => $recipe_data['title'],
                'post_content' => $recipe_data['content'],
                'post_status' => 'publish',
                'post_type' => 'recipe',
                'post_author' => get_current_user_id()
            ));
            
            if ($post_id && !is_wp_error($post_id)) {
                // Add meta fields
                foreach ($recipe_data['meta'] as $key => $value) {
                    update_post_meta($post_id, $key, $value);
                }
                
                // Add ingredients and instructions
                update_post_meta($post_id, '_pcs_ingredients', $recipe_data['ingredients']);
                update_post_meta($post_id, '_pcs_instructions', $recipe_data['instructions']);
                
                // Set taxonomies
                foreach ($recipe_data['taxonomies'] as $taxonomy => $term) {
                    wp_set_object_terms($post_id, $term, $taxonomy);
                }
            }
        }
    }
}

// Initialize the plugin
new RecipeSystemFixer();
