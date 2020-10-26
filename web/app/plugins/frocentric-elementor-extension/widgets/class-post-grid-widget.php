<?php
/**
 * Elementor Post Grid Widget.
 *
 * Elementor widget that inserts a grid of posts into the page.
 *
 * @since 1.0.0
 */
class Post_Grid_Widget extends \Elementor\Widget_Base {

	/**
	 * Flags whether columns are enabled
	 *
	 * @var $enable_columns
	 */
	protected static bool $enable_columns = false;

	/**
	 * Get widget name.
	 *
	 * Retrieve oEmbed widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'post_grid';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve oEmbed widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Post Grid', 'frocentric-elementor-extension' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve oEmbed widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'fa fa-code';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the oEmbed widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'general' ];
	}

	/**
	 * Set column-based layout
	 */
	public function set_columns( $columns ) {
		return self::$enable_columns ? true : $columns;
	}

	/**
	 * Enable author output
	 */
	public function enable_author( $author ) {
		return self::$enable_columns ? true : $author;
	}

	/**
	 * Generates blog post classes
	 */
	public function generate_blog_post_classes( $classes ) {
		// Set our column classes
		if ( self::$enable_columns ) {
			$classes[] = 'generate-columns';
			$classes[] = 'tablet-grid-50';
			$classes[] = 'mobile-grid-100';
			$classes[] = 'grid-parent';

			// Set our featured column class
			if ( $wp_query->current_post == 0 && $paged == 1 && $settings['featured_column'] ) {
				if ( 50 == generate_blog_get_column_count() ) {
					$classes[] = 'grid-100';
				}

				if ( 33 == generate_blog_get_column_count() ) {
					$classes[] = 'grid-66';
				}

				if ( 25 == generate_blog_get_column_count() ) {
					$classes[] = 'grid-50';
				}

				if ( 20 == generate_blog_get_column_count() ) {
					$classes[] = 'grid-60';
				}
				$classes[] = 'featured-column';
			} else {
				$classes[] = 'grid-' . generate_blog_get_column_count();
			}
		}

		return $classes;
	}

	/**
	 * Prints the Post Image to post excerpts
	 */
	protected function generate_post_image_for_widget() {
		// If there's no featured image, return.
		if ( ! has_post_thumbnail() ) {
			return;
		}
	
		echo apply_filters( 'generate_featured_image_output', sprintf( // WPCS: XSS ok.
			'<div class="post-image">
				%3$s
				<a href="%1$s">
					%2$s
				</a>
			</div>',
			esc_url( get_permalink() ),
			get_the_post_thumbnail(
				get_the_ID(),
				apply_filters( 'generate_page_header_default_size', 'full' ),
				array(
					'itemprop' => 'image',
				)
			),
			apply_filters( 'generate_inside_featured_image_output', '' )
		) );
	}

	/**
	 * Prints the Post Image to post excerpts
	 */
	protected function generate_post_image() {
		// If there's no featured image, return.
		if ( ! has_post_thumbnail() ) {
			return;
		}

		echo apply_filters(
			'generate_featured_image_output',
			sprintf( // WPCS: XSS ok.
			'<div class="post-image">
				%3$s
				<a href="%1$s">
					%2$s
				</a>
			</div>',
			esc_url( get_permalink() ),
			get_the_post_thumbnail(
				get_the_ID(),
				apply_filters( 'generate_page_header_default_size', 'full' ),
				array(
					'itemprop' => 'image',
				)
			)
			,apply_filters( 'generate_inside_featured_image_output', '' )
		) );
	}

	/**
	 * Register oEmbed widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'plugin-name' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'url',
			[
				'label' => __( 'URL to embed', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'input_type' => 'url',
				'placeholder' => __( 'https://your-link.com', 'plugin-name' ),
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render oEmbed widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$the_query = new WP_Query(
			array(
				'ignore_sticky_posts' => true,
				'post_type'           => 'post',
				'posts_per_page'      => 3,
			)
		);
?>
<div id="primary" <?php generate_do_element_classes( 'content' ); ?>>
	<main id="main" <?php generate_do_element_classes( 'main' ); ?>>
			<?php
			if ( $the_query->have_posts() ) :
				$post_count = 0;
				$total      = $the_query->post_count;

				while ( $the_query->have_posts() ) : $the_query->the_post();

					if ( 0 === $post_count && $post_count !== $total ) {
						self::$enable_columns = true;
						do_action( 'generate_before_main_content' );
						self::$enable_columns = false;
					}

					/*
					 * Include the Post-Format-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					include 'fragments/article.php';

					if ( $post_count + 1 === $total ) {
						self::$enable_columns = true;
						do_action( 'generate_after_main_content' );
						self::$enable_columns = false;
					}
			
					$post_count++;

				endwhile;

				wp_reset_postdata();

			else :

				get_template_part( 'no-results', 'archive' );

			endif;
			?>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php
	}

}
