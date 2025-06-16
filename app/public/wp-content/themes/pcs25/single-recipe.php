<?php
/**
 * Single Recipe Template – theme wrapper only.
 * Loads full recipe markup from template-parts/recipe-content.php
 *
 * @package Pacific_Cloud_Seafood_25
 */

get_header();

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/recipe-content' );
	}
}

get_footer();

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="recipe-header">
			<h1 class="entry-title"><?php the_title(); ?></h1>
			
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="recipe-featured-image">
					<?php the_post_thumbnail( 'large' ); ?>
				</div>
			<?php endif; ?>
			
			<div class="recipe-meta">
				<?php 
				// Use correct meta field keys from plugin
				$prep_time = get_post_meta( get_the_ID(), '_pcs_prep_time', true );
				$cook_time = get_post_meta( get_the_ID(), '_pcs_cook_time', true );
				$total_time = get_post_meta( get_the_ID(), '_pcs_total_time', true );
				$servings = get_post_meta( get_the_ID(), '_pcs_servings', true );
				$calories = get_post_meta( get_the_ID(), '_pcs_calories', true );
				
				if ( $prep_time ) : ?>
					<div class="recipe-meta-item">
						<span class="recipe-meta-icon">⏱️</span>
						<span class="recipe-meta-label">Prep Time</span>
						<span class="recipe-meta-value"><?php echo esc_html( $prep_time ); ?> mins</span>
					</div>
				<?php endif; ?>
				
				<?php if ( $cook_time ) : ?>
					<div class="recipe-meta-item">
						<span class="recipe-meta-icon">🍳</span>
						<span class="recipe-meta-label">Cook Time</span>
						<span class="recipe-meta-value"><?php echo esc_html( $cook_time ); ?> mins</span>
					</div>
				<?php endif; ?>
				
				<?php if ( $total_time ) : ?>
					<div class="recipe-meta-item">
						<span class="recipe-meta-icon">⏲️</span>
						<span class="recipe-meta-label">Total Time</span>
						<span class="recipe-meta-value"><?php echo esc_html( $total_time ); ?> mins</span>
					</div>
				<?php endif; ?>
				
				<?php if ( $servings ) : ?>
					<div class="recipe-meta-item">
						<span class="recipe-meta-icon">👥</span>
						<span class="recipe-meta-label">Servings</span>
						<span class="recipe-meta-value"><?php echo esc_html( $servings ); ?></span>
					</div>
				<?php endif; ?>
				
				<?php if ( $calories ) : ?>
					<div class="recipe-meta-item">
						<span class="recipe-meta-icon">🔥</span>
						<span class="recipe-meta-label">Calories</span>
						<span class="recipe-meta-value"><?php echo esc_html( $calories ); ?> kcal</span>
					</div>
				<?php endif; ?>
			</div>
		</div>
		
		<div class="recipe-content">
			<?php the_content(); ?>
		</div>
		
		<?php 
		$ingredients = get_post_meta( get_the_ID(), '_pcs_ingredients', true );
		if ( $ingredients ) : ?>
			<div class="recipe-ingredients">
				<h3>Ingredients</h3>
				<?php echo wp_kses_post( $ingredients ); ?>
			</div>
		<?php endif; ?>
		
		<?php 
		$instructions = get_post_meta( get_the_ID(), '_pcs_instructions', true );
		if ( $instructions ) : ?>
			<div class="recipe-instructions">
				<h3>Instructions</h3>
				<?php echo wp_kses_post( $instructions ); ?>
			</div>
		<?php endif; ?>
		
		<?php
		// Display taxonomies
		$fish_types = get_the_terms( get_the_ID(), 'recipe_fish_type' );
		$meal_types = get_the_terms( get_the_ID(), 'recipe_meal_type' );
		$techniques = get_the_terms( get_the_ID(), 'recipe_technique' );
		$difficulty = get_the_terms( get_the_ID(), 'recipe_difficulty' );
		
		if ( $fish_types || $meal_types || $techniques || $difficulty ) : ?>
			<div class="recipe-taxonomies">
				<?php if ( $fish_types && ! is_wp_error( $fish_types ) ) : ?>
					<div class="recipe-taxonomy-group">
						<h4 class="recipe-taxonomies-title">Fish Type:</h4>
						<div class="recipe-taxonomies-list">
							<?php foreach ( $fish_types as $fish_type ) : ?>
								<a href="<?php echo esc_url( get_term_link( $fish_type ) ); ?>" class="recipe-taxonomy-item">
									<?php echo esc_html( $fish_type->name ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
				
				<?php if ( $meal_types && ! is_wp_error( $meal_types ) ) : ?>
					<div class="recipe-taxonomy-group">
						<h4 class="recipe-taxonomies-title">Meal Type:</h4>
						<div class="recipe-taxonomies-list">
							<?php foreach ( $meal_types as $meal_type ) : ?>
								<a href="<?php echo esc_url( get_term_link( $meal_type ) ); ?>" class="recipe-taxonomy-item">
									<?php echo esc_html( $meal_type->name ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
				
				<?php if ( $techniques && ! is_wp_error( $techniques ) ) : ?>
					<div class="recipe-taxonomy-group">
						<h4 class="recipe-taxonomies-title">Technique:</h4>
						<div class="recipe-taxonomies-list">
							<?php foreach ( $techniques as $technique ) : ?>
								<a href="<?php echo esc_url( get_term_link( $technique ) ); ?>" class="recipe-taxonomy-item">
									<?php echo esc_html( $technique->name ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
				
				<?php if ( $difficulty && ! is_wp_error( $difficulty ) ) : ?>
					<div class="recipe-taxonomy-group">
						<h4 class="recipe-taxonomies-title">Difficulty:</h4>
						<div class="recipe-taxonomies-list">
							<?php foreach ( $difficulty as $diff ) : ?>
								<a href="<?php echo esc_url( get_term_link( $diff ) ); ?>" class="recipe-taxonomy-item recipe-difficulty-<?php echo esc_attr( $diff->slug ); ?>">
									<?php echo esc_html( $diff->name ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php
endwhile;

get_footer();
