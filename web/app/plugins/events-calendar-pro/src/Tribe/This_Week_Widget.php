<?php
/**
 * This Week Event Widget
 */
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Tribe__Events__Pro__This_Week_Widget extends WP_Widget {

	/**
	 *  This Week Widget - Construct
	 */
	public function __construct() {
		// Widget settings.
		$description = sprintf( '%1s %2s %3s',
			__( 'Display', 'tribe-events-calendar-pro' ),
			tribe_get_event_label_plural_lowercase(),
			__( 'by day for the week.', 'tribe-events-calendar-pro' )
		);

		$widget_ops = array(
			'classname'   => 'tribe-this-week-events-widget',
			'description' => esc_attr( $description ),
		);

		$control_ops = array( 'id_base' => 'tribe-this-week-events-widget' );
		$widget_name = sprintf( '%1s %2s',
			__( 'This Week', 'tribe-events-calendar-pro' ),
			tribe_get_event_label_plural()
		);

		// Create the widget.
		parent::__construct( 'tribe-this-week-events-widget', esc_attr( $widget_name ), $widget_ops, $control_ops );

		add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );

		// Do not enqueue if the widget is inactive
		if ( is_active_widget( false, false, $this->id_base, true ) || is_customize_preview() ) {
			add_action( 'tribe_events_pro_widget_render', array( 'Tribe__Events__Pro__Widgets', 'enqueue_calendar_widget_styles' ), 100 );
		}

	}

	/**
	 * @param $hook
	 */
	public function load_scripts( $hook ) {

		if ( 'widgets.php' != $hook ) {
			return;
		}

		//Need for Customizer and to prevent errors in Widgets Section with Color Picker
		wp_enqueue_script( 'underscore' );

		//Colorpicker
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
	}

	/**
	 * @param $args
	 * @param $instance
	 */
	public function widget( $args, $instance ) {

		// Initialize defaults. When the widget is added via the Customizer, the widget is rendered
		// prior to being saved and the instance is empty. This ensures that $instance holds the
		// defaults so the behavior is expected and doesn't throw notices.
		$instance = $this->instance_defaults( $instance );

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

		//Disable Tooltips
		$ecp = Tribe__Events__Pro__Main::instance();
		$tooltip_status = $ecp->recurring_info_tooltip_status();
		$ecp->disable_recurring_info_tooltip();

		//Check If a Taxonomy is set
		if ( ! empty( $instance['raw_filters'] ) || isset( $instance['filters'] ) ) {
			$filters = isset( $instance['raw_filters'] ) ? $instance['raw_filters'] : json_decode( $instance['filters'] );
		} else {
			$filters = null;
		}

		//Prepare Categories for Query
		$tax_query = Tribe__Events__Pro__Widgets::form_tax_query( $filters, $instance['operand'] );

		//Use Date to find start of week if provided in shortcode
		$start_date = isset( $instance['start_date'] ) ? $instance['start_date'] : null;

		//Use Date to find start of week if provided in shortcode
		$week_offset = isset( $instance['week_offset'] ) ? $instance['week_offset'] : null;

		//Array of Variables to use for Data Attributes and
		$this_week_query_vars['start_date']    = tribe_get_this_week_first_week_day( $start_date, $week_offset );
		$this_week_query_vars['end_date']      = tribe_get_this_week_last_week_day( $this_week_query_vars['start_date'] );
		$this_week_query_vars['count']         = $instance['count'];
		$this_week_query_vars['layout']        = $instance['layout'];
		$this_week_query_vars['tax_query']     = $tax_query;
		$this_week_query_vars['hide_weekends'] = isset( $instance['hide_weekends'] ) ? $instance['hide_weekends'] : false;

		//Setup Variables for Template
		$this_week_template_vars = Tribe__Events__Pro__This_Week::this_week_template_vars( $this_week_query_vars );

		//Setup Attributes for Ajax
		$this_week_data_attrs = Tribe__Events__Pro__This_Week::this_week_data_attr( $this_week_query_vars );

		//Setups This Week Object for Each Day
		$week_days = Tribe__Events__Pro__This_Week::this_week_query( $this_week_query_vars );

		echo $args['before_widget'];

		do_action( 'tribe_events_this_week_widget_before_the_title' );

		echo ( ! empty( $instance['title'] ) ) ? $args['before_title'] . $instance['title'] . $args['after_title'] : '';

		do_action( 'tribe_events_this_week_widget_after_the_title' );

		include Tribe__Events__Templates::getTemplateHierarchy( 'pro/widgets/this-week-widget.php' );

		echo $args['after_widget'];

		// Re-enable recurring event info
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
			$this->print_jsonld_markup_for( $week_days );
		}

		wp_reset_postdata();
	}

	/**
	 *  Include the file for the administration view of the widget.
	 *
	 * @param $instance
	 */
	public function form( $instance ) {
		$this->instance_defaults( $instance );

		$taxonomies = get_object_taxonomies( Tribe__Events__Main::POSTTYPE, 'objects' );
		$taxonomies = array_reverse( $taxonomies );

		$instance = $this->instance;
		include( Tribe__Events__Pro__Main::instance()->pluginPath . 'src/admin-views/widget-admin-this-week.php' );
	}

	/**
	 * @param $instance
	 */
	protected function instance_defaults( $instance ) {
		$this->instance = wp_parse_args( (array) $instance, array(
			'title'           => '',
			'layout'          => 'vertical',
			'highlight_color' => '',
			'count'           => 3,
			'widget_id'       => 3,
			'filters'         => '',
			'operand'         => 'OR',
			'start_date'      => '',
			'week_offset'     => '',
			'hide_weekends'   => false,
			'jsonld_enable'   => true,
			'instance'        => &$this->instance,
		) );

		return $this->instance;
	}

	/**
	 * Function allowing updating of widget information.
	 *
	 * @param $new_instance
	 * @param $old_instance
	 *
	 * @return mixed
	 */
	public function update( $new_instance, $old_instance ) {

		$instance['title']           = sanitize_text_field( $new_instance['title'] );
		$instance['layout']          = sanitize_text_field( $new_instance['layout'] );
		$instance['highlight_color'] = sanitize_text_field( $new_instance['highlight_color'] );
		$instance['count']           = absint( $new_instance['count'] );
		$instance['filters']         = maybe_unserialize( sanitize_text_field( $new_instance['filters'] ) );
		$instance['operand']         = sanitize_text_field( $new_instance['operand'] );
		$instance['jsonld_enable']   = ( ! empty( $new_instance['jsonld_enable'] ) ? 1 : 0 );

		return $instance;
	}

	protected function print_jsonld_markup_for( $week_days ) {
		$days   = wp_list_pluck( $week_days, 'this_week_events' );
		$events = array();
		foreach ( $days as $day ) {
			$events = array_merge( $events, $day );
		}

		if ( empty( $events ) ) {
			return;
		}

		Tribe__Events__JSON_LD__Event::instance()->markup( $events );
	}
}
