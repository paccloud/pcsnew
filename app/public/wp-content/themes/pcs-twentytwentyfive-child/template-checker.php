<?php
/**
 * Template Checker for Pacific Cloud Seafoods
 * 
 * This script checks which templates are being used for different page types
 * and ensures they're using the correct templates
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Check template usage across the site
 */
function pcs_check_templates() {
    echo "<h2>Template Usage Analysis</h2>\n";
    
    // Get current theme info
    $current_theme = wp_get_theme();
    echo "<h3>Current Theme</h3>\n";
    echo "<p>Theme: " . $current_theme->get('Name') . " v" . $current_theme->get('Version') . "</p>\n";
    echo "<p>Template: " . $current_theme->get('Template') . "</p>\n";
    
    // Check available templates
    echo "<h3>Available Templates</h3>\n";
    $child_templates = pcs_get_theme_templates(get_stylesheet_directory());
    $parent_templates = pcs_get_theme_templates(get_template_directory());
    
    echo "<h4>Child Theme Templates</h4>\n";
    if (!empty($child_templates)) {
        echo "<ul>\n";
        foreach ($child_templates as $template) {
            echo "<li>" . basename($template) . "</li>\n";
        }
        echo "</ul>\n";
    } else {
        echo "<p>No custom templates in child theme</p>\n";
    }
    
    echo "<h4>Parent Theme Templates</h4>\n";
    echo "<ul>\n";
    foreach ($parent_templates as $template) {
        echo "<li>" . basename($template) . "</li>\n";
    }
    echo "</ul>\n";
    
    // Check specific page types
    echo "<h3>Page Type Template Mapping</h3>\n";
    
    // Homepage
    $home_template = pcs_get_template_for_page_type('home');
    echo "<p><strong>Homepage:</strong> " . $home_template . "</p>\n";
    
    // Blog page
    $blog_template = pcs_get_template_for_page_type('blog');
    echo "<p><strong>Blog Page:</strong> " . $blog_template . "</p>\n";
    
    // Single post
    $single_template = pcs_get_template_for_page_type('single');
    echo "<p><strong>Single Post:</strong> " . $single_template . "</p>\n";
    
    // Page
    $page_template = pcs_get_template_for_page_type('page');
    echo "<p><strong>Static Page:</strong> " . $page_template . "</p>\n";
    
    // Recipe archive
    $recipe_archive_template = pcs_get_template_for_page_type('archive-recipe');
    echo "<p><strong>Recipe Archive:</strong> " . $recipe_archive_template . "</p>\n";
    
    // Single recipe
    $single_recipe_template = pcs_get_template_for_page_type('single-recipe');
    echo "<p><strong>Single Recipe:</strong> " . $single_recipe_template . "</p>\n";
    
    // Check specific pages
    echo "<h3>Specific Page Templates</h3>\n";
    $important_pages = array(
        'About Us' => '/about-us/',
        'Recipes' => '/recipes/',
        'Blog' => '/blog/',
        'FAQs' => '/faqs/',
        'Newsletter' => '/newsletter/'
    );
    
    foreach ($important_pages as $page_name => $page_url) {
        $page = get_page_by_path(trim($page_url, '/'));
        if ($page) {
            $template = get_page_template_slug($page->ID);
            if (empty($template)) {
                $template = 'page.html (default)';
            }
            echo "<p><strong>{$page_name}:</strong> {$template}</p>\n";
        } else {
            echo "<p><strong>{$page_name}:</strong> ❌ Page not found</p>\n";
        }
    }
    
    // Check template parts
    echo "<h3>Template Parts</h3>\n";
    $template_parts = array('header', 'footer', 'sidebar');
    foreach ($template_parts as $part) {
        $child_part = get_stylesheet_directory() . '/parts/' . $part . '.html';
        $parent_part = get_template_directory() . '/parts/' . $part . '.html';
        
        if (file_exists($child_part)) {
            echo "<p><strong>{$part}:</strong> ✅ Child theme override</p>\n";
        } elseif (file_exists($parent_part)) {
            echo "<p><strong>{$part}:</strong> ✅ Parent theme</p>\n";
        } else {
            echo "<p><strong>{$part}:</strong> ❌ Missing</p>\n";
        }
    }
    
    // Check for template issues
    echo "<h3>Template Issues Check</h3>\n";
    pcs_check_template_issues();
}

/**
 * Get all template files from a theme directory
 */
function pcs_get_theme_templates($theme_dir) {
    $templates = array();
    $template_dir = $theme_dir . '/templates';
    
    if (is_dir($template_dir)) {
        $files = glob($template_dir . '/*.html');
        $templates = array_merge($templates, $files);
    }
    
    return $templates;
}

/**
 * Get the template that would be used for a specific page type
 */
function pcs_get_template_for_page_type($type) {
    switch ($type) {
        case 'home':
            if (file_exists(get_stylesheet_directory() . '/templates/home.html')) {
                return 'home.html (child theme)';
            } elseif (file_exists(get_template_directory() . '/templates/home.html')) {
                return 'home.html (parent theme)';
            } else {
                return 'index.html (fallback)';
            }
            
        case 'blog':
            if (file_exists(get_stylesheet_directory() . '/templates/index.html')) {
                return 'index.html (child theme)';
            } elseif (file_exists(get_template_directory() . '/templates/index.html')) {
                return 'index.html (parent theme)';
            } else {
                return 'index.html (default)';
            }
            
        case 'single':
            if (file_exists(get_stylesheet_directory() . '/templates/single.html')) {
                return 'single.html (child theme)';
            } elseif (file_exists(get_template_directory() . '/templates/single.html')) {
                return 'single.html (parent theme)';
            } else {
                return 'index.html (fallback)';
            }
            
        case 'page':
            if (file_exists(get_stylesheet_directory() . '/templates/page.html')) {
                return 'page.html (child theme)';
            } elseif (file_exists(get_template_directory() . '/templates/page.html')) {
                return 'page.html (parent theme)';
            } else {
                return 'index.html (fallback)';
            }
            
        case 'archive-recipe':
            if (file_exists(get_stylesheet_directory() . '/templates/archive-recipe.html')) {
                return 'archive-recipe.html (child theme)';
            } elseif (file_exists(get_template_directory() . '/templates/archive-recipe.html')) {
                return 'archive-recipe.html (parent theme)';
            } else {
                return 'archive.html (fallback)';
            }
            
        case 'single-recipe':
            if (file_exists(get_stylesheet_directory() . '/templates/single-recipe.html')) {
                return 'single-recipe.html (child theme)';
            } elseif (file_exists(get_template_directory() . '/templates/single-recipe.html')) {
                return 'single-recipe.html (parent theme)';
            } else {
                return 'single.html (fallback)';
            }
            
        default:
            return 'Unknown';
    }
}

/**
 * Check for common template issues
 */
function pcs_check_template_issues() {
    $issues = array();
    
    // Check if home template includes footer
    $home_template = get_stylesheet_directory() . '/templates/home.html';
    if (file_exists($home_template)) {
        $content = file_get_contents($home_template);
        if (strpos($content, 'template-part') === false || strpos($content, 'footer') === false) {
            $issues[] = "❌ Home template may not include footer template part";
        } else {
            echo "<p>✅ Home template includes footer template part</p>\n";
        }
    }
    
    // Check if recipe archive template exists and is correct
    $recipe_archive = get_stylesheet_directory() . '/templates/archive-recipe.html';
    if (file_exists($recipe_archive)) {
        $content = file_get_contents($recipe_archive);
        if (strpos($content, 'template-part') === false || strpos($content, 'footer') === false) {
            $issues[] = "❌ Recipe archive template may not include footer template part";
        } else {
            echo "<p>✅ Recipe archive template includes footer template part</p>\n";
        }
    } else {
        $issues[] = "⚠️ Recipe archive template exists but may need footer check";
    }
    
    // Check for missing essential templates
    $essential_templates = array('page.html', 'single.html', 'archive.html');
    foreach ($essential_templates as $template) {
        $child_path = get_stylesheet_directory() . '/templates/' . $template;
        $parent_path = get_template_directory() . '/templates/' . $template;
        
        if (!file_exists($child_path) && !file_exists($parent_path)) {
            $issues[] = "❌ Missing essential template: {$template}";
        }
    }
    
    // Check template parts
    $essential_parts = array('header.html', 'footer.html');
    foreach ($essential_parts as $part) {
        $child_path = get_stylesheet_directory() . '/parts/' . $part;
        $parent_path = get_template_directory() . '/parts/' . $part;
        
        if (!file_exists($child_path) && !file_exists($parent_path)) {
            $issues[] = "❌ Missing essential template part: {$part}";
        }
    }
    
    if (empty($issues)) {
        echo "<p>✅ No template issues found</p>\n";
    } else {
        echo "<h4>Issues Found:</h4>\n<ul>\n";
        foreach ($issues as $issue) {
            echo "<li>{$issue}</li>\n";
        }
        echo "</ul>\n";
    }
}

/**
 * Fix common template issues
 */
function pcs_fix_template_issues() {
    $results = array();
    
    // Ensure essential templates exist in child theme
    $essential_templates = array(
        'page.html' => 'Standard page template',
        'single.html' => 'Single post template',
        'single-recipe.html' => 'Single recipe template'
    );
    
    foreach ($essential_templates as $template => $description) {
        $child_path = get_stylesheet_directory() . '/templates/' . $template;
        $parent_path = get_template_directory() . '/templates/' . $template;
        
        if (!file_exists($child_path)) {
            if (file_exists($parent_path)) {
                // Copy from parent theme
                $content = file_get_contents($parent_path);
                file_put_contents($child_path, $content);
                $results[] = "✅ Created {$template} from parent theme";
            } else {
                // Create basic template
                $basic_template = pcs_create_basic_template($template);
                file_put_contents($child_path, $basic_template);
                $results[] = "✅ Created basic {$template}";
            }
        }
    }
    
    return $results;
}

/**
 * Create a basic template with header and footer
 */
function pcs_create_basic_template($template_name) {
    $content = '<!-- wp:template-part {"slug":"header"} /-->' . "\n\n";
    
    switch ($template_name) {
        case 'page.html':
            $content .= '<!-- wp:group {"tagName":"main","layout":{"type":"constrained"}} -->' . "\n";
            $content .= '<main class="wp-block-group">' . "\n";
            $content .= '<!-- wp:post-title {"level":1} /-->' . "\n";
            $content .= '<!-- wp:post-content /-->' . "\n";
            $content .= '</main>' . "\n";
            $content .= '<!-- /wp:group -->' . "\n\n";
            break;
            
        case 'single.html':
            $content .= '<!-- wp:group {"tagName":"main","layout":{"type":"constrained"}} -->' . "\n";
            $content .= '<main class="wp-block-group">' . "\n";
            $content .= '<!-- wp:post-title {"level":1} /-->' . "\n";
            $content .= '<!-- wp:post-date /-->' . "\n";
            $content .= '<!-- wp:post-content /-->' . "\n";
            $content .= '<!-- wp:post-terms {"term":"category"} /-->' . "\n";
            $content .= '<!-- wp:post-terms {"term":"post_tag"} /-->' . "\n";
            $content .= '</main>' . "\n";
            $content .= '<!-- /wp:group -->' . "\n\n";
            break;
            
        case 'single-recipe.html':
            $content .= '<!-- wp:group {"tagName":"main","layout":{"type":"constrained"}} -->' . "\n";
            $content .= '<main class="wp-block-group">' . "\n";
            $content .= '<!-- wp:post-title {"level":1} /-->' . "\n";
            $content .= '<!-- wp:post-featured-image /-->' . "\n";
            $content .= '<!-- wp:post-content /-->' . "\n";
            $content .= '</main>' . "\n";
            $content .= '<!-- /wp:group -->' . "\n\n";
            break;
            
        default:
            $content .= '<!-- wp:group {"tagName":"main","layout":{"type":"constrained"}} -->' . "\n";
            $content .= '<main class="wp-block-group">' . "\n";
            $content .= '<!-- wp:post-content /-->' . "\n";
            $content .= '</main>' . "\n";
            $content .= '<!-- /wp:group -->' . "\n\n";
            break;
    }
    
    $content .= '<!-- wp:template-part {"slug":"footer"} /-->';
    
    return $content;
}

// If called directly for testing
if (defined('WP_CLI') && WP_CLI) {
    pcs_check_templates();
}
?>
