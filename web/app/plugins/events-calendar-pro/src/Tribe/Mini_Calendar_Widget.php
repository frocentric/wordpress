<?php

class Tribe__Events__Pro__Mini_Calendar_Widget extends WP_Widget {

	public function __construct() {
		$widget_ops = array(
			'classname'   => 'tribe_mini_calendar_widget',
			'description' => __( 'The events calendar mini calendar widget', 'tribe-events-calendar-pro' ),
		);

		parent::__construct( 'tribe-mini-calendar', __( 'Events Calendar', 'tribe-events-calendar-pro' ), $widget_ops );

		if ( is_active_widget( false, false, $this->id_base ) || is_customize_preview() ) {
			add_action( 'tribe_events_pro_widget_render', array( 'Tribe__Events__Pro__Widgets', 'enqueue_calendar_widget_styles' ), 100 );
		}
	}

	public function widget( $args, $instance ) {
		$ecp = Tribe__Events__Pro__Main::instance();
		$tooltip_status = $ecp->recurring_info_tooltip_status();
		$ecp->disable_recurring_info_tooltip();

		add_filter( 'tribe_events_list_show_ical_link', '__return_false' );

		echo $args['before_widget'];

		$defaults = array(
			'title'   => __( 'Events Calendar', 'tribe-events-calendar-pro' ),
			'count'   => 5,
			'filters' => null,
			'operand' => 'OR',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		tribe_asset_enqueue( 'tribe-mini-calendar' );

		// Add localization variables
		$data_tec = tribe( 'tec.assets' )->get_js_calendar_script_data();
		wp_localize_script( 'tribe-events-calendar-script', 'tribe_js_config', $data_tec );

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

		$filters   = isset( $instance['raw_filters'] ) ? $instance['raw_filters'] : json_decode( $instance['filters'] );
		$tax_query = Tribe__Events__Pro__Widgets::form_tax_query( $filters, $instance['operand'] );

		do_action( 'tribe_events_mini_cal_before_the_title' );

		echo ( $instance['title'] ) ? $args['before_title'] . $instance['title'] . $args['after_title'] : '';

		do_action( 'tribe_events_mini_cal_after_the_title' );

		$instance['tax_query'] = $tax_query;
		$instance['id_base'] = $this->id_base;

		Tribe__Events__Pro__Mini_Calendar::instance()->do_calendar( $instance );

		echo $args['after_widget'];

		remove_filter( 'tribe_events_list_show_ical_link', '__return_false' );

		if ( $tooltip_status ) {
			$ecp->enable_recurring_info_tooltip();
		}

	}

	public function update( $new_instance, $old_instance ) {
		$instance                  = $old_instance;
		$instance['title']         = strip_tags( $new_instance['title'] );
		$instance['count']         = intval( strip_tags( $new_instance['count'] ) );
		$instance['operand']       = strip_tags( $new_instance['operand'] );
		$instance['filters']       = maybe_unserialize( $new_instance['filters'] );
		$instance['jsonld_enable'] = ( ! empty( $new_instance['jsonld_enable'] ) ? 1 : 0 );

		return $instance;
	}

	public function form( $instance ) {
		$defaults = array(
			'title'         => __( 'Events Calendar', 'tribe-events-calendar-pro' ),
			'layout'        => 'tall',
			'count'         => 5,
			'operand'       => 'OR',
			'filters'       => null,
			'jsonld_enable' => true,
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$taxonomies = get_object_taxonomies( Tribe__Events__Main::POSTTYPE, 'objects' );
		$taxonomies = array_reverse( $taxonomies );

		$ts = Tribe__Events__Pro__Main::instance();

		include $ts->pluginPath . 'src/admin-views/widget-calendar.php';
	}
}
