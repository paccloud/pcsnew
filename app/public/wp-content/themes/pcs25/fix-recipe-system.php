<?php
/**
 * Fix Recipe System Script
 * 
 * This script fixes the recipe system issues:
 * 1. Updates meta field keys to match plugin
 * 2. Creates sample recipe posts
 * 3. Sets up proper permalinks
 * 
 * Run this once to fix the recipe system
 */

// Load WordPress
require_once('../../../../wp-load.php');

// Only run if user is logged in and has admin privileges
if (!is_user_logged_in() || !current_user_can('manage_options')) {
    wp_die('Access denied. Please login as an administrator.');
}

echo "<h1>Fixing Recipe System...</h1>";

// Step 1: Fix permalink structure if needed
flush_rewrite_rules();
echo "<p>✓ Flushed rewrite rules</p>";

// Step 2: Create sample recipe posts if they don't exist
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
        'prep_time' => '15',
        'cook_time' => '15',
        'total_time' => '30',
        'servings' => '4',
        'calories' => '320',
        'course' => 'Main Course',
        'cuisine' => 'American',
        'fish_type' => 'salmon',
        'meal_type' => 'dinner',
        'technique' => 'pan-searing',
        'difficulty' => 'easy'
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
        'prep_time' => '10',
        'cook_time' => '10',
        'total_time' => '20',
        'servings' => '4',
        'calories' => '220',
        'course' => 'Main Course',
        'cuisine' => 'American',
        'fish_type' => 'crab',
        'meal_type' => 'dinner',
        'technique' => 'steaming',
        'difficulty' => 'easy'
    ),
    array(
        'title' => 'Honey-Glazed Wild Sockeye Salmon',
        'content' => 'This elegant yet simple dish highlights the rich flavor of wild Alaskan sockeye salmon with a sweet and savory glaze.',
        'ingredients' => '<ul>
            <li>2 lbs wild sockeye salmon fillets</li>
            <li>1/4 cup honey</li>
            <li>3 tablespoons soy sauce</li>
            <li>2 tablespoons rice vinegar</li>
            <li>2 cloves garlic (minced)</li>
            <li>1 teaspoon fresh ginger (grated)</li>
            <li>2 tablespoons olive oil</li>
            <li>Green onions for garnish</li>
        </ul>',
        'instructions' => '<ol>
            <li>Whisk together honey, soy sauce, vinegar, garlic, and ginger.</li>
            <li>Marinate salmon in half the glaze for 30 minutes.</li>
            <li>Heat oil in a large oven-safe skillet.</li>
            <li>Sear salmon for 3 minutes per side.</li>
            <li>Brush with remaining glaze.</li>
            <li>Transfer to 400°F oven for 8-10 minutes.</li>
            <li>Garnish with green onions and serve.</li>
        </ol>',
        'prep_time' => '40',
        'cook_time' => '20',
        'total_time' => '60',
        'servings' => '6',
        'calories' => '280',
        'course' => 'Main Course',
        'cuisine' => 'Asian',
        'fish_type' => 'salmon',
        'meal_type' => 'dinner',
        'technique' => 'glazing',
        'difficulty' => 'medium'
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
        'prep_time' => '15',
        'cook_time' => '15',
        'total_time' => '30',
        'servings' => '4',
        'calories' => '290',
        'course' => 'Main Course',
        'cuisine' => 'Mediterranean',
        'fish_type' => 'rockfish',
        'meal_type' => 'dinner',
        'technique' => 'pan-searing',
        'difficulty' => 'medium'
    )
);

// Check if recipes already exist
$existing_recipes = get_posts(array(
    'post_type' => 'recipe',
    'posts_per_page' => 1,
    'post_status' => 'publish'
));

if (empty($existing_recipes)) {
    echo "<h2>Creating Sample Recipes...</h2>";
    
    foreach ($sample_recipes as $recipe_data) {
        // Create the post
        $post_id = wp_insert_post(array(
            'post_title' => $recipe_data['title'],
            'post_content' => $recipe_data['content'],
            'post_status' => 'publish',
            'post_type' => 'recipe',
            'post_author' => 1
        ));
        
        if ($post_id && !is_wp_error($post_id)) {
            // Add meta fields
            update_post_meta($post_id, '_pcs_prep_time', $recipe_data['prep_time']);
            update_post_meta($post_id, '_pcs_cook_time', $recipe_data['cook_time']);
            update_post_meta($post_id, '_pcs_total_time', $recipe_data['total_time']);
            update_post_meta($post_id, '_pcs_servings', $recipe_data['servings']);
            update_post_meta($post_id, '_pcs_calories', $recipe_data['calories']);
            update_post_meta($post_id, '_pcs_course', $recipe_data['course']);
            update_post_meta($post_id, '_pcs_cuisine', $recipe_data['cuisine']);
            update_post_meta($post_id, '_pcs_ingredients', $recipe_data['ingredients']);
            update_post_meta($post_id, '_pcs_instructions', $recipe_data['instructions']);
            
            // Set taxonomies
            if (isset($recipe_data['fish_type'])) {
                wp_set_object_terms($post_id, $recipe_data['fish_type'], 'recipe_fish_type');
            }
            if (isset($recipe_data['meal_type'])) {
                wp_set_object_terms($post_id, $recipe_data['meal_type'], 'recipe_meal_type');
            }
            if (isset($recipe_data['technique'])) {
                wp_set_object_terms($post_id, $recipe_data['technique'], 'recipe_technique');
            }
            if (isset($recipe_data['difficulty'])) {
                wp_set_object_terms($post_id, $recipe_data['difficulty'], 'recipe_difficulty');
            }
            
            echo "<p>✓ Created recipe: " . $recipe_data['title'] . "</p>";
        } else {
            echo "<p>✗ Failed to create recipe: " . $recipe_data['title'] . "</p>";
        }
    }
} else {
    echo "<p>✓ Recipes already exist, skipping creation</p>";
}

// Step 3: Create/update taxonomy terms
$taxonomies_terms = array(
    'recipe_fish_type' => array('salmon', 'crab', 'rockfish', 'cod', 'halibut', 'scallops'),
    'recipe_meal_type' => array('breakfast', 'lunch', 'dinner', 'appetizer', 'snack'),
    'recipe_technique' => array('pan-searing', 'steaming', 'grilling', 'baking', 'glazing'),
    'recipe_difficulty' => array('easy', 'medium', 'hard')
);

foreach ($taxonomies_terms as $taxonomy => $terms) {
    foreach ($terms as $term) {
        if (!term_exists($term, $taxonomy)) {
            wp_insert_term($term, $taxonomy);
            echo "<p>✓ Created term: $term in $taxonomy</p>";
        }
    }
}

// Step 4: Check for static pages conflicting with recipe archive
$recipes_page = get_page_by_path('recipes');
if ($recipes_page) {
    echo "<p>⚠ Warning: Found static 'Recipes' page that may conflict with recipe archive</p>";
    echo "<p>Consider changing the page slug or deleting it to allow recipe archive to work properly</p>";
}

// Step 5: Final rewrite rules flush
flush_rewrite_rules();
echo "<p>✓ Final permalink flush completed</p>";

echo "<h2>Recipe System Fix Complete!</h2>";
echo "<p><a href='" . home_url('/recipe/') . "'>View Recipe Archive</a></p>";
echo "<p><a href='" . admin_url() . "'>Go to Admin</a></p>";
