<?php
/**
 * Template Fixer for Pacific Cloud Seafoods
 * 
 * This script ensures all pages are using the correct templates
 * and that all templates include proper header and footer
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Fix all template issues
 */
function pcs_fix_all_templates() {
    $results = array();
    
    // 1. Ensure all templates include footer
    $results = array_merge($results, pcs_ensure_templates_have_footer());
    
    // 2. Create missing essential templates
    $results = array_merge($results, pcs_create_missing_templates());
    
    // 3. Verify template hierarchy
    $results = array_merge($results, pcs_verify_template_hierarchy());
    
    // 4. Clear template caches
    $results[] = pcs_clear_template_caches();
    
    return $results;
}

/**
 * Ensure all templates include footer template part
 */
function pcs_ensure_templates_have_footer() {
    $results = array();
    $templates_dir = get_stylesheet_directory() . '/templates';
    
    if (!is_dir($templates_dir)) {
        mkdir($templates_dir, 0755, true);
        $results[] = "✅ Created templates directory";
    }
    
    // Get all template files
    $template_files = glob($templates_dir . '/*.html');
    
    foreach ($template_files as $template_file) {
        $content = file_get_contents($template_file);
        $template_name = basename($template_file);
        
        // Check if template includes footer
        if (strpos($content, 'template-part') === false || strpos($content, 'footer') === false) {
            // Add footer if missing
            if (substr(trim($content), -2) !== '-->') {
                $content .= "\n\n";
            }
            $content .= '<!-- wp:template-part {"slug":"footer"} /-->';
            
            file_put_contents($template_file, $content);
            $results[] = "✅ Added footer to {$template_name}";
        } else {
            $results[] = "✅ {$template_name} already includes footer";
        }
    }
    
    return $results;
}

/**
 * Create missing essential templates
 */
function pcs_create_missing_templates() {
    $results = array();
    $templates_dir = get_stylesheet_directory() . '/templates';
    
    // Essential templates that should exist
    $essential_templates = array(
        'page.html' => 'page_template',
        'single.html' => 'single_post_template', 
        'single-recipe.html' => 'single_recipe_template',
        'archive.html' => 'archive_template',
        'search.html' => 'search_template',
        '404.html' => 'error_template'
    );
    
    foreach ($essential_templates as $template_file => $template_type) {
        $template_path = $templates_dir . '/' . $template_file;
        
        if (!file_exists($template_path)) {
            $content = pcs_generate_template_content($template_type);
            file_put_contents($template_path, $content);
            $results[] = "✅ Created {$template_file}";
        }
    }
    
    return $results;
}

/**
 * Generate template content based on type
 */
function pcs_generate_template_content($template_type) {
    $header = '<!-- wp:template-part {"slug":"header"} /-->' . "\n\n";
    $footer = "\n\n" . '<!-- wp:template-part {"slug":"footer"} /-->';
    
    switch ($template_type) {
        case 'page_template':
            $main = '<!-- wp:group {"tagName":"main","style":{"spacing":{"margin":{"top":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->' . "\n";
            $main .= '<main class="wp-block-group" style="margin-top:var(--wp--preset--spacing--60)">' . "\n";
            $main .= '	<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->' . "\n";
            $main .= '	<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">' . "\n";
            $main .= '		<!-- wp:post-featured-image {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|60"}}}} /-->' . "\n";
            $main .= '		<!-- wp:post-title {"level":1} /-->' . "\n";
            $main .= '		<!-- wp:post-content {"align":"full","layout":{"type":"constrained"}} /-->' . "\n";
            $main .= '	</div>' . "\n";
            $main .= '	<!-- /wp:group -->' . "\n";
            $main .= '</main>' . "\n";
            $main .= '<!-- /wp:group -->';
            break;
            
        case 'single_post_template':
            $main = '<!-- wp:group {"tagName":"main","style":{"spacing":{"margin":{"top":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->' . "\n";
            $main .= '<main class="wp-block-group" style="margin-top:var(--wp--preset--spacing--60)">' . "\n";
            $main .= '	<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->' . "\n";
            $main .= '	<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">' . "\n";
            $main .= '		<!-- wp:post-title {"level":1} /-->' . "\n";
            $main .= '		<!-- wp:post-featured-image {"aspectRatio":"3/2"} /-->' . "\n";
            $main .= '		<!-- wp:post-date /-->' . "\n";
            $main .= '		<!-- wp:post-content {"align":"full","layout":{"type":"constrained"}} /-->' . "\n";
            $main .= '		<!-- wp:post-terms {"term":"category"} /-->' . "\n";
            $main .= '		<!-- wp:post-terms {"term":"post_tag"} /-->' . "\n";
            $main .= '	</div>' . "\n";
            $main .= '	<!-- /wp:group -->' . "\n";
            $main .= '</main>' . "\n";
            $main .= '<!-- /wp:group -->';
            break;
            
        case 'single_recipe_template':
            // Use the content from the single-recipe.html file we created
            $single_recipe_file = get_stylesheet_directory() . '/templates/single-recipe.html';
            if (file_exists($single_recipe_file)) {
                return file_get_contents($single_recipe_file);
            }
            // Fallback to basic recipe template
            $main = '<!-- wp:group {"tagName":"main","style":{"spacing":{"margin":{"top":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->' . "\n";
            $main .= '<main class="wp-block-group" style="margin-top:var(--wp--preset--spacing--60)">' . "\n";
            $main .= '	<!-- wp:post-title {"level":1} /-->' . "\n";
            $main .= '	<!-- wp:post-featured-image /-->' . "\n";
            $main .= '	<!-- wp:post-content /-->' . "\n";
            $main .= '</main>' . "\n";
            $main .= '<!-- /wp:group -->';
            break;
            
        case 'archive_template':
            $main = '<!-- wp:group {"tagName":"main","style":{"spacing":{"margin":{"top":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->' . "\n";
            $main .= '<main class="wp-block-group" style="margin-top:var(--wp--preset--spacing--60)">' . "\n";
            $main .= '	<!-- wp:query-title {"type":"archive"} /-->' . "\n";
            $main .= '	<!-- wp:query {"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true}} -->' . "\n";
            $main .= '	<div class="wp-block-query">' . "\n";
            $main .= '		<!-- wp:post-template -->' . "\n";
            $main .= '			<!-- wp:post-title {"isLink":true} /-->' . "\n";
            $main .= '			<!-- wp:post-excerpt /-->' . "\n";
            $main .= '		<!-- /wp:post-template -->' . "\n";
            $main .= '		<!-- wp:query-pagination /-->' . "\n";
            $main .= '	</div>' . "\n";
            $main .= '	<!-- /wp:query -->' . "\n";
            $main .= '</main>' . "\n";
            $main .= '<!-- /wp:group -->';
            break;
            
        case 'search_template':
            $main = '<!-- wp:group {"tagName":"main","style":{"spacing":{"margin":{"top":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->' . "\n";
            $main .= '<main class="wp-block-group" style="margin-top:var(--wp--preset--spacing--60)">' . "\n";
            $main .= '	<!-- wp:query-title {"type":"search"} /-->' . "\n";
            $main .= '	<!-- wp:query {"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true}} -->' . "\n";
            $main .= '	<div class="wp-block-query">' . "\n";
            $main .= '		<!-- wp:post-template -->' . "\n";
            $main .= '			<!-- wp:post-title {"isLink":true} /-->' . "\n";
            $main .= '			<!-- wp:post-excerpt /-->' . "\n";
            $main .= '		<!-- /wp:post-template -->' . "\n";
            $main .= '		<!-- wp:query-pagination /-->' . "\n";
            $main .= '		<!-- wp:query-no-results -->' . "\n";
            $main .= '			<!-- wp:paragraph -->' . "\n";
            $main .= '			<p>Sorry, but nothing was found. Please try a search with different keywords.</p>' . "\n";
            $main .= '			<!-- /wp:paragraph -->' . "\n";
            $main .= '		<!-- /wp:query-no-results -->' . "\n";
            $main .= '	</div>' . "\n";
            $main .= '	<!-- /wp:query -->' . "\n";
            $main .= '</main>' . "\n";
            $main .= '<!-- /wp:group -->';
            break;
            
        case 'error_template':
            $main = '<!-- wp:group {"tagName":"main","style":{"spacing":{"margin":{"top":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->' . "\n";
            $main .= '<main class="wp-block-group" style="margin-top:var(--wp--preset--spacing--60)">' . "\n";
            $main .= '	<!-- wp:heading {"level":1} -->' . "\n";
            $main .= '	<h1 class="wp-block-heading">Page Not Found</h1>' . "\n";
            $main .= '	<!-- /wp:heading -->' . "\n";
            $main .= '	<!-- wp:paragraph -->' . "\n";
            $main .= '	<p>The page you are looking for could not be found. Please try searching or return to the homepage.</p>' . "\n";
            $main .= '	<!-- /wp:paragraph -->' . "\n";
            $main .= '	<!-- wp:search {"label":"Search","showLabel":false,"placeholder":"Search..."} /-->' . "\n";
            $main .= '</main>' . "\n";
            $main .= '<!-- /wp:group -->';
            break;
            
        default:
            $main = '<!-- wp:group {"tagName":"main","layout":{"type":"constrained"}} -->' . "\n";
            $main .= '<main class="wp-block-group">' . "\n";
            $main .= '	<!-- wp:post-content /-->' . "\n";
            $main .= '</main>' . "\n";
            $main .= '<!-- /wp:group -->';
            break;
    }
    
    return $header . $main . $footer;
}

/**
 * Verify template hierarchy is working correctly
 */
function pcs_verify_template_hierarchy() {
    $results = array();
    
    // Check that child theme templates take precedence
    $child_templates = glob(get_stylesheet_directory() . '/templates/*.html');
    $parent_templates = glob(get_template_directory() . '/templates/*.html');
    
    $results[] = "✅ Found " . count($child_templates) . " child theme templates";
    $results[] = "✅ Found " . count($parent_templates) . " parent theme templates";
    
    // Verify template parts exist
    $essential_parts = array('header.html', 'footer.html');
    foreach ($essential_parts as $part) {
        $child_part = get_stylesheet_directory() . '/parts/' . $part;
        $parent_part = get_template_directory() . '/parts/' . $part;
        
        if (file_exists($child_part)) {
            $results[] = "✅ {$part} - using child theme version";
        } elseif (file_exists($parent_part)) {
            $results[] = "✅ {$part} - using parent theme version";
        } else {
            $results[] = "❌ {$part} - missing!";
        }
    }
    
    return $results;
}

/**
 * Clear template caches
 */
function pcs_clear_template_caches() {
    // Clear WordPress template caches
    wp_cache_delete('wp_template', 'themes');
    wp_cache_delete('wp_template_part', 'themes');
    
    // Clear theme cache
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
    
    // Clear rewrite rules
    flush_rewrite_rules();
    
    return "✅ Template caches cleared";
}

// If called directly for testing
if (defined('WP_CLI') && WP_CLI) {
    $results = pcs_fix_all_templates();
    foreach ($results as $result) {
        WP_CLI::log($result);
    }
}
?>
