<?php
/**
 * Implements a shortcode that wraps the existing featured venue widget. Basic usage
 * is as follows (using a venue's post ID):
 *
 *     [tribe_featured_venue id="123"]
 *
 * Besides supplying the venue ID, a slug can be used. It is also possible to limit
 * the number of upcoming events:
 *
 *     [tribe_featured_venue slug="the-club" limit="5"]
 *
 * A title can also be added if desired:
 *
 *     [tribe_featured_venue slug="busy-location" title="Check out these events!"]
 */
class Tribe__Events__Pro__Shortcodes__Featured_Venue {
	public $output = '';

	/**
	 * Default arguments expected by the featured venue widget.
	 *
	 * @var array
	 */
	protected $default_args = array(
		'before_widget' => '',
		'before_title'  => '',
		'title'         => '',
		'after_title'   => '',
		'after_widget'  => '',

		'slug'          => '',
		'venue'         => '',
		'id'            => '',
		'limit'         => '',
		'hide_if_empty' => true,
	);

	protected $arguments = array();


	public function __construct( $attributes ) {
		$this->arguments = shortcode_atts( $this->default_args, $attributes );
		$this->parse_args();

		// If no venue has been set simply bail with an empty string
		if ( ! isset( $this->arguments['venue_ID'] ) ) {
			return;
		}

		/**
		 * Allows hot-swapping the featured venue widget class for different versions of the widget.
		 *
		 * @since 5.3.0
		 *
		 * @param string              $widget_class The widget class name we want to implement.
		 * @param array<string,mixed> $arguments    The widget arguments.
		 */
		$widget_class = apply_filters( 'tribe_events_pro_shortcodes_venue_widget_class', Tribe__Events__Pro__Venue_Widget::class, $this->arguments );

		if ( Tribe__Events__Pro__Venue_Widget::class === $widget_class ) {
			Tribe__Events__Pro__Widgets::enqueue_calendar_widget_styles();
		}

		ob_start();

		// We use $this->arguments for both the args and the instance vars here
		the_widget( $widget_class, $this->arguments, $this->arguments );

		$this->output = ob_get_clean();
	}

	/**
	 * Venue can be specified with one of "id" or "venue". Limit can be set using a
	 * "count" attribute.
	 */
	protected function parse_args() {
		if ( strlen( $this->arguments['id'] ) ) {
			$this->arguments['venue_ID'] = (int) $this->arguments['id'];
		} elseif ( strlen( $this->arguments['venue'] ) ) {
			$this->arguments['venue_ID'] = (int) $this->arguments['venue'];
		} elseif ( strlen( $this->arguments['slug'] ) ) {
			$this->set_by_slug();
		}

		if ( strlen( $this->arguments['limit'] ) ) {
			$this->arguments['count'] = (int) $this->arguments['limit'];
		} else {
			$this->arguments['count'] = (int) tribe_get_option( 'postsPerPage', 10 );
		}
	}

	/**
	 * Facilitates specifying the venue by providing its slug.
	 */
	protected function set_by_slug() {
		$venues = get_posts( array(
			'post_type' => Tribe__Events__Main::VENUE_POST_TYPE,
			'name' => $this->arguments['slug'],
			'posts_per_page' => 1,
		) );

		if ( empty( $venues ) ) {
			return;
		}

		$venue = array_shift( $venues );
		$this->arguments['venue_ID'] = (int) $venue->ID;
	}
}
