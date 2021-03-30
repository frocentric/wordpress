<?php
/**
 * Countdown Widget
 *
 * @since   5.3.0
 *
 * @package Tribe\Events\Views\V2\Widgets
 */

namespace Tribe\Events\Pro\Views\V2\Widgets;

use Tribe\Events\Views\V2\Assets;
use \Tribe\Events\Views\V2\Widgets\Widget_Abstract;
use Tribe__Context as Context;
use Tribe__Date_Utils as Dates;

/**
 * Class for the Countdown Widget.
 *
 * @since   5.3.0
 *
 * @package Tribe\Events\Views\V2\Widgets
 */
class Widget_Countdown extends Widget_Abstract {
	/**
	 * {@inheritDoc}
	 *
	 * @var string
	 */
	protected $slug = 'tribe_events_countdown_widget';

	/**
	 * {@inheritDoc}
	 *
	 * @var string
	 */
	protected $view_slug = 'widget-countdown';

	/**
	 * {@inheritDoc}
	 *
	 * @var string
	 */
	protected $asset_slug = 'tribe-events-countdown-widget-v2';

	/**
	 * {@inheritDoc}
	 *
	 * @var string
	 */
	protected $view_admin_slug = 'widgets/countdown';

	/**
	 * {@inheritDoc}
	 *
	 * @var string
	 */
	public $id_base = 'tribe-events-countdown-widget';

	/**
	 * {@inheritDoc}
	 *
	 * @var array<string,mixed>
	 */
	protected $default_arguments = [
		// View options.
		'view'                      => null,
		'should_manage_url'         => false,

		// Countdown widget options.
		'id'                        => null,
		'alias-slugs'               => null,
		'title'                     => '',
		'type'                      => 'next-event',
		'event'                     => null,
		'show_seconds'              => true,
		'complete'                  => '',
		'jsonld_enable'             => true,
		'is_countdown_widget'       => true,

		// WP_Widget properties.
		'id_base'                   => null,
		'name'                      => null,
		'admin_fields'              => [],
	];

	/**
	 * {@inheritDoc}
	 */
	public function setup_view( $arguments ) {
		parent::setup_view( $arguments );

		add_filter( 'tribe_customizer_should_print_widget_customizer_styles', '__return_true' );
		add_filter( 'tribe_customizer_inline_stylesheets', [ $this, 'add_full_stylesheet_to_customizer' ], 12 );
	}

	/**
	 * {@inheritDoc}
	 */
	protected function setup_arguments( array $instance = [] ) {
		$arguments = parent::setup_arguments( $instance );

		// Convert old "Featured" to "Next"
		if ( empty( $arguments['type'] ) || 'future-event' === $arguments['type'] ) {
			$arguments['type'] = 'next-event';
		}

		return $arguments;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update( $new_instance, $old_instance ) {
		$updated_instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$updated_instance['title']               = wp_strip_all_tags( $new_instance['title'] );
		$updated_instance['type']                = ! empty( $new_instance['type'] ) ? $new_instance['type'] : $this->default_arguments['type'];
		$updated_instance['event']               = ! empty( $new_instance['event'] ) && '-1' !== $new_instance['event'] ? absint( $new_instance['event'] ) : null;
		$updated_instance['complete']            = wp_strip_all_tags( $new_instance['complete'] );
		$updated_instance['show_seconds']        = ! empty( $new_instance['show_seconds'] );
		$updated_instance['jsonld_enable']       = ! empty( $new_instance['jsonld_enable'] );
		$updated_instance['is_countdown_widget'] = true;

		if ( 'future-event' === $updated_instance['type'] ) {
			$updated_instance['type'] = 'next-event';
		}

		return $this->filter_updated_instance( $updated_instance, $new_instance );
	}

	/**
	 * {@inheritDoc}
	 */
	public function setup_admin_fields() {
		return [
			'title'          => [
				'type'  => 'text',
				'label' => _x(
					'Title:',
					'The label for the title field of the Countdown Widget.',
					'tribe-events-calendar-pro'
				),
			],
			'type'           => [
				'type'     => 'fieldset',
				'classes'  => 'tribe-common-form-control-checkbox-radio-group',
				'label'    => _x(
					'Countdown to:',
					'The label for the type field of the Countdown Widget.',
					'tribe-events-calendar-pro'
				),
				'children' => [
					[
						'type'         => 'radio',
						'label'        => _x(
							'Next upcoming event',
							'Label for the "countdown to a single event" option.',
							'tribe-events-calendar-pro'
						),
						'button_value' => 'next-event',
					],
					[
						'type'         => 'radio',
						'label'        => _x(
							'Specific event',
							'Label for the "countdown to a single event" option.',
							'tribe-events-calendar-pro'
						),
						'button_value' => 'single-event',
					],
					'type_container' => [
						'type'       => 'fieldset',
						'classes'    => 'tribe-dependent',
						'dependency' => [
							'ID' => 'type-single-event',
							'is-checked' => true,
							'parent'     => '.tribe-common-form-control-checkbox-radio-group',
						],
						'children'   => [
							'event'              => [
								'type'    => 'event-dropdown',
								'label'   => _x(
									'Event:',
									'The label for the event field of the Countdown Widget.',
									'tribe-events-calendar-pro'
								),
								'placeholder' => sprintf(
									/* Translators: 1: single event term */
									esc_html__( 'Select an %1$s', 'tribe-events-calendar-pro' ),
									tribe_get_event_label_singular_lowercase()
								),
								'disabled' => '',
								'options' => [
									[
										'text'  => 'Choose an event.',
										'value' => '',
									],
								],
							],
						],
					],
				],
			],
			'complete'       => [
				'type'        => 'text',
				'label'       => _x(
					'Countdown Completed Text',
					'The label for the field to change the displayed text on countdown completion.',
					'tribe-events-calendar-pro'
				),
				'description' => _x(
					'On “Next Event” type of countdown, this text will only show when there are no events to show.',
					'A note about what this shows when "Next Event" is the countdown type.',
					'tribe-events-calendar-pro'
				),
			],
			'show_seconds'   => [
				'type'  => 'checkbox',
				'label' => _x(
					'Show seconds?',
					'The label for the option to show seconds in the countdown widget.',
					'tribe-events-calendar-pro'
				),
			],
			'jsonld_enable'  => [
				'type'  => 'checkbox',
				'label' => _x(
					'Generate JSON-LD data',
					'The label for the option to enable JSON-LD in the List Widget.',
					'tribe-events-calendar-pro'
				),
			],
		];
	}

	/**
	 * Add full events countdown widget stylesheets to customizer styles array to check.
	 *
	 * @since 5.3.0
	 *
	 * @param array<string> $sheets Array of sheets to search for.
	 *
	 * @return array Modified array of sheets to search for.
	 */
	public function add_full_stylesheet_to_customizer( $sheets ) {
		return array_merge( (array) $sheets, [ 'tribe-events-pro-widgets-v2-countdown-full' ] );
	}

	/**
	 * {@inheritDoc}
	 */
	public function enqueue_assets( $context, $view ) {
		parent::enqueue_assets( $context, $view );

		// Ensure we also have all the other things from Tribe\Events\Views\V2\Assets we need.
		tribe_asset_enqueue( 'tribe-events-pro-widgets-v2-countdown' );
		tribe_asset_enqueue( 'tribe-events-pro-widgets-v2-countdown-skeleton' );

		if ( tribe( Assets::class )->should_enqueue_full_styles() ) {
			tribe_asset_enqueue( 'tribe-events-pro-widgets-v2-countdown-full' );
		}

		tribe_asset_enqueue( 'tribe-events-views-v2-manager' );
	}

	/**
	 * {@inheritDoc}
	 */
	protected function setup_default_arguments() {
		$default_arguments = parent::setup_default_arguments();
		$description       = _x(
			'Displays the time remaining until a specified event.',
			'The description of the Countdown Widget.',
			'tribe-events-calendar-pro'
		);

		$name              = _x(
			'Events Countdown',
			'The name of the Countdown Widget.',
			'tribe-events-calendar-pro'
		);

		$new_arguments = [
			'name'            => esc_html( $name ),
			'description'     => esc_html( $description ),
			'complete'        => esc_attr__( 'Hooray!', 'tribe-events-calendar-pro' ),
			'id_base'         => $this->id_base,
			'widget_options'  => [
				'classname'   => $this->id_base,
				'description' => esc_html( $description ),
			],
			'control_options' => [
				'id_base' => $this->id_base,
			],
		];

		return array_merge( $default_arguments, $new_arguments );
	}

	/**
	 * {@inheritDoc}
	 */
	protected function args_to_context( array $arguments, Context $context ) {
		$alterations                      = parent::args_to_context( $arguments, $context );
		$alterations['widget_title']      = ! empty( $arguments['title'] ) ? $arguments['title'] : '';
		$alterations['jsonld_enable']     = (int) tribe_is_truthy( $arguments['jsonld_enable'] );
		$alterations['show_seconds']      = tribe_is_truthy( $arguments['show_seconds'] );
		$alterations['complete']          = wp_strip_all_tags( $arguments['complete'] );

		// Use set event or the next upcoming one.
		$alterations['event'] = $this->get_fallback_event( $arguments['event'] );

		// The widget only uses one event, but some things expect an array of event(s) here (like JSON_LD).
		$alterations['events'] = (array) $alterations['event'];

		/**
		 * Applies a filter to the args to context.
		 *
		 * @since 5.3.0
		 *
		 * @param array<string,mixed> $alterations The alterations to make to the context.
		 * @param array<string,mixed> $arguments   Current set of arguments.
		 */
		return apply_filters( 'tribe_events_views_v2_countdown_widget_args_to_context', $alterations, $arguments );
	}

	/**
	 * This function grabs and returns the passed event/event ID as an object.
	 * If none is passed it first looks for the next upcoming event
	 * if no upcoming event is found, the last (most recent) event.
	 *
	 * @since 5.3.0
	 *
	 * @param null|int|WP_Post $event  The event ID or post object or `null` to use the global one.
	 *
	 * @return array|mixed|void|WP_Post|null See tribe_get_event() for details.
	 */
	public function get_fallback_event( $event = null ) {
		// If we have an event specified, use it.
		if ( ! empty( $event ) ) {
			return tribe_get_event( $event );
		}

		$future_event = tribe_events()->where( 'start_date', tribe_context()->get( 'now', 'now' ) )->first();

		// If there is an upcoming event, use it.
		if ( ! empty( $future_event ) ) {
			return $future_event;
		}


		// If there are NO upcoming events, use the last event (will show as completed).
		return tribe_events()->where( 'ends_before', tribe_context()->get( 'now', 'now' ) )->order( 'DESC' )->first();
	}
}
