<?php
/**
 * Title: Recipe Card
 * Slug: pcs25/recipe-card
 * Categories: pcs25-recipe
 * Description: A card displaying recipe information for archives and lists
 */
?>

<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","right":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30"}},"border":{"radius":"8px","width":"1px"}},"borderColor":"contrast-3","backgroundColor":"base","className":"recipe-card","layout":{"type":"constrained"}} -->
<div class="wp-block-group recipe-card has-border-color has-contrast-3-border-color has-base-background-color has-background" style="border-width:1px;border-radius:8px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">

  <!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|30","left":"var:preset|spacing|30"}}}} -->
  <div class="wp-block-columns are-vertically-aligned-center">
    
    <!-- wp:column {"verticalAlignment":"center","width":"40%"} -->
    <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:40%">
      <!-- wp:post-featured-image {"isLink":true,"aspectRatio":"1","width":"100%","height":"","style":{"border":{"radius":"4px"}}} /-->
    </div>
    <!-- /wp:column -->
    
    <!-- wp:column {"verticalAlignment":"center","width":"60%"} -->
    <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:60%">
      <!-- wp:post-title {"isLink":true,"style":{"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|10"}},"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"large"} /-->
      
      <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|10","margin":{"top":"var:preset|spacing|10","bottom":"var:preset|spacing|20"}}},"layout":{"type":"flex","flexWrap":"wrap"}} -->
      <div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--10);margin-bottom:var(--wp--preset--spacing--20)">
        
        <!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"500"}},"textColor":"contrast-2","fontSize":"small"} -->
        <p class="has-contrast-2-color has-text-color has-small-font-size" style="font-style:normal;font-weight:500">Prep: <?php echo esc_html('{{prep_time}}'); ?> min</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"500"}},"textColor":"contrast-2","fontSize":"small"} -->
        <p class="has-contrast-2-color has-text-color has-small-font-size" style="font-style:normal;font-weight:500">• Cook: <?php echo esc_html('{{cook_time}}'); ?> min</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"500"}},"textColor":"contrast-2","fontSize":"small"} -->
        <p class="has-contrast-2-color has-text-color has-small-font-size" style="font-style:normal;font-weight:500">• Serves: <?php echo esc_html('{{servings}}'); ?></p>
        <!-- /wp:paragraph -->
      </div>
      <!-- /wp:group -->
      
      <!-- wp:post-excerpt {"moreText":"View Recipe →","showMoreOnNewLine":false,"style":{"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|20"}}},"fontSize":"small"} /-->
      
      <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|10"}},"layout":{"type":"flex","flexWrap":"wrap"}} -->
      <div class="wp-block-group">
        <!-- wp:post-terms {"term":"recipe_category","style":{"typography":{"fontStyle":"normal","fontWeight":"500"}},"fontSize":"x-small"} /-->
      </div>
      <!-- /wp:group -->
    </div>
    <!-- /wp:column -->
    
  </div>
  <!-- /wp:columns -->
  
</div>
<!-- /wp:group -->
