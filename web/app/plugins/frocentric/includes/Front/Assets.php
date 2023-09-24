<?php
/**
 * Register frontend assets.
 *
 * @class       FrontAssets
 * @version     1.0.0
 * @package     Frocentric/Classes/
 */

namespace Frocentric\Front;

use Frocentric\Assets as AssetsMain;
use Frocentric\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Frontend assets class
 */
final class Assets {

	/**
	 * Add scripts for the frontend.
	 *
	 * @param  array $scripts Frontend scripts.
	 * @return array<string,array>
	 */
	public static function add_scripts( $scripts ) {

		$scripts['frocentric-general'] = array(
			'src'  => AssetsMain::localize_asset( 'source/js/frontend/frocentric.js' ),
			'data' => array(
				'ajaxurl' => Utils::ajax_url(),
				'homeurl' => home_url(),
			),
		);

		return $scripts;
	}

	/**
	 * Add styles for the frontend.
	 *
	 * @param array $styles Frontend styles.
	 * @return array<string,array>
	 */
	public static function add_styles( $styles ) {

		$styles['frocentric-general'] = array(
			'src' => AssetsMain::localize_asset( 'source/css/frontend/frocentric.css' ),
		);
		$styles['dashicons'] = array();
		$styles['google-fonts-nunito'] = array(
			'src'  => 'https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,wght@0,400;0,600;0,700;0,800;0,900;1,400;1,600;1,700;1,800;1,900&display=swap',
			'deps' => array(),
		);

		return $styles;
	}

	/**
	 * Adds support for audio post types
	 */
	public static function after_setup_theme() {
		add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link', 'status', 'audio' ) );
	}

	private static function explode_path( $path ) {
		return preg_split( '@/@', $path, -1, PREG_SPLIT_NO_EMPTY );
	}

	private static function get_category_slug() {
		$categories = get_the_category();
		$slug       = null;

		if ( ! empty( $categories ) ) {
			$slug = $categories[0]->slug;
		}

		return $slug;
	}

	private static function get_events_root() {
		return function_exists( 'tribe_get_option' ) ? tribe_get_option( 'eventsSlug', 'events' ) : 'events';
	}

	/**
	 * Hook in methods.
	 */
	public static function hooks() {
		// Actions
		add_action( 'after_setup_theme', array( __CLASS__, 'after_setup_theme' ), 100 );
		add_action( 'init', array( __CLASS__, 'init' ) );
		add_action( 'login_enqueue_scripts', array( __CLASS__, 'login_enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( AssetsMain::class, 'load_scripts' ) );
		add_action( 'wp_insert_post', array( __CLASS__, 'wp_insert_post' ), 10, 3 );
		add_action( 'wp_print_footer_scripts', array( AssetsMain::class, 'localize_printed_scripts' ), 5 );
		add_action( 'wp_print_scripts', array( AssetsMain::class, 'localize_printed_scripts' ), 5 );

		// Filters
		add_filter( 'logout_redirect', array( __CLASS__, 'logout_redirect' ), 10, 3 );
		add_filter( 'nav_menu_css_class', array( __CLASS__, 'nav_menu_css_class' ), 10, 3 );
		add_filter( 'frocentric_enqueue_scripts', array( __CLASS__, 'add_scripts' ), 9 );
		add_filter( 'frocentric_enqueue_styles', array( __CLASS__, 'add_styles' ), 9 );
		add_filter( 'show_admin_bar', array( __CLASS__, 'show_admin_bar' ), 20, 1 );
		add_filter( 'script_loader_src', array( __CLASS__, 'remove_version_from_assets' ), 999 );
		add_filter( 'style_loader_src', array( __CLASS__, 'remove_version_from_assets' ), 999 );
		add_filter( 'the_content', array( __CLASS__, 'the_content' ), 999, 1 );
		add_filter( 'the_content_feed', array( __CLASS__, 'the_content_feed' ), 999, 1 );
		add_filter( 'the_excerpt_rss', array( __CLASS__, 'the_content_feed' ), 999, 1 );
		add_filter( 'the_generator', array( __CLASS__, 'remove_version_from_generator' ) );
		add_filter( 'wp_get_nav_menu_items', array( __CLASS__, 'wp_get_nav_menu_items' ), 10, 3 );
		add_filter( 'wp_nav_menu_objects', array( __CLASS__, 'wp_nav_menu_objects' ) );

		// Shortcodes.
		add_shortcode(
			'wpse_comments_template',
			function ( $atts = array(), $content = '' ) {
				if ( is_singular() && post_type_supports( get_post_type(), 'comments' ) ) {
					ob_start();
					comments_template();
					add_filter( 'comments_open', 'wpse_comments_open' );
					add_filter( 'get_comments_number', 'wpse_comments_number' );
					return ob_get_clean();
				}
				return '';
			}
		);
	}

	/**
	 * Add categories and tags to pages
	 *
	 * @since    1.1.0
	 */
	public static function init() {
		register_taxonomy_for_object_type( 'category', 'page' );
		register_taxonomy_for_object_type( 'post_tag', 'page' );
	}

	/**
	 * Register/queue frontend scripts.
	 *
	 * @return void
	 */
	public static function login_enqueue_scripts() {
		wp_enqueue_style( 'frocentric-login', AssetsMain::localize_asset( 'source/css/frontend/login.css' ), array(), \Frocentric\VERSION, 'all' );
		wp_enqueue_script( 'frocentric-login', AssetsMain::localize_asset( 'source/js/frontend/login.js' ), array( 'jquery' ), \Frocentric\VERSION, false );
	}

	/**
	 * Redirects the user to homepage after logging out.
	 *
	 * @param string $redirect_to URL to redirect to.
	 * @param string $request URL the user is coming from.
	 * @param object $user Logged user's data.
	 * @return string
	 */
	public static function logout_redirect( $redirect_to, $request, $user ) {
		return esc_url( home_url() );
	}

	/**
	 * Modify navigation output to correctly highlight current section.
	 *
	 * @param    string[] $classes Array of the CSS classes that are applied to the menu item's <li> element.
	 * @param    WP_Post  $item    The current menu item.
	 * @param    stdClass $args    An object of wp_nav_menu() arguments.
	 * @param    int      $depth   Depth of menu item. Used for padding.
	 * @return   string[]
	 * @since    1.0.0
	 */
	public static function nav_menu_css_class( $classes, $item, $args, $depth = 0 ) {
		global $post;

		// Modifies primary navigation menu only.
		if ( $args->theme_location !== 'primary' ) {
			return $classes;
		}

		$parent_classes = array( 'current-menu-item', 'page_item', 'current_page_item', 'current_page_parent' );
		$event_prefixes   = array( 'events', 'event', 'organiser', 'venue', 'series' );
		$page_path = wp_make_link_relative( get_permalink() );
		$page_segments = self::explode_path( $page_path );
		$item_segments = self::explode_path( $item->url );
		$events_root = self::get_events_root();
		$is_events_item = count( $item_segments ) > 0 && $item_segments[ count( $item_segments ) - 1 ] === $events_root;
		$is_events_page = count( $page_segments ) > 0 && in_array( $page_segments[0], $event_prefixes, true );

		// Highlight Events page link for any event-related page.
		if ( $is_events_page && $is_events_item ) {
			$classes = array_merge( $classes, $parent_classes );
		} else {
			$posts_page = get_option( 'page_for_posts' );
			$slug       = self::get_category_slug();

			// Specify default page if posts page not enabled.
			if ( $posts_page === 0 ) {
				$posts_page = 13283; // TODO: refactor magic number.
			}

			// Highlight Content page link for any content post or category page.
			if (
				(
					( is_single() && get_post_type() === 'post' && $slug !== 'news' )
					|| is_author()
					|| is_category()
					|| is_tag()
					|| is_tax()
					|| is_search()
					|| strpos( $page_path, '/authors' ) === 0
				)
				&& strpos( parse_url( $item->url, PHP_URL_PATH ), '/content' ) === 0
			) {
				$classes = array_merge( $classes, $parent_classes );
			} elseif ( is_page() && $post->post_parent === (int) $item->object_id ) {
				$classes = array_merge( $classes, $parent_classes );
			}
		}

		// Filter out duplicate classes.
		return array_unique( $classes );
	}

	/**
	 * Remove WP version number from CSS and JS URLs
	 */
	public static function remove_version_from_assets( $src ) {
		if ( strpos( $src, '?ver=' ) || strpos( $src, '&ver=' ) ) {
			$src = remove_query_arg( 'ver', $src );
		}

		return $src;
	}

	/**
	 * Remove WP version number in <generator /> tag from HTML pages and RSS feeds
	 */
	public static function remove_version_from_generator() {
		return '';
	}

	/**
	 * Hides the admin bar if user can't create/edit posts.
	 */
	public static function show_admin_bar( $show_admin_bar ) {
		return current_user_can( 'edit_posts' ) ? $show_admin_bar : false;
	}

	/**
	 * Append a copyright notice to the end of the post content
	 *
	 * @see https://codex.wordpress.org/Plugin_API/Filter_Reference/the_content_feed
	 * @param    string $content    The post content.
	 * @return   string
	 */
	public static function the_content( $content ) {
		global $post;
		if ( is_single( $post ) && ! empty( $post->_genesis_canonical_uri ) ) {
			// translators: %1 is the current year, %2 is the post author's name.
			$content .= '<aside class="copyright-notice">' . sprintf( esc_html__( '&copy; %1$s %2$s. Licensed for use by Frocentric CIC.', 'frocentric' ), gmdate( 'Y' ), get_the_author_meta( 'display_name', $post->post_author ) ) . '</aside>';
		}

		return $content;
	}

	/**
	 * Filter the content feed to use main content instead of excerpt
	 * Add a featured image
	 *
	 * @see https://codex.wordpress.org/Plugin_API/Filter_Reference/the_content_feed
	 * @param    string $excerpt    Post excerpt.
	 * @return   string
	 */
	public static function the_content_feed( $excerpt ) {
		// Add featured image?
		global $post;
		$excerpt = $post->post_excerpt;

		if ( has_post_thumbnail( $post->ID ) ) {
			$excerpt = get_the_post_thumbnail( $post->ID, 'post-thumbnail', array( 'style' => 'max-width: 600px; width: 100%; height: auto; margin: 30px 0;' ) ) . $excerpt;
		}

		return $excerpt;
	}

	/**
	 * Modifies the "Logout" menu link to direct to the correct URL
	 */
	public static function wp_get_nav_menu_items( $items, $menu, $args ) {
		if ( is_admin() || ! is_user_logged_in() ) {
			return $items;
		}
		foreach ( $items as $key => $item ) {
			if ( str_contains( $item->url, 'logout' ) ) {
				$items[ $key ]->url = wp_logout_url();

				break;
			}
		}
		return $items;
	}

	/**
	 * Sets the post format based on stored metadata
	 */
	public static function wp_insert_post( $post_ID, $post, $update ) {
		// execute only on creation, not on update, and only if the post type is post
		if ( $update !== true && $post->post_type === 'post' ) {
			$post_format = get_metadata( 'post', $post_ID, 'post_format', true );

			if ( $post_format ) {
				set_post_format( $post_ID, $post_format );
				delete_post_meta( $post_ID, 'post_format' );
			}
		}
	}

	/**
	 * Hides navigation menu items depending on user login status
	 */
	public static function wp_nav_menu_objects( $items ) { //phpcs:ignore
		global $wp;

		$permalink = trailingslashit( home_url( $wp->request ) );
		$current_user = wp_get_current_user();
		$offset = 0;
		$flagged = array();

		foreach ( $items as $item ) {
			if ( in_array( 'user-login', $item->classes, true ) && isset( $_SERVER['REQUEST_URI'] ) && strpos( $item->url, 'redirect_to=' ) === false ) {
				$item->url = add_query_arg( 'redirect_to', wp_sanitize_redirect( urlencode( home_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) ) ), $item->url );
			}

			if ( in_array( 'user-logout', $item->classes, true ) ) {
				$item->url = wp_logout_url( $permalink );
			}

			if ( in_array( 'user-profile', $item->classes, true ) && is_user_logged_in() ) {
				$item->title = get_avatar( $current_user->ID, 25 );
			}

			if ( ( is_user_logged_in() && in_array( 'logged-out', $item->classes, true ) ) || ( ! is_user_logged_in() && in_array( 'logged-in', $item->classes, true ) ) ) {
				$flagged[] = $offset;
			}

			$offset = ++$offset;
		}

		$index = count( $flagged );

		while ( $index ) {
			array_splice( $items, $flagged[ --$index ], 1 );
		}

		return $items;
	}
}
