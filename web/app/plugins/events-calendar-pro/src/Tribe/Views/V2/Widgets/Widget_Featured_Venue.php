<?php
/**
 * Featured Venue Widget
 *
 * @since   5.3.0
 *
 * @package Tribe\Events\Views\V2\Widgets
 */

namespace Tribe\Events\Pro\Views\V2\Widgets;

use Tribe\Events\Views\V2\Assets;
use Tribe__Context as Context;
use Tribe\Events\Views\V2\Widgets\Widget_Abstract;

/**
 * Class for the Featured Venue Widget.
 *
 * @since   5.3.0
 *
 * @package Tribe\Events\Views\V2\Widgets
 */
class Widget_Featured_Venue extends Widget_Abstract {

	/**
	 * {@inheritDoc}
	 *
	 * @var string
	 */
	protected $slug = 'tribe_events_featured_venue_widget';

	/**
	 * {@inheritDoc}
	 *
	 * @var string
	 */
	protected $view_slug = 'widget-featured-venue';

	/**
	 * {@inheritDoc}
	 *
	 * @var string
	 */
	protected $asset_slug = 'tribe-events-venue-widget-v2';

	/**
	 * {@inheritDoc}
	 *
	 * @var string
	 */
	protected $view_admin_slug = 'widgets/venue';

	/**
	 * {@inheritDoc}
	 *
	 * @var array<string,mixed>
	 */
	protected $default_arguments = [
		// View options.
		'view'              => null,
		'should_manage_url' => false,

		// Event widget options.
		'id'                => null,
		'alias-slugs'       => null,
		'title'             => '',
		'venue_ID'          => null,
		'count'             => 3,
		'hide_if_empty'     => false,
		'jsonld_enable'     => true,

		// WP_Widget properties.
		'id_base'           => 'tribe-events-venue-widget',
		'name'              => null,
		'widget_options'    => [
			'classname'   => 'tribe-events-venue-widget',
			'description' => null,
		],
		'control_options'   => [
			'id_base' => 'tribe-events-venue-widget',
		],
	];

	/**
	 * {@inheritDoc}
	 */
	public function setup_view( $arguments ) {
		parent::setup_view( $arguments );

		add_filter( 'tribe_customizer_should_print_widget_customizer_styles', '__return_true' );
		add_filter( 'tribe_customizer_inline_stylesheets', [ $this, 'add_full_stylesheet_to_customizer' ], 12, 2 );
	}

	/**
	 * {@inheritDoc}
	 */
	public function enqueue_assets( $context, $view ) {
		parent::enqueue_assets( $context, $view );

		// Ensure we also have all the other things from Tribe\Events\Views\V2\Assets we need.
		tribe_asset_enqueue( 'tribe-events-pro-widgets-v2-featured-venue-skeleton' );

		if ( tribe( Assets::class )->should_enqueue_full_styles() ) {
			tribe_asset_enqueue( 'tribe-events-pro-widgets-v2-featured-venue-full' );
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function setup_default_arguments() {
		$default_arguments = parent::setup_default_arguments();

		$default_arguments['description'] = esc_html_x( 'Displays a list of upcoming events at a specific venue.', 'The description of the Featured Venue Widget.', 'tribe-events-calendar-pro' );
		// @todo update name once this widget is ready to replace the existing featured venue widget.
		$default_arguments['name']                          = esc_html_x( 'Events Featured Venue', 'The name of the Featured Venue.', 'tribe-events-calendar-pro' );
		$default_arguments['widget_options']['description'] = esc_html_x( 'Displays a list of upcoming events at a specific venue.', 'The description of the Featured Venue Widget.', 'tribe-events-calendar-pro' );
		// Setup default title.
		$default_arguments['title'] = '';

		return $default_arguments;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update( $new_instance, $old_instance ) {
		$updated_instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$updated_instance['title']         = wp_strip_all_tags( $new_instance['title'] );
		$updated_instance['venue_ID']      = $new_instance['venue_ID'];
		$updated_instance['count']         = $new_instance['count'];
		$updated_instance['hide_if_empty'] = ! empty( $new_instance['hide_if_empty'] );
		$updated_instance['jsonld_enable'] = ! empty( $new_instance['jsonld_enable'] );

		return $this->filter_updated_instance( $updated_instance, $new_instance );
	}

	/**
	 * {@inheritDoc}
	 */
	public function setup_admin_fields() {
		return [
			'title'         => [
				'label' => _x( 'Title:', 'The label for the field of the title of the Featured Venue Widget.', 'tribe-events-calendar-pro' ),
				'type'  => 'text',
			],
			'venue_ID'      => [
				'type'        => 'venue-dropdown',
				'label'       => _x( 'Venue:', 'The label for the venue field of the Featured Venue Widget.', 'tribe-events-calendar-pro' ),
				'placeholder' => sprintf( /* Translators: 1: single event term */ esc_html__( 'Select an %1$s', 'tribe-events-calendar-pro' ), tribe_get_venue_label_singular() ),
				'disabled'    => '',
				'options'     => [
					[
						'text'  => _x( 'Choose a venue.', 'The label to choose the venue to show in the Featured Venue Widget.', 'tribe-events-calendar-pro' ),
						'value' => '',
					],
				],
				'selected'    => $this->get_default_venue_id(),
			],
			'count'         => [
				'label'   => _x( 'Show:', 'The label for the amount of events to show in the Featured Venue Widget.', 'tribe-events-calendar-pro' ),
				'type'    => 'dropdown',
				'options' => $this->get_limit_options(),
			],
			'hide_if_empty' => [
				'label' => _x( 'Hide this widget if there are no upcoming events.', 'The label for the option to hide the Featured Venue Widget if no upcoming events.', 'tribe-events-calendar-pro' ),
				'type'  => 'checkbox',
			],
			'jsonld_enable' => [
				'label' => _x( 'Generate JSON-LD data', 'The label for the option to enable JSON-LD in the Featured Venue Widget.', 'tribe-events-calendar-pro' ),
				'type'  => 'checkbox',
			],
		];
	}

	/**
	 * Get the options to use in a the limit dropdown.
	 *
	 * @since 5.3.0
	 *
	 * @return array<string,mixed> An array of options with the text and value included.
	 */
	public function get_limit_options() {
		/**
		 * Filter the max limit of events to display in the Featured Venue Widget.
		 *
		 * @since 5.3.0
		 *
		 * @param int The max limit of events to display in the Featured Venue Widget, default 10.
		 */
		$events_limit = apply_filters( 'tribe_events_widget_featured_venue_events_max_limit', 10 );

		$options = [];

		foreach ( range( 1, $events_limit ) as $i ) {
			$options[] = [
				'text'  => $i,
				'value' => $i,
			];
		}

		return $options;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function args_to_context( array $arguments, Context $context ) {
		$alterations = parent::args_to_context( $arguments, $context );

		// Add venue id
		$alterations['venue'] = (int) absint( $arguments['venue_ID'] );

		// Enable JSON-LD?
		$alterations['jsonld_enable'] = (int) tribe_is_truthy( $arguments['jsonld_enable'] );

		// Hide widget if no events.
		$alterations['no_upcoming_events'] = tribe_is_truthy( $arguments['hide_if_empty'] );

		// Add posts per page.
		$alterations['events_per_page'] = (int) isset( $arguments['count'] ) && $arguments['count'] > 0 ? (int) $arguments['count'] : 5;

		/**
		 * Applies a filter to the args to context.
		 *
		 * @since 5.3.0
		 *
		 * @param array<string,mixed> $alterations The alterations to make to the context.
		 * @param array<string,mixed> $arguments   Current set of arguments.
		 */
		return apply_filters( 'tribe_events_views_v2_featured_venue_widget_args_to_context', $alterations, $arguments );
	}

	/**
	 * Add full events featured venue widget stylesheets to customizer styles array to check.
	 *
	 * @since 5.3.0
	 *
	 * @param array<string> $sheets       Array of sheets to search for.
	 * @param string        $css_template String containing the inline css to add.
	 *
	 * @return array Modified array of sheets to search for.
	 */
	public function add_full_stylesheet_to_customizer( $sheets, $css_template ) {
		return array_merge( $sheets, [ 'tribe-events-widgets-v2-events-featured-venue-full' ] );
	}

	/**
	 * Get the first alphabetical venue id as the default.
	 *
	 * @since 5.3.0
	 *
	 * @return int|null $venue_id The venue id for the default venue.
	 */
	public function get_default_venue_id() {

		$venue_id = tribe_venues()
				->fields( 'ids' )
				->order_by( 'post_title', 'ASC' )
				->first();

		/**
		 * Filter the default venue for the Featured Venue Widget.
		 *
		 * @since 5.3.0
		 *
		 * @param int|null              $venue_id The venue id for the default venue.
		 * @param Widget_Featured_Venue $this     This featured widget venue post object.
		 */
		return apply_filters( 'tribe_events_widget_featured_venue_default_venue_id', $venue_id, $this );
	}
}
