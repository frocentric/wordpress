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
	 * @return   array
	 * @since    1.0.0
	 */
	public function generate_svg_icon_element( $output, $icon ) {
		if ( 'menu-bars' === $icon ) {
			$output = '<img alt="Menu open" src="' . esc_url( get_stylesheet_directory_uri() ) . '/images/burger-menu-open.svg" />';
		}

		return $output;
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

			// Specify default page if posts page not enabled.
			if ( 0 === $posts_page ) {
				$posts_page = 13283; // TODO: refactor magic number.
			}

			// Highlight Content page link for any post or category page.
			if ( ( ( is_single() && get_post_type() === 'post' ) || is_category() ) && $posts_page === (int) $item->object_id ) {
				$classes = array_merge( $classes, $parent_classes );
			} elseif ( is_page() && $post->post_parent === (int) $item->object_id ) {
				$classes = array_merge( $classes, $parent_classes );
			}
		}

		// Filter out duplicate classes.
		return array_unique( $classes );
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
	 * Forces imported posts to take date from feed
	 *
	 * @param    array  $current_item    Current post data to be saved.
	 * @param    array  $campaign    Current campaign data.
	 * @param    object $feed    Feed database object.
	 * @param    object $item    SimplePie_Item object.
	 * @return   array
	 */
	public function wpematico_item_parsers_callback( $current_item, $campaign, $feed, $item ) {
		$found     = false;
		$tags      = [ 'pubDate', 'published' ];
		$date_elem = null;

		foreach ( $tags as $tag ) {
			$elem = $item->get_item_tags( '', $tag );

			if ( null !== $elem ) {
				if ( is_array( $elem ) ) {
					$date_elem = $elem[0]['data'];
					$found     = true;
				} elseif ( is_string( $elem ) ) {
					$date_elem = $elem;
					$found     = true;
				}
			}
		}

		if ( $found ) {
			$date                 = strtotime( trim( $date_elem, "; \t\n\r\0\x0B" ) );
			$current_item['date'] = $date;
		}

		return $current_item;
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

	protected function parse_url( $url, $matches ) {
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
	}

	/**
	 * Shims WP_Router->parse_request to prevent errors when The Events Calendar Community Events plugin is enabled
	 */
	public function shim_parse_request( $query ) {
		if ( is_a( $query, 'WP' ) ) {
			WP_Router::get_instance()->parse_request( $query );
		}
	}

	public function discourse_comment_html( $output ) {
		return str_replace( '64', '32', $output );
	}
}
