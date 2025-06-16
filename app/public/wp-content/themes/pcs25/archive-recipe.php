<?php
/**
 * The template for displaying recipe archives
 *
 * @package PCS_Enhanced_Recipes
 */

if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {

    if ( function_exists( 'wp_template_part' ) ) {

    }
} else {

}
?>

<div class="pcs-recipe-archive-container">
    <header class="pcs-recipe-archive-header">
        <h1 class="pcs-recipe-archive-title"><?php post_type_archive_title(); ?></h1>
        
        <div class="pcs-recipe-search-container">
            <form id="pcs-recipe-filter-form" class="pcs-recipe-filter-form" method="get">
                <div class="pcs-filter-fields">
                    <div class="pcs-filter-field">
                        <label for="keyword"><?php _e('Search Recipes', 'pcs-enhanced-recipes'); ?></label>
                        <input type="text" name="keyword" id="keyword" placeholder="<?php _e('Enter keywords...', 'pcs-enhanced-recipes'); ?>" value="<?php echo isset($_GET['keyword']) ? esc_attr($_GET['keyword']) : ''; ?>">
                    </div>
                    
                    <div class="pcs-filter-field">
                        <label for="fish_type"><?php _e('Fish Type', 'pcs-enhanced-recipes'); ?></label>
                        <select name="fish_type[]" id="fish_type" multiple="multiple" class="pcs-select2">
                            <option value=""><?php _e('All Types', 'pcs-enhanced-recipes'); ?></option>
                            <?php 
                            $fish_types = get_terms(array('taxonomy' => 'recipe_fish_type', 'hide_empty' => true));
                            $selected_fish_types = isset($_GET['fish_type']) ? (array) $_GET['fish_type'] : array();
                            
                            foreach ($fish_types as $fish_type) : 
                                $selected = in_array($fish_type->slug, $selected_fish_types) ? 'selected="selected"' : '';
                            ?>
                                <option value="<?php echo esc_attr($fish_type->slug); ?>" <?php echo $selected; ?>><?php echo esc_html($fish_type->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="pcs-filter-field">
                        <label for="meal_type"><?php _e('Meal Type', 'pcs-enhanced-recipes'); ?></label>
                        <select name="meal_type[]" id="meal_type" multiple="multiple" class="pcs-select2">
                            <option value=""><?php _e('All Meal Types', 'pcs-enhanced-recipes'); ?></option>
                            <?php 
                            $meal_types = get_terms(array('taxonomy' => 'recipe_meal_type', 'hide_empty' => true));
                            $selected_meal_types = isset($_GET['meal_type']) ? (array) $_GET['meal_type'] : array();
                            
                            foreach ($meal_types as $meal_type) : 
                                $selected = in_array($meal_type->slug, $selected_meal_types) ? 'selected="selected"' : '';
                            ?>
                                <option value="<?php echo esc_attr($meal_type->slug); ?>" <?php echo $selected; ?>><?php echo esc_html($meal_type->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="pcs-filter-field">
                        <label for="technique"><?php _e('Cooking Technique', 'pcs-enhanced-recipes'); ?></label>
                        <select name="technique[]" id="technique" multiple="multiple" class="pcs-select2">
                            <option value=""><?php _e('All Techniques', 'pcs-enhanced-recipes'); ?></option>
                            <?php 
                            $techniques = get_terms(array('taxonomy' => 'recipe_technique', 'hide_empty' => true));
                            $selected_techniques = isset($_GET['technique']) ? (array) $_GET['technique'] : array();
                            
                            foreach ($techniques as $technique) : 
                                $selected = in_array($technique->slug, $selected_techniques) ? 'selected="selected"' : '';
                            ?>
                                <option value="<?php echo esc_attr($technique->slug); ?>" <?php echo $selected; ?>><?php echo esc_html($technique->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="pcs-filter-field">
                        <label for="difficulty"><?php _e('Difficulty', 'pcs-enhanced-recipes'); ?></label>
                        <select name="difficulty[]" id="difficulty" multiple="multiple" class="pcs-select2">
                            <option value=""><?php _e('Any Difficulty', 'pcs-enhanced-recipes'); ?></option>
                            <?php 
                            $difficulties = get_terms(array('taxonomy' => 'recipe_difficulty', 'hide_empty' => true));
                            $selected_difficulties = isset($_GET['difficulty']) ? (array) $_GET['difficulty'] : array();
                            
                            foreach ($difficulties as $difficulty) : 
                                $selected = in_array($difficulty->slug, $selected_difficulties) ? 'selected="selected"' : '';
                            ?>
                                <option value="<?php echo esc_attr($difficulty->slug); ?>" <?php echo $selected; ?>><?php echo esc_html($difficulty->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="pcs-filter-actions">
                    <button type="submit" class="pcs-filter-button"><?php _e('Search', 'pcs-enhanced-recipes'); ?></button>
                    <button type="reset" id="pcs-filter-reset" class="pcs-filter-reset"><?php _e('Reset', 'pcs-enhanced-recipes'); ?></button>
                </div>
            </form>
        </div>
    </header>

    <div class="pcs-recipe-grid">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <div class="pcs-recipe-card">
                    <a href="<?php the_permalink(); ?>" class="pcs-recipe-card-link">
                        <div class="pcs-recipe-card-image">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium_large'); ?>
                            <?php else : ?>
                                <img src="<?php echo plugin_dir_url(dirname(__FILE__)) . 'assets/images/recipe-placeholder.jpg'; ?>" alt="<?php the_title_attribute(); ?>" />
                            <?php endif; ?>
                        </div>
                        
                        <div class="pcs-recipe-card-content">
                            <h2 class="pcs-recipe-card-title"><?php the_title(); ?></h2>
                            
                            <div class="pcs-recipe-card-meta">
                                <?php 
                                $prep_time = get_post_meta(get_the_ID(), '_pcs_prep_time', true);
                                $cook_time = get_post_meta(get_the_ID(), '_pcs_cook_time', true);
                                $total_time = get_post_meta(get_the_ID(), '_pcs_total_time', true);
                                $servings = get_post_meta(get_the_ID(), '_pcs_servings', true);
                                
                                if ($total_time) : ?>
                                    <span class="pcs-recipe-time">
                                        <i class="pcs-icon pcs-icon-clock"></i> <?php echo esc_html($total_time); ?> <?php _e('mins', 'pcs-enhanced-recipes'); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if ($servings) : ?>
                                    <span class="pcs-recipe-servings">
                                        <i class="pcs-icon pcs-icon-users"></i> <?php echo esc_html($servings); ?> <?php _e('servings', 'pcs-enhanced-recipes'); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php 
                                // Get difficulty level
                                $difficulty_terms = get_the_terms(get_the_ID(), 'recipe_difficulty');
                                if ($difficulty_terms && !is_wp_error($difficulty_terms)) :
                                    $difficulty = $difficulty_terms[0]->name;
                                ?>
                                    <span class="pcs-recipe-difficulty">
                                        <i class="pcs-icon pcs-icon-difficulty"></i> <?php echo esc_html($difficulty); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="pcs-recipe-card-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            
                            <div class="pcs-recipe-card-tags">
                                <?php 
                                $fish_types = get_the_terms(get_the_ID(), 'recipe_fish_type');
                                if ($fish_types && !is_wp_error($fish_types)) :
                                    foreach ($fish_types as $fish_type) :
                                ?>
                                    <span class="pcs-recipe-tag pcs-fish-type"><?php echo esc_html($fish_type->name); ?></span>
                                <?php 
                                    endforeach;
                                endif;
                                
                                $meal_types = get_the_terms(get_the_ID(), 'recipe_meal_type');
                                if ($meal_types && !is_wp_error($meal_types)) :
                                    foreach ($meal_types as $meal_type) :
                                ?>
                                    <span class="pcs-recipe-tag pcs-meal-type"><?php echo esc_html($meal_type->name); ?></span>
                                <?php 
                                    endforeach;
                                endif;
                                ?>
                            </div>
                            
                            <span class="pcs-recipe-card-button"><?php _e('View Recipe', 'pcs-enhanced-recipes'); ?></span>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
            
            <div class="pcs-recipe-pagination">
                <?php 
                the_posts_pagination(array(
                    'mid_size' => 2,
                    'prev_text' => __('← Previous', 'pcs-enhanced-recipes'),
                    'next_text' => __('Next →', 'pcs-enhanced-recipes'),
                )); 
                ?>
            </div>
        <?php else : ?>
            <div class="pcs-no-recipes">
                <p><?php _e('No recipes found. Please try a different search.', 'pcs-enhanced-recipes'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
    if ( function_exists( 'wp_template_part' ) ) {

    }
} else {

}
