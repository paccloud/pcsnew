<?php
/**
 * The template for displaying single recipe posts
 *
 * @package PCS_Enhanced_Recipes
 */

// For block themes, we need a different approach to preserve the header and footer
$is_block_theme = function_exists('wp_is_block_theme') && wp_is_block_theme();

// Only include header/footer directly for classic themes
if (!$is_block_theme) {
    get_header();
}

// Start output buffering to capture our content
ob_start();

while ( have_posts() ) :
    the_post();
    
    // Get recipe meta
    $prep_time = get_post_meta( get_the_ID(), '_pcs_prep_time', true );
    $cook_time = get_post_meta( get_the_ID(), '_pcs_cook_time', true );
    $total_time = get_post_meta( get_the_ID(), '_pcs_total_time', true );
    $servings = get_post_meta( get_the_ID(), '_pcs_servings', true );
    $calories = get_post_meta( get_the_ID(), '_pcs_calories', true );
    $course = get_post_meta( get_the_ID(), '_pcs_course', true );
    $cuisine = get_post_meta( get_the_ID(), '_pcs_cuisine', true );
    $ingredients = get_post_meta( get_the_ID(), '_pcs_ingredients', true );
    $instructions = get_post_meta( get_the_ID(), '_pcs_instructions', true );

    // Get author
    $author = get_post_meta( get_the_ID(), '_pcs_author', true );
    if (empty($author)) {
        $author = get_the_author();
    }
    
    // Get the recipe content
    $recipe_content = ob_get_clean();
    
    // For block themes, wrap our content in the theme's template
    if ($is_block_theme) {
        // Get block template parts
        $template_part_header = get_block_template_html('header');
        $template_part_footer = get_block_template_html('footer');
        
        // Output with block theme template parts
        echo $template_part_header;
    }
    ?>
    
    <div class="pcs-recipe-container">
        <article id="post-<?php the_ID(); ?>" <?php post_class('pcs-recipe'); ?>>
            <header class="pcs-recipe-header">
                <h1 class="pcs-recipe-title"><?php the_title(); ?></h1>
                
                <?php if (!empty($author)) : ?>
                <div class="pcs-recipe-author">
                    <div class="pcs-author-avatar">
                        <?php echo get_avatar(get_the_author_meta('ID'), 40); ?>
                    </div>
                    <span><?php echo esc_html($author); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if (!empty(get_the_excerpt())) : ?>
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
                            <span class="pcs-star pcs-star-filled">★</span>
                            <span class="pcs-star pcs-star-filled">★</span>
                            <span class="pcs-star pcs-star-filled">★</span>
                            <span class="pcs-star pcs-star-filled">★</span>
                            <span class="pcs-star pcs-star-half">★</span>
                        </div>
                        <span class="pcs-recipe-rating-count">4.62 from 13 votes</span>
                    </div>
                    
                    <div class="pcs-recipe-actions">
                        <button class="pcs-recipe-action-button pcs-recipe-print-button">
                            <span class="pcs-recipe-action-icon">🖨️</span>
                            <?php _e( 'Print Recipe', 'pcs-enhanced-recipes' ); ?>
                        </button>
                        
                        <button class="pcs-recipe-action-button pcs-recipe-pin-button">
                            <span class="pcs-recipe-action-icon">📌</span>
                            <?php _e( 'Pin Recipe', 'pcs-enhanced-recipes' ); ?>
                        </button>
                        
                        <button class="pcs-recipe-action-button pcs-recipe-collection-button">
                            <span class="pcs-recipe-action-icon">📂</span>
                            <?php _e( 'Add to Collection', 'pcs-enhanced-recipes' ); ?>
                        </button>
                    </div>
                    
                    <div class="pcs-recipe-time-info">
                        <div class="pcs-recipe-time-block">
                            <div class="pcs-recipe-time-icon">⏱️</div>
                            <div class="pcs-recipe-time-content">
                                <div class="pcs-recipe-time-label"><?php _e( 'PREP TIME', 'pcs-enhanced-recipes' ); ?></div>
                                <div class="pcs-recipe-time-value"><?php echo esc_html( !empty($prep_time) ? $prep_time : '20' ); ?> <span class="pcs-recipe-time-unit"><?php _e( 'mins', 'pcs-enhanced-recipes' ); ?></span></div>
                            </div>
                        </div>
                        
                        <div class="pcs-recipe-time-block">
                            <div class="pcs-recipe-time-icon">🍳</div>
                            <div class="pcs-recipe-time-content">
                                <div class="pcs-recipe-time-label"><?php _e( 'COOK TIME', 'pcs-enhanced-recipes' ); ?></div>
                                <div class="pcs-recipe-time-value"><?php echo esc_html( !empty($cook_time) ? $cook_time : '15' ); ?> <span class="pcs-recipe-time-unit"><?php _e( 'mins', 'pcs-enhanced-recipes' ); ?></span></div>
                            </div>
                        </div>
                        
                        <div class="pcs-recipe-time-block">
                            <div class="pcs-recipe-time-icon">⏲️</div>
                            <div class="pcs-recipe-time-content">
                                <div class="pcs-recipe-time-label"><?php _e( 'TOTAL TIME', 'pcs-enhanced-recipes' ); ?></div>
                                <div class="pcs-recipe-time-value"><?php echo esc_html( !empty($total_time) ? $total_time : '35' ); ?> <span class="pcs-recipe-time-unit"><?php _e( 'mins', 'pcs-enhanced-recipes' ); ?></span></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pcs-recipe-details-info">
                        <div class="pcs-recipe-detail-block">
                            <div class="pcs-recipe-detail-label"><?php _e( 'COURSE', 'pcs-enhanced-recipes' ); ?></div>
                            <div class="pcs-recipe-detail-value">
                                <?php 
                                if (!empty($course)) {
                                    echo esc_html($course);
                                } else {
                                    echo '<a href="#">' . __('Dinner', 'pcs-enhanced-recipes') . '</a>, <a href="#">' . __('Pasta', 'pcs-enhanced-recipes') . '</a>';
                                }
                                ?>
                            </div>
                        </div>
                        
                        <div class="pcs-recipe-detail-block">
                            <div class="pcs-recipe-detail-label"><?php _e( 'CUISINE', 'pcs-enhanced-recipes' ); ?></div>
                            <div class="pcs-recipe-detail-value">
                                <?php 
                                if (!empty($cuisine)) {
                                    echo esc_html($cuisine);
                                } else {
                                    echo '<a href="#">' . __('Italian', 'pcs-enhanced-recipes') . '</a>';
                                }
                                ?>
                            </div>
                        </div>
                        
                        <div class="pcs-recipe-detail-block">
                            <div class="pcs-recipe-detail-label"><?php _e( 'SERVINGS', 'pcs-enhanced-recipes' ); ?></div>
                            <div class="pcs-recipe-detail-value">
                                <?php echo esc_html( !empty($servings) ? $servings : '4' ); ?> <span class="pcs-recipe-detail-unit"><?php _e( 'people', 'pcs-enhanced-recipes' ); ?></span>
                            </div>
                        </div>
                        
                        <div class="pcs-recipe-detail-block">
                            <div class="pcs-recipe-detail-label"><?php _e( 'CALORIES', 'pcs-enhanced-recipes' ); ?></div>
                            <div class="pcs-recipe-detail-value">
                                <?php echo esc_html( !empty($calories) ? $calories : '778' ); ?> <span class="pcs-recipe-detail-unit"><?php _e( 'kcal', 'pcs-enhanced-recipes' ); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="pcs-recipe-content">
                <div class="pcs-recipe-ingredients">
                    <h2><?php _e( 'INGREDIENTS', 'pcs-enhanced-recipes' ); ?></h2>
                    
                    <div class="pcs-recipe-unit-switcher">
                        <button class="pcs-unit-button pcs-unit-us active" data-unit="us"><?php _e( 'US Customary', 'pcs-enhanced-recipes' ); ?></button>
                        <button class="pcs-unit-button pcs-unit-metric" data-unit="metric"><?php _e( 'Metric', 'pcs-enhanced-recipes' ); ?></button>
                        <div class="pcs-recipe-servings-switcher">
                            <button class="pcs-serving-button pcs-serving-1x active" data-serving="1"><?php _e( '1x', 'pcs-enhanced-recipes' ); ?></button>
                            <button class="pcs-serving-button pcs-serving-2x" data-serving="2"><?php _e( '2x', 'pcs-enhanced-recipes' ); ?></button>
                            <button class="pcs-serving-button pcs-serving-3x" data-serving="3"><?php _e( '3x', 'pcs-enhanced-recipes' ); ?></button>
                        </div>
                    </div>
                    
                    <?php if (!empty($ingredients)) : ?>
                        <div class="pcs-ingredients-list">
                            <?php 
                            // Process ingredients to add data attributes for unit conversion
                            $processed_ingredients = $ingredients;
                            
                            // Look for ingredient amounts and add data attributes
                            // Pattern to find amounts like "2 cups" or "1/2 teaspoon"
                            $pattern = '/<li>(.*?)<\/li>/s';
                            preg_match_all($pattern, $ingredients, $matches);
                            
                            if (!empty($matches[0])) {
                                foreach ($matches[0] as $i => $ingredient_item) {
                                    $ingredient_text = $matches[1][$i];
                                    
                                    // Extract amount and unit
                                    $amount_pattern = '/^(\d+[\.\d]*|\d*\/\d+|\d+\s\d+\/\d+)\s*([a-zA-Z]+)?/';
                                    
                                    if (preg_match($amount_pattern, trim($ingredient_text), $amount_matches)) {
                                        $amount = $amount_matches[1];
                                        $unit = isset($amount_matches[2]) ? $amount_matches[2] : '';
                                        
                                        // Convert fractions to decimals for calculation
                                        $decimal_amount = $amount;
                                        if (strpos($amount, '/') !== false) {
                                            // Handle fractions like 1/2
                                            $parts = explode('/', $amount);
                                            if (count($parts) === 2) {
                                                $decimal_amount = $parts[0] / $parts[1];
                                            }
                                            
                                            // Handle mixed fractions like "1 1/2"
                                            if (strpos($amount, ' ') !== false) {
                                                $mixed_parts = explode(' ', $amount);
                                                $whole = $mixed_parts[0];
                                                $fraction_parts = explode('/', $mixed_parts[1]);
                                                $decimal_amount = $whole + ($fraction_parts[0] / $fraction_parts[1]);
                                            }
                                        }
                                        
                                        // Calculate metric conversion based on unit
                                        $metric_amount = $decimal_amount;
                                        $metric_unit = $unit;
                                        
                                        switch (strtolower($unit)) {
                                            case 'cup':
                                            case 'cups':
                                                $metric_amount = $decimal_amount * 236.6; // ml
                                                $metric_unit = 'ml';
                                                if ($metric_amount >= 1000) {
                                                    $metric_amount /= 1000;
                                                    $metric_unit = 'L';
                                                }
                                                break;
                                            case 'tbsp':
                                            case 'tablespoon':
                                            case 'tablespoons':
                                                $metric_amount = $decimal_amount * 15; // ml
                                                $metric_unit = 'ml';
                                                break;
                                            case 'tsp':
                                            case 'teaspoon':
                                            case 'teaspoons':
                                                $metric_amount = $decimal_amount * 5; // ml
                                                $metric_unit = 'ml';
                                                break;
                                            case 'oz':
                                            case 'ounce':
                                            case 'ounces':
                                                $metric_amount = $decimal_amount * 28.35; // g
                                                $metric_unit = 'g';
                                                break;
                                            case 'lb':
                                            case 'pound':
                                            case 'pounds':
                                                $metric_amount = $decimal_amount * 453.6; // g
                                                $metric_unit = 'g';
                                                if ($metric_amount >= 1000) {
                                                    $metric_amount /= 1000;
                                                    $metric_unit = 'kg';
                                                }
                                                break;
                                            case 'inch':
                                            case 'inches':
                                                $metric_amount = $decimal_amount * 2.54; // cm
                                                $metric_unit = 'cm';
                                                break;
                                        }
                                        
                                        // Format metric amount
                                        if ($metric_amount < 1) {
                                            $metric_amount = round($metric_amount, 2);
                                        } else if ($metric_amount < 10) {
                                            $metric_amount = round($metric_amount, 1);
                                        } else {
                                            $metric_amount = round($metric_amount);
                                        }
                                        
                                        // Create new ingredient HTML with data attributes
                                        $new_ingredient_start = '<span class="pcs-ingredient-amount" data-us="' . esc_attr($amount) . '" data-metric="' . esc_attr($metric_amount . ($metric_unit ? ' ' . $metric_unit : '')) . '">' . esc_html($amount . ($unit ? ' ' . $unit : '')) . '</span>';
                                        
                                        // Replace the amount in the original ingredient with the new span
                                        $new_ingredient_item = preg_replace('/^' . preg_quote($amount . ($unit ? ' ' . $unit : ''), '/') . '/', $new_ingredient_start, $ingredient_text, 1);
                                        
                                        // Replace the old ingredient item with the new one
                                        $processed_ingredients = str_replace($ingredient_text, $new_ingredient_item, $processed_ingredients);
                                    }
                                }
                            }
                            
                            echo wp_kses_post($processed_ingredients);
                            ?>
                        </div>
                    <?php else : ?>
                        <ul class="pcs-ingredients-list">
                            <li><span class="pcs-ingredient-amount" data-us="400" data-metric="400">400 g</span> tagliatelle</li>
                            <li><span class="pcs-ingredient-amount" data-us="3" data-metric="3">3 tbsp</span> olive oil extra virgin</li>
                            <li><span class="pcs-ingredient-amount" data-us="250" data-metric="250">250 g</span> flour</li>
                            <li class="pcs-ingredient-note">These ingredients are special</li>
                            <li><span class="pcs-ingredient-amount" data-us="400" data-metric="400">400 g</span> bacon</li>
                            <li><span class="pcs-ingredient-amount" data-us="2" data-metric="2">2</span> tomatoes</li>
                            <li><span class="pcs-ingredient-amount" data-us="3" data-metric="3">3</span> onions chopped</li>
                            <li>rosemary</li>
                        </ul>
                    <?php endif; ?>
                </div>
                
                <div class="pcs-recipe-instructions">
                    <h2><?php _e( 'INSTRUCTIONS', 'pcs-enhanced-recipes' ); ?></h2>
                    
                    <?php if (!empty($instructions)) : ?>
                        <div class="pcs-instructions-list">
                            <?php echo wp_kses_post($instructions); ?>
                        </div>
                    <?php else : ?>
                        <ol class="pcs-instructions-list">
                            <li>Cook pasta according to package directions.</li>
                            <li>Heat olive oil in a large skillet.</li>
                            <li>Add chopped bacon and cook until crispy.</li>
                            <li>Add chopped onions and tomatoes.</li>
                            <li>Cook for 5 minutes until vegetables are softened.</li>
                            <li>Drain pasta and add to the skillet.</li>
                            <li>Toss to combine and serve hot.</li>
                        </ol>
                    <?php endif; ?>
                </div>
                
                <div class="pcs-recipe-notes">
                    <h2><?php _e( 'NOTES', 'pcs-enhanced-recipes' ); ?></h2>
                    <div class="pcs-notes-content">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
            
            <footer class="pcs-recipe-footer">
                <?php
                // Display recipe categories
                $categories = get_the_terms( get_the_ID(), 'recipe_category' );
                if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) :
                ?>
                <div class="pcs-recipe-taxonomies">
                    <span class="pcs-taxonomy-label"><?php _e( 'Categories:', 'pcs-enhanced-recipes' ); ?></span>
                    <div class="pcs-taxonomy-terms pcs-recipe-categories">
                        <?php
                        $category_links = array();
                        foreach ( $categories as $category ) {
                            $category_links[] = '<a href="' . esc_url( get_term_link( $category ) ) . '" rel="category">' . esc_html( $category->name ) . '</a>';
                        }
                        echo implode( ', ', $category_links );
                        ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php
                // Display recipe tags
                $tags = get_the_terms( get_the_ID(), 'recipe_tag' );
                if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) :
                ?>
                <div class="pcs-recipe-taxonomies">
                    <span class="pcs-taxonomy-label"><?php _e( 'Tags:', 'pcs-enhanced-recipes' ); ?></span>
                    <div class="pcs-taxonomy-terms pcs-recipe-tags">
                        <?php
                        $tag_links = array();
                        foreach ( $tags as $tag ) {
                            $tag_links[] = '<a href="' . esc_url( get_term_link( $tag ) ) . '" rel="tag">' . esc_html( $tag->name ) . '</a>';
                        }
                        echo implode( ', ', $tag_links );
                        ?>
                    </div>
                </div>
                <?php endif; ?>
            </footer>
        </article>
        
        <?php
        // If comments are open or we have at least one comment, load up the comment template.
        if ( comments_open() || get_comments_number() ) :
            comments_template();
        endif;
        ?>
    </div>
    
    <?php
    // Close block theme template
    if ($is_block_theme) {
        echo $template_part_footer;
    } else {
        get_footer();
    }
endwhile;
