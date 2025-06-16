# Technical Context

## Development Environment

### Local by Flywheel Setup
- Site Name: pcsnew
- Domain: pcsnew.local
- URL: http://pcsnew.local/
- WordPress Version: Latest
- PHP Version: 8.1+
- Database: MySQL
- Web Server: Nginx

### Environment Requirements
- Local by Flywheel must be running
- Site services must be started through Local
- MySQL service must be accessible
- Custom domain resolution through Local
- SSL configuration through Local

### Database Configuration
- Name: local
- User: root
- Password: root
- Host: localhost
- Charset: utf8
- Collation: utf8_general_ci

## Project Migration (March 4, 2025)
- Migrated from `/Users/ryanhorwath/Local Sites/pacific-cloud-seafood/` to `/Users/ryanhorwath/Local Sites/pcsnew/`
- Created Twenty Twenty-Five child theme structure
- Transferred recipe plugin to new site
- Updated memory-bank files to reflect migration
- Set up proper theme hierarchy with Twenty Twenty-Five as parent theme

## Recipe System Architecture

### Template System
- Unified template handling for both theme types
- Block theme template part integration
- Output buffering for content capture
- Proper header/footer integration
- Responsive layout system

### JavaScript Architecture
- Unit conversion system:
  - Common cooking measurements (cups, tbsp, tsp)
  - Weight units (pounds, ounces)
  - Temperature conversion (F to C)
  - Length measurements (inches to cm)
- Serving size adjustment:
  - Fractional multipliers (½×)
  - Integer multipliers (1×, 2×, 3×)
  - Original value preservation
  - Dynamic recalculation
- Event handling:
  - Unit toggle events
  - Serving size events
  - Print functionality
  - Save recipe events

### CSS Architecture
- Modern grid-based layout
- Responsive design system
- Print-specific styles
- Mobile-first approach
- Icon integration (dashicons)
- Typography system
- Spacing system

### Data Management
- Post meta handling
- Original value preservation
- Dynamic content updates
- State management
- Template part integration

### Performance Considerations
- Optimized template loading
- Efficient DOM manipulation
- Print optimization
- Mobile performance
- Asset loading

### Security
- Data sanitization
- XSS prevention
- AJAX nonce verification
- Proper escaping

### Secrets Management (added 2025-06-16)
To keep credentials out of version control while still accessible locally:
1. Create `conf/local-secrets.env` (or `.env.local` at project root).
2. Add the following keys (values reside only on your machine):
   ```
   WP_API_URL=http://pcsnew.local/
   WP_API_USERNAME=cline
   WP_API_PASSWORD=<application-password>
   # Add any other API tokens here
   ```
3. The file path is already listed in `.gitignore`, so Git will ignore it.
4. Reference in PHP or tooling via `getenv('WP_API_PASSWORD')` etc.

- Data sanitization
- XSS prevention
- AJAX nonce verification
- Proper escaping

### Accessibility
- Semantic HTML
- ARIA attributes
- Keyboard navigation
- Screen reader support
- Color contrast

### Browser Support
- Modern browser features
- Legacy fallbacks
- Print functionality
- Mobile compatibility

## Implementation Details
1. Template Structure:
   - `single-recipe.php`: Main template
   - `single-recipe-content.php`: Content template
   - Block theme integration
   - Template part handling

2. JavaScript Components:
   - Unit conversion module
   - Serving size calculator
   - Event handlers
   - Print manager
   - Save functionality

3. CSS Components:
   - Layout grid
   - Typography system
   - Component styles
   - Responsive rules
   - Print styles

4. Data Flow:
   - Template loading
   - Content integration
   - Dynamic updates
   - State management
   - User interactions

## Template System
- Dual theme support for classic and block themes
- Block theme integration uses `template_include` filter with priority 999
- Template parts (header/footer) are loaded using `get_block_template_html()`
- Output buffering used to capture and inject recipe content
- Single recipe template handles both theme types adaptively

## Recipe Features
- Unit conversion system (US to metric)
- Dynamic serving size adjustment
- Print and share functionality
- Recipe metadata display (prep time, cook time, servings, etc.)
- Ingredient parsing and formatting
- Instruction step formatting

## WordPress Integration
- Custom post type: 'recipe'
- Custom taxonomies: 'recipe_category', 'recipe_tag'
- Block theme compatibility layer
- Template hierarchy respect
- AJAX handlers for dynamic updates

## Asset Management
- CSS: recipe-styles.css for layout and design
- JS: recipe-scripts.js for interactivity
- Conditional asset loading based on post type

## Security
- AJAX nonce verification
- Proper data sanitization
- Safe template loading

## Performance
- Optimized template loading
- Efficient block theme integration
- Minimal DOM manipulation

## Technical Changes
- **Site Logo Update**: Modified `parts/header.html` in the child theme to use the `core/site-logo` block, replacing `core/site-title`. The logo is sourced from the Media Library.
- **Recipe Archive PHP Warnings**: Copied `archive-recipe.php` from the parent theme to the child theme and added `is_object()` and `property_exists()` checks before accessing term properties to prevent 'Attempt to read property on array' warnings in filter dropdowns.
- Fixed header background color and navigation by overriding `parts/header.html` in the child theme.
- Enhanced menu item spacing (1em margins)
- Added relative positioning for dropdowns
- Improved touch targets (padding)
- Added responsive padding for tablet viewports
- Updated mobile menu breakpoint threshold
- Modified theme metadata to make it appear in WordPress admin
- Updated theme color palette with seafood-themed colors
- Changed text domain from "twentytwentyfive" to "pcs25"
- Created dynamic block "myheader" with namespace "myblocks"
- Installed WP-CLI version 2.11.0 via Homebrew
- Created centered-layout.css to fix layout alignment issues
- Added CSS for responsive layout with proper centering
- Updated functions.php to enqueue the new CSS file
- Implemented fixes for recipe blog layout:
  - Created multiple CSS files for recipe layout fixes (recipe-layout-fix.css, recipe-block-fix.css, recipe-archive-template.css)
  - Created custom PHP template for recipe archives (templates/archive-recipe.php)
  - Added body class for recipe archive pages
  - Implemented custom template loading for recipe archive and taxonomy pages
  - Added inline critical CSS for immediate rendering fixes

## Theme Structure (pcs25)
- Overrides parent theme's `parts/header.html` to customize header background, navigation, and display the site logo using `core/site-logo`.
- Child theme of Twenty Twenty-Five
- Theme name: "Pacific Cloud Seafood 25"
- Text domain: "pcs25"
- Template: twentytwentyfive
- Version: 1.0.0
- Custom CSS for navigation and layout
- Custom functions.php for style enqueuing
- Assets directory structure for CSS, JS, and images
- Integration with recipe plugin
- Custom recipe templates (single-recipe.php, archive-recipe.php). `archive-recipe.php` is now overridden in the child theme to fix PHP warnings.
- Custom CSS for recipe styling

## Block Development
- Dynamic block "myheader" created with @wordpress/create-block
- Block namespace: "myblocks"
- Block files located in theme directory
- Uses WordPress block API version 3
- Includes render.php for server-side rendering
- Requires npm build process for development

## Block Theme Integration Strategy

### Overview
The site uses WordPress block themes, specifically a customized Twenty Twenty-Five child theme. Our plugins need to properly integrate with this modern block-based approach while maintaining compatibility with classic themes.

### Block Theme Template Integration
- **Template Registration**: Using WordPress's block template system through `register_block_pattern()`
- **Template Parts**: Utilizing theme's header and footer template parts via block syntax
- **Content Formatting**: Custom content filter to inject recipe metadata into block editor content
- **Conditional Loading**: Different template handling for block vs classic themes

### Technical Implementation
```php
// Block Theme Detection
if ($this->is_using_block_theme()) {
    add_action('rest_api_init', array($this, 'register_recipe_meta_in_rest'));
    add_filter('the_content', array($this, 'format_recipe_content_for_blocks'), 20);
    add_action('init', array($this, 'register_recipe_block_template_parts'));
}

// Block Template Structure
<!-- wp:template-part {"slug":"header"} /-->
<!-- wp:group {"tagName":"main"} -->
<main class="wp-block-group">
    <!-- wp:post-title {"level":1,"align":"wide"} /-->
    <!-- wp:post-content {"align":"wide"} /-->
</main>
<!-- /wp:group -->
<!-- wp:template-part {"slug":"footer"} /-->
```

### Key Components
1. **Template Detection**:
   - Use `wp_is_block_theme()` to detect block themes
   - Different template handling for block vs classic themes

2. **Content Integration**:
   - Format recipe content for block editor
   - Inject recipe metadata into post content
   - Maintain proper block structure

3. **Template Parts**:
   - Use block syntax for header/footer inclusion
   - Maintain theme's styling and layout
   - Proper spacing and margin handling

4. **Block Patterns**:
   - Register custom patterns for recipe display
   - Follow block theme conventions
   - Maintain consistent styling

### Benefits
- Better integration with block themes
- Proper header/footer display
- Maintained styling consistency
- Enhanced content structure
- Future-proof approach

### Considerations
- Backward compatibility for classic themes
- Proper block structure maintenance
- Content formatting consistency
- Template part availability

## Development Tools
- WP-CLI 2.11.0 for WordPress command-line management
- npm for JavaScript package management
- @wordpress/scripts for block development

## MCP Configuration Status
- Playwright automation: Requires security approval
- Current testing method: Manual verification workflow
- Whitelisted domains: pacific-cloud-seafood.local

## Alternative Verification Plan
1. Manual navigation testing
2. Screen recording analysis
3. DOM inspection via browser tools
