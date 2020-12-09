<?php
/**
 * The file that defines the Post Grid widget class
 *
 * @link       https://github.com/frocentric
 * @since      1.0.0
 *
 * @package    Frocentric
 * @subpackage Frocentric/includes
 */

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
	protected static $enable_columns = false;

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
		return array( 'general' );
	}

	/**
	 * Set column-based layout
	 *
	 * @param bool $columns Flags whether setting columns or not.
	 */
	public function set_columns( $columns ) {
		return self::$enable_columns ? true : $columns;
	}

	/**
	 * Enable author output
	 *
	 * @param bool $author  Flags author output.
	 */
	public function enable_author( $author ) {
		return self::$enable_columns ? true : $author;
	}

	/**
	 * Generates blog post classes
	 *
	 * @param string[] $classes  Array of CSS classes.
	 */
	public function generate_blog_post_classes( $classes ) {
		global $wp_query;
		$paged = get_query_var( 'paged' );
		$paged = $paged ? $paged : 1;

		// Get our options.
		$settings = wp_parse_args(
			get_option( 'generate_blog_settings', array() ),
			generate_blog_get_defaults()
		);

		// Set our column classes.
		if ( self::$enable_columns ) {
			$classes[] = 'generate-columns';
			$classes[] = 'tablet-grid-50';
			$classes[] = 'mobile-grid-100';
			$classes[] = 'grid-parent';

			// Set our featured column class.
			if ( 0 === $wp_query->current_post && 1 === $paged && $settings['featured_column'] ) {
				if ( 50 === generate_blog_get_column_count() ) {
					$classes[] = 'grid-100';
				}

				if ( 33 === generate_blog_get_column_count() ) {
					$classes[] = 'grid-66';
				}

				if ( 25 === generate_blog_get_column_count() ) {
					$classes[] = 'grid-50';
				}

				if ( 20 === generate_blog_get_column_count() ) {
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

    // phpcs:ignore
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
				),
				apply_filters( 'generate_inside_featured_image_output', '' )
			)
		);
	}

	/**
	 * Prints the Post Image to post excerpts
	 */
	protected function generate_post_image() {
		// If there's no featured image, return.
		if ( ! has_post_thumbnail() ) {
			return;
		}

    // phpcs:ignore
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
				),
				apply_filters( 'generate_inside_featured_image_output', '' )
			)
		);
	}

	/**
	 * Register oEmbed widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
  // phpcs:ignore
	protected function _register_controls() {

		$this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'Content', 'frocentric-elementor-extension' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'query_type',
			array(
				'label'   => __( 'Query Type', 'frocentric-elementor-extension' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => array(
					'latest'  => array(
						'title' => __( 'Latest', 'frocentric-elementor-extension' ),
						'icon'  => 'fa fa-calendar-alt',
					),
					'related' => array(
						'title' => __( 'Related', 'frocentric-elementor-extension' ),
						'icon'  => 'fa fa-link',
					),
				),
				'default' => 'latest',
				'toggle'  => true,
			)
		);

		$this->add_control(
			'page_size',
			array(
				'label'   => __( 'Post Count', 'frocentric-elementor-extension' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 3,
			)
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

		$settings   = $this->get_settings_for_display();
		$page_size  = $settings['page_size'];
		$query_type = $settings['query_type'];

		$the_query = 'related' === $query_type && is_singular( 'post' ) ? $this->get_related_posts( get_the_ID(), $page_size ) : new WP_Query(
			array(
				'ignore_sticky_posts' => true,
				'post_type'           => 'post',
				'posts_per_page'      => $page_size,
			)
		);
		?>
<div id="primary" <?php generate_do_element_classes( 'content' ); ?>>
	<main id="main" <?php generate_do_element_classes( 'main' ); ?>>
			<?php
			if ( $the_query->have_posts() ) :
				$post_count = 0;
				$total      = $the_query->post_count;

				while ( $the_query->have_posts() ) :
					$the_query->the_post();

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

	/**
	 * Get related posts query or query arguments.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param int      $post_id  Post identifier.
	 * @param int      $related_count  Count of related posts.
	 * @param object[] $args  Array of arguments.
	 */
	protected function get_related_posts( $post_id, $related_count, $args = array() ) {
		$args = wp_parse_args(
			(array) $args,
			array(
				'orderby' => 'rand',
				'return'  => 'query', // Valid values are: 'query' (WP_Query object), 'array' (the arguments array).
			)
		);

		$related_args = array(
			'post_type'      => get_post_type( $post_id ),
			'posts_per_page' => $related_count,
			'post_status'    => 'publish',
			'post__not_in'   => array( $post_id ),
			'orderby'        => $args['orderby'],
			'tax_query'      => array(), //phpcs:ignore
		);

		$post       = get_post( $post_id );
		$taxonomies = get_object_taxonomies( $post, 'names' );

		foreach ( $taxonomies as $taxonomy ) {
			$terms = get_the_terms( $post_id, $taxonomy );
			if ( empty( $terms ) ) {
				continue;
			}
			$term_list                   = wp_list_pluck( $terms, 'slug' );
			$related_args['tax_query'][] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => $term_list,
			);
		}

		if ( count( $related_args['tax_query'] ) > 1 ) {
			$related_args['tax_query']['relation'] = 'OR';
		}

		if ( 'query' === $args['return'] ) {
			return new WP_Query( $related_args );
		} else {
			return $related_args;
		}
	}

}
