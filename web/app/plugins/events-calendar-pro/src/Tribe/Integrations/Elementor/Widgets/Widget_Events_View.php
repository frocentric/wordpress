<?php
/**
 * Events View Elementor Widget.
 *
 * @since   5.4.0
 *
 * @package Tribe\Events\Pro\Integrations\Elementor\Widgets
 */

namespace Tribe\Events\Pro\Integrations\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Plugin;
use Tribe\Events\Pro\Integrations\Elementor\Traits;
use Tribe\Events\Views\V2\Assets;
use Tribe\Events\Views\V2\Manager;

class Widget_Events_View extends Widget_Abstract {
	use Traits\Categories;

	/**
	 * {@inheritdoc}
	 */
	protected static $widget_slug = 'events_view';

	/**
	 * {@inheritdoc}
	 */
	protected $widget_icon = 'fa fa-calendar-alt';

	/**
	 * {@inheritdoc}
	 */
	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		$this->widget_title = __( 'Events View', 'tribe-events-calendar-pro' );
	}

	/**
	 * Render widget output.
	 *
	 * @since 5.4.0
	 */
	protected function render() {
		add_action( 'tribe_events_views_v2_view_template_vars', [ $this, 'filter_template_vars_to_override_is_initial_load' ], 15 );

		$settings = $this->get_settings_for_display();

		if ( isset( $settings['events_per_page_setting'] ) && 'custom' === $settings['events_per_page_setting'] ) {
			$settings['events_per_page'] = $settings['events_per_page_custom'];
		}

		if ( isset( $settings['month_events_per_day_setting'] ) && 'custom' === $settings['month_events_per_day_setting'] ) {
			$settings['month_events_per_day'] = $settings['month_events_per_day_custom'];
		}

		if ( isset( $settings['featured'] ) ) {
			switch ( $settings['featured'] ) {
				case 'exclude':
					$settings['featured'] = false;
					break;
				case 'only':
					$settings['featured'] = true;
					break;
				case 'include':
				default:
					unset( $settings['featured'] );
					break;
			}
		}

		$settings_string = $this->get_shortcode_attribute_string(
			$settings,
			[
				'view',
				'category',
				'featured',
				'date',
				'tribe-bar',
				'events_per_page',
				'month_events_per_day',
				'keyword',
			]
		);

		echo do_shortcode( '[tribe_events ' . $settings_string . ']' );
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

		$views = tribe( Manager::class )->get_registered_views();
		$views = array_filter(
			$views,
			static function ( $view_class, $slug ) {
				return (bool) call_user_func( [ $view_class, 'is_publicly_visible' ] );
			},
			ARRAY_FILTER_USE_BOTH
		);
		$views = array_map( static function( $value ) {
			return tribe( Manager::class )->get_view_label_by_class( $value );
		}, $views );

		$view_selector = [ 'default' => '' ];
		$view_selector = array_merge( $view_selector, $views );

		$this->add_control(
			'tribe-bar',
			[
				'label'        => __( 'Events Bar', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'label_on'     => __( 'Show', 'tribe-events-calendar-pro' ),
				'label_off'    => __( 'Hide', 'tribe-events-calendar-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'view',
			[
				'label'       => __( 'View', 'tribe-events-calendar-pro' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => false,
				'options'     => $view_selector,
			]
		);

		$this->start_controls_tabs(
			'options_tabs'
		);

		$this->start_controls_tab(
			'option_event_tab',
			[
				'label' => __( 'Event Options', 'tribe-events-calendar-pro' ),
			]
		);

		$this->add_control(
			'featured',
			[
				'label'       => __( 'Featured Events', 'tribe-events-calendar-pro' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'include' => [
						'title' => __( 'Include', 'tribe-events-calendar-pro' ),
						'icon'  => 'fa fa-plus',
					],
					'exclude' => [
						'title' => __( 'Exclude', 'tribe-events-calendar-pro' ),
						'icon'  => 'fa fa-minus',
					],
					'only'    => [
						'title' => __( 'Only Featured Events', 'tribe-events-calendar-pro' ),
						'icon'  => 'fa fa-check',
					],
				],
				'default'     => 'include',
				'toggle'      => false,
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

		$this->add_control(
			'keyword',
			[
				'label' => __( 'Keyword', 'tribe-events-calendar-pro' ),
				'description' => __( 'Display events with a specific search keyword.', 'tribe-events-calendar-pro' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => '',
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'option_view_tab',
			[
				'label' => __( 'View Options', 'tribe-events-calendar-pro' ),
			]
		);

		$this->add_control(
			'date',
			[
				'label' => __( 'View Start Date', 'tribe-events-calendar-pro' ),
				'description' => __( 'Date in YYYY-MM-DD or YYYY-MM format.', 'tribe-events-calendar-pro' ) . '<br><br>' . __( 'Note: the Day View only supports YYYY-MM-DD date formats as well as relative date formats like "yesterday", "today", "tomorrow", "+3 days", etc.', 'tribe-events-calendar-pro' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => '',
			]
		);

		$this->add_control(
			'events_per_page_setting',
			[
				'label'       => __( 'Events Per Page', 'tribe-events-calendar-pro' ),
				'description' => __( 'The number of events to display per page.', 'tribe-events-calendar-pro' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'separator'   => 'before',
				'default'     => 'default',
				'options'     => [
					'default' => __( 'Default', 'tribe-events-calendar-pro' ),
					'custom'  => __( 'Custom', 'tribe-events-calendar-pro' ),
				],
			]
		);

		$this->add_control(
			'events_per_page_custom',
			[
				'label'       => __( 'Event Count', 'tribe-events-calendar-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'label_block' => true,
				'default'     => (int) tribe_get_option( 'postsPerPage', 10 ),
				'condition'   => [
					'events_per_page_setting' => 'custom',
				],
			]
		);

		$this->add_control(
			'month_events_per_day_setting',
			[
				'label'       => __( 'Month View Events Per Day', 'tribe-events-calendar-pro' ),
				'description' => __( 'The number of events to display per page day in month view. Defaults to the value set in Events > Settings.', 'tribe-events-calendar-pro' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'separator'   => 'before',
				'default'     => 'default',
				'options'     => [
					'default' => __( 'Default', 'tribe-events-calendar-pro' ),
					'custom'  => __( 'Custom', 'tribe-events-calendar-pro' ),
				],
			]
		);

		$this->add_control(
			'month_events_per_day_custom',
			[
				'label'       => __( 'Event Count', 'tribe-events-calendar-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'label_block' => true,
				'default'     => (int) tribe_get_option( 'monthEventAmount', 3 ),
				'condition'   => [
					'month_events_per_day_setting' => 'custom',
				],
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
		tribe_asset_enqueue( 'tribe-events-views-v2-breakpoints' );
		tribe_asset_enqueue( 'tribe-events-views-v2-manager' );
		tribe_asset_enqueue( 'tribe-events-virtual-skeleton' );
		tribe_asset_enqueue( 'tribe-events-filterbar-views-v2-1-filter-bar-skeleton' );

		if ( tribe( Assets::class )->should_enqueue_full_styles() ) {
			tribe_asset_enqueue( 'tribe-events-virtual-full' );
			tribe_asset_enqueue( 'tribe-events-filterbar-views-v2-1-filter-bar-full' );
		}
	}

	/**
	 * Overrides the is_initial_load variable on render within the preview.
	 *
	 * @since 5.4.0
	 *
	 * @param array<string,mixed> $template_vars Template variables.
	 *
	 * @return array<string,mixed>
	 */
	public function filter_template_vars_to_override_is_initial_load( $template_vars ) {
		if (
			! empty( $_POST['action'] )
			&& 'elementor_ajax' === $_POST['action']
		) {
			$template_vars['is_initial_load'] = true;
		}

		remove_action( 'tribe_events_views_v2_view_template_vars', [ $this, 'filter_template_vars_to_override_is_initial_load' ], 15 );

		return $template_vars;
	}
}
