<?php

namespace EAddonsForElementor\Modules\Query\Skins;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use EAddonsForElementor\Modules\Query\Skins\Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Carousel extends Base {

    public function _register_controls_actions() {
        if ($this->parent) {
            parent::_register_controls_actions();
            add_action('elementor/element/' . $this->parent->get_name() . '/section_e_query/after_section_end', [$this, 'register_additional_carousel_controls'], 20);
        }        
    }

    public function get_script_depends() {
        if (!wp_script_is('swiper', 'registered')) {
            // fix improved_assets_loading
            wp_register_script(
                    'swiper',
                    //$frontend->get_js_assets_url( 'swiper', 'assets/lib/swiper/' ),
                    ELEMENTOR_ASSETS_URL .'lib/swiper/swiper.min.js',
                    [],
                    '5.3.6',
                    true
            );
        }
        return ['imagesloaded', 'swiper', 'jquery-swiper', 'e-addons-query-carousel'];
    }

    public function get_style_depends() {
        return ['custom-swiper', 'e-addons-common-query', 'e-addons-query-grid', 'e-addons-query-carousel'];
    }

    public function get_id() {
        return 'carousel';
    }
    
    public function get_pid() {
        return 264;
    }

    public function get_title() {
        return esc_html__('Carousel', 'e-addons');
    }

    public function get_docs() {
        return 'https://e-addons.com';
    }

    public function get_icon() {
        return 'eadd-queryviews-carousel';
    }

    public function register_additional_carousel_controls() {

        $this->start_controls_section(
                'section_carousel', [
            'label' => '<i class="eaddicon eadd-queryviews-carousel"></i> ' . esc_html__('Carousel', 'e-addons'),
            'tab' => Controls_Manager::TAB_CONTENT,
                ]
        );

        $this->add_control(
                'speed_slider', [
            'label' => esc_html__('Speed (ms)', 'e-addons'),
            'description' => esc_html__('Duration of transition between slides (in ms)', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => 300,
            'min' => 0,
            'max' => 3000,
            'step' => 10,
            'frontend_available' => true
                ]
        );

        // ------------------------------------
        $this->add_control(
            'effects', [
            'label' => esc_html__('Transition Effects', 'e-addons'),
            'type' => 'ui_selector',
            'label_block' => true,
            'toggle' => false,
            'type_selector' => 'image',
            'columns_grid' => 5,
            'separator' => 'before',
            'options' => [
                'slide' => [
                    'title' => esc_html__('Slide','e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL.'modules/query/assets/img/slider_effects/slide.svg',
                ],
                'fade' => [
                    'title' => esc_html__('Fade','e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL.'modules/query/assets/img/slider_effects/alpha.svg',
                ],
                'cube' => [
                    'title' => esc_html__('Cube','e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL.'modules/query/assets/img/slider_effects/cube.svg',
                ],
                'coverflow' => [
                    'title' => esc_html__('Coverflow','e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL.'modules/query/assets/img/slider_effects/coverflow.svg',
                ],
                'flip' => [
                    'title' => esc_html__('Flip','e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL.'modules/query/assets/img/slider_effects/flip.svg',
                ]
            ],
            'default' => 'slide',
            'render_type' => 'template',
            'frontend_available' => true,
            'prefix_class' => 'e-add-carousel-effect-'
            ]
        );
        

        // ------- slideShadows (true) ------
        $this->add_control(
            'effect_cube_heading', [
                'label' => esc_html__('Cube options', 'e-addons'),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    $this->get_control_id('effects') => ['cube']
                ]
            ]
        );
        
        // ------- cube shadow (true) ------
        $this->add_control(
                'cube_shadow', [
            'label' => esc_html__('Shadow', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('effects') => ['cube']
            ]
                ]
        );
        // ------- fade crossFade (false) ------
        $this->add_control(
            'effect_fade_heading', [
                'label' => esc_html__('Fade option', 'e-addons'),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    $this->get_control_id('effects') => ['fade']
                ]
            ]
        );
        $this->add_control(
                'crossFade', [
            'label' => esc_html__('Crossfade', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('effects') => ['fade']
            ]
                ]
        );
        // ------- coverflow stretch (0) ------
        $this->add_control(
            'effect_coverflow_heading', [
                'label' => esc_html__('Coverflow options', 'e-addons'),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    $this->get_control_id('effects') => ['coverflow']
                ]
            ]
        );
        $this->add_control(
                'coverflow_stretch', [
            'label' => esc_html__('Coverflow Stretch', 'e-addons'),
            'description' => esc_html__('Stretch space between slides (in px)', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => '0',
            //'tablet_default' => '',
            //'mobile_default' => '',
            'min' => 0,
            'max' => 100,
            'step' => 1,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('effects') => ['coverflow']
            ]
                ]
        );
        // ------- coverflow modifier (1) ------
        $this->add_control(
                'coverflow_modifier', [
            'label' => esc_html__('Coverflow Modifier', 'e-addons'),
            'description' => esc_html__('Effect multipler', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => '1',
            //'tablet_default' => '',
            //'mobile_default' => '',
            'min' => 0,
            'max' => 2,
            'step' => 0.1,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('effects') => ['coverflow']
            ]
                ]
        );
        $this->add_control(
            'slideShadows', [
        'label' => esc_html__('Slide Shadows', 'e-addons'),
        'type' => Controls_Manager::SWITCHER,
        'default' => 'yes',
        'frontend_available' => true,
        'condition' => [
            $this->get_control_id('effects') => ['cube', 'flip', 'coverflow']
        ]
            ]
        );
        

        // Directions of slider
        $this->add_control(
            'direction_slider', [
                'label' => esc_html__('Slide Direction', 'e-addons'),
                'type' => Controls_Manager::CHOOSE,
                'toggle' => false,
                'options' => [
                    'horizontal' => [
                        'title' => esc_html__('Horizontal', 'e-addons'),
                        'icon' => 'fas fa-arrows-alt-h',
                    ],
                    'vertical' => [
                        'title' => esc_html__('Vertical', 'e-addons'),
                        'icon' => 'fas fa-arrows-alt-v',
                    ]
                ],
                'default' => 'horizontal',
                'prefix_class' => 'e-add-carousel-direction-',
                'frontend_available' => true,
                'render_type' => 'template',
                'separator' => 'before'
            ]
        );
        
        $this->add_responsive_control(
                'height_container', [
            'label' => esc_html__('Height of viewport', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'vh', '%'],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 800,
                    'step' => 1
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-skin-carousel.swiper-container-vertical' => 'height: {{SIZE}}{{UNIT}};'
            ],
            'condition' => [
                $this->get_control_id('direction_slider') => 'vertical'
            ]
                ]
        );


        $this->add_responsive_control(
                'slidesPerView', [
            'label' => esc_html__('Slides per View', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => '1',
            'separator' => 'before',
            'min' => 1,
            'max' => 12,
            'step' => 1,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('effects') => ['slide','coverflow'],
                $this->get_control_id('slidesPerView_auto') => '',
            ]
                ]
        );
        $this->add_control(
            'slidesPerView_auto', [
                'label' => esc_html__('Enable Auto Slides', 'e-addons'),
                'description' => esc_html__('Enable to use different widths', 'e-addons'),
                'type' => Controls_Manager::SWITCHER,
                //'default' => 'yes',
                'frontend_available' => true,
                'render_type' => 'template',
                'selectors' => [
                    '{{WRAPPER}} .e-add-post-item' => 'width: auto; height: 100%;',
                ],
                'condition' => [
                    $this->get_control_id('effects') => ['slide','coverflow'],
                    $this->get_control_id('direction_slider') => 'horizontal'
                ]
            ]
        );
        $this->add_responsive_control(
            'slidesPerView_auto_height', [
                'label' => esc_html__('slides Height', 'e-addons'),
                'type' => Controls_Manager::SLIDER,
                'separator' => 'after',
                'default' => [
                    'size' => '300',
                    'unit' => 'px'
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 800,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .e-add-post-item img' => 'width: auto; height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    $this->get_control_id('effects') => ['slide','coverflow'],
                    $this->get_control_id('direction_slider') => 'horizontal',
                    $this->get_control_id('slidesPerView_auto') => 'yes'
                ]
            ]
        );
        
        $this->add_responsive_control(
                'slidesPerGroup', [
            'label' => esc_html__('Slides per Group', 'e-addons'),
            'description' => esc_html__('Set numbers of slides to define and enable group sliding. Useful to use with slidesPerView > 1', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => 1,
            'min' => 1,
            'max' => 12,
            'step' => 1,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('effects') => 'slide',
            ]
                ]
        );
        $this->add_responsive_control(
                'slidesColumn', [
            'label' => esc_html__('Slides Column', 'e-addons'),
            'description' => esc_html__('Number of slides per column, for multirow layout.', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => '1',
            'min' => 1,
            'max' => 4,
            'step' => 1,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('effects') => 'slide',
            ]
                ]
        );

        $this->add_control(
                'hr_interface',
                [
                    'type' => Controls_Manager::DIVIDER,
                    'style' => 'thick',
                ]
        );
        $this->start_controls_tabs('carousel_interface');

        // -----Tab navigation
        // xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
        $this->start_controls_tab('tab_carousel_navigation', [
            'label' => esc_html__('Navigation', 'e-addons'),
        ]);

        // --------------- Navigation options ------
        $this->add_control(
                'useNavigation', [
            'label' => esc_html__('Use Navigation', 'e-addons'),
            'description' => esc_html__('Enable to use the navigation arrows.', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
                ]
        );
        // --------- Navigations Arrow Options
        $this->add_control(
            'arrrows_heading', [
                'label' => esc_html__('Arrows', 'e-addons'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    $this->get_control_id('useNavigation') => 'yes'
                ]
        ]
        );
        $this->add_responsive_control(
            'horiz_navigation_shift', [
                'label' => esc_html__('Horizontal Shift', 'e-addons'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '',
                    'unit' => 'px'
                ],
                'range' => [
                    'px' => [
                        'max' => 120,
                        'min' => -120,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .e-add-carousel-controls .swiper-button-prev' => 'left: {{SIZE}}%;',
                    '{{WRAPPER}} .e-add-carousel-controls .swiper-button-next' => 'right: {{SIZE}}%;',
                ],
                'condition' => [
                    $this->get_control_id('useNavigation') => 'yes'
                ]
            ]
        );
        $this->add_responsive_control(
            'vert_navigation_shift', [
                'label' => esc_html__('Vertical Shift', 'e-addons'),
                'type' => Controls_Manager::SLIDER,

                'default' => [
                    'size' => '',
                    'unit' => 'px'
                ],
                'range' => [
                    'px' => [
                        'max' => 120,
                        'min' => -120,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .e-add-carousel-controls .swiper-button-prev, {{WRAPPER}} .e-add-carousel-controls .swiper-button-next' => 'top: {{SIZE}}{{UNIT}};',
                    //'{{WRAPPER}} .e-add-carousel-controls .e-add-container-navigation' => 'top: calc( 50% - {{SIZE}}%);',
                ],
                'condition' => [
                    $this->get_control_id('useNavigation') => 'yes'
                ]
            ]
        );

        // ------- ++++++ @p questo è da veedere +++++++++ START
        $this->add_responsive_control(
            'navigation_space', [
                'label' => esc_html__('Horizontal Space', 'e-addons'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '',
                ],
                'size_units' => '%',
                'range' => [
                    '%' => [
                        'max' => 100,
                        'min' => 20,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .e-add-carousel-controls .e-add-container-navigation' => 'width: {{SIZE}}%;'
                ],
                'condition' => [
                    $this->get_control_id('useNavigation') => 'yes',
                ]
            ]
        );
        $this->add_responsive_control(
            'h_navigation_position', [
                'label' => esc_html__('Horizontal position', 'e-addons'),
                'type' => Controls_Manager::CHOOSE,
                'toggle' => false,
                'options' => [
                    'left: 0%;' => [
                        'title' => esc_html__('Left', 'e-addons'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'transform: translateX(-50%); left: 50%;' => [
                        'title' => esc_html__('Center', 'e-addons'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'left: auto; right: 0;' => [
                        'title' => esc_html__('Right', 'e-addons'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .e-add-carousel-controls .e-add-container-navigation' => '{{VALUE}}'
                ],
                'condition' => [
                    $this->get_control_id('useNavigation') => 'yes',
                    $this->get_control_id('navigation_space[size]!') => ['',0],
                ]
            ]
        );
        $this->add_responsive_control(
            'v_navigation_position', [
                'label' => esc_html__('Vertical position', 'e-addons'),
                'type' => Controls_Manager::CHOOSE,
                'toggle' => false,
                'options' => [
                    '0' => [
                        'title' => esc_html__('Top', 'e-addons'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    '50' => [
                        'title' => esc_html__('Middle', 'e-addons'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    '100' => [
                        'title' => esc_html__('Down', 'e-addons'),
                        'icon' => 'eicon-v-align-bottom',
                    ]
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .e-add-carousel-controls .e-add-container-navigation' => 'top: {{VALUE}}%;'
                ],
                'condition' => [
                    $this->get_control_id('useNavigation') => 'yes',
                ]
            ]
        );
        // ------- ++++++ @p questo è da vedere +++++++++ END
    
		// popover
		$this->add_control(
			'arrows_style_popover', [
				'label' => esc_html__('Arrows Style', 'e-addons'),
				'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label_off' => esc_html__('Default', 'e-addons'),
				'label_on' => esc_html__('Custom', 'e-addons'),
				'return_value' => 'yes',
				'render_type' => 'ui',
                'condition' => [
                    $this->get_control_id('useNavigation') => 'yes'
                ]
			]
		);
		$this->parent->start_popover();



		// --------- Navigations -> Arrows Options
		$this->add_control(
			'heading_navigation_arrow_color', [
				'type' => Controls_Manager::RAW_HTML,
				'show_label' => false,
				'raw' => '<i class="fas fa-tint"></i> <b>' . esc_html__('Color', 'e-addons') . '</b>',
				'content_classes' => 'e-add-inner-heading',
				'condition' => [
					$this->get_control_id('arrows_style_popover') => 'yes',
                    $this->get_control_id('useNavigation') => 'yes'
				]
			]
		);
		$this->add_control(
			'navigation_arrow_color', [
				'label' => esc_html__('Color', 'e-addons'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
                    '{{WRAPPER}} .e-add-carousel-controls .swiper-button-next path, {{WRAPPER}} .swiper-button-prev path' => 'fill: {{VALUE}};',
                    '{{WRAPPER}} .e-add-carousel-controls .swiper-button-next line, {{WRAPPER}} .swiper-button-prev line, {{WRAPPER}} .swiper-button-next polyline, {{WRAPPER}} .swiper-button-prev polyline' => 'stroke: {{VALUE}};',
                ],
				'condition' => [
					$this->get_control_id('arrows_style_popover') => 'yes',
                    $this->get_control_id('useNavigation') => 'yes'
				]
			]
		);
		$this->add_control(
			'navigation_arrow_color_hover', [
				'label' => esc_html__('Hover color', 'e-addons'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
                    '{{WRAPPER}} .e-add-carousel-controls .swiper-button-next:hover path, {{WRAPPER}} .swiper-button-prev:hover path' => 'fill: {{VALUE}};',
                    '{{WRAPPER}} .e-add-carousel-controls .swiper-button-next:hover line, {{WRAPPER}} .swiper-button-prev:hover line, {{WRAPPER}} .swiper-button-next:hover polyline, {{WRAPPER}} .swiper-button-prev:hover polyline' => 'stroke: {{VALUE}};',
                ],
				'condition' => [
					$this->get_control_id('arrows_style_popover') => 'yes',
                    $this->get_control_id('useNavigation') => 'yes'
				]
			]
		);
		//  STYLE: transform
		$this->add_control(
			'heading_navigation_transform', [
				'type' => Controls_Manager::RAW_HTML,
				'show_label' => false,
				'separator_before' => true,
				'raw' => '<i class="fas fa-vector-square"></i> <b>' . esc_html__('Transform', 'e-addons') . '</b>',
				'content_classes' => 'e-add-inner-heading',
				'condition' => [
					$this->get_control_id('arrows_style_popover') => 'yes',
                    $this->get_control_id('useNavigation') => 'yes'
				]
			]
		);
		
	
		$this->add_responsive_control(
			'navigation_stroke_1', [
				'label' => esc_html__('Arrows Weight', 'e-addons'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '',
				],
				'tablet_default' => [
					'size' => '',
				],
				'mobile_default' => [
					'size' => '',
				],
				'range' => [
					'px' => [
						'max' => 30,
						'min' => 0,
						'step' => 1.0000,
					],
				],
				'selectors' => [
                    '{{WRAPPER}} .e-add-carousel-controls .swiper-button-prev polyline, {{WRAPPER}} .e-add-carousel-controls .swiper-button-next polyline, {{WRAPPER}} .swiper-button-prev line, {{WRAPPER}} .swiper-button-next line' => 'stroke-width: {{SIZE}};',
                ],
				'condition' => [
					$this->get_control_id('arrows_style_popover') => 'yes',
                    $this->get_control_id('useNavigation') => 'yes'
				]
			]
		);
		$this->add_responsive_control(
			'navigation_stroke_2', [
				'label' => esc_html__('Line Weight', 'e-addons'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '',
				],
				'range' => [
					'px' => [
						'max' => 30,
						'min' => 0,
						'step' => 1.0000,
					],
				],
				'selectors' => [
                    '{{WRAPPER}} .e-add-carousel-controls .swiper-button-next line, {{WRAPPER}} .e-add-carousel-controls .swiper-button-prev line' => 'stroke-width: {{SIZE}};',
                ],
				'condition' => [
					$this->get_control_id('arrows_style_popover') => 'yes',
                    $this->get_control_id('useNavigation') => 'yes'
				]
			]
		);

		
		$this->add_responsive_control(
			'navigation_size', [
				'label' => esc_html__('Arrows Size', 'e-addons'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 2,
						'min' => 0.10,
						'step' => 0.1,
					],
				],
                'selectors' => [
                    '{{WRAPPER}} .e-add-carousel-controls .swiper-button-next, {{WRAPPER}} .e-add-carousel-controls .swiper-button-prev' => 'transform: scale({{SIZE}}) rotate(0deg);',
                ],
				'condition' => [
					$this->get_control_id('arrows_style_popover') => 'yes',
                    $this->get_control_id('useNavigation') => 'yes'
				]
			]
		);
		$this->add_responsive_control(
			'navigation_size_line', [
				'label' => esc_html__('Line Size', 'e-addons'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 2,
						'min' => 0,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .e-add-carousel-controls .swiper-button-next line, {{WRAPPER}} .e-add-carousel-controls .swiper-button-prev line' => 'transform: scaleX({{SIZE}});',
				],
				'condition' => [
					$this->get_control_id('arrows_style_popover') => 'yes',
                    $this->get_control_id('useNavigation') => 'yes'
				]
			]
		);
		$this->parent->end_popover();
		
        // -------------------- CIRCLE
		$this->add_control(
			'use_navigation_circle', [
				'label' => esc_html__('Circle Style', 'e-addons'),
				'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label_off' => esc_html__('Default', 'e-addons'),
				'label_on' => esc_html__('Custom', 'e-addons'),
				'return_value' => 'yes',
				'render_type' => 'ui',
                'condition' => [
                    $this->get_control_id('useNavigation') => 'yes'
				]
			]
		);
		$this->parent->start_popover();
		$this->add_control(
			'navigation_circle_color', [
				'label' => esc_html__('Color', 'e-addons'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .e-add-carousel-controls .e-add-container-navigation .arrow-wrap:before' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id('use_navigation_circle!') => '',
                    $this->get_control_id('useNavigation') => 'yes'
				]
			]
		);
		$this->add_control(
			'navigation_circle_color_hover', [
				'label' => esc_html__('Hover color', 'e-addons'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .e-add-carousel-controls .e-add-container-navigation .arrow-wrap:hover:before' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id('use_navigation_circle!') => '',
                    $this->get_control_id('useNavigation') => 'yes'
				]
			]
		);
		$this->add_responsive_control(
			'navigation_circle_size', [
				'label' => esc_html__('Circle Size', 'e-addons'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 2,
						'min' => 0.1,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .e-add-carousel-controls .e-add-container-navigation .e-add-navigation-circle .arrow-wrap:before' => 'transform: scale({{SIZE}});',
					'{{WRAPPER}} .e-add-carousel-controls .e-add-container-navigation .e-add-navigation-circle.swiper-button-next .arrow-wrap::before' => 'transform: scale({{SIZE}}) rotate(180deg);',
				],
				'condition' => [
					$this->get_control_id('use_navigation_circle!') => '',
                    $this->get_control_id('useNavigation') => 'yes'
				]
			]
		);
		
		$this->add_responsive_control(
			'horiz_navigation_circle_shift', [
				'label' => esc_html__('Circle Shift', 'e-addons'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => 'px',
				'default' => [
					'size' => '',
					'unit' => 'px'
				],
				'range' => [
					'px' => [
						'max' => 50,
						'min' => -50,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .e-add-carousel-controls .e-add-container-navigation .e-add-navigation-circle.swiper-button-prev .arrow-wrap:before' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .e-add-carousel-controls .e-add-container-navigation .e-add-navigation-circle.swiper-button-next .arrow-wrap:before' => 'right: {{SIZE}}{{UNIT}};',
					
				],
				'condition' => [
					$this->get_control_id('use_navigation_circle!') => '',
                    $this->get_control_id('useNavigation') => 'yes'
				]
			]
		);
		
		$this->add_group_control(
			Group_Control_Border::get_type(), [
				'name' => 'navigation_circle_border',
				'label' => esc_html__('Border', 'e-addons'),
				'selector' => '{{WRAPPER}} .e-add-carousel-controls .e-add-container-navigation .e-add-navigation-circle .arrow-wrap:before',
				
				'condition' => [
					$this->get_control_id('use_navigation_circle!') => '',
                    $this->get_control_id('useNavigation') => 'yes'
				]
			]
		);
		$this->add_control(
			'navigation_circle_border_radius', [
				'label' => esc_html__('Border Radius', 'e-addons'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .e-add-carousel-controls .e-add-container-navigation .e-add-navigation-circle .arrow-wrap:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id('use_navigation_circle!') => '',
                    $this->get_control_id('useNavigation') => 'yes'
				]
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(), [
				'name' => 'navigation_circle_shadow',
				'selector' => '{{WRAPPER}} .e-add-carousel-controls .e-add-container-navigation .e-add-navigation-circle .arrow-wrap:before',
				'condition' => [
					$this->get_control_id('use_navigation_circle!') => '',
                    $this->get_control_id('useNavigation') => 'yes'
				]
			]
		);
		$this->parent->end_popover();
        

        $this->add_control(
                'useNavigation_animationHover', [
            'label' => esc_html__('Use rollover animation', 'e-addons'),
            'description' => esc_html__('Enable for a short animation on rollover.', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'prefix_class' => 'hoveranim-',
            'separator' => 'before',
            'condition' => [
                $this->get_control_id('useNavigation') => 'yes'
            ]
                ]
        );

        $this->end_controls_tab();

        // -----Tab pagination
        // xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
        $this->start_controls_tab('tab_carousel_pagination', [
            'label' => esc_html__('Pagination', 'e-addons'),
        ]);


        $this->add_control(
                'usePagination', [
            'label' => esc_html__('Use Pagination', 'e-addons'),
            'description' => esc_html__('Enable to use the slide progression display system ("bullets", "fraction", "progress").', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
                ]
        );
        $this->add_control(
                'pagination_type', [
            'label' => esc_html__('Pagination Type', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'bullets' => esc_html__('Bullets', 'e-addons'),
                'fraction' => esc_html__('Fraction', 'e-addons'),
                'progressbar' => esc_html__('Progressbar', 'e-addons'),
            ],
            'default' => 'bullets',
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
            ]
                ]
        );
        // ------------ Pagination Fraction Options
        $this->add_control(
                'fraction_heading', [
            'label' => esc_html__('Fraction', 'e-addons'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'fraction',
            ]
                ]
        );
        $this->add_control(
                'fraction_separator', [
            'label' => esc_html__('Fraction text separator', 'e-addons'),
            'description' => esc_html__('The text that separates the 2 numbers', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'frontend_available' => true,
            'default' => '/',
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'fraction',
            ]
                ]
        );

        $this->add_control(
                'fraction_color', [
            'label' => esc_html__('Numbers color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-fraction > *' => 'color: {{VALUE}};',
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'fraction',
            ]
                ]
        );
        $this->add_control(
                'fraction_current_color', [
            'label' => esc_html__('Current Number Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-fraction .swiper-pagination-current' => 'color: {{VALUE}};',
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'fraction',
            ]
                ]
        );
        $this->add_control(
                'fraction_separator_color', [
            'label' => esc_html__('Separator Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-fraction .separator' => 'color: {{VALUE}};',
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'fraction',
            ]
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'fraction_typography',
            'label' => esc_html__('Typography', 'e-addons'),
            'selector' => '{{WRAPPER}} .swiper-pagination-fraction > *',
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'fraction',
            ]
                ]
        );

        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'fraction_typography_current',
            'label' => esc_html__('Current Number Typography', 'e-addons'),
            'selector' => '{{WRAPPER}} .swiper-pagination-fraction .swiper-pagination-current',
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'fraction',
            ]
                ]
        );

        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => esc_html__('fraction_typography_separator', 'e-addons'),
            'label' => 'Separator Typography',
            'selector' => '{{WRAPPER}} .swiper-pagination-fraction .separator',
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'fraction',
            ]
                ]
        );

        $this->add_responsive_control(
                'fraction_space', [
            'label' => esc_html__('Spacing', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '4',
                'unit' => 'px',
            ],
            'tablet_default' => [
                'unit' => 'px',
            ],
            'mobile_default' => [
                'unit' => 'px',
            ],
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => -20,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-fraction .separator' => 'margin: 0 {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'fraction',
            ]
                ]
        );
        // ------------ Pagination Bullets Options
        $this->add_control(
                'bullets_options_heading', [
            'label' => esc_html__('Bullets Options', 'e-addons'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'bullets',
            ]
                ]
        );
        $this->add_control(
                'dynamicBullets', [
            'label' => esc_html__('dynamicBullets', 'e-addons'),
            'description' => esc_html__('Useful when you use bullets pagination with a lot of slides. So it will keep only few bullets visible at the same time.', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => ['bullets', 'custom'],
            ]
                ]
        );
        // ------------ Pagination Custom Options
        $this->add_control(
                'bullets_style', [
            'label' => esc_html__('Bullets Style', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'default' => esc_html__('Default', 'e-addons'),
                'shamso' => esc_html__('Dots', 'e-addons'),
                'timiro' => esc_html__('Circles', 'e-addons'),
                'xusni' => esc_html__('VerticalBars', 'e-addons'),
                'etefu' => esc_html__('Bars', 'e-addons'),
                'xusni' => esc_html__('VerticalBars', 'e-addons'),
                'ubax' => esc_html__('Square', 'e-addons'),
                'magool' => esc_html__('Lines', 'e-addons'),
            //'desta' 	=> esc_html__('Triangles', 'e-addons'),
            //'totit'		=> esc_html__('Icons', 'e-addons'),
            //'zahi' 		=> esc_html__('Timeline', 'e-addons'),
            ],
            'default' => 'default',
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'bullets',
                $this->get_control_id('dynamicBullets') => '',
            ]
                ]
        );
        // Directions of bullets
        $this->add_control(
            'direction_pagination', [
                'label' => esc_html__('Bullets Direction', 'e-addons'),
                'type' => Controls_Manager::CHOOSE,
                'toggle' => false,
                'options' => [
                    'horizontal' => [
                        'title' => esc_html__('Horizontal', 'e-addons'),
                        'icon' => 'fas fa-ellipsis-h',
                    ],
                    'vertical' => [
                        'title' => esc_html__('Vertical', 'e-addons'),
                        'icon' => 'fas fa-ellipsis-v',
                    ]
                ],
                'default' => 'horizontal',
                'prefix_class' => 'e-add-carousel-direction-',
                'frontend_available' => true,
                'render_type' => 'template',
                'separator' => 'after',
                'condition' => [
                    $this->get_control_id('usePagination') => 'yes',
                    $this->get_control_id('pagination_type') => 'bullets',
                    $this->get_control_id('dynamicBullets') => '',
                ]
            ]
        );
        // numbers
        $this->add_control(
                'bullets_numbers', [
            'label' => esc_html__('Show numbers', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('bullets_style!') => 'default',
                $this->get_control_id('pagination_type') => 'bullets',
                $this->get_control_id('dynamicBullets') => '',
            ]
                ]
        );
        // numbers positions
        $this->add_control(
                'bullets_number_color', [
            'label' => esc_html__('Numbers Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet .swiper-pagination-bullet-title' => 'color: {{VALUE}}',
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('bullets_style!') => 'default',
                $this->get_control_id('pagination_type') => 'bullets',
                $this->get_control_id('dynamicBullets') => '',
                $this->get_control_id('bullets_numbers') => 'yes',
            ]
                ]
        );
        // numbers typography
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'bullets_number_typography',
            'label' => esc_html__('Numbers Typography', 'e-addons'),
            'selector' => '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet .swiper-pagination-bullet-title',
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('bullets_style!') => 'default',
                $this->get_control_id('pagination_type') => 'bullets',
                $this->get_control_id('dynamicBullets') => '',
                $this->get_control_id('bullets_numbers') => 'yes',
            ]
                ]
        );
        // BULLETS STYLE
        $this->add_control(
                'bullets_style_heading', [
            'label' => esc_html__('Bullets Style', 'e-addons'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'bullets',
            ]
                ]
        );



        
        $this->add_control(
            'heading_bullet_color', [
                'type' => Controls_Manager::RAW_HTML,
                'show_label' => false,
                'raw' => '<i class="far fa-circle"></i> <b>' . esc_html__('Normal', 'e-addons') . '</b>',
                'content_classes' => 'e-add-inner-heading',
                'condition' => [
                    $this->get_control_id('usePagination') => 'yes',
                    $this->get_control_id('pagination_type') => 'bullets',
                ]
            ]
        );
        $this->add_control(
                'bullets_color', [
            'label' => esc_html__('Bullets Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-bullets.nav--default .swiper-pagination-bullet, {{WRAPPER}} .swiper-pagination-bullets.nav--ubax .swiper-pagination-bullet:after, {{WRAPPER}} .swiper-pagination-bullets.nav--shamso .swiper-pagination-bullet:before, {{WRAPPER}} .swiper-pagination-bullets.nav--xusni .swiper-pagination-bullet:before, {{WRAPPER}} .swiper-pagination-bullets.nav--etefu .swiper-pagination-bullet, {{WRAPPER}} .swiper-pagination-bullets.nav--timiro .swiper-pagination-bullet, {{WRAPPER}} .swiper-pagination-bullets.nav--magool .swiper-pagination-bullet:after' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'bullets',
            ]
                ]
        );
        $this->add_group_control(
                Group_Control_Border::get_type(), [
            'name' => 'border_bullet',
            'label' => esc_html__('Bullets border', 'e-addons'),
            'selector' => '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet',
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'bullets',
            ]
                ]
        );
        
        $this->add_control(
            'heading_current_bullet_color', [
                'type' => Controls_Manager::RAW_HTML,
                'show_label' => false,
                'raw' => '<i class="fas fa-circle"></i> <b>' . esc_html__('Active', 'e-addons') . '</b>',
                'content_classes' => 'e-add-inner-heading',
                'condition' => [
                    $this->get_control_id('usePagination') => 'yes',
                    $this->get_control_id('pagination_type') => 'bullets',
                ]
            ]
        );
        $this->add_control(
                'current_bullet_color', [
            'label' => esc_html__('Active bullet color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-bullets.nav--default .swiper-pagination-bullet-active, 
                 {{WRAPPER}} .swiper-pagination-bullets.nav--ubax .swiper-pagination-bullet-active:after, 
                 {{WRAPPER}} .swiper-pagination-bullets.nav--shamso .swiper-pagination-bullet:not(.swiper-pagination-bullet-active), 
                 {{WRAPPER}} .swiper-pagination-bullets.nav--shamso .swiper-pagination-bullet-active:before, 
                 {{WRAPPER}} .swiper-pagination-bullets.nav--xusni .swiper-pagination-bullet-active:before, 
                 {{WRAPPER}} .swiper-pagination-bullets.nav--etefu .swiper-pagination-bullet-active:before, 
                 {{WRAPPER}} .swiper-pagination-bullets.nav--timiro .swiper-pagination-bullet-active:before, 
                 {{WRAPPER}} .swiper-pagination-bullets.nav--magool .swiper-pagination-bullet-active:after' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .swiper-pagination-bullets.nav--shamso .swiper-pagination-bullet-active::after' => 'box-shadow: inset 0 0 0 3px {{VALUE}};'
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'bullets',
            ]
                ]
        );
        $this->add_control(
            'border_color_current_bullet',
            [
                'type' => Controls_Manager::COLOR,
                'label' => esc_html__('Active bullet border', 'e-addons'),
                'selectors' => [
                    '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet-active:not(.nav--ubax):not(.nav--magool), {{WRAPPER}} .swiper-pagination-bullets.nav--ubax .swiper-pagination-bullet-active::after' => 'border-color: {{VALUE}}',
                ],
                'condition' => [
                    $this->get_control_id('usePagination') => 'yes',
                    $this->get_control_id('pagination_type') => 'bullets',
                    $this->get_control_id('border_bullet_border!') => '',
                ]
            ]
        );
     


        
        $this->add_control(
            'heading_pagination_transform', [
                'type' => Controls_Manager::RAW_HTML,
                'show_label' => false,
                'raw' => '<i class="fas fa-vector-square"></i> <b>' . esc_html__('Transform', 'e-addons') . '</b>',
                'content_classes' => 'e-add-inner-heading',
                'condition' => [
                    $this->get_control_id('usePagination') => 'yes',
                    $this->get_control_id('pagination_type') => 'bullets',
                ]
            ]
        );
        // -------------- Transform
        $this->add_control(
                'pagination_transform_popover', [
            'label' => esc_html__('Transform', 'e-addons'),
            'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
            'label_off' => esc_html__('Default', 'e-addons'),
            'label_on' => esc_html__('Custom', 'e-addons'),
            'return_value' => 'yes',
            'separrator' => 'before',
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'bullets',
                
            ]
                ]
        );
        $this->parent->start_popover();


        $this->add_responsive_control(
                'pagination_bullets_opacity', [
            'label' => esc_html__('Opacity (%)', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
            ],
            'range' => [
                'px' => [
                    'max' => 1,
                    'min' => 0.10,
                    'step' => 0.01,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet:not(.swiper-pagination-bullet-active)' => 'opacity: {{SIZE}};',
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'bullets',
                $this->get_control_id('pagination_transform_popover') => 'yes'
            ]
                ]
        );
        $this->add_responsive_control(
                'pagination_bullets_space', [
            'label' => esc_html__('Space', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => 'px',
            ],
            'tablet_default' => [
                'unit' => 'px',
            ],
            'mobile_default' => [
                'unit' => 'px',
            ],
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 50,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-container-horizontal > .swiper-pagination-bullets .swiper-pagination-bullet' => 'margin: 0 {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .swiper-container-vertical > .swiper-pagination-bullets .swiper-pagination-bullet' => 'margin: {{SIZE}}{{UNIT}} 0;'
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'bullets',
                $this->get_control_id('dynamicBullets') => '',
                $this->get_control_id('pagination_transform_popover') => 'yes'
            ]
                ]
        );
        $this->add_responsive_control(
                'pagination_bullets_dimansion', [
            'label' => esc_html__('Bullets dimension', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => 'px',
            ],
            'tablet_default' => [
                'unit' => 'px',
            ],
            'mobile_default' => [
                'unit' => 'px',
            ],
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .swiper-container-horizontal > .swiper-pagination-bullets.swiper-pagination-bullets-dynamic' => 'height: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .swiper-container-vertical > .swiper-pagination-bullets.swiper-pagination-bullets-dynamic' => 'width: {{SIZE}}{{UNIT}};'
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'bullets',
                $this->get_control_id('pagination_transform_popover') => 'yes'
            ]
                ]
        );
        /* $this->add_responsive_control(
          'current_bullet', [
          'label' => esc_html__('Dimension of active bullet', 'e-addons'),
          'type' => Controls_Manager::SLIDER,
          'default' => [
          'size' => '',
          'unit' => 'px',
          ],
          'tablet_default' => [
          'unit' => 'px',
          ],
          'mobile_default' => [
          'unit' => 'px',
          ],
          'size_units' => ['px'],
          'range' => [
          'px' => [
          'min' => 0,
          'max' => 100,
          ],
          ],
          'selectors' => [
          '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet-active' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',

          ],
          'condition' => [
          $this->get_control_id('usePagination') => 'yes',
          $this->get_control_id('pagination_type') => 'bullets',
          $this->get_control_id('pagination_transform_popover') => 'yes'
          ]
          ]
          ); */
        $this->parent->end_popover();
        // -------------- Position
        $this->add_control(
                'pagination_position_popover', [
            'label' => esc_html__('Position', 'e-addons'),
            'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
            'label_off' => esc_html__('Default', 'e-addons'),
            'label_on' => esc_html__('Custom', 'e-addons'),
            'return_value' => 'yes',
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'bullets',
            ]
                ]
        );
        $this->parent->start_popover();
        $this->add_responsive_control(
                'h_pagination_position', [
            'label' => esc_html__('Position', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'toggle' => true,
            'options' => [
                'text-align: left; left: 0; transform: translate3d(0,0,0);' => [
                    'title' => esc_html__('Left', 'e-addons'),
                    'icon' => 'eicon-h-align-left',
                ],
                'text-align: center; left: 50%; transform: translate3d(-50%,0,0);' => [
                    'title' => esc_html__('Center', 'e-addons'),
                    'icon' => 'eicon-h-align-center',
                ],
                'text-align: right; left: auto; right: 0; transform: translate3d(0,0,0);' => [
                    'title' => esc_html__('Right', 'e-addons'),
                    'icon' => 'eicon-h-align-right',
                ],
            ],
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .swiper-container-horizontal > .swiper-pagination-bullets' => '{{VALUE}}'
            ],
            'condition' => [
                $this->get_control_id('useNavigation') => 'yes',
                $this->get_control_id('pagination_position_popover') => 'yes',
                $this->get_control_id('direction_pagination') => 'horizontal'
            ]
                ]
        );
        $this->add_responsive_control(
                'v_pagination_position', [
            'label' => esc_html__('Position', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'toggle' => true,
            'options' => [
                'top: 0; transform: translate3d(0,0,0);' => [
                    'title' => esc_html__('Left', 'e-addons'),
                    'icon' => 'eicon-v-align-top',
                ],
                'top: 50%; transform: translate3d(0,-50%,0);' => [
                    'title' => esc_html__('Center', 'e-addons'),
                    'icon' => 'eicon-v-align-middle',
                ],
                'top: auto; bottom: 0; transform: translate3d(0,0,0);' => [
                    'title' => esc_html__('Right', 'e-addons'),
                    'icon' => 'eicon-v-align-bottom',
                ],
            ],
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .swiper-container-vertical > .swiper-pagination-bullets' => '{{VALUE}}'
            ],
            'condition' => [
                $this->get_control_id('useNavigation') => 'yes',
                $this->get_control_id('pagination_position_popover') => 'yes',
                $this->get_control_id('direction_pagination') => 'vertical'
            ]
                ]
        );
        $this->add_responsive_control(
                'pagination_bullets_posy', [
            'label' => esc_html__('Shift', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => 'px',
            ],
            'tablet_default' => [
                'unit' => 'px',
            ],
            'mobile_default' => [
                'unit' => 'px',
            ],
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => -160,
                    'max' => 160,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-container-horizontal > .swiper-pagination-bullets' => ' bottom: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .swiper-container-vertical > .swiper-pagination-bullets' => ' right: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'bullets',
                $this->get_control_id('pagination_position_popover') => 'yes'
            ]
                ]
        );

        $this->parent->end_popover();


        // ------------ Pagination progressbar Options
        $this->add_control(
                'progress_heading', [
            'label' => esc_html__('Progress', 'e-addons'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'progressbar',
            ]
                ]
        );
        $this->add_control(
                'progress_color', [
            'label' => esc_html__('Bar Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-progressbar .swiper-pagination-progressbar-fill' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'progressbar',
            ]
                ]
        );
        $this->add_control(
                'progressbar_bg_color', [
            'label' => esc_html__('Background Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-progressbar' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'progressbar',
            ]
                ]
        );
        $this->add_responsive_control(
                'progressbal_size', [
            'label' => esc_html__('Progressbar Size', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '4',
                'unit' => 'px',
            ],
            'tablet_default' => [
                'unit' => 'px',
            ],
            'mobile_default' => [
                'unit' => 'px',
            ],
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 80,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-container-horizontal > .swiper-pagination-progressbar:not(.swiper-pagination-progressbar-opposite), {{WRAPPER}} .swiper-container-vertical > .swiper-pagination-progressbar.swiper-pagination-progressbar-opposite' => 'height: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .swiper-container-vertical > .swiper-pagination-progressbar:not(.swiper-pagination-progressbar-opposite), {{WRAPPER}} .swiper-container-horizontal > .swiper-pagination-progressbar.swiper-pagination-progressbar-opposite' => 'width: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'progressbar',
            ]
                ]
        );
        
        $this->add_control(
            'progressbarOpposite', [
        'label' => esc_html__('Progressbar Opposite', 'e-addons'),
        'type' => Controls_Manager::SWITCHER,
        'frontend_available' => true,
        'condition' => [
            $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'progressbar',
        ]
            ]
    );
        $this->end_controls_tab();

        // -----Tab scrollbar
        // xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
        $this->start_controls_tab('tab_carousel_scrollbar', [
            'label' => esc_html__('ScrollBar', 'e-addons'),
        ]);
        // ----------------- Scrollbar options ------
        $this->add_control(
                'useScrollbar', [
            'label' => esc_html__('Use Scrollbar', 'e-addons'),
            'description' => esc_html__('If "yes", you will use a scrollbar that displays navigation', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
                ]
        );
        $this->add_control(
                'scrollbar_draggable', [
            'label' => esc_html__('Draggable', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('useScrollbar') => 'yes'
            ]
                ]
        );
        $this->add_control(
                'scrollbar_hide', [
            'label' => esc_html__('Hide', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('useScrollbar') => 'yes'
            ]
                ]
        );
        $this->add_control(
                'scrollbar_style_popover', [
            'label' => esc_html__('Style', 'e-addons'),
            'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
            'label_off' => esc_html__('Default', 'e-addons'),
            'label_on' => esc_html__('Custom', 'e-addons'),
            'return_value' => 'yes',
            'condition' => [
                $this->get_control_id('useScrollbar') => 'yes'
            ]
                ]
        );
        $this->parent->start_popover();
        $this->add_control(
                'scrollbar_color', [
            'label' => esc_html__('Bar Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .swiper-scrollbar .swiper-scrollbar-drag' => 'background: {{VALUE}};',
            ],
            'condition' => [
                $this->get_control_id('useScrollbar') => 'yes',
                $this->get_control_id('scrollbar_style_popover') => 'yes',
            ]
                ]
        );
        $this->add_control(
                'scrollbar_bg_color', [
            'label' => esc_html__('Background Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .swiper-scrollbar' => 'background: {{VALUE}};',
            ],
            'condition' => [
                $this->get_control_id('useScrollbar') => 'yes',
                $this->get_control_id('scrollbar_style_popover') => 'yes',
            ]
                ]
        );
        $this->add_responsive_control(
                'scrollbar_size', [
            'label' => esc_html__('Size', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'vh'],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 100,
                    'step' => 1
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-container-horizontal > .swiper-scrollbar' => 'height: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .swiper-container-vertical > .swiper-scrollbar' => 'width: {{SIZE}}{{UNIT}};'
            ],
            'condition' => [
                $this->get_control_id('useScrollbar') => 'yes',
                $this->get_control_id('scrollbar_style_popover') => 'yes',
            ]
                ]
        );
        $this->parent->end_popover();
        /* Da implemantare e verificare .......... */
        $this->end_controls_tab();

        $this->end_controls_tabs();

        // ******************************************************************
        // ******************************************************************
        // ******************************************************************

        $this->add_control(
                'hr_options',
                [
                    'type' => Controls_Manager::DIVIDER,
                    'style' => 'thick',
                ]
        );
        $this->start_controls_tabs('carousel_options');

        // -----Tab Autoplay
        // xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
        $this->start_controls_tab('tab_carousel_autoplay', [
            'label' => esc_html__('Autoplay', 'e-addons'),
        ]);

        // ------------------ Autoplay ------
        $this->add_control(
                'useAutoplay', [
            'label' => esc_html__('Use Autoplay', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
                ]
        );
        $this->add_control(
                'autoplay', [
            'label' => esc_html__('Auto Play', 'e-addons'),
            'description' => esc_html__('Delay between transitions (in ms). If this parameter is not specified (by default), autoplay will be disabled', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => '4000',
            'min' => 0,
            'max' => 15000,
            'step' => 100,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('useAutoplay') => 'yes',
            ]
                ]
        );
        $this->add_control(
                'autoplayStopOnLast', [
            'label' => esc_html__('Autoplay stop on last slide', 'e-addons'),
            'description' => esc_html__('Enable this parameter and autoplay will be stopped when it reaches last slide (has no effect in loop mode)', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('useAutoplay') => 'yes',
                $this->get_control_id('autoplay!') => '',
            ]
                ]
        );
        $this->add_control(
                'autoplayDisableOnInteraction', [
            'label' => esc_html__('Autoplay Disable on interaction', 'e-addons'),
            'description' => esc_html__('Set to "false" and autoplay will not be disabled after user interactions (swipes), it will be restarted every time after interaction', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('useAutoplay') => 'yes',
                $this->get_control_id('autoplay!') => '',
            ]
                ]
        );
        $this->add_control(
                'autoplayPauseOnHover', [
            'label' => esc_html__('Autoplay Pause on Hover', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('autoplayDisableOnInteraction') => '',
                $this->get_control_id('useAutoplay') => 'yes',
                $this->get_control_id('autoplay!') => '',
            ]
                ]
        );

        $this->end_controls_tab();

        // -----Tab freemode
        // xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
        $this->start_controls_tab('tab_carousel_freemode', [
            'label' => esc_html__('FreeMode', 'e-addons'),
        ]);

        // ----------- Free Mode ------
        $this->add_control(
                'freeMode', [
            'label' => esc_html__('Use Free Mode', 'e-addons'),
            'description' => esc_html__('Set to true to enable free drag mode', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true
                ]
        );
        $this->add_control(
            'freeModeMinimumVelocity', [
                'label' => esc_html__('Velocity', 'e-addons'),
                'description' => esc_html__('Minimum touchmove-velocity required to trigger free mode momentum', 'e-addons'),
                'type' => Controls_Manager::NUMBER,
                'default' => 0.02,
                'min' => 0,
                'max' => 1,
                'step' => 0.01,
                'frontend_available' => true,
                'condition' => [
                    $this->get_control_id('freeMode') => 'yes',
                ]
            ]
        );
        $this->add_control(
            'freeModeSticky', [
            'label' => esc_html__('Sticky', 'e-addons'),
            'description' => esc_html__('Set \'yes\' to enable snap to slides positions in free mode', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('freeMode') => 'yes',
            ]
                ]
        );
        $this->add_control(
            'freeModeMomentum_heading', [
                'label' => esc_html__('FreeMode Momentum (optional)', 'e-addons'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    $this->get_control_id('freeMode') => 'yes',
                ]
            ]
        );
        $this->add_control(
                'freeModeMomentum', [
            'label' => esc_html__('Enable Momentum', 'e-addons'),
            'description' => esc_html__('If true, then slide will keep moving for a while after you release it', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            
            'condition' => [
                $this->get_control_id('freeMode') => 'yes',
            ]
                ]
        );
        
        $this->add_control(
                'freeModeMomentumRatio', [
            'label' => esc_html__('Ratio', 'e-addons'),
            'description' => esc_html__('Higher value produces larger momentum distance after you release slider', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => 1,
            'min' => 0,
            'max' => 10,
            'step' => 0.1,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('freeMode') => 'yes',
                $this->get_control_id('freeModeMomentum') => 'yes'
            ]
                ]
        );
        $this->add_control(
                'freeModeMomentumVelocityRatio', [
            'label' => esc_html__('Velocity Ratio', 'e-addons'),
            'description' => esc_html__('Higher value produces larger momentum velocity after you release slider', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => 1,
            'min' => 0,
            'max' => 10,
            'step' => 0.1,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('freeMode') => 'yes',
                $this->get_control_id('freeModeMomentum') => 'yes'
            ]
                ]
        );
        $this->add_control(
                'freeModeMomentumBounce', [
            'label' => esc_html__('Bounce', 'e-addons'),
            'description' => esc_html__('Set to false if you want to disable momentum bounce in free mode', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('freeMode') => 'yes',
                $this->get_control_id('freeModeMomentum') => 'yes'
            ]
                ]
        );
        $this->add_control(
                'freeModeMomentumBounceRatio', [
            'label' => esc_html__('Bounce Ratio', 'e-addons'),
            'description' => esc_html__('Higher value produces larger momentum bounce effect', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => 1,
            'min' => 0,
            'max' => 10,
            'step' => 0.1,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('freeMode') => 'yes',
                $this->get_control_id('freeModeMomentum') => 'yes',
                $this->get_control_id('freeModeMomentumBounce') => 'yes'
            ]
                ]
        );
        
        

        $this->end_controls_tab();

        // -----Tab options
        // xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
        $this->start_controls_tab('tab_carousel_options', [
            'label' => esc_html__('Options', 'e-addons'),
        ]);
        // --------------- spaceBetween ------
        $this->add_responsive_control(
                'spaceBetween', [
            'label' => esc_html__('Space Between', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => 0,
            'tablet_default' => '',
            'mobile_default' => '',
            'min' => 0,
            'max' => 100,
            'step' => 1,
            'frontend_available' => true,
                ]
        );
        $this->add_responsive_control(
                'slidesOffsetBefore', [
            'label' => esc_html__('Slides Offset Before', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => 0,
            'min' => 0,
            'max' => 100,
            'step' => 1,
            'frontend_available' => true,
                ]
        );
        $this->add_responsive_control(
                'slidesOffsetAfter', [
            'label' => esc_html__('Slides Offset After', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => 0,
            'min' => 0,
            'max' => 100,
            'step' => 1,
            'frontend_available' => true,
                ]
        );
        $this->add_control(
                'slidesPerColumnFill', [
            'label' => '<i class="fas fa-th-large"></i> '.esc_html__('Slides per Column Fill', 'e-addons'),
            'description' => esc_html__('Tranisition effect from the slides.', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'separator' => 'before',
            'options' => [
                'row' => esc_html__('Row', 'e-addons'),
                'column' => esc_html__('Column', 'e-addons'),
            ],
            'default' => 'row',
            'frontend_available' => true,
                ]
        );
        // --------------- loop ------
        $this->add_control(
                'loop', [
            'label' => '<i class="fas fa-infinity"></i> '.esc_html__('Loop', 'e-addons'),
            'description' => esc_html__('Set to true to enable continuous loop mode', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'separator' => 'before'
                ]
        );
        /*$this->add_control(
            'loopInsufficientSlides', [
                'label' => esc_html__('Disable Loop on Insufficient Slides', 'e-addons'),
                'description' => esc_html__('When Loop is enabled the slides will be cloned to permit the infine loop navigation, this option prevent Loop if number of Slides are less than Slides per Row', 'e-addons'),
                'type' => Controls_Manager::SWITCHER,
                'frontend_available' => true,
                'condition' => [
                    $this->get_control_id('loop!') => '',
                ]
            ]
        );*/
        
        $this->add_control(
            'show_hidden_slides', [
                'label' => '<i class="far fa-eye"></i> '.esc_html__('Show hidden slides', 'e-addons'),
                'description' => esc_html__('Select to show hidden slides.', 'e-addons'),
                'type' => Controls_Manager::SWITCHER,
                'separator' => 'before',
                'default' => '',
                'frontend_available' => true,
                'selectors' => [
                    '.elementor-top-section.e-overflow-hidden' => 'overflow: hidden', 
                    '{{WRAPPER}} .e-add-posts-container.swiper-container.e-add-skin-carousel.swiper-container-initialized.swiper-container-horizontal' => 'overflow: visible;'
                ],
                'condition' => [
                    $this->get_control_id('direction_slider') => 'horizontal',
                ]
            ]
        );
        // --------------- centerSlides --------
        $this->add_control(
                'centeredSlides', [
            'label' => '<i class="fas fa-plus"></i> '.esc_html__('Centered Slides', 'e-addons'),
            'description' => esc_html__('When enabled active slide will be centered, not on the left as default.', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'separator' => 'before',
            'condition' => [
                $this->get_control_id('effects!') => ['cube', 'flip'],
            ]
                ]
        );
        $this->add_control(
                'centeredSlidesBounds', [
            'label' => esc_html__('Centered Slides Bounds', 'e-addons'),
            'description' => esc_html__('If true, then active slide will be centered without adding gaps at the beginning and end of slider. Required centeredSlides: true. Not intended to be used with loop or pagination.', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('effects!') => ['cube', 'flip'],
                $this->get_control_id('centeredSlides') => 'yes',
            ]
                ]
        );
        $this->add_control(
            'centerInsufficientSlides', [
                'label' => '<i class="fas fa-align-center"></i> '.esc_html__('Center Insufficient Slides', 'e-addons'),
                'description' => esc_html__('When enabled it center slides if the amount of slides less than slidesPerView. Not intended to be used loop mode and slidesPerColumn.', 'e-addons'),
                'type' => Controls_Manager::SWITCHER,
                'frontend_available' => true,
                'condition' => [
                    $this->get_control_id('effects!') => ['cube', 'flip'],
                ]
            ]
        );

        // --------------- autoHeight ------
        $this->add_control(
                'autoHeight', [
            'label' => '<i class="fas fa-ruler-vertical"></i> '.esc_html__('Auto Height', 'e-addons'),
            'description' => esc_html__('Set to true and slider wrapper will adopt its height to the height of the currently active slide.', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'separator' => 'before'
                ]
        );
        // --------------- allowTouchMove ------
        $this->add_control(
            'allowTouchMove', [
                'label' => '<i class="fas fa-hand-paper"></i> '.esc_html__('Allow Touch Move Control', 'e-addons'),
                'description' => esc_html__('If false, then the only way to switch the slide is use of external API functions like slidePrev or slideNext', 'e-addons'),
                'type' => Controls_Manager::SWITCHER,
                'frontend_available' => true,
                'default' => 'yes',
                'separator' => 'before',
            ]
        );
        // --------------- grabCursor ------	
        $this->add_control(
                'grabCursor', [
            'label' => '<i class="fas fa-hand-rock"></i> '.esc_html__('Grab Cursor', 'e-addons'),
            'description' => esc_html__('This option may a little improve desktop usability. If enabled user will see the "grab" cursor when hover on Swiper.', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('allowTouchMove') => 'yes'
            ]
                ]
        );
        // --------------- Keyboard ------
        $this->add_control(
                'keyboardControl', [
            'label' => '<i class="fas fa-keyboard"></i> '.esc_html__('Keyboard Control', 'e-addons'),
            'description' => esc_html__('Enable keyboard control', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
                ]
        );
        // --------------- mouse Wheel ------
        $this->add_control(
            'mousewheelControl', [
                'label' => '<i class="fas fa-mouse"></i> '.esc_html__('Mousewheel Control', 'e-addons'),
                'description' => esc_html__('Enables navigation through slides using mouse wheel', 'e-addons'),
                'type' => Controls_Manager::SWITCHER,
                'frontend_available' => true,
                'separator' => 'before'
            ]
        );
        $this->add_control(
            'mousewheelControl_forceToAxis', [
                'label' => '<i class="fas fa-mouse"></i> '.esc_html__('Force to Axis', 'e-addons'),
                'description' => esc_html__('Set to true to force mousewheel swipes to axis. So in horizontal mode mousewheel will work only with horizontal mousewheel scrolling, and only with vertical scrolling in vertical mode.', 'e-addons'),
                'type' => Controls_Manager::SWITCHER,
                'frontend_available' => true,
                'condition' => [
                    $this->get_control_id('mousewheelControl') => 'yes'
                ]
            ]
        );
        $this->add_control(
            'mousewheelControl_invert', [
                'label' => '<i class="fas fa-mouse"></i> '.esc_html__('Invert', 'e-addons'),
                'description' => esc_html__('Set to true to invert sliding direction.', 'e-addons'),
                'type' => Controls_Manager::SWITCHER,
                'frontend_available' => true,
                'condition' => [
                    $this->get_control_id('mousewheelControl') => 'yes'
                ]
            ]
        );
        
        $this->add_control(
          'watchOverflow', [
          'label' => '<i class="far fa-meh-rolling-eyes"></i> '.esc_html__('Watch Overflow', 'e-addons'),
          'description' => esc_html__('When enabled Swiper will be disabled and hide navigation buttons on case there are not enough slides for sliding.', 'e-addons'),
          'type' => Controls_Manager::SWITCHER,
          'frontend_available' => true,
          'default' => 'yes',
          'separator' => 'before',

          ]
        );
         
        $this->add_control(
                'reverseDirection', [
            'label' => '<i class="fas fa-map-signs"></i> '.esc_html__('Reverse Direction RTL', 'e-addons'),
            'description' => esc_html__('Enables autoplay in reverse direction.', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'separator' => 'before'
                ]
        );
        
        $this->add_control(
        'watchSlidesVisibility', [
            'label' => esc_html__('Watch Slides Visibility', 'e-addons'),
            'description' => esc_html__('WatchSlidesProgress should be enabled. Enable this option and slides that are in viewport will have additional visible class.', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'separator' => 'before',
            //'condition' => [
            //    'watchSlidesProgress' => 'yes',
            //]
            ]
        );

        /* $this->add_control(
          'nested', [
          'label' => esc_html__('Nidificato', 'e-addons'),
          'description' => esc_html__('Set to true on nested Swiper for correct touch events interception. Use only on nested swipers that use same direction as the parent one.', 'e-addons'),
          'type' => Controls_Manager::SWITCHER,
          'frontend_available' => true,
          'separator' => 'before'
          ]
          ); */
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    /* public function register_style_controls() {
      parent::register_style_controls();

      $this->start_controls_section(
      'section_style_carousel',
      [
      'label' => esc_html__( 'Carousel', 'e-addons' ),
      'tab' => Controls_Manager::TAB_STYLE,
      ]
      );
      $this->end_controls_section();
      } */

    public function render_container_before() {
        echo '<div class="e-add-carousel-container">';
    }

    public function render_container_after() {
        echo '<div class="e-add-carousel-controls" data-post-id="' . $this->current_id . '">';
        if ($this->get_instance_value('usePagination')) {
            //@p questi sono degli stili aggiuntivi per i bullets
            $bullets_style = $this->get_instance_value('bullets_style');
            $style_pagination = $this->get_instance_value('pagination_type');
            $dynamicBullets = $this->get_instance_value('dynamicBullets');
            
            $bullets_class = !empty($bullets_style) && $style_pagination == 'bullets' && !$dynamicBullets ? ' e-add-nav-style nav--' . $bullets_style : ' nav--default';

            $paginationDir = !empty($bullets_style) && $style_pagination == 'bullets' && !$dynamicBullets ? $this->get_instance_value('direction_pagination') : $this->get_instance_value('direction_slider');;

            // Add Pagination
            echo '<div class="e-add-container-pagination swiper-container-' . $paginationDir . '"><div class="swiper-pagination pagination-' . $this->parent->get_id() . '-' . $this->current_id . $bullets_class . '"></div></div>';
        }
        if ($this->get_instance_value('useNavigation')) {
            // Add Arrows
            echo '<div class="e-add-container-navigation swiper-container-' . $this->get_instance_value('direction_slider') . '">';
            echo '<div class="swiper-button-prev prev-' . $this->parent->get_id() . '-' . $this->current_id . ' e-add-navigation-circle"><div class="arrow-wrap e-add-cards-touch-button">
            <svg x="0px" y="0px" width="80px" height="80px" xml:space="preserve">
            <polyline fill="none" stroke="#000000" stroke-width="2" stroke-dasharray="0,0" points="40,80 0,40 40,0 "/>
            <line fill="none" stroke="#000000" stroke-width="2" stroke-dasharray="0,0" x1="0" y1="40" x2="80" y2="40"/>
            </svg>
            </div></div>';
            echo '<div class="swiper-button-next next-' . $this->parent->get_id() . '-' . $this->current_id . ' e-add-navigation-circle"><div class="arrow-wrap e-add-cards-touch-button">
            <svg x="0px" y="0px" width="80px" height="80px" xml:space="preserve">
                <polyline fill="none" stroke="#000000" stroke-width="2" points="40,80 80,40 40,0"/>
                <line fill="none" stroke="#000000" stroke-width="2" stroke-dasharray="0,0" x1="80" y1="40" x2="0" y2="40"/>
            </svg>
            </div></div>';
            echo '</div>';
        }
        echo '</div>';

        echo '</div>'; // END: e-add-carousel-container
    }

    public function render_posts_after() {
        if ($this->get_instance_value('useScrollbar')) {
            echo '<div class="swiper-scrollbar"></div>';
        }
    }

    // Classes ----------
    public function get_container_class() {
        return 'swiper-container e-add-skin-' . $this->get_id();
    }

    public function get_wrapper_class() {
        return 'swiper-wrapper e-add-wrapper-' . $this->get_id();
    }

    public function get_item_class() {
        return 'swiper-slide e-add-item-' . $this->get_id();
    }

}