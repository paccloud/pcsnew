# System Patterns

## Architecture
- WordPress with WooCommerce
- Block theme (Twenty Twenty-Five child theme)
- Theme customizations via theme.json
- Git deployment to Hostinger

## Key Technical Patterns
1. **Block Theme Structure**
   - Uses WordPress block editor
   - Customizations through theme.json
   - No classic theme functions

2. **Child Theme Rules**
   - Edit only in pcs-twentytwentyfive-child directory
   - Parent stylesheet must be enqueued
   - Use theme.json for visual changes

3. **Content Structure**
   - Blog posts for stories and education
   - Recipe custom post type or category
   - Product pages via WooCommerce

## WordPress Best Practices
- Follow WordPress Coding Standards
- Use block patterns and templates
- Maintain valid block markup
