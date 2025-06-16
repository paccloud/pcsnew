<?php
/**
 * Title: Recipe Ingredients
 * Slug: pcs25/recipe-ingredients
 * Categories: text
 * Description: Displays recipe ingredients.
 */

// Get the current post ID
$post_id = get_the_ID();

// Get ingredients from post meta
$ingredients = get_post_meta($post_id, '_pcs25_ingredients', true);

// Skip if no ingredients found
if (empty($ingredients)) {
    return;
}

// Convert ingredients to array
$ingredients_array = explode("\n", $ingredients);
?>

<!-- wp:group {"style":{"border":{"radius":"8px","width":"1px"},"spacing":{"padding":{"top":"var:preset|spacing|30","right":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30"}}},"borderColor":"contrast-3","backgroundColor":"base","className":"recipe-ingredients","layout":{"type":"constrained"}} -->
<div class="wp-block-group recipe-ingredients has-border-color has-contrast-3-border-color has-base-background-color has-background" style="border-width:1px;border-radius:8px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">

  <!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"medium"} -->
  <h3 class="wp-block-heading has-medium-font-size" style="font-style:normal;font-weight:600">Ingredients</h3>
  <!-- /wp:heading -->
  
  <!-- wp:list -->
  <ul>
    <?php
    foreach ($ingredients_array as $ingredient) {
        $ingredient = trim($ingredient);
        if (!empty($ingredient)) {
            echo '<!-- wp:list-item --><li>' . esc_html($ingredient) . '</li><!-- /wp:list-item -->' . PHP_EOL;
        }
    }
    ?>
  </ul>
  <!-- /wp:list -->
  
</div>
<!-- /wp:group -->
