<?php
/**
 * Twenty Twenty-Five functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

// Adds theme support for post formats.
if ( ! function_exists( 'twentytwentyfive_post_format_setup' ) ) :
	/**
	 * Adds theme support for post formats.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_post_format_setup() {
		add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );
	}
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_post_format_setup' );

// Auto-create Recipes page if it doesn't exist
if ( ! function_exists( 'twentytwentyfive_create_recipes_page' ) ) :
	/**
	 * Automatically creates a Recipes page if it doesn't exist.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_create_recipes_page() {
		// Check if Recipes page already exists
		$recipes_page = get_page_by_title('Recipes');
		
		if (!$recipes_page) {
			// Create the Recipes page
			$page_data = array(
				'post_title'    => 'Recipes',
				'post_name'     => 'recipes',
				'post_content'  => '<!-- wp:heading {"level":1} --><h1>Our Delicious Recipes</h1><!-- /wp:heading --><!-- wp:paragraph --><p>Discover our collection of mouth-watering seafood recipes featuring the finest Pacific Northwest catch.</p><!-- /wp:paragraph -->[display_recipes]',
				'post_status'   => 'publish',
				'post_type'     => 'page',
				'post_author'   => 1,
				'menu_order'    => 0
			);
			
			wp_insert_post($page_data);
		}
	}
endif;
add_action( 'admin_init', 'twentytwentyfive_create_recipes_page' );

// Shortcode to display recipes
if ( ! function_exists( 'twentytwentyfive_display_recipes_shortcode' ) ) :
	/**
	 * Shortcode to display all recipes in a grid layout.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return string HTML output of recipes
	 */
	function twentytwentyfive_display_recipes_shortcode($atts) {
		ob_start();
		
		// Query for recipe posts
		$recipes = new WP_Query(array(
			'post_type' => 'recipe',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'orderby' => 'title',
			'order' => 'ASC'
		));
		
		if ($recipes->have_posts()) {
			echo '<div class="recipes-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin: 2rem 0;">';
			
			while ($recipes->have_posts()) {
				$recipes->the_post();
				?>
				<div class="recipe-card" style="border: 1px solid #e0e0e0; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
					<?php if (has_post_thumbnail()) : ?>
						<div style="margin-bottom: 1rem;">
							<a href="<?php the_permalink(); ?>">
								<?php the_post_thumbnail('medium', array('style' => 'width: 100%; height: 200px; object-fit: cover; border-radius: 4px;')); ?>
							</a>
						</div>
					<?php endif; ?>
					
					<h3 style="margin-bottom: 0.75rem; font-size: 1.25rem;">
						<a href="<?php the_permalink(); ?>" style="text-decoration: none; color: inherit;">
							<?php the_title(); ?>
						</a>
					</h3>
					
					<?php if (get_the_excerpt()) : ?>
						<p style="margin-bottom: 1rem; color: #666; font-size: 0.9rem;">
							<?php echo wp_trim_words(get_the_excerpt(), 20); ?>
						</p>
					<?php endif; ?>
					
					<div style="display: flex; gap: 1rem; font-size: 0.85rem; color: #888; margin-bottom: 1rem;">
						<?php
						$prep_time = get_post_meta(get_the_ID(), '_recipe_prep_time', true);
						$cook_time = get_post_meta(get_the_ID(), '_recipe_cook_time', true);
						$servings = get_post_meta(get_the_ID(), '_recipe_servings', true);
						
						if ($prep_time) echo '<span>⏱️ Prep: ' . esc_html($prep_time) . '</span>';
						if ($cook_time) echo '<span>🔥 Cook: ' . esc_html($cook_time) . '</span>';
						if ($servings) echo '<span>👥 Serves: ' . esc_html($servings) . '</span>';
						?>
					</div>
					
					<a href="<?php the_permalink(); ?>" style="display: inline-block; padding: 0.5rem 1rem; background: #333; color: white; text-decoration: none; border-radius: 4px; font-size: 0.9rem;">
						View Recipe →
					</a>
				</div>
				<?php
			}
			
			echo '</div>';
			wp_reset_postdata();
		} else {
			echo '<div style="text-align: center; padding: 3rem;"><h3>No recipes found</h3><p>Check back soon for delicious seafood recipes!</p></div>';
		}
		
		return ob_get_clean();
	}
endif;
add_shortcode('display_recipes', 'twentytwentyfive_display_recipes_shortcode');

// Enqueues editor-style.css in the editors.
if ( ! function_exists( 'twentytwentyfive_editor_style' ) ) :
	/**
	 * Enqueues editor-style.css in the editors.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_editor_style() {
		add_editor_style( get_parent_theme_file_uri( 'assets/css/editor-style.css' ) );
	}
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_editor_style' );

// Enqueues style.css on the front.
if ( ! function_exists( 'twentytwentyfive_enqueue_styles' ) ) :
	/**
	 * Enqueues style.css on the front.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_enqueue_styles() {
		wp_enqueue_style(
			'twentytwentyfive-style',
			get_parent_theme_file_uri( 'style.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;
add_action( 'wp_enqueue_scripts', 'twentytwentyfive_enqueue_styles' );

// Registers custom block styles.
if ( ! function_exists( 'twentytwentyfive_block_styles' ) ) :
	/**
	 * Registers custom block styles.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_block_styles() {
		register_block_style(
			'core/list',
			array(
				'name'         => 'checkmark-list',
				'label'        => __( 'Checkmark', 'twentytwentyfive' ),
				'inline_style' => '
				ul.is-style-checkmark-list {
					list-style-type: "\2713";
				}

				ul.is-style-checkmark-list li {
					padding-inline-start: 1ch;
				}',
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_block_styles' );

// Registers pattern categories.
if ( ! function_exists( 'twentytwentyfive_pattern_categories' ) ) :
	/**
	 * Registers pattern categories.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_pattern_categories() {

		register_block_pattern_category(
			'twentytwentyfive_page',
			array(
				'label'       => __( 'Pages', 'twentytwentyfive' ),
				'description' => __( 'A collection of full page layouts.', 'twentytwentyfive' ),
			)
		);

		register_block_pattern_category(
			'twentytwentyfive_post-format',
			array(
				'label'       => __( 'Post formats', 'twentytwentyfive' ),
				'description' => __( 'A collection of post format patterns.', 'twentytwentyfive' ),
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_pattern_categories' );

// Registers block binding sources.
if ( ! function_exists( 'twentytwentyfive_register_block_bindings' ) ) :
	/**
	 * Registers the post format block binding source.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_register_block_bindings() {
		register_block_bindings_source(
			'twentytwentyfive/format',
			array(
				'label'              => _x( 'Post format name', 'Label for the block binding placeholder in the editor', 'twentytwentyfive' ),
				'get_value_callback' => 'twentytwentyfive_format_binding',
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_register_block_bindings' );

// Registers block binding callback function for the post format name.
if ( ! function_exists( 'twentytwentyfive_format_binding' ) ) :
	/**
	 * Callback function for the post format name block binding source.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return string|void Post format name, or nothing if the format is 'standard'.
	 */
	function twentytwentyfive_format_binding() {
		$post_format_slug = get_post_format();

		if ( $post_format_slug && 'standard' !== $post_format_slug ) {
			return get_post_format_string( $post_format_slug );
		}
	}
endif;
