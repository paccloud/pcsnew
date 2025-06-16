<?php
/**
 * Test Nutrition Data
 * 
 * This script creates a test recipe with nutrition information
 * to demonstrate the nutrition facts feature.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Create a test recipe with nutrition information
 */
function pcs_create_test_recipe() {
    // Check if the recipe already exists
    $existing = get_page_by_title('Test Recipe with Nutrition Facts', OBJECT, 'recipe');
    if ($existing) {
        return 'Test recipe already exists with ID: ' . $existing->ID;
    }

    // Create recipe post
    $recipe_data = array(
        'post_title'    => 'Test Recipe with Nutrition Facts',
        'post_content'  => 'This is a test recipe to demonstrate the nutrition facts feature of PCS Enhanced Recipes plugin.',
        'post_status'   => 'publish',
        'post_type'     => 'recipe',
    );

    // Insert the post
    $recipe_id = wp_insert_post($recipe_data);

    if (!$recipe_id) {
        return 'Failed to create test recipe';
    }

    // Set featured image if needed
    // $attachment_id = YOUR_ATTACHMENT_ID;
    // set_post_thumbnail($recipe_id, $attachment_id);

    // Add recipe meta data
    $recipe_meta = array(
        '_pcs_prep_time'       => '15',
        '_pcs_cook_time'       => '30',
        '_pcs_total_time'      => '45',
        '_pcs_servings'        => '4',
        '_pcs_course'          => 'Main Course',
        '_pcs_cuisine'         => 'Italian',
        '_pcs_ingredients'     => '<ul>
            <li>1 pound spaghetti</li>
            <li>2 tablespoons olive oil</li>
            <li>3 cloves garlic, minced</li>
            <li>1 can (28 oz) crushed tomatoes</li>
            <li>1/4 teaspoon red pepper flakes</li>
            <li>1/2 teaspoon salt</li>
            <li>1/4 teaspoon black pepper</li>
            <li>1/4 cup fresh basil, chopped</li>
            <li>1/2 cup grated Parmesan cheese</li>
        </ul>',
        '_pcs_instructions'    => '<ol>
            <li>Bring a large pot of salted water to a boil. Add spaghetti and cook according to package directions until al dente.</li>
            <li>Meanwhile, heat olive oil in a large skillet over medium heat. Add garlic and cook until fragrant, about 30 seconds.</li>
            <li>Add crushed tomatoes, red pepper flakes, salt, and pepper. Simmer for 10 minutes.</li>
            <li>Drain pasta and add to the sauce. Toss to coat.</li>
            <li>Stir in fresh basil and serve with grated Parmesan cheese on top.</li>
        </ol>',
        
        // Nutrition facts
        '_pcs_calories'        => '380',
        '_pcs_protein'         => '12',
        '_pcs_carbohydrates'   => '58',
        '_pcs_fat'             => '11',
        '_pcs_saturated_fat'   => '3',
        '_pcs_cholesterol'     => '10',
        '_pcs_sodium'          => '520',
        '_pcs_fiber'           => '4',
        '_pcs_sugar'           => '5',
    );

    foreach ($recipe_meta as $key => $value) {
        update_post_meta($recipe_id, $key, $value);
    }

    // Add recipe terms
    wp_set_object_terms($recipe_id, 'Pasta', 'recipe_category');
    wp_set_object_terms($recipe_id, 'Dinner', 'recipe_meal_type');
    wp_set_object_terms($recipe_id, 'Easy', 'recipe_difficulty');

    return 'Test recipe created successfully with ID: ' . $recipe_id;
}

/**
 * Admin notice to display the result
 */
function pcs_nutrition_test_admin_notice() {
    if (isset($_GET['pcs_test_recipe']) && $_GET['pcs_test_recipe'] === 'created') {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo pcs_create_test_recipe(); ?></p>
            <p>View the recipe: <a href="<?php echo esc_url(home_url('/recipe/test-recipe-with-nutrition-facts/')); ?>">Test Recipe with Nutrition Facts</a></p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'pcs_nutrition_test_admin_notice');

/**
 * Add admin menu item for testing
 */
function pcs_add_test_menu() {
    add_submenu_page(
        'edit.php?post_type=recipe',
        'Create Test Recipe',
        'Create Test Recipe',
        'manage_options',
        'create-test-recipe',
        'pcs_create_test_recipe_page'
    );
}
add_action('admin_menu', 'pcs_add_test_menu');

/**
 * Test recipe page
 */
function pcs_create_test_recipe_page() {
    ?>
    <div class="wrap">
        <h1>Create Test Recipe</h1>
        <p>Click the button below to create a test recipe with nutrition facts:</p>
        <a href="<?php echo esc_url(admin_url('edit.php?post_type=recipe&pcs_test_recipe=created')); ?>" class="button button-primary">Create Test Recipe</a>
    </div>
    <?php
}
