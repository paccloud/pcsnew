<?php
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'twentytwentyfive-style', get_template_directory_uri() . '/style.css' );
} );

/**
 * Clean up post tags - remove "Recipe" tags from non-recipe posts
 * This function helps separate blog posts from recipes properly
 */
function pcs_clean_post_tags() {
    // Get all posts that have the "Recipe" tag
    $posts_with_recipe_tag = get_posts(array(
        'post_type' => 'post', // Only regular posts, not recipe post type
        'posts_per_page' => -1,
        'meta_query' => array(),
        'tax_query' => array(
            array(
                'taxonomy' => 'post_tag',
                'field'    => 'slug',
                'terms'    => 'recipe',
            )
        )
    ));
    
    // Remove "Recipe" tag from these posts
    foreach ($posts_with_recipe_tag as $post) {
        $recipe_tag = get_term_by('slug', 'recipe', 'post_tag');
        if ($recipe_tag) {
            wp_remove_object_terms($post->ID, $recipe_tag->term_id, 'post_tag');
        }
    }
}

/**
 * Add featured images to recipes that don't have them
 * This function creates placeholder featured images for recipes
 */
function pcs_add_recipe_featured_images() {
    // Get all recipes without featured images
    $recipes_without_images = get_posts(array(
        'post_type' => 'recipe',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_thumbnail_id',
                'compare' => 'NOT EXISTS'
            )
        )
    ));
    
    // For each recipe without an image, try to find and set an appropriate image
    foreach ($recipes_without_images as $recipe) {
        // Try to find an image based on the recipe title
        $title = strtolower($recipe->post_title);
        $image_id = null;
        
        // Simple mapping of recipe names to potential image searches
        if (strpos($title, 'salmon') !== false) {
            $image_id = pcs_get_image_by_name('salmon');
        } elseif (strpos($title, 'cod') !== false) {
            $image_id = pcs_get_image_by_name('cod');
        } elseif (strpos($title, 'halibut') !== false) {
            $image_id = pcs_get_image_by_name('halibut');
        } elseif (strpos($title, 'sablefish') !== false) {
            $image_id = pcs_get_image_by_name('sablefish');
        } elseif (strpos($title, 'scallop') !== false) {
            $image_id = pcs_get_image_by_name('scallop');
        } else {
            // Default to a general seafood image
            $image_id = pcs_get_image_by_name('seafood');
        }
        
        // Set the featured image if one was found
        if ($image_id) {
            set_post_thumbnail($recipe->ID, $image_id);
        }
    }
}

/**
 * Helper function to find an image by name in the media library
 */
function pcs_get_image_by_name($search_term) {
    $attachments = get_posts(array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'posts_per_page' => 1,
        's' => $search_term
    ));
    
    if (!empty($attachments)) {
        return $attachments[0]->ID;
    }
    
    return null;
}

/**
 * Admin function to manually trigger cleanup
 * Add this to admin menu for easy access
 */
function pcs_add_admin_menu() {
    add_management_page(
        'PCS Site Fixer',
        'PCS Site Fixer',
        'manage_options',
        'pcs-site-fixer',
        'pcs_site_fixer_page'
    );
}
add_action('admin_menu', 'pcs_add_admin_menu');

/**
 * Admin page for site fixes
 */
function pcs_site_fixer_page() {
    if (isset($_POST['clean_tags'])) {
        pcs_clean_post_tags();
        echo '<div class="notice notice-success"><p>Post tags cleaned successfully!</p></div>';
    }

    if (isset($_POST['add_images'])) {
        pcs_add_recipe_featured_images();
        echo '<div class="notice notice-success"><p>Recipe featured images added successfully!</p></div>';
    }

    if (isset($_POST['debug_footer'])) {
        echo '<div class="notice notice-info"><p>Footer debug information:</p></div>';
        include get_stylesheet_directory() . '/footer-debug.php';
        pcs_debug_footer();
        return;
    }

    if (isset($_POST['fix_footer'])) {
        include get_stylesheet_directory() . '/fix-footer.php';
        $results = pcs_fix_footer();
        echo '<div class="notice notice-success"><p>Footer fix completed:</p><ul>';
        foreach ($results as $result) {
            echo '<li>' . $result . '</li>';
        }
        echo '</ul></div>';

        $status = pcs_check_footer_status();
        echo '<div class="notice notice-info"><p>Footer status after fix:</p><ul>';
        echo '<li>Footer file exists: ' . ($status['footer_file'] ? '✅ Yes' : '❌ No') . '</li>';
        echo '<li>Footer pattern registered: ' . ($status['footer_pattern'] ? '✅ Yes' : '❌ No') . '</li>';
        echo '<li>Template parts in database: ' . $status['template_parts_count'] . '</li>';
        echo '</ul></div>';
    }

    if (isset($_POST['check_templates'])) {
        echo '<div class="notice notice-info"><p>Template analysis:</p></div>';
        include get_stylesheet_directory() . '/template-checker.php';
        pcs_check_templates();
        return;
    }

    if (isset($_POST['fix_templates'])) {
        include get_stylesheet_directory() . '/template-checker.php';
        $results = pcs_fix_template_issues();
        if (!empty($results)) {
            echo '<div class="notice notice-success"><p>Template fixes completed:</p><ul>';
            foreach ($results as $result) {
                echo '<li>' . $result . '</li>';
            }
            echo '</ul></div>';
        } else {
            echo '<div class="notice notice-info"><p>No template fixes needed - all templates are in place.</p></div>';
        }
    }

    if (isset($_POST['fix_all_templates'])) {
        include get_stylesheet_directory() . '/template-fixer.php';
        $results = pcs_fix_all_templates();
        echo '<div class="notice notice-success"><p>Comprehensive template fix completed:</p><ul>';
        foreach ($results as $result) {
            echo '<li>' . $result . '</li>';
        }
        echo '</ul></div>';
        echo '<div class="notice notice-info"><p><strong>Important:</strong> Please check your site to ensure all pages are displaying correctly with headers and footers.</p></div>';
    }

    if (isset($_POST['fix_full_width'])) {
        include get_stylesheet_directory() . '/fix-full-width.php';
        $results = pcs_fix_full_width();
        echo '<div class="notice notice-success"><p>Full-width fix completed:</p><ul>';
        foreach ($results as $result) {
            echo '<li>' . $result . '</li>';
        }
        echo '</ul></div>';

        $status = pcs_check_full_width_status();
        echo '<div class="notice notice-info"><p>Full-width status after fix:</p><ul>';
        foreach ($status as $status_item) {
            echo '<li>' . $status_item . '</li>';
        }
        echo '</ul></div>';
    }

    if (isset($_POST['debug_header'])) {
        echo '<div class="notice notice-info"><p>Header debug information:</p></div>';
        include get_stylesheet_directory() . '/debug-header.php';
        pcs_debug_header();
        return;
    }

    if (isset($_POST['clear_header_db'])) {
        include get_stylesheet_directory() . '/debug-header.php';
        $results = pcs_clear_database_header_templates();
        echo '<div class="notice notice-success"><p>Database header templates cleared:</p><ul>';
        foreach ($results as $result) {
            echo '<li>' . $result . '</li>';
        }
        echo '</ul></div>';
    }
    
    ?>
    <div class="wrap">
        <h1>PCS Site Fixer</h1>
        <p>Use these tools to fix common site issues:</p>
        
        <h2>Clean Post Tags</h2>
        <p>Remove "Recipe" tags from regular blog posts to prevent them from appearing in recipe queries.</p>
        <form method="post" action="">
            <input type="submit" name="clean_tags" class="button-primary" value="Clean Post Tags" />
        </form>
        
        <h2>Add Recipe Featured Images</h2>
        <p>Automatically add featured images to recipes that don't have them based on their content.</p>
        <form method="post" action="">
            <input type="submit" name="add_images" class="button-primary" value="Add Recipe Images" />
        </form>

        <h2>Debug Footer</h2>
        <p>Check footer template parts and patterns for display issues.</p>
        <form method="post" action="">
            <input type="submit" name="debug_footer" class="button-secondary" value="Debug Footer" />
        </form>

        <h2>Fix Footer</h2>
        <p>Automatically fix common footer display issues (clears caches, fixes template parts, registers patterns).</p>
        <form method="post" action="">
            <input type="submit" name="fix_footer" class="button-primary" value="Fix Footer Now" onclick="return confirm('This will clear theme caches and fix footer template parts. Continue?');" />
        </form>

        <h2>Check Templates</h2>
        <p>Analyze which templates are being used for different page types and identify any issues.</p>
        <form method="post" action="">
            <input type="submit" name="check_templates" class="button-secondary" value="Check Templates" />
        </form>

        <h2>Fix Templates</h2>
        <p>Automatically create missing essential templates (page, single post, single recipe) with proper header/footer.</p>
        <form method="post" action="">
            <input type="submit" name="fix_templates" class="button-primary" value="Fix Templates" onclick="return confirm('This will create missing template files. Continue?');" />
        </form>

        <h2>Comprehensive Template Fix</h2>
        <p><strong>Complete template solution:</strong> Creates all missing templates, ensures footers are included, and verifies template hierarchy.</p>
        <form method="post" action="">
            <input type="submit" name="fix_all_templates" class="button-primary" value="Fix All Templates" onclick="return confirm('This will perform a comprehensive template fix. This may create/modify multiple template files. Continue?');" style="background-color: #d63638; border-color: #d63638;" />
        </form>

        <h2>Fix Full-Width Header & Footer</h2>
        <p><strong>Make header and footer stretch full screen width:</strong> Updates template parts and CSS to ensure header and footer span the entire width of the screen.</p>
        <form method="post" action="">
            <input type="submit" name="fix_full_width" class="button-primary" value="Fix Full-Width Layout" onclick="return confirm('This will update header and footer templates to use full-width layout. Continue?');" />
        </form>

        <h2>Debug Header Template</h2>
        <p>Check which header template is being used and identify any database overrides that might prevent file changes from taking effect.</p>
        <form method="post" action="">
            <input type="submit" name="debug_header" class="button-secondary" value="Debug Header" />
        </form>

        <h2>Clear Database Header Templates</h2>
        <p><strong>Force use of file-based header:</strong> Removes any database-stored header template parts that might be overriding your file-based header template.</p>
        <form method="post" action="">
            <input type="submit" name="clear_header_db" class="button-primary" value="Clear Database Headers" onclick="return confirm('This will delete any database-stored header templates and force WordPress to use your file-based header template. Continue?');" style="background-color: #d63638; border-color: #d63638;" />
        </form>
        
        <h2>Current Status</h2>
        <?php
        // Show current status
        $posts_with_recipe_tag = get_posts(array(
            'post_type' => 'post',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'post_tag',
                    'field'    => 'slug',
                    'terms'    => 'recipe',
                )
            )
        ));
        
        $recipes_without_images = get_posts(array(
            'post_type' => 'recipe',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_thumbnail_id',
                    'compare' => 'NOT EXISTS'
                )
            )
        ));
        
        echo '<ul>';
        echo '<li>Blog posts with "Recipe" tag: ' . count($posts_with_recipe_tag) . '</li>';
        echo '<li>Recipes without featured images: ' . count($recipes_without_images) . '</li>';
        echo '</ul>';
        ?>
    </div>
    <?php
}

/**
 * Register Pacific Cloud Seafoods custom patterns
 */
function pcs_register_patterns() {
    // Get the footer pattern content from the file
    $footer_pattern_file = get_stylesheet_directory() . '/patterns/footer.php';

    if (file_exists($footer_pattern_file)) {
        // Start output buffering to capture the pattern content
        ob_start();
        include $footer_pattern_file;
        $footer_content = ob_get_clean();

        // Extract just the HTML content (remove PHP header)
        $footer_content = preg_replace('/^<\?php.*?\?\>\s*/s', '', $footer_content);

        // Register the custom footer pattern
        register_block_pattern(
            'pcs-twentytwentyfive-child/footer',
            array(
                'title'       => __('Pacific Cloud Seafoods Footer', 'pcs-twentytwentyfive-child'),
                'description' => __('Footer with logo, title, tagline and navigation links to existing pages.', 'pcs-twentytwentyfive-child'),
                'content'     => trim($footer_content),
                'categories'  => array('footer'),
                'keywords'    => array('footer', 'navigation', 'pacific cloud seafoods'),
            )
        );
    }
}
add_action('init', 'pcs_register_patterns');
