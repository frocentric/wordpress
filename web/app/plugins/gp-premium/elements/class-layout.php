<?php
/**
 * This file handles our Layout Element functionality.
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

/**
 * The Layout Element.
 */
class GeneratePress_Site_Layout {
	/**
	 * Set our location variable.
	 *
	 * @since 1.7
	 * @var array
	 */
	protected $conditional = array();

	/**
	 * Set our exclusion variable.
	 *
	 * @since 1.7
	 * @var array
	 */
	protected $exclude = array();

	/**
	 * Set our user condition variable.
	 *
	 * @since 1.7
	 * @var array
	 */
	protected $users = array();

	/**
	 * Set up our other options.
	 *
	 * @since 1.7
	 * @deprecated 1.7.3
	 * @var array
	 */
	protected static $options = array();

	/**
	 * Sidebar layout.
	 *
	 * @since 1.7.3
	 * @var string
	 */
	protected $sidebar_layout = null;

	/**
	 * Footer widgets layout.
	 *
	 * @since 1.7.3
	 * @var int
	 */
	protected $footer_widgets = null;

	/**
	 * Whether to disable site header.
	 *
	 * @since 1.7.3
	 * @var boolean
	 */
	protected $disable_site_header = null;

	/**
	 * Whether to disable mobile header.
	 *
	 * @since 1.11.0
	 * @var boolean
	 */
	protected $disable_mobile_header = null;

	/**
	 * Whether to disable top bar.
	 *
	 * @since 1.7.3
	 * @var boolean
	 */
	protected $disable_top_bar = null;

	/**
	 * Whether to disable primary nav.
	 *
	 * @since 1.7.3
	 * @var boolean
	 */
	protected $disable_primary_navigation = null;

	/**
	 * Whether to disable secondary nav.
	 *
	 * @since 1.7.3
	 * @var boolean
	 */
	protected $disable_secondary_navigation = null;

	/**
	 * Whether to disable featured image.
	 *
	 * @since 1.7.3
	 * @var boolean
	 */
	protected $disable_featured_image = null;

	/**
	 * Whether to disable content title.
	 *
	 * @since 1.7.3
	 * @var boolean
	 */
	protected $disable_content_title = null;

	/**
	 * Whether to disable footer.
	 *
	 * @since 1.7.3
	 * @var boolean
	 */
	protected $disable_footer = null;

	/**
	 * Container type (full width etc..).
	 *
	 * @since 1.7.3
	 * @var string
	 */
	protected $content_area = null;

	/**
	 * Content width.
	 *
	 * @since 1.7.3
	 * @var int
	 */
	protected $content_width = null;

	/**
	 * Set our post ID.
	 *
	 * @since 1.7
	 * @var int
	 */
	protected static $post_id = '';

	/**
	 * Count how many instances are set.
	 *
	 * @since 1.7
	 * @deprecated 1.7.3
	 * @var int
	 */
	public static $instances = 0;

	/**
	 * Set our class and give our variables values.
	 *
	 * @since 1.7
	 *
	 * @param int $post_id The post ID of our element.
	 */
	public function __construct( $post_id ) {

		self::$post_id = $post_id;

		if ( get_post_meta( $post_id, '_generate_element_display_conditions', true ) ) {
			$this->conditional = get_post_meta( $post_id, '_generate_element_display_conditions', true );
		}

		if ( get_post_meta( $post_id, '_generate_element_exclude_conditions', true ) ) {
			$this->exclude = get_post_meta( $post_id, '_generate_element_exclude_conditions', true );
		}

		if ( get_post_meta( $post_id, '_generate_element_user_conditions', true ) ) {
			$this->users = get_post_meta( $post_id, '_generate_element_user_conditions', true );
		}

		if ( get_post_meta( $post_id, '_generate_sidebar_layout', true ) ) {
			$this->sidebar_layout = get_post_meta( $post_id, '_generate_sidebar_layout', true );
		}

		if ( get_post_meta( $post_id, '_generate_footer_widgets', true ) ) {
			$this->footer_widgets = get_post_meta( $post_id, '_generate_footer_widgets', true );
		}

		if ( get_post_meta( $post_id, '_generate_disable_site_header', true ) ) {
			$this->disable_site_header = get_post_meta( $post_id, '_generate_disable_site_header', true );
		}

		if ( get_post_meta( $post_id, '_generate_disable_mobile_header', true ) ) {
			$this->disable_mobile_header = get_post_meta( $post_id, '_generate_disable_mobile_header', true );
		}

		if ( get_post_meta( $post_id, '_generate_disable_top_bar', true ) ) {
			$this->disable_top_bar = get_post_meta( $post_id, '_generate_disable_top_bar', true );
		}

		if ( get_post_meta( $post_id, '_generate_disable_primary_navigation', true ) ) {
			$this->disable_primary_navigation = get_post_meta( $post_id, '_generate_disable_primary_navigation', true );
		}

		if ( get_post_meta( $post_id, '_generate_disable_secondary_navigation', true ) ) {
			$this->disable_secondary_navigation = get_post_meta( $post_id, '_generate_disable_secondary_navigation', true );
		}

		if ( get_post_meta( $post_id, '_generate_disable_featured_image', true ) ) {
			$this->disable_featured_image = get_post_meta( $post_id, '_generate_disable_featured_image', true );
		}

		if ( get_post_meta( $post_id, '_generate_disable_content_title', true ) ) {
			$this->disable_content_title = get_post_meta( $post_id, '_generate_disable_content_title', true );
		}

		if ( get_post_meta( $post_id, '_generate_disable_footer', true ) ) {
			$this->disable_footer = get_post_meta( $post_id, '_generate_disable_footer', true );
		}

		if ( get_post_meta( $post_id, '_generate_content_area', true ) ) {
			$this->content_area = get_post_meta( $post_id, '_generate_content_area', true );
		}

		if ( get_post_meta( $post_id, '_generate_content_width', true ) ) {
			$this->content_width = get_post_meta( $post_id, '_generate_content_width', true );
		}

		$display = apply_filters( 'generate_layout_element_display', GeneratePress_Conditions::show_data( $this->conditional, $this->exclude, $this->users ), $post_id );

		/**
		 * Simplify filter name.
		 *
		 * @since 2.0.0
		 */
		$display = apply_filters(
			'generate_element_display',
			$display,
			$post_id
		);

		if ( $display ) {
			global $generate_elements;

			$generate_elements[ $post_id ] = array(
				'is_block_element' => false,
				'type' => 'layout',
				'id' => $post_id,
			);

			add_action( 'wp', array( $this, 'after_setup' ), 100 );
			add_action( 'wp_enqueue_scripts', array( $this, 'build_css' ), 50 );

			if ( is_admin() ) {
				add_action( 'current_screen', array( $this, 'after_setup' ), 100 );
				add_action( 'enqueue_block_editor_assets', array( $this, 'build_css' ), 50 );
			}
		}

	}

	/**
	 * Return our available options.
	 *
	 * @since 1.7
	 * @deprecated 1.7.3
	 *
	 * @return array
	 */
	public static function get_options() {
		return false;
	}

	/**
	 * Initiate our set layout changes.
	 *
	 * @since 1.7
	 */
	public function after_setup() {
		if ( $this->sidebar_layout && ! self::post_meta_exists( '_generate-sidebar-layout-meta' ) ) {
			add_filter( 'generate_sidebar_layout', array( $this, 'filter_options' ) );
		}

		if ( $this->footer_widgets && ! self::post_meta_exists( '_generate-footer-widget-meta' ) ) {
			add_filter( 'generate_footer_widgets', array( $this, 'filter_options' ) );
		}

		if ( $this->disable_site_header ) {
			remove_action( 'generate_header', 'generate_construct_header' );
		}

		if ( $this->disable_mobile_header ) {
			remove_action( 'generate_after_header', 'generate_menu_plus_mobile_header', 5 );
		}

		if ( $this->disable_top_bar ) {
			remove_action( 'generate_before_header', 'generate_top_bar', 5 );
			remove_action( 'generate_inside_secondary_navigation', 'generate_secondary_nav_top_bar_widget', 5 );
		}

		if ( $this->disable_primary_navigation ) {
			add_filter( 'generate_navigation_location', '__return_false', 20 );
			add_filter( 'generate_disable_mobile_header_menu', '__return_true' );
		}

		if ( $this->disable_secondary_navigation ) {
			add_filter( 'has_nav_menu', array( $this, 'disable_secondary_navigation' ), 10, 2 );
		}

		if ( $this->disable_featured_image ) {
			remove_action( 'generate_after_entry_header', 'generate_blog_single_featured_image' );
			remove_action( 'generate_before_content', 'generate_blog_single_featured_image' );
			remove_action( 'generate_after_header', 'generate_blog_single_featured_image' );
			remove_action( 'generate_before_content', 'generate_featured_page_header_inside_single' );
			remove_action( 'generate_after_header', 'generate_featured_page_header' );
			add_filter( 'body_class', array( $this, 'remove_featured_image_class' ), 20 );
		}

		if ( $this->disable_content_title ) {
			add_filter( 'generate_show_title', '__return_false' );
		}

		if ( $this->disable_footer ) {
			remove_action( 'generate_footer', 'generate_construct_footer' );
			add_filter( 'generate_footer_widgets', '__return_null' );
		}

		if ( $this->content_area ) {
			add_filter( 'body_class', array( $this, 'body_classes' ) );
		}

		if ( is_admin() ) {
			if ( $this->sidebar_layout && ! self::admin_post_meta_exists( '_generate-sidebar-layout-meta' ) ) {
				add_filter( 'generate_block_editor_sidebar_layout', array( $this, 'filter_options' ) );
			}

			if ( $this->disable_content_title ) {
				add_filter( 'generate_block_editor_show_content_title', '__return_false' );
			}

			if ( $this->content_area ) {
				add_filter( 'generate_block_editor_content_area_type', array( $this, 'set_editor_content_area' ) );
			}

			if ( $this->content_width ) {
				add_filter( 'generate_block_editor_container_width', array( $this, 'set_editor_content_width' ) );
			}
		}
	}

	/**
	 * Build dynamic CSS
	 */
	public function build_css() {
		if ( $this->content_width ) {
			wp_add_inline_style( 'generate-style', '#content {max-width: ' . absint( $this->content_width ) . 'px;margin-left: auto;margin-right: auto;}' );
		}

		if ( is_admin() ) {
			$admin_css = '';

			if ( version_compare( generate_premium_get_theme_version(), '3.2.0-alpha.1', '<' ) ) {
				if ( 'full-width' === $this->content_area ) {
					$admin_css .= 'html .editor-styles-wrapper .wp-block{max-width: 100%}';
				}

				if ( $this->content_width ) {
					$admin_css .= 'html .editor-styles-wrapper .wp-block{max-width: ' . absint( $this->content_width ) . 'px;}';
				}
			}

			if ( $this->content_area ) {
				$admin_css .= '#generate-layout-page-builder-container {opacity: 0.5;pointer-events: none;}';
			}

			if ( $admin_css ) {
				wp_add_inline_style( 'wp-edit-blocks', $admin_css );
			}
		}
	}

	/**
	 * Check to see if our individual post metabox has a value.
	 *
	 * @since 1.7
	 *
	 * @param string $meta The meta key we're checking for.
	 * @return bool
	 */
	public static function post_meta_exists( $meta ) {
		if ( ! is_singular() ) {
			return false;
		}

		$value = get_post_meta( get_the_ID(), $meta, true );

		if ( '_generate-footer-widget-meta' === $meta && '0' === $value ) {
			$value = true;
		}

		if ( $value ) {
			return true;
		}

		return false;
	}

	/**
	 * Check to see if our individual post metabox has a value in the admin area.
	 *
	 * @since 1.11.0
	 *
	 * @param string $meta The meta key we're checking for.
	 * @return bool
	 */
	public static function admin_post_meta_exists( $meta ) {
		if ( is_admin() ) {
			$current_screen = get_current_screen();

			if ( isset( $current_screen->is_block_editor ) && $current_screen->is_block_editor ) {
				$post_id = false;

				if ( isset( $_GET['post'] ) ) { // phpcs:ignore -- No data processing happening here.
					$post_id = absint( $_GET['post'] ); // phpcs:ignore -- No data processing happening here.
				}

				if ( $post_id ) {
					$value = get_post_meta( $post_id, $meta, true );

					if ( '_generate-footer-widget-meta' === $meta && '0' === $value ) {
						$value = true;
					}

					if ( $value ) {
						return true;
					}
				} else {
					return false;
				}
			}
		}
	}

	/**
	 * Filter our filterable options.
	 *
	 * @since 1.7
	 */
	public function filter_options() {
		$filter = current_filter();

		if ( 'generate_sidebar_layout' === $filter || 'generate_block_editor_sidebar_layout' === $filter ) {
			return $this->sidebar_layout;
		}

		if ( 'generate_footer_widgets' === $filter ) {
			if ( 'no-widgets' === $this->footer_widgets ) {
				return 0;
			} else {
				return $this->footer_widgets;
			}
		}
	}

	/**
	 * Set the content area type in the editor.
	 *
	 * @param string $area Content area type.
	 */
	public function set_editor_content_area( $area ) {
		if ( 'full-width' === $this->content_area ) {
			$area = 'true';
		}

		if ( 'contained-container' === $this->content_area ) {
			$area = 'contained';
		}

		return $area;
	}

	/**
	 * Set the content width in the editor.
	 *
	 * @param string $width Content width with unit.
	 */
	public function set_editor_content_width( $width ) {
		if ( $this->content_width ) {
			$width = absint( $this->content_width ) . 'px';
		}

		return $width;
	}

	/**
	 * Disable the Secondary Navigation if set.
	 *
	 * @since 1.7
	 *
	 * @param bool   $has_nav_menu The existing value.
	 * @param string $location The location we're checking.
	 * @return bool
	 */
	public static function disable_secondary_navigation( $has_nav_menu, $location ) {
		if ( 'secondary' === $location ) {
			return false;
		}

		return $has_nav_menu;
	}

	/**
	 * Sets any necessary body classes.
	 *
	 * @since 1.7
	 *
	 * @param array $classes Our existing body classes.
	 * @return array Our new set of classes.
	 */
	public function body_classes( $classes ) {
		if ( 'full-width' === $this->content_area ) {
			$classes[] = 'full-width-content';
		}

		if ( 'contained' === $this->content_area ) {
			$classes[] = 'contained-content';
		}

		return $classes;
	}

	/**
	 * Remove the featured image class if it's disabled.
	 *
	 * @since 2.1.0
	 * @param array $classes The body classes.
	 */
	public function remove_featured_image_class( $classes ) {
		if ( is_singular() ) {
			$classes = generate_premium_remove_featured_image_class( $classes, $this->disable_featured_image );
		}

		return $classes;
	}

}
