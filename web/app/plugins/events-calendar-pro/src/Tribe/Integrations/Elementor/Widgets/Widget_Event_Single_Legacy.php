<?php
/**
 * Event Single Elementor Widget.
 *
 * @since   5.4.0
 *
 * @package Tribe\Events\Pro\Integrations\Elementor\Widgets
 */

namespace Tribe\Events\Pro\Integrations\Elementor\Widgets;

use Elementor\Controls_Manager;
use Tribe\Events\Views\V2\Assets;
use Tribe\Events\Views\V2\Template_Bootstrap;
use Tribe__Utils__Array as Arr;

class Widget_Event_Single_Legacy extends Widget_Abstract {
	use Traits\Event_Query;

	/**
	 * {@inheritdoc}
	 */
	protected static $widget_slug = 'event_single_legacy';

	/**
	 * {@inheritdoc}
	 */
	protected $widget_icon = 'fa fa-calendar-day';

	/**
	 * {@inheritdoc}
	 */
	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		$this->widget_title = __( 'Event', 'tribe-events-calendar-pro' );
	}

	/**
	 * Render widget output.
	 *
	 * @since 5.4.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$event_query_settings = $this->get_event_query_settings( $settings );
		$event_query_settings = $this->set_id_from_repository_if_unset( $event_query_settings );

		/** @var Template_Bootstrap $bootstrap */
		$bootstrap = tribe( Template_Bootstrap::class );

		global $post, $wp_query;
		$backup_query = $wp_query;

		$repository = $this->build_event_repository( $event_query_settings );
		$repository->per_page( 1 );
		$posts      = $repository->all();

		if ( ! $posts ) {
			return;
		}

		$wp_query = $repository->get_query_for_posts( $posts );
		$post     = $posts[0];
		setup_postdata( $post );

		$selector = $this->get_unique_selector();

		add_filter( 'tribe_events_views_v2_bootstrap_should_display_single', '__return_true' );
		$html = '<div class="single-tribe_events">' . $bootstrap->get_view_html() . '</div>';
		remove_filter( 'tribe_events_views_v2_bootstrap_should_display_single', '__return_true' );

		$wp_query = $backup_query;

		// We need to keep resetting since inside of the single V1 view we call `the_post()`.
		wp_reset_postdata();

		$this->enqueue_render_assets( $settings );

		$styles = $this->get_style_overrides( $settings, $selector );
		$styles = '<style>' . implode( "\n", $styles ) . '</style>';

		echo $styles . $html;
	}

	/**
	 * Get the style overrides for the output.
	 *
	 * @since 5.4.0
	 *
	 * @param array $settings Array of Elementor Widget settings.
	 * @param string $selector CSS Selector for the Elementor Widget.
	 *
	 * @return array
	 */
	protected function get_style_overrides( $settings = [], $selector ) {
		$setting_style_map = [
			'after_html'         => '.tribe-events-after-html',
			'all_events_link'    => '.tribe-events-back',
			'before_html'        => '.tribe-events-before-html',
			'calendar_links'     => '.tribe-events-cal-links',
			'cost'               => '.tribe-events-cost',
			'custom_fields'      => '.tribe-events-meta-group-other',
			'description'        => '.tribe-events-single-event-description',
			'details_categories' => [
				'.tribe-events-event-categories-label',
				'.tribe-events-event-categories-label + dd',
			],
			'details_cost'       => [
				'.tribe-events-event-cost-label',
				'.tribe-events-event-cost-label + dd',
			],
			'details_date'       => [
				'.tribe-events-start-date-label',
				'.tribe-events-start-date-label + dd',
			],
			'details_tags'       => [
				'.tribe-events-event-categories-label + dd + dt',
				'.tribe-event-tags',
			],
			'details_time'       => [
				'.tribe-events-start-time-label',
				'.tribe-events-start-time-label + dd',
			],
			'featured_image'     => '.tribe-events-event-image',
			'footer'             => '#tribe-events-footer',
			'navigation'         => '.tribe-events-nav-pagination',
			'notices'            => '.tribe-events-notices',
			'organizer'          => '.tribe-events-meta-group-organizer',
			'organizer_email'    => [
				'.tribe-organizer-email-label',
				'.tribe-organizer-email',
			],
			'organizer_name'     => '.tribe-organizer',
			'organizer_phone'    => [
				'.tribe-organizer-tel-label',
				'.tribe-organizer-tel',
			],
			'organizer_url'      => [
				'.tribe-organizer-url-label',
				'.tribe-organizer-url',
			],
			'related_events'     => [
				'.tribe-events-related-events-title',
				'.tribe-related-events',
			],
			'tickets'            => [
				'.cart',
				'.event-tickets',
			],
			'title'              => '.tribe-events-single-event-title',
			'venue'              => '.tribe-events-single-section.tribe-events-event-meta.secondary',
			'venue_name'         => '.tribe-venue',
			'venue_location'     => '.tribe-venue-location',
			'venue_map'          => '.tribe-events-venue-map',
			'venue_phone'        => [
				'.tribe-venue-tel-label',
				'.tribe-venue-tel',
			],
			'venue_url'          => [
				'.tribe-venue-url-label',
				'.tribe-venue-url',
			],
			'virtual_video_embed' => '.tribe-events-virtual-single-video-embed',
			'virtual_watch_button' => [
				'.tribe-events-virtual-link-button',
				'.tribe-events-virtual-single-zoom-details__meta-group--link-button',
			],
			'virtual_zoom_link' => '.tribe-events-virtual-single-zoom-details__meta-group--zoom-link',
			'virtual_zoom_phone' => '.tribe-events-virtual-single-zoom-details__meta-group--zoom-phone',
		];

		$styles = [];

		/*---------------------------------------------------
		 * Setup our simple deactivations.
		 *---------------------------------------------------*/
		foreach ( $setting_style_map as $setting => $map ) {
			if ( tribe_is_truthy( Arr::get( $settings, $setting ) ) ) {
				continue;
			}

			if ( ! is_array( $map ) ) {
				$map = [ $map ];
			}

			// Prepend the widget's ID to each selector.
			$selectors = array_map( function( $value ) use ( $selector ) {
				return "{$selector} {$value}";
			}, $map );

			$selectors = implode( ', ', $selectors );

			$styles[] = "{$selectors} { display: none !important; }";
		}

		/*---------------------------------------------------
		 * We have some more complicated deactivations to check.
		 *---------------------------------------------------*/
		if ( ! tribe_is_truthy( Arr::get( $settings, 'date-time' ) ) ) {
			if ( ! tribe_is_truthy( Arr::get( $settings, 'cost' ) ) ) {
				$styles[] = "{$selector} .tribe-events-schedule { display: none !important; }";
			} else {
				$styles[] = "{$selector} .tribe-events-schedule h2 { display: none !important; }";
			}
		}

		if ( ! tribe_is_truthy( Arr::get( $settings, 'details' ) ) ) {
			if ( tribe_is_truthy( Arr::get( $settings, 'organizer' ) ) ) {
				$styles[] = "{$selector} .tribe-events-meta-group-details { display: none !important; }";
			} else {
				$styles[] = "{$selector} .tribe-events-single-section.tribe-events-event-meta.primary { display: none !important; }";
			}
		}

		if (
			tribe_is_truthy( Arr::get( $settings, 'venue_map' ) )
			&& ! tribe_is_truthy( Arr::get( $settings, 'venue_name' ) )
			&& ! tribe_is_truthy( Arr::get( $settings, 'venue_location' ) )
			&& ! tribe_is_truthy( Arr::get( $settings, 'venue_phone' ) )
			&& ! tribe_is_truthy( Arr::get( $settings, 'venue_url' ) )
		) {
			$styles[] = "{$selector} .tribe-events-meta-group-venue { display: none !important; }";
			$styles[] = "{$selector} .tribe-events-venue-map { float: none; margin-left: 20px; }";
		}

		if (
			! tribe_is_truthy( Arr::get( $settings, 'virtual_watch_button' ) )
			&& ! tribe_is_truthy( Arr::get( $settings, 'virtual_zoom_link' ) )
			&& ! tribe_is_truthy( Arr::get( $settings, 'virtual_zoom_phone' ) )
		) {
			$styles[] = "{$selector} .tribe-events-virtual-single-zoom-details { display: none !important; }";
		}

		return $styles;
	}

	/**
	 * Enqueues necessary assets for rendering the widget.
	 *
	 * @since 5.4.0
	 *
	 * @param array $settings Array of Elementor Widget settings.
	 */
	protected function enqueue_render_assets( $settings = [] ) {
		/*---------------------------------------------------
		 * Enqueue some stuff if certain settings are truthy.
		 *---------------------------------------------------*/
		tribe_asset_enqueue_group( 'events-styles' );

		if ( tribe_is_truthy( Arr::get( $settings, 'related-events' ) ) ) {
			tribe_asset_enqueue( 'tribe-events-full-pro-calendar-style' );
			tribe_asset_enqueue( 'tribe-events-calendar-pro-style' );
			tribe_asset_enqueue( 'tribe-events-calendar-pro-override-style' );
		}

		if ( tribe_is_truthy( Arr::get( $settings, 'tickets' ) ) ) {
			tribe_asset_enqueue( 'event-tickets-reset-css' );
			tribe_asset_enqueue( 'event-tickets-tickets-css' );
			tribe_asset_enqueue( 'event-tickets-tickets-rsvp-css' );
			tribe_asset_enqueue( 'event-tickets-tickets-rsvp-js' );
			tribe_asset_enqueue( 'event-tickets-attendees-list-js' );
			tribe_asset_enqueue( 'event-tickets-details-js' );
			tribe_asset_enqueue( 'tribe-tickets-forms-style' );

			if ( class_exists( 'Tribe__Tickets__Main' ) ) {
				if ( tribe_tickets_new_views_is_enabled() || tribe_tickets_rsvp_new_views_is_enabled() ) {
					tribe_asset_enqueue( 'tribe-tickets-loader' );
				}

				if ( tribe_tickets_new_views_is_enabled() ) {
					tribe_asset_enqueue( 'tribe-common-responsive' );
					tribe_asset_enqueue( 'tribe-tickets-utils' );
				}
			}
		}

		tribe_asset_enqueue( 'tribe-events-virtual-single-skeleton' );

		if ( tribe( Assets::class )->should_enqueue_full_styles() ) {
			tribe_asset_enqueue( 'tribe-events-virtual-single-full' );
		}
	}

	/**
	 * Register widget controls.
	 *
	 * @since 5.4.0
	 */
	protected function _register_controls() {
		$this->add_event_query_section();

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'tribe-events-calendar-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title',
			[
				'label'        => __( 'Title', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'date-time',
			[
				'label'        => __( 'Date/Time', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
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
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'description',
			[
				'label'        => __( 'Description', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'featured_image',
			[
				'label'        => __( 'Featured Image', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'notices',
			[
				'label'        => __( 'Notices', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		if ( class_exists( 'Tribe__Tickets__Main' ) ) {
			$this->add_control(
				'tickets',
				[
					'label'        => __( 'RSVP/Tickets', 'tribe-events-calendar-pro' ),
					'description'  => __( 'RSVP/Tickets are not available in preview mode.', 'tribe-events-calendar-pro' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_block'  => false,
					'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
					'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
					'return_value' => 'yes',
					'default'      => 'no',
				]
			);
		}

		$this->start_controls_tabs(
			'options_tabs'
		);

		$this->start_controls_tab(
			'option_details_tab',
			[
				'label' => __( 'Details', 'tribe-events-calendar-pro' ),
			]
		);

		$this->add_base_and_child_controls(
			'details',
			[
				'label'        => __( 'Event Details', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			],
			[
				'date'          => __( 'Date', 'tribe-events-calendar-pro' ),
				'time'          => __( 'Time', 'tribe-events-calendar-pro' ),
				'cost'          => __( 'Cost', 'tribe-events-calendar-pro' ),
				'categories'    => __( 'Categories', 'tribe-events-calendar-pro' ),
				'tags'          => __( 'Tags', 'tribe-events-calendar-pro' ),
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'option_organizer_tab',
			[
				'label' => __( 'Organizer', 'tribe-events-calendar-pro' ),
			]
		);

		$this->add_base_and_child_controls(
			'organizer',
			[
				'label'        => __( 'Organizer', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			],
			[
				'name'       => __( 'Name', 'tribe-events-calendar-pro' ),
				'phone'      => __( 'Phone', 'tribe-events-calendar-pro' ),
				'email'      => __( 'Email', 'tribe-events-calendar-pro' ),
				'url'        => __( 'Website', 'tribe-events-calendar-pro' ),
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'option_venue_tab',
			[
				'label' => __( 'Venue', 'tribe-events-calendar-pro' ),
			]
		);

		$this->add_base_and_child_controls(
			'venue',
			[
				'label'        => __( 'Venue', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			],
			[
				'name'       => __( 'Name', 'tribe-events-calendar-pro' ),
				'location'   => __( 'Location', 'tribe-events-calendar-pro' ),
				'phone'      => __( 'Phone', 'tribe-events-calendar-pro' ),
				'url'        => __( 'Website', 'tribe-events-calendar-pro' ),
				'map'        => __( 'Map', 'tribe-events-calendar-pro' ),
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'custom_section',
			[
				'label' => __( 'Custom Content', 'tribe-events-calendar-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'custom_fields',
			[
				'label'        => __( 'Custom Fields', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'before_html',
			[
				'label'        => __( 'Before HTML', 'tribe-events-calendar-pro' ),
				'description'  => __( 'Customizable HTML to display before the event.', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'after_html',
			[
				'label'        => __( 'After HTML', 'tribe-events-calendar-pro' ),
				'description'  => __( 'Customizable HTML to display after the event.', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'navigation_section',
			[
				'label' => __( 'Navigation', 'tribe-events-calendar-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'all_events_link',
			[
				'label'        => __( 'All Events Link', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'calendar_links',
			[
				'label'        => __( 'Calendar Links', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'related_events',
			[
				'label'        => __( 'Related Events', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'footer',
			[
				'label'        => __( 'Footer', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'navigation',
			[
				'label'        => __( 'Event Navigation', 'tribe-events-calendar-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => [
					'footer' => 'yes',
				],
			]
		);
		$this->end_controls_section();

		if ( class_exists( 'Tribe\\Events\\Virtual\\Plugin' ) ) {
			$this->start_controls_section(
				'virtual_section',
				[
					'label' => __( 'Virtual', 'tribe-events-calendar-pro' ),
					'tab'   => Controls_Manager::TAB_CONTENT,
				]
			);

			$this->add_control(
				'virtual_video_embed',
				[
					'label'        => __( 'Video Embed', 'tribe-events-calendar-pro' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_block'  => false,
					'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
					'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				]
			);

			$this->add_control(
				'virtual_watch_button',
				[
					'label'        => __( 'Watch Button', 'tribe-events-calendar-pro' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_block'  => false,
					'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
					'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				]
			);

			$this->add_control(
				'virtual_zoom_link',
				[
					'label'        => __( 'Zoom Link', 'tribe-events-calendar-pro' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_block'  => false,
					'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
					'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				]
			);

			$this->add_control(
				'virtual_zoom_phone',
				[
					'label'        => __( 'Zoom Dial-in Info', 'tribe-events-calendar-pro' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_block'  => false,
					'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
					'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				]
			);
			$this->end_controls_section();
		}
	}

	/**
	 * Enqueues assets for this widget.
	 */
	public function enqueue_editor_assets() {
		tribe_asset_enqueue_group( 'events-styles' );
		tribe_asset_enqueue( 'tribe-events-full-pro-calendar-style' );
		tribe_asset_enqueue( 'tribe-events-calendar-pro-style' );
		tribe_asset_enqueue( 'tribe-events-calendar-pro-override-style' );
		tribe_asset_enqueue( 'tribe-events-virtual-single-skeleton' );

		tribe_asset_enqueue( 'event-tickets-reset-css' );
		tribe_asset_enqueue( 'event-tickets-tickets-css' );
		tribe_asset_enqueue( 'event-tickets-tickets-rsvp-css' );
		tribe_asset_enqueue( 'event-tickets-tickets-rsvp-js' );
		tribe_asset_enqueue( 'event-tickets-attendees-list-js' );
		tribe_asset_enqueue( 'event-tickets-details-js' );
		tribe_asset_enqueue( 'tribe-tickets-forms-style' );

		if ( class_exists( 'Tribe__Tickets__Main' ) ) {
			if ( tribe_tickets_new_views_is_enabled() || tribe_tickets_rsvp_new_views_is_enabled() ) {
				tribe_asset_enqueue( 'tribe-tickets-loader' );
			}

			if ( tribe_tickets_new_views_is_enabled() ) {
				tribe_asset_enqueue( 'tribe-common-responsive' );
				tribe_asset_enqueue( 'tribe-tickets-utils' );
			}
		}

		if ( tribe( Assets::class )->should_enqueue_full_styles() ) {
			tribe_asset_enqueue( 'tribe-events-virtual-single-full' );
		}
	}

	/**
	 * Add base CHOOSE control and conditional CHOOSE sub-controls.
	 *
	 * @since 5.4.0
	 *
	 * @param string $parent_id Control parent ID.
	 * @param array $parent_args Control parent Arguments.
	 * @param array $controls Collection of dependent child controls.
	 */
	protected function add_base_and_child_controls( $parent_id, array $parent_args, array $controls ) {
		$this->add_control( $parent_id, $parent_args );

		foreach ( $controls as $id => $label ) {
			$this->add_control(
				"{$parent_id}_{$id}",
				[
					'label'        => $label,
					'type'         => Controls_Manager::SWITCHER,
					'label_block'  => false,
					'label_on'     => __( 'Yes', 'tribe-events-calendar-pro' ),
					'label_off'    => __( 'No', 'tribe-events-calendar-pro' ),
					'return_value' => 'yes',
					'default'      => 'yes',
					'condition'    => [
						$parent_id => 'yes',
					],
				]
			);
		}
	}
}
