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
		$events_class   = 'events';

		// Highlight Events page link for any event-related page.
		if ( substr( wp_make_link_relative( get_permalink() ), 0, strlen( $events_class ) + 1 ) === "/$events_class" &&
				in_array( $events_class, $classes, true ) ) {
			$classes = array_merge( $classes, $parent_classes );
		} else {
			$posts_page = get_option( 'page_for_posts' );
			$slug       = $this->get_category_slug();

			// Specify default page if posts page not enabled.
			if ( 0 === $posts_page ) {
				$posts_page = 13283; // TODO: refactor magic number.
			}

			// Highlight Content page link for any content post or category page.
			if ( ( ( is_single() && get_post_type() === 'post' && $slug !== 'news' ) || is_author() || is_category() || is_tag() || is_tax() || is_search() ) && strpos( parse_url( $item->url, PHP_URL_PATH ), '/content' ) === 0 ) {
				$classes = array_merge( $classes, $parent_classes );
			} elseif ( is_page() && $post->post_parent === (int) $item->object_id ) {
				$classes = array_merge( $classes, $parent_classes );
			}
		}

		// Filter out duplicate classes.
		return array_unique( $classes );
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

			$this->parse_url( $url, $matches );
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

	protected function parse_url( $url, $matches ) {
		// TODO: replace with regex test
		switch ( $matches[1] ) {
			case 'eventbrite.com':
			case 'eventbrite.co.uk':
			case 'www.eventbrite.com':
			case 'www.eventbrite.co.uk':
				$this->parse_eventbrite_url( $url, $matches );
				break;
		}
		wp_send_json_error( __( 'Unsupported domain, please try again', 'froware' ) );
	}

	protected function parse_eventbrite_url( $url, $matches ) {
		$regex = '/.*-(\d+)(?:\/|\?)?.*$/';
		// Capture event ID from URL.
		preg_match( $regex, $url, $matches );

		if ( $matches && count( $matches ) > 1 ) {
			$this->send_response( 'eventbrite', $matches );
		} else {
			wp_send_json_error( __( 'Valid URL not supplied, please try again', 'froware' ) );
		}
	}

	protected function send_response( $origin, $matches ) {
		$event_id                         = $matches[1];
		$response                         = new stdClass();
		$response->action                 = 'import_event';
		$response->eventbrite_import_by   = 'event_id';
		$response->event_plugin           = 'tec';
		$response->event_status           = 'draft';
		$response->import_frequency       = 'daily';
		$response->import_origin          = $origin;
		$response->import_type            = 'onetime';
		$response->wpea_action            = 'wpea_import_submit';
		$response->wpea_eventbrite_id     = $event_id;
		$response->wpea_import_form_nonce = wp_create_nonce( 'wpea_import_form_nonce_action' );

		wp_send_json_success( $response );
	}

	/**
	 * Renders the event import form
	 */
	public function event_import_form() {
		$action       = '';
		$tribe_id     = '';
		$tribe_events = new Tribe__Events__Community__Main();
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
			$importer = new WP_Event_Aggregator_Pro_Manage_Import();

			$importer->handle_import_form_submit();

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

		return $modified;
	}

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
	public function discourse_update_post_meta( $object_id, array $terms, array $tt_ids, string $taxonomy ) {
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
		}
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
}
