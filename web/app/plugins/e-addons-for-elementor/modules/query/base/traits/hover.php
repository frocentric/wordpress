<?php

namespace EAddonsForElementor\Modules\Query\Base\Traits;

use EAddonsForElementor\Core\Utils\Query as Query_Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;

/**
 * Description of Hover
 *
 * @author fra
 */
trait Hover {

    // ------------------------------------------------------------ [SECTION Hover Effects]
    public function register_controls_hovereffects($widget) { // Widget_Base
        //
        $this->start_controls_section(
                'section_hover_effect', [
                    'label' => '<i class="eaddicon eicon-image-rollover" aria-hidden="true"></i> ' . esc_html__('Hover effect', 'e-addons'),
                    'tab' => Controls_Manager::TAB_CONTENT,
                    /*'condition' => [
                        '_skin' => ['grid', 'filters', 'carousel', 'dualslider'],
                        'style_items!' => 'template',
                    ],*/
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'style_items',
                                'operator' => '!=',
                                'value' => 'template',
                            ],
                            [
                                'name' => '_skin',
                                'operator' => 'in',
                                'value' => ['grid', 'filters', 'carousel', 'dualslider','justifiedgrid','softscroll','expander'],
                            ]
                        ]
                    ]
            ]
        );
        $this->start_controls_tabs('items_this_tab');

        $this->start_controls_tab('tab_hover_block', [
            'label' => esc_html__('Block', 'e-addons'),
        ]);
        $this->add_control(
                'hover_animation', [
            'label' => esc_html__('Hover Animation', 'e-addons'),
            'type' => Controls_Manager::HOVER_ANIMATION,
                ]
        );
        $this->add_control(
                'use_overlay_hover', [
            'label' => esc_html__('Overlay', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'toggle' => false,
            'label_block' => false,
            'separator' => 'before',
            'options' => [
                '1' => [
                    'title' => esc_html__('Yes', 'e-addons'),
                    'icon' => 'fa fa-check',
                ],
                '0' => [
                    'title' => esc_html__('No', 'e-addons'),
                    'icon' => 'fa fa-ban',
                ]
            ],
            'default' => '0',
                ]
        );
        // overlay: color/image/gradient
        $this->add_group_control(
                Group_Control_Background::get_type(), [
            'name' => 'overlay_hover_bgcolor',
            'label' => esc_html__('Background', 'e-addons'),
            'types' => ['classic', 'gradient'],
            'selector' => '{{WRAPPER}} .e-add-post-block.e-add-post-overlayhover:before',
            /* '
              @p il default per background non l'ho capito..
              default' => [
              'background' => 'classic',
              'color' => '#00000080'
              ], */
            'condition' => [
                'use_overlay_hover' => '1',
            ]
                ]
        );
        // overlay: opacity
        $this->add_control(
                'overlay_hover_opacity',
                [
                    'label' => esc_html__('Opacity', 'e-addons'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => .5,
                    ],
                    'range' => [
                        'px' => [
                            'max' => 1,
                            'step' => 0.01,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .e-add-post-block.e-add-post-overlayhover:hover:before' => 'opacity: {{SIZE}};',
                    ],
                    'condition' => [
                        'overlay_color_hover_background' => ['classic', 'gradient'],
                        'use_overlay_hover' => '1',
                    ],
                ]
        );
        
        $this->add_group_control(
                Group_Control_Border::get_type(), [
            'name' => 'overlay_hover_border',
            'label' => esc_html__('Border', 'e-addons'),
            'selector' => '{{WRAPPER}} .e-add-post-block.e-add-post-overlayhover:hover:before',
            //'separator' => 'before',
            'condition' => [
                'use_overlay_hover' => '1',
            ]
                ]
        );
        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(), [
            'name' => 'overlay_hover_shadow',
            'label' => esc_html__('Shadow', 'e-addons'),
            'selector' => '{{WRAPPER}} .e-add-post-item .e-add-post-block:hover',
            'separator' => 'before',
                ]
        );
        
        $this->end_controls_tab();

        $this->start_controls_tab('tab_hover_image', [
            'label' => esc_html__('Image', 'e-addons'),
        ]);
        $this->add_control(
                'hover_image_opacity', [
            'label' => esc_html__('Opacity', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'max' => 1,
                    'min' => 0.10,
                    'step' => 0.01,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-post-block:not(.e-add-hover-effects):hover .e-add-post-image, 
                    {{WRAPPER}} .e-add-post-block.e-add-hover-effects:hover .e-add-post-image' => 'opacity: {{SIZE}};',
            ]
                ]
        );
        $this->add_group_control(
                Group_Control_Css_Filter::get_type(), [
            'name' => 'hover_filters_image',
            'label' => 'Filters image',
            'selector' => '{{WRAPPER}} .e-add-post-block:not(.e-add-hover-effects):hover .e-add-post-image img, {{WRAPPER}} .e-add-post-block.e-add-hover-effects:hover .e-add-post-image img',
                ]
        );

        $this->add_control(
                'use_overlayimg_hover', [
            'label' => esc_html__('Overlay', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'toggle' => false,
            'label_block' => false,
            'separator' => 'before',
            'options' => [
                '1' => [
                    'title' => esc_html__('Yes', 'e-addons'),
                    'icon' => 'fa fa-check',
                ],
                '0' => [
                    'title' => esc_html__('No', 'e-addons'),
                    'icon' => 'fa fa-ban',
                ]
            ],
            'default' => '0',
                ]
        );
        // overlay: color/image/gradient
        $this->add_group_control(
                Group_Control_Background::get_type(), [
            'name' => 'overlayimg_color_hover',
            'label' => esc_html__('Background', 'e-addons'),
            'types' => ['classic', 'gradient'],
            //'selector' => '{{WRAPPER}} .e-add-post-image.e-add-post-overlayhover:before, {{WRAPPER}} .e-add-post-image.e-add-post-overlayhover:before',
            'selector' => '{{WRAPPER}} .e-add-post-block .e-add-post-image.e-add-post-overlayhover:before',

            /* '
              @p il default per background non l'ho capito..
              default' => [
              'background' => 'classic',
              'color' => '#00000080'
              ], */
            'condition' => [
                'use_overlayimg_hover' => '1',
            ]
                ]
        );
        // overlay: opacity
        $this->add_control(
                'overlayimg_opacity',
                [
                    'label' => esc_html__('Opacity', 'e-addons'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => .5,
                    ],
                    'range' => [
                        'px' => [
                            'max' => 1,
                            'step' => 0.01,
                        ],
                    ],
                    'selectors' => [
                    //'{{WRAPPER}} .e-add-post-image.e-add-post-overlayhover:hover:before' => 'opacity: {{SIZE}};',
                    '{{WRAPPER}} .e-add-post-block:hover .e-add-post-image.e-add-post-overlayhover:before' => 'opacity: {{SIZE}};',

                    ],
                    'condition' => [
                        'overlayimg_color_hover_background' => ['classic', 'gradient'],
                        'use_overlayimg_hover' => '1',
                    ],
                ]
        );
        $this->add_control(
            'overlayimg_hovereffects_zoom', [
                'label' => esc_html__('Zoom Effect', 'e-addons'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'separator' => 'before',
                'prefix_class' => 'zoom-effect-',
                
            ]
        );
        $this->add_control(
            'overlayimg_hovereffects_zoom_rate', [
                'label' => esc_html__('Zoom rate', 'e-addons'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 1.2,
                ],
                'range' => [
                    'px' => [
                        'max' => 0.5,
                        'min' => 2,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}.zoom-effect-yes .e-add-post-block:hover .e-add-item.e-add-item_image .e-add-img' => 'transform: scale({{SIZE}});',
                ],
                'condition' => [
                    'overlayimg_hovereffects_zoom!' => '',
                ]
            ]
        );
        // overlay: mix blend mode
        $this->add_control(
                'overlay_blendmode',
                [
                    'label' => esc_html__('Blend Mode', 'e-addons'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => esc_html__('Normal', 'elementor'),
                        'multiply' => 'Multiply',
                        'screen' => 'Screen',
                        'overlay' => 'Overlay',
                        'darken' => 'Darken',
                        'lighten' => 'Lighten',
                        'color-dodge' => 'Color Dodge',
                        'saturation' => 'Saturation',
                        'color' => 'Color',
                        'luminosity' => 'Luminosity',
                    ],
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .e-add-post-image.e-add-post-overlayhover:before' => 'mix-blend-mode: {{VALUE}}',
                    ],
                    'condition' => [
                        'overlay_color_hover_background' => ['classic', 'gradient'],
                        'use_overlayimg_hover' => '1',
                    ],
                ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab('tab_hover_content', [
            'label' => esc_html__('Content', 'e-addons'),
            /* 'condition' => [
              'style_items!' => 'default',
              ], */
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'name' => 'style_items',
                        'operator' => '!=',
                        'value' => 'default',
                    ],
                    [
                        'name' => '_skin',
                        'operator' => 'in',
                        'value' => ['justifiedgrid'],
                    ]/* ,
                  [
                  'relation' => 'and',
                  'terms' => [
                  [
                  'name' => 'item_type',
                  'value' => 'item_custommeta',
                  ],
                  [
                  'name' => 'metafield_type',
                  'operator' => 'in',
                  'value' => ['text','image','file']
                  ]
                  ]
                  ] */
                ]
            ]
        ]);
        $this->add_control(
                'hover_content_animation', [
            'label' => esc_html__('Hover Animation', 'e-addons'),
            'type' => Controls_Manager::HOVER_ANIMATION,
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'style_items',
                        'operator' => '!=',
                        'value' => 'float',
                    ],
                    [
                        'name' => '_skin',
                        'operator' => '!in',
                        'value' => ['justifiedgrid'],
                    ]
                ]
            ]
                ]
        );
        /* ----- FLOAT text-efffects------ */
        $this->add_control(
                'hover_text_heading_float', [
            'label' => esc_html__('Float Style', 'e-addons'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'name' => 'style_items',
                        'operator' => '==',
                        'value' => 'float',
                    ],
                    [
                        'name' => '_skin',
                        'operator' => 'in',
                        'value' => ['justifiedgrid'],
                    ]
                ]
            ]
                ]
        );
        $this->add_control(
            'hover_text_effect', [
                'label' => esc_html__('Transform Origin', 'e-addons'),
                'type' => 'ui_selector',
                'toggle' => true,
                'type_selector' => 'image',
                'columns_grid' => 4,
                'label_block' => false,
                'options' => [
                    '' => [
                        'title' => esc_html__('None', 'e-addons'),
                        'return_val' => 'val',
                        'image' => E_ADDONS_URL . 'assets/img/effects/none.svg',
                    ],
                    'fade' => [
                        'title' => esc_html__('None', 'e-addons'),
                        'return_val' => 'val',
                        'image' => E_ADDONS_URL . 'assets/img/effects/alphaopacity.svg',
                    ],
                    'slidebottom' => [
                        'title' => esc_html__('None', 'e-addons'),
                        'return_val' => 'val',
                        'image' => E_ADDONS_URL . 'assets/img/effects/totop.svg',
                    ],
                    'slidetop' => [
                        'title' => esc_html__('None', 'e-addons'),
                        'return_val' => 'val',
                        'image' => E_ADDONS_URL . 'assets/img/effects/tobottom.svg',
                    ],
                    'slideleft' => [
                        'title' => esc_html__('None', 'e-addons'),
                        'return_val' => 'val',
                        'image' => E_ADDONS_URL . 'assets/img/effects/toright.svg',
                    ],
                    'sliderigh' => [
                        'title' => esc_html__('None', 'e-addons'),
                        'return_val' => 'val',
                        'image' => E_ADDONS_URL . 'assets/img/effects/toleft.svg',
                    ],
                    'cssanimations' => [
                        'title' => esc_html__('None', 'e-addons'),
                        'return_val' => 'val',
                        'image' => E_ADDONS_URL . 'assets/img/effects/css.svg',
                    ],
                ],
                'prefix_class' => 'e-add-hovertexteffect-',
                'toggle' => false,
                'default' => '',
                'frontend_available' => true,
                'render_type' => 'template',
                'separator' => 'before',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'style_items',
                            'operator' => '==',
                            'value' => 'float',
                        ],
                        [
                            'name' => '_skin',
                            'operator' => 'in',
                            'value' => ['justifiedgrid'],
                        ]
                    ]
                ]
            ]
        );
        $this->add_control(
            'hover_text_effect_timingFunction', [
                'label' => esc_html__('Transition Timing function', 'e-addons'),
                'type' => Controls_Manager::SELECT,
                'groups' => Query_Utils::get_anim_timingFunctions(),
                'default' => 'ease-in-out',
                'selectors' => [
                    '{{WRAPPER}} .e-add-post-item .e-add-hover-effect-content' => 'transition-timing-function: {{VALUE}}; -webkit-transition-timing-function: {{VALUE}};',
                ],
                /* 'condition' => [
                'hover_text_effect!') => 'cssanimations',
                'style_items' => 'float',
                ] */
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'hover_text_effect!',
                                    'operator' => '!=',
                                    'value' => 'cssanimations',
                                ],
                                [
                                    'name' => 'style_items',
                                    'operator' => '==',
                                    'value' => 'float',
                                ]
                            ]
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'hover_text_effect!',
                                    'operator' => '!=',
                                    'value' => 'cssanimations',
                                ],
                                [
                                    'name' => '_skin',
                                    'operator' => 'in',
                                    'value' => ['justifiedgrid'],
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );
        // IN
        $this->add_control(
                'heading_hover_text_effect_in', [
            'label' => esc_html__('IN', 'e-addons'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            /* 'condition' => [
              'hover_text_effect') => 'cssanimations',
              'style_items' => 'float',
              ] */
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'hover_text_effect!',
                                'value' => 'cssanimations',
                            ],
                            [
                                'name' => 'style_items',
                                'value' => 'float',
                            ]
                        ]
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'hover_text_effect!',
                                'value' => 'cssanimations',
                            ],
                            [
                                'name' => '_skin',
                                'operator' => 'in',
                                'value' => ['justifiedgrid'],
                            ]
                        ]
                    ]
                ]
            ]
                ]
        );
        $this->add_control(
                'hover_text_effect_animation_in', [
            'label' => esc_html__('Animation effect', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'groups' => Query_Utils::get_anim_in(),
            'default' => 'fadeIn',
            'frontend_available' => true,
            'render_type' => 'template',
            'selectors' => [
                '{{WRAPPER}} .e-add-post-item .e-add-hover-effect-content.e-add-open' => 'animation-name: {{VALUE}}; -webkit-animation-name: {{VALUE}};'
            ],
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'hover_text_effect!',
                                'value' => 'cssanimations',
                            ],
                            [
                                'name' => 'style_items',
                                'value' => 'float',
                            ]
                        ]
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'hover_text_effect!',
                                'value' => 'cssanimations',
                            ],
                            [
                                'name' => '_skin',
                                'operator' => 'in',
                                'value' => ['justifiedgrid'],
                            ]
                        ]
                    ]
                ]
            ]
                ]
        );
        $this->add_control(
                'hover_text_effect_timingFunction_in', [
            'label' => esc_html__('Animation Timing function', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'groups' => Query_Utils::get_anim_timingFunctions(),
            'default' => 'ease-in-out',
            'selectors' => [
                '{{WRAPPER}} .e-add-post-item:hover .e-add-hover-effect-content.e-add-open' => 'animation-timing-function: {{VALUE}}; -webkit-animation-timing-function: {{VALUE}};',
            ],
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'hover_text_effect!',
                                'value' => 'cssanimations',
                            ],
                            [
                                'name' => 'style_items',
                                'value' => 'float',
                            ]
                        ]
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'hover_text_effect!',
                                'value' => 'cssanimations',
                            ],
                            [
                                'name' => '_skin',
                                'operator' => 'in',
                                'value' => ['justifiedgrid'],
                            ]
                        ]
                    ]
                ]
            ]
                ]
        );
        $this->add_control(
                'hover_text_effect_speed_in', [
            'label' => esc_html__('Animation Duration', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => 0.5,
            'min' => 0.1,
            'max' => 2,
            'step' => 0.1,
            'dynamic' => [
                'active' => false,
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-post-item:hover .e-add-hover-effect-content.e-add-open' => 'animation-duration: {{VALUE}}s; -webkit-animation-duration: {{VALUE}}s;',
            ],
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'hover_text_effect!',
                                'value' => 'cssanimations',
                            ],
                            [
                                'name' => 'style_items',
                                'value' => 'float',
                            ]
                        ]
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'hover_text_effect!',
                                'value' => 'cssanimations',
                            ],
                            [
                                'name' => '_skin',
                                'operator' => 'in',
                                'value' => ['justifiedgrid'],
                            ]
                        ]
                    ]
                ]
            ]
                ]
        );
        // OUT
        $this->add_control(
                'heading_hover_text_effect_out', [
            'label' => esc_html__('OUT', 'e-addons'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'hover_text_effect!',
                                'value' => 'cssanimations',
                            ],
                            [
                                'name' => 'style_items',
                                'value' => 'float',
                            ]
                        ]
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'hover_text_effect!',
                                'value' => 'cssanimations',
                            ],
                            [
                                'name' => '_skin',
                                'operator' => 'in',
                                'value' => ['justifiedgrid'],
                            ]
                        ]
                    ]
                ]
            ]
                ]
        );
        $this->add_control(
                'hover_text_effect_animation_out', [
            'label' => esc_html__('Animation effect', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'groups' => Query_Utils::get_anim_out(),
            'default' => 'fadeOut',
            'frontend_available' => true,
            'render_type' => 'template',
            'selectors' => [
                '{{WRAPPER}} .e-add-post-item .e-add-hover-effect-content.e-add-close' => 'animation-name: {{VALUE}}; -webkit-animation-name: {{VALUE}};'
            ],
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'hover_text_effect!',
                                'value' => 'cssanimations',
                            ],
                            [
                                'name' => 'style_items',
                                'value' => 'float',
                            ]
                        ]
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'hover_text_effect!',
                                'value' => 'cssanimations',
                            ],
                            [
                                'name' => '_skin',
                                'operator' => 'in',
                                'value' => ['justifiedgrid'],
                            ]
                        ]
                    ]
                ]
            ]
                ]
        );

        $this->add_control(
                'hover_text_effect_timingFunction_out', [
            'label' => esc_html__('Animation Timing function', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'groups' => Query_Utils::get_anim_timingFunctions(),
            'default' => 'ease-in-out',
            'selectors' => [
                '{{WRAPPER}} .e-add-post-item .e-add-hover-effect-content.e-add-close' => 'animation-timing-function: {{VALUE}}; -webkit-animation-timing-function: {{VALUE}};',
            ],
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'hover_text_effect!',
                                'value' => 'cssanimations',
                            ],
                            [
                                'name' => 'style_items',
                                'value' => 'float',
                            ]
                        ]
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'hover_text_effect!',
                                'value' => 'cssanimations',
                            ],
                            [
                                'name' => '_skin',
                                'operator' => 'in',
                                'value' => ['justifiedgrid'],
                            ]
                        ]
                    ]
                ]
            ]
                ]
        );
        $this->add_control(
                'hover_text_effect_speed_out', [
            'label' => esc_html__('Animation Duration', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => 0.5,
            'min' => 0.1,
            'max' => 2,
            'step' => 0.1,
            'dynamic' => [
                'active' => false,
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-post-item .e-add-hover-effect-content.e-add-close' => 'animation-duration: {{VALUE}}s; -webkit-animation-duration: {{VALUE}}s;',
            ],
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'hover_text_effect!',
                                'value' => 'cssanimations',
                            ],
                            [
                                'name' => 'style_items',
                                'value' => 'float',
                            ]
                        ]
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'hover_text_effect!',
                                'value' => 'cssanimations',
                            ],
                            [
                                'name' => '_skin',
                                'operator' => 'in',
                                'value' => ['justifiedgrid'],
                            ]
                        ]
                    ]
                ]
            ]
                ]
        );
        $this->end_controls_tab();

        $this->end_controls_tabs();


        $this->end_controls_section();
    }

}
