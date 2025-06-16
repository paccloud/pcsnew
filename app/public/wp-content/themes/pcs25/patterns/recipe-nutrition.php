<?php
/**
 * Title: Recipe Nutrition
 * Slug: pcs25/recipe-nutrition
 * Categories: text
 * Description: Displays recipe nutrition information.
 */
?>

<!-- wp:group {"style":{"border":{"radius":"8px","width":"1px"},"spacing":{"padding":{"top":"var:preset|spacing|30","right":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30"}}},"borderColor":"contrast-3","backgroundColor":"base","className":"recipe-nutrition","layout":{"type":"constrained"}} -->
<div class="wp-block-group recipe-nutrition has-border-color has-contrast-3-border-color has-base-background-color has-background" style="border-width:1px;border-radius:8px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">

  <!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"medium"} -->
  <h3 class="wp-block-heading has-medium-font-size" style="font-style:normal;font-weight:600">Nutrition Information</h3>
  <!-- /wp:heading -->
  
  <!-- wp:paragraph {"fontSize":"small"} -->
  <p class="has-small-font-size">The nutrition information is an estimate per serving.</p>
  <!-- /wp:paragraph -->
  
  <?php
  // This section will be filled by the editor with nutrition information
  // We add a placeholder table to guide users
  ?>
  
  <!-- wp:table {"className":"is-style-stripes"} -->
  <figure class="wp-block-table is-style-stripes"><table><tbody>
  <tr><td>Calories</td><td>Per Serving</td></tr>
  <tr><td>Protein</td><td>g</td></tr>
  <tr><td>Carbohydrates</td><td>g</td></tr>
  <tr><td>Fat</td><td>g</td></tr>
  <tr><td>Fiber</td><td>g</td></tr>
  <tr><td>Sodium</td><td>mg</td></tr>
  </tbody></table></figure>
  <!-- /wp:table -->
  
</div>
<!-- /wp:group -->
