<?php
/**
 * Advanced List Widget
 *
 * @since   5.2.0
 *
 * @package Tribe\Events\Pro\Views\V2\Widgets
 */

namespace Tribe\Events\Pro\Views\V2\Widgets;

use Tribe\Events\Views\V2\Assets as TEC_Assets;
use Tribe\Events\Views\V2\View_Interface;
use Tribe__Main as Main;
use Tribe__Utils__Array as Arr;
use Taxonomy_Filter;

/**
 * Class for the Advanced List Widget.
 *
 * @since   5.2.0
 *
 * @package Tribe\Events\Pro\Views\V2\Widgets
 */
class Widget_Advanced_List {

	/**
	 * Default arguments to be merged into final arguments of the widget.
	 *
	 * @since 5.2.0
	 *
	 * @var array<string,mixed>
	 */
	protected $default_arguments = [
		// Event widget options.
		'venue'     => false,
		'country'   => false,
		'street'    => false,
		'city'      => false,
		'region'    => false,
		'zip'       => false,
		'phone'     => false,
		'cost'      => false,
		'organizer' => false,
		'operand'   => 'OR',
		'filters'   => '',
	];

	/**
	 * Renders the event cost in the event.
	 *
	 * @since 5.2.0
	 *
	 * @param \Tribe__Template $template Current instance of the `Tribe__Template` that's being rendered.
	 */
	public function render_event_cost( $template ) {
		$template->template( 'widgets/widget-events-list/event/cost', $template->get_values() );
	}

	/**
	 * Renders the event venue in the event.
	 *
	 * @since 5.2.0
	 *
	 * @param \Tribe__Template $template Current instance of the `Tribe__Template` that's being rendered.
	 */
	public function render_event_venue( $template ) {
		$template->template( 'widgets/widget-events-list/event/venue', $template->get_values() );
	}

	/**
	 * Renders the event organizers in the event.
	 *
	 * @since 5.2.0
	 *
	 * @param \Tribe__Template $template Current instance of the `Tribe__Template` that's being rendered.
	 */
	public function render_event_organizers( $template ) {
		$template->template( 'widgets/widget-events-list/event/organizers', $template->get_values() );
	}

	/**
	 * Renders the recurring icon in the event.
	 *
	 * @since 5.2.0
	 *
	 * @param \Tribe__Template $template Current instance of the `Tribe__Template` that's being rendered.
	 *
	 * @return string
	 */
	public function render_event_recurring_icon( $template ) {
		return $template->template( 'widgets/widget-events-list/event/date/recurring', $template->get_values() );
	}

	/**
	 * Enqueue assets for PRO version of events list widget.
	 *
	 * @since 5.2.0
	 *
	 * @param boolean         $should_enqueue Whether assets are enqueued or not.
	 * @param \Tribe__Context $context        Context we are using to build the view.
	 * @param View_Interface  $view           Which view we are using the template on.
	 */
	public function enqueue_assets( $should_enqueue, $context, $view ) {
		if ( ! $should_enqueue ) {
			return;
		}

		tribe_asset_enqueue( 'tribe-events-pro-widgets-v2-events-list-skeleton' );

		if ( tribe( TEC_Assets::class )->should_enqueue_full_styles() ) {
			tribe_asset_enqueue( 'tribe-events-pro-widgets-v2-events-list-full' );
		}
	}

	/**
	 * Filter the default arguments for the list widget.
	 *
	 * @since 5.2.0
	 *
	 * @param array<string,mixed> $arguments   Current set of arguments.
	 *
	 * @return array<string,mixed> The map of widget default arguments.
	 */
	public function filter_default_arguments( $arguments ) {
		return array_merge( $arguments, $this->default_arguments );
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
	public function filter_admin_fields( $admin_fields ) {
		$adv_admin_fields = [
			'metadata_section' => [
				'type'     => 'fieldset',
				'classes'  => 'tribe-common-form-control-checkbox-checkbox-group',
				'label'    => _x( 'Display:', 'The title for the meta data section of the List Widget.', 'tribe-events-calendar-pro' ),
				'children' => [
					'cost'      => [
						'type'  => 'checkbox',
						'label' => _x( 'Price', 'The label for the option to enable cost display in the List Widget.', 'tribe-events-calendar-pro' ),
					],
					'venue'     => [
						'type'  => 'checkbox',
						'label' => _x( 'Venue', 'The label for the option to enable venue display in the List Widget.', 'tribe-events-calendar-pro' ),
					],
					'street'    => [
						'type'  => 'checkbox',
						'label' => _x( 'Street', 'The label for the option to enable street display in the List Widget.', 'tribe-events-calendar-pro' ),
					],
					'city'      => [
						'type'  => 'checkbox',
						'label' => _x( 'City', 'The label for the option to enable city display in the List Widget.', 'tribe-events-calendar-pro' ),
					],
					'region'    => [
						'type'  => 'checkbox',
						'label' => _x( 'State (US) Or Province (Int)', 'The label for the option to enable region display in the List Widget.', 'tribe-events-calendar-pro' ),
					],
					'zip'       => [
						'type'  => 'checkbox',
						'label' => _x( 'Postal Code', 'The label for the option to enable zip/postal code display in the List Widget.', 'tribe-events-calendar-pro' ),
					],
					'country'   => [
						'type'  => 'checkbox',
						'label' => _x( 'Country', 'The label for the option to enable country display in the List Widget.', 'tribe-events-calendar-pro' ),
					],
					'phone'     => [
						'type'  => 'checkbox',
						'label' => _x( 'Phone', 'The label for the option to enable phone display in the List Widget.', 'tribe-events-calendar-pro' ),
					],
					'organizer' => [
						'type'  => 'checkbox',
						'label' => _x( 'Organizer', 'The label for the option to enable organizer display in the List Widget.', 'tribe-events-calendar-pro' ),
					],
				],
			],
		];

		// Add the taxonomy filter controls.
		$adv_admin_fields = array_merge( $adv_admin_fields, tribe( 'pro.views.v2.widgets.taxonomy' )->get_taxonomy_admin_section() );

		return Main::array_insert_after_key( 'limit', $admin_fields, $adv_admin_fields );
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
	public function filter_widgets_updated_instance( $updated_instance, $new_instance ) {
		$updated_instance['venue']     = ! empty( $new_instance['venue'] );
		$updated_instance['country']   = ! empty( $new_instance['country'] );
		$updated_instance['street']    = ! empty( $new_instance['street'] );
		$updated_instance['city']      = ! empty( $new_instance['city'] );
		$updated_instance['region']    = ! empty( $new_instance['region'] );
		$updated_instance['zip']       = ! empty( $new_instance['zip'] );
		$updated_instance['phone']     = ! empty( $new_instance['phone'] );
		$updated_instance['cost']      = ! empty( $new_instance['cost'] );
		$updated_instance['organizer'] = ! empty( $new_instance['organizer'] );
		$updated_instance['operand']   = ! empty( $new_instance['operand'] ) ? $new_instance['operand'] : false;
		$updated_instance['filters']   = ! empty( $new_instance['filters'] ) ? tribe( 'pro.views.v2.widgets.taxonomy' )->format_taxonomy_filters( $new_instance['filters'] ) : false;

		return $updated_instance;
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
	public function filter_args_to_context( $alterations, $arguments ) {
		/* @var \Tribe\Events\Pro\Views\V2\Widgets\Taxonomy_Filter $taxonomy_filters */
		$taxonomy_filters = tribe( 'pro.views.v2.widgets.taxonomy' );

		$alterations['event_display']     = 'list';
		$alterations['view']              = 'list';

		$alterations['widget_list_display'] = [
			'cost'      => tribe_is_truthy( $arguments['cost'] ),
			'venue'     => tribe_is_truthy( $arguments['venue'] ),
			'street'    => tribe_is_truthy( $arguments['street'] ),
			'city'      => tribe_is_truthy( $arguments['city'] ),
			'region'    => tribe_is_truthy( $arguments['region'] ),
			'zip'       => tribe_is_truthy( $arguments['zip'] ),
			'country'   => tribe_is_truthy( $arguments['country'] ),
			'phone'     => tribe_is_truthy( $arguments['phone'] ),
			'organizer' => tribe_is_truthy( $arguments['organizer'] ),
		];

		// Handle tax filters.
		if ( ! empty( $arguments['filters'] ) ) {
			$alterations            = array_merge( $alterations, $taxonomy_filters->set_taxonomy_args( $arguments['filters'], $arguments['operand'] ) );
			$alterations['operand'] = $arguments['operand'];
		}

		return $alterations;
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
	public function filter_template_vars( $template_vars, $view_interface ) {
		$context = $view_interface->get_context();
		$display = $context->get( 'widget_list_display' );

		$template_vars['display'] = [
			'cost'      => Arr::get( $display, 'cost' ),
			'venue'     => Arr::get( $display, 'venue' ),
			'street'    => Arr::get( $display, 'street' ),
			'city'      => Arr::get( $display, 'city' ),
			'region'    => Arr::get( $display, 'region' ),
			'zip'       => Arr::get( $display, 'zip' ),
			'country'   => Arr::get( $display, 'country' ),
			'phone'     => Arr::get( $display, 'phone' ),
			'organizer' => Arr::get( $display, 'organizer' ),
		];

		return $template_vars;
	}
}
