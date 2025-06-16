<?php
/**
 * Title: Recipe Social Share
 * Slug: pcs25/recipe-social-share
 * Categories: buttons
 * Description: Adds social sharing buttons for recipes
 */
?>

<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","right":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30"}},"border":{"radius":"8px","width":"1px"}},"borderColor":"contrast-3","backgroundColor":"base","className":"recipe-social-share","layout":{"type":"constrained"}} -->
<div class="wp-block-group recipe-social-share has-border-color has-contrast-3-border-color has-base-background-color has-background" style="border-width:1px;border-radius:8px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">
  
  <!-- wp:heading {"level":4,"style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"medium"} -->
  <h4 class="wp-block-heading has-medium-font-size" style="font-style:normal;font-weight:600">Share this Recipe</h4>
  <!-- /wp:heading -->
  
  <!-- wp:paragraph {"fontSize":"small"} -->
  <p class="has-small-font-size">Enjoy this recipe? Share it with your friends!</p>
  <!-- /wp:paragraph -->
  
  <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"left"}} -->
  <div class="wp-block-buttons">
    
    <!-- wp:button {"className":"is-style-outline social-share-facebook","style":{"spacing":{"padding":{"top":"10px","bottom":"10px","left":"20px","right":"20px"}}}} -->
    <div class="wp-block-button is-style-outline social-share-facebook"><a class="wp-block-button__link wp-element-button" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_url(get_permalink()); ?>" target="_blank" rel="noreferrer noopener" style="padding-top:10px;padding-right:20px;padding-bottom:10px;padding-left:20px">Facebook</a></div>
    <!-- /wp:button -->
    
    <!-- wp:button {"className":"is-style-outline social-share-twitter","style":{"spacing":{"padding":{"top":"10px","bottom":"10px","left":"20px","right":"20px"}}}} -->
    <div class="wp-block-button is-style-outline social-share-twitter"><a class="wp-block-button__link wp-element-button" href="https://twitter.com/intent/tweet?url=<?php echo esc_url(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank" rel="noreferrer noopener" style="padding-top:10px;padding-right:20px;padding-bottom:10px;padding-left:20px">Twitter</a></div>
    <!-- /wp:button -->
    
    <!-- wp:button {"className":"is-style-outline social-share-pinterest","style":{"spacing":{"padding":{"top":"10px","bottom":"10px","left":"20px","right":"20px"}}}} -->
    <div class="wp-block-button is-style-outline social-share-pinterest"><a class="wp-block-button__link wp-element-button" href="https://pinterest.com/pin/create/button/?url=<?php echo esc_url(get_permalink()); ?>&media=<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'full')); ?>&description=<?php echo urlencode(get_the_title()); ?>" target="_blank" rel="noreferrer noopener" style="padding-top:10px;padding-right:20px;padding-bottom:10px;padding-left:20px">Pinterest</a></div>
    <!-- /wp:button -->
    
    <!-- wp:button {"className":"is-style-outline social-share-email","style":{"spacing":{"padding":{"top":"10px","bottom":"10px","left":"20px","right":"20px"}}}} -->
    <div class="wp-block-button is-style-outline social-share-email"><a class="wp-block-button__link wp-element-button" href="mailto:?subject=<?php echo urlencode(get_the_title()); ?>&body=<?php echo urlencode(get_the_title() . ' - ' . get_permalink()); ?>" target="_blank" rel="noreferrer noopener" style="padding-top:10px;padding-right:20px;padding-bottom:10px;padding-left:20px">Email</a></div>
    <!-- /wp:button -->
    
  </div>
  <!-- /wp:buttons -->
</div>
<!-- /wp:group -->
