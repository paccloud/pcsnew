<?php
require_once( __DIR__ . '/app/public/wp-load.php' );

/**
 * Create a new recipe post.
 */
function create_recipe($title, $content, $meta_input = array()) {
    $my_post = array(
        'post_title'    => $title,
        'post_content'  => $content,
        'post_status'   => 'publish',
        'post_author'   => 1,
        'post_type'     => 'recipe',
        'meta_input'    => $meta_input
    );

    $post_id = wp_insert_post( $my_post );

    if ($post_id) {
        return "Recipe '{$title}' created successfully! Post ID: " . $post_id;
    } else {
        return "Error creating recipe '{$title}'.";
    }
}

/**
 * Parse a recipe from markdown format
 */
function parse_recipe($markdown) {
    $lines = explode("\n", trim($markdown));
    
    if (count($lines) < 3) {
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
            $species_list = array('Sockeye Salmon', 'Sablefish', 'Cod', 'Halibut', 'Weathervane Scallops', 'Salmon');
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
            preg_match('/(\d+)/', $line, $matches);
            if (isset($matches[1])) {
                $recipe['prep_time'] = $matches[1];
            }
        } else if (strpos($line, '**Cook Time:**') !== false) {
            preg_match('/(\d+)/', $line, $matches);
            if (isset($matches[1])) {
                $recipe['cook_time'] = $matches[1];
            }
        } else if (strpos($line, '**Total Time:**') !== false) {
            preg_match('/(\d+)/', $line, $matches);
            if (isset($matches[1])) {
                $recipe['total_time'] = $matches[1];
            }
        } else if (strpos($line, '**Servings:**') !== false) {
            preg_match('/(\d+)/', $line, $matches);
            if (isset($matches[1])) {
                $recipe['servings'] = $matches[1];
            }
        } else if (strpos($line, '**Calories:**') !== false) {
            preg_match('/(\d+)/', $line, $matches);
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
    $content_started = false;
    $ol_started = false;
    $notes_started = false;
    
    $excerpt = '';
    
    foreach ($lines as $line) {
        if (strpos($line, '## ') === 0 || strpos($line, '**Prep Time:**') !== false || 
            strpos($line, '**Cook Time:**') !== false || strpos($line, '**Total Time:**') !== false || 
            strpos($line, '**Servings:**') !== false || strpos($line, '**Calories:**') !== false) {
            continue;
        }
        
        if (trim($line) === '### Ingredients') {
            $in_ingredients = true;
            $in_instructions = false;
            $in_notes = false;
            $content .= "<!-- wp:heading {\"level\":3} -->\n<h3>Ingredients</h3>\n<!-- /wp:heading -->\n\n";
            continue;
        } 
        
        if (trim($line) === '### Instructions') {
            $in_ingredients = false;
            $in_instructions = true;
            $in_notes = false;
            $content .= "<!-- wp:heading {\"level\":3} -->\n<h3>Instructions</h3>\n<!-- /wp:heading -->\n\n";
            continue;
        } 
        
        if (trim($line) === '### Notes') {
            $in_ingredients = false;
            $in_instructions = false;
            $in_notes = true;
            $content .= "<!-- wp:heading {\"level\":3} -->\n<h3>Notes</h3>\n<!-- /wp:heading -->\n\n";
            continue;
        }
        
        if ($in_ingredients) {
            if (strpos($line, '- ') === 0) {
                if (!$content_started) {
                    $content .= "<!-- wp:list -->\n<ul>\n";
                    $content_started = true;
                }
                $content .= "<li>" . str_replace('- ', '', $line) . "</li>\n";
            } else if ($content_started && trim($line) === '') {
                $content .= "</ul>\n<!-- /wp:list -->\n\n";
                $content_started = false;
            }
        }
        
        if ($in_instructions) {
            if (preg_match('/^\d+\./', $line)) {
                if (!$ol_started) {
                    $content .= "<!-- wp:list {\"ordered\":true} -->\n<ol>\n";
                    $ol_started = true;
                }
                $content .= "<li>" . preg_replace('/^\d+\.\s+/', '', $line) . "</li>\n";
            } else if ($ol_started && trim($line) === '') {
                $content .= "</ol>\n<!-- /wp:list -->\n\n";
                $ol_started = false;
            }
        }
        
        if ($in_notes) {
            if (strpos($line, '- ') === 0) {
                if (!$notes_started) {
                    $content .= "<!-- wp:list -->\n<ul>\n";
                    $notes_started = true;
                }
                $content .= "<li>" . str_replace('- ', '', $line) . "</li>\n";
            } else if ($notes_started && trim($line) === '') {
                $content .= "</ul>\n<!-- /wp:list -->\n\n";
                $notes_started = false;
            } else if (!empty(trim($line))) {
                $content .= "<!-- wp:paragraph -->\n<p>" . $line . "</p>\n<!-- /wp:paragraph -->\n\n";
            }
        }
    }
    
    if ($content_started) {
        $content .= "</ul>\n<!-- /wp:list -->\n\n";
    }
    if ($ol_started) {
        $content .= "</ol>\n<!-- /wp:list -->\n\n";
    }
    if ($notes_started) {
        $content .= "</ul>\n<!-- /wp:list -->\n\n";
    }
    
    $recipe['content'] = $content;
    
    // Create an excerpt from the first paragraph of notes if available
    if (isset($recipe['content'])) {
        preg_match('/<p>(.*?)<\/p>/', $recipe['content'], $matches);
        if (isset($matches[1])) {
            $recipe['excerpt'] = $matches[1];
        }
    }

    return $recipe;
}

// Read the sample recipes file
$markdown_content = file_get_contents(__DIR__ . '/sample-recipes.md');

// Split the content into individual recipes
$recipes_markdown = explode('---', $markdown_content);

foreach ($recipes_markdown as $recipe_markdown) {
    if (trim($recipe_markdown) === '') {
        continue;
    }

    $parsed_recipe = parse_recipe($recipe_markdown);

    if ($parsed_recipe) {
        $meta_input = array(
            '_pcs_prep_time' => $parsed_recipe['prep_time'],
            '_pcs_cook_time' => $parsed_recipe['cook_time'],
            '_pcs_total_time' => $parsed_recipe['total_time'],
            '_pcs_servings' => $parsed_recipe['servings'],
            '_pcs_calories' => $parsed_recipe['calories'],
            '_pcs_excerpt' => $parsed_recipe['excerpt'],
        );

        echo create_recipe($parsed_recipe['title'], $parsed_recipe['content'], $meta_input);
        echo "\n";
    }
}

?>
