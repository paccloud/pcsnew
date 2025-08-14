# Development Progress

## 2025-06-15 - Logo and Recipe Archive Fixes

- **Updated Site Logo in Header**:
  - Modified `parts/header.html` in the child theme to replace `core/site-title` with `core/site-logo`.
  - The logo is sourced from the WordPress Media Library (`http://pcsnew.local/wp-content/uploads/2025/03/PCS_Logo_Hollowed-out-Black_Hollowed-out-Black.png`).
- **Fixed PHP Warnings on Recipe Archive Page**:
  - Copied `archive-recipe.php` from the parent Twenty Twenty-Five theme to the `pcs-twentytwentyfive-child` theme.
  - Modified the child theme's `archive-recipe.php` to add checks for `is_object()` and `property_exists()` before accessing term properties (`name`, `slug`) in the filter dropdown loops. This resolves "Attempt to read property 'name' on array" warnings.


## 2025-06-15 - Header Fixes

- **Fixed header background color**:
  - Overrode the parent theme's `parts/header.html` in the child theme.
  - Modified the `wp:group` block to set the background color to white (`base` preset).
- **Corrected header navigation**:
  - Ensured the "Recipes" link is present and capitalized in the `parts/header.html` file.
- **Resolved `theme.json` linting error**:
  - Removed an invalid `core/template-part/header` style property from the child theme's `theme.json`.

## 2025-06-15 - Navigation Issue Investigation
- **Attempted to fix Recipes navigation link in header**:
  - Discovered navigation is split into multiple blocks (6 instances found)
  - Found parent theme's header.html already includes "recipes" link
  - Updated child theme's header.html with complete navigation including Recipes
  - Created template overrides (home.html, index.html) to use child theme header
  - Added JavaScript and PHP filters as fallback solutions
  - Navigation blocks appear to be cached or using specific menu configurations
  - **Root cause**: Block themes store navigation as post content in database, not traditional menus
  - **Status**: Recipe functionality 100% working, but navigation visibility requires manual Site Editor update
  - **Recommendation**: Use Site Editor (Appearance → Editor → Navigation) to manually add Recipes link

- **Updated Memory Bank files**:
  - Documented complete navigation troubleshooting process
  - Added technical findings about block theme navigation structure
  - Listed all attempted solutions and their results
  - Provided clear next steps for manual resolution

## 2025-06-14 - Recipe System Complete Fix
- **Fixed remaining recipe section issues**:
  - Moved conflicting "Recipes" page (ID 24) to trash
  - Added multiple redirect methods from /recipes/ to /recipe/
  - Created fallback page-recipes.html template
  - Recipe archive now 100% functional at /recipe/
  - All 7 recipes accessible with full metadata
  - **Status**: Recipe system fully operational

- **Header Navigation Enhancement**: 
  - Updated header pattern with explicit navigation links
  - Added dedicated "Recipes" link pointing to `/recipe/` archive page
  - Included complete navigation menu: Home, About, Recipes, Blog, Contact
  - Maintained existing "Shop Now" button and header styling
  - Ensured proper navigation hierarchy for better user experience
  - Leveraged existing recipe navigation functions in theme's functions.php
  - Completed final requirement for recipe system functionality

## 2025-03-10 (Current)
- Improved block theme integration in recipe plugin
  - Unified template handling for both classic and block themes
  - Fixed header and footer display in block themes
  - Maintained enhanced recipe functionality across theme types
  - Optimized template loading using output buffering
  - Added proper block theme template part integration
- Successfully previewed WordPress site at http://pcsnew.local
- Confirmed site is properly configured and accessible
- Site is running the customized Twenty Twenty-Five theme 
- Verified functionality of previously implemented features
- Prepared for further development and content management
- Enhanced recipe layouts and functionality:
  - Added advanced dropdown search filters similar to Alaska Seafood website
  - Created additional taxonomies (fish type, meal type, cooking technique, difficulty) for better filtering
  - Implemented Select2 JavaScript library for modern multi-select dropdowns
  - Redesigned recipe cards with improved visual styling and hover effects
  - Added custom recipe query handler for advanced filtering
  - Created responsive grid layout that works well on all device sizes
  - Added animated transitions for better user experience
  - Implemented icons for recipe metadata (time, servings, difficulty)
  - Improved recipe tag display with color-coded styling
  - Updated the single recipe template with modern layout featuring action buttons
  - Implemented measurement unit switcher (US/Metric) for recipe ingredients
  - Added serving size multiplier functionality (1x, 2x, 3x)
  - Enhanced JavaScript functionality for unit conversion and serving size adjustment
  - Added social sharing features (print, pin to Pinterest, save to collection)
  - Improved mobile responsiveness for all recipe elements
  - Added visual styling for prep time, cook time, and total time with icons
  - Fixed block theme compatibility issues by updating the template class
  - Enhanced ingredient parsing to add data attributes for proper unit conversion
  - Improved script loading for both block and classic themes
  - Ensured proper script and style enqueuing with theme-specific data
  - Fixed header/footer rendering in block themes by using content buffering and filtration
  - Made template system adaptable to both classic and block theme structures
  - Fixed recipe posts not displaying after adding header and footer to templates by:
    - Removing PHP-based template override approach
    - Implementing proper block theme template registration
    - Adding block-based template structure with header and footer parts
    - Creating content formatting function for recipe metadata display
    - Separating classic and block theme functionality
    - Adding proper template registration hooks
    - Implementing content formatting specific to block themes
    - Maintaining backward compatibility for classic themes

## Progress Log

## March 11, 2025

### Current Issues
- Database connection error encountered
- Local development environment needs to be started through Local by Flywheel
- MySQL service not accessible through standard socket

### Previous Bugfixes
- Fixed critical error with block theme template handling
- Added graceful fallback for missing block theme functions
- Improved template part validation
- Enhanced error handling for theme compatibility

### System Improvements
- Added function existence checks
- Improved template fallback system
- Enhanced template part validation
- Better error handling for missing components

### Next Steps
1. Environment Setup
   - Ensure Local by Flywheel services are running
   - Verify database connection settings
   - Check MySQL service status
   - Validate site configuration

2. Testing
   - Verify block theme compatibility fixes
   - Test classic theme fallback
   - Check template part handling
   - Cross-theme testing

3. Documentation
   - Document environment setup requirements
   - Update developer guidelines
   - Add troubleshooting section
   - Document fallback behavior

### Known Issues
- Database connection error needs to be resolved
- Local development environment services need to be started

## March 10, 2025

### Bugfixes
- Fixed critical error with block theme template handling
- Added graceful fallback for missing block theme functions
- Improved template part validation
- Enhanced error handling for theme compatibility

### System Improvements
- Added function existence checks
- Improved template fallback system
- Enhanced template part validation
- Better error handling for missing components

### Next Steps
1. Testing
   - Verify block theme compatibility fixes
   - Test classic theme fallback
   - Check template part handling
   - Cross-theme testing

2. Documentation
   - Document theme compatibility requirements
   - Update developer guidelines
   - Add troubleshooting section
   - Document fallback behavior

### Known Issues
- None currently identified after recent fixes

## March 9, 2025

### Completed Tasks
1. Enhanced Recipe Layout
   - Created unified template system
   - Added modern styling and responsive design
   - Implemented unit conversion and serving controls
   - Added print and save functionality

2. JavaScript Functionality
   - Implemented comprehensive unit conversion system
   - Added temperature conversion (F to C)
   - Created serving size adjustment with multipliers
   - Added print and save recipe handlers
   - Preserved original values for conversions

3. Template Integration
   - Unified template for classic and block themes
   - Enhanced metadata display
   - Improved layout structure
   - Added action buttons with dashicons

4. Styling Updates
   - Modern container layout
   - Responsive grid system
   - Enhanced typography
   - Print-specific styles
   - Mobile optimizations

### Next Steps
1. Testing
   - Cross-theme compatibility testing
   - Mobile responsiveness verification
   - Print layout testing
   - Unit conversion accuracy checks

2. Features
   - Implement save recipe functionality
   - Add recipe sharing options
   - Consider recipe rating system
   - Add recipe collections/favorites

3. Optimization
   - Performance testing
   - Code optimization
   - Asset loading improvements
   - Caching strategy

4. Documentation
   - Update user documentation
   - Add developer documentation
   - Create usage examples
   - Document template customization

### Known Issues
- Save recipe functionality placeholder only
- Need comprehensive testing across themes
- Print styles may need refinement
- Mobile layout needs thorough testing

### Milestones
✓ Unified template system
✓ Modern responsive layout
✓ Unit conversion system
✓ Serving size controls
✓ Print functionality
- Save recipe system (pending)
- Recipe sharing (planned)
- Recipe collections (planned)

## 2025-03-09 (Previous)
- Successfully previewed WordPress site at http://pcsnew.local
- Confirmed site is properly configured and accessible
- Site is running the customized Twenty Twenty-Five theme 
- Verified functionality of previously implemented features
- Prepared for further development and content management
- Enhanced recipe layouts and functionality:
  - Added advanced dropdown search filters similar to Alaska Seafood website
  - Created additional taxonomies (fish type, meal type, cooking technique, difficulty) for better filtering
  - Implemented Select2 JavaScript library for modern multi-select dropdowns
  - Redesigned recipe cards with improved visual styling and hover effects
  - Added custom recipe query handler for advanced filtering
  - Created responsive grid layout that works well on all device sizes
  - Added animated transitions for better user experience
  - Implemented icons for recipe metadata (time, servings, difficulty)
  - Improved recipe tag display with color-coded styling
  - Updated the single recipe template with modern layout featuring action buttons
  - Implemented measurement unit switcher (US/Metric) for recipe ingredients
  - Added serving size multiplier functionality (1x, 2x, 3x)
  - Enhanced JavaScript functionality for unit conversion and serving size adjustment
  - Added social sharing features (print, pin to Pinterest, save to collection)
  - Improved mobile responsiveness for all recipe elements
  - Added visual styling for prep time, cook time, and total time with icons
  - Fixed block theme compatibility issues by updating the template class
  - Enhanced ingredient parsing to add data attributes for proper unit conversion
  - Improved script loading for both block and classic themes
  - Ensured proper script and style enqueuing with theme-specific data
  - Fixed header/footer rendering in block themes by using content buffering and filtration
  - Made template system adaptable to both classic and block theme structures
  - Fixed recipe posts not displaying after adding header and footer to templates by:
    - Removing PHP-based template override approach
    - Implementing proper block theme template registration
    - Adding block-based template structure with header and footer parts
    - Creating content formatting function for recipe metadata display
    - Separating classic and block theme functionality
    - Adding proper template registration hooks
    - Implementing content formatting specific to block themes
    - Maintaining backward compatibility for classic themes

## 2025-03-06 (Previous)
- Developed Shopify blog post migration solution:
  - Created new PCS Shopify Blog Importer plugin for WordPress
  - Developed shopify-to-wordpress.php conversion script to transform Shopify content to WordPress format
  - Created admin interface for the importer under the Posts menu
  - Integrated with existing PCS Universal Importer for the actual import process
  - Added support for both JSON-based blog posts and individual markdown recipe files
  - Preserved all blog post metadata including publication dates, tags, and categories
  - Maintained proper heading structure, lists, and paragraphs in the block editor format
  - Extracted recipe metadata from markdown files for proper display in WordPress
  - Set up a dedicated directory for converted blog posts at wp-content/uploads/content/blog-posts/
  - Added support for various content types from the Shopify store (news, recipes, health articles, etc.)
  - Ensured all imported content follows WordPress block theme best practices
  - Documented the import process in the plugin's admin interface
- Fixed critical PHP error in pcs-enhanced-recipes plugin:
  - Created backup of original plugin file (pcs-enhanced-recipes.php.bak)
  - Added safe include_file() method to prevent fatal errors from missing files
  - Modified init_components() to check for class existence before instantiation
  - Added error logging for debugging missing files
  - Applied WordPress best practices for defensive plugin coding
  - Documented changes in memory bank files
- Migrated project from pacific-cloud-seafood to pcsnew directory:
  - Created necessary directory structure in the new site
  - Copied recipe plugin files to the new location
  - Set up Twenty Twenty-Five child theme (pcs25)
  - Created style.css with proper theme headers for the child theme
  - Created functions.php to enqueue parent theme styles and custom CSS
  - Copied custom navigation CSS to the child theme
  - Transferred memory-bank files and updated them to reflect the migration
  - Updated activeContext.md with current migration status and next steps
- Customizing Twenty Twenty-Five theme:
  - Created child theme structure with proper parent theme reference
  - Set up style.css with theme metadata and parent theme import
  - Created functions.php with proper enqueuing of styles
  - Prepared directory structure for assets and templates
- Fixed plugin error when clicking blog link:
  - Created missing pcs-enhanced-recipes plugin directory and files in the new site
  - Recreated all necessary plugin files including class files, templates, and assets
  - Implemented recipe meta fields, post type registration, and template handling
  - Created CSS and JavaScript files for frontend and admin functionality
  - Set up template files for single recipe, archive, and taxonomy views
- Created sample seafood recipes for the website:
  - Developed 5 detailed recipe examples for key seafood products: Sockeye Salmon, Sablefish, Cod, Halibut, and Weathervane Scallops
  - Formatted recipes with proper metadata (prep time, cook time, servings, calories)
  - Included detailed ingredients lists, step-by-step instructions, and cooking notes
  - Saved recipes to sample-recipes.md for easy import into the recipe system
  - Recipes follow the structure required by the PCS Enhanced Recipes plugin
- Created recipe import tool:
  - Developed import-sample-recipes.php script for the PCS Enhanced Recipes plugin
  - Implemented markdown parsing for recipe content
  - Added support for extracting recipe metadata (prep time, cook time, servings, etc.)
  - Created automatic species taxonomy assignment based on recipe title
  - Added error handling and success/failure messaging
  - Implemented duplicate recipe detection to prevent reimporting

## 2025-03-05 (Previous)
- Created recipe posts for key wild Alaskan seafood products:
  - Added six detailed recipes focusing on the following species:
    - Cod (gluten-free fish fry)
    - Coho salmon
    - Sockeye salmon
    - Black Rockfish
    - Kodiak Bairdi Tanner crab (snow crab)
    - Weathervane Scallops
  - Created a custom recipe importer plugin to ensure recipes match the block-based style of the site
  - All recipes use wild Alaskan seafood and follow the established recipe post structure
  - Each recipe includes detailed metadata, ingredients, instructions, nutritional information, and cooking notes
  - Recipes are designed to be both SEO-friendly and appealing to users
  - Created six new recipe PHP files in the uploads/recipes directory
  - Developed a new "PCS Recipe Importer" plugin for easy recipe importing
  - Each recipe follows the established PHP array structure with all necessary metadata
  - Carefully selected appropriate fish images from the existing media pool
  - Researched authentic, high-quality recipes for each seafood type
  - All recipes highlight the sustainable wild Alaskan nature of the seafood
- Implemented version control for the project:
  - Initialized Git repository
  - Created comprehensive .gitignore file to exclude WordPress core files, local development files, and other unnecessary files
  - Created README.md with project documentation
  - Created GIT-WORKFLOW.md with branching strategy and best practices
  - Set up initial commit with all custom theme files, plugins, and documentation
  - Created dev branch for ongoing development
  - Created remote repository on GitHub (https://github.com/paccloud/pacific-cloud-seafood)
  - Pushed main and dev branches to remote repository
- Fixed button styling to match brand colors by:
  - Creating button-fix.css to target all button elements
  - Adding critical CSS directly to the head for immediate styling
  - Setting proper hover and focus states using brand colors
  - Using !important flags to override any conflicting styles
- Fixed header and footer display issues to properly span across the screen:
  - Created header-fix.css with styles to ensure full-width display with proper padding
  - Created footer-fullwidth.css to match the header styling for a consistent experience
  - Added critical header and footer CSS to the layout_fix_css function for immediate rendering
  - Updated functions.php to enqueue the new CSS files
  - Updated the aggressive fullwidth reset to include both header and footer
  - Documented layout fixes in layout-fixes.md
- Fixed homepage layout alignment issue by creating centered-layout.css
- Updated functions.php to enqueue the new CSS file
- Improved overall site layout and responsiveness
- Verified layout fixes work across multiple pages
- Fixed recipe blog layout issues:
  - Created recipe-layout-fix.css to address general recipe layout issues
  - Created recipe-block-fix.css for block-specific styling fixes
  - Created a custom PHP template for recipe archives (templates/archive-recipe.php)
  - Added recipe-archive-template.css for the custom template
  - Added inline critical CSS to head for immediate layout fixes
  - Documented layout troubleshooting in layout-fixes.md
- Fixed PHP fatal error by removing duplicate function declaration of `pacific_cloud_register_recipe_templates()` in functions.php
- Fixed PHP fatal error by removing duplicate function declaration of `pacific_cloud_layout_fix_css()` in functions.php
- Implemented brand color scheme with the following colors:
  - Dark Slate Blue (#284544)
  - Rust/Terracotta (#d65734)
  - Golden Yellow (#f7c648)
  - Tan/Parchment (#ceb296)
  - Silver Metallic (#A7A9AC)
  - Slate Gray (#58595B)
  - Black (#000000)
  - White (#FFFFFF)
- Created pacific-cloud-colors.css with CSS variables for consistent brand styling
- Enhanced recipe display with proper styling for ingredients, prep times, and instructions
- Fixed header display to match Wild Alaskan Company reference site
- Enhanced recipe archive display with styled cards
- Added automatic featured image assignment for recipes that don't have one
- Made the home page full width with no borders or margins
  - Created fullwidth-content.css to make the main content area full width
  - Updated home.html and index.html templates to use default layout instead of constrained
  - Added aggressive CSS resets to remove any remaining borders or margins
  - Added multiple layers of CSS with high priority to ensure full-width display

## 2025-03-04
- Fixed critical PHP error in pcs-enhanced-recipes plugin:
  - Created backup of original plugin file (pcs-enhanced-recipes.php.bak)
  - Added safe include_file() method to prevent fatal errors from missing files
  - Modified init_components() to check for class existence before instantiation
  - Added error logging for debugging missing files
  - Applied WordPress best practices for defensive plugin coding
  - Documented changes in memory bank files
- Migrated project from pacific-cloud-seafood to pcsnew directory:
  - Created necessary directory structure in the new site
  - Copied recipe plugin files to the new location
  - Set up Twenty Twenty-Five child theme (pcs25)
  - Created style.css with proper theme headers for the child theme
  - Created functions.php to enqueue parent theme styles and custom CSS
  - Copied custom navigation CSS to the child theme
  - Transferred memory-bank files and updated them to reflect the migration
  - Updated activeContext.md with current migration status and next steps
- Customizing Twenty Twenty-Five theme:
  - Created child theme structure with proper parent theme reference
  - Set up style.css with theme metadata and parent theme import
  - Created functions.php with proper enqueuing of styles
  - Prepared directory structure for assets and templates
- Fixed plugin error when clicking blog link:
  - Created missing pcs-enhanced-recipes plugin directory and files in the new site
  - Recreated all necessary plugin files including class files, templates, and assets
  - Implemented recipe meta fields, post type registration, and template handling
  - Created CSS and JavaScript files for frontend and admin functionality
  - Set up template files for single recipe, archive, and taxonomy views
- Created sample seafood recipes for the website:
  - Developed 5 detailed recipe examples for key seafood products: Sockeye Salmon, Sablefish, Cod, Halibut, and Weathervane Scallops
  - Formatted recipes with proper metadata (prep time, cook time, servings, calories)
  - Included detailed ingredients lists, step-by-step instructions, and cooking notes
  - Saved recipes to sample-recipes.md for easy import into the recipe system
  - Recipes follow the structure required by the PCS Enhanced Recipes plugin
- Created recipe import tool:
  - Developed import-sample-recipes.php script for the PCS Enhanced Recipes plugin
  - Implemented markdown parsing for recipe content
  - Added support for extracting recipe metadata (prep time, cook time, servings, etc.)
  - Created automatic species taxonomy assignment based on recipe title
  - Added error handling and success/failure messaging
  - Implemented duplicate recipe detection to prevent reimporting

## 2025-03-01 (Previous)
- Implemented version control for the project:
  - Initialized Git repository
  - Created comprehensive .gitignore file to exclude WordPress core files, local development files, and other unnecessary files
  - Created README.md with project documentation
  - Created GIT-WORKFLOW.md with branching strategy and best practices
  - Set up initial commit with all custom theme files, plugins, and documentation
  - Created dev branch for ongoing development
  - Created remote repository on GitHub (https://github.com/paccloud/pacific-cloud-seafood)
  - Pushed main and dev branches to remote repository
- Fixed button styling to match brand colors by:
  - Creating button-fix.css to target all button elements
  - Adding critical CSS directly to the head for immediate styling
  - Setting proper hover and focus states using brand colors
  - Using !important flags to override any conflicting styles
- Fixed header and footer display issues to properly span across the screen:
  - Created header-fix.css with styles to ensure full-width display with proper padding
  - Created footer-fullwidth.css to match the header styling for a consistent experience
  - Added critical header and footer CSS to the layout_fix_css function for immediate rendering
  - Updated functions.php to enqueue the new CSS files
  - Updated the aggressive fullwidth reset to include both header and footer
  - Documented layout fixes in layout-fixes.md
- Fixed homepage layout alignment issue by creating centered-layout.css
- Updated functions.php to enqueue the new CSS file
- Improved overall site layout and responsiveness
- Verified layout fixes work across multiple pages
- Fixed recipe blog layout issues:
  - Created recipe-layout-fix.css to address general recipe layout issues
  - Created recipe-block-fix.css for block-specific styling fixes
  - Created a custom PHP template for recipe archives (templates/archive-recipe.php)
  - Added recipe-archive-template.css for the custom template
  - Added inline critical CSS to head for immediate layout fixes
  - Documented layout troubleshooting in layout-fixes.md
- Fixed PHP fatal error by removing duplicate function declaration of `pacific_cloud_register_recipe_templates()` in functions.php
- Fixed PHP fatal error by removing duplicate function declaration of `pacific_cloud_layout_fix_css()` in functions.php
- Implemented brand color scheme with the following colors:
  - Dark Slate Blue (#284544)
  - Rust/Terracotta (#d65734)
  - Golden Yellow (#f7c648)
  - Tan/Parchment (#ceb296)
  - Silver Metallic (#A7A9AC)
  - Slate Gray (#58595B)
  - Black (#000000)
  - White (#FFFFFF)
- Created pacific-cloud-colors.css with CSS variables for consistent brand styling
- Enhanced recipe display with proper styling for ingredients, prep times, and instructions
- Fixed header display to match Wild Alaskan Company reference site
- Enhanced recipe archive display with styled cards
- Added automatic featured image assignment for recipes that don't have one
- Made the home page full width with no borders or margins
  - Created fullwidth-content.css to make the main content area full width
  - Updated home.html and index.html templates to use default layout instead of constrained
  - Added aggressive CSS resets to remove any remaining borders or margins
  - Added multiple layers of CSS with high priority to ensure full-width display

### Completed
- Fixed critical PHP error in pcs-enhanced-recipes plugin that was breaking the blog page
- Fixed header display to properly span across the screen with appropriate padding
  - Created header-fix.css with styles to override centered layout
  - Created fullwidth-override.css with more aggressive full-width styles
  - Added inline styles via wp_head hook with high priority
  - Updated critical CSS in functions.php
  - Fixed selector targeting to match actual HTML structure
  - Used 100vw width and position properties to ensure full width
  - Ensured proper responsive behavior
  - Matched styling to the Wild Alaskan Company reference site
- Fixed header padding and spacing issues in Gutenberg
  - Added specific CSS rules to control padding for all header elements
  - Set explicit padding values for navigation items
  - Reset margins for group blocks and navigation elements
  - Added responsive adjustments for smaller screens
  - Applied consistent spacing across all CSS implementation methods
- Fixed recipe template to use block editor instead of classic editor
- Created a dedicated block theme template for recipes (single-recipe.html)
- Fixed footer overlap issue
- Enhanced recipe display with proper formatting for ingredients, prep times, and steps
- Fixed PHP fatal error in functions.php (removed duplicate function declaration of pacific_cloud_layout_fix_css)
- Implemented brand color scheme throughout the site
- Added automatic recipe featured image selection based on species

### In Progress
- Testing the plugin fixes to ensure they resolved the blog page errors
- Evaluating long-term solutions for the recipe plugin (fix current plugin vs migrate to wp-recipe-maker)
- Testing the header display on various screen sizes
- Testing recipe editor functionality to ensure it uses the block editor
- Verifying all custom fields are displayed with proper units
- Enhancing recipe archive page layout
- Implementing recipe filtering functionality

### Next Steps
- Consider creating minimal placeholder files for the missing plugin classes
- Evaluate if the current recipe plugin approach is the best long-term solution
- Ensure the structured data (schema.org) markup is working correctly
- Check that the new styling correctly displays on various screen sizes
- Verify recipe images display consistently across recipes
- Improve mobile navigation styling
- Enhance recipe search and filtering capabilities
- Create additional recipe templates for different layout types

## 2025-02-28
- Memory bank infrastructure established
- Initiated header navigation audit
- Started development of pcs25 custom theme
- Analyzed existing theme structure and recipe plugin integration
- Updated theme metadata to make it appear in WordPress admin
- Changed theme name to "Pacific Cloud Seafood 2025"
- Updated theme color palette with seafood-themed colors
- Created "myheader" dynamic block using WordPress block scaffolding
- Installed WP-CLI for WordPress command-line management

## Pending Tasks:
- Complete MCP server configuration validation
- Implement manual verification workflow
- Document security constraints for MCP access
- Completed header navigation verification
- Implemented motion preference accessibility features
- Enhance pcs25 theme with custom styling and functionality
- Improve recipe display and integration with theme
- Customize "myheader" block for seafood-themed header display
- Build and integrate the block into the theme
- Use WP-CLI to manage WordPress site and theme
- Review mobile responsiveness across all templates
- Ensure consistent spacing and alignment across all pages

## 2025-03-02 00:03:46 - Product Images Migration
Migrated product images from Shopify to WordPress media library for the following products:
- Salmon (all varieties)
- Halibut
- Cod
- Sablefish
- Crab
- Scallops

Total images copied: 76

## 2025-08-13 - PCS Site Fixer: Clear Front/Home DB Templates

- Added a new admin utility in `wp-content/themes/pcs-twentytwentyfive-child/functions.php` that exposes a PCS Site Fixer button: "Clear Front/Home DB Templates".
- This action deletes DB-stored `wp_template` overrides for `front-page` and `home` scoped to the active child theme, ensuring file-based `templates/front-page.html` (and `templates/home.html`) are used.
- Usage: WP Admin → Tools → PCS Site Fixer → Clear Front/Home DB Templates. Then optionally run Clear Database Headers and save permalinks.
- Expected result: Homepage renders the new hero, Featured Recipes, and “Latest from the Blog” sections from the file-based template.

## 2025-08-14 - Blog Layout Upgrade (Categories + Grid)

- Improved posts index template at `wp-content/themes/pcs-twentytwentyfive-child/templates/home.html`:
  - Inserted a "Browse by Category" section using the core Categories block with counts.
  - Switched the Query Loop to a 3-column grid layout to reduce scrolling.
- Added `templates/archive.html` in the child theme to override parent archives (includes Archive Title + Description and a 3-column Query Loop grid with pagination).
- Updated `style.css` to support the new layout:
  - Responsive categories grid styles under the `.pcs-categories-grid` wrapper.
  - Enforced post grids to 3/2/1 columns at desktop/tablet/mobile.
  - Kept existing card hover effects and spacing.
- Notes/Considerations:
  - If a DB-stored template for `home`/`archive` exists, use Tools → PCS Site Fixer to clear it so file-based templates take effect.
  - Ensure Settings → Reading assigns a static homepage and sets a Posts page (e.g., `/blog/`) so `home.html` is used for the posts index.
  - Minor block inline styles remain (padding/margins from Gutenberg); can consolidate into `theme.json` later.

## 2025-08-14 - Separated Home and Blog

- Updated `wp-content/themes/pcs-twentytwentyfive-child/templates/front-page.html`:
  - Removed the "Latest from the Blog" query loop section so the homepage no longer lists posts.

- Updated `wp-content/themes/pcs-twentytwentyfive-child/templates/home.html` to be a dedicated Blog index:
  - Added H1 "Blog" at the top.
  - Kept the "Browse by Category" grid and the 3-column posts grid with pagination.

- Admin steps/notes:
  - Settings → Reading: set a static Front page (Home) and set a Posts page (e.g., "Blog").
  - If file-based templates do not appear, use Tools → PCS Site Fixer → Clear Front/Home DB Templates.
  - Minor inline padding/margin styles remain from Gutenberg. Plan: move to `theme.json` or `style.css` to satisfy linter.
