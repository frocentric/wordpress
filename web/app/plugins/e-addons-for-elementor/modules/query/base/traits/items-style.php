<?php

namespace EAddonsForElementor\Modules\Query\Base\Traits;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;
//use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

/**
 * Description of label
 *
 * @author fra
 */
trait Items_Style {

    // ----------------------------------------------------------
    public function controls_items_base_style($target) {

        // ------------ BASE
        $target->add_responsive_control(
                'item_align',
                [
                    'label' => esc_html__('Alignment', 'elementor'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left' => [
                            'title' => esc_html__('Left', 'elementor'),
                            'icon' => 'eicon-text-align-left',
                        ],
                        'center' => [
                            'title' => esc_html__('Center', 'elementor'),
                            'icon' => 'eicon-text-align-center',
                        ],
                        'right' => [
                            'title' => esc_html__('Right', 'elementor'),
                            'icon' => 'eicon-text-align-right',
                        ],
                        'justify' => [
                            'title' => esc_html__('Justify', 'elementor'),
                            'icon' => 'eicon-text-align-justify',
                        ],
                    ],
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} {{CURRENT_ITEM}}' => 'text-align: {{VALUE}} !important;',
                    ],
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'operator' => '!in',
                                'value' => ['item_image', 'item_imageoricon', 'item_avatar'],
                            ],
                            [
                                'name' => 'display_inline',
                                'operator' => '!=',
                                'value' => 'inline-block',
                            ]
                        ]
                    ]
                ]
        );
        $target->add_responsive_control(
                'item_flex_align',
                [
                    'label' => esc_html__('Alignment', 'elementor'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'flex-start' => [
                            'title' => esc_html__('Left', 'e-addons'),
                            'icon' => 'eicon-h-align-left',
                        ],
                        'center' => [
                            'title' => esc_html__('Center', 'e-addons'),
                            'icon' => 'eicon-h-align-center',
                        ],
                        'flex-end' => [
                            'title' => esc_html__('Right', 'e-addons'),
                            'icon' => 'eicon-h-align-right',
                        ]
                    ],
                    'default' => '',
                    'toggle' => true,
                    'selectors' => [
                        '{{WRAPPER}} {{CURRENT_ITEM}}' => 'display: flex; justify-content: {{VALUE}};',
                    ],
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'operator' => 'in',
                                'value' => ['item_image', 'item_imageoricon', 'item_avatar'],
                            ],
                            [
                                'name' => 'use_bgimage',
                                'value' => '',
                            ],
                            [
                                'name' => 'display_inline',
                                'operator' => '!=',
                                'value' => 'inline-block',
                            ]
                        ]
                    ]
                ]
        );
        /*
          'conditions' => [
          'relation' => 'and',
          'terms' => [
          [
          'name' => 'item_type',
          'operator' => '!in',
          'value' => ['item_image','item_imageoricon', 'item_avatar','item_label'],
          ],
          [
          'name' => 'display_inline',
          'value' => '',
          ],
          [
          'relation' => 'and',
          'terms' => [
          [
          'name' => 'item_type',
          'value' => 'item_label'
          ],
          [
          'name' => 'label_html_type',
          'value' => 'image'
          ]
          ]

          ]
          ]
          ]
         */
        $target->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'item_typography',
            'label' => esc_html__('Typography', 'e-addons'),
            'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}, {{WRAPPER}} {{CURRENT_ITEM}} > *, {{WRAPPER}} {{CURRENT_ITEM}} a, {{WRAPPER}} {{CURRENT_ITEM}} a > *, {{WRAPPER}} {{CURRENT_ITEM}} .elementor-button',
            'separator' => 'before',
            /*'global' => [
                'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
            ],*/
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => '!in',
                        'value' => ['item_image', 'item_avatar', 'item_imageoricon', 'item_author', 'item_label', 'item_custommeta'],
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'value' => 'item_label'
                            ],
                            [
                                'name' => 'label_html_type',
                                'operator' => '!=',
                                'value' => 'image'
                            ]
                        ]
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'value' => 'item_custommeta'
                            ],
                            [
                                'name' => 'metafield_type',
                                'operator' => '!in',
                                'value' => ['image', 'oembed']
                            ]
                        ]
                    ]
                ]
            ]
                ]
        );
        $target->add_control(
                'item_space', [
            'label' => esc_html__('Space', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
            ],
            'size_units' => ['px', '%'],
            'range' => [
                '%' => [
                    'min' => -100,
                    'max' => 100,
                ],
                'px' => [
                    'min' => -150,
                    'max' => 150,
                ]
            ],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}' => 'margin-bottom: {{SIZE}}{{UNIT}};'
            ],
            'dynamic' => [
                'active' => false
            ],
                ]
        );
        $target->add_control(
                'item_z_index', [
            'label' => esc_html__('Z Index', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'min' => 0,
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}' => 'position: relative; z-index: {{VALUE}};'
            ],
            'dynamic' => [
                'active' => false
            ],
                ]
        );
    }

    // ----------------------------------------------------------
    public function controls_items_colors_style($target) {

        // ------------ COLORS
        $target->add_control(
                'colors_heading',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="fas fa-tint"></i> <b>' . esc_html__('Colors', 'e-addons') . '</b>',
                    'content_classes' => 'e-add-inner-heading',
                    'separator' => 'before',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'operator' => '!in',
                                'value' => ['item_image', 'item_imageoricon', 'item_avatar'],
                            ]
                        ]
                    ]
                ]
        );
        $target->add_control(
                'color_item', [
            'label' => esc_html__('Text Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} > *' => 'color: {{VALUE}};',
                '{{WRAPPER}} {{CURRENT_ITEM}} a' => 'color: {{VALUE}};',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => '!in',
                        'value' => ['item_image', 'item_imageoricon', 'item_avatar'],
                    ]
                ]
            ]
                ]
        );
        $target->add_control(
                'bgcolor_item', [
            'label' => esc_html__('Background Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}:not(.e-add-item_readmore) > *' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} {{CURRENT_ITEM}} a.e-add-button' => 'background-color: {{VALUE}};',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => '!in',
                        'value' => ['item_image', 'item_imageoricon', 'item_avatar', 'item_author'],
                    ]
                ]
            ]
                ]
        );
        $target->add_group_control(
                Group_Control_Text_Shadow::get_type(),
                [
                    'name' => 'shadow_item',
                    'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} > *, {{WRAPPER}} {{CURRENT_ITEM}} a',
                    'condition' => [
                        'item_type!' => ['item_image', 'item_imageoricon', 'item_avatar'],
                    ]
                ]
        );
        $target->add_control(
                'color_item_separator', [
            'label' => esc_html__('Separator Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-term-item .e-add-separator' => 'color: {{VALUE}};',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_termstaxonomy'],
                    ],
                    [
                        'name' => 'block_enable',
                        'value' => '',
                    ]
                ]
            ]
                ]
        );
    }

    // ----------------------------------------------------------
    public function controls_items_colorshover_style($target) {

        // ------- COLORS - HOVER
        $target->add_control(
                'colors_hover_heading',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="far fa-hand-pointer"></i> <b>' . esc_html__('Hover Colors', 'e-addons') . '</b>',
                    'content_classes' => 'e-add-inner-heading',
                    'separator' => 'before',
                    'conditions' => [
                        'relation' => 'or',
                        'terms' => [
                            [
                                'relation' => 'and',
                                'terms' => [
                                    [
                                        'name' => 'item_type',
                                        'operator' => 'in',
                                        'value' => ['item_readmore', 'item_guid'],
                                    ],
                                    [
                                        'name' => 'use_link',
                                        'operator' => '!=',
                                        'value' => ''
                                    ]
                                ]
                            ],
                            [
                                'relation' => 'and',
                                'terms' => [
                                    [
                                        'name' => 'item_type',
                                        'operator' => 'in',
                                        'value' => ['item_custommeta'],
                                    ],
                                    [
                                        'name' => 'link_to',
                                        'operator' => '!=',
                                        'value' => ''
                                    ]
                                ]
                            ],
                            [
                                'relation' => 'and',
                                'terms' => [
                                    [
                                        'name' => 'item_type',
                                        'operator' => 'in',
                                        'value' => ['item_author'],
                                    ],
                                    [
                                        'name' => 'author_link',
                                        'operator' => '!=',
                                        'value' => ''
                                    ]
                                ]
                            ],
                            [
                                'relation' => 'and',
                                'terms' => [
                                    [
                                        'name' => 'item_type',
                                        'operator' => '!in',
                                        'value' => [
                                            'item_image',
                                            'item_imageoricon',
                                            'item_avatar',
                                            'item_posttype',
                                            'item_date',
                                            'item_registered',
                                            'item_author',
                                            'item_readmore',
                                            'item_guid',
                                            //'item_termstaxonomy',
                                            'item_content',
                                            'item_excerpt',
                                            'item_description',
                                            'item_taxonomy',
                                            'item_custommeta',
                                            'item_caption',
                                            'item_alternativetext',
                                            'item_imagemeta',
                                            'item_mimetype',
                                            'item_counts'
                                        ],
                                    ],
                                    [
                                        'name' => 'use_link',
                                        'operator' => '!=',
                                        'value' => ''
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
        );
        $target->add_control(
                'hover_color_item', [
            'label' => esc_html__('Hover Text Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} a:hover' => 'color: {{VALUE}};',
            ],
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'operator' => 'in',
                                'value' => ['item_readmore', 'item_guid'],
                            ],
                            [
                                'name' => 'use_link',
                                'operator' => '!=',
                                'value' => ''
                            ]
                        ]
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'operator' => 'in',
                                'value' => ['item_custommeta'],
                            ],
                            [
                                'name' => 'link_to',
                                'operator' => '!=',
                                'value' => ''
                            ]
                        ]
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'operator' => 'in',
                                'value' => ['item_author'],
                            ],
                            [
                                'name' => 'author_link',
                                'operator' => '!=',
                                'value' => ''
                            ]
                        ]
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'operator' => '!in',
                                'value' => [
                                    'item_image',
                                    'item_imageoricon',
                                    'item_avatar',
                                    'item_posttype',
                                    'item_date',
                                    'item_registered',
                                    'item_author',
                                    'item_readmore',
                                    'item_guid',
                                    //'item_termstaxonomy',
                                    'item_content',
                                    'item_excerpt',
                                    'item_description',
                                    'item_taxonomy',
                                    'item_custommeta',
                                    'item_caption',
                                    'item_alternativetext',
                                    'item_imagemeta',
                                    'item_mimetype',
                                    'item_counts'
                                ],
                            ],
                            [
                                'name' => 'use_link',
                                'operator' => '!=',
                                'value' => ''
                            ]
                        ]
                    ]
                ]
            ]
                ]
        );
        $target->add_control(
                'hover_bgcolor_item', [
            'label' => esc_html__('Hover Background Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}:not(.e-add-item_readmore) > *:hover' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} {{CURRENT_ITEM}} a.e-add-button:hover' => 'background-color: {{VALUE}};',
            ],
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'operator' => 'in',
                                'value' => ['item_readmore', 'item_guid'],
                            ],
                            [
                                'name' => 'use_link',
                                'operator' => '!=',
                                'value' => ''
                            ]
                        ]
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'operator' => 'in',
                                'value' => ['item_author'],
                            ],
                            [
                                'name' => 'author_link',
                                'operator' => '!=',
                                'value' => ''
                            ]
                        ]
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'operator' => 'in',
                                'value' => ['item_custommeta'],
                            ],
                            [
                                'name' => 'link_to',
                                'operator' => '!=',
                                'value' => ''
                            ]
                        ]
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'operator' => '!in',
                                'value' => [
                                    'item_image',
                                    'item_imageoricon',
                                    'item_avatar',
                                    'item_posttype',
                                    'item_date',
                                    'item_registered',
                                    'item_author',
                                    'item_readmore',
                                    'item_guid',
                                    //'item_termstaxonomy',
                                    'item_content',
                                    'item_excerpt',
                                    'item_description',
                                    'item_taxonomy',
                                    'item_custommeta',
                                    'item_caption',
                                    'item_alternativetext',
                                    'item_imagemeta',
                                    'item_mimetype',
                                    'item_counts'
                                ],
                            ],
                            [
                                'name' => 'use_link',
                                'operator' => '!=',
                                'value' => ''
                            ]
                        ]
                    ]
                ]
            ]
                ]
        );
    }

    // ----------------------------------------------------------
    // ------------ AUTHOR-BOX
    public function controls_items_author_style($target) {
        $target->add_control(
                'author_heading', [
            'type' => Controls_Manager::RAW_HTML,
            'show_label' => false,
            'raw' => '<i class="fas fa-user"></i> <b>' . esc_html__('Author', 'e-addons') . '</b>',
            'content_classes' => 'e-add-inner-heading',
            'separator' => 'before',
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'value' => 'item_author',
                    ]
                ]
            ]
                ]
        );
        $target->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'item_authorname_typography',
            'label' => esc_html__('Name Typography', 'e-addons'),
            'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-author-display_name',
            'separator' => 'before',
            /*'global' => [
                'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
            ],*/
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'value' => 'item_author',
                    ]
                ]
            ]
                ]
        );
        $target->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'item_authorbio_typography',
            'label' => esc_html__('Bio Typography', 'e-addons'),
            'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-author-description',
            'separator' => 'before',
            /*'global' => [
                'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
            ],*/
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'value' => 'item_author',
                    ]
                ]
            ]
                ]
        );

        $target->add_responsive_control(
                'items_author_flow', [
            'label' => esc_html__('Flow', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'toggle' => true,
            'options' => [
                'row' => [
                    'title' => esc_html__('Row', 'e-addons'),
                    'icon' => 'eicon-h-align-left',
                ],
                'row-reverse' => [
                    'title' => esc_html__('Row-Reverse', 'e-addons'),
                    'icon' => 'eicon-h-align-right',
                ],
                'column' => [
                    'title' => esc_html__('Column', 'e-addons'),
                    'icon' => 'eicon-v-align-top',
                ],
                'column-reverse' => [
                    'title' => esc_html__('Column-Reverse', 'e-addons'),
                    'icon' => 'eicon-v-align-bottom',
                ]
            ],
            'prefix_class' => 'flow-',
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-post-author' => 'flex-flow: {{VALUE}};',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'value' => 'item_author',
                    ]
                ]
            ]
                ]
        );
        $target->add_responsive_control(
                'positions_in_row', [
            'label' => esc_html__('Row Position', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'toggle' => true,
            'options' => [
                'flex-start' => [
                    'title' => esc_html__('Left', 'e-addons'),
                    'icon' => 'eicon-h-align-left',
                ],
                'center' => [
                    'title' => esc_html__('Center', 'e-addons'),
                    'icon' => 'eicon-h-align-center',
                ],
                'flex-end' => [
                    'title' => esc_html__('Right', 'e-addons'),
                    'icon' => 'eicon-h-align-right',
                ]
            ],
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}.e-add-item_image, {{WRAPPER}} {{CURRENT_ITEM}} .e-add-post-author' => 'justify-content: {{VALUE}};',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_author'],
                    ],
                    [
                        'name' => 'items_author_flow',
                        'operator' => 'in',
                        'value' => ['row', 'row-reverse'],
                    ]
                ]
            ]
                ]
        );
        $target->add_responsive_control(
                'position_in_col', [
            'label' => esc_html__('Column Position', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'toggle' => true,
            'options' => [
                'flex-start' => [
                    'title' => esc_html__('Left', 'e-addons'),
                    'icon' => 'eicon-h-align-left',
                ],
                'center' => [
                    'title' => esc_html__('Center', 'e-addons'),
                    'icon' => 'eicon-h-align-center',
                ],
                'flex-end' => [
                    'title' => esc_html__('Right', 'e-addons'),
                    'icon' => 'eicon-h-align-right',
                ]
            ],
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-post-author' => 'align-items: {{VALUE}};',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_author'],
                    ],
                    [
                        'name' => 'items_author_flow',
                        'operator' => 'in',
                        'value' => ['column', 'column-reverse'],
                    ]
                ]
            ]
                ]
        );
    }

    // ----------------------------------------------------------
    public function controls_items_spaces_style($target) {

        // ------------ SPACES
        $target->add_control(
                'spacing_heading', [
            'type' => Controls_Manager::RAW_HTML,
            'show_label' => false,
            'raw' => '<i class="fas fa-arrows-alt"></i> <b>' . esc_html__('Spacing', 'e-addons') . '</b>',
            'content_classes' => 'e-add-inner-heading',
            'separator' => 'before',
                ]
        );
        $target->add_responsive_control(
                'item_padding', [
            'label' => esc_html__('Padding', 'e-addons'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'rem'],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}:not(.e-add-item_readmore) > *, {{WRAPPER}} {{CURRENT_ITEM}} a.e-add-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );
        $target->add_responsive_control(
                'item_margin', [
            'label' => esc_html__('Margin', 'e-addons'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'rem'],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );
    }

    // ----------------------------------------------------------
    public function controls_items_bordersandshadow_style($target) {

        // ------------ BORDERS & SHADOW (Texts)
        $target->add_control(
                'bordershadow_heading', [
            'type' => Controls_Manager::RAW_HTML,
            'show_label' => false,
            'raw' => '<i class="far fa-square"></i> <b>' . esc_html__('Border and shadow', 'e-addons') . '</b>',
            'content_classes' => 'e-add-inner-heading',
            'separator' => 'before',
                ]
        );
        $target->add_group_control(
                Group_Control_Border::get_type(), [
            'name' => 'item_border',
            'label' => esc_html__('Border', 'e-addons'),
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => '!in',
                        'value' => ['item_image', 'item_imageoricon', 'item_avatar', 'item_readmore', 'item_guid', 'item_author'],
                    ]
                ]
            ],
            'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} > *',
                ]
        );
        $target->add_control(
                'item_border_radius', [
            'label' => esc_html__('Border Radius', 'e-addons'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => '!in',
                        'value' => ['item_image', 'item_imageoricon', 'item_avatar', 'item_readmore', 'item_guid'],
                    ]
                ]
            ],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} > *,{{WRAPPER}} {{CURRENT_ITEM}} > a > img,{{WRAPPER}} {{CURRENT_ITEM}}.e-add-item_label img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ]
                ]
        );
        $target->add_group_control(
                Group_Control_Box_Shadow::get_type(), [
            'name' => 'box_shadow',
            'label' => esc_html__('Box Shadow', 'e-addons'),
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => '!in',
                        'value' => ['item_image', 'item_imageoricon', 'item_avatar', 'item_readmore', 'item_guid', 'item_author'],
                    ]
                ]
            ],
            'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} > *',
                ]
        );

        // ------------ BORDERS & SHADOW (Image - Readmore - Author)
        $target->add_group_control(
                Group_Control_Border::get_type(), [
            'name' => 'item_in_border',
            'label' => esc_html__('Border', 'e-addons'),
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_image', 'item_imageoricon', 'item_avatar', 'item_readmore', 'item_guid', 'item_author'],
                    ]
                ]
            ],
            'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-button, {{WRAPPER}} {{CURRENT_ITEM}} .e-add-img',
                ]
        );
        $target->add_control(
                'item_in_border_radius', [
            'label' => esc_html__('Border Radius', 'e-addons'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_image', 'item_imageoricon', 'item_avatar', 'item_readmore', 'item_guid'],
                    ]
                ]
            ],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-button, {{WRAPPER}} {{CURRENT_ITEM}} .e-add-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ]
                ]
        );
        $target->add_group_control(
                Group_Control_Box_Shadow::get_type(), [
            'name' => 'box_in_shadow',
            'label' => esc_html__('Box Shadow', 'e-addons'),
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_image', 'item_imageoricon', 'item_avatar', 'item_readmore', 'item_guid', 'item_author'],
                    ]
                ]
            ],
            'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-button, {{WRAPPER}} {{CURRENT_ITEM}} .e-add-img',
                ]
        );
    }

    // ----------------------------------------------------------
    public function controls_items_label_style($target) {
        $target->add_control(
                'label_before_style_heading', [
            'type' => Controls_Manager::RAW_HTML,
            'show_label' => false,
            'raw' => '<i class="fas fa-minus"></i> <b>' . esc_html__('Before', 'e-addons') . '</b>',
            'content_classes' => 'e-add-inner-heading',
            'separator' => 'before',
            'condition' => [
                'use_label_before' => 'yes'
            ]
                ]
        );
        $target->add_control(
                'color_label_before', [
            'label' => esc_html__('Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-label-before' => 'color: {{VALUE}};',
            ],
            'condition' => [
                'use_label_before' => 'yes'
            ]
                ]
        );
        $target->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'label-before_typography',
            'label' => esc_html__('Typography', 'e-addons'),
            'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-label-before',
            /*'global' => [
                'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
            ],*/
            'condition' => [
                'use_label_before' => 'yes'
            ]
                ]
        );

        $target->add_control(
                'label_after_style_heading', [
            'type' => Controls_Manager::RAW_HTML,
            'show_label' => false,
            'raw' => '<b>' . esc_html__('After', 'e-addons') . '</b> <i class="fas fa-minus"></i>',
            'content_classes' => 'e-add-inner-heading',
            'separator' => 'before',
            'condition' => [
                'use_label_after!' => ''
            ]
                ]
        );
        $target->add_control(
                'color_label_after', [
            'label' => esc_html__('Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-label-after' => 'color: {{VALUE}};',
            ],
            'condition' => [
                'use_label_after!' => ''
            ]
                ]
        );
        $target->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'label-after_typography',
            'label' => esc_html__('Typography', 'e-addons'),
            /*'global' => [
                'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
            ],*/
            'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-label-after',
            'condition' => [
                'use_label_after!' => ''
            ]
                ]
        );
    }

    // ----------------------------------------------------------
    public function controls_items_icon_style($target) {
        //Icon color-size-space
        $target->add_control(
                'icon_style_heading', [
            'type' => Controls_Manager::RAW_HTML,
            'show_label' => false,
            'raw' => '<i class="fas fa-star"></i> <b>' . esc_html__('Icon', 'e-addons') . '</b>',
            'content_classes' => 'e-add-inner-heading',
            'separator' => 'before',
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => [
                            'item_termstaxonomy',
                            'item_date',
                            'item_registered',
                            'item_custommeta'
                        ],
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'icon_enable',
                                'value' => 'yes',
                            ]
                        ]
                    ],
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
                                'value' => ['file', 'button', 'text']
                            ],
                            [
                                'name' => 'show_icon[value]',
                                'operator' => '!=',
                                'value' => '',
                            ]
                        ]
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'value' => 'item_label',
                            ],
                            [
                                'name' => 'label_html_type',
                                'value' => 'icon',
                            ],
                            [
                                'name' => 'label_html_icon[value]',
                                'operator' => '!=',
                                'value' => '',
                            ]
                        ]
                    ]
                ]
            ]
                ]
        );
        $target->add_control(
                'icon_style', [
            'label' => esc_html__('Icon', 'e-addons'),
            'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
            'return_value' => 'yes',
            'render_type' => 'ui',
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => [
                            'item_termstaxonomy',
                            'item_date',
                            'item_registered',
                            'item_custommeta'
                        ],
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'icon_enable',
                                'value' => 'yes',
                            ]
                        ]
                    ],
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
                                'value' => ['file', 'button', 'text']
                            ],
                            [
                                'name' => 'show_icon[value]',
                                'operator' => '!=',
                                'value' => '',
                            ]
                        ]
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'value' => 'item_label',
                            ],
                            [
                                'name' => 'label_html_type',
                                'value' => 'icon',
                            ],
                            [
                                'name' => 'label_html_icon[value]',
                                'operator' => '!=',
                                'value' => '',
                            ]
                        ]
                    ]
                ]
            ]
                ]
        );
        $target->start_popover();

        $target->add_control(
                'color_item_icon', [
            'label' => esc_html__('Icon Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-icon, {{WRAPPER}} {{CURRENT_ITEM}} .e-add-query-icon' => 'color: {{VALUE}};',
                '{{WRAPPER}} {{CURRENT_ITEM}} svg.e-add-icon, {{WRAPPER}} {{CURRENT_ITEM}} svg.e-add-query-icon' => 'fill: {{VALUE}};',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'icon_style',
                        'value' => 'yes',
                    ]
                ]
            ]
                ]
        );
        $target->add_responsive_control(
                'icon_size', [
            'label' => esc_html__('Icon size', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => 'px',
            ],
            'range' => [
                'px' => [
                    'min' => 10,
                    'max' => 80,
                    'step' => 1
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-icon, {{WRAPPER}} {{CURRENT_ITEM}} .e-add-query-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} {{CURRENT_ITEM}} svg.e-add-icon, {{WRAPPER}} {{CURRENT_ITEM}} svg.e-add-query-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'icon_style',
                        'value' => 'yes',
                    ]
                ]
            ]
                ]
        );
        $target->add_responsive_control(
                'icon_space', [
            'label' => esc_html__('Icon space', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => 'px',
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 50,
                    'step' => 1
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-icon, {{WRAPPER}} {{CURRENT_ITEM}} .e-add-query-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'icon_style',
                        'value' => 'yes',
                    ]
                ]
            ]
                ]
        );
        $target->end_popover();
    }

}
