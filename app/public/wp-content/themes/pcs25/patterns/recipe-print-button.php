<?php
/**
 * Title: Recipe Print Button
 * Slug: pcs25/recipe-print-button
 * Categories: buttons
 * Description: Adds a print button for recipes
 */
?>

<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|20","right":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|20"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--20)">
  
  <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"right"}} -->
  <div class="wp-block-buttons">
    
    <!-- wp:button {"className":"recipe-print-button is-style-outline","style":{"spacing":{"padding":{"top":"8px","right":"16px","bottom":"8px","left":"16px"}}}} -->
    <div class="wp-block-button recipe-print-button is-style-outline">
      <a class="wp-block-button__link wp-element-button" href="#" onclick="window.print(); return false;" style="padding-top:8px;padding-right:16px;padding-bottom:8px;padding-left:16px">
        <!-- wp:html -->
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="6 9 6 2 18 2 18 9"></polyline>
          <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
          <rect x="6" y="14" width="12" height="8"></rect>
        </svg>
        <!-- /wp:html -->
        Print Recipe
      </a>
    </div>
    <!-- /wp:button -->
    
  </div>
  <!-- /wp:buttons -->
  
</div>
<!-- /wp:group -->
