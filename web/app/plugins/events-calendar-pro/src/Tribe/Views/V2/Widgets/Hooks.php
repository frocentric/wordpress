<?php
/**
 * Handles hooking all the actions and filters used by the module.
 *
 * To remove a filter:
 * remove_filter( 'some_filter', [ tribe( Tribe\Events\Pro\Views\V2\Hooks::class ), 'some_filtering_method' ] );
 * remove_filter( 'some_filter', [ tribe( 'pro.views.v2.hooks' ), 'some_filtering_method' ] );
 *
 * To remove an action:
 * remove_action( 'some_action', [ tribe( Tribe\Events\Pro\Views\V2\Hooks::class ), 'some_method' ] );
 * remove_action( 'some_action', [ tribe( 'pro.views.v2.hooks' ), 'some_method' ] );
 *
 * @since   5.2.0
 *
 * @package Tribe\Events\Pro\Views\V2\Widgets
 */

namespace Tribe\Events\Pro\Views\V2\Widgets;

use \Tribe\Events\Pro\Views\V2\Widgets\Admin_Template as Admin_Template;
use Tribe\Events\Views\V2\View_Interface;
use Tribe\Events\Views\V2\Views\Widgets\Widget_View;
use Tribe\Events\Views\V2\Widgets\Widget_Abstract;

/**
 * Class Hooks.
 *
 * @since   5.2.0
 *
 * @package Tribe\Events\Pro\Views\V2\Widgets
 */
class Hooks extends \tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 5.2.0
	 */
	public function register() {
		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Adds the actions for V2 widgets.
	 *
	 * @since 5.2.0
	 */
	protected function add_actions() {
		add_action(
			'tribe_template_entry_point:events/v2/widgets/widget-events-list/event:event_meta',
			[ $this, 'widget_events_list_event_meta_cost' ],
			10,
			3
		);
		add_action(
			'tribe_template_entry_point:events/v2/widgets/widget-events-list/event:event_meta',
			[ $this, 'widget_events_list_event_meta_venue' ],
			15,
			3
		);
		add_action(
			'tribe_template_entry_point:events/v2/widgets/widget-events-list/event:event_meta',
			[ $this, 'widget_events_list_event_meta_organizers' ],
			20,
			3
		);
		add_action(
			'tribe_template_entry_point:events/v2/widgets/widget-events-list/event/date:after_event_datetime',
			[ $this, 'widget_events_list_event_recurring_icon' ],
			10,
			3
		);

		add_action(
			'tribe_events_views_v2_widget_widget-events-list_after_enqueue_assets',
			[ $this, 'widget_events_list_after_enqueue_assets' ],
			10,
			3
		);

		add_action(
			'tribe_events_views_v2_widget_widget-countdown_after_enqueue_assets',
			[ $this, 'widget_events_countdown_after_enqueue_assets' ],
			10,
			3
		);

		add_action(
			'tribe_events_views_v2_widget_admin_form_taxonomy-filters_input',
			[ $this, 'add_taxonomy_filters' ],
			10,
			2
		);
		add_action(
			'tribe_events_views_v2_widget_admin_form_taxonomy_input',
			[ $this, 'add_taxonomy_input' ],
			10,
			2
		);

		add_action(
			'tribe_events_pro_v1_registered_widget_classes',
			[ $this, 'deregister_v1_widgets' ]
		);

		add_action(
			'tribe_events_pro_v1_unregistered_widget_classes',
			[ $this, 'unregister_v1_widgets' ]
		);

		add_action(
			'wp_ajax_tribe_widget_dropdown_events',
			[ $this, 'ajax_get_events' ]
		);

		add_action(
			'wp_ajax_tribe_widget_dropdown_venues',
			[ $this, 'ajax_get_venues' ]
		);
	}

	/**
	 * Adds the filters for V2 Widgets
	 *
	 * @since 5.2.0
	 */
	protected function add_filters() {
		// Setup the Advanced List Widget by filtering the The Events Calendar List Widget.
		add_filter( 'tribe_events_views_v2_view_repository_args', [ $this, 'filter_widget_recurrence_repository_args' ], 10, 2 );
		add_filter( 'tribe_widget_tribe_events_list_widget_default_arguments', [ $this, 'filter_list_widget_default_arguments' ] );
		add_filter( 'tribe_widget_tribe_events_list_widget_admin_fields', [ $this, 'filter_list_widget_admin_fields' ] );
		add_filter( 'tribe_widget_tribe_events_list_widget_updated_instance', [ $this, 'filter_list_widget_updated_instance' ], 10, 2 );
		add_filter( 'tribe_events_views_v2_list_widget_args_to_context', [ $this, 'filter_list_widget_args_to_context' ], 10, 2 );
		add_filter( 'tribe_events_views_v2_list_widget_template_vars', [ $this, 'filter_list_widget_template_vars' ], 10, 2 );
		add_filter( 'tribe_events_views_v2_view_widget-events-list_template_vars', [ $this, 'filter_list_widget_template_vars' ], 10, 2 );
		add_filter( 'tribe_events_views_v2_widget_field_data', [ $this, 'filter_taxonomy_filters_field_data' ], 10, 3 );
		add_filter( 'tribe_events_views_v2_widget_repository_args', [ $this, 'filter_repository_taxonomy_args' ], 10, 3 );
		add_filter( 'tribe_customizer_inline_stylesheets', [ $this, 'filter_add_full_stylesheet_to_customizer' ], 12, 2 );
		// We don't use icons on the countdown widget, so no VE styles necessary.
		add_filter( 'tribe-events-virtual-widgets-v2-widget-countdown-should-enqueue-assets', '__return_false' );
	}

	/**
	 * Stop registration of the v1 Widgets as needed.
	 *
	 * @since 5.2.0
	 *
	 * @param array<string> $widgets The array of widget classes to register.
	 *
	 * @return array<string> $widgets The modified array of widget classes to register.
	 */
	public function deregister_v1_widgets( $widgets ) {
		unset( $widgets['Tribe__Events__Pro__Advanced_List_Widget'] );
		unset( $widgets['Tribe__Events__Pro__Countdown_Widget'] );
		unset( $widgets['Tribe__Events__Pro__Venue_Widget'] );

		return $widgets;
	}

	/**
	 * Un-registration of the v1 Widgets as needed.
	 *
	 * @since 5.2.0
	 *
	 * @param array<string> $widgets The array of widget classes to unregister.
	 *
	 * @return array<string> $widgets The modified array of widget classes to unregister.
	 */
	public function unregister_v1_widgets( $widgets ) {
		$widgets[] = 'Tribe__Events__Pro__Advanced_List_Widget';
		$widgets[] = 'Tribe__Events__Pro__Countdown_Widget';
		$widgets[] = 'Tribe__Events__Pro__Venue_Widget';

		return $widgets;
	}

	/**
	 * Creates the select2 event dropdown for widgets.
	 *
	 * @since 5.3.0
	 *
	 * @return string The HTML to display.
	 */
	public function ajax_get_events() {
		return $this->container->make( Ajax::class )->get_events();
	}

	/**
	 * Creates the select2 venue dropdown for widgets.
	 *
	 * @since 5.3.0
	 *
	 * @return array The array of venues.
	 */
	public function ajax_get_venues() {
		return $this->container->make( Ajax::class )->get_venues();
	}

	/* ADVANCED LIST WIDGET HOOKS */
	/**
	 * Action to inject the cost meta into the events list widget event.
	 *
	 * @since 5.2.0
	 *
	 * @param string             $file      Complete path to include the PHP File.
	 * @param array<string>      $name      Template name.
	 * @param \Tribe__Template   $template  Current instance of the Tribe__Template.
	 */
	public function widget_events_list_event_meta_cost( $file, $name, $template ) {
		$this->container->make( Widget_Advanced_List::class )->render_event_cost( $template );
	}

	/**
	 * Action to inject the venue meta into the events list widget event.
	 *
	 * @since 5.2.0
	 *
	 * @param string             $file      Complete path to include the PHP File.
	 * @param array<string>      $name      Template name.
	 * @param \Tribe__Template   $template  Current instance of the Tribe__Template.
	 */
	public function widget_events_list_event_meta_venue( $file, $name, $template ) {
		$this->container->make( Widget_Advanced_List::class )->render_event_venue( $template );
	}

	/**
	 * Action to inject the organizers meta into the events list widget event.
	 *
	 * @since 5.2.0
	 *
	 * @param string             $file      Complete path to include the PHP File.
	 * @param array<string>      $name      Template name.
	 * @param \Tribe__Template   $template  Current instance of the Tribe__Template.
	 */
	public function widget_events_list_event_meta_organizers( $file, $name, $template ) {
		$this->container->make( Widget_Advanced_List::class )->render_event_organizers( $template );
	}

	/**
	 * Action to inject the recurring icon into the events list widget event.
	 *
	 * @since 5.2.0
	 *
	 * @param string             $file      Complete path to include the PHP File.
	 * @param array<string>      $name      Template name.
	 * @param \Tribe__Template   $template  Current instance of the Tribe__Template.
	 */
	public function widget_events_list_event_recurring_icon( $file, $name, $template ) {
		$this->container->make( Widget_Advanced_List::class )->render_event_recurring_icon( $template );
	}

	/**
	 * Action to enqueue assets for PRO version of events list widget.
	 *
	 * @since 5.2.0
	 *
	 * @param boolean         $should_enqueue Whether assets are enqueued or not.
	 * @param \Tribe__Context $context        Context we are using to build the view.
	 * @param View_Interface  $view           Which view we are using the template on.
	 */
	public function widget_events_list_after_enqueue_assets( $should_enqueue, $context, $view ) {
		$this->container->make( Widget_Advanced_List::class )->enqueue_assets( $should_enqueue, $context, $view );
	}

	/**
	 * Action to enqueue assets for PRO version of events list widget.
	 *
	 * @since 5.2.0
	 *
	 * @param boolean         $should_enqueue Whether assets are enqueued or not.
	 * @param \Tribe__Context $context        Context we are using to build the view.
	 * @param View_Interface  $view           Which view we are using the template on.
	 */
	public function widget_events_countdown_after_enqueue_assets( $should_enqueue, $context, $view ) {
		$this->container->make( Widget_Countdown::class )->enqueue_assets( $context, $view );
	}

	/**
	 * Filter the default arguments for the list widget.
	 *
	 * @since 5.2.0
	 *
	 * @param array<string,mixed> $arguments Current set of arguments.
	 *
	 * @return array<string,mixed> The map of widget default arguments.
	 */
	public function filter_list_widget_default_arguments( $arguments ) {
		return $this->container->make( Widget_Advanced_List::class )->filter_default_arguments( $arguments );
	}

	/**
	 * Filter the admin fields for the list widget.
	 *
	 * @since 5.2.0
	 *
	 * @param array<string,mixed> $admin_fields The array of widget admin fields.
	 *
	 * @return array<string,mixed> The array of widget admin fields.
	 */
	public function filter_list_widget_admin_fields( $admin_fields ) {
		return $this->container->make( Widget_Advanced_List::class )->filter_admin_fields( $admin_fields );
	}

	/**
	 * Filters the updated instance for the list widget.
	 *
	 * @since 5.2.0
	 *
	 * @param array<string,mixed> $updated_instance The updated instance of the widget.
	 * @param array<string,mixed> $new_instance     The new values for the widget instance.
	 *
	 * @return array<string,mixed> The updated instance to be saved for the widget.
	 */
	public function filter_list_widget_updated_instance( $updated_instance, $new_instance ) {
		return $this->container->make( Widget_Advanced_List::class )->filter_widgets_updated_instance( $updated_instance, $new_instance );
	}

	/**
	 * Filters the args to context for the list widget.
	 *
	 * @since 5.2.0
	 *
	 * @param array<string,mixed> $alterations The alterations to make to the context.
	 * @param array<string,mixed> $arguments   Current set of arguments.
	 *
	 * @return array<string,mixed> $alterations The alterations to make to the context.
	 */
	public function filter_list_widget_args_to_context( $alterations, $arguments ) {
		return $this->container->make( Widget_Advanced_List::class )->filter_args_to_context( $alterations, $arguments );
	}

	/**
	 * Filters the template vars for the list widget.
	 *
	 * @since 5.2.0
	 *
	 * @param array<string,mixed> $template_vars  The updated instance of the widget.
	 * @param View_Interface      $view_interface The current view template.
	 *
	 * @return array<string,mixed> $template_vars The updated instance of the widget.
	 */
	public function filter_list_widget_template_vars( $template_vars, $view_interface ) {
		return $this->container->make( Widget_Advanced_List::class )->filter_template_vars( $template_vars, $view_interface );
	}

	/**
	 * Adds the (hide) Recurring event instances setting to the widget args.
	 *
	 * @since 5.2.0
	 *
	 * @deprecated 5.3.0 Deprecated in favor of one function for all widgets.
	 * @see        filter_widget_recurrence_repository_args()
	 *
	 * @param array<string,mixed> $args    The unmodified arguments.
	 * @param \Tribe__Context     $context The context.
	 *
	 * @return array<string,mixed> The arguments, ready to be set on the View repository instance.
	 */
   public function filter_list_widget_repository_args( $args, $context ) {
		_deprecated_function( __METHOD__, '5.3.0', 'filter_widget_recurrence_repository_args' );

		return $this->filter_widget_recurrence_repository_args( $args, $context );
	}

	/**
	 * Adds the (hide) Recurring event instances setting to the widget args.
	 *
	 * @since 5.2.0
	 * @since 5.3.0   Apply to all widgets.
	 *
	 * @param array<string,mixed> $args    The unmodified arguments.
	 * @param \Tribe__Context     $context The context.
	 *
	 * @return array<string,mixed> The arguments, ready to be set on the View repository instance.
	 */
	public function filter_widget_recurrence_repository_args( $args, $context ) {
		if ( ! $context->get( 'widget' ) ) {
			return $args;
		}

		$hide_recurring = tribe_is_truthy( tribe_get_option( 'hideSubsequentRecurrencesDefault', false ) );

		/**
		 * Allows filtering recurrence display rules for widgets.
		 *
		 * @since 5.3.0
		 *
		 * @param boolean             $hide_recurring Whether to hide (true) or show (false) the subsequent recurrences of events.
		 * @param array<string,mixed> $args           The unmodified arguments.
		 * @param \Tribe__Context     $context        The context.
		 */
		$hide_recurring = apply_filters( 'tribe_widget_hide_subsequent_recurring_events', $hide_recurring, $args, $context );

		$args['hide_subsequent_recurrences'] = $hide_recurring;

		if ( $hide_recurring ) {
			add_filter( 'tribe_repository_events_collapse_recurring_event_instances', '__return_true' );
		}

		return $args;
	}

	/**
	 * Adds the correct field data to the taxonomy input.
	 *
	 * @since 5.2.0
	 *
	 * @param array<string,mixed> $data       The field data we're editing.
	 * @param array<string,mixed> $field_name The info for the field we're rendering.
	 * @param Widget_Abstract     $widget_obj The widget object.
	 *
	 * @return array<string,mixed>
	 */
	public function filter_taxonomy_filters_field_data( $data, $field_name, $widget_obj ) {
		return $this->container->make( Taxonomy_Filter::class )->add_taxonomy_filters_field_data( $data, $field_name, $widget_obj );
	}

	/**
	 * Add some repository args pre-query.
	 *
	 * @since 5.1.1
	 * @since 5.3.0 Include $widget_view param.
	 *
	 * @param array<string,mixed> $args        The arguments to be set on the View repository instance.
	 * @param \Tribe__Context     $context     The context to use to setup the args.
	 * @param Widget_View         $widget_view Widget View being filtered.
	 *
	 * @return array<string,mixed> $args       The arguments, ready to be set on the View repository instance.
	 */
	public function filter_repository_taxonomy_args( $args, $context, $widget_view ) {
		return $this->container->make( Taxonomy_Filter::class )->add_taxonomy_filters_repository_args( $args, $context, $widget_view );
	}

	/**
	 * Add full events list widget stylesheets to customizer styles array to check.
	 *
	 * @since 5.2.0
	 *
	 * @param array<string> $sheets       Array of sheets to search for.
	 * @param string        $css_template String containing the inline css to add.
	 *
	 * @return array Modified array of sheets to search for.
	 */
	public function filter_add_full_stylesheet_to_customizer( $sheets, $css_template ) {
		return array_merge( $sheets, [ 'tribe-events-widgets-v2-events-list-full' ] );
	}
}
