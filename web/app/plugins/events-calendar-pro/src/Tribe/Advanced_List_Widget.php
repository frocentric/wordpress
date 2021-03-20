<?php
/**
 * Event List Widget - Premium version
 *
 * Creates a widget that displays the next upcoming x events
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Tribe__Events__Pro__Advanced_List_Widget extends Tribe__Events__List_Widget {
	/**
	 * @var array
	 */
	public $instance = array();


	public function __construct() {
		$widget_ops = array(
			'classname'   => 'tribe-events-adv-list-widget',
			'description' => __( 'A widget that displays the next upcoming x events.', 'tribe-events-calendar-pro' ),
		);

		$control_ops = array( 'id_base' => 'tribe-events-adv-list-widget' );

		parent::__construct( 'tribe-events-adv-list-widget', __( 'Events List', 'tribe-events-calendar-pro' ), $widget_ops, $control_ops );
		add_filter( 'tribe_events_list_widget_query_args', array( $this, 'taxonomy_filters' ) );

		// Do not enqueue if the widget is inactive
		if ( is_active_widget( false, false, $this->id_base, true ) || is_customize_preview() ) {
			add_action( 'tribe_events_pro_widget_render', array( 'Tribe__Events__Pro__Widgets', 'enqueue_calendar_widget_styles' ), 100 );
		}
	}

	public function taxonomy_filters( $query ) {
		if ( empty( $this->instance ) ) {
			return $query;
		}

		$filters   = isset( $this->instance['raw_filters'] ) ? $this->instance['raw_filters'] : json_decode( $this->instance['filters'] );
		$tax_query = Tribe__Events__Pro__Widgets::form_tax_query( $filters, $this->instance['operand'] );

		if ( isset( $query['tax_query'] ) ) {
			$query['tax_query'] = array_merge( $query['tax_query'], $tax_query );
		} else {
			$query['tax_query'] = $tax_query;
		}

		return $query;
	}

	public function widget( $args, $instance ) {
		$ecp            = Tribe__Events__Pro__Main::instance();
		$tooltip_status = $ecp->recurring_info_tooltip_status();
		$ecp->disable_recurring_info_tooltip();

		$this->instance_defaults( $instance );

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

		// @todo remove after 3.7 (continuity helper for upgrading users)
		if ( isset( $this->instance['category'] ) ) {
			$this->include_cat_id( $this->instance['filters'], $this->instance['category'] );
		}

		parent::widget_output( $args, $this->instance, 'pro/widgets/list-widget' );

		if ( $tooltip_status ) {
			$ecp->enable_recurring_info_tooltip();
		}
	}

	public function update( $new_instance, $old_instance ) {
		$instance = parent::update( $new_instance, $old_instance );
		$new_instance = $this->default_instance_args( $new_instance, true );

		$instance['venue']   = $new_instance['venue'];
		$instance['country'] = $new_instance['country'];
		$instance['street']  = $new_instance['street'];
		//@todo remove $instance['address'] after 4.6 (continuity helper for upgrading users)
		$instance['address']              = $new_instance['address'];
		$instance['city']                 = $new_instance['city'];
		$instance['region']               = $new_instance['region'];
		$instance['zip']                  = $new_instance['zip'];
		$instance['phone']                = $new_instance['phone'];
		$instance['cost']                 = $new_instance['cost'];
		$instance['organizer']            = $new_instance['organizer'];
		$instance['tribe_is_list_widget'] = $new_instance['tribe_is_list_widget'];
		$instance['operand']              = strip_tags( $new_instance['operand'] );
		$instance['filters']              = maybe_unserialize( $this->clear_filters( $new_instance['filters'] ) );
		$instance['jsonld_enable']        = ( ! empty( $new_instance['jsonld_enable'] ) ? 1 : 0 );

		return $instance;
	}

	/**
	 * Function that removes all filters that contains empty strings as before was creating data structures such as:
	 * {"tribe_events_cat":[]}, instead of just empty string.
	 *
	 * @since 4.4.21
	 *
	 * @param mixed $filters The filter taxonomies to be analyzed.
	 *
	 * @return string A string representation of the filters or empty string if all are empty.
	 */
	public function clear_filters( $filters ) {
		$filters = maybe_unserialize( $filters );

		if ( is_string( $filters ) ) {
			$filters = json_decode( $filters, true );
		}

		$filters = array_filter( (array) $filters );

		return empty( $filters ) ? '' : (string) wp_json_encode( $filters );
	}

	public function form( $instance ) {
		$this->instance_defaults( $instance );

		$taxonomies = get_object_taxonomies( Tribe__Events__Main::POSTTYPE, 'objects' );
		$taxonomies = array_reverse( $taxonomies );

		$instance = $this->instance;
		include( Tribe__Events__Pro__Main::instance()->pluginPath . 'src/admin-views/widget-admin-advanced-list.php' );
	}

	protected function instance_defaults( $instance ) {
		$this->instance = $this->default_instance_args( (array) $instance );
	}

	/**
	 * Returns the instance arguments padded out with default values. If optional
	 * param $empty_values is specified, then it simply ensures that the expected keys
	 * are present - not that they are set to their default values.
	 *
	 * @param array $instance
	 * @param bool  $empty_values
	 *
	 * @return array
	 */
	protected function default_instance_args( array $instance, $empty_values = false ) {
		$defaults = array(
			'title'                => __( 'Upcoming Events', 'tribe-events-calendar-pro' ),
			'limit'                => '5',
			'no_upcoming_events'   => false,
			'featured_events_only' => false,
			'venue'                => false,
			'country'              => true,
			'street'               => false,
			//@todo remove 'address' after 4.6 (continuity helper for upgrading users)
			'address'              => false,
			'city'                 => true,
			'region'               => true,
			'zip'                  => false,
			'phone'                => false,
			'cost'                 => false,
			'organizer'            => false,
			'tribe_is_list_widget' => true,
			'operand'              => 'OR',
			'filters'              => '',
			'jsonld_enable'        => true,
			'instance'             => &$this->instance,
		);

		if ( $empty_values ) {
			$defaults = array_map( '__return_empty_string', $defaults );
		}



		return wp_parse_args( (array) $instance, $defaults );
	}

	/**
	 * Adds the provided category ID to the list of filters.
	 *
	 * In 3.6 taxonomy filters were added to this widget (as already existed for the calendar
	 * widget): this helper exists to provide some continuity for users upgrading from a 3.5.x
	 * release or earlier, transitioning any existing category setting to the new filters
	 * list.
	 *
	 * @todo remove after 3.7
	 *
	 * @param mixed &$filters
	 * @param int   $id
	 */
	protected function include_cat_id( &$filters, $id ) {
		$id  = (string) absint( $id ); // An absint for sanity but a string for comparison purposes
		$tax = Tribe__Events__Main::TAXONOMY;
		if ( '0' === $id || ! is_string( $filters ) ) {
			return;
		}

		$filters = (array) json_decode( $filters, true );

		if ( isset( $filters[ $tax ] ) && ! in_array( $id, $filters[ $tax ] ) ) {
			$filters[ $tax ][] = $id;
		} elseif ( ! isset( $filters[ $tax ] ) ) {
			$filters[ $tax ] = array( $id );
		}

		$filters = json_encode( $filters );
	}
}
