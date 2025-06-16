<?php
/**
 * Recipe Importer
 * 
 * A utility script to import sample recipes into the WordPress database
 * 
 * Usage: Place this file in the plugins directory and access it via the browser
 * or run it via WP-CLI with: wp eval-file import-sample-recipes.php
 * 
 * @package PCS_Enhanced_Recipes
 */

// Exit if accessed directly outside of WordPress
if (!defined('ABSPATH') && !defined('WP_CLI')) {
    // Set up WordPress environment if running standalone
    $wp_load_path = dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';
    if (file_exists($wp_load_path)) {
        require_once($wp_load_path);
    } else {
        die('WordPress environment not found. Please run this script from within WordPress.');
    }
}

/**
 * Recipe Importer Class
 */
class PCS_Recipe_Importer {
    /**
     * Sample recipes file path
     */
    private $sample_recipes_file;
    
    /**
     * Constructor
     */
    public function __construct() {
        // Set the path to the sample recipes file
        $this->sample_recipes_file = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/sample-recipes.md';
        
        // Check if the file exists
        if (!file_exists($this->sample_recipes_file)) {
            $this->output_message('Sample recipes file not found at: ' . $this->sample_recipes_file, 'error');
            return;
        }
        
        // Import the recipes
        $this->import_recipes();
    }
    
    /**
     * Import recipes from the sample file
     */
    public function import_recipes() {
        // Read the file content
        $content = file_get_contents($this->sample_recipes_file);
        
        if (!$content) {
            $this->output_message('Failed to read sample recipes file.', 'error');
            return;
        }
        
        // Split the content by recipe (separated by ---)
        $recipes = explode('---', $content);
        
        $imported_count = 0;
        $skipped_count = 0;
        
        foreach ($recipes as $recipe) {
            if (empty(trim($recipe))) {
                continue;
            }
            
            // Parse the recipe
            $parsed_recipe = $this->parse_recipe($recipe);
            
            if (!$parsed_recipe) {
                $skipped_count++;
                continue;
            }
            
            // Check if the recipe already exists
            $existing = get_page_by_title($parsed_recipe['title'], OBJECT, 'recipe');
            
            if ($existing) {
                $this->output_message('Recipe "' . $parsed_recipe['title'] . '" already exists. Skipping.', 'notice');
                $skipped_count++;
                continue;
            }
            
            // Create the recipe post
            $post_id = wp_insert_post(array(
                'post_title'    => $parsed_recipe['title'],
                'post_content'  => $parsed_recipe['content'],
                'post_status'   => 'publish',
                'post_type'     => 'recipe',
                'post_excerpt'  => $parsed_recipe['excerpt'],
            ));
            
            if (is_wp_error($post_id)) {
                $this->output_message('Failed to create recipe "' . $parsed_recipe['title'] . '": ' . $post_id->get_error_message(), 'error');
                $skipped_count++;
                continue;
            }
            
            // Add recipe meta
            update_post_meta($post_id, '_pcs_prep_time', $parsed_recipe['prep_time']);
            update_post_meta($post_id, '_pcs_cook_time', $parsed_recipe['cook_time']);
            update_post_meta($post_id, '_pcs_total_time', $parsed_recipe['total_time']);
            update_post_meta($post_id, '_pcs_servings', $parsed_recipe['servings']);
            update_post_meta($post_id, '_pcs_calories', $parsed_recipe['calories']);
            
            // Add recipe categories and tags
            if (!empty($parsed_recipe['species'])) {
                $species_term = term_exists($parsed_recipe['species'], 'recipe_species');
                if (!$species_term) {
                    $species_term = wp_insert_term($parsed_recipe['species'], 'recipe_species');
                }
                
                if (!is_wp_error($species_term)) {
                    wp_set_object_terms($post_id, intval($species_term['term_id']), 'recipe_species');
                }
            }
            
            // Add to recipe category
            wp_set_object_terms($post_id, 'Seafood', 'recipe_category', true);
            
            $this->output_message('Imported recipe: ' . $parsed_recipe['title'], 'success');
            $imported_count++;
        }
        
        $this->output_message('Import complete. Imported: ' . $imported_count . ', Skipped: ' . $skipped_count, 'success');
    }
    
    /**
     * Parse a recipe from markdown format
     */
    private function parse_recipe($markdown) {
        $lines = explode("\n", trim($markdown));
        
        if (count($lines) < 5) {
            return false;
        }
        
        $recipe = array(
            'title' => '',
            'prep_time' => '',
            'cook_time' => '',
            'total_time' => '',
            'servings' => '',
            'calories' => '',
            'species' => '',
            'excerpt' => '',
            'content' => '',
        );
        
        // Get the title (first line starting with ##)
        foreach ($lines as $i => $line) {
            if (strpos($line, '## ') === 0) {
                $recipe['title'] = trim(str_replace('## ', '', $line));
                
                // Extract species from title
                $species_list = array('Sockeye Salmon', 'Sablefish', 'Cod', 'Halibut', 'Weathervane Scallops');
                foreach ($species_list as $species) {
                    if (stripos($recipe['title'], $species) !== false) {
                        $recipe['species'] = $species;
                        break;
                    }
                }
                
                break;
            }
        }
        
        // Get prep time, cook time, etc.
        foreach ($lines as $line) {
            if (strpos($line, '**Prep Time:**') !== false) {
                preg_match('/\*\*Prep Time:\*\* (\d+)/', $line, $matches);
                if (isset($matches[1])) {
                    $recipe['prep_time'] = $matches[1];
                }
            } else if (strpos($line, '**Cook Time:**') !== false) {
                preg_match('/\*\*Cook Time:\*\* (\d+)/', $line, $matches);
                if (isset($matches[1])) {
                    $recipe['cook_time'] = $matches[1];
                }
            } else if (strpos($line, '**Total Time:**') !== false) {
                preg_match('/\*\*Total Time:\*\* (\d+)/', $line, $matches);
                if (isset($matches[1])) {
                    $recipe['total_time'] = $matches[1];
                }
            } else if (strpos($line, '**Servings:**') !== false) {
                preg_match('/\*\*Servings:\*\* (\d+)/', $line, $matches);
                if (isset($matches[1])) {
                    $recipe['servings'] = $matches[1];
                }
            } else if (strpos($line, '**Calories:**') !== false) {
                preg_match('/\*\*Calories:\*\* (\d+)/', $line, $matches);
                if (isset($matches[1])) {
                    $recipe['calories'] = $matches[1];
                }
            }
        }
        
        // Create the content
        $content = '';
        $in_ingredients = false;
        $in_instructions = false;
        $in_notes = false;
        
        // Create excerpt from first paragraph after ingredients
        $excerpt = '';
        
        foreach ($lines as $line) {
            // Skip title and metadata lines
            if (strpos($line, '##') === 0 || strpos($line, '**Prep Time:**') !== false || 
                strpos($line, '**Cook Time:**') !== false || strpos($line, '**Total Time:**') !== false || 
                strpos($line, '**Servings:**') !== false || strpos($line, '**Calories:**') !== false) {
                continue;
            }
            
            // Handle section headers
            if (strpos($line, '### Ingredients') === 0) {
                $in_ingredients = true;
                $in_instructions = false;
                $in_notes = false;
                $content .= "<!-- wp:heading {\"level\":3} -->\n<h3>Ingredients</h3>\n<!-- /wp:heading -->\n\n";
                continue;
            } else if (strpos($line, '### Instructions') === 0) {
                $in_ingredients = false;
                $in_instructions = true;
                $in_notes = false;
                $content .= "<!-- wp:heading {\"level\":3} -->\n<h3>Instructions</h3>\n<!-- /wp:heading -->\n\n";
                continue;
            } else if (strpos($line, '### Notes') === 0) {
                $in_ingredients = false;
                $in_instructions = false;
                $in_notes = true;
                $content .= "<!-- wp:heading {\"level\":3} -->\n<h3>Notes</h3>\n<!-- /wp:heading -->\n\n";
                continue;
            } else if (strpos($line, '#### For the') === 0) {
                $content .= "<!-- wp:heading {\"level\":4} -->\n<h4>" . str_replace('#### ', '', $line) . "</h4>\n<!-- /wp:heading -->\n\n";
                continue;
            }
            
            // Handle ingredients list
            if ($in_ingredients && strpos($line, '- ') === 0) {
                if (empty($excerpt)) {
                    $excerpt = str_replace('- ', '', $line);
                }
                
                if (empty($content_started)) {
                    $content .= "<!-- wp:list -->\n<ul>\n";
                    $content_started = true;
                }
                
                $content .= "<li>" . str_replace('- ', '', $line) . "</li>\n";
            } else if ($in_ingredients && $content_started && trim($line) === '') {
                $content .= "</ul>\n<!-- /wp:list -->\n\n";
                $content_started = false;
            }
            
            // Handle instructions list
            if ($in_instructions && preg_match('/^\d+\./', $line)) {
                if (empty($ol_started)) {
                    $content .= "<!-- wp:list {\"ordered\":true} -->\n<ol>\n";
                    $ol_started = true;
                }
                
                $content .= "<li>" . preg_replace('/^\d+\.\s+/', '', $line) . "</li>\n";
            } else if ($in_instructions && $ol_started && trim($line) === '') {
                $content .= "</ol>\n<!-- /wp:list -->\n\n";
                $ol_started = false;
            }
            
            // Handle notes
            if ($in_notes && strpos($line, '- ') === 0) {
                if (empty($notes_started)) {
                    $content .= "<!-- wp:list -->\n<ul>\n";
                    $notes_started = true;
                }
                
                $content .= "<li>" . str_replace('- ', '', $line) . "</li>\n";
            } else if ($in_notes && $notes_started && trim($line) === '') {
                $content .= "</ul>\n<!-- /wp:list -->\n\n";
                $notes_started = false;
            } else if ($in_notes && !empty(trim($line)) && strpos($line, '- ') !== 0) {
                $content .= "<!-- wp:paragraph -->\n<p>" . $line . "</p>\n<!-- /wp:paragraph -->\n\n";
            }
        }
        
        // Close any open lists
        if (!empty($content_started)) {
            $content .= "</ul>\n<!-- /wp:list -->\n\n";
        }
        if (!empty($ol_started)) {
            $content .= "</ol>\n<!-- /wp:list -->\n\n";
        }
        if (!empty($notes_started)) {
            $content .= "</ul>\n<!-- /wp:list -->\n\n";
        }
        
        $recipe['content'] = $content;
        $recipe['excerpt'] = $excerpt;
        
        return $recipe;
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
}

// Check if taxonomy exists, if not create it
if (!taxonomy_exists('recipe_species')) {
    $labels = array(
        'name'                       => _x('Species', 'taxonomy general name', 'pcs-enhanced-recipes'),
        'singular_name'              => _x('Species', 'taxonomy singular name', 'pcs-enhanced-recipes'),
        'search_items'               => __('Search Species', 'pcs-enhanced-recipes'),
        'popular_items'              => __('Popular Species', 'pcs-enhanced-recipes'),
        'all_items'                  => __('All Species', 'pcs-enhanced-recipes'),
        'parent_item'                => null,
        'parent_item_colon'          => null,
        'edit_item'                  => __('Edit Species', 'pcs-enhanced-recipes'),
        'update_item'                => __('Update Species', 'pcs-enhanced-recipes'),
        'add_new_item'               => __('Add New Species', 'pcs-enhanced-recipes'),
        'new_item_name'              => __('New Species Name', 'pcs-enhanced-recipes'),
        'separate_items_with_commas' => __('Separate species with commas', 'pcs-enhanced-recipes'),
        'add_or_remove_items'        => __('Add or remove species', 'pcs-enhanced-recipes'),
        'choose_from_most_used'      => __('Choose from the most used species', 'pcs-enhanced-recipes'),
        'not_found'                  => __('No species found.', 'pcs-enhanced-recipes'),
        'menu_name'                  => __('Species', 'pcs-enhanced-recipes'),
    );
    
    $args = array(
        'hierarchical'      => false,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'recipe-species'),
        'show_in_rest'      => true
    );
    
    register_taxonomy('recipe_species', 'recipe', $args);
}

// Run the importer
$importer = new PCS_Recipe_Importer();

// If running in browser, add a link back to admin
if (!defined('WP_CLI')) {
    echo '<p><a href="' . admin_url('edit.php?post_type=recipe') . '">Return to Recipes</a></p>';
}
