<?php
/**
 * The template for displaying single recipe posts.
 * This template is designed to be modern and visually appealing, similar to popular recipe plugins.
 *
 * @package PCS_Enhanced_Recipes
 */

get_header();

while (have_posts()) :
    the_post();

    // --- Recipe Data Fetching ---
    $recipe_id = get_the_ID();
    $author_name = get_the_author();
    $excerpt = get_the_excerpt();

    // Meta fields
    $prep_time = get_post_meta($recipe_id, '_pcs_prep_time', true);
    $cook_time = get_post_meta($recipe_id, '_pcs_cook_time', true);
    $total_time = get_post_meta($recipe_id, '_pcs_total_time', true);
    $servings = get_post_meta($recipe_id, '_pcs_servings', true);
    $calories = get_post_meta($recipe_id, '_pcs_calories', true);
    $course = get_the_terms($recipe_id, 'recipe_course');
    $cuisine = get_the_terms($recipe_id, 'recipe_cuisine');
    $ingredients = get_post_meta($recipe_id, '_pcs_ingredients', true);
    $instructions = get_post_meta($recipe_id, '_pcs_instructions', true);
    $notes = get_the_content();

    // --- Template Start ---
    ?>
    <div class="wp-block-group alignwide pcs-recipe-card-container">
        <article id="post-<?php echo esc_attr($recipe_id); ?>" <?php post_class('pcs-recipe-card'); ?>>

            <header class="pcs-recipe-card__header">
                <h1 class="pcs-recipe-card__title"><?php the_title(); ?></h1>
                <div class="pcs-recipe-card__author-info">
                    <div class="pcs-recipe-card__author-avatar">
                        <?php echo get_avatar(get_the_author_meta('ID'), 48); ?>
                    </div>
                    <span class="pcs-recipe-card__author-name"><?php echo esc_html($author_name); ?></span>
                </div>
                <?php if ($excerpt) : ?>
                    <p class="pcs-recipe-card__description"><?php echo esc_html($excerpt); ?></p>
                <?php endif; ?>
            </header>

            <div class="pcs-recipe-card__main-content">
                <div class="pcs-recipe-card__image-and-meta">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="pcs-recipe-card__featured-image">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="pcs-recipe-card__meta-container">
                        <div class="pcs-recipe-card__meta-item pcs-recipe-card__meta-item--rating">
                            <span class="pcs-recipe-card__meta-label"><?php _e('Rating', 'pcs-enhanced-recipes'); ?></span>
                            <div class="pcs-recipe-card__rating-stars">
                                <span class="pcs-star pcs-star-filled">★</span>
                                <span class="pcs-star pcs-star-filled">★</span>
                                <span class="pcs-star pcs-star-filled">★</span>
                                <span class="pcs-star pcs-star-filled">★</span>
                                <span class="pcs-star pcs-star-half">★</span>
                            </div>
                        </div>
                        <div class="pcs-recipe-card__meta-item">
                            <span class="pcs-recipe-card__meta-label"><?php _e('Prep Time', 'pcs-enhanced-recipes'); ?></span>
                            <span class="pcs-recipe-card__meta-value"><?php echo esc_html($prep_time ?: 'N/A'); ?></span>
                        </div>
                        <div class="pcs-recipe-card__meta-item">
                            <span class="pcs-recipe-card__meta-label"><?php _e('Cook Time', 'pcs-enhanced-recipes'); ?></span>
                            <span class="pcs-recipe-card__meta-value"><?php echo esc_html($cook_time ?: 'N/A'); ?></span>
                        </div>
                        <div class="pcs-recipe-card__meta-item">
                            <span class="pcs-recipe-card__meta-label"><?php _e('Total Time', 'pcs-enhanced-recipes'); ?></span>
                            <span class="pcs-recipe-card__meta-value"><?php echo esc_html($total_time ?: 'N/A'); ?></span>
                        </div>
                        <div class="pcs-recipe-card__meta-item">
                            <span class="pcs-recipe-card__meta-label"><?php _e('Servings', 'pcs-enhanced-recipes'); ?></span>
                            <span class="pcs-recipe-card__meta-value"><?php echo esc_html($servings ?: 'N/A'); ?></span>
                        </div>
                        <div class="pcs-recipe-card__meta-item">
                            <span class="pcs-recipe-card__meta-label"><?php _e('Calories', 'pcs-enhanced-recipes'); ?></span>
                            <span class="pcs-recipe-card__meta-value"><?php echo esc_html($calories ?: 'N/A'); ?></span>
                        </div>
                    </div>

                    <div class="pcs-recipe-card__actions">
                        <a href="#" class="pcs-recipe-card__button pcs-recipe-card__button--print"><?php _e('Print Recipe', 'pcs-enhanced-recipes'); ?></a>
                        <a href="#" class="pcs-recipe-card__button pcs-recipe-card__button--pin"><?php _e('Pin Recipe', 'pcs-enhanced-recipes'); ?></a>
                    </div>
                </div>

                <div class="pcs-recipe-card__body">
                    <div class="pcs-recipe-card__ingredients">
                        <h2 class="pcs-recipe-card__section-title"><?php _e('Ingredients', 'pcs-enhanced-recipes'); ?></h2>
                        <?php if (!empty($ingredients)) : ?>
                            <div class="pcs-ingredients-list">
                                <?php echo wp_kses_post(pcs_enhanced_recipes()->template->get_processed_ingredients_html($ingredients)); ?>
                            </div>
                        <?php else : ?>
                            <p><?php _e('No ingredients listed.', 'pcs-enhanced-recipes'); ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="pcs-recipe-card__instructions">
                        <h2 class="pcs-recipe-card__section-title"><?php _e('Instructions', 'pcs-enhanced-recipes'); ?></h2>
                        <?php if (!empty($instructions)) : ?>
                            <div class="pcs-instructions-list">
                                <?php echo wp_kses_post($instructions); ?>
                            </div>
                        <?php else : ?>
                            <p><?php _e('No instructions provided.', 'pcs-enhanced-recipes'); ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($notes)) : ?>
                        <div class="pcs-recipe-card__notes">
                            <h2 class="pcs-recipe-card__section-title"><?php _e('Notes', 'pcs-enhanced-recipes'); ?></h2>
                            <div class="pcs-notes-content">
                                <?php echo wp_kses_post(apply_filters('the_content', $notes)); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <footer class="pcs-recipe-card__footer">
                <?php if (!is_wp_error($course) && !empty($course)) : ?>
                    <div class="pcs-recipe-card__taxonomy">
                        <span class="pcs-recipe-card__taxonomy-label"><?php _e('Course:', 'pcs-enhanced-recipes'); ?></span>
                        <span class="pcs-recipe-card__taxonomy-terms"><?php echo get_the_term_list($recipe_id, 'recipe_course', '', ', '); ?></span>
                    </div>
                <?php endif; ?>
                <?php if (!is_wp_error($cuisine) && !empty($cuisine)) : ?>
                    <div class="pcs-recipe-card__taxonomy">
                        <span class="pcs-recipe-card__taxonomy-label"><?php _e('Cuisine:', 'pcs-enhanced-recipes'); ?></span>
                        <span class="pcs-recipe-card__taxonomy-terms"><?php echo get_the_term_list($recipe_id, 'recipe_cuisine', '', ', '); ?></span>
                    </div>
                <?php endif; ?>
            </footer>

        </article>
    </div>

    <?php
    // Display comments
    if (comments_open() || get_comments_number()) {
        comments_template();
    }

endwhile; // End of the loop.

get_footer();
