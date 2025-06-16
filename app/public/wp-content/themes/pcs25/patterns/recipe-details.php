<?php
/**
 * Title: Recipe Details
 * Slug: pcs25/recipe-details
 * Categories: text
 * Description: Displays recipe details such as prep time, cook time, and servings.
 */

// Get the current post ID
$post_id = get_the_ID();

// Get recipe meta data
$prep_time = get_post_meta($post_id, '_pcs25_prep_time', true);
$cook_time = get_post_meta($post_id, '_pcs25_cook_time', true);
$servings = get_post_meta($post_id, '_pcs25_servings', true);

// Calculate total time if both prep and cook time are set
$total_time = '';
if (!empty($prep_time) && !empty($cook_time)) {
    // Check if times are in minutes format
    if (is_numeric($prep_time) && is_numeric($cook_time)) {
        $total_time = intval($prep_time) + intval($cook_time);
        $total_time = $total_time . ' minutes';
    }
}
?>

<!-- wp:group {"style":{"border":{"radius":"8px","width":"1px"},"spacing":{"padding":{"top":"var:preset|spacing|30","right":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30"}}},"borderColor":"contrast-3","backgroundColor":"base","className":"recipe-details","layout":{"type":"constrained"}} -->
<div class="wp-block-group recipe-details has-border-color has-contrast-3-border-color has-base-background-color has-background" style="border-width:1px;border-radius:8px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">

  <!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"medium"} -->
  <h3 class="wp-block-heading has-medium-font-size" style="font-style:normal;font-weight:600">Recipe Details</h3>
  <!-- /wp:heading -->
  
  <!-- wp:columns {"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|20","left":"var:preset|spacing|20"}}}} -->
  <div class="wp-block-columns">
    
    <?php if (!empty($prep_time)) : ?>
    <!-- wp:column -->
    <div class="wp-block-column">
      <!-- wp:group {"className":"recipe-meta-item","layout":{"type":"constrained"}} -->
      <div class="wp-block-group recipe-meta-item">
        <!-- wp:paragraph {"style":{"typography":{"fontWeight":"600"}},"fontSize":"small"} -->
        <p class="has-small-font-size" style="font-weight:600">Prep Time</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"fontSize":"small"} -->
        <p class="has-small-font-size"><?php echo esc_html($prep_time); ?></p>
        <!-- /wp:paragraph -->
      </div>
      <!-- /wp:group -->
    </div>
    <!-- /wp:column -->
    <?php endif; ?>
    
    <?php if (!empty($cook_time)) : ?>
    <!-- wp:column -->
    <div class="wp-block-column">
      <!-- wp:group {"className":"recipe-meta-item","layout":{"type":"constrained"}} -->
      <div class="wp-block-group recipe-meta-item">
        <!-- wp:paragraph {"style":{"typography":{"fontWeight":"600"}},"fontSize":"small"} -->
        <p class="has-small-font-size" style="font-weight:600">Cook Time</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"fontSize":"small"} -->
        <p class="has-small-font-size"><?php echo esc_html($cook_time); ?></p>
        <!-- /wp:paragraph -->
      </div>
      <!-- /wp:group -->
    </div>
    <!-- /wp:column -->
    <?php endif; ?>
    
    <?php if (!empty($total_time)) : ?>
    <!-- wp:column -->
    <div class="wp-block-column">
      <!-- wp:group {"className":"recipe-meta-item","layout":{"type":"constrained"}} -->
      <div class="wp-block-group recipe-meta-item">
        <!-- wp:paragraph {"style":{"typography":{"fontWeight":"600"}},"fontSize":"small"} -->
        <p class="has-small-font-size" style="font-weight:600">Total Time</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"fontSize":"small"} -->
        <p class="has-small-font-size"><?php echo esc_html($total_time); ?></p>
        <!-- /wp:paragraph -->
      </div>
      <!-- /wp:group -->
    </div>
    <!-- /wp:column -->
    <?php endif; ?>
    
    <?php if (!empty($servings)) : ?>
    <!-- wp:column -->
    <div class="wp-block-column">
      <!-- wp:group {"className":"recipe-meta-item","layout":{"type":"constrained"}} -->
      <div class="wp-block-group recipe-meta-item">
        <!-- wp:paragraph {"style":{"typography":{"fontWeight":"600"}},"fontSize":"small"} -->
        <p class="has-small-font-size" style="font-weight:600">Servings</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"fontSize":"small"} -->
        <p class="has-small-font-size"><?php echo esc_html($servings); ?></p>
        <!-- /wp:paragraph -->
      </div>
      <!-- /wp:group -->
    </div>
    <!-- /wp:column -->
    <?php endif; ?>
    
  </div>
  <!-- /wp:columns -->
  
</div>
<!-- /wp:group -->
