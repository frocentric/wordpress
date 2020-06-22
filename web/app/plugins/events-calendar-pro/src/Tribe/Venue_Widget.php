<?php
/**
 * Related event widget
 */
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

use Tribe__Date_Utils as Dates;

if ( ! class_exists( 'Tribe__Events__Pro__Venue_Widget' ) ) {
	class Tribe__Events__Pro__Venue_Widget extends WP_Widget {
		public function __construct() {
			// Widget settings.
			$widget_ops = array(
				'classname'   => 'tribe-events-venue-widget',
				'description' => __( 'Displays a list of upcoming events at a specific venue.', 'tribe-events-calendar-pro' ),
			);
			// Create the widget.
			parent::__construct( 'tribe-events-venue-widget', __( 'Events Featured Venue', 'tribe-events-calendar-pro' ), $widget_ops );

			// Do not enqueue if the widget is inactive
			if ( is_active_widget( false, false, $this->id_base, true ) || is_customize_preview() ) {
				add_action( 'tribe_events_pro_widget_render', array( 'Tribe__Events__Pro__Widgets', 'enqueue_calendar_widget_styles' ), 100 );
			}
		}

		public function widget( $args, $instance ) {
			// We need the Defaults to avoid problems on the Customizer
			$defaults = array(
				'title'         => '',
				'venue_ID'      => null,
				'count'         => 3,
				'hide_if_empty' => true,
			);
			$instance = wp_parse_args( (array) $instance, $defaults );

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

			extract( $args );
			extract( $instance );

			if ( empty( $hide_if_empty ) || 'false' === $hide_if_empty ) {
				$hide_if_empty = false;
			}

			$event_args = array(
				'post_type'      => Tribe__Events__Main::POSTTYPE,
				'venue'          => $venue_ID,
				'posts_per_page' => $count,
				'eventDisplay'   => 'list',
				'tribe_render_context' => 'widget',
				'start_date'     => Dates::build_date_object( 'now' ),
			);

			/**
			 * Filter Venue Widget tribe_get_event args
			 *
			 * @param array $event_args Arguments for the Venue Widget's call to tribe_get_events
			 */
			$event_args = apply_filters( 'tribe_events_pro_venue_widget_event_query_args', $event_args );

			// Get all the upcoming events for this venue.
			$events = tribe_get_events( $event_args, true );

			// If there are no events, and the user has set to hide if empty, don't display the widget.
			if ( $hide_if_empty && ! $events->have_posts() ) {
				return;
			}

			$ecp            = Tribe__Events__Pro__Main::instance();
			$tooltip_status = $ecp->recurring_info_tooltip_status();
			$ecp->disable_recurring_info_tooltip();

			echo $before_widget;

			do_action( 'tribe_events_venue_widget_before_the_title' );

			echo ( $instance['title'] ) ? $args['before_title'] . $instance['title'] . $args['after_title'] : '';

			do_action( 'tribe_events_venue_widget_after_the_title' );

			include( Tribe__Events__Templates::getTemplateHierarchy( 'pro/widgets/venue-widget.php' ) );
			echo $after_widget;

			if ( $tooltip_status ) {
				$ecp->enable_recurring_info_tooltip();
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
				$this->print_jsonld_markup_for( $events );
			}

			wp_reset_postdata();
		}

		// Include the file for the administration view of the widget.
		public function form( $instance ) {
			$defaults = array(
				'title'         => '',
				'venue_ID'      => null,
				'count'         => 3,
				'hide_if_empty' => true,
				'jsonld_enable' => true,
			);
			$venues   = get_posts( array(
					'post_type' => Tribe__Events__Main::VENUE_POST_TYPE,
					'orderby'   => 'title',
					'nopaging'  => true,
				) );
			$instance = wp_parse_args( (array) $instance, $defaults );
			include( Tribe__Events__Pro__Main::instance()->pluginPath . 'src/admin-views/widget-admin-venue.php' );
		}

		// Function allowing updating of widget information.
		public function update( $new_instance, $old_instance ) {
			$instance = parent::update( $new_instance, $old_instance );

			$instance['title']         = $new_instance['title'];
			$instance['venue_ID']      = $new_instance['venue_ID'];
			$instance['count']         = $new_instance['count'];
			$instance['hide_if_empty'] = ( isset( $new_instance['hide_if_empty'] ) ? 1 : 0 );
			$instance['jsonld_enable']        = ( ! empty( $new_instance['jsonld_enable'] ) ? 1 : 0 );

			return $instance;
		}

		protected function print_jsonld_markup_for( $events ) {
			$events = $events->posts;

			if ( empty( $events ) ) {
				return;
			}

			Tribe__Events__JSON_LD__Event::instance()->markup( $events );
		}
	}
}
