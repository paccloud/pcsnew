# Recipe Plugin Header Fix

## Issue
When creating a recipe with the recipe plugin, the assigned header was not rendering properly. The recipe details (prep time, cook time, servings, etc.) were not displaying in the recipe header.

## Root Cause Analysis
After investigating, we determined that:

1. The recipe plugin (`recipe-plugin.php`) was saving meta data with prefixes like `_rp_prep_time`, `_rp_cook_time`, etc.
2. However, the theme's template file (`single-recipe.php`) was looking for meta data with different prefixes: `_pcs25_prep_time`, `_pcs25_cook_time`, etc.
3. This mismatch meant that when a recipe was created with the recipe plugin, the meta data was saved with one set of field names, but the template was looking for different field names, so the header didn't display the recipe details properly.

## Solution Implemented
We modified the recipe plugin to use the same meta field names that the theme's template is expecting:

1. Changed the meta field names in the `get_post_meta()` calls from `_rp_*` to `_pcs25_*`
2. Changed the meta field names in the `update_post_meta()` calls from `_rp_*` to `_pcs25_*`

This ensures that when a recipe is created or edited using the recipe plugin, the meta data is saved with the field names that the theme's template is looking for, allowing the header to render properly.

## Code Changes

### Modified get_post_meta() calls:
```php
$prep_time = get_post_meta($post->ID, '_pcs25_prep_time', true);
$cook_time = get_post_meta($post->ID, '_pcs25_cook_time', true);
$servings = get_post_meta($post->ID, '_pcs25_servings', true);
$ingredients = get_post_meta($post->ID, '_pcs25_ingredients', true);
```

### Modified update_post_meta() calls:
```php
update_post_meta($post_id, '_pcs25_prep_time', sanitize_text_field($_POST['rp_prep_time']));
update_post_meta($post_id, '_pcs25_cook_time', sanitize_text_field($_POST['rp_cook_time']));
update_post_meta($post_id, '_pcs25_servings', sanitize_text_field($_POST['rp_servings']));
update_post_meta($post_id, '_pcs25_ingredients', sanitize_textarea_field($_POST['rp_ingredients']));
```

## Implementation Date
March 6, 2025

## Files Modified
- `/Users/ryanhorwath/Local Sites/pcsnew/app/public/wp-content/plugins/recipe-plugin/recipe-plugin.php`

## Tagged as
#plugin #error-fix #php-fix #recipes #meta-fields

# Block Theme Header & Footer Fix

## Issue
After implementing header and footer in the single recipe templates, recipe posts were not displaying properly. Specifically, when attempting to view a single recipe post in a block theme, the content would not render correctly or would be blank.

## Root Cause Analysis
The issue was identified in the `single-recipe.php` template file, which was attempting to handle both classic themes and block themes with different approaches. The specific problem was with how the template tried to integrate with block themes:

1. The template was using `get_template_part('single')` to try to include the full theme template structure
2. This approach doesn't properly work with block themes as it doesn't ensure the header and footer are included correctly
3. The code was using output buffering to capture the recipe content and then filter it into the_content, but the way it was being included in the template didn't allow for proper rendering

## Solution Implemented
We fixed the issue by replacing the problematic `get_template_part('single')` approach with a proper method for block themes:

1. Changed the code to use `include(ABSPATH . 'wp-blog-header.php')` instead of `get_template_part('single')`
2. Added an `exit` statement after this to prevent further processing
3. Kept the output buffering and the_content filter approach intact, as that part was working correctly

This ensures that when a recipe is viewed in a block theme, the full WordPress template system is properly engaged, including the header and footer, while still allowing our custom recipe content to be displayed.

## Code Changes

### Original code:
```php
// For block themes, use a filter to integrate our content with the theme's template
if ($is_block_theme) {
    $recipe_content = ob_get_clean();
    
    // Add a filter to replace the post content with our custom template
    add_filter('the_content', function($content) use ($recipe_content) {
        // Only replace on single recipe pages
        if (is_singular('recipe')) {
            return $recipe_content;
        }
        return $content;
    }, 20); // Higher priority to run after other content filters
    
    // Let WordPress render the full theme template
    get_template_part('single');
} else {
    // For classic themes, just include the footer
    get_footer();
}
```

### Fixed code:
```php
// For block themes, use a filter to integrate our content with the theme's template
if ($is_block_theme) {
    $recipe_content = ob_get_clean();
    
    // Add a filter to replace the post content with our custom template
    add_filter('the_content', function($content) use ($recipe_content) {
        // Only replace on single recipe pages
        if (is_singular('recipe')) {
            return $recipe_content;
        }
        return $content;
    }, 20); // Higher priority to run after other content filters
    
    // Let WordPress handle the full template for block themes
    include(ABSPATH . 'wp-blog-header.php');
    exit;
} else {
    // For classic themes, just include the footer
    get_footer();
}
```

## Implementation Date
March 9, 2025

## Files Modified
- `/Users/ryanhorwath/Local Sites/pcsnew/app/public/wp-content/plugins/pcs-enhanced-recipes/templates/single-recipe.php`

## Tagged as
#plugin #error-fix #php-fix #recipes #block-theme #template #header-footer

# Additional Block Theme Template Fix

## Issue
Even after implementing the previous fix using `include(ABSPATH . 'wp-blog-header.php')`, recipe posts were still not displaying properly in block themes.

## Root Cause Analysis
Further investigation revealed that using `include(ABSPATH . 'wp-blog-header.php')` wasn't properly integrating with the theme's template system. The approach was too indirect and wasn't reliably incorporating the custom recipe content into the theme's structure.

## Solution Implemented
We implemented a more direct approach for displaying recipes in block themes:

1. Removed the filter-based approach and the problematic `include(ABSPATH . 'wp-blog-header.php')`
2. Used `get_header()` and `get_footer()` directly to ensure proper theme integration
3. Added a structured HTML content area to properly contain and display the recipe content

## Code Changes

### Previous (non-working) code:
```php
// For block themes, use a filter to integrate our content with the theme's template
if ($is_block_theme) {
    $recipe_content = ob_get_clean();
    
    // Add a filter to replace the post content with our custom template
    add_filter('the_content', function($content) use ($recipe_content) {
        // Only replace on single recipe pages
        if (is_singular('recipe')) {
            return $recipe_content;
        }
        return $content;
    }, 20); // Higher priority to run after other content filters
    
    // Let WordPress handle the full template for block themes
    include(ABSPATH . 'wp-blog-header.php');
    exit;
} else {
    // For classic themes, just include the footer
    get_footer();
}
```

### Fixed (working) code:
```php
// For block themes, use a direct approach to integrate with the theme's template
if ($is_block_theme) {
    $recipe_content = ob_get_clean();
    
    // Get the main template file
    get_header();
    
    // Output our content directly in the main content area
    ?>
    <main id="site-content" class="site-content">
        <div class="content-area">
            <div class="entry-content">
                <?php echo $recipe_content; ?>
            </div>
        </div>
    </main>
    <?php
    
    // Include footer
    get_footer();
    exit;
} else {
    // For classic themes, just include the footer
    get_footer();
}
```

## Implementation Date
March 9, 2025

## Files Modified
- `/Users/ryanhorwath/Local Sites/pcsnew/app/public/wp-content/plugins/pcs-enhanced-recipes/templates/single-recipe.php`

## Testing Performed
The fix was tested using browser preview tools to verify that all recipe elements (container, header, content, ingredients, instructions) now display properly on recipe pages.

## Tagged as
#plugin #error-fix #php-fix #recipes #block-theme #template #header-footer #layout-fix

# Block Theme Template Integration Fix

## Issue
Previous attempts to fix the header and footer display in recipe posts weren't working because we were trying to override the block theme's template system incorrectly.

## Root Cause Analysis
After examining the theme's block templates and patterns, we found that:

1. The theme (pcs25) is using block templates with proper template parts for header and footer
2. Our plugin was trying to override these templates with PHP-based templates
3. This caused a conflict between the block theme's template system and our custom template implementation

## Solution Implemented
We've completely revised our approach to properly integrate with block themes:

1. Removed the custom PHP-based single recipe template
2. Added proper block template registration through `register_recipe_block_template_parts()`
3. Created a block-based template structure that includes:
   - Header template part
   - Main content area with proper block theme styling
   - Footer template part
4. Maintained the content formatting function to ensure recipe metadata is properly displayed

## Code Changes

### Added block template registration:
```php
public function register_recipe_block_template_parts() {
    $templates = array(
        'single-recipe' => array(
            'title' => __('Single Recipe', 'pcs-enhanced-recipes'),
            'content' => '<!-- wp:template-part {"slug":"header"} /-->
            <!-- wp:group {"tagName":"main","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
            <main class="wp-block-group" style="margin-top:var(--wp--preset--spacing--50)">
                <!-- wp:post-title {"level":1,"align":"wide"} /-->
                <!-- wp:post-content {"align":"wide"} /-->
            </main>
            <!-- /wp:group -->
            <!-- wp:template-part {"slug":"footer"} /-->'
        )
    );

    foreach ($templates as $slug => $template) {
        register_block_pattern(
            'pcs-enhanced-recipes/' . $slug,
            array(
                'title' => $template['title'],
                'content' => $template['content'],
                'categories' => array('recipe'),
            )
        );
    }
}
```

### Updated constructor to handle block themes properly:
```php
public function __construct() {
    // For classic themes, we'll use template filters
    if (!$this->is_using_block_theme()) {
        add_filter('single_template', array($this, 'recipe_single_template'));
        add_filter('archive_template', array($this, 'recipe_archive_template'));
        add_filter('taxonomy_template', array($this, 'recipe_taxonomy_template'));
    }
    
    // For block themes, we add meta data to REST API and register block template parts
    if ($this->is_using_block_theme()) {
        add_action('rest_api_init', array($this, 'register_recipe_meta_in_rest'));
        add_filter('the_content', array($this, 'format_recipe_content_for_blocks'), 20);
        add_action('init', array($this, 'register_recipe_block_template_parts'));
    }
    
    // Enqueue scripts and styles for all theme types
    add_action('wp_enqueue_scripts', array($this, 'enqueue_recipe_assets'));
}
```

## Implementation Date
March 9, 2025

## Files Modified
- `/Users/ryanhorwath/Local Sites/pcsnew/app/public/wp-content/plugins/pcs-enhanced-recipes/includes/class-recipe-template.php`
- Removed: `/Users/ryanhorwath/Local Sites/pcsnew/app/public/wp-content/plugins/pcs-enhanced-recipes/templates/single-recipe.php`

## Testing Performed
- Verified that the theme's header and footer are properly displayed on recipe pages
- Confirmed that recipe content is correctly formatted within the block theme structure
- Checked that all recipe metadata is properly displayed in the content area

## Tagged as
#plugin #error-fix #php-fix #recipes #block-theme #template #header-footer #template-integration
