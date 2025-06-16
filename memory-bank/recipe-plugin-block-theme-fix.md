# Recipe Plugin Block Theme Fix

## Issue
The enhanced recipe plugin (`pcs-enhanced-recipes`) was causing conflicting header layout issues in the WordPress block theme site. The plugin was overriding the default block theme templates with its own PHP-based templates, causing the header to display incorrectly or not at all on recipe pages.

## Root Cause Analysis
After investigating, we determined that:

1. The `pcs-enhanced-recipes` plugin was using classic theme template functions (`get_header()`, `get_footer()`) in its templates.
2. The `class-recipe-template.php` was registering filters that override the WordPress template hierarchy, forcing it to use the plugin's templates instead of the theme's block-based templates.
3. This approach conflicts with how block themes work, as they use `.html` template files and the block editor system instead of traditional PHP templates.
4. The CSS from the plugin wasn't specifically scoped to avoid conflicts with the theme's header styles.

## Solution Implemented
We modified the plugin to be compatible with block themes while maintaining compatibility with classic themes:

1. Updated `class-recipe-template.php` to:
   - Detect whether a block theme is being used
   - Skip template overrides for block themes
   - Add recipe metadata to the REST API for block editor integration
   - Only use classic template overrides for non-block themes

2. Modified `class-recipe-assets.php` to:
   - Detect block themes and use more targeted CSS
   - Add inline CSS that prevents plugin styles from affecting headers and layout
   - Scope CSS selectors to recipe content only
   - Apply different styles based on theme type (block vs. classic)

3. Created backups of the original files before modification to ensure we can revert if needed.

## Code Changes

### Modified class-recipe-template.php:
- Added `is_using_block_theme()` method to detect block themes
- Added conditional logic to only use template overrides for classic themes
- Added REST API integration for block editor compatibility
- Preserved backward compatibility for classic themes

### Modified class-recipe-assets.php:
- Added block theme detection
- Created targeted CSS for block themes
- Added inline CSS to fix header conflicts
- Improved CSS scoping to prevent layout issues

## Implementation Date
March 7, 2025

## Files Modified
- `/Users/ryanhorwath/Local Sites/pcsnew/app/public/wp-content/plugins/pcs-enhanced-recipes/includes/class-recipe-template.php`
- `/Users/ryanhorwath/Local Sites/pcsnew/app/public/wp-content/plugins/pcs-enhanced-recipes/includes/class-recipe-assets.php`

## Backups Created
- `/Users/ryanhorwath/Local Sites/pcsnew/app/public/wp-content/plugins/pcs-enhanced-recipes/includes/class-recipe-template.php.bak`
- `/Users/ryanhorwath/Local Sites/pcsnew/app/public/wp-content/plugins/pcs-enhanced-recipes/includes/class-recipe-assets.php.bak`

## Expected Results
- Recipe pages should now display correctly with the proper site header and footer
- Block theme templates should be used instead of the plugin's custom templates
- CSS conflicts should be resolved, with plugin styles scoped to recipe content only
- The plugin should continue to work as expected for recipe content
- Recipe metadata should be available in the block editor

## Long-Term Recommendations
1. **Full Block Integration**: Consider rewriting the plugin to fully embrace the block editor approach, using block patterns and block templates instead of PHP templates
2. **Custom Block Creation**: Create custom blocks for recipe components (ingredients, instructions, nutritional info) for a better block editor experience
3. **CSS Namespacing**: Further improve CSS namespacing to avoid any potential conflicts with theme styles
4. **Schema Markup**: Ensure proper recipe schema markup is maintained for SEO benefits
5. **User Documentation**: Update documentation to explain how the plugin works with block themes

## Tagged as
#plugin #error-fix #layout #header-fix #block-theme #css-fix #recipes
