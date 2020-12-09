<?php
/**
 * Events Calendar Pro template Tags
 *
 * Display functions for use in WordPress templates.
 * @todo move view specific functions to their own file
 */

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( class_exists( 'Tribe__Events__Pro__Main' ) ) {

	if ( ! function_exists( 'tribe_get_mapview_link' ) ) {
		function tribe_get_mapview_link( $term = null ) {

			$wp_query = tribe_get_global_query_object();

			if ( ! is_null( $wp_query ) && isset( $wp_query->query_vars[ Tribe__Events__Main::TAXONOMY ] ) ) {
				$term = $wp_query->query_vars[ Tribe__Events__Main::TAXONOMY ];
			}
			$output = Tribe__Events__Main::instance()->getLink( 'map', null, $term );

			return apply_filters( 'tribe_get_map_view_permalink', $output );
		}
	}

	/**
	 * Event Recurrence
	 *
	 * Test to see if event is recurring.
	 *
	 * @param int $postId (optional)
	 *
	 * @return bool true if event is a recurring event.
	 */
	if ( ! function_exists( 'tribe_is_recurring_event' ) ) {
		function tribe_is_recurring_event( $post_id = null ) {

			$post_id = Tribe__Events__Main::postIdHelper( $post_id );

			if ( empty( $post_id ) ) {
				return false;
			}

			$post = get_post( $post_id );
			if ( $post->post_type != Tribe__Events__Main::POSTTYPE ) {
				return false;
			}

			$recurring = false;

			if ( $post->post_parent > 0 ) {
				$recurring = true;
			} else {
				$recurrence_meta = get_post_meta( $post_id, '_EventRecurrence', true );

				if ( ! empty( $recurrence_meta['rules'] ) ) {
					// check if this is event has old-style meta (pre 3.12)
					if ( ! isset( $recurrence_meta['rules'] ) && isset( $recurrence_meta['type'] ) ) {
						$recurrence_meta['rules'] = array( $recurrence_meta );
					}
					foreach ( $recurrence_meta['rules'] as &$recurrence ) {
						if ( 'None' !== $recurrence['type'] ) {
							$recurring = true;
							break;
						}
					}

				// Support legacy Recurrence
				} elseif ( ! empty( $recurrence_meta['type'] ) ) {
					if ( 'None' !== $recurrence_meta['type'] ) {
						$recurring = true;
					}
				}
			}

			/**
			 * Allows for filtering whether the specified event is recurring or not.
			 *
			 * @param boolean $recurring Whether the specified event is recurring or not.
			 * @param int $post_id The post ID of the specificed event.
			 */
			return apply_filters( 'tribe_is_recurring_event', $recurring, $post_id );
		}
	}

	/**
	 * Get the start dates of all instances of the event,
	 * in ascending order
	 *
	 * @param int $post_id
	 *
	 * @return array Start times, as Y-m-d H:i:s
	 */
	function tribe_get_recurrence_start_dates( $post_id = null ) {
		$post_id = Tribe__Events__Main::postIdHelper( $post_id );

		return Tribe__Events__Pro__Recurrence__Meta::get_start_dates( $post_id );
	}

	/**
	 * Recurrence Text
	 *
	 * Get the textual version of event recurrence
	 * e.g Repeats daily for three days
	 *
	 * @param int $postId (optional)
	 *
	 * @return string Summary of recurrence.
	 */
	if ( ! function_exists( 'tribe_get_recurrence_text' ) ) {

		function tribe_get_recurrence_text( $post_id = null ) {

			$post_id = Tribe__Events__Main::postIdHelper( $post_id );

			/**
			 * Allow for filtering the textual version of event recurrence.
			 *
			 * @param string $recurrence_text The textual version of the specified event's recurrence details.
			 * @param int $post_id The post ID of the specificed event.
			 */
			return apply_filters( 'tribe_get_recurrence_text', Tribe__Events__Pro__Recurrence__Meta::recurrenceToTextByPost( $post_id ), $post_id );
		}
	}

	/**
	 * Recurring Event List Link
	 *
	 * Display link for all occurrences of an event (based on the currently queried event).
	 *
	 * @since 3.0.0
	 * @since 5.0.0 Introduced caching based on Post ID or Parent Post ID.
	 *
	 * @param int      $post_id (optional) Which post we are looking for the All link.
	 * @param booolean $echo    (optional) Should be echoed along side returning the value.
	 *
	 * @return string  Link reference to all events in a recurrent event.
	 */
	if ( ! function_exists( 'tribe_all_occurences_link' ) ) {
		function tribe_all_occurences_link( $post_id = null, $echo = true ) {
			$cache_key_links = __FUNCTION__ . ':links';
			$cache_key_parent_ids = __FUNCTION__ . ':parent_ids';
			$cache_links = tribe_get_var( $cache_key_links, [] );
			$cache_parent_ids = tribe_get_var( $cache_key_parent_ids, [] );

			$post_id = Tribe__Events__Main::postIdHelper( $post_id );

			if ( ! isset( $cache_parent_ids[ $post_id ] ) ) {
				$cache_parent_ids[ $post_id ] = wp_get_post_parent_id( $post_id );
				tribe_set_var( $cache_key_parent_ids, $cache_parent_ids );
			}

			// The ID to cache will be diff depending on Parent or child post of recurrent event.
			$cache_id = $cache_parent_ids[ $post_id ] ? $cache_parent_ids[ $post_id ] : $post_id;

			if ( ! isset( $cache_links[ $cache_id ] ) ) {
				$tribe_ecp = Tribe__Events__Main::instance();
				$cache_links[ $cache_id ] = apply_filters( 'tribe_all_occurences_link', $tribe_ecp->getLink( 'all', $post_id ) );
				tribe_set_var( $cache_key_links, $cache_links );
			}

			if ( $echo ) {
				echo $cache_links[ $cache_id ];
			}

			return $cache_links[ $cache_id ];
		}
	}

	// show user front-end settings only if ECP is active
	function tribe_recurring_instances_toggle( $postId = null ) {
			$hide_recurrence = ( ! empty( $_REQUEST['tribeHideRecurrence'] ) && $_REQUEST['tribeHideRecurrence'] == '1' ) || ( empty( $_REQUEST['tribeHideRecurrence'] ) && empty( $_REQUEST['action'] ) && tribe_get_option( 'hideSubsequentRecurrencesDefault', false ) ) ? '1' : false;
		if ( ! tribe_is_week() && ! tribe_is_month() ) {
			echo '<span class="tribe-events-user-recurrence-toggle">';
				echo '<label for="tribeHideRecurrence">';
					echo '<input type="checkbox" name="tribeHideRecurrence" value="1" id="tribeHideRecurrence" ' . checked( $hide_recurrence, 1, false ) . '>' . sprintf( __( 'Show only the first upcoming instance of recurring %s', 'tribe-events-calendar-pro' ), tribe_get_event_label_plural_lowercase() );
				echo '</label>';
			echo '</span>';
		}
	}

	/**
	 * Event Custom Fields
	 *
	 * Get an array of custom fields
	 *
	 * @param int $postId (optional)
	 *
	 * @return array $data of custom fields
	 * @todo move logic to Tribe__Events__Pro__Custom_Meta class
	 */
	function tribe_get_custom_fields( $postId = null ) {
		$postId = Tribe__Events__Main::postIdHelper( $postId );
		$data = array();
		$customFields = tribe_get_option( 'custom-fields', false );
		if ( is_array( $customFields ) ) {
			foreach ( $customFields as $field ) {
				$meta = str_replace( '|', ', ', get_post_meta( $postId, $field['name'], true ) );
				if ( $field['type'] == 'url' && ! empty( $meta ) ) {
					$url_label = $meta;
					$parseUrl = parse_url( $meta );
					if ( empty( $parseUrl['scheme'] ) ) {
						$meta = "http://$meta";
					}

					/**
					 * Filter the target attribute for the event website link
					 *
					 * @since 5.1.0
					 *
					 * @param string the target attribute string. Defaults to "_self".
					 */
					$target = apply_filters( 'tribe_get_event_website_link_target', '_self' );

					/**
					 * Filter the website link label
					 *
					 * @since 3.0
					 *
					 * @param string the link label/text.
					 */
					$label  = apply_filters( 'tribe_get_event_website_link_label', $url_label );

					$meta   = sprintf( '<a href="%s" target="%s">%s</a>',
						esc_url( $meta ),
						esc_attr( $target ),
						esc_html( $label )
					);
				}

				// Display $meta if not empty - making a special exception for (string) '0'
				// which in this context should be considered a valid, non-empty value
				if ( $meta || '0' === $meta ) {
					$data[ esc_html( $field['label'] ) ] = $meta; // $meta has been through wp_kses - links are allowed
				}
			}
		}

		return apply_filters( 'tribe_get_custom_fields', $data );
	}

	/**
	 * Displays the saved organizer
	 * Used in the settings screen
	 *
	 * @return void
	 * @deprecated
	 * @todo move this to the settings classes and remove
	 */
	function tribe_display_saved_organizer() {
		$current_organizer_id = Tribe__Events__Main::instance()->defaults()->organizer_id();
		$current_organizer = ( $current_organizer_id != 'none' && $current_organizer_id != 0 && $current_organizer_id ) ? tribe_get_organizer( $current_organizer_id ) : __( 'No default set', 'tribe-events-calendar-pro' );
		$current_organizer = esc_html( $current_organizer );
		echo '<p class="tribe-field-indent description">' . sprintf( __( 'The current default organizer is: %s', 'tribe-events-calendar-pro' ), '<strong>' . $current_organizer . '</strong>' ) . '</p>';
	}

	/**
	 * Displays the saved venue
	 * Used in the settings screen
	 *
	 * @return void
	 * @deprecated
	 * @todo move this to the settings classes and remove
	 */
	function tribe_display_saved_venue() {
		$current_venue_id = Tribe__Events__Main::instance()->defaults()->venue_id();
		$current_venue = ( $current_venue_id != 'none' && $current_venue_id != 0 && $current_venue_id ) ? tribe_get_venue( $current_venue_id ) : __( 'No default set', 'tribe-events-calendar-pro' );
		$current_venue = esc_html( $current_venue );
		echo '<p class="tribe-field-indent tribe-field-description description">' . sprintf( __( 'The current default venue is: %s', 'tribe-events-calendar-pro' ), '<strong>' . $current_venue . '</strong>' ) . '</p>';
	}

	/**
	 * Displays the saved address
	 * Used in the settings screen
	 *
	 * @return void
	 * @deprecated
	 * @todo move this to the settings classes and remove
	 */
	function tribe_display_saved_address() {
		$option = Tribe__Events__Main::instance()->defaults()->address();
		$option = empty( $option ) ? __( 'No default set', 'tribe-events-calendar-pro' ) : $option;
		$option = esc_html( $option );
		echo '<p class="tribe-field-indent tribe-field-description venue-default-info description">' . sprintf( __( 'The current default address is: %s', 'tribe-events-calendar-pro' ), '<strong>' . $option . '</strong>' ) . '</p>';
	}

	/**
	 * Displays the saved city
	 * Used in the settings screen
	 *
	 * @return void
	 * @deprecated
	 * @todo move this to the settings classes and remove
	 */
	function tribe_display_saved_city() {
		$option = Tribe__Events__Main::instance()->defaults()->city();
		$option = empty( $option ) ? __( 'No default set', 'tribe-events-calendar-pro' ) : $option;
		$option = esc_html( $option );
		echo '<p class="tribe-field-indent tribe-field-description venue-default-info description">' . sprintf( __( 'The current default city is: %s', 'tribe-events-calendar-pro' ), '<strong>' . $option . '</strong>' ) . '</p>';
	}

	/**
	 * Displays the saved state
	 * Used in the settings screen
	 *
	 * @return void
	 * @deprecated
	 * @todo move this to the settings classes and remove
	 */
	function tribe_display_saved_state() {
		$option = Tribe__Events__Main::instance()->defaults()->state();
		$option = empty( $option ) ? __( 'No default set', 'tribe-events-calendar-pro' ) : $option;
		$option = esc_html( $option );
		echo '<p class="tribe-field-indent tribe-field-description venue-default-info description tribe-saved-state">' . sprintf( __( 'The current default state/province is: %s', 'tribe-events-calendar-pro' ), '<strong>' . $option . '</strong>' ) . '</p>';
	}

	/**
	 * Displays the saved province
	 * Used in the settings screen
	 *
	 * @return void
	 * @deprecated
	 * @todo move this to the settings classes and remove
	 */
	function tribe_display_saved_province() {
		$option = Tribe__Events__Main::instance()->defaults()->province();
		$option = empty( $option ) ? __( 'No default set', 'tribe-events-calendar-pro' ) : $option;
		$option = esc_html( $option );
		echo '<p class="tribe-field-indent tribe-field-description venue-default-info description tribe-saved-province">' . sprintf( __( 'The current default state/province is: %s', 'tribe-events-calendar-pro' ), '<strong>' . $option . '</strong>' ) . '</p>';
	}

	/**
	 * Displays the saved zip
	 * Used in the settings screen
	 *
	 * @return void
	 * @deprecated
	 * @todo move this to the settings classes and remove
	 */
	function tribe_display_saved_zip() {
		$option = Tribe__Events__Main::instance()->defaults()->zip();
		$option = empty( $option ) ? __( 'No default set', 'tribe-events-calendar-pro' ) : $option;
		$option = esc_html( $option );
		echo '<p class="tribe-field-indent tribe-field-description venue-default-info description">' . sprintf( __( 'The current default postal code/zip code is: %s', 'tribe-events-calendar-pro' ), '<strong>' . $option . '</strong>' ) . '</p>';
	}

	/**
	 * Displays the saved country
	 * Used in the settings screen
	 *
	 * @return void
	 * @deprecated
	 * @todo move this to the settings classes and remove
	 */
	function tribe_display_saved_country() {
		$option = Tribe__Events__Main::instance()->defaults()->country();
		$option = empty( $option[1] ) ? __( 'No default set', 'tribe-events-calendar-pro' ) : $option[1];
		$option = esc_html( $option );
		echo '<p class="tribe-field-indent tribe-field-description venue-default-info description">' . sprintf( __( 'The current default country is: %s', 'tribe-events-calendar-pro' ), '<strong>' . $option . '</strong>' ) . '</p>';
	}

	/**
	 * Displays the saved phone
	 * Used in the settings screen
	 *
	 * @return void
	 * @deprecated
	 * @todo move this to the settings classes and remove
	 */
	function tribe_display_saved_phone() {
		$option = Tribe__Events__Main::instance()->defaults()->phone();
		$option = empty( $option ) ? __( 'No default set', 'tribe-events-calendar-pro' ) : $option;
		$option = esc_html( $option );
		echo '<p class="tribe-field-indent tribe-field-description venue-default-info description">' . sprintf( __( 'The current default phone is: %s', 'tribe-events-calendar-pro' ), '<strong>' . $option . '</strong>' ) . '</p>';
	}

	/**
	 * Returns the formatted and converted distance from the db (always in kms.) to the unit selected
	 * by the user in the 'defaults' tab of our settings.
	 *
	 * @param $distance_in_kms
	 *
	 * @return mixed
	 */
	function tribe_get_distance_with_unit( $distance_in_kms ) {

		$unit     = Tribe__Settings_Manager::get_option( 'geoloc_default_unit', 'miles' );
		$distance = round( tribe_convert_units( $distance_in_kms, 'kms', $unit ), 2 );

		return apply_filters( 'tribe_get_distance_with_unit', $distance . ' ' . $unit, $distance, $distance_in_kms, $unit );
	}

	/**
	 * Returns an events distance from location search term
	 *
	 * @return string
	 * @todo move tags to template
	 *
	 */
	function tribe_event_distance() {
		global $post;
		if ( ! empty( $post->distance ) ) {
			return '<span class="tribe-events-distance">'. tribe_get_distance_with_unit( $post->distance ) .'</span>';
		}
	}

	/**
	 *
	 * Converts units. Uses tribe_convert_$unit_to_$unit_ratio filter to get the ratio.
	 *
	 * @param $value
	 * @param $unit_from
	 * @param $unit_to
	 */
	function tribe_convert_units( $value, $unit_from, $unit_to ) {

		if ( $unit_from === $unit_to ) {
			return $value;
		}

		$filter = sprintf( 'tribe_convert_%s_to_%s_ratio', $unit_from, $unit_to );
		$ratio  = apply_filters( $filter, 0 );

		// if there's not filter for this conversion, let's return the original value
		if ( empty( $ratio ) ) {
			return $value;
		}

		return ( $value * $ratio );

	}

	/**
	 * Get the first day of the week from a provided date
	 *
	 * @param null|mixed $date  given date or week # (week # assumes current year)
	 *
	 * @return string
	 * @todo move logic to Tribe__Date_Utils
	 */
	function tribe_get_first_week_day( $date = null ) {

		$wp_query = tribe_get_global_query_object();

		$offset = 7 - get_option( 'start_of_week', 0 );

		if ( tribe_is_ajax_view_request() ) {

			$date = is_null( $date ) ? $_REQUEST['eventDate'] : $date;

			// get the first value if we receiv an array
			$date = is_array( $date ) ? Tribe__Utils__Array::get( $date, array( 0 ) ) : $date;

		} else {
			$date = is_null( $date ) && ! is_null( $wp_query ) ? $wp_query->get( 'start_date' ) : $date;
		}

		$timezone = Tribe__Timezones::wp_timezone_string();
		$timezone = Tribe__Timezones::generate_timezone_string_from_utc_offset( $timezone );

		try {
			$date = new DateTime( $date, new DateTimeZone( $timezone ) );
		} catch ( exception $e ) {
			$date = new DateTime( current_time( 'Y-m-d' ), new DateTimeZone( $timezone ) );
		}

		// Clone to avoid altering the original date
		$r = clone $date;
		$r->modify( - ( ( $date->format( 'w' ) + $offset ) % 7 ) . 'days' );

		return apply_filters( 'tribe_get_first_week_day', $r->format( 'Y-m-d' ) );
	}

	/**
	 * Get the last day of the week from a provided date
	 *
	 * @param string|int $date_or_int A given date or week # (week # assumes current year)
	 * @param bool $by_date determines how to parse the date vs week provided
	 * @param int $first_day sets start of the week (offset) respectively, accepts 0-6
	 *
	 * @return DateTime
	 */
	function tribe_get_last_week_day( $date_or_int, $by_date = true ) {
		return apply_filters( 'tribe_get_last_week_day', date( 'Y-m-d', strtotime( tribe_get_first_week_day( $date_or_int, $by_date ) . ' +7 days' ) ) );
	}

	/**
	 * Week View Test
	 *
	 * Returns true when on the "real" Week View itself, but not in other secondary instances of the
	 * Week View like instance of the [tribe_events] shortcode.
	 *
	 * @return bool
	 */
	function tribe_is_week() {
		$is_week = ( 'week' === Tribe__Events__Main::instance()->displaying ) ? true : false;

		/**
		 * Allows filtering of the tribe_is_week boolean value.
		 *
		 * @since 4.4.26 Added inline documentation for this filter.
		 *
		 * @param boolean $is_week Whether you're on the main Week View or not
		 * @param Tribe__Events__Main $tribe_ecp The current Tribe__Events__Main instance.
		 */
		return apply_filters( 'tribe_is_week', $is_week, Tribe__Events__Main::instance() );
	}

	/**
	 * Photo View Test
	 *
	 * Returns true when on the "real" Photo View itself, but not in other secondary instances of the
	 * Photo View like instance of the [tribe_events] shortcode.
	 *
	 * @return bool
	 */
	function tribe_is_photo() {
		$is_photo = ( 'photo' === Tribe__Events__Main::instance()->displaying ) ? true : false;

		/**
		 * Allows filtering of the tribe_is_photo boolean value.
		 *
		 * @since 4.4.26 Added inline documentation for this filter.
		 *
		 * @param boolean $is_photo Whether you're on the main Photo View or not
		 * @param Tribe__Events__Main $tribe_ecp The current Tribe__Events__Main instance.
		 */
		return apply_filters( 'tribe_is_photo', $is_photo, Tribe__Events__Main::instance() );
	}

	/**
	 * Map View Test
	 *
	 * Returns true when on the "real" Map View itself, but not in other secondary instances of the
	 * Map View like instance of the [tribe_events] shortcode.
	 *
	 * @return bool
	 */
	function tribe_is_map() {
		$tribe_ecp = Tribe__Events__Main::instance();
		$is_map    = ( 'map' === $tribe_ecp->displaying ) ? true : false;

		/**
		 * Allows filtering of the tribe_is_map boolean value.
		 *
		 * @since 4.4.26 Added inline documentation for this filter.
		 *
		 * @param boolean $is_map Whether you're on the main Map View or not
		 * @param Tribe__Events__Main $tribe_ecp The current Tribe__Events__Main instance.
		 */
		return apply_filters( 'tribe_is_map', $is_map, $tribe_ecp );
	}

	/**
	 * Get last week permalink by provided date (7 days offset)
	 *
	 * @uses tribe_get_week_permalink
	 *
	 * @param string $week
	 * @param bool $is_current
	 *
	 * @return string $permalink
	 * @todo move logic to week template class
	 */
	function tribe_get_last_week_permalink( $week = null ) {
		$week = ! empty( $week ) ? $week : tribe_get_first_week_day();
		if ( PHP_INT_SIZE <= 4 ) {
			if ( date( 'Y-m-d', strtotime( $week ) ) < '1902-01-08' ) {
				throw new OverflowException( __( 'Date out of range.', 'tribe-events-calendar-pro' ) );
			}
		}

		$week = date( 'Y-m-d', strtotime( $week . ' -1 week') );

		return apply_filters( 'tribe_get_last_week_permalink', tribe_get_week_permalink( $week ) );
	}

	/**
	 * Get next week permalink by provided date (7 days offset)
	 *
	 * @uses tribe_get_week_permalink
	 *
	 * @param string $week
	 *
	 * @return string $permalink
	 * @todo move logic to week template class
	 */
	function tribe_get_next_week_permalink( $week = null ) {
		$week = ! empty( $week ) ? $week : tribe_get_first_week_day();
		if ( PHP_INT_SIZE <= 4 ) {
			if ( date( 'Y-m-d', strtotime( $week ) ) > '2037-12-24' ) {
				throw new OverflowException( __( 'Date out of range.', 'tribe-events-calendar-pro' ) );
			}
		}
		$week = date( 'Y-m-d', strtotime( $week . ' +1 week' ) );

		return apply_filters( 'tribe_get_next_week_permalink', tribe_get_week_permalink( $week ) );
	}

	/**
	 * Get the week view permalink.
	 *
	 * @param string        $week
	 * @param bool|int|null $term
	 *
	 * @return string $permalink
	 */
	function tribe_get_week_permalink( $week = null, $term = null ) {
		$week      = is_null( $week ) ? false : date( 'Y-m-d', strtotime( $week ) );
		$permalink = Tribe__Events__Main::instance()->getLink( 'week', $week, $term );

		/**
		 * Provides an opportunity to modify the week view permalink.
		 *
		 * @var string $permalink
		 * @var string $week
		 * @var mixed  $term
		 */
		return apply_filters( 'tribe_get_week_permalink', $permalink, $week, $term );
	}


	/**
	 * Get the photo permalink.
	 *
	 * @param bool|int|null $term
	 *
	 * @return string $permalink
	 */
	function tribe_get_photo_permalink( $term = null ) {
		$permalink = Tribe__Events__Main::instance()->getLink( 'photo', null, $term );

		/**
		 * Provides an opportunity to modify the photo view permalink.
		 *
		 * @var string $permalink
		 * @var mixed  $term
		 */
		return apply_filters( 'tribe_get_photo_view_permalink', $permalink, $term );
	}

	/**
	 * Echos the single events page related events boxes.
	 * @return void.
	 */
	function tribe_single_related_events( ) {
		tribe_get_template_part( 'pro/related-events' );
	}

	/**
	 * Template tag to get related posts for the current post.
	 *
	 * @param int $count number of related posts to return.
	 * @param int|obj $post the post to get related posts to, defaults to current global $post
	 *
	 * @return array the related posts.
	 */
	function tribe_get_related_posts( $count = 3, $post = false ) {
		$post_id = Tribe__Events__Main::postIdHelper( $post );

		$args = [
			'posts_per_page' => $count,
			'start_date' => 'now',
		];
		$posts = [];

		$orm_args = tribe_events()->filter_by_related_to( $post_id );

		if ( $orm_args ) {
			$args = array_merge( $args, $orm_args );

			if ( $args ) {
				$posts = tribe_get_events( $args );
			}
		}

		/**
		 * Filter the related posts for the current post.
		 *
		 * @param array $posts   The related posts.
		 * @param int   $post_id Current Post ID.
		 * @param array $args    Query arguments.
		 *
		 * @since 3.2
		 */
		return apply_filters( 'tribe_get_related_posts', $posts, $post_id, $args );
	}

	/**
	 * Shows the recurring event info in a tooltip, including details of the start/end date/time.
	 *
	 * @param int $post_id
	 *
	 * @return string
	 */
	function tribe_events_recurrence_tooltip( $post_id = null ) {

		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}

		$tooltip = '';

		if ( tribe_is_recurring_event( $post_id ) ) {
			$tooltip .= '<div class="recurringinfo">';
			$tooltip .= '<div class="event-is-recurring">';
			$tooltip .= '<span class="tribe-events-divider">|</span>';
			$tooltip .= sprintf( esc_html__( 'Recurring %s', 'tribe-events-calendar-pro' ), tribe_get_event_label_singular() );
			$tooltip .= sprintf( ' <a href="%s">%s</a>',
				esc_url( tribe_all_occurences_link( $post_id, false ) ),
				esc_html__( '(See all)', 'tribe-events-calendar-pro' )
			);
			$tooltip .= '<div id="tribe-events-tooltip-'. $post_id .'" class="tribe-events-tooltip recurring-info-tooltip">';
			$tooltip .= '<div class="tribe-events-event-body">';
			$tooltip .= tribe_get_recurrence_text( $post_id );
			$tooltip .= '</div>';
			$tooltip .= '<span class="tribe-events-arrow"></span>';
			$tooltip .= '</div>';
			$tooltip .= '</div>';
			$tooltip .= '</div>';
		}

		/**
		 * Allows filtering the recurrence tooltip HTML for the specified event.
		 *
		 * @param string $tooltip The HTML of the recurrence tooltip for the specified event.
		 * @param int $post_id The post ID of the event.
		 */
		return apply_filters( 'tribe_events_recurrence_tooltip', $tooltip, $post_id );
	}

	/*
	 * Returns or echoes a url to a file in the Events Calendar PRO plugin resources directory
	 *
	 * @param string $resource the filename of the resource
	 * @param bool $echo whether or not to echo the url
	 * @return string
	 **/
	function tribe_events_pro_resource_url( $resource, $echo = false ) {
		$extension = pathinfo( $resource, PATHINFO_EXTENSION );
		$resources_path = 'src/resources/';
		switch ( $extension ) {
			case 'css':
				$resource_path = $resources_path .'css/';
				break;
			case 'js':
				$resource_path = $resources_path .'js/';
				break;
			case 'scss':
				$resource_path = $resources_path .'scss/';
				break;
			default:
				$resource_path = $resources_path;
				break;
		}

		$path = $resource_path . $resource;
		$url = apply_filters( 'tribe_events_pro_resource_url', trailingslashit( Tribe__Events__Pro__Main::instance()->pluginUrl ) . $path, $resource );
		if ( $echo ) {
			echo $url;
		}

		return $url;
	}

	/**
	 * Output the upcoming events associated with a venue
	 *
	 * @return void
	 */
	function tribe_organizer_upcoming_events( $post_id = false ) {

		$post_id = Tribe__Events__Main::postIdHelper( $post_id );

		if ( $post_id ) {

			$args = array(
				'organizer'      => $post_id,
				'eventDisplay'   => 'list',
				'posts_per_page' => apply_filters( 'tribe_events_single_organizer_posts_per_page', 100 ),
				'starts_after'   => 'now',
			);

			$html = tribe_include_view_list( $args );

			return apply_filters( 'tribe_organizer_upcoming_events', $html );
		}
	}

	/**
	 * Returns the next upcoming event in a recurring series from the /all/ URL
	 * if one can be found, else returns null.
	 *
	 * @since 4.2
	 *
	 * @param string $url URL of the recurring series
	 *
	 * @return int|null
	 */
	function tribe_get_upcoming_recurring_event_id_from_url( $url ) {
		$path = @parse_url( $url );

		// Ensure we were able to parse the URL and have an actual path to look at (could be just a scheme, host and query etc)
		if ( empty( $path ) || ! isset( $path[ 'path' ] ) ) {
			return null;
		}

		$path = trim( $path['path'], '/' );
		$path = explode( '/', $path );

		// We expect $path to contain at least 3 elements (could be more, for subdir installations etc)
		if ( count( $path ) < 3 ) {
			return null;
		}

		// Grab the post name from the /all/ URL
		$post_name = $path[ count( $path ) - 2 ];

		// Fetch the parent (even if it is in the past, hence 'custom')
		$sequence_parent = tribe_get_events( array(
			'name'           => $post_name,
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'eventDisplay'   => 'custom',
		) );

		if ( empty( $sequence_parent ) ) {
			return null;
		}

		$parent = current( $sequence_parent );

		// Ensure we are indeed looking at an actual recurring event
		if ( ! tribe_is_recurring_event( $parent->ID ) ) {
			return null;
		}

		// Is the parent itself the next upcoming instance? If so, we can return its ID
		if ( $parent->_EventEndDateUTC >= current_time( 'mysql' ) ) {
			return $parent->ID;
		}

		// Otherwise look for upcoming children of this event
		$upcoming_child = tribe_get_events( array(
			'post_parent'    => $parent->ID,
			'posts_per_page' => 1,
		) );

		if ( empty( $upcoming_child ) ) {
			return null;
		}

		return current( $upcoming_child )->ID;
	}

}

if ( ! function_exists( 'tribe_get_mobile_default_view' ) ) {
	/**
	 * Allow users to fetch default view For Mobile
	 *
	 * @category Events
	 *
	 * @return int
	 */
	function tribe_get_mobile_default_view() {
		$default = Tribe__Events__Main::instance()->default_view();

		// If there isn't a default mobile set, it will get the default from the normal settings
		$default_view = tribe_get_option( 'mobile_default_view', 'default' );

		if ( 'default' === $default_view ) {
			$default_view = $default;
		}

		/**
		 * Allow users to filter which is the default Mobile view globally
		 *
		 * @param string $default_view The default view set
		 */
		return apply_filters( 'tribe_events_mobile_default_view', $default_view );
	}
}//end if

