<?php
/**
 * The template for displaying recipe taxonomy archives
 *
 * @package PCS_Enhanced_Recipes
 */

get_header();

// Get current taxonomy and term
$taxonomy = get_query_var( 'taxonomy' );
$term = get_term_by( 'slug', get_query_var( 'term' ), $taxonomy );
?>

<div class="pcs-recipe-archive-container">
    <header class="pcs-recipe-archive-header">
        <h1 class="pcs-recipe-archive-title">
            <?php
            if ( 'recipe_category' === $taxonomy ) {
                printf( __( 'Recipe Category: %s', 'pcs-enhanced-recipes' ), single_term_title( '', false ) );
            } elseif ( 'recipe_tag' === $taxonomy ) {
                printf( __( 'Recipe Tag: %s', 'pcs-enhanced-recipes' ), single_term_title( '', false ) );
            } else {
                single_term_title();
            }
            ?>
        </h1>
        
        <?php
        // Display term description if available
        $term_description = term_description();
        if ( ! empty( $term_description ) ) :
            printf( '<div class="pcs-recipe-archive-description">%s</div>', $term_description );
        endif;
        ?>
        
        <div class="pcs-recipe-filters">
            <?php
            // Display recipe category filter
            if ( 'recipe_tag' === $taxonomy ) {
                $categories = get_terms( array(
                    'taxonomy' => 'recipe_category',
                    'hide_empty' => true,
                ) );
                
                if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) :
                ?>
                <div class="pcs-recipe-filter">
                    <h3 class="pcs-recipe-filter-title"><?php _e( 'Categories', 'pcs-enhanced-recipes' ); ?></h3>
                    <ul class="pcs-recipe-filter-list">
                        <?php foreach ( $categories as $category ) : ?>
                        <li>
                            <a href="<?php echo esc_url( get_term_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif;
            }
            ?>
            
            <?php
            // Display recipe tag filter
            if ( 'recipe_category' === $taxonomy ) {
                $tags = get_terms( array(
                    'taxonomy' => 'recipe_tag',
                    'hide_empty' => true,
                ) );
                
                if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) :
                ?>
                <div class="pcs-recipe-filter">
                    <h3 class="pcs-recipe-filter-title"><?php _e( 'Tags', 'pcs-enhanced-recipes' ); ?></h3>
                    <ul class="pcs-recipe-filter-list">
                        <?php foreach ( $tags as $tag ) : ?>
                        <li>
                            <a href="<?php echo esc_url( get_term_link( $tag ) ); ?>"><?php echo esc_html( $tag->name ); ?></a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif;
            }
            ?>
        </div>
    </header>
    
    <?php if ( have_posts() ) : ?>
    
    <div class="pcs-recipe-grid pcs-recipe-grid-3-col">
        <?php
        // Start the Loop
        while ( have_posts() ) :
            the_post();
            
            // Get recipe meta
            $prep_time = get_post_meta( get_the_ID(), '_pcs_prep_time', true );
            $cook_time = get_post_meta( get_the_ID(), '_pcs_cook_time', true );
            ?>
            
            <article id="post-<?php the_ID(); ?>" <?php post_class( 'pcs-recipe-grid-item' ); ?>>
                <?php if ( has_post_thumbnail() ) : ?>
                <div class="pcs-recipe-grid-image">
                    <a href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail( 'medium' ); ?>
                    </a>
                </div>
                <?php endif; ?>
                
                <div class="pcs-recipe-grid-content">
                    <h2 class="pcs-recipe-grid-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>
                    
                    <div class="pcs-recipe-grid-meta">
                        <?php if ( ! empty( $prep_time ) || ! empty( $cook_time ) ) : ?>
                        <div class="pcs-recipe-grid-times">
                            <?php if ( ! empty( $prep_time ) ) : ?>
                            <span class="pcs-recipe-grid-prep-time">
                                <span class="dashicons dashicons-clock"></span>
                                <?php echo esc_html( $prep_time ); ?> <?php _e( 'min', 'pcs-enhanced-recipes' ); ?>
                            </span>
                            <?php endif; ?>
                            
                            <?php if ( ! empty( $cook_time ) ) : ?>
                            <span class="pcs-recipe-grid-cook-time">
                                <span class="dashicons dashicons-clock"></span>
                                <?php echo esc_html( $cook_time ); ?> <?php _e( 'min', 'pcs-enhanced-recipes' ); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="pcs-recipe-grid-excerpt">
                        <?php the_excerpt(); ?>
                    </div>
                    
                    <a href="<?php the_permalink(); ?>" class="pcs-recipe-grid-link">
                        <?php _e( 'View Recipe', 'pcs-enhanced-recipes' ); ?>
                    </a>
                </div>
            </article>
            
        <?php endwhile; ?>
    </div>
    
    <?php
    // Pagination
    the_posts_pagination( array(
        'prev_text' => __( '&laquo; Previous', 'pcs-enhanced-recipes' ),
        'next_text' => __( 'Next &raquo;', 'pcs-enhanced-recipes' ),
    ) );
    
    else :
    ?>
    
    <p class="pcs-recipe-no-results">
        <?php _e( 'No recipes found.', 'pcs-enhanced-recipes' ); ?>
    </p>
    
    <?php endif; ?>
</div>

<?php
get_footer();
