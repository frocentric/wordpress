<?php
/**
 * The Map View.
 *
 * @package Tribe\Events\Pro\Views\V2\Views
 * @since 4.7.7
 */

namespace Tribe\Events\Pro\Views\V2\Views;

use Tribe\Events\Pro\Views\V2\Maps;
use Tribe\Events\Views\V2\Messages;
use Tribe\Events\Views\V2\Utils;
use Tribe\Events\Views\V2\View;
use Tribe\Events\Views\V2\Views\Traits\List_Behavior;
use Tribe__Events__Main as TEC;
use Tribe__Utils__Array as Arr;

class Map_View extends View {
	use List_Behavior;

	/**
	 * Slug for this view
	 *
	 * @since 4.7.7
	 *
	 * @var string
	 */
	protected $slug = 'map';

	/**
	 * Visibility for this view.
	 *
	 * @since 4.7.7
	 * @since 4.7.9 Made the property static.
	 *
	 * @var bool
	 */
	protected static $publicly_visible = true;

	/**
	 * Indicates Map View supports the date as a query argument appended to its URL, not as part of a "pretty" URL.
	 *
	 * @var bool
	 */
	protected static $date_in_url = false;

	/**
	 * Map_View constructor.
	 *
	 * @since 5.0.1
	 *
	 * {@inheritDoc}
	 */
	public function __construct( Messages $messages = null ) {
		parent::__construct($messages);
		$this->rewrite = tribe( 'events.rewrite' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function prev_url( $canonical = false, array $passthru_vars = [] ) {
		$cache_key = __METHOD__ . '_' . md5( wp_json_encode( func_get_args() ) );

		if ( isset( $this->cached_urls[ $cache_key ] ) ) {
			return $this->cached_urls[ $cache_key ];
		}

		$current_page = (int) $this->context->get( 'page', 1 );
		$display      = $this->context->get( 'event_display_mode', $this->slug );

		if ( 'past' === $display ) {
			$url = parent::next_url( $canonical, [ Utils\View::get_past_event_display_key() => 'past' ] );
		} elseif ( $current_page > 1 ) {
			$url = parent::prev_url( $canonical );
		} else {
			$url = $this->get_past_url( $canonical );
		}

		$url = $this->filter_prev_url( $canonical, $url );

		$this->cached_urls[ $cache_key ] = $url;

		return $url;
	}

	/**
	 * {@inheritDoc}
	 */
	public function next_url( $canonical = false, array $passthru_vars = [] ) {
		$cache_key = __METHOD__ . '_' . md5( wp_json_encode( func_get_args() ) );

		if ( isset( $this->cached_urls[ $cache_key ] ) ) {
			return $this->cached_urls[ $cache_key ];
		}

		$current_page = (int) $this->context->get( 'page', 1 );
		$display      = $this->context->get( 'event_display_mode', $this->slug );

		if ( $this->slug === $display || 'default' === $display ) {
			$url = parent::next_url( $canonical );
		} elseif ( $current_page > 1 ) {
			$url = parent::prev_url( $canonical, [ Utils\View::get_past_event_display_key() => 'past' ] );
		} else {
			$url = $this->get_upcoming_url( $canonical );
		}

		$url = $this->filter_next_url( $canonical, $url );

		$this->cached_urls[ $cache_key ] = $url;

		return $url;
	}

	/**
	 * Return the URL to a page of past events.
	 *
	 * @since 4.7.8
	 *
	 * @param bool $canonical Whether to return the canonical version of the URL or the normal one.
	 * @param int  $page The page to return the URL for.
	 *
	 * @return string The URL to the past URL page, if available, or an empty string.
	 */
	protected function get_past_url( $canonical = false, $page = 1 ) {
		$default_date   = 'now';
		$date           = $this->context->get( 'event_date', $default_date );
		$event_date_var = $default_date === $date ? '' : $date;

		$past = tribe_events()->by_args( $this->setup_repository_args( $this->context->alter( [
			'event_display_mode' => 'past',
			'paged'              => $page,
		] ) ) );

		if ( $past->count() > 0 ) {
			$event_display_key = Utils\View::get_past_event_display_key();
			$query_args        = [
				'post_type'        => TEC::POSTTYPE,
				$event_display_key => 'past',
				'eventDate'        => $event_date_var,
				$this->page_key    => $page,
				'tribe-bar-search' => $this->context->get( 'keyword' ),
			];

			$query_args = $this->filter_query_args( $query_args, $canonical );

			$past_url_object = clone $this->url->add_query_args( array_filter( $query_args ) );

			$past_url = (string) $past_url_object;

			if ( ! $canonical ) {
				return $past_url;
			}

			// We've got rewrite rules handling `eventDate` and `eventDisplay`, but not List. Let's remove it.
			$canonical_url = $this->rewrite->get_clean_url(
				add_query_arg(
					[ 'eventDisplay' => $this->slug ],
					remove_query_arg( [ 'eventDate' ], $past_url )
				)
			);

			// We use the `eventDisplay` query var as a display mode indicator: we have to make sure it's there.
			$url = add_query_arg( [ $event_display_key => 'past' ], $canonical_url );

			// Let's re-add the `eventDate` if we had one and we're not already passing it with one of its aliases.
			if ( ! (
				empty( $event_date_var )
				|| $past_url_object->get_query_arg_alias_of( 'event_date', $this->context )
			) ) {
				$url = add_query_arg( [ 'eventDate' => $event_date_var ], $url );
			}

			return $url;
		}

		return '';
	}

	/**
	 * Return the URL to a page of upcoming events.
	 *
	 * @since 4.7.8
	 *
	 * @param bool $canonical Whether to return the canonical version of the URL or the normal one.
	 * @param int  $page The page to return the URL for.
	 *
	 * @return string The URL to the upcoming URL page, if available, or an empty string.
	 */
	protected function get_upcoming_url( $canonical = false, $page = 1 ) {
		$default_date   = 'now';
		$date           = $this->context->get( 'event_date', $default_date );
		$event_date_var = $default_date === $date ? '' : $date;

		$upcoming = tribe_events()->by_args( $this->setup_repository_args( $this->context->alter( [
			'eventDisplay' => $this->slug,
			'paged'        => $page,
		] ) ) );

		if ( $upcoming->count() > 0 ) {
			$query_args = [
				'post_type'        => TEC::POSTTYPE,
				'eventDisplay'     => $this->slug,
				$this->page_key    => $page,
				'eventDate'        => $event_date_var,
				'tribe-bar-search' => $this->context->get( 'keyword' ),
			];

			$query_args = $this->filter_query_args( $query_args, $canonical );

			$upcoming_url_object = clone $this->url->add_query_args( array_filter( $query_args ) );

			$upcoming_url = (string) $upcoming_url_object;

			if ( ! $canonical ) {
				return $upcoming_url;
			}

			// We've got rewrite rules handling `eventDate`, but not List. Let's remove it to build the URL.
			$url = tribe( 'events.rewrite' )->get_clean_url(
				remove_query_arg( [ 'eventDate', 'tribe_event_display' ], $upcoming_url )
			);

			// Let's re-add the `eventDate` if we had one and we're not already passing it with one of its aliases.
			if ( ! (
				empty( $event_date_var )
				|| $upcoming_url_object->get_query_arg_alias_of( 'event_date', $this->context )
			) ) {
				$url = add_query_arg( [ 'eventDate' => $event_date_var ], $url );
			}

			return $url;
		}

		return '';
	}

	/**
	 * {@inheritDoc}
	 */
	protected function setup_repository_args( \Tribe__Context $context = null ) {
		$context = null !== $context ? $context : $this->context;

		$args = parent::setup_repository_args( $context );

		$context_arr = $context->to_array();

		$date = Arr::get( $context_arr, 'event_date', 'now' );
		$event_display_mode = Arr::get( $context_arr, 'event_display_mode', Arr::get( $context_arr, 'event_display' ), 'current' );

		if ( 'past' !== $event_display_mode ) {
			$args['order']       = 'ASC';
			$args['ends_after'] = $date;
		} else {
			$args['order']       = 'DESC';
			$args['ends_before'] = $date;
		}

		return $args;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function setup_template_vars() {
		$template_vars           = parent::setup_template_vars();
		$geoloc_search           = $this->context->get( 'geoloc_search', false );
		$show_distance           = ! empty( $geoloc_search );
		$template_vars['events'] = $this->maybe_remove_non_venue_events( $template_vars['events'] );

		if ( $show_distance ) {
			$template_vars['events'] = $this->sort_events_by_distance( $template_vars['events'] );
		} else {
			$template_vars['events'] = $this->sort_events_by_display_mode( $template_vars['events'] );
		}

		if ( ! empty( $template_vars['events'] ) && 'past' === $this->context->get( 'event_display_mode' ) ) {
			// Past events are fetched by in DESC start date, but shown in ASC order.
			$template_vars['events'] = array_reverse( $template_vars['events'] );
		}

		$template_vars = tribe( Maps::class )->setup_map_provider( $template_vars );
		$template_vars = $this->setup_events_by_venue( $template_vars );
		$template_vars = $this->setup_datepicker_template_vars($template_vars);

		$template_vars['show_distance'] = $show_distance;
		$template_vars['geoloc_unit']   = $this->setup_geoloc_unit();

		return $template_vars;
	}

	/**
	 * Setup the events by venue for the map view, using the template variables.
	 *
	 * @since  4.7.8
	 *
	 * @param  array $template_vars Previous template variables in which the events by venue will be added to.
	 *
	 * @return array
	 */
	protected function setup_events_by_venue( $template_vars ) {
		$template_vars['events_by_venue'] = [];

		foreach( $template_vars['events'] as $event ) {
			foreach ( $event->venues as $venue ) {
				if ( empty( $template_vars['events_by_venue'][ $venue->ID ] ) ) {
					$geolocation = $venue->geolocation;

					if (
						! isset( $geolocation->latitude, $geolocation->longitude )
						|| ( '' === $geolocation->latitude || '' === $geolocation->longitude )
					) {
						// If the venue is missing the geolocation information, then it's not mappable.
						continue;
					}

					// WP_Post instances will be suppressed by the data filter, so we convert it to an object.
					$template_vars['events_by_venue'][ $venue->ID ]            = (object) [
						'ID' => $venue->ID,
						'geolocation' => $geolocation,
						'post_title' => $venue->post_title,
					];
					$template_vars['events_by_venue'][ $venue->ID ]->event_ids = [];
				}

				$template_vars['events_by_venue'][ $venue->ID ]->event_ids[] = $event->ID;
			}
		}

		return $template_vars;
	}

	/**
	 * Overrides the base implementation to remove notions of a "past" events request on page reset.
	 *
	 * @since 4.7.9
	 */
	protected function on_page_reset() {
		parent::on_page_reset();
		$this->remove_past_query_args();
	}

	/**
	 * Sorts the events by distance, in the specified direction.
	 *
	 * This method relies on geo-location resolution handlers to set the `geoloc_lat` and `geoloc_lng` in the Context,
	 * if not set.
	 *
	 * @since 5.0.0
	 *
	 * @param array  $events    The events to sort, if any.
	 * @param string $direction The direction to sort events in, either `ASC` or `DESC`.
	 *
	 * @return array The sorted list of events if geolocation latitude and longitude information is available, else
	 *               the original list of events.
	 */
	protected function sort_events_by_distance( $events, $direction = 'ASC' ) {
		if ( empty( $events ) || ! is_array( $events ) ) {
			return $events;
		}

		$geo_loc = \Tribe__Events__Pro__Geo_Loc::instance();

		// These should have been set by the location search handlers, if not, bail.
		$lat_from = $this->context->get( 'geoloc_lat', false );
		$lng_from = $this->context->get( 'geoloc_lng', false );

		if ( false === $lat_from && false === $lng_from ) {
			return $events;
		}

		// Assign the distance in Kms.
		$geo_loc->assign_distance_to_posts( $events, $lat_from, $lng_from );
		// Convert the distance to the current unit.
		array_walk( $events, static function ( \WP_Post $event ) {
			$event->distance = tribe_get_distance_with_unit( $event->distance );
		} );

		return wp_list_sort( $events, 'distance', $direction );
	}

	/**
	 * Reverses the events order if looking at the past view.
	 *
	 * @since 5.0.0
	 *
	 * @param array $events An array of events to sort.
	 *
	 * @return array The array of sorted events.
	 */
	protected function sort_events_by_display_mode( $events ) {
		if ( empty( $events ) || ! is_array( $events ) ) {
			return $events;
		}

		$is_past = 'past' === $this->context->get( 'event_display_mode', 'map' );

		if ( ! $is_past ) {
			return $events;
		}

		return array_reverse( $events );
	}

	/**
	 * Determines if we should hide events with no venue.
	 *
	 * @since 5.1.1
	 *
	 * @param array $events
	 * @return array
	 */
	protected function maybe_remove_non_venue_events( $events ) {
		/**
		 * Filter allowing user control over showing of events with no venue.
		 *
		 * @since 5.1.1
		 *
		 * @param boolean $include_no_venue To show or not to show.
		 * @param array $events Array of events to filter.
		 *
		 * @return boolean To show or not to show.
		 */
		$include_no_venue = apply_filters( 'tribe_events_pro_map_view_show_events_with_no_venue', false, $events );

		if ( ! empty( $include_no_venue ) ) {
			return $events;
		}

		return array_filter( $events, static function ( \WP_Post $event ) {
			return ! empty( $event->venues->count() );
		} );
	}

	/**
	 * Returns the localized version of the geo-location unit used to calculate and display distances.
	 *
	 * This value is not filtered here as template vars are already filtered.
	 *
	 * @since 5.0.0
	 *
	 * @return string The localized version of the geo-location unit used to calculate and display distances.
	 */
	protected function setup_geoloc_unit() {
		switch ( tribe_get_option( 'geoloc_default_unit', 'miles' ) ) {
			case 'kms':
				$localized_geoloc_unit = __( 'Kilometers', 'tribe-events-calendar-pro' );
				break;
			default:
			case 'miles':
				$localized_geoloc_unit = __( 'Miles', 'tribe-events-calendar-pro' );
				break;
		}

		return $localized_geoloc_unit;
	}
}
