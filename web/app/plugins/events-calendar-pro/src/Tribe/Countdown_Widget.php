<?php
/*
Event Countdown Widget
*/

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

use Tribe__Date_Utils as Dates;

if ( ! class_exists( 'Tribe__Events__Pro__Countdown_Widget' ) ) {
	class Tribe__Events__Pro__Countdown_Widget extends WP_Widget {

		public function __construct() {
			$widget_ops  = array(
				'classname'   => 'tribe-events-countdown-widget',
				'description' => __( 'Displays the time remaining until a specified event.', 'tribe-events-calendar-pro' ),
			);
			$control_ops = array( 'id_base' => 'tribe-events-countdown-widget' );

			parent::__construct( 'tribe-events-countdown-widget', __( 'Events Countdown', 'tribe-events-calendar-pro' ), $widget_ops, $control_ops );

			// Do not enqueue if the widget is inactive
			if ( is_active_widget( false, false, $this->id_base, true ) || is_customize_preview() ) {
				add_action( 'tribe_events_pro_widget_render', array( 'Tribe__Events__Pro__Widgets', 'enqueue_calendar_widget_styles' ), 100 );
			}
		}

		public function update( $new_instance, $old_instance ) {

			$instance = $old_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['show_seconds'] = ( isset( $new_instance['show_seconds'] ) ? 1 : 0 );

			$instance['type'] = 'single-event';
			if ( isset( $new_instance['type'] ) && in_array( $new_instance['type'], array( 'next-event', 'single-event', 'future-event' ) ) ) {
				$instance['type'] = $new_instance['type'];
			}

			$instance['complete']      = '' === $new_instance['complete'] ? $old_instance['complete'] : $new_instance['complete'];
			$instance['event_ID']      = $instance['event'] = absint( $new_instance['event'] );
			$instance['event_date']    = tribe_get_start_date( $instance['event_ID'], false, Tribe__Date_Utils::DBDATETIMEFORMAT, 'event' );
			$instance['jsonld_enable'] = ( ! empty( $new_instance['jsonld_enable'] ) ? 1 : 0 );

			return $instance;
		}

		public function form( $instance ) {
			$defaults = array(
				'title'         => '',
				'type'          => 'single-event',
				'event'         => null,
				'show_seconds'  => true,
				'complete'      => esc_attr__( 'Hooray!', 'tribe-events-calendar-pro' ),
				'jsonld_enable' => true,

				// Legacy Elements
				'event_ID'   => null,
				'event_date' => null,
			);

			$instance = wp_parse_args( (array) $instance, $defaults );
			if ( empty( $instance['event'] ) ) {
				$instance['event'] = $instance['event_ID'];
			}

			$limit = apply_filters( 'tribe_events_pro_countdown_widget_limit', 250 );
			$paged = apply_filters( 'tribe_events_pro_countdown_widget_paged', 1 );

			/**
			 * Filters which post types are allowed for the widget.
			 *
			 * @param array allowed statuses; default `publish`.
			 *
			 * @since 4.4.11
			 */
			$statuses = apply_filters( 'tribe_events_pro_countdown_widget_allowed_status', array( 'publish' ) );

			$events = tribe_get_events( array(
				'eventDisplay'   => 'list',
				'posts_per_page' => $limit,
				'post_status'    => $statuses,
				'paged'          => $paged,
				'start_date'     => Dates::build_date_object( 'now' ),
			) );

			if ( is_numeric( $instance['event'] ) ) {
				$event = get_post( $instance['event'] );
				if ( $event instanceof WP_Post && ! in_array( $event->ID, wp_list_pluck( $events, 'ID' ) ) ) {
					$event->EventStartDate = tribe_get_start_date( $event->ID, false, Tribe__Date_Utils::DBDATETIMEFORMAT );
					$event->EventEndDate = tribe_get_end_date( $event->ID, false, Tribe__Date_Utils::DBDATETIMEFORMAT );
					$events = array_merge( array( $event ), $events );
				}
			}

			include( Tribe__Events__Pro__Main::instance()->pluginPath . 'src/admin-views/widget-admin-countdown.php' );
		}

		public function widget( $args, $instance ) {
			$defaults = array(
				'title' => null,
				'type' => 'single-event',
				'event' => null,
				'show_seconds' => true,
				'complete' => esc_attr__( 'Hooray!', 'tribe-events-calendar-pro' ),

				// Legacy Elements
				'event_ID' => null,
				'event_date' => null,
			);

			$instance = wp_parse_args( (array) $instance, $defaults );
			tribe_asset_enqueue( 'tribe-events-countdown-widget' );

			/**
			 * Do things pre-render like: optionally enqueue assets if we're not in a sidebar
			 * This has to be done in widget() because we have to be able to access
			 * the queried object for some plugins
			 *
			 * @since 4.4.29
			 *
			 * @param string __CLASS__ the widget class
			 * @param array  $args     the widget args
			 * @param array  $instance the widget instance
			 */
			do_action( 'tribe_events_pro_widget_render', __CLASS__, $args, $instance );

			// Setup required variables
			if ( empty( $instance['event'] ) ) {
				$instance['event'] = $instance['event_ID'];
			}

			$title = apply_filters( 'widget_title', $instance['title'] );

			if ( $instance['complete'] ) {
				$instance['complete'] = '<h3 class="tribe-countdown-complete">' . $instance['complete'] . '</h3>';
			}

			echo $args['before_widget'];
			if ( ! empty( $title ) ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}

			echo $this->get_output( $instance );

			echo $args['after_widget'];
		}

		/**
		 * Get the Output of the Widget based on the Instance from the Database
		 *
		 * @param  array $instance     The Array of arguments that will build the HTML
		 * @param  null $deprecated    Deprecated Argument
		 * @param  null $deprecated_   Deprecated Argument
		 * @param  null $deprecated__  Deprecated Argument
		 * @return string
		 */
		public function get_output( $instance, $deprecated = null, $deprecated_ = null, $deprecated__ = null ) {
			$time = Tribe__Timezones::localize_date( Tribe__Date_Utils::DBDATETIMEFORMAT, current_time( 'timestamp' ) );

			if ( 'next-event' === $instance['type'] ) {
				$event = tribe_events()
					->where( 'ends_after', 'now' )
					->where( 'hidden', false )
					->order_by( 'event_date', 'ASC' )
					->first();
			} elseif ( 'future-event' === $instance['type'] ) {
				$event = tribe_events()
					->where( 'starts_after', 'now' )
					->where( 'hidden', false )
					->order_by( 'event_date', 'ASC' )
					->first();
			} else {
				$event = get_post( $instance['event'] );
			}

			$ret = $instance['complete'];
			$show_seconds = $instance['show_seconds'];

			ob_start();
			include Tribe__Events__Templates::getTemplateHierarchy( 'pro/widgets/countdown-widget' );
			$hourformat = ob_get_clean();

			if ( $event instanceof WP_Post ) {

				// Get the event start date and time zone
				$startdate = new DateTime( tribe_get_start_date( $event->ID, false, Tribe__Date_Utils::DBTZDATETIMEFORMAT, 'event' ) );
				$use_tz = $startdate->getTimeZone();//Tribe__Events__Timezones::get_event_timezone_string( $event->ID );

				// Get current time, make both times use the same timezone
				try {
					$now = new DateTime( 'now', new DateTimeZone( $use_tz->getName() ) );
					$startdate->setTimezone( new DateTimeZone( $use_tz->getName() ) );
				} catch ( Exception $e ) {
					$now = new DateTime( 'now' );
				}

				// Get the number of seconds remaining until the date in question.
				// Note: can't use $startdate->getTimestamp() as that negates all the TZ work we just did!
				$seconds = strtotime( $startdate->format( Tribe__Date_Utils::DBTZDATETIMEFORMAT ) ) - strtotime( $now->format( Tribe__Date_Utils::DBTZDATETIMEFORMAT ) );

			} else {
				$seconds = 0;
			}

			if ( $seconds > 0 ) {
				$ret = $this->generate_countdown_output( $seconds, $instance['complete'], $hourformat, $event );
			}

			$jsonld_enable = isset( $instance['jsonld_enable'] ) ? $instance['jsonld_enable'] : true;

			/**
			 * Filters whether JSON LD information should be printed to the page or not for this widget type.
			 *
			 * @param bool $jsonld_enable Whether JSON-LD should be printed to the page or not; default `true`.
			 */
			$jsonld_enable = apply_filters( 'tribe_events_' . $this->id_base . '_jsonld_enabled', $jsonld_enable );


			/**
			 * Filters whether JSON LD information should be printed to the page for any widget type.
			 *
			 * @param bool $jsonld_enable Whether JSON-LD should be printed to the page or not; default `true`.
			 */
			$jsonld_enable = apply_filters( 'tribe_events_widget_jsonld_enabled', $jsonld_enable );

			if ( $jsonld_enable ) {
				$this->print_jsonld_markup_for( $event );
			}

			return $ret;
		}

		/**
		 * Generate the hidden information to be passed to jQuery
		 *
		 * @param  int $seconds             The amount of seconds to show
		 * @param  string $complete         HTML for when the countdown is over
		 * @param  string $hourformat       HTML from View
		 * @param  WP_Post|int|null $event  Event Instance of WP_Post
		 * @param  null $deprecated         Deprecated Argument
		 * @return string
		 */
		public function generate_countdown_output( $seconds, $complete, $hourformat, $event, $deprecated = null ) {
			$event = get_post( $event );
			$link = tribe_get_event_link( $event );

			$output = '';

			if ( $event ) {
				$output .= '<div class="tribe-countdown-text"><a href="' . esc_url( $link ) . '">' . esc_attr( $event->post_title ) . '</a></div>';
			}

			return $output . '
			<div class="tribe-countdown-timer">
				<span class="tribe-countdown-seconds">' . $seconds . '</span>
				<span class="tribe-countdown-format">' . $hourformat . '</span>
				' . $complete . '
			</div>';
		}

		protected function print_jsonld_markup_for( $event ) {
			$event = get_post( $event );

			if ( empty( $event ) ) {
				return;
			}

			Tribe__Events__JSON_LD__Event::instance()->markup( $event );
		}

	}

}
