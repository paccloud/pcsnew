<?php
/**
 * Shopify to WordPress Blog Post Converter
 *
 * This script converts Shopify blog posts to WordPress format
 *
 * @package PCS_Blog_Importer
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Shopify to WordPress Blog Post Converter Class
 */
class PCS_Shopify_Blog_Importer {
    /**
     * Shopify blog posts JSON file path
     */
    private $shopify_blogs_file;

    /**
     * Shopify blog posts directory
     */
    private $shopify_blogs_dir;

    /**
     * WordPress content directory
     */
    private $wp_content_dir;

    /**
     * Constructor
     *
     * @param string $shopify_file Optional path to Shopify blogs file
     */
    public function __construct($shopify_file = '') {
        // Set the WordPress content directory
        $this->wp_content_dir = WP_CONTENT_DIR . '/uploads/content/blog-posts/';

        // Create the WordPress content directory if it doesn't exist
        if (!file_exists($this->wp_content_dir)) {
            mkdir($this->wp_content_dir, 0755, true);
        }

        // Set the Shopify blogs file if provided
        if (!empty($shopify_file)) {
            $this->shopify_blogs_file = $shopify_file;
            $this->shopify_blogs_dir = dirname($shopify_file);
        }
    }

    /**
     * Import blog posts from the Shopify file
     *
     * @return array Result array with success status and counts
     */
    public function import_blog_posts() {
        // Check if the Shopify blogs file is set
        if (empty($this->shopify_blogs_file)) {
            $this->output_message('Shopify blogs file not specified.', 'error');
            return array(
                'success' => false,
                'message' => 'Shopify blogs file not specified.',
                'imported' => 0,
                'skipped' => 0
            );
        }

        // Read the file content
        if (!file_exists($this->shopify_blogs_file)) {
            $this->output_message('Shopify blogs file not found: ' . $this->shopify_blogs_file, 'error');
            return array(
                'success' => false,
                'message' => 'Shopify blogs file not found.',
                'imported' => 0,
                'skipped' => 0
            );
        }

        $content = file_get_contents($this->shopify_blogs_file);

        if (!$content) {
            $this->output_message('Failed to read Shopify blogs file.', 'error');
            return array(
                'success' => false,
                'message' => 'Failed to read Shopify blogs file.',
                'imported' => 0,
                'skipped' => 0
            );
        }

        // Decode the JSON content
        $blogs = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $error_message = 'JSON decode error: ' . json_last_error_msg();
            $this->output_message($error_message, 'error');
            return array(
                'success' => false,
                'message' => $error_message,
                'imported' => 0,
                'skipped' => 0
            );
        }

        if (!$blogs || !isset($blogs['blogs'])) {
            // Try to handle a different JSON format - maybe it's a direct array of blog posts
            if (is_array($blogs) && !empty($blogs)) {
                // Assume it's an array of blog posts
                $direct_posts = $blogs;
                $blogs = array('blogs' => array(
                    array(
                        'title' => 'Imported Blog',
                        'handle' => 'imported-blog',
                        'articles' => $direct_posts
                    )
                ));
            } else {
                $this->output_message('Invalid JSON format in Shopify blogs file.', 'error');
                return array(
                    'success' => false,
                    'message' => 'Invalid JSON format in Shopify blogs file.',
                    'imported' => 0,
                    'skipped' => 0
                );
            }
        }

        $imported_count = 0;
        $skipped_count = 0;
        $imported_posts = array();

        // Process each blog
        foreach ($blogs['blogs'] as $blog) {
            $blog_title = isset($blog['title']) ? $blog['title'] : 'Imported Blog';
            $blog_handle = isset($blog['handle']) ? $blog['handle'] : sanitize_title($blog_title);

            $this->output_message('Processing blog: ' . $blog_title, 'info');

            // Process each article in the blog
            if (isset($blog['articles']) && is_array($blog['articles'])) {
                foreach ($blog['articles'] as $article) {
                    $result = $this->process_article($article, $blog_title, $blog_handle);

                    if ($result) {
                        $imported_count++;
                        $imported_posts[] = $result;
                    } else {
                        $skipped_count++;
                    }
                }
            }
        }

        // Check for individual markdown files
        if (!empty($this->shopify_blogs_dir)) {
            $markdown_files = glob($this->shopify_blogs_dir . '/*.md');
            if (is_array($markdown_files)) {
                foreach ($markdown_files as $markdown_file) {
                    $result = $this->process_markdown_file($markdown_file);

                    if ($result) {
                        $imported_count++;
                        $imported_posts[] = $result;
                    } else {
                        $skipped_count++;
                    }
                }
            }
        }

        $this->output_message('Import complete. Imported: ' . $imported_count . ', Skipped: ' . $skipped_count, 'success');

        return array(
            'success' => ($imported_count > 0),
            'message' => 'Import complete.',
            'imported' => $imported_count,
            'skipped' => $skipped_count,
            'posts' => $imported_posts
        );
    }

    /**
     * Process a Shopify article
     *
     * @param array $article The article data
     * @param string $blog_title The blog title
     * @param string $blog_handle The blog handle
     * @return array|false Result array or false on failure
     */
    private function process_article($article, $blog_title, $blog_handle) {
        // Validate required fields
        if (!isset($article['title']) || !isset($article['content'])) {
            $this->output_message('Article missing required fields (title or content)', 'error');
            return false;
        }

        $title = $article['title'];
        $handle = isset($article['handle']) ? $article['handle'] : sanitize_title($title);
        $content = $article['content'];
        $published_at = isset($article['publishedAt']) ? $article['publishedAt'] : current_time('mysql');
        $tags = isset($article['tags']) ? $article['tags'] : array();
        $image = isset($article['image']) ? $article['image'] : '';

        $this->output_message('Processing article: ' . $title, 'info');

        // Create a unique filename for the article
        $filename = sanitize_file_name($handle) . '.php';
        $file_path = $this->wp_content_dir . $filename;

        // Convert content to WordPress blocks format
        $blocks_content = $this->convert_to_blocks($content);

        // Determine categories based on blog handle
        $categories = array($blog_title);

        // Create post directly in WordPress
        $post_data = array(
            'post_title'    => $title,
            'post_content'  => $blocks_content,
            'post_status'   => 'publish',
            'post_type'     => 'post',
            'post_excerpt'  => substr(strip_tags($content), 0, 150) . '...',
            'post_date'     => date('Y-m-d H:i:s', strtotime($published_at))
        );

        // Insert the post
        $post_id = wp_insert_post($post_data);

        if (is_wp_error($post_id)) {
            $this->output_message('Failed to create post: ' . $post_id->get_error_message(), 'error');
            return false;
        }

        // Set categories
        foreach ($categories as $category_name) {
            $category = get_term_by('name', $category_name, 'category');
            if (!$category) {
                // Create the category if it doesn't exist
                $category_id = wp_create_category($category_name);
            } else {
                $category_id = $category->term_id;
            }
            wp_set_post_categories($post_id, array($category_id), true);
        }

        // Set tags
        if (!empty($tags)) {
            wp_set_post_tags($post_id, $tags);
        }

        // Set featured image if provided
        if (!empty($image)) {
            // TODO: Import the featured image
        }

        // Set post meta
        update_post_meta($post_id, '_original_shopify_blog', $blog_handle);
        update_post_meta($post_id, '_original_shopify_handle', $handle);

        // Also save the PHP file for reference
        $php_content = '<?php
return array(
    "title" => "' . esc_js($title) . '",
    "post_type" => "post",
    "post_status" => "publish",
    "post_excerpt" => "' . esc_js(substr(strip_tags($content), 0, 150)) . '...",
    "post_date" => "' . date('Y-m-d H:i:s', strtotime($published_at)) . '",
    "post_content" => "' . esc_js($blocks_content) . '",
    "featured_image" => "' . esc_js($image) . '",
    "categories" => array("' . implode('", "', array_map('esc_js', $categories)) . '"),
    "tags" => array("' . implode('", "', array_map('esc_js', $tags)) . '"),
    "meta" => array(
        "_original_shopify_blog" => "' . esc_js($blog_handle) . '",
        "_original_shopify_handle" => "' . esc_js($handle) . '"
    )
);';

        file_put_contents($file_path, $php_content);

        $this->output_message('Created post: ' . $title, 'success');
        return array(
            'post_id' => $post_id,
            'title' => $title,
            'handle' => $handle,
            'file' => $filename
        );
    }

    /**
     * Process a Shopify markdown file
     *
     * @param string $markdown_file Path to the markdown file
     * @return array|false Result array or false on failure
     */
    private function process_markdown_file($markdown_file) {
        $filename = basename($markdown_file);
        $handle = str_replace('.md', '', $filename);

        $this->output_message('Processing markdown file: ' . $filename, 'info');

        // Read the markdown content
        $markdown_content = file_get_contents($markdown_file);

        if (!$markdown_content) {
            $this->output_message('Failed to read markdown file: ' . $filename, 'error');
            return false;
        }

        // Parse the markdown content
        $parsed_content = $this->parse_markdown($markdown_content);

        if (!$parsed_content) {
            $this->output_message('Failed to parse markdown file: ' . $filename, 'error');
            return false;
        }

        // Create a unique filename for the article
        $php_filename = sanitize_file_name($handle) . '.php';
        $file_path = $this->wp_content_dir . $php_filename;

        // Convert content to WordPress blocks format
        $blocks_content = $this->convert_to_blocks($parsed_content['content']);

        // Create post directly in WordPress
        $post_data = array(
            'post_title'    => $parsed_content['title'],
            'post_content'  => $blocks_content,
            'post_status'   => 'publish',
            'post_type'     => 'post',
            'post_excerpt'  => $parsed_content['excerpt']
        );

        // Insert the post
        $post_id = wp_insert_post($post_data);

        if (is_wp_error($post_id)) {
            $this->output_message('Failed to create post: ' . $post_id->get_error_message(), 'error');
            return false;
        }

        // Set categories - default to 'Recipes' for markdown files
        $category = get_term_by('name', 'Recipes', 'category');
        if (!$category) {
            // Create the category if it doesn't exist
            $category_id = wp_create_category('Recipes');
        } else {
            $category_id = $category->term_id;
        }
        wp_set_post_categories($post_id, array($category_id));

        // Set tags
        if (!empty($parsed_content['tags'])) {
            wp_set_post_tags($post_id, $parsed_content['tags']);
        }

        // Add meta data
        if (!empty($parsed_content['prep_time'])) {
            update_post_meta($post_id, '_prep_time', $parsed_content['prep_time']);
        }
        if (!empty($parsed_content['cook_time'])) {
            update_post_meta($post_id, '_cook_time', $parsed_content['cook_time']);
        }
        if (!empty($parsed_content['total_time'])) {
            update_post_meta($post_id, '_total_time', $parsed_content['total_time']);
        }
        if (!empty($parsed_content['servings'])) {
            update_post_meta($post_id, '_servings', $parsed_content['servings']);
        }
        if (!empty($parsed_content['calories'])) {
            update_post_meta($post_id, '_calories', $parsed_content['calories']);
        }
        if (!empty($parsed_content['difficulty'])) {
            update_post_meta($post_id, '_difficulty', $parsed_content['difficulty']);
        }

        // Also save the PHP file for reference
        $php_content = '<?php
return array(
    "title" => "' . esc_js($parsed_content['title']) . '",
    "post_type" => "post",
    "post_status" => "publish",
    "post_excerpt" => "' . esc_js($parsed_content['excerpt']) . '",
    "post_content" => "' . esc_js($blocks_content) . '",
    "categories" => array("Recipes"),
    "tags" => array("' . implode('", "', array_map('esc_js', $parsed_content['tags'])) . '"),
    "meta" => array(
        "_prep_time" => "' . esc_js($parsed_content['prep_time']) . '",
        "_cook_time" => "' . esc_js($parsed_content['cook_time']) . '",
        "_total_time" => "' . esc_js($parsed_content['total_time']) . '",
        "_servings" => "' . esc_js($parsed_content['servings']) . '",
        "_calories" => "' . esc_js($parsed_content['calories']) . '",
        "_difficulty" => "' . esc_js($parsed_content['difficulty']) . '"
    )
);';

        file_put_contents($file_path, $php_content);

        $this->output_message('Created post from markdown: ' . $parsed_content['title'], 'success');
        return array(
            'post_id' => $post_id,
            'title' => $parsed_content['title'],
            'handle' => $handle,
            'file' => $php_filename
        );
    }

    /**
     * Parse markdown content
     */
    private function parse_markdown($markdown) {
        $lines = explode("\n", trim($markdown));

        if (count($lines) < 5) {
            return false;
        }

        $result = array(
            'title' => '',
            'excerpt' => '',
            'content' => '',
            'prep_time' => '',
            'cook_time' => '',
            'total_time' => '',
            'servings' => '',
            'calories' => '',
            'difficulty' => '',
            'tags' => array()
        );

        // Get the title (first line starting with #)
        foreach ($lines as $i => $line) {
            if (strpos($line, '# ') === 0) {
                $result['title'] = trim(str_replace('# ', '', $line));
                break;
            }
        }

        // Get the description/excerpt
        $description_start = false;
        $description_lines = array();

        foreach ($lines as $line) {
            if (strpos($line, '## Description') === 0) {
                $description_start = true;
                continue;
            } else if ($description_start && strpos($line, '##') === 0) {
                $description_start = false;
                break;
            } else if ($description_start && !empty(trim($line))) {
                $description_lines[] = trim($line);
            }
        }

        if (!empty($description_lines)) {
            $result['excerpt'] = implode(' ', $description_lines);
        }

        // Get preparation time
        foreach ($lines as $line) {
            if (strpos($line, '- Prep:') !== false) {
                preg_match('/Prep: (\d+)/', $line, $matches);
                if (isset($matches[1])) {
                    $result['prep_time'] = $matches[1];
                }
            } else if (strpos($line, '- Cook:') !== false) {
                preg_match('/Cook: (\d+)/', $line, $matches);
                if (isset($matches[1])) {
                    $result['cook_time'] = $matches[1];
                }
            } else if (strpos($line, '- Total:') !== false) {
                preg_match('/Total: (\d+)/', $line, $matches);
                if (isset($matches[1])) {
                    $result['total_time'] = $matches[1];
                }
            } else if (strpos($line, '- Servings:') !== false) {
                preg_match('/Servings: (\d+)/', $line, $matches);
                if (isset($matches[1])) {
                    $result['servings'] = $matches[1];
                }
            }
        }

        // Get difficulty level
        foreach ($lines as $line) {
            if (strpos($line, '## Difficulty Level') === 0) {
                $next_line = next($lines);
                $result['difficulty'] = trim($next_line);
                break;
            }
        }

        // Get calories
        foreach ($lines as $line) {
            if (strpos($line, '- Calories:') !== false) {
                preg_match('/Calories: (\d+)/', $line, $matches);
                if (isset($matches[1])) {
                    $result['calories'] = $matches[1];
                }
            }
        }

        // Extract tags from the title
        $title_words = explode(' ', strtolower($result['title']));
        $possible_tags = array('salmon', 'halibut', 'cod', 'rockfish', 'scallops', 'crab', 'shrimp');

        foreach ($possible_tags as $tag) {
            if (in_array(strtolower($tag), $title_words)) {
                $result['tags'][] = ucfirst($tag);
            }
        }

        // Add 'Recipe' tag
        $result['tags'][] = 'Recipe';

        // Set content to the full markdown
        $result['content'] = $markdown;

        return $result;
    }

    /**
     * Convert content to WordPress blocks format
     */
    private function convert_to_blocks($content) {
        // Split content into paragraphs
        $paragraphs = preg_split('/\n\s*\n/', $content);

        $blocks_content = '';

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);

            if (empty($paragraph)) {
                continue;
            }

            // Check if paragraph is a heading
            if (preg_match('/^#+\s+(.+)$/m', $paragraph, $matches)) {
                $heading_level = substr_count(trim($matches[0]), '#');
                $heading_text = trim($matches[1]);

                $blocks_content .= "<!-- wp:heading {\"level\":" . $heading_level . "} -->\n";
                $blocks_content .= "<h" . $heading_level . ">" . $heading_text . "</h" . $heading_level . ">\n";
                $blocks_content .= "<!-- /wp:heading -->\n\n";
            }
            // Check if paragraph is a list
            else if (preg_match('/^[\-\*]\s+(.+)$/m', $paragraph)) {
                $list_items = preg_split('/\n/', $paragraph);
                $blocks_content .= "<!-- wp:list -->\n<ul>\n";

                foreach ($list_items as $item) {
                    if (preg_match('/^[\-\*]\s+(.+)$/', $item, $matches)) {
                        $blocks_content .= "<li>" . trim($matches[1]) . "</li>\n";
                    }
                }

                $blocks_content .= "</ul>\n<!-- /wp:list -->\n\n";
            }
            // Check if paragraph is a numbered list
            else if (preg_match('/^\d+\.\s+(.+)$/m', $paragraph)) {
                $list_items = preg_split('/\n/', $paragraph);
                $blocks_content .= "<!-- wp:list {\"ordered\":true} -->\n<ol>\n";

                foreach ($list_items as $item) {
                    if (preg_match('/^\d+\.\s+(.+)$/', $item, $matches)) {
                        $blocks_content .= "<li>" . trim($matches[1]) . "</li>\n";
                    }
                }

                $blocks_content .= "</ol>\n<!-- /wp:list -->\n\n";
            }
            // Regular paragraph
            else {
                $blocks_content .= "<!-- wp:paragraph -->\n";
                $blocks_content .= "<p>" . $paragraph . "</p>\n";
                $blocks_content .= "<!-- /wp:paragraph -->\n\n";
            }
        }

        return $blocks_content;
    }

    /**
     * Output a message
     */
    private function output_message($message, $type = 'info') {
        if (defined('WP_CLI')) {
            switch ($type) {
                case 'error':
                    WP_CLI::error($message);
                    break;
                case 'warning':
                case 'notice':
                    WP_CLI::warning($message);
                    break;
                case 'success':
                    WP_CLI::success($message);
                    break;
                case 'info':
                default:
                    WP_CLI::log($message);
                    break;
            }
        } else {
            $color = '';
            switch ($type) {
                case 'error':
                    $color = 'red';
                    break;
                case 'warning':
                case 'notice':
                    $color = 'orange';
                    break;
                case 'success':
                    $color = 'green';
                    break;
                case 'info':
                default:
                    $color = 'blue';
                    break;
            }

            echo '<div style="color: ' . $color . '; margin: 5px 0;">' . $message . '</div>';
        }
    }

    /**
     * Process HTML content
     *
     * @param string $content HTML content
     * @param int $category_id Category ID
     * @return array Result array
     */
    public function process_html_content($content, $category_id = 0) {
        // Convert HTML to blocks
        $blocks_content = $this->convert_to_blocks($content);

        // Extract title from h1 tag
        preg_match('/<h1[^>]*>(.*?)<\/h1>/i', $content, $matches);
        $title = isset($matches[1]) ? strip_tags($matches[1]) : 'Imported Blog Post';

        // Create post
        $post_id = wp_insert_post(array(
            'post_title'    => $title,
            'post_content'  => $blocks_content,
            'post_status'   => 'publish',
            'post_type'     => 'post',
            'post_excerpt'  => substr(strip_tags($content), 0, 150) . '...',
            'post_category' => array($category_id)
        ));

        if (is_wp_error($post_id)) {
            return array(
                'success' => false,
                'message' => $post_id->get_error_message()
            );
        }

        return array(
            'success' => true,
            'title' => $title,
            'post_id' => $post_id
        );
    }

    /**
     * Process Markdown content
     *
     * @param string $content Markdown content
     * @param int $category_id Category ID
     * @return array Result array
     */
    public function process_markdown_content($content, $category_id = 0) {
        // Parse the markdown content
        $parsed_content = $this->parse_markdown($content);

        if (!$parsed_content) {
            return array(
                'success' => false,
                'message' => 'Failed to parse markdown content'
            );
        }

        // Convert content to WordPress blocks format
        $blocks_content = $this->convert_to_blocks($parsed_content['content']);

        // Create post
        $post_id = wp_insert_post(array(
            'post_title'    => $parsed_content['title'],
            'post_content'  => $blocks_content,
            'post_status'   => 'publish',
            'post_type'     => 'post',
            'post_excerpt'  => $parsed_content['excerpt'],
            'post_category' => array($category_id)
        ));

        if (is_wp_error($post_id)) {
            return array(
                'success' => false,
                'message' => $post_id->get_error_message()
            );
        }

        // Add tags
        if (!empty($parsed_content['tags'])) {
            wp_set_post_tags($post_id, $parsed_content['tags']);
        }

        // Add meta data
        if (!empty($parsed_content['prep_time'])) {
            update_post_meta($post_id, '_prep_time', $parsed_content['prep_time']);
        }
        if (!empty($parsed_content['cook_time'])) {
            update_post_meta($post_id, '_cook_time', $parsed_content['cook_time']);
        }
        if (!empty($parsed_content['total_time'])) {
            update_post_meta($post_id, '_total_time', $parsed_content['total_time']);
        }
        if (!empty($parsed_content['servings'])) {
            update_post_meta($post_id, '_servings', $parsed_content['servings']);
        }
        if (!empty($parsed_content['calories'])) {
            update_post_meta($post_id, '_calories', $parsed_content['calories']);
        }
        if (!empty($parsed_content['difficulty'])) {
            update_post_meta($post_id, '_difficulty', $parsed_content['difficulty']);
        }

        return array(
            'success' => true,
            'title' => $parsed_content['title'],
            'post_id' => $post_id
        );
    }
}
