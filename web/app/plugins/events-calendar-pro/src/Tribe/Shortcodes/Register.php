<?php


/**
 * Registers shortcodes handlers for each of the widget wrappers.
 */
class Tribe__Events__Pro__Shortcodes__Register {
	/**
	 * Variable that holds the attributes of the shortcode being rendered by the iCal feed.
	 *
	 * @since 4.4.23
	 *
	 * @var array
	 */
	private $ical_shortcode_attributes = array(
		'view' => 'default',
	);

	/**
	 * Variable that holds the name of the option being created
	 *
	 * @since 4.4.26
	 *
	 * @var string
	 */
	private $main_calendar_option = 'tribe-shortcode-main-calendar-id';

	/**
	 * Variable that holds the name of the shortcodes being created
	 *
	 * @since 5.1.4
	 *
	 * @var array
	 */
	private $shortcodes = [
		'tribe_mini_calendar',
		'tribe_events_list',
		'tribe_featured_venue',
		'tribe_event_countdown',
		'tribe_this_week',
		'tribe_events',
		'tribe_event_inline',
	];

	public function __construct() {
		add_shortcode( 'tribe_mini_calendar', array( $this, 'mini_calendar' ) );
		add_shortcode( 'tribe_events_list', array( $this, 'events_list' ) );
		add_shortcode( 'tribe_featured_venue', array( $this, 'featured_venue' ) );
		add_shortcode( 'tribe_event_countdown', array( $this, 'event_countdown' ) );
		add_shortcode( 'tribe_this_week', array( $this, 'this_week' ) );
		add_shortcode( 'tribe_events', array( $this, 'tribe_events' ) );
		add_shortcode( 'tribe_event_inline', array( $this, 'tribe_inline' ) );

		$this->hook();
	}

	/**
	 * Function used to attach the hooks associated with this class.
	 *
	 * @since 4.4.26
	 */
	public function hook() {
		add_action( 'tribe_events_ical_before', array( $this, 'search_shortcodes' ) );
		// Hooks attached to the main calendar attribute on the shortcodes
		add_filter( 'tribe_events_get_link', array( $this, 'shortcode_main_calendar_link' ), 10, 2 );
		add_action( 'save_post', array( $this, 'update_shortcode_main_calendar' ) );
		add_action( 'trashed_post', array( $this, 'maybe_reset_main_calendar' ) );
		add_action( 'deleted_post', array( $this, 'maybe_reset_main_calendar' ) );
		add_filter( 'tribe_body_classes_should_add', [ $this, 'body_classes_should_add' ], 10, 4 );
	}

	public function mini_calendar( $atts ) {
		$wrapper = new Tribe__Events__Pro__Shortcodes__Mini_Calendar( $atts );

		return $wrapper->output;
	}

	public function events_list( $atts ) {
		$wrapper = new Tribe__Events__Pro__Shortcodes__Events_List( $atts );

		return $wrapper->output;
	}

	public function featured_venue( $atts ) {
		$wrapper = new Tribe__Events__Pro__Shortcodes__Featured_Venue( $atts );

		return $wrapper->output;
	}

	public function event_countdown( $atts ) {
		$wrapper = new Tribe__Events__Pro__Shortcodes__Event_Countdown( $atts );

		return $wrapper->output;
	}

	/**
	 * @param $atts
	 *
	 * @return string
	 */
	public function this_week( $atts ) {
		$wrapper = new Tribe__Events__Pro__Shortcodes__This_Week( $atts );

		return $wrapper->output;
	}

	/**
	 * Handler for the [tribe_events] shortcode.
	 *
	 * Please note that the shortcode should not be used alongside a regular event archive
	 * view nor should it be used more than once in the same request - or else breakages may
	 * occur. We try to limit accidental breakages by returning an empty string if we detect
	 * any of the above scenarios.
	 *
	 * This limitation can be lifted once our CSS, JS and template classes are refactored to
	 * support multiple instances of each view in the same request.
	 *
	 * @param $atts
	 *
	 * @return string
	 */
	public function tribe_events( $atts ) {
		static $deployed = false;

		if ( tribe_is_event_query() || $deployed ) {
			return '';
		}

		$shortcode = new Tribe__Events__Pro__Shortcodes__Tribe_Events( $atts );
		$deployed  = true;

		return $shortcode->output();
	}

	/**
	 * Handler for Inline Event Content Shortcode
	 *
	 * @param $atts
	 * @param $content
	 * @param $tag
	 *
	 * @return string
	 */
	public function tribe_inline( $atts, $content, $tag ) {

		$shortcode = new Tribe__Events__Pro__Shortcodes__Tribe_Inline( $atts, $content, $tag );

		return $shortcode->output();
	}

	/**
	 * Callback called by `tribe_events_ical_before` just before the iCal process is started to give us some time
	 * to setup other filters so we can change the event list used for the feed if the link is executed in a page with
	 * a shortcode of the calendars.
	 *
	 * @since 4.4.23
	 */
	public function search_shortcodes() {

		$valid_types = array(
			Tribe__Events__Main::POSTTYPE,
			Tribe__Events__Organizer::POSTTYPE,
			Tribe__Events__Venue::POSTTYPE,
		);

		if ( is_single() && ! is_singular( $valid_types ) ) {
			$this->find_events_in_shortcode();
		} elseif ( is_page() ) {
			$this->find_events_in_shortcode();
		}
	}

	/**
	 * Returns a list of events based on the shortcode inserted in current post / page, this will look if there are shortcode
	 * and extract the attributes of the shortcode to return the events based on those settings.
	 *
	 * @since 4.4.23
	 *
	 * @return array
	 */
	private function find_events_in_shortcode() {
		$this->ical_shortcode_attributes = $this->get_shortcode_attributes( get_the_ID() );
		$this->ical_shortcode_attributes['view'] = tribe_get_request_var( 'tribe_event_display', $this->ical_shortcode_attributes['view'] );

		if ( 'month' === strtolower( $this->ical_shortcode_attributes['view'] ) ) {
			add_filter( 'tribe_ical_feed_month_view_query_args', array( $this, 'ical_events_list_args' ) );
			add_filter( 'tribe_is_month', '__return_true' );
		} else {
			add_filter( 'tribe_events_ical_events_list_query', '__return_null' );
			add_filter( 'tribe_events_ical_events_list_args', array( $this, 'ical_events_list_args' ) );
		}
	}

	/**
	 * Callback attached to the filter `tribe_events_ical_events_list_args` to change the set of arguments used to
	 * query the Objects used on the iCal feed if the view is not month otherwise is attached to the filter
	 * `tribe_ical_feed_month_view_query_args`.
	 *
	 * @since 4.4.24
	 *
	 * @param $args array
	 * @return array
	 */
	public function ical_events_list_args( $args = array() ) {
		$date = empty( $this->ical_shortcode_attributes['date'] ) ? '' : $this->ical_shortcode_attributes['date'];
		$view = $this->ical_shortcode_attributes['view'];
		if ( 'month' === $this->ical_shortcode_attributes['view'] ) {
			$view = 'custom';
			if ( ! empty( $this->ical_shortcode_attributes['date'] ) ) {
				$args['start_date'] = Tribe__Events__Template__Month::calculate_first_cell_date( $date );
				$args['end_date'] = Tribe__Events__Template__Month::calculate_final_cell_date( $date );
			}
		} else {
			// if we are not in a month view $args should be created from scratch instead.
			$args = array();
			if ( ! empty( $date ) ) {
				$args['start_date'] = $date;
			}
		}
		$args['eventDisplay'] = $view;

		if ( ! empty( $this->ical_shortcode_attributes['category'] ) ) {
			$args[ Tribe__Events__Main::TAXONOMY ] = $this->ical_shortcode_attributes['category'];
		}

		if ( ! empty( $this->ical_shortcode_attributes['featured'] ) ) {
			$args['meta_key'] = Tribe__Events__Featured_Events::FEATURED_EVENT_KEY;
		}

		return $args;
	}

	/**
	 * Look for the attributes of the [tribe_events] shortcode inside of the current post / page by looking into
	 * the content, extract the attributes to know exactly how to render the iCal feed.
	 *
	 * @since 4.4.23
	 *
	 * @param int $id The post / page ID.
	 * @return array
	 */
	private function get_shortcode_attributes( $id = 0 ) {
		$content = get_post_field( 'post_content', $id );
		$shortcode = $this->get_shortcode( $content );
		// remove any empty value
		$attributes = array_filter( (array) shortcode_parse_atts( $shortcode ) );
		return wp_parse_args( $attributes, array(
			'view' => 'default',
		) );
	}

	/**
	 * Function to extract the $shortcode from the content, by default it will return the first match with all the attributes
	 * definition from the provided content. Removes the opening, closing and shortcode it self so it returns only the attributes
	 * of the shortcode.
	 *
	 * @since 4.4.23
	 *
	 * @param string $content The content with the shortcode (if any)
	 * @param string $shortcode The desired shortcode
	 * @return string
	 */
	private function get_shortcode( $content = '', $shortcode = 'tribe_events' ) {
		$pattern = get_shortcode_regex();
		preg_match_all( "/$pattern/s", $content, $matches );
		if ( ! empty( $matches[0] ) && is_array( $matches[0] ) ) {
			foreach ( $matches[0] as $match ) {
				// return only the first shortcode found.
				if ( false !== strpos( $match, $shortcode ) ) {
					// remove opening, closing and shortcode definition.
					return str_replace( array( '[', ']', $shortcode ), '', $match );
				}
			}
		}
		return '';
	}

	/**
	 * If the post has a shortcode [tribe_events] with the main calendar attribute on it we need to store
	 * the ID of the post to use it for later usages on the All Events Link page.
	 *
	 * @since 4.4.26
	 *
	 * @param $post_id
	 */
	public function update_shortcode_main_calendar( $post_id ) {
		$content = get_post_field( 'post_content', $post_id );

		// If does not have any shortcode any more
		if ( ! has_shortcode( $content, 'tribe_events' ) ) {
			$this->maybe_reset_main_calendar( $post_id );
			return;
		}

		if ( 'publish' !== get_post_status( $post_id ) ) {
			$this->maybe_reset_main_calendar( $post_id );
			return;
		}

		$shortcode = $this->get_shortcode( $content );
		$attrs = wp_parse_args( (array) shortcode_parse_atts( $shortcode ), array(
			'main-calendar' => false,
		) );

		if ( tribe_is_truthy( $attrs['main-calendar'] ) ) {
			tribe_update_option( $this->main_calendar_option, $post_id );
			return;
		}

		$this->maybe_reset_main_calendar( $post_id );
	}

	/**
	 * Maybe reset the ID of option that stores the main calendar post ID if is the same ID, useful for times
	 * when the post is removed
	 *
	 * @since 4.4.26
	 *
	 * @param $post_id
	 */
	public function maybe_reset_main_calendar( $post_id ) {
		$main_calendar_id = tribe_get_option( $this->main_calendar_option, 0 );
		// If the shortcode has been removed
		if ( $main_calendar_id === $post_id ) {
			tribe_update_option( $this->main_calendar_option, 0 );
		}
	}

	/**
	 * Constructs a new Link for the Home of the calendar if is on the singular view of the events page
	 *
	 * @since 4.4.26
	 *
	 * @param $link
	 * @param $type
	 *
	 * @return string
	 */
	public function shortcode_main_calendar_link( $link, $type ) {
		if ( 'home' !== $type )  {
			return $link;
		}

		/**
		 * This will prevent to change the main home link in all the site and just changed on the single event view
		 */
		if ( ! is_single() ) {
			return $link;
		}

		$wp_query = tribe_get_global_query_object();
		$post_parent = 0;
		$displaying = '';
		if ( null !== $wp_query && $wp_query->is_main_query() ) {
			$post_parent = $wp_query->get( 'post_parent' );
			$displaying = $wp_query->get( 'eventDisplay' );
		}
		$is_recurrence_single = $post_parent && 'all' == $displaying;

		$is_valid_page = is_singular( Tribe__Events__Main::POSTTYPE ) || $is_recurrence_single;
		if ( ! $is_valid_page ) {
			return $link;
		}

		$main_calendar_id = tribe_get_option( $this->main_calendar_option, 0 );

		if ( $main_calendar_id ) {
			$home_link = get_permalink( $main_calendar_id );
			return $home_link ? $home_link : $link;
		}

		return $link;
	}

	/**
	 * Hook into filter and add our logic for adding body classes.
	 *
	 * @since 5.1.4
	 *
	 * @param boolean $add              Whether to add classes or not.
	 * @param array   $add_classes      The array of body class names to add.
	 * @param array   $existing_classes An array of existing body class names from WP.
	 * @param string  $queue            The queue we want to get 'admin', 'display', 'all'.
	 *
	 * @return boolean Whether body classes should be added or not.
	 */
	public function body_classes_should_add( $add, $add_classes, $existing_classes, $queue ) {
		global $post;

		// If we're doing the tribe_events shortcode, add classes.
		if (
			is_singular()
			&& $post instanceof \WP_Post
		) {
			foreach ( $this->shortcodes as $shortcode ) {
				if ( has_shortcode( $post->post_content, $shortcode ) ) {
					return true;
				}
			}
		}

		return $add;
	}
}
