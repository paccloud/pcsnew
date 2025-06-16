<?php
/**
 * Test Recipe Parser
 * 
 * A simple script to test the markdown parsing functionality of the recipe importer
 */

// Define the path to the test recipe file
$test_recipe_file = __DIR__ . '/test-recipe.md';

// Check if the file exists
if (!file_exists($test_recipe_file)) {
    echo "Test recipe file not found at: " . $test_recipe_file . "\n";
    exit;
}

// Read the file content
$content = file_get_contents($test_recipe_file);

if (!$content) {
    echo "Failed to read test recipe file.\n";
    exit;
}

echo "Successfully read test recipe file.\n";
echo "Content length: " . strlen($content) . " bytes\n\n";
echo "First 100 characters:\n" . substr($content, 0, 100) . "...\n\n";

// Parse the recipe
$recipe = parse_recipe($content);

if (!$recipe) {
    echo "Failed to parse recipe.\n";
    exit;
}

echo "Successfully parsed recipe:\n";
echo "Title: " . $recipe['title'] . "\n";
echo "Prep Time: " . $recipe['prep_time'] . "\n";
echo "Cook Time: " . $recipe['cook_time'] . "\n";
echo "Total Time: " . $recipe['total_time'] . "\n";
echo "Servings: " . $recipe['servings'] . "\n";
echo "Calories: " . $recipe['calories'] . "\n";
echo "Species: " . $recipe['species'] . "\n";
echo "Excerpt: " . $recipe['excerpt'] . "\n";
echo "Content length: " . strlen($recipe['content']) . " bytes\n";
echo "Content preview:\n" . substr($recipe['content'], 0, 200) . "...\n";

// Debug information
echo "\n\nDEBUG INFORMATION:\n";
echo "Number of lines in markdown: " . count(explode("\n", trim($content))) . "\n";

// Print out each line with line number for debugging
echo "\nMarkdown content with line numbers:\n";
$lines = explode("\n", trim($content));
foreach ($lines as $i => $line) {
    echo sprintf("%3d: %s\n", $i+1, $line);
}

/**
 * Parse a recipe from markdown format
 * This is a copy of the function from import-sample-recipes.php
 */
function parse_recipe($markdown) {
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
    $content_started = false;
    $ol_started = false;
    $notes_started = false;
    
    // Create excerpt from first paragraph after ingredients
    $excerpt = '';
    
    echo "\nDEBUG: Starting content generation...\n";
    
    foreach ($lines as $line) {
        echo "DEBUG: Processing line: '" . $line . "'\n";
        // Skip title and metadata lines
        if (strpos($line, '##') === 0 || strpos($line, '**Prep Time:**') !== false || 
            strpos($line, '**Cook Time:**') !== false || strpos($line, '**Total Time:**') !== false || 
            strpos($line, '**Servings:**') !== false || strpos($line, '**Calories:**') !== false) {
            echo "DEBUG: Skipping metadata line\n";
            continue;
        }
        
        // Handle section headers - using exact string comparison for debugging
        echo "DEBUG: Checking if '" . $line . "' matches '### Ingredients'\n";
        if ($line === '### Ingredients') {
            echo "DEBUG: Found Ingredients section\n";
            $in_ingredients = true;
            $in_instructions = false;
            $in_notes = false;
            $content .= "<!-- wp:heading {\"level\":3} -->\n<h3>Ingredients</h3>\n<!-- /wp:heading -->\n\n";
            continue;
        } 
        
        echo "DEBUG: Checking if '" . $line . "' matches '### Instructions'\n";
        if ($line === '### Instructions') {
            echo "DEBUG: Found Instructions section\n";
            $in_ingredients = false;
            $in_instructions = true;
            $in_notes = false;
            $content .= "<!-- wp:heading {\"level\":3} -->\n<h3>Instructions</h3>\n<!-- /wp:heading -->\n\n";
            continue;
        } 
        
        echo "DEBUG: Checking if '" . $line . "' matches '### Notes'\n";
        if ($line === '### Notes') {
            echo "DEBUG: Found Notes section\n";
            $in_ingredients = false;
            $in_instructions = false;
            $in_notes = true;
            $content .= "<!-- wp:heading {\"level\":3} -->\n<h3>Notes</h3>\n<!-- /wp:heading -->\n\n";
            continue;
        }
        
        // Handle ingredients list
        if ($in_ingredients && strpos($line, '- ') === 0) {
            echo "DEBUG: Found ingredient: " . str_replace('- ', '', $line) . "\n";
            if (empty($excerpt)) {
                $excerpt = str_replace('- ', '', $line);
            }
            
            if (empty($content_started)) {
                $content .= "<!-- wp:list -->\n<ul>\n";
                $content_started = true;
                echo "DEBUG: Started ingredients list\n";
            }
            
            $content .= "<li>" . str_replace('- ', '', $line) . "</li>\n";
        } else if ($in_ingredients && $content_started && trim($line) === '') {
            $content .= "</ul>\n<!-- /wp:list -->\n\n";
            $content_started = false;
            echo "DEBUG: Ended ingredients list\n";
        }
        
        // Handle instructions list
        if ($in_instructions && preg_match('/^\d+\./', $line)) {
            echo "DEBUG: Found instruction: " . preg_replace('/^\d+\.\s+/', '', $line) . "\n";
            if (empty($ol_started)) {
                $content .= "<!-- wp:list {\"ordered\":true} -->\n<ol>\n";
                $ol_started = true;
                echo "DEBUG: Started instructions list\n";
            }
            
            $content .= "<li>" . preg_replace('/^\d+\.\s+/', '', $line) . "</li>\n";
        } else if ($in_instructions && $ol_started && trim($line) === '') {
            $content .= "</ol>\n<!-- /wp:list -->\n\n";
            $ol_started = false;
            echo "DEBUG: Ended instructions list\n";
        }
        
        // Handle notes
        if ($in_notes && strpos($line, '- ') === 0) {
            echo "DEBUG: Found note: " . str_replace('- ', '', $line) . "\n";
            if (empty($notes_started)) {
                $content .= "<!-- wp:list -->\n<ul>\n";
                $notes_started = true;
                echo "DEBUG: Started notes list\n";
            }
            
            $content .= "<li>" . str_replace('- ', '', $line) . "</li>\n";
        } else if ($in_notes && $notes_started && trim($line) === '') {
            $content .= "</ul>\n<!-- /wp:list -->\n\n";
            $notes_started = false;
            echo "DEBUG: Ended notes list\n";
        } else if ($in_notes && !empty(trim($line)) && strpos($line, '- ') !== 0) {
            echo "DEBUG: Found note paragraph: " . $line . "\n";
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
    
    echo "DEBUG: Final content length: " . strlen($content) . " bytes\n";
    echo "DEBUG: Final excerpt: " . $excerpt . "\n";
    
    return $recipe;
}