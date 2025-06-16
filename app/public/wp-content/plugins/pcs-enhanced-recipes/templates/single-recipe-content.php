<?php
/**
 * Recipe content template
 * Used by both classic and block themes
 * 
 * @package PCS_Enhanced_Recipes
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get recipe meta
$post_id = get_the_ID();
$prep_time = get_post_meta($post_id, '_pcs_prep_time', true);
$cook_time = get_post_meta($post_id, '_pcs_cook_time', true);
$total_time = get_post_meta($post_id, '_pcs_total_time', true);
$servings = get_post_meta($post_id, '_pcs_servings', true);
$calories = get_post_meta($post_id, '_pcs_calories', true);
$ingredients = get_post_meta($post_id, '_pcs_ingredients', true);
$instructions = get_post_meta($post_id, '_pcs_instructions', true);
$difficulty = get_post_meta($post_id, '_pcs_difficulty', true);
$course = get_post_meta($post_id, '_pcs_course', true);
$cuisine = get_post_meta($post_id, '_pcs_cuisine', true);

// Get featured image
$featured_image = '';
if (has_post_thumbnail()) {
    $featured_image = get_the_post_thumbnail(null, 'large', array('class' => 'pcs-recipe-featured-image'));
}
?>

<div class="pcs-recipe-container">
    <article id="post-<?php the_ID(); ?>" <?php post_class('pcs-recipe'); ?>>
        <header class="pcs-recipe-header">
            <h1 class="pcs-recipe-title"><?php the_title(); ?></h1>
            
            <?php if ($featured_image) : ?>
            <div class="pcs-recipe-image">
                <?php echo $featured_image; ?>
            </div>
            <?php endif; ?>

            <div class="pcs-recipe-actions">
                <button class="pcs-recipe-print" onclick="window.print();">
                    <span class="dashicons dashicons-printer"></span> Print Recipe
                </button>
                <button class="pcs-recipe-unit-toggle" data-current-unit="us">
                    <span class="dashicons dashicons-randomize"></span> Switch to Metric
                </button>
                <button class="pcs-recipe-save">
                    <span class="dashicons dashicons-heart"></span> Save Recipe
                </button>
            </div>
        </header>

        <div class="pcs-recipe-meta">
            <div class="pcs-recipe-times">
                <?php if ($prep_time) : ?>
                <div class="pcs-recipe-time">
                    <span class="dashicons dashicons-clock"></span>
                    <span class="pcs-recipe-time-label">Prep Time:</span>
                    <span class="pcs-recipe-time-value"><?php echo esc_html($prep_time); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($cook_time) : ?>
                <div class="pcs-recipe-time">
                    <span class="dashicons dashicons-clock"></span>
                    <span class="pcs-recipe-time-label">Cook Time:</span>
                    <span class="pcs-recipe-time-value"><?php echo esc_html($cook_time); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($total_time) : ?>
                <div class="pcs-recipe-time">
                    <span class="dashicons dashicons-clock"></span>
                    <span class="pcs-recipe-time-label">Total Time:</span>
                    <span class="pcs-recipe-time-value"><?php echo esc_html($total_time); ?></span>
                </div>
                <?php endif; ?>
            </div>

            <div class="pcs-recipe-details">
                <?php if ($servings) : ?>
                <div class="pcs-recipe-servings">
                    <span class="dashicons dashicons-groups"></span>
                    <span class="pcs-recipe-servings-label">Servings:</span>
                    <span class="pcs-recipe-servings-value"><?php echo esc_html($servings); ?></span>
                    <div class="pcs-recipe-servings-adjust">
                        <button data-multiply="0.5">½×</button>
                        <button data-multiply="1" class="active">1×</button>
                        <button data-multiply="2">2×</button>
                        <button data-multiply="3">3×</button>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($calories) : ?>
                <div class="pcs-recipe-calories">
                    <span class="dashicons dashicons-chart-bar"></span>
                    <span class="pcs-recipe-calories-label">Calories:</span>
                    <span class="pcs-recipe-calories-value"><?php echo esc_html($calories); ?></span>
                </div>
                <?php endif; ?>

                <?php if ($difficulty) : ?>
                <div class="pcs-recipe-difficulty">
                    <span class="dashicons dashicons-chart-line"></span>
                    <span class="pcs-recipe-difficulty-label">Difficulty:</span>
                    <span class="pcs-recipe-difficulty-value"><?php echo esc_html($difficulty); ?></span>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($course || $cuisine) : ?>
            <div class="pcs-recipe-categories">
                <?php if ($course) : ?>
                <div class="pcs-recipe-course">
                    <span class="dashicons dashicons-food"></span>
                    <span class="pcs-recipe-course-label">Course:</span>
                    <span class="pcs-recipe-course-value"><?php echo esc_html($course); ?></span>
                </div>
                <?php endif; ?>

                <?php if ($cuisine) : ?>
                <div class="pcs-recipe-cuisine">
                    <span class="dashicons dashicons-admin-site-alt3"></span>
                    <span class="pcs-recipe-cuisine-label">Cuisine:</span>
                    <span class="pcs-recipe-cuisine-value"><?php echo esc_html($cuisine); ?></span>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($ingredients) : ?>
        <div class="pcs-recipe-ingredients">
            <h2>Ingredients</h2>
            <div class="pcs-recipe-ingredients-content">
                <?php echo wp_kses_post(wpautop($ingredients)); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($instructions) : ?>
        <div class="pcs-recipe-instructions">
            <h2>Instructions</h2>
            <div class="pcs-recipe-instructions-content">
                <?php echo wp_kses_post(wpautop($instructions)); ?>
            </div>
        </div>
        <?php endif; ?>

        <footer class="pcs-recipe-footer">
            <div class="pcs-recipe-tags">
                <?php
                $tags = get_the_terms(get_the_ID(), 'recipe_tag');
                if ($tags && !is_wp_error($tags)) :
                    foreach ($tags as $tag) :
                        echo '<a href="' . esc_url(get_term_link($tag)) . '" class="pcs-recipe-tag">' . esc_html($tag->name) . '</a>';
                    endforeach;
                endif;
                ?>
            </div>
        </footer>
    </article>
</div>
