<?php
/**
 * Recipe content template part – pulls full markup from plugin but without header/footer shell.
 *
 * @package pcs25
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

while ( have_posts() ) :
    the_post();

    // Meta values
    $prep_time  = get_post_meta( get_the_ID(), '_pcs_prep_time', true );
    $cook_time  = get_post_meta( get_the_ID(), '_pcs_cook_time', true );
    $total_time = get_post_meta( get_the_ID(), '_pcs_total_time', true );
    $servings   = get_post_meta( get_the_ID(), '_pcs_servings', true );
    $calories   = get_post_meta( get_the_ID(), '_pcs_calories', true );
    $course     = get_post_meta( get_the_ID(), '_pcs_course', true );
    $cuisine    = get_post_meta( get_the_ID(), '_pcs_cuisine', true );
    $ingredients = get_post_meta( get_the_ID(), '_pcs_ingredients', true );
    $instructions = get_post_meta( get_the_ID(), '_pcs_instructions', true );

    $author = get_post_meta( get_the_ID(), '_pcs_author', true );
    if ( empty( $author ) ) {
        $author = get_the_author();
    }
    ?>

    <div class="pcs-recipe-container">
        <article id="post-<?php the_ID(); ?>" <?php post_class( 'pcs-recipe' ); ?>>
            <header class="pcs-recipe-header">
                <h1 class="pcs-recipe-title"><?php the_title(); ?></h1>
                <?php if ( ! empty( $author ) ) : ?>
                    <div class="pcs-recipe-author">
                        <div class="pcs-author-avatar">
                            <?php echo get_avatar( get_the_author_meta( 'ID' ), 40 ); ?>
                        </div>
                        <span><?php echo esc_html( $author ); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ( get_the_excerpt() ) : ?>
                    <div class="pcs-recipe-description">
                        <?php the_excerpt(); ?>
                    </div>
                <?php endif; ?>
            </header>

            <div class="pcs-recipe-content-wrapper">
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="pcs-recipe-featured-image">
                        <?php the_post_thumbnail( 'large' ); ?>
                    </div>
                <?php endif; ?>

                <div class="pcs-recipe-meta-wrapper">
                    <div class="pcs-recipe-rating">
                        <div class="pcs-recipe-rating-stars">
                            <span class="pcs-star pcs-star-filled">★</span><span class="pcs-star pcs-star-filled">★</span><span class="pcs-star pcs-star-filled">★</span><span class="pcs-star pcs-star-filled">★</span><span class="pcs-star pcs-star-half">★</span>
                        </div>
                        <span class="pcs-recipe-rating-count">4.62 from 13 votes</span>
                    </div>

                    <div class="pcs-recipe-time-info">
                        <?php if ( $prep_time ) : ?>
                            <span class="pcs-recipe-time-block"><strong><?php esc_html_e( 'Prep:', 'pcs-enhanced-recipes' ); ?></strong> <?php echo esc_html( $prep_time ); ?> <?php esc_html_e( 'mins', 'pcs-enhanced-recipes' ); ?></span>
                        <?php endif; ?>
                        <?php if ( $cook_time ) : ?>
                            <span class="pcs-recipe-time-block"><strong><?php esc_html_e( 'Cook:', 'pcs-enhanced-recipes' ); ?></strong> <?php echo esc_html( $cook_time ); ?> <?php esc_html_e( 'mins', 'pcs-enhanced-recipes' ); ?></span>
                        <?php endif; ?>
                        <?php if ( $total_time ) : ?>
                            <span class="pcs-recipe-time-block"><strong><?php esc_html_e( 'Total:', 'pcs-enhanced-recipes' ); ?></strong> <?php echo esc_html( $total_time ); ?> <?php esc_html_e( 'mins', 'pcs-enhanced-recipes' ); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="pcs-recipe-content">
                    <?php if ( $ingredients ) : ?>
                        <section class="pcs-recipe-ingredients">
                            <h2><?php esc_html_e( 'Ingredients', 'pcs-enhanced-recipes' ); ?></h2>
                            <?php echo wp_kses_post( wpautop( $ingredients ) ); ?>
                        </section>
                    <?php endif; ?>

                    <?php if ( $instructions ) : ?>
                        <section class="pcs-recipe-instructions">
                            <h2><?php esc_html_e( 'Instructions', 'pcs-enhanced-recipes' ); ?></h2>
                            <?php echo wp_kses_post( wpautop( $instructions ) ); ?>
                        </section>
                    <?php endif; ?>
                </div>
            </div>
        </article>

        <?php
        // Comments support
        if ( comments_open() || get_comments_number() ) {
            comments_template();
        }
        ?>
    </div>
    <?php
endwhile;
