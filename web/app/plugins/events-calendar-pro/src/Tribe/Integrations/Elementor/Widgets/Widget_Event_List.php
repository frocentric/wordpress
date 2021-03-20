<?php
/**
 * Countdown View Elementor Widget.
 *
 * @since   5.4.0
 *
 * @package Tribe\Events\Pro\Integrations\Elementor\Widgets
 */

namespace Tribe\Events\Pro\Integrations\Elementor\Widgets;

use Elementor\Controls_Manager;
use Tribe\Events\Views\V2\Assets;

class Widget_Event_List extends Widget_Abstract {
	/**
	 * {@inheritdoc}
	 */
	protected static $widget_slug = 'events_list_widget';

	/**
	 * {@inheritdoc}
	 */
	protected $widget_icon = 'fa fa-list';

	/**
	 * @var string
	 */
	protected $shortcode = 'tribe_events_list';

	/**
	 * {@inheritdoc}
	 */
	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		$this->widget_title = __( 'Events List', 'tribe-events-calendar-pro' );
	}

	/**
	 * Render widget output.
	 *
	 * @since 5.4.0
	 */
	protected function render() {
		$settings        = $this->get_settings_for_display();
		$settings_string = $this->get_shortcode_attribute_string(
			$settings,
			[
				'category',
				'city',
				'cost',
				'country',
				'limit',
				'organizer',
				'phone',
				'region',
				'street',
				'venue',
				'zip',
			]
		);

		echo do_shortcode( '[tribe_events_list ' . $settings_string . ']' );
	}

	/**
	 * Register widget controls.
	 *
	 * @since 5.4.0
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'tribe-events-calendar-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'limit',
			[
				'label'        => __( 'Maximum Events', 'tribe-events-calendar-pro' ),
				'description'  => __( 'The maximum number of events that this widget should show.', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::TEXT,
				'label_block'  => true,
				'default'      => '',
			]
		);

		$this->start_controls_tabs(
			'options_tabs'
		);

		$this->start_controls_tab(
			'option_view_tab',
			[
				'label' => __( 'View Options', 'tribe-events-calendar-pro' ),
			]
		);

		$this->add_control(
			'cost',
			[
				'label'        => __( 'Cost', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'organizer',
			[
				'label'        => __( 'Organizer', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'venue',
			[
				'label'        => __( 'Venue', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'street',
			[
				'label'        => __( 'Street', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'country',
			[
				'label'        => __( 'Country', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'city',
			[
				'label'        => __( 'City', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'region',
			[
				'label'        => __( 'Region', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'zip',
			[
				'label'        => __( 'Zip', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'phone',
			[
				'label'        => __( 'Phone', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'separator'    => 'before',
			]
		);

		$this->end_controls_tab();
		$this->start_controls_tab(
			'option_event_tab',
			[
				'label' => __( 'Event Options', 'tribe-events-calendar-pro' ),
			]
		);

		$this->add_control(
			'category',
			[
				'label'       => __( 'Category', 'tribe-events-calendar-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->get_event_categories(),
				'label_block' => true,
				'multiple'    => true,
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Enqueues assets for this widget.
	 *
	 * @since 5.4.0
	 */
	public function enqueue_editor_assets() {
		tribe_asset_enqueue( 'tribe-events-views-v2-manager' );
		tribe_asset_enqueue( 'tribe-events-widgets-v2-events-list-skeleton' );
		tribe_asset_enqueue( 'tribe-events-virtual-widgets-v2-events-list-skeleton' );

		if ( tribe( Assets::class )->should_enqueue_full_styles() ) {
			tribe_asset_enqueue( 'tribe-events-widgets-v2-events-list-full' );
			tribe_asset_enqueue( 'tribe-events-virtual-widgets-v2-events-list-full' );
		}

	}
}
