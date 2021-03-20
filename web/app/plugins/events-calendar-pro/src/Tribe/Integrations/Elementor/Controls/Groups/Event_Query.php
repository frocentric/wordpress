<?php

namespace Tribe\Events\Pro\Integrations\Elementor\Controls\Groups;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Base;
use Tribe\Events\Pro\Integrations\Elementor\Traits as Elementor_Traits;

class Event_Query extends Group_Control_Base {
	use Elementor_Traits\Categories;
	use Elementor_Traits\Tags;

	/**
	 * @var string Control Group slug.
	 */
	protected static $slug = 'tec_elementor_event_query_group';

	/**
	 * @var array Initialized control fields.
	 */
	protected static $fields;

	/**
	 * {@inheritDoc}
	 */
	public static function get_type() {
		return static::$slug;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function init_args( $args ) {
		parent::init_args( $args );
		$args = $this->get_args();
		static::$fields = $this->init_fields_by_name( $args['name'] );
	}

	/**
	 * {@inheritDoc}
	 */
	protected function init_fields() {
		$args = $this->get_args();

		return $this->init_fields_by_name( $args['name'] );
	}

	/**
	 * Initialize controls and tabs via array.
	 *
	 * @since 5.4.0
	 *
	 * @param string $name Control Group name.
	 *
	 * @return array
	 */
	protected function init_fields_by_name( $name ) {
		$fields = [];

		$name .= '_';

		$fields['id_selection'] = [
			'label'       => __( 'Specify Event', 'tribe-events-calendar-pro' ),
			'description' => __( 'Select a specific event by ID.', 'tribe-events-calendar-pro' ),
			'type'        => Controls_Manager::SELECT,
			'options'     => [
				''        => '',
				'current' => __( "Use the current page's event ID", 'tribe-events-calendar-pro' ),
				'custom'  => __( 'Manually enter event ID', 'tribe-events-calendar-pro' ),
			],
			'label_block' => true,
		];

		$fields['id'] = [
			'label'       => __( 'Event ID', 'tribe-events-calendar-pro' ),
			'type'        => Controls_Manager::TEXT,
			'label_block' => true,
			'condition'   => [
				'id_selection' => 'custom',
			],
		];

		$fields['search'] = [
			'label'       => __( 'Search', 'tribe-events-calendar-pro' ),
			'description' => __( 'Find upcoming events via search.', 'tribe-events-calendar-pro' ),
			'type'        => Controls_Manager::TEXT,
			'label_block' => true,
		];

		$fields['slug'] = [
			'label'       => __( 'Event Slug', 'tribe-events-calendar-pro' ),
			'description' => __( 'Find upcoming events via a url-formatted event name.', 'tribe-events-calendar-pro' ),
			'type'        => Controls_Manager::TEXT,
			'label_block' => true,
		];

		$fields['tab_heading'] = [
			'label'       => __( 'Advanced event filtering', 'tribe-events-calendar-pro' ),
			'type'        => Controls_Manager::HEADING,
			'separator'   => 'before',
		];

		$fields['query_tabs'] = [
			'type' => Controls_Manager::TABS,
		];

		$tabs_wrapper = $name . 'query_tabs';
		$date_tab_wrapper = $name . 'date_tab';
		$meta_tab_wrapper = $name . 'meta_tab';
		$search_tab_wrapper = $name . 'search_tab';

		$fields['date_tab'] = [
			'type'         => Controls_Manager::TAB,
			'label'        => __( 'Dates', 'tribe-events-calendar-pro' ),
			'tabs_wrapper' => $tabs_wrapper,
		];

		$fields['starts_when'] = [
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab'    => $date_tab_wrapper,
			'label'        => __( 'Starts', 'tribe-events-calendar-pro' ),
			'type'         => Controls_Manager::SELECT,
			'label_block'  => false,
			'options'      => [
				''            => '',
				'after'       => __( 'After', 'tribe-events-calendar-pro' ),
				'before'      => __( 'Before', 'tribe-events-calendar-pro' ),
				'between'     => __( 'Between', 'tribe-events-calendar-pro' ),
				'on'          => __( 'On', 'tribe-events-calendar-pro' ),
				'on_or_after' => __( 'On or After', 'tribe-events-calendar-pro' ),
			],
		];

		$fields['starts_method'] = [
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab'    => $date_tab_wrapper,
			'label'        => __( 'Date Entry Format', 'tribe-events-calendar-pro' ),
			'type'         => Controls_Manager::CHOOSE,
			'label_block'  => false,
			'default'      => 'date',
			'options'      => [
				'date'       => [
					'title' => __( 'Date', 'tribe-events-calendar-pro' ),
					'icon'  => 'fa fa-calendar-alt',
				],
				'custom'     => [
					'title' => __( 'Custom', 'tribe-events-calendar-pro' ),
					'icon'  => 'fa fa-edit',
				],
			],
			'condition'    => [
				'starts_when!' => [ '' ],
			],
		];

		$fields['start_date'] = [
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab'    => $date_tab_wrapper,
			'label'        => __( 'Date', 'tribe-events-calendar-pro' ),
			'type'         => Controls_Manager::DATE_TIME,
			'label_block'  => true,
			'condition'    => [
				'starts_when!' => [ '', 'between' ],
				'starts_method' => 'date',
			],
		];

		$fields['start_date_custom'] = [
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab'    => $date_tab_wrapper,
			'label'        => __( 'Date', 'tribe-events-calendar-pro' ),
			'description'  => __( 'Enter a date using a standard date format or a relative string like: tomorrow, next week, +5 days, etc', 'tribe-events-calendar-pro' ),
			'type'         => Controls_Manager::TEXT,
			'label_block'  => true,
			'condition'    => [
				'starts_when!' => [ '', 'between' ],
				'starts_method' => 'custom',
			],
		];

		$fields['start_date_start'] = [
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab'    => $date_tab_wrapper,
			'label'        => __( 'Date Lower Boundary', 'tribe-events-calendar-pro' ),
			'type'         => Controls_Manager::DATE_TIME,
			'label_block'  => true,
			'condition'    => [
				'starts_when' => 'between',
				'starts_method' => 'date',
			],
		];

		$fields['start_date_end'] = [
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab'    => $date_tab_wrapper,
			'label'        => __( 'Date Upper Boundary', 'tribe-events-calendar-pro' ),
			'type'         => Controls_Manager::DATE_TIME,
			'label_block'  => true,
			'condition'    => [
				'starts_when' => 'between',
				'starts_method' => 'date',
			],
		];

		$fields['start_date_start_custom'] = [
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab'    => $date_tab_wrapper,
			'label'        => __( 'Date Lower Boundary', 'tribe-events-calendar-pro' ),
			'description'  => __( 'Enter a date using a standard date format or a relative string like: tomorrow, next week, +5 days, etc', 'tribe-events-calendar-pro' ),
			'type'         => Controls_Manager::TEXT,
			'label_block'  => true,
			'condition'    => [
				'starts_when' => 'between',
				'starts_method' => 'custom',
			],
		];

		$fields['start_date_end_custom'] = [
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab'    => $date_tab_wrapper,
			'label'        => __( 'Date Upper Boundary', 'tribe-events-calendar-pro' ),
			'description'  => __( 'Enter a date using a standard date format or a relative string like: tomorrow, next week, +5 days, etc', 'tribe-events-calendar-pro' ),
			'type'         => Controls_Manager::TEXT,
			'label_block'  => true,
			'condition'    => [
				'starts_when' => 'between',
				'starts_method' => 'custom',
			],
		];

		$fields['ends_when'] = [
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab'    => $date_tab_wrapper,
			'label'        => __( 'Ends', 'tribe-events-calendar-pro' ),
			'type'         => Controls_Manager::SELECT,
			'label_block'  => false,
			'separator'    => 'before',
			'options'      => [
				''             => '',
				'after'        => __( 'After', 'tribe-events-calendar-pro' ),
				'before'       => __( 'Before', 'tribe-events-calendar-pro' ),
				'between'      => __( 'Between', 'tribe-events-calendar-pro' ),
				'on'           => __( 'On', 'tribe-events-calendar-pro' ),
				'on_or_before' => __( 'On or Before', 'tribe-events-calendar-pro' ),
			],
		];

		$fields['ends_method'] = [
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab'    => $date_tab_wrapper,
			'label'        => __( 'Date Entry Format', 'tribe-events-calendar-pro' ),
			'type'         => Controls_Manager::CHOOSE,
			'label_block'  => false,
			'default'      => 'date',
			'options'      => [
				'date'       => [
					'title' => __( 'Date', 'tribe-events-calendar-pro' ),
					'icon'  => 'fa fa-calendar-alt',
				],
				'custom'     => [
					'title' => __( 'Custom', 'tribe-events-calendar-pro' ),
					'icon'  => 'fa fa-edit',
				],
			],
			'condition'    => [
				'ends_when!' => [ '' ],
			],
		];

		$fields['end_date'] = [
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab'    => $date_tab_wrapper,
			'label'        => __( 'Date', 'tribe-events-calendar-pro' ),
			'type'         => Controls_Manager::DATE_TIME,
			'label_block'  => true,
			'condition'    => [
				'ends_when!' => [ '', 'between' ],
				'ends_method' => 'date',
			],
		];

		$fields['end_date_custom'] = [
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab'    => $date_tab_wrapper,
			'label'        => __( 'Date', 'tribe-events-calendar-pro' ),
			'description'  => __( 'Enter a date using a standard date format or a relative string like: tomorrow, next week, +5 days, etc', 'tribe-events-calendar-pro' ),
			'type'         => Controls_Manager::TEXT,
			'label_block'  => true,
			'condition'    => [
				'ends_when!' => [ '', 'between' ],
				'ends_method' => 'custom',
			],
		];

		$fields['end_date_start'] = [
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab'    => $date_tab_wrapper,
			'label'        => __( 'Date Lower Boundary', 'tribe-events-calendar-pro' ),
			'type'         => Controls_Manager::DATE_TIME,
			'label_block'  => true,
			'condition'    => [
				'ends_when' => 'between',
				'ends_method' => 'date',
			],
		];

		$fields['end_date_end'] = [
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab'    => $date_tab_wrapper,
			'label'        => __( 'Date Upper Boundary', 'tribe-events-calendar-pro' ),
			'type'         => Controls_Manager::DATE_TIME,
			'label_block'  => true,
			'condition'    => [
				'ends_when' => 'between',
				'ends_method' => 'date',
			],
		];

		$fields['end_date_start_custom'] = [
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab'    => $date_tab_wrapper,
			'label'        => __( 'Date Lower Boundary', 'tribe-events-calendar-pro' ),
			'description'  => __( 'Enter a date using a standard date format or a relative string like: tomorrow, next week, +5 days, etc', 'tribe-events-calendar-pro' ),
			'type'         => Controls_Manager::TEXT,
			'label_block'  => true,
			'condition'    => [
				'ends_when' => 'between',
				'ends_method' => 'custom',
			],
		];

		$fields['end_date_end_custom'] = [
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab'    => $date_tab_wrapper,
			'label'        => __( 'Date Upper Boundary', 'tribe-events-calendar-pro' ),
			'description'  => __( 'Enter a date using a standard date format or a relative string like: tomorrow, next week, +5 days, etc', 'tribe-events-calendar-pro' ),
			'type'         => Controls_Manager::TEXT,
			'label_block'  => true,
			'condition'    => [
				'ends_when' => 'between',
				'ends_method' => 'custom',
			],
		];

		$fields['meta_tab'] = [
			'tabs_wrapper' => $tabs_wrapper,
			'type'         => Controls_Manager::TAB,
			'label'        => __( 'Meta Data', 'tribe-events-calendar-pro' ),
		];

		$fields['all_day'] = [
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab'    => $meta_tab_wrapper,
			'label'       => __( 'All-day Events', 'tribe-events-calendar-pro' ),
			'type'        => Controls_Manager::CHOOSE,
			'toggle'      => false,
			'default'     => 'include',
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
					'title' => __( 'Only All-day Events', 'tribe-events-calendar-pro' ),
					'icon'  => 'fa fa-check',
				],
			],
		];

		$fields['multiday'] = [
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab'    => $meta_tab_wrapper,
			'label'       => __( 'Multi-day Events', 'tribe-events-calendar-pro' ),
			'type'        => Controls_Manager::CHOOSE,
			'toggle'      => false,
			'default'     => 'include',
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
					'title' => __( 'Only Multi-day Events', 'tribe-events-calendar-pro' ),
					'icon'  => 'fa fa-check',
				],
			],
		];

		$fields['featured'] = [
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab'    => $meta_tab_wrapper,
			'label'       => __( 'Featured Events', 'tribe-events-calendar-pro' ),
			'type'        => Controls_Manager::CHOOSE,
			'default'     => 'include',
			'toggle'      => false,
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
		];

		$fields['has_geoloc'] = [
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab'    => $meta_tab_wrapper,
			'label'       => __( 'Geocoded Events', 'tribe-events-calendar-pro' ),
			'type'        => Controls_Manager::CHOOSE,
			'toggle'      => false,
			'default'     => 'include',
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
					'title' => __( 'Only Geocoded Events', 'tribe-events-calendar-pro' ),
					'icon'  => 'fa fa-check',
				],
			],
		];

		$fields['series'] = [
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab'    => $meta_tab_wrapper,
			'label'       => __( 'Recurring Events', 'tribe-events-calendar-pro' ),
			'type'        => Controls_Manager::CHOOSE,
			'toggle'      => false,
			'default'     => 'include',
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
					'title' => __( 'Only Recurring Events', 'tribe-events-calendar-pro' ),
					'icon'  => 'fa fa-check',
				],
			],
		];

		$fields['category'] = [
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab'    => $meta_tab_wrapper,
			'label'       => __( 'Category', 'tribe-events-calendar-pro' ),
			'type'        => Controls_Manager::SELECT2,
			'options'     => $this->get_event_categories(),
			'label_block' => true,
			'multiple'    => true,
			'separator'   => 'before',
		];

		$fields['post_tag'] = [
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab'    => $meta_tab_wrapper,
			'label'       => __( 'Tag', 'tribe-events-calendar-pro' ),
			'type'        => Controls_Manager::SELECT2,
			'options'     => $this->get_event_tags(),
			'label_block' => true,
			'multiple'    => true,
		];

		return $fields;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_default_options() {
		return [
			'popover' => false,
		];
	}
}