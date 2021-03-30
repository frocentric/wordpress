<?php
/**
 * Provides methods for fetching events via the Event_Query control group.
 *
 * @since   5.4.0
 *
 * @package Tribe\Events\Pro\Integrations\Elementor\Widgets\Traits
 */

namespace Tribe\Events\Pro\Integrations\Elementor\Widgets\Traits;

use Elementor\Controls_Manager;
use Tribe\Events\Pro\Integrations\Elementor\Controls\Groups;
use Tribe__Utils__Array as Arr;

/**
 * Trait Categories
 *
 * @since   5.4.0
 *
 * @package Tribe\Events\Pro\Integrations\Elementor\Widgets\Traits
 */
trait Event_Query {
	/**
	 * @var string Event Query control prefix.
	 */
	protected $event_query_control_prefix = 'event_query';

	/**
	 * @var bool Whether or not we should default the repository to the current date/time.
	 */
	protected $default_repository_to_current_date = true;

	/**
	 * Method for adding the event_query section in the widget controls.
	 *
	 * @since 5.4.0
	 */
	public function add_event_query_section() {
		$this->start_controls_section(
			'event_query_section',
			[
				'label' => __( 'Event Query', 'tribe-events-calendar-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_group_control( Groups\Event_Query::get_type(), [ 'name' => $this->event_query_control_prefix ] );

		$this->end_controls_section();
	}

	/**
	 * Translates prefixed event_query settings to simpler values.
	 *
	 * @since 5.4.0
	 *
	 * @param array<string> $settings Widget settings.
	 *
	 * @return array
	 */
	public function get_event_query_settings( $settings = [] ) {
		$query_settings = [];
		$prefix_length  = strlen( $this->event_query_control_prefix . '_' );

		foreach ( $settings as $key => $value ) {
			if ( 0 !== strpos( $key, $this->event_query_control_prefix ) ) {
				continue;
			}

			$query_settings[ substr( $key, $prefix_length ) ] = $value;
		}

		return $query_settings;
	}

	/**
	 * Gets the event repository based on widget settings.
	 *
	 * @since 5.4.0
	 *
	 * @param array<string> $settings Widget settings.
	 *
	 * @return \Tribe__Events__Repositories__Event
	 */
	public function build_event_repository( $settings = [] ) {
		$repository = tribe_events();

		/*---------------------------------
		 * Handle meta
		 *--------------------------------*/

		if ( Arr::get( $settings, 'search' ) ) {
			$repository->search( $settings['search'] );
		}

		if ( Arr::get( $settings, 'category' ) ) {
			$repository->where( 'category', $settings['category'] );
		}

		if ( Arr::get( $settings, 'post_tag' ) ) {
			$repository->where( 'post_tag', $settings['post_tag'] );
		}

		$featured = Arr::get( $settings, 'featured' );

		if ( $featured && 'include' !== $featured ) {
			$repository->where( 'featured', 'only' == $featured );
		}

		$all_day = Arr::get( $settings, 'all_day' );
		if ( $all_day && 'include' !== $all_day ) {
			$repository->where( 'all_day', 'only' == $all_day );
		}

		$multi_day = Arr::get( $settings, 'multiday' );
		if ( $multi_day && 'include' !== $multi_day ) {
			$repository->where( 'multiday', 'only' == $multi_day );
		}

		$series = Arr::get( $settings, 'series' );
		if ( $series && 'include' !== $series ) {
			$repository->where( 'series', 'only' == $series );

			if ( 'only' === $series ) {
				$repository->where( 'meta_not_equals', '_EventRecurrence', 'a:3:{s:5:"rules";a:0:{}s:10:"exclusions";a:0:{}s:11:"description";N;}' );
			}
		}

		$has_geoloc = Arr::get( $settings, 'has_geoloc' );
		if ( $has_geoloc && 'include' !== $has_geoloc ) {
			$repository->where( 'has_geoloc', 'only' == $has_geoloc );
		}

		/*----------------------------------------------------
		 * If the following do not manipulate the repository,
		 * we need to establish a date-based default.
		 *----------------------------------------------------*/

		if ( 'current' === Arr::get( $settings, 'id_selection' ) ) {
			global $post;

			if ( isset( $post->ID ) ) {
				$repository->in( absint( $post->ID ) );
				$this->default_repository_to_current_date = false;
			}
		}

		if ( Arr::get( $settings, 'id' ) ) {
			$repository->in( absint( $settings['id'] ) );
			$this->default_repository_to_current_date = false;
		}

		if ( Arr::get( $settings, 'slug' ) ) {
			$repository->where( 'name', $settings['slug'] );
			$this->default_repository_to_current_date = false;
		}

		$repository = $this->setup_repository_dates( $repository, $settings, 'start' );
		$repository = $this->setup_repository_dates( $repository, $settings, 'end' );

		if ( $this->default_repository_to_current_date ) {
			$repository = $repository->where( 'starts_on_or_after', date( \Tribe__Date_Utils::DBDATETIMEFORMAT, time() ) );
		}

		return $repository;
	}

	/**
	 * Takes widget settings and adds where conditionals for dates.
	 *
	 * @since 5.4.0
	 *
	 * @param \Tribe__Events__Repositories__Event $repository Event Repository.
	 * @param array<string> $settings Widget settings.
	 * @param string $which Which date type to analyze. 'start' or 'end'.
	 *
	 * @return array
	 */
	protected function setup_repository_dates( $repository, $settings = [], $which = 'start' ) {
		$when = Arr::get( $settings, "{$which}s_when" );

		if ( ! $when ) {
			return $repository;
		}

		$method = Arr::get( $settings, "{$which}s_method" );
		$suffix = 'custom' === $method ? '_custom' : '';

		$date       = Arr::get( $settings, "{$which}_date{$suffix}" );
		$date_start = Arr::get( $settings, "{$which}_date_start{$suffix}" );
		$date_end   = Arr::get( $settings, "{$which}_date_end{$suffix}" );

		switch ( $when ) {
			case 'on':
				if ( $date ) {
					$date = date( \Tribe__Date_Utils::DBDATEFORMAT, strtotime( $date ) );
					$date_start = $date . ' 00:00:00';
					$date_end   = $date . ' 23:59:59';
					$repository->where( "{$which}s_between", $date_start, $date_end );
					$this->default_repository_to_current_date = false;
				}
				break;
			case 'before':
				if ( $date ) {
					$repository->where( "{$which}s_before", $date );
					$this->default_repository_to_current_date = false;
				}
				break;
			case 'after':
				if ( $date ) {
					$repository->where( "{$which}s_after", $date );
					$this->default_repository_to_current_date = false;
				}
				break;
			case 'on_or_after':
				if ( $date ) {
					$repository->where( "{$which}s_on_or_after", $date );
					$this->default_repository_to_current_date = false;
				}
				break;
			case 'on_or_before':
				if ( $date ) {
					$repository->where( "{$which}s_on_or_before", $date );
					$this->default_repository_to_current_date = false;
				}
				break;
			case 'between':
				if ( $date_start && $date_end ) {
					$repository->where( "{$which}s_between", $date_start, $date_end );
					$this->default_repository_to_current_date = false;
				}
				break;
		}

		return $repository;
	}

	/**
	 * Sets the id field from the repository results if it isn't already set.
	 *
	 * @since 5.4.0
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return array
	 */
	public function set_id_from_repository_if_unset( $settings = [] ) {
		if ( ! Arr::get( $settings, 'id' ) ) {
			$repository = $this->build_event_repository( $settings );
			$posts      = $repository->all();

			if ( ! empty( $posts ) ) {
				$settings['id'] = $posts[0]->ID;
			}
		}

		return $settings;
	}
}
