<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


class Tribe__Events__Pro__Shortcodes__Inline__Parser {

	/**
	 * @var string
	 */
	protected $output = '';

	/**
	 * @var array
	 */
	protected $placeholders = array();

	/**
	 * Container for the shortcode attributes.
	 *
	 * @var array
	 */
	protected $atts = array();

	/**
	 * @var int
	 */
	protected $id = 0;

	/**
	 * @var array
	 */
	protected $organizer_id = array();

	/**
	 * @var string
	 */
	protected $content = '';

	/**
	 * Construct
	 *
	 * @param Tribe__Events__Pro__Shortcodes__Tribe_Inline $shortcode
	 */
	public function __construct( Tribe__Events__Pro__Shortcodes__Tribe_Inline $shortcode ) {

		$this->shortcode = $shortcode;
		$this->atts      = $shortcode->atts;
		$this->id        = $this->atts['id'];
		$this->content   = $shortcode->content;

		/**
		 * Filter the Placeholders to be parsed in the inline content
		 *
		 * @param array $placeholders
		 */
		$this->placeholders = apply_filters( 'tribe_events_pro_inline_placeholders', $this->placeholders() );

		$this->process();

		$this->process_multiple_organizers();

	}

	/**
	 * Placeholders to be parsed
	 *
	 * @return array
	 */
	protected function placeholders() {
		return array(
			'{title}'              => 'get_the_title',
			'{name}'               => 'get_the_title',
			'{title:linked}'       => array( $this, 'linked_title' ),
			'{link}'               => 'get_permalink',
			'{url}'                => array( $this, 'url_open' ),
			'{/url}'               => array( $this, 'url_close' ),
			'{content}'            => array( $this, 'content' ),
			'{content:unfiltered}' => array( $this, 'content_unfiltered' ),
			'{description}'        => array( $this, 'content' ),
			'{excerpt}'            => array( $this, 'tribe_events_get_the_excerpt' ),
			'{thumbnail}'          => array( $this, 'thumbnail' ),
			'{start_date}'         => array( $this, 'start_date' ),
			'{start_time}'         => array( $this, 'start_time' ),
			'{end_date}'           => array( $this, 'end_date' ),
			'{end_time}'           => array( $this, 'end_time' ),
			'{event_website}'      => 'tribe_get_event_website_link',
			'{cost}'               => 'tribe_get_cost',
			'{cost:formatted}'     => array( $this, 'tribe_get_cost' ),
			'{venue}'              => 'tribe_get_venue',
			'{venue:name}'         => 'tribe_get_venue',
			'{venue:linked}'       => array( $this, 'linked_title_venue' ),
			'{venue_address}'      => array( $this, 'venue_address' ),
			'{venue_phone}'        => 'tribe_get_phone',
			'{venue_website}'      => 'tribe_get_venue_website_link',
			'{organizer}'          => array( $this, 'tribe_get_organizer' ),
			'{organizer:linked}'   => array( $this, 'linked_title_organizer' ),
			'{organizer_phone}'    => array( $this, 'tribe_get_organizer_phone' ),
			'{organizer_email}'    => array( $this, 'tribe_get_organizer_email' ),
			'{organizer_website}'  => array( $this, 'tribe_get_organizer_website_link' ),
		);
	}

	/**
	 * Process the placeholders
	 */
	protected function process() {

		// Prevents unbalanced tags (and thus broken HTML) on final shortcode output.
		$this->content = force_balance_tags( $this->content );

		$this->organizer_id = tribe_get_organizer_ids( $this->id );

		foreach ( $this->placeholders as $tag => $handler ) {

			if ( false === strpos( $this->content, $tag ) ) {
				continue;
			}

			$id = $this->id;
			//Used to support multiple organizers
			if ( 'organizer' === substr( $tag, 1, 9 ) ) {
				$id = 0;
			}

			$value         = is_callable( $handler ) ? call_user_func( $handler, $id ) : '';
			$this->content = str_replace( $tag, $value, $this->content );
		}

		/**
		 * Filter Processed Content
		 * Includes only first organizer
		 *
		 * @param string $html
		 */
		$this->output = apply_filters( 'tribe_events_pro_inline_output', $this->content );
	}

	/**
	 * Process the placeholders
	 */
	protected function process_multiple_organizers() {

		$multiple = count( $this->organizer_id ) > 1;

		// only parse again if multiple organizers connected to event
		if ( $multiple ) {

			preg_match_all( '/{(organizer.*?)(\\d+)}/', $this->content, $match );

			if ( null !== $match && is_array( $match[1] ) ) {

				foreach ( $match[1] as $key => $tag ) {

					if ( ! isset( $match[2][ $key ] ) ) {
						continue;
					}

					$id_array_num = $match[2][ $key ] - 1;
					if ( ! isset( $this->organizer_id[ $id_array_num ] ) ) {
						return false;
					}

					$tag     = '{' . $tag . '}';
					$replace = $match[0][ $key ];
					$handler = $this->placeholders[ $tag ];

					$value         = is_callable( $handler ) ? call_user_func( $handler, $this->organizer_id[ $id_array_num ] ) : '';
					$this->content = str_replace( $replace, $value, $this->content );

				}

			}
			/**
			 * Filter Processed Content After Multiple Organizers
			 *
			 * @param string $html
			 */
			$this->output = apply_filters( 'tribe_events_pro_inline_event_multi_organizer_output', $this->content );
		}

		return false;
	}

	/**
	 * Linked Event/Post Title
	 *
	 * @return string
	 */
	public function linked_title() {
		return '<a href="' . get_permalink( $this->id ) . '">' . get_the_title( $this->id ) . '</a>';
	}

	/**
	 * Opening URL Tag
	 *
	 * @return string
	 */
	public function url_open() {
		return '<a href="' . get_permalink( $this->id ) . '">';
	}

	/**
	 * Closing URL Tag
	 *
	 * @return string
	 */
	public function url_close() {
		return '</a>';
	}

	/**
	 * Content with applied filters
	 *
	 * @return mixed|void
	 */
	public function content() {

		$content = get_post_field( 'post_content', $this->id );

		return apply_filters( 'the_content', $content );
	}

	/**
	 * Unfiltered Content
	 *
	 * @return string
	 */
	public function content_unfiltered() {

		return get_post_field( 'post_content', $this->id );

	}

	/**
	 * Get Excerpt Using Tribe's Function
	 *
	 * @return string
	 */
	public function tribe_events_get_the_excerpt() {

		return tribe_events_get_the_excerpt( $this->id, wp_kses_allowed_html( 'post' ) );

	}

	/**
	 * Featured Image with no link
	 *
	 * @return string
	 */
	public function thumbnail() {
		return tribe_event_featured_image( $this->id, 'full', false );
	}

	/**
	 * Start Date Formatted by Tribe Setting
	 *
	 * @return null|string
	 */
	public function start_date() {
		return tribe_get_start_date( $this->id, false );
	}

	/**
	 * Start time if not all day event
	 *
	 * @return null|string
	 */
	public function start_time() {
		if ( ! tribe_event_is_all_day( $this->id ) ) {
			return tribe_get_start_date( $this->id, false, get_option( 'time_format', Tribe__Date_Utils::TIMEFORMAT ) );
		}

		return false;
	}

	/**
	 * End Date Formatted by Tribe Setting
	 *
	 * @return null|string
	 */
	public function end_date() {
		return tribe_get_end_date( $this->id, false );
	}

	/**
	 * End time if not all day event
	 *
	 * @return null|string
	 */
	public function end_time() {
		if ( ! tribe_event_is_all_day( $this->id ) ) {
			return tribe_get_end_date( $this->id, false, get_option( 'time_format', Tribe__Date_Utils::TIMEFORMAT ) );
		}

		return false;
	}

	/**
	 * Event Cost with formatting
	 *
	 * @return string
	 */
	public function tribe_get_cost() {
		return tribe_get_cost( $this->id, true );
	}


	/**
	 * Linked Venue Title
	 *
	 * @return bool|string
	 */
	public function linked_title_venue() {

		$venue_id = tribe_get_venue_id( $this->id );

		if ( ! $venue_id ) {
			return false;
		}

		return '<a href="' . get_permalink( $venue_id ) . '">' . get_the_title( $venue_id ) . '</a>';
	}

	/**
	 * Venue Address displayed inline
	 *
	 * @return bool|string
	 */
	public function venue_address() {

		$venue_address = array(
			'address'       => tribe_get_address( $this->id ),
			'city'          => tribe_get_city( $this->id ),
			'stateprovince' => tribe_get_stateprovince( $this->id ),
			'zip'           => tribe_get_zip( $this->id ),
			'country'       => tribe_get_country( $this->id ),
		);

		//Unset any address with no value for line
		foreach ( $venue_address as $key => $line ) {
			if ( ! $venue_address[ $key ] ) {
				unset( $venue_address[ $key ] );
			}
		}

		if ( ! empty( $venue_address ) ) {
			return implode( ', ', $venue_address );
		}

		return false;

	}

	/**
	 * Linked Organizer Title
	 *
	 * @return string
	 */
	public function tribe_get_organizer( $org_id ) {

		if ( 0 === $org_id && isset( $this->organizer_id[ $org_id ] ) ) {
			$org_id = $this->organizer_id[ $org_id ];
		}
		if ( $org_id ) {
			return tribe_get_organizer( $org_id );
		}

		return false;
	}

	/**
	 * Linked Organizer Title
	 *
	 * @return bool|string
	 */
	public function linked_title_organizer( $org_id ) {

		if ( 0 === $org_id && isset( $this->organizer_id[ $org_id ] ) ) {
			$org_id = $this->organizer_id[ $org_id ];
		}
		if ( $org_id ) {
			return '<a href="' . get_permalink( $org_id ) . '">' . get_the_title( $org_id ) . '</a>';
		}

		return false;
	}

	/**
	 * Get Organizer Phone
	 *
	 * @return bool|string
	 */
	public function tribe_get_organizer_phone( $org_id ) {

		if ( 0 === $org_id && isset( $this->organizer_id[ $org_id ] ) ) {
			$org_id = $this->organizer_id[ $org_id ];
		}

		if ( $org_id ) {
			return tribe_get_organizer_phone( $org_id );
		}

		return false;

	}

	/**
	 * Get Organizer Email
	 *
	 * @return bool|string
	 */
	public function tribe_get_organizer_email( $org_id ) {

		if ( 0 === $org_id && isset( $this->organizer_id[ $org_id ] ) ) {
			$org_id = $this->organizer_id[ $org_id ];
		}
		if ( $org_id ) {
			return tribe_get_organizer_email( $org_id );
		}

		return false;

	}

	/**
	 * Get Organizer Website Link
	 *
	 * @return bool|string
	 */
	public function tribe_get_organizer_website_link( $org_id ) {

		if ( 0 === $org_id && isset( $this->organizer_id[ $org_id ] ) ) {
			$org_id = $this->organizer_id[ $org_id ];
		}
		if ( $org_id ) {
			return tribe_get_organizer_website_link( $org_id );
		}

		return false;

	}

	/**
	 * Returns the output of the parsed content for this shortcode
	 *
	 * @return string
	 */
	public function output() {
		return $this->output;
	}

}
