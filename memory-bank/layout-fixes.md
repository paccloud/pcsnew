# Layout Fixes

## Navigation Issue (March 4, 2025)

### Problem
- Navigation menu is missing on single post pages and single recipe pages
- Home page, blog index, about page, and FAQs page have proper navigation

### Investigation
- Found that single-recipe.html template explicitly references the parent theme's header:
  `<!-- wp:template-part {"slug":"header","theme":"twentytwentyfive"} /-->`
- The parent theme's single.html doesn't specify a theme, but the child theme doesn't have its own single.html

### Solution
1. Create a "parts" directory in the child theme:
   `mkdir -p /Users/ryanhorwath/Local Sites/pcsnew/app/public/wp-content/themes/pcs25/parts`

2. Copy the header.html from parent theme to child theme:
   `cp /Users/ryanhorwath/Local Sites/pcsnew/app/public/wp-content/themes/twentytwentyfive/parts/header.html /Users/ryanhorwath/Local Sites/pcsnew/app/public/wp-content/themes/pcs25/parts/`

3. Update single-recipe.html to use the header without specifying theme:
   Change `<!-- wp:template-part {"slug":"header","theme":"twentytwentyfive"} /-->` 
   to `<!-- wp:template-part {"slug":"header"} /-->`

4. Create single.html in the child theme that includes the header part:
   Create file at `/Users/ryanhorwath/Local Sites/pcsnew/app/public/wp-content/themes/pcs25/templates/single.html`
   with content from parent theme but ensuring header reference doesn't specify theme

5. Also ensure footer references don't specify the parent theme

### Implementation Steps
```bash
# 1. Create parts directory
mkdir -p /Users/ryanhorwath/Local Sites/pcsnew/app/public/wp-content/themes/pcs25/parts

# 2. Copy header from parent theme
cp /Users/ryanhorwath/Local Sites/pcsnew/app/public/wp-content/themes/twentytwentyfive/parts/header.html /Users/ryanhorwath/Local Sites/pcsnew/app/public/wp-content/themes/pcs25/parts/

# 3. Update single-recipe.html
# Edit: /Users/ryanhorwath/Local Sites/pcsnew/app/public/wp-content/themes/pcs25/templates/single-recipe.html
# Change: <!-- wp:template-part {"slug":"header","theme":"twentytwentyfive"} /-->
# To: <!-- wp:template-part {"slug":"header"} /-->
# Also change: <!-- wp:template-part {"slug":"footer","theme":"twentytwentyfive"} /-->
# To: <!-- wp:template-part {"slug":"footer"} /-->

# 4. Copy and create single.html in child theme
cp /Users/ryanhorwath/Local Sites/pcsnew/app/public/wp-content/themes/twentytwentyfive/templates/single.html /Users/ryanhorwath/Local Sites/pcsnew/app/public/wp-content/themes/pcs25/templates/
```

### Expected Result
- Navigation menu will appear consistently on all pages
- The child theme's header will be used if it exists, or will fall back to the parent theme's header
- Same with the footer

### Notes
- When using `wp:template-part` without specifying a theme, WordPress will first look in the child theme and then fall back to the parent theme
- This approach maintains the block theme best practices
