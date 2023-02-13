<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://ingenyus.com
 * @since      1.0.0
 *
 * @package    Froware
 * @subpackage Froware/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Froware
 * @subpackage Froware/public
 * @author     Gary McPherson <gary@ingenyus.com>
 */
class Froware_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The ID of an imported event.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      int    $imported_event_id    The ID of an imported event.
	 */
	protected $imported_event_id;

	/**
	 * The taxonomies used to generate Discourse tags.
	 */
	protected $discourse_tag_taxonomies;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name       = $plugin_name;
		$this->version           = $version;
		$this->imported_event_id = -1;
		$this->discourse_tag_taxonomies = [ 'discipline', 'interest' ];

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Froware_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Froware_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/froware-public.css', [], $this->version, 'all' );
		wp_enqueue_style( 'dashicons' );
		// phpcs:ignore
		wp_enqueue_style( 'google-fonts-nunito', 'https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,wght@0,400;0,600;0,700;0,800;0,900;1,400;1,600;1,700;1,800;1,900&display=swap', false );

	}

	/**
	 * Retrieves the sub-domain for the current request
	 */
	protected function get_subdomain() {
		if ( ! isset( $_SERVER ) || ! isset( $_SERVER['HTTP_HOST'] ) ) {
			return '';
		}

		$domain = filter_input( INPUT_SERVER, 'HTTP_HOST' );

		return substr( $domain, strpos( $domain, '.' ) + 1 );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_login_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Froware_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Froware_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/froware-login.css', [], $this->version, 'all' );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/froware-login.js', [ 'jquery' ], $this->version, false );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Froware_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Froware_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/froware-public.js', [ 'jquery' ], $this->version, false );
		wp_localize_script(
			$this->plugin_name,
			'settings',
			[
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'homeurl' => home_url(),
			]
		);

	}

	/**
	 * Add custom fonts to GeneratePress font list.
	 *
	 * @param    string[] $fonts    Array of loaded fonts.
	 * @return   array
	 * @since    1.0.0
	 */
	public function add_generatepress_fonts( $fonts ) {
		$fonts[] = 'Helvetica Neue Condensed Bold';
		$fonts[] = 'Nunito Sans';

		sort( $fonts );

		return $fonts;
	}

	/**
	 * Filter post date markup.
	 *
	 * @param    string $output      Default output.
	 * @param    string $time_string Post time string.
	 * @return   array
	 * @since    1.0.0
	 */
	public function generate_inside_post_meta_item_output( $output, $item ) {
		if ( 'author' === $item ) {
			$user_id = get_the_author_meta( 'ID' );
			$output  = sprintf( '<a href="%1$s" class="avatar-link">%2$s</a>', get_author_posts_url( $user_id ), get_avatar( $user_id, 32 ) );
		}

		return $output;
	}

	/**
	 * Filter post date markup.
	 *
	 * @param    string $output      Default output.
	 * @param    string $time_string Post time string.
	 * @return   array
	 * @since    1.0.0
	 */
	public function generate_post_date_output( $output, $time_string ) {
		return sprintf( // WPCS: XSS ok, sanitization ok.
			'<span class="posted-on">%1$s%2$s</span> ',
			apply_filters( 'generate_inside_post_meta_item_output', '', 'date' ),
			$time_string
		);
	}

	/**
	 * Generate SVG icon markup for navigation menu.
	 *
	 * @param    string $output      Default output.
	 * @param    string $time_string Post time string.
	 * @return   string
	 * @since    1.0.0
	 */
	public function generate_svg_icon_element( $output, $icon ) {
		if ( 'menu-bars' === $icon ) {
			$output = '<img alt="Open menu" src="' . esc_url( get_stylesheet_directory_uri() ) . '/images/burger-menu-open.svg" class="open-menu" />';
			$output .= '<img alt="Close menu" src="' . esc_url( get_stylesheet_directory_uri() ) . '/images/burger-menu-close.svg" class="close-menu" />';
		}

		return $output;
	}

	/**
	 * Filter SVG icon markup.
	 * Removes default icon markup.
	 *
	 * @param    string $output      Default output.
	 * @param    string $icon        The icon.
	 * @return   string
	 * @since    1.0.0
	 */
	public function remove_svg_icon( $output, $icon ) {
		$output = str_replace(
			'<svg viewBox="0 0 512 512" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"><path d="M71.029 71.029c9.373-9.372 24.569-9.372 33.942 0L256 222.059l151.029-151.03c9.373-9.372 24.569-9.372 33.942 0 9.372 9.373 9.372 24.569 0 33.942L289.941 256l151.03 151.029c9.372 9.373 9.372 24.569 0 33.942-9.373 9.372-24.569 9.372-33.942 0L256 289.941l-151.029 151.03c-9.373 9.372-24.569 9.372-33.942 0-9.372-9.373-9.372-24.569 0-33.942L222.059 256 71.029 104.971c-9.372-9.373-9.372-24.569 0-33.942z" /></svg>',
			'',
			$output
		);

		return $output;
	}

	/**
	 * Fixes landing page 404 when non-standard permalinks are enabled.
	 *
	 * @param \WP_Query $query
	 * @return   string
	 * @since    1.0.0
	 */
	public function elementor_pre_get_posts( \WP_Query $query ) {
		if (
			// If the post type includes the Elementor landing page CPT.
			class_exists( '\Elementor\Modules\LandingPages\Module' )
			&& is_array( $query->get( 'post_type' ) )
			&& in_array( \Elementor\Modules\LandingPages\Module::CPT, $query->get( 'post_type' ), true )
			// If custom permalinks are enabled.
			&& '' !== get_option( 'permalink_structure' )
			// If the query is for a front-end page.
			&& ( ! is_admin() || wp_doing_ajax() )
			&& $query->is_main_query()
			// If the query is for a page.
			&& isset( $query->query['page'] )
			// If the query is not for a static home/blog page.
			&& ! is_home()
			// If the page name has been set and is not for a path.
			&& ! empty( $query->query['pagename'] )
			&& false === strpos( $query->query['pagename'], '/' )
			// If the name has not already been set.
			&& empty( $query->query['name'] ) ) {
			$query->set( 'name', $query->query['pagename'] );
		}
	}

	/**
	 * Add categories and tags to pages
	 *
	 * @since    1.1.0
	 */
	public function add_taxonomy_to_pages() {
		register_taxonomy_for_object_type( 'category', 'page' );
		register_taxonomy_for_object_type( 'post_tag', 'page' );
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
	public function special_nav_class( $classes, $item, $args, $depth = 0 ) {
		global $post;

		// Modifies primary navigation menu only.
		if ( 'primary' !== $args->theme_location ) {
			return $classes;
		}

		$parent_classes = [ 'current-menu-item', 'page_item', 'current_page_item', 'current_page_parent' ];
		$event_prefixes   = [ 'events', 'event', 'organiser', 'venue', 'series' ];
		$page_path = wp_make_link_relative( get_permalink() );
		$page_segments = $this->explode_path( $page_path );
		$item_segments = $this->explode_path( $item->url );
		$events_root = $this->get_events_root();
		$is_events_item = count( $item_segments ) > 0 && $item_segments[ count( $item_segments ) - 1 ] === $events_root;
		$is_events_page = count( $page_segments ) > 0 && in_array( $page_segments[0], $event_prefixes, true );

		// Highlight Events page link for any event-related page.
		if ( $is_events_page && $is_events_item ) {
			$classes = array_merge( $classes, $parent_classes );
		} else {
			$posts_page = get_option( 'page_for_posts' );
			$slug       = $this->get_category_slug();

			// Specify default page if posts page not enabled.
			if ( 0 === $posts_page ) {
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

	protected function get_events_root() {
		return function_exists( 'tribe_get_option' ) ? tribe_get_option( 'eventsSlug', 'events' ) : 'events';
	}

	protected function explode_path( $path ) {
		return preg_split( '@/@', $path, -1, PREG_SPLIT_NO_EMPTY );
	}

	protected function get_category_slug() {
		$categories = get_the_category();
		$slug       = null;

		if ( ! empty( $categories ) ) {
			$slug = $categories[0]->slug;
		}

		return $slug;
	}

	/**
	 * Hides navigation menu items depending on user login status
	 */
	public function wp_nav_menu_objects_callback( $items ) { //phpcs:ignore
		global $wp;

		$permalink = trailingslashit( home_url( $wp->request ) );
		$current_user = wp_get_current_user();
		$offset = 0;
		$flagged = [];

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

	/**
	 * Adds support for audio post types
	 */
	public function extend_theme_support() {
		add_theme_support( 'post-formats', [ 'aside', 'image', 'video', 'quote', 'link', 'status', 'audio' ] );
	}

	/**
	 * Overrides WP Show Posts card styling
	 *
	 * @param string[] $defaults    Array of default configuration values.
	 * @return string[] Modified array of configuration values.
	 */
	public function wpsp_defaults( $defaults ) {
		$defaults['wpsp_post_meta_bottom_style'] = 'inline';

		return $defaults;
	}

	/**
	 * Modifies the post content when the "Post Content" placeholder has been inserted
	 */
	public function feedzy_content_callback( $item_content, $item ) {
		$placeholder = esc_html__( 'Post Content', 'feedzy-rss-feeds' );

		if ( strpos( $item_content, $placeholder ) === 0 ) {
			$item_content = str_replace( $placeholder, '', $item_content );
		}

		return $item_content;
	}

	/**
	 * Add attributes to $item_array.
	 *
	 * @since   1.0.0
	 * @access  public
	 *
	 * @param   array  $item_array The item attributes array.
	 * @param   object $item The feed item.
	 * @param   array  $sc The shorcode attributes array.
	 * @param   int    $index The item number (may not be the same as the item_index).
	 * @param   int    $item_index The real index of this items in the feed (maybe be different from $index if filters are used).
	 *
	 * @return mixed
	 */
	public function feedzy_item_filter_callback( $item_array, $item, $sc = null, $index = null, $item_index = null ) {
		// Embed enclosure for podcast feeds
		if ( is_array( $item->data['enclosures'] ) && count( $item->data['enclosures'] ) > 0 ) {
			$enclosure = $item->data['enclosures'][0];
			$item_array['item_content'] .= '[audio src="' . $enclosure->link . '"]';
		}

		return $item_array;
	}

	/**
	 * Modifies the post arguments when importing an item.
	 */
	public function feedzy_insert_post_args_callback( $args, $item, $post_title, $post_content, $index, $job ) {
		$args = $this->set_post_author( $args, $item );
		$args = $this->set_post_canonical_url( $args, $item );
		$args = $this->set_post_format( $args, $item );

		return $args;
	}

	/**
	 * Sets the post format based on stored meta data
	 */
	public function wp_insert_post_callback( $post_ID, $post, $update ) {
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
	 * Get the post content up to the More tag
	 *
	 * @see https://codex.wordpress.org/Function_Reference/get_extended
	 */
	public function get_content_to_more_tag() {
		global $post;
		$content_main = get_extended( $post->post_content );
		$content_main = $content_main['main'];

		return wpautop( $content_main );
	}

	/**
	 * Filter the content feed to use main content instead of excerpt
	 * Add a featured image
	 *
	 * @see https://codex.wordpress.org/Plugin_API/Filter_Reference/the_content_feed
	 * @param    string $excerpt    Post excerpt.
	 * @return   string
	 */
	public function filter_content_feed( $excerpt ) {
		// Add featured image?
		global $post;
		$excerpt = $post->post_excerpt;

		if ( has_post_thumbnail( $post->ID ) ) {
			$excerpt = get_the_post_thumbnail( $post->ID, 'post-thumbnail', [ 'style' => 'max-width: 600px; width: 100%; height: auto; margin: 30px 0;' ] ) . $excerpt;
		}

		return $excerpt;
	}

	/**
	 * Append a copyright notice to the end of the post content
	 *
	 * @see https://codex.wordpress.org/Plugin_API/Filter_Reference/the_content_feed
	 * @param    string $content    The post content.
	 * @return   string
	 */
	public function append_copyright_notice( $content ) {
		global $post;
		if ( is_single( $post ) && ! empty( $post->_genesis_canonical_uri ) ) {
			// translators: %1 is the current year, %2 is the post author's name.
			$content .= '<aside class="copyright-notice">' . sprintf( esc_html__( '&copy; %1$s %2$s. Licensed for use by Frocentric CIC.', 'frocentric' ), gmdate( 'Y' ), get_the_author_meta( 'display_name', $post->post_author ) ) . '</aside>';
		}

		return $content;
	}

	/**
	 * Filter the request args for Twig Anything requests to support Discourse API changes
	 *
	 * @param   array $args   Array of request arguments.
	 * @param   array $config Array of configuration values.
	 * @return array
	 */
	public function twig_anything_request_args( $args, $config ) {
		if ( defined( 'DISCOURSE_API_KEY' ) && defined( 'DISCOURSE_API_USERNAME' ) ) {
			$args['headers'] = [
				'Api-Key'      => DISCOURSE_API_KEY,
				'Api-Username' => DISCOURSE_API_USERNAME,
			];
		}

		return $args;
	}

	/**
	 * Validates an event URL for import
	 */
	public function validate_event_url() {
		global $wpea_success_msg, $wpea_errors;

		// Ensure callback handler only executes once per request.
		if ( did_action( 'validate_event_url' ) > 1 ) {
			return;
		}

		// if ( check_admin_referer( 'wpea_import_form_nonce_action', 'wpea_import_form_nonce' ) === false ) {.
		if ( ! isset( $_POST ) || ! isset( $_POST['event_url'] ) || check_admin_referer( 'import_form_nonce_action', 'import_form_nonce' ) === false ) {
			wp_send_json_error( __( 'Invalid form', 'froware' ) );
		}

		$url = filter_input( INPUT_POST, 'event_url' );

		if ( ! empty( $url ) ) {
			$regex = '/^https?:\/\/([^\/]+)*/';
			// Capture domain from URL.
			preg_match( $regex, $url, $matches );

			if ( ! $matches || count( $matches ) <= 1 ) {
				wp_send_json_error( __( 'Invalid URL, please try again', 'froware' ) );
			}

			$this->import_from_url( $url );
		} else {
			wp_send_json_error( __( 'URL not supplied, please try again', 'froware' ) );
		}
	}

	/**
	 * Sets the post author based on the provided querystring value
	 * Requires "feed_author" parameter to be added to feed URL in Feedzy control panel. Can be set to either a user ID or login.
	 */
	protected function set_post_author( $args, $item ) {
		$source = $item['item']->get_feed()->subscribe_url();
		$source = parse_url( $source );
		$author = null;

		if ( isset( $source['query'] ) ) {
			parse_str( $source['query'], $params );

			// Set post author
			$feed_author = isset( $params['feed_author'] ) ? $params['feed_author'] : 0;

			if ( $feed_author ) {
				$author = is_numeric( $feed_author ) ? get_user_by( 'ID', (int) $feed_author ) : get_user_by( 'login', $feed_author );

				if ( $author ) {
					$args['post_author'] = $author->ID;
				}
			}
		}

		$args = $this->set_post_citation( $args, $item, $author );

		return $args;
	}

	/**
	 * Sets the post format based on the provided querystring value
	 * Requires "post_format" parameter to be added to feed URL in Feedzy control panel. Can be set to any standard post format.
	 */
	protected function set_post_format( $args, $item ) {
		$source = $item['item']->get_feed()->subscribe_url();
		$source = parse_url( $source );
		$post_format = null;

		if ( isset( $source['query'] ) ) {
			parse_str( $source['query'], $params );

			// Set post format
			$post_format = isset( $params['post_format'] ) ? $params['post_format'] : null;

			if ( $post_format ) {
				$formats = array_keys( get_post_format_slugs() );

				if ( empty( $args['meta_input'] ) ) {
					$args['meta_input'] = [];
				}

				$args['meta_input']['post_format'] = in_array( $post_format, $formats, true ) ? $post_format : 'standard';
			}
		}

		return $args;
	}

	/**
	 * Sets the canonical link to the source item
	 */
	protected function set_post_canonical_url( $args, $item ) {
		$args['meta_input']['_genesis_canonical_uri'] = $item['item_url'];

		return $args;
	}

	/**
	 * Replaces the default "Read More" link to the source with a formatted citation
	 */
	protected function set_post_citation( $args, $item, $author ) {
		$author_name = '';

		if ( ! empty( $author ) ) {
			$author_name = $author->display_name;
		} else {
			if ( $item['item_author'] ) {
				if ( is_string( $item['item_author'] ) ) {
					$author_name = $item['item_author'];
				} elseif ( is_object( $item['item_author'] ) ) {
					$author_name = $item['item_author']->get_name();
				}
			}
		}

		$item_link  = '<a href="' . $item['item_url'] . '" target="_blank" class="feedzy-rss-link-icon">' . __( 'Read More', 'feedzy-rss-feeds' ) . '</a>';
		$citation = '<aside class="cite">' . __( 'Originally posted by ', 'frocentric' ) . $author_name . ' to <a href="' . $item['item_url'] . '" target="_blank" rel="nofollow">' . $args['post_title'] . '</a></aside>';
		$args['post_content'] = str_replace( $item_link, $citation, $args['post_content'] );

		return $args;
	}

	protected function import_from_url( $url ) {
		$regex = '/^https?:\/\/(?:www\.)?eventbrite(?:\.[a-z]{2,3}){1,2}\/.*/';

		if ( ! preg_match( $regex, $url ) ) {
			wp_send_json_error( __( 'Unsupported domain, please create manually', 'froware' ) );

			return;
		}

		// phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.FoundInControlStructure
		if ( ( $event_id = $this->get_eventbrite_event_id( $url ) ) !== false ) {
			$response = $this->create_eventbrite_response( $event_id );
		} else {
			wp_send_json_error( __( 'Valid URL not supplied, please try again', 'froware' ) );

			return;
		}

		wp_send_json_success( $response );
	}

	protected function get_eventbrite_event_id( $url ) {
		$regex = '/^https?:\/\/(?:www\.)?eventbrite(?:\.[a-z]{2,3}){1,2}\/e\/.*-(\d+)(?:\/|\?)?.*/';
		$matches = [];
		// Capture event ID from URL.
		preg_match( $regex, $url, $matches );

		if ( $matches && count( $matches ) > 1 ) {
			return $matches[1];
		}

		return false;
	}

	protected function create_eventbrite_response( $event_id ) {
		$response                         = new stdClass();
		$response->action                 = 'import_event';
		$response->eventbrite_import_by   = 'event_id';
		$response->event_plugin           = 'tec';
		$response->event_status           = tribe( 'community.main' )->getOption( 'defaultStatus', 'pending' );
		$response->import_frequency       = 'daily';
		$response->import_origin          = 'eventbrite';
		$response->import_type            = 'onetime';
		$response->wpea_action            = 'wpea_import_submit';
		$response->wpea_eventbrite_id     = $event_id;
		$response->wpea_import_form_nonce = wp_create_nonce( 'wpea_import_form_nonce_action' );
		$response->import_source          = 'tec_community_submission';

		return $response;
	}

	/**
	 * Renders the event import form
	 */
	public function event_import_form() {
		$post_id      = get_the_ID();

		if ( class_exists( 'WP_Event_Aggregator_Pro_Manage_Import' ) && ( ! $post_id || ! tribe_is_event( $post_id ) ) ) {
			require_once plugin_dir_path( __FILE__ ) . '../public/partials/froware-event-form.php';
		}
	}

	/**
	 * Imports an event using the WP Event Aggregator API
	 */
	public function import_event() {
		global $wpea_success_msg, $wpea_errors;

		// Ensure callback handler only executes once per request.
		if ( did_action( 'import_event' ) > 1 ) {
			return;
		}

		if ( check_admin_referer( 'wpea_import_form_nonce_action', 'wpea_import_form_nonce' ) === false ) {
			wp_send_json_error();
		}

		// TODO: Validate fields (type, frequency, status, categories).

		if ( class_exists( 'WP_Event_Aggregator_Pro_Manage_Import' ) ) {
			run_wp_event_aggregator()->manage_import->handle_import_form_submit();

			if ( count( $wpea_success_msg ) > 0 ) {
				$imported_event = tribe_get_event( $this->imported_event_id );

				$this->send_status( $imported_event );
			} elseif ( count( $wpea_errors ) > 0 ) {
				wp_send_json_error( $wpea_errors[0] );
			}
		} else {
			wp_send_json_error( __( 'Unrecognised event format.', 'froware' ) );
		}
	}

	/**
	 * Prints an error message and ensures that we don't hit bugs on Select2
	 *
	 * @since  4.6
	 *
	 * @param string $message
	 *
	 * @return void
	 */
	protected function ajax_error( $message ) {
		$data = [
			'message' => $message,
			'results' => [],
		];

		wp_send_json_error( $data );
	}

	/**
	 * Flattens the taxonomy array passed back to a Select2 dropdown
	 *
	 * @param array<object>              $data   Array of results.
	 * @param string|array<string|mixed> $search Search string from Select2
	 * @param int                        $page   When we deal with pagination
	 * @param array<string|mixed>        $args   Which arguments we got from the Template
	 * @param string                     $source What source it is
	 *
	 * @return array<string|mixed>
	 */
	// phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded, Generic.Metrics.NestingLevel.MaxExceeded
	public function tribe_dropdown_search_terms( $data, $search, $page, $args, $source ) {
		if ( empty( $args['taxonomy'] ) ) {
			$this->ajax_error( esc_attr__( 'Cannot look for Terms without a taxonomy', 'tribe-common' ) );
		}

		// We always want all the fields so we overwrite it
		$args['fields']     = isset( $args['fields'] ) ? $args['fields'] : 'all';
		$args['hide_empty'] = isset( $args['hide_empty'] ) ? $args['hide_empty'] : false;

		if ( ! empty( $search ) ) {
			if ( ! is_array( $search ) ) {
				// For older pieces that still use Select2 format.
				$args['search'] = $search;
			} else {
				// Newer SelectWoo uses a new search format.
				$args['search'] = $search['term'];
			}
		}

		// On versions older than 4.5 taxonomy goes as an Param
		if ( version_compare( $GLOBALS['wp_version'], '4.5', '<' ) ) {
			$terms = get_terms( $args['taxonomy'], $args );
		} else {
			$terms = get_terms( $args );
		}

		$results = [];

		if ( empty( $args['search'] ) ) {
			foreach ( $terms as $i => $term ) {
				// Prep for Select2
				$term->id   = $term->term_id;
				$term->text = $term->name;

				$results[ $term->term_id ] = $term;
				unset( $terms[ $i ] );
			}
		} else {
			foreach ( $terms as $term ) {
				// Prep for Select2
				$term->id          = $term->term_id;
				$term->text        = $term->name;
				$term->breadcrumbs = [];

				if ( 0 !== (int) $term->parent ) {
					$ancestors = get_ancestors( $term->id, $term->taxonomy );
					$ancestors = array_reverse( $ancestors );
					foreach ( $ancestors as $ancestor ) {
						$ancestor            = get_term( $ancestor );
						$term->breadcrumbs[] = $ancestor->name;
					}
				}

				$results[] = $term;
			}
		}

		foreach ( $results as $result ) {
			$result->text = wp_specialchars_decode( wp_kses( $result->text, [] ) );
		}

		$data['results']    = array_values( (array) $results );
		$data['taxonomies'] = get_taxonomies();

		return $data;
	}
	/**
	 * Get an event's cost
	 *
	 * @param string   $cost                 Current cost value
	 * @param null|int $post_id              (optional)
	 * @param bool     $with_currency_symbol Include the currency symbol
	 *
	 * @return string Cost of the event.
	 * @category Cost
	 */
	public function tribe_get_cost( $cost, $post_id, $with_currency_symbol ) {
		if ( empty( $cost ) ) {
			$cost = '0';
		}

		return $cost;
	}

	/**
	 * Edits the event submission message to be more friendly
	 */
	public function tribe_events_filter_submission_message( $message, $type ) {
		if ( 'update' === $type ) {
			$events_label_singular = tribe_get_event_label_singular();
			$events_label_singular_lowercase = tribe_get_event_label_singular_lowercase();

			// translators: %s is the singular event label.
			if ( strpos( $message, sprintf( __( '%s updated.', 'tribe-events-community' ), $events_label_singular ) ) === 0 ) {
				// translators: %s is the lower-case singular event label.
				$message = sprintf( __( 'Your %s has been submitted and is awaiting review before being published. Thank you for contributing, we truly appreciate it!', 'tribe-events-community' ), $events_label_singular_lowercase );
			}
		}

		return $message;
	}

	/**
	 * Allows filtering the quantity available displayed below the ticket
	 * quantity input for purchase of this one ticket.
	 *
	 * If less than the maximum quantity available, will restrict that as well.
	 *
	 * @since 4.8.1
	 *
	 * @param int                           $available_at_a_time Max purchase quantity, as restricted by Max At A Time.
	 * @param Tribe__Tickets__Ticket_Object $ticket              Ticket object.
	 * @param WP_Post                       $event               Event post.
	 */
	public function tribe_tickets_set_ticket_max_purchase( $available_at_a_time, $ticket, $event ) {
		$key = 'ticket_max_purchase';
		$values = get_post_custom_values( $key, $event->ID );

		if ( is_array( $values ) && is_numeric( $values[0] ) ) {
			 $available_at_a_time = intval( $values[0] );
		}

		return $available_at_a_time;
	}

	/**
	 * Get markup for Eventbrite (non-modal) checkout.
	 * Adapted from WP Event Aggregator.
	 *
	 * @return string
	 */
	public function tribe_eventbrite_checkout_markup( $eventbrite_id ) {
		ob_start();
		?>
	<div id="tec-eventbrite-checkout-widget"></div>
	<?php // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript ?>
	<script src="https://www.eventbrite.com/static/widgets/eb_widgets.js"></script>
	<script type="text/javascript">
		window.EBWidgets.createWidget({
			widgetType: "checkout",
			eventId: "<?php echo $eventbrite_id; ?>",
			iframeContainerId: "tec-eventbrite-checkout-widget",
			iframeContainerHeight: <?php echo apply_filters( 'tec_embedded_checkout_height', 530 ); ?>,
			onOrderComplete: () => {console.log("Order complete!");}
		});
	</script>
		<?php
		return ob_get_clean();
	}

	/**
	 * Get ticket section markup for Eventbrite events.
	 *
	 * @since  1.1.0
	 * @return html
	 */
	public function tribe_get_ticket_section( $eventbrite_id = 0 ) {
		if ( $eventbrite_id > 0 ) {
			ob_start();

			if ( is_ssl() ) {
				echo $this->tribe_eventbrite_checkout_markup( $eventbrite_id );
			} else {
				?>
				<div class="eventbrite-ticket-section" style="width:100%; text-align:left;">
					<iframe id="eventbrite-tickets-<?php echo $eventbrite_id; ?>" src="//www.eventbrite.com/tickets-external?eid=<?php echo $eventbrite_id; ?>" style="width:100%;height:300px; border: 0px;"></iframe>
				</div>
				<?php
			}

			$ticket = ob_get_clean();

			return $ticket;
		} else {
			return '';
		}

	}

	/**
	 * Display Ticket Section after eventbrite events.
	 *
	 * @since 1.0.0
	 */
	public function tribe_add_eventbrite_ticket_section() {
		global $importevents;
		$event_id = get_the_ID();
		$event_url = get_post_meta( $event_id, '_EventURL', true );
		$eventbrite_event_id = $this->get_eventbrite_event_id( $event_url );

		if ( $event_id > 0 && $eventbrite_event_id && is_numeric( $eventbrite_event_id ) && $eventbrite_event_id > 0 ) {
			$ticket_section = $this->tribe_get_ticket_section( $eventbrite_event_id );
			echo $ticket_section;
		}
	}

	const EVENT_TAXONOMIES = [
		'audience' => 'Filterbar_Filter_Audience',
		'discipline' => 'Filterbar_Filter_Discipline',
		'interest' => 'Filterbar_Filter_Interest',
	];

	/**
	 * Includes the custom taxonomy filter classes and creates instances of them.
	 */
	public function tribe_filterbar_create_filters() {
		if ( ! class_exists( 'Tribe__Events__Filterbar__Filter' ) ) {
			return;
		}

		$this->include_filter_classes();

		// Instantiate custom taxonomy filter classes
		foreach ( self::EVENT_TAXONOMIES as $taxonomy => $class_name ) {
			$ref = new ReflectionClass( $class_name );
			$obj = $ref->newInstanceArgs( [ ucfirst( $taxonomy ), ( 'filterbar_' . $taxonomy ) ] );
		}
	}

	/**
	 * Filters the map of filters available on the front-end to include the custom one.
	 *
	 * @param array<string,string> $map A map relating the filter slugs to their respective classes.
	 *
	 * @return array<string,string> The filtered slug to filter class map.
	 */
	public function tribe_filterbar_filter_map( array $map ) {
		if ( ! class_exists( 'Tribe__Events__Filterbar__Filter' ) ) {
			// This would not make much sense, but let's be cautious.
			return $map;
		}

		$this->include_filter_classes();

		// Add the filter classes to our filters map.
		foreach ( self::EVENT_TAXONOMIES as $taxonomy => $class_name ) {
			$map[ ( 'filterbar_' . $taxonomy ) ] = $class_name;
		}

		// Return the modified $map.
		return $map;
	}

	/**
	 * Filters the Context locations to let the Context know how to fetch the value of the filter from a request.
	 *
	 * Here we add the taxonomy filters as read-only Context locations: we'll not need to write it.
	 *
	 * @param array<string,array> $locations A map of the locations the Context supports and is able to read from and write
	 *                                                                              to.
	 *
	 * @return array<string,array> The filtered map of Context locations, with the one required from the filter added to it.
	 */
	public function tribe_filterbar_filter_context_locations( array $locations ) {
		$get_fb_val_from_view_data = static function ( $key ) {
			return static function ( $view_data ) use ( $key ) {
				return ! empty( $view_data[ 'tribe_filterbar_' . $key ] ) ? $view_data[ 'tribe_filterbar_' . $key ] : null;
			};
		};

		$taxonomy_locations = [];

		foreach ( array_keys( self::EVENT_TAXONOMIES ) as $taxonomy ) {
			$taxonomy_locations[ 'filterbar_' . $taxonomy ] = [
				'read' => [
					\Tribe__Context::QUERY_VAR     => [ ( 'tribe_filterbar_' . $taxonomy ) ],
					\Tribe__Context::REQUEST_VAR   => [ ( 'tribe_filterbar_' . $taxonomy ) ],
					\Tribe__Context::LOCATION_FUNC => [ 'view_data', $get_fb_val_from_view_data( $taxonomy ) ],
				],
			];
		}
		// Read the filter selected values, if any, from the URL request vars.
		$locations = array_merge( $locations, $taxonomy_locations );

		// Return the modified $locations.
		return $locations;
	}

	protected function include_filter_classes() {
		// TODO: implement class autoloading for custom filters
		include_once __DIR__ . '/class-filterbar-filter-taxonomy.php';

		foreach ( array_keys( self::EVENT_TAXONOMIES ) as $taxonomy ) {
			include_once __DIR__ . '/class-filterbar-filter-' . $taxonomy . '.php';
		}
	}

	protected function send_status( $imported_event ) {
		global $wpea_success_msg;

		if ( ! empty( $imported_event ) && ! is_wp_error( $imported_event ) ) {
			wp_send_json_success( $imported_event );
		} else {
			// TODO: Pattern match against __( '%d Skipped (Already exists)', 'wp-event-aggregator' )
			$message = strpos( $wpea_success_msg[0], 'Already exists' ) > 0 ?
				__( 'This event has already been imported, please try again', 'froware' ) :
				$wpea_success_msg[0];
			wp_send_json_error( $message );
		}
	}

	/**
	 * Tracks a new event in order to be rendered in front-end event creation UI.
	 *
	 * @param int   $new_event_id ID of newly-saved event.
	 * @param array $formatted_args Array of arguments used when creating the event.
	 * @param array $centralize_array Centralize array form of event.
	 */
	public function track_new_event( $new_event_id, $formatted_args, $centralize_array ) {
		$this->imported_event_id = $new_event_id;
	}

	/**
	 * Overrides parse_request event hook in The Events Calendar Community Events plugin
	 */
	public function override_community_events_parse_request_hook() {
		if ( class_exists( 'WP_Router' ) ) {
			remove_action( 'parse_request', [ WP_Router::get_instance(), 'parse_request' ], 10, 1 );
			add_action( 'parse_request', [ $this, 'shim_parse_request' ], 10, 1 );
		}

		if ( function_exists( 'wpmus_maybesync_newuser' ) ) {
			// phpcs:ignore
			global $wpmus_newUserSync;

			//phpcs:ignore
			if ( $wpmus_newUserSync === 'yes' ) {
				remove_action( 'wp_login', 'wpmus_maybesync_newuser', 10, 1 );
				remove_action( 'social_connect_login', 'wpmus_maybesync_newuser', 10, 1 );
				add_action( 'wp_login', [ $this, 'wpmus_maybesync_newuser' ], 10, 1 );
				add_action( 'social_connect_login', [ $this, 'wpmus_maybesync_newuser' ], 10, 1 );
			}
		}
	}

	/**
	 * New login trigger. This action is needed to check if the user is on all sites
	 */
	public function wpmus_maybesync_newuser( $user_login ) {
		global $wpmus_newUserSync; //phpcs:ignore

		//phpcs:ignore
		if ( $wpmus_newUserSync === 'yes' ) {

			$userdata = get_user_by( 'login', $user_login );

			if ( $userdata !== false && get_user_meta( $userdata->ID, 'msum_has_caps', true ) !== 'true' ) {
				wpmus_sync_newuser( $userdata->ID );
			}
		}
	}

	/**
	 * Hooks onto ninja_forms_post_run_action_type_redirect to correctly decode redirection URL argument
	 * Fixes URL provided when logging in from Discourse
	 */
	public function ninja_forms_post_run_action_type_redirect_callback( $data ) {
		if ( array_key_exists( 'actions', $data ) && array_key_exists( 'redirect', $data['actions'] ) ) {
			$data['actions']['redirect'] = htmlspecialchars_decode( $data['actions']['redirect'] );
		}

		return $data;
	}

	/**
	 * Shims WP_Router->parse_request to prevent errors when The Events Calendar Community Events plugin is enabled
	 */
	public function shim_parse_request( $query ) {
		if ( is_a( $query, 'WP' ) ) {
			WP_Router::get_instance()->parse_request( $query );
		}
	}

	/**
	 * Modifies the comment template to render user avatars as 32x32px
	 */
	public function discourse_comment_html( $output ) {
		return str_replace( '64', '32', $output );
	}

	/**
	 * Modifies the replies template to add the comment count to the title
	 */
	public function discourse_replies_html( $output ) {
		$modified = $output;

		if ( isset( $_GET['post_id'] ) && is_numeric( $_GET['post_id'] ) ) {
			$post = get_post( sanitize_key( wp_unslash( $_GET['post_id'] ) ) );

			if ( $post && get_comments_number( $post->ID ) ) {
				$modified = str_replace( '</h2>', ' (<span class="comment-count">' . get_comments_number( $post->ID ) . '</span>)</h2>', $modified );

			}
		}

		$modified = preg_replace_callback(
			'/(<h3\sid="reply-title".*?href="([^"]+)".*?<\/h3>)/s',
			function ( $matches ) {
				$url = $matches[2] . '#reply';

				if ( ! wp_validate_logged_in_cookie( false ) ) {
					$url = home_url( '?discourse_sso=1&redirect_to=' . urlencode( $url ) );
					$url = str_replace( '%7B', '{', str_replace( '%7D', '}', $url ) );
				}

				$value = '<div class="elementor-button-wrapper"><a href="' . $url . '" class="elementor-button-link elementor-button elementor-size-lg" role="button"><span class="elementor-button-content-wrapper"><span class="elementor-button-text">' . esc_html__( 'Reply', 'frocentric' ) . '</span></span></a></div>';

				return $value;
			}, $modified
		);
		$needle = 'class="comments-area">';

		if ( strpos( $modified, $needle ) !== false ) {
			$modified = str_replace(
				$needle,
				$needle . '<div class="comments-title-wrap"><h2 class="comments-title discourse-comments-title">' . esc_html__( 'No Replies' ) . '</h2></div>',
				$modified
			);
		}

		return $modified;
	}

	/*

			<div class="comments-title-wrap">
				<h2 class="comments-title discourse-comments-title"><?php echo esc_html( self::get_text_options( 'notable-replies-text' ) ); ?></h2>
			</div>
	"		<div id="comments" class="comments-area">
			<div class="respond comment-respond">
				<div class="elementor-button-wrapper"><a href="{topic_url}#reply" class="elementor-button-link elementor-button elementor-size-lg" role="button"><span class="elementor-button-content-wrapper"><span class="elementor-button-text">Reply</span></span></a></div>
			</div>
		</div>
		"
	*/
	/**
	 * Redirect user after successful login via Discourse.
	 *
	 * @param string $redirect_to URL to redirect to.
	 * @param string $request URL the user is coming from.
	 * @param object $user Logged user's data.
	 * @return string
	 */
	public function discourse_login_redirect( $redirect_to, $request, $user ) {
		//is there a user to check?
		if ( isset( $user->roles ) && is_array( $user->roles ) && $this->discourse_client_configured() ) {
			// check for admin URL
			if ( str_starts_with( $redirect_to, admin_url() ) ) {
				// redirect them to the default location
				return $redirect_to;
			} else {
				// redirect them to the community
				return get_option( 'discourse_connect' )['url'];
			}
		} else {
			return $redirect_to;
		}
	}

	/**
	 * Uses the Discourse avatar if user has one, otherwise uses the WordPress avatar.
	 *
	 * @param string $url The current URL.
	 * @param mixed $id_or_email The Gravatar key.
	 * @param array $args Arguments passed to get_avatar_data.
	 */
	public function discourse_get_avatar_url( $url, $id_or_email, $args ) {
		if ( is_numeric( $id_or_email ) ) {
			$user = get_user_by( 'id', $id_or_email );
		} elseif ( is_object( $id_or_email ) ) {
			if ( ! empty( $id_or_email->user_id ) ) {
				$user = get_user_by( 'id', $id_or_email->user_id );
			}
		} else {
			$user = get_user_by( 'email', $id_or_email );
		}

		if ( $user && $user->ID ) {
			$discourse_user = get_user_meta( $user->ID, 'discourse_user', true );

			if ( $discourse_user && isset( $discourse_user['avatar_url'] ) ) {
				return $discourse_user['avatar_url'];
			}
		}

		return $url;
	}

	/**
	 * Saves the Discourse account details to the user metadata.
	 *
	 * @param int $user_id The WordPress user's ID.
	 * @param array $discourse_user The Discourse user data.
	 */
	public function discourse_sso_update_user_meta( $user_id, $discourse_user ) {
		update_user_meta( $user_id, 'discourse_user', $discourse_user );
	}

	/**
	 * Updates Discourse publishing meta.
	 *
	 * @param int $post_id The object's ID.
	 * @param array $terms An array of object term IDs or slugs.
	 * @param array $tt_ids An array of term taxonomy IDs.
	 * @param string $taxonomy The taxonomy slug.
	 */
	public function discourse_update_post_meta( $object_id, $terms, $tt_ids, $taxonomy ) {
		if ( ! in_array( $taxonomy, $this->discourse_tag_taxonomies, true ) ) {
			return;
		}

		$post = get_post( $object_id );
		// bail out if this isn't a regular post
		if ( empty( $post ) || is_wp_error( $post ) || 'post' !== $post->post_type ) {
			return;
		}

		// bail out if the post isn't in the Community or Platform categories
		$categories = wp_get_post_categories( $object_id, [ 'fields' => 'slugs' ] );
		if ( ! in_array( 'community', $categories, true ) && ! in_array( 'platform', $categories, true ) ) {
			return;
		}

		$tags = $this->generate_discourse_tags( $object_id );
		// Update Discourse tags.
		update_post_meta( $object_id, 'wpdc_topic_tags', $tags );

		if ( ! metadata_exists( 'post', $object_id, 'publish_to_discourse' ) ) {
			// Enable publishing in Discourse, during initial save only.
			update_post_meta( $object_id, 'publish_to_discourse', true );
			$discourse_publish_option = get_option( 'discourse_publish' );

			if ( is_array( $discourse_publish_option ) && array_key_exists( 'publish-category', $discourse_publish_option ) ) {
				$publish_category = $discourse_publish_option['publish-category'];
				update_post_meta( $object_id, 'publish_post_category', $publish_category );
			}
		}
	}

	/**
	 * Enables the Discourse user webhook.
	 */
	public function discourse_enable_user_webhook( $use_webhook_sync ) {
		return true;
	}

	public function discourse_webhook_before_update_user_meta( $wordpress_user, $discourse_user, $event_type ) {
		$bio  = $discourse_user['bio_raw'];
		$website = $discourse_user['website'];
		$user_fields = $discourse_user['user_fields'];
		$user_id = $wordpress_user->ID;
		$discourse_meta = get_user_meta( $user_id, 'discourse_user', true );

		if ( empty( $discourse_meta ) && ! empty( $user_fields ) ) {
			$discourse_meta = [];
		}

		if ( empty( $user_fields ) ) {
			if ( array_key_exists( 'user_fields', $discourse_meta ) ) {
				unset( $discourse_meta['user_fields'] );
			}
		} else {
			$discourse_meta['user_fields'] = $user_fields;
		}

		update_user_meta( $user_id, 'discourse_user', $discourse_meta );
		update_user_meta( $user_id, 'description', empty( $bio ) ? '' : $bio );
		wp_update_user( [ 'ID' => $user_id, 'user_url' => empty( $website ) ? '' : $website ] );
	}

	/**
	 * Generates the Discourse tags based on WP taxonomies.
	 *
	 * @param int $post_id The post's ID.
	 * @return array
	 */
	protected function generate_discourse_tags( $post_id ) {
		$tags       = [];

		foreach ( $this->discourse_tag_taxonomies as $taxonomy ) {
			$terms = get_the_terms( $post_id, $taxonomy );

			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$tags[] = $term->slug;
				}
			}
		}

		return $tags;
	}

	/**
	 * Checks if WordPress is configured as a Discourse client.
	 *
	 * @return bool
	 */
	protected function discourse_client_configured() {
		return class_exists( 'WPDiscourse\Discourse\Discourse' ) && isset( get_option( 'discourse_connect' )['url'] );
	}

	/**
	 * Redirects the user to homepage after logging out.
	 *
	 * @param string $redirect_to URL to redirect to.
	 * @param string $request URL the user is coming from.
	 * @param object $user Logged user's data.
	 * @return string
	 */
	public function logout_redirect( $redirect_to, $request, $user ) {
		return esc_url( home_url() );
	}

	/**
	 * Modifies the "Logout" menu link to direct to the correct URL
	 */
	public function set_logout_menu_item_url( $items, $menu, $args ) {
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
	 * Parses API fields from the e-addons link field
	 * Tokens are in the format {{field_name}}
	 */
	public function parse_api_fields( $value, $fields = [], $urlencode = false ) {
		if ( ! array_key_exists( 'block', $fields ) ) {
			return $value;
		}

		$api_fields = $fields['block'];
		$value = preg_replace_callback(
			'/(\{\{\s*(\w+)\s*\}\})/',
			function ( $matches ) use ( $urlencode, $api_fields ) {
				$value = '';

				if ( isset( $api_fields[ $matches[2] ] ) ) {
					$value = $api_fields[ $matches[2] ];
				}

				if ( $urlencode ) {
					$value = urlencode( $value );
				}

				return $value;
			}, $value
		);

		return $value;
	}

	/**
	 * Hides the admin bar if user can't create/edit posts.
	 */
	public function toggle_admin_bar( $show_admin_bar ) {
		return current_user_can( 'edit_posts' ) ? $show_admin_bar : false;
	}
}
