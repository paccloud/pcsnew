<?php
/**
 * Title: Recipe Instructions
 * Slug: pcs25/recipe-instructions
 * Categories: text
 * Description: Displays recipe instructions.
 */
?>

<!-- wp:group {"style":{"border":{"radius":"8px","width":"1px"},"spacing":{"padding":{"top":"var:preset|spacing|30","right":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30"}}},"borderColor":"contrast-3","backgroundColor":"base","className":"recipe-instructions","layout":{"type":"constrained"}} -->
<div class="wp-block-group recipe-instructions has-border-color has-contrast-3-border-color has-base-background-color has-background" style="border-width:1px;border-radius:8px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">

  <!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"medium"} -->
  <h3 class="wp-block-heading has-medium-font-size" style="font-style:normal;font-weight:600">Instructions</h3>
  <!-- /wp:heading -->
  
  <?php
  // This part will be filled by the editor with instructions in the content
  // We're using a placeholder paragraph to guide users
  ?>
  
  <!-- wp:paragraph {"placeholder":"Add your cooking instructions here, step by step. You can add numbered lists, paragraphs, or even images to illustrate the cooking process."} -->
  <p></p>
  <!-- /wp:paragraph -->
  
</div>
<!-- /wp:group -->
