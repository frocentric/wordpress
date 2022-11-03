<?php

namespace EAddonsForElementor\Modules\Query\Base\Traits;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;

/**
 * Description of label
 *
 * @author fra
 */
trait Common {

    // -------------- Label Html ---------
    public function controls_items_common_content($target) {
        $target->add_control(
                'item_text_label', [
            'label' => esc_html__('Label', 'e-addons'),
            'type' => Controls_Manager::TEXT,
                ]
        );
        
    }
    public function controls_items_grid_debug($target) {
        $target->add_control(
            'items_grid_debug', [
                'label' => '<span style="color: #fff; background-color: #93003c; padding: 5px 10px; border-radius: 20px;">' . esc_html__('Show grid for DEBUG', 'e-addons') . '</span>',
                'type' => Controls_Manager::SWITCHER,
                'prefix_class' => 'e-add-grid-debug-',
                'separator' => 'before',
                'condition' => [
                    '_skin!' => ['table'],
                ],
            ]
        );
        
    }
    
    // ---------------------------------------------------------------
    public function register_style_controls() {
        //
        // Blocks - Style
        $this->start_controls_section(
                'section_blocks_style',
                [
                    'label' => esc_html__('Block Style', 'e-addons'),
                    'tab' => Controls_Manager::TAB_STYLE,
                    'condition' => [
                        'style_items!' => 'template',
                        '_skin!' => ['table', 'list', 'mosaic', 'export'],
                    ]
                ]
        );
        $this->add_responsive_control(
                'blocks_align', [
            'label' => esc_html__('Text Alignment', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'toggle' => true,
            'options' => [
                'left' => [
                    'title' => esc_html__('Left', 'e-addons'),
                    'icon' => 'fa fa-align-left',
                ],
                'center' => [
                    'title' => esc_html__('Center', 'e-addons'),
                    'icon' => 'fa fa-align-center',
                ],
                'right' => [
                    'title' => esc_html__('Right', 'e-addons'),
                    'icon' => 'fa fa-align-right',
                ]
            ],
            'default' => 'left',
            'prefix_class' => 'e-add-align%s-',
            'selectors' => [
                '{{WRAPPER}} .e-add-post-item' => 'text-align: {{VALUE}};',
            ],
            'separator' => 'before',
                ]
        );
        $this->add_control(
                'heading_blocks_align_flex',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="fas fa-arrows-alt" aria-hidden="true"></i> ' . esc_html__('Flex Alignment', 'e-addons'),
                    'separator' => 'before',
                    'content_classes' => 'e-add-inner-heading',
                ]
        );
        // xxxxxx
        /*
          $this->add_responsive_control(
          'blocks_align_flex', [
          'label' => esc_html__('Horizontal Flex align', 'e-addons'),
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
          // 'condition' => [
          //     'style_items!' => 'template',
          //     '_skin' => 'grid',
          // ],
          'conditions' => [
          'relation' => 'and',
          'terms' => [
          [
          'name' => 'style_items',
          'operator' => '!=',
          'value' => 'template',
          ],
          [
          'name' => '_skin',
          'operator' => 'in',
          'value' => ['grid','carousel','dualslider'],
          ]
          ]
          ],
          'default' => 'center',
          'selectors' => [
          '{{WRAPPER}} .e-add-post-block, {{WRAPPER}} .e-add-item-area' => 'justify-content: {{VALUE}};',
          ],

          ]
          ); */

        $this->add_responsive_control(
                'blocks_align_flex', [
            'label' => esc_html__('Horizontal Flex align', 'e-addons'), //__('Flex Items Align', 'e-addons'),
            'type' => 'ui_selector',
            'toggle' => true,
            'type_selector' => 'image',
            'label_block' => false,
            'columns_grid' => 3,
            'options' => [
                'flex-start' => [
                    'title' => esc_html__('Left', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/grid_alignments/block_left.svg',
                ],
                'center' => [
                    'title' => esc_html__('Middle', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/grid_alignments/block_middle.svg',
                ],
                'flex-end' => [
                    'title' => esc_html__('Right', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/grid_alignments/block_right.svg',
                ],
            /* 'space-between' => [
              'title' => esc_html__('Space Between', 'e-addons'),
              'return_val' => 'val',
              'image' => E_ADDONS_URL . 'modules/query/assets/img/grid_alignments/block_space-between.svg',
              ],
              'space-around' => [
              'title' => esc_html__('Space Around', 'e-addons'),
              'return_val' => 'val',
              'image' => E_ADDONS_URL . 'modules/query/assets/img/grid_alignments/block_space-around.svg',
              ], */
            ],
            'default' => 'center',
            'selectors' => [
                '{{WRAPPER}} .e-add-post-block, {{WRAPPER}} .e-add-item-area' => 'justify-content: {{VALUE}};',
                //'{{WRAPPER}} .e-add-wrapper-grid' => 'justify-content: {{VALUE}};',
            ],
            /* 'condition' => [
              'style_items!' => 'template',
              '_skin' => 'grid',
              ] */
            'conditions' => [
                'relation' => 'and',
                'terms' => [
                    [
                        'name' => 'style_items',
                        'operator' => '!=',
                        'value' => 'template',
                    ],
                    [
                        'name' => '_skin',
                        'operator' => 'in',
                        'value' => ['grid', 'carousel', 'dualslider'],
                    ]
                ]
            ],
                ]
        );

        $this->add_responsive_control(
                'blocks_align_justify', [
            'label' => esc_html__('Vertical Flex align', 'e-addons'), //__('Flex Justify Content', 'e-addons'),
            'type' => 'ui_selector',
            'toggle' => true,
            'label_block' => true,
            'type_selector' => 'image',
            'columns_grid' => 5,
            'options' => [
                'flex-start' => [
                    'title' => esc_html__('Top', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/grid_alignments/flex_top.svg',
                ],
                'center' => [
                    'title' => esc_html__('Center', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/grid_alignments/flex_middle.svg',
                ],
                'flex-end' => [
                    'title' => esc_html__('Bottom', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/grid_alignments/flex_bottom.svg',
                ],
                'space-between' => [
                    'title' => esc_html__('Space Betweens', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/grid_alignments/flex_space-between.svg',
                ],
                'space-around' => [
                    'title' => esc_html__('Space Around', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/grid_alignments/flex_space-around.svg',
                ]
            ],
            'default' => 'flex-start',
            'selectors' => [
                '{{WRAPPER}} .e-add-post-block, {{WRAPPER}} .e-add-item-area' => 'align-content: {{VALUE}}; align-items: {{VALUE}};',
            ],
                ]
        );

        $this->add_control(
                'blocks_bgcolor', [
            'label' => esc_html__('Background Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .e-add-post-item .e-add-post-block' => 'background-color: {{VALUE}};'
            ],
                ]
        );
        $this->add_group_control(
                Group_Control_Border::get_type(), [
            'name' => 'blocks_border',
            'selector' => '{{WRAPPER}} .e-add-post-item .e-add-post-block',
                ]
        );
        $this->add_responsive_control(
                'blocks_padding', [
            'label' => esc_html__('Padding', 'e-addons'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .e-add-post-item .e-add-post-block' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );
        $this->add_control(
                'blocks_border_radius', [
            'label' => esc_html__('Border Radius', 'e-addons'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .e-add-post-item .e-add-post-block' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
            ],
                ]
        );
        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(), [
            'name' => 'blocks_boxshadow',
            'selector' => '{{WRAPPER}} .e-add-post-item .e-add-post-block',
                ]
        );
        // Vertical Alternate
        /*
          $this->add_control(
          'dis_alternate',
          [
          'type' => Controls_Manager::RAW_HTML,
          'show_label' => false,
          'separator' => 'before',
          'raw' => '<img src="' . E_ADDONS_QUERY_URL . 'assets/img/skins/alternate.png' . '" />',
          'content_classes' => 'e-add-skin-dis',
          'condition' => [
          $this->get_control_id('grid_type') => ['flex']
          ],
          ]
          );

          $this->add_responsive_control(
          'blocks_alternate', [
          'label' => esc_html__('Vertical Alternate', 'e-addons'),
          'type' => Controls_Manager::SLIDER,
          'size_units' => ['px'],
          'range' => [
          'px' => [
          'max' => 100,
          'min' => 0,
          'step' => 1,
          ],
          ],
          'selectors' => [
          '{{WRAPPER}}.e-add-col-3 .e-add-post-item:nth-child(3n+2) .e-add-post-block, {{WRAPPER}}:not(.e-add-col-3) .e-add-post-item:nth-child(even) .e-add-post-block' => 'margin-top: {{SIZE}}{{UNIT}};',
          ],
          'condition' => [
          $this->get_control_id('grid_type') => ['flex']
          ],
          ]
          ); */
        $this->end_controls_section();

        //
    }
    
    
    public function add_controls_block_style_items() {
        // +********************* Style: Left, Right, Alternate
            $this->add_responsive_control(
                    'image_rate', [
                'label' => esc_html__('Distribution (%)', 'e-addons'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '50',
                    'unit' => '%',
                ],
                'size_units' => ['%'],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .e-add-image-area' => 'width: {{SIZE}}%;',
                    '{{WRAPPER}} .e-add-content-area' => 'width: calc( 100% - {{SIZE}}% );',
                ],
                'condition' => [
                    '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                    'style_items' => ['left', 'right', 'alternate'],
                ],
                    ]
            );

            // +********************* Float Hover style descripton:
            $this->add_control(
                    'float_hoverstyle_description',
                    [
                        'type' => Controls_Manager::RAW_HTML,
                        'show_label' => false,
                        'raw' => '<i class="eaddicon eicon-image-rollover" aria-hidden="true"></i> ' . esc_html__('Float style allows you to create animations between the content and the underlying image, from "Hover effect" Panel you can set the features.', 'e-addons'),
                        'content_classes' => 'e-add-info-panel',
                        'condition' => [
                            '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                            'style_items' => ['float'],
                        ],
                    ]
            );
            // +********************* Image Zone Style:
            $this->add_control(
                    'heading_imagezone',
                    [
                        'type' => Controls_Manager::RAW_HTML,
                        'show_label' => false,
                        'raw' => '<i class="far fa-image"></i> &nbsp;' . esc_html__('IMAGE:', 'e-addons'),
                        'content_classes' => 'e-add-icon-heading',
                        'condition' => [
                            '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll'],
                            'style_items!' => ['default', 'template', 'html'],
                        ],
                    ]
            );

            /*

              // +********************* Image Zone: Mask
              $this->add_control(
              'imagemask_popover', [
              'label' => esc_html__('Mask', 'e-addons'),
              'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
              'label_off' => esc_html__('Default', 'e-addons'),
              'label_on' => esc_html__('Custom', 'e-addons'),
              'return_value' => 'yes',
              'condition' => [
              '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion','softscroll','expander'],
              'style_items!' => ['default', 'template'],
              ],
              ]
              );
              $this->start_popover();
              $this->add_control(
              'mask_heading',
              [
              'label' => esc_html__('Mask', 'e-addons'),
              'description' => esc_html__('Shape Parameters', 'e-addons'),
              'type' => Controls_Manager::HEADING,
              'separator' => 'before',
              'condition' => [
              '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion','softscroll','expander'],
              'style_items!' => ['default', 'template'],
              'imagemask_popover' => 'yes',
              ],
              ]
              );
              $this->add_group_control(
              Masking::get_type(),
              [
              'name' => 'mask',
              'label' => esc_html__('Mask', 'e-addons'),
              'selector' => '{{WRAPPER}} .e-add-posts-container .e-add-post-image',
              'condition' => [
              '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion','softscroll','expander'],
              'style_items!' => ['default', 'template'],
              'imagemask_popover' => 'yes',
              ],
              ]
              );
              $this->end_popover();
              // +********************* Image Zone: Transforms
              $this->add_control(
              'imagetransforms_popover',
              [
              'label' => esc_html__('Transforms', 'plugin-name'),
              'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
              'return_value' => 'yes',
              'render_type' => 'ui',
              'condition' => [
              '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion','softscroll','expander'],
              'style_items!' => ['default', 'template'],
              ],
              ]
              );
              $this->start_popover();

              $this->add_group_control(
              Transform::get_type(),
              [
              'name' => 'transform_image',
              'label' => 'Transform image',
              'selector' => '{{WRAPPER}} .e-add-post-item .e-add-image-area',
              'separator' => 'before',
              'condition' => [
              '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion','softscroll','expander'],
              'style_items!' => ['default', 'template'],
              'imagetransforms_popover' => 'yes',
              ],
              ]
              );
              $this->end_popover();

             */

            // +********************* Image Zone: Filters
            $this->add_group_control(
                    Group_Control_Css_Filter::get_type(),
                    [
                        'name' => 'imagezone_filters',
                        'label' => 'Filters',
                        'render_type' => 'ui',
                        'selector' => '{{WRAPPER}} .e-add-post-block .e-add-post-image img',
                        'condition' => [
                            '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                            'style_items!' => ['default', 'template', 'html'],
                        ],
                    ]
            );
            // +********************* Content Zone Style:
            $this->add_control(
                    'heading_contentzone',
                    [
                        'type' => Controls_Manager::RAW_HTML,
                        'show_label' => false,
                        'raw' => '<i class="fas fa-align-left"></i> &nbsp;' . esc_html__('CONTENT:', 'e-addons'),
                        'content_classes' => 'e-add-icon-heading',
                        'condition' => [
                            '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                            'style_items!' => ['default', 'template', 'html'],
                        ],
                    ]
            );
            // +********************* Content Zone: Style
            $this->add_control(
                    'contentstyle_popover', [
                'label' => esc_html__('Style', 'e-addons'),
                'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
                'label_off' => esc_html__('Default', 'e-addons'),
                'label_on' => esc_html__('Custom', 'e-addons'),
                'return_value' => 'yes',
                'render_type' => 'ui',
                'condition' => [
                    '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                    'style_items!' => ['default', 'template', 'html'],
                ],
                    ]
            );
            $this->start_popover();
            $this->add_control(
                    'contentzone_bgcolor', [
                'label' => esc_html__('Background Color', 'e-addons'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .e-add-post-item .e-add-content-area' => 'background-color: {{VALUE}};'
                ],
                'condition' => [
                    '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                    'style_items!' => ['default', 'template', 'html'],
                    'contentstyle_popover' => 'yes',
                ],
                    ]
            );
            $this->add_group_control(
                    Group_Control_Border::get_type(), [
                'name' => 'contentzone_border',
                'selector' => '{{WRAPPER}} .e-add-post-item .e-add-content-area',
                'condition' => [
                    '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                    'style_items!' => ['default', 'template', 'html'],
                    'contentstyle_popover' => 'yes',
                ],
                    ]
            );
            $this->add_responsive_control(
                    'contentzone_padding', [
                'label' => esc_html__('Padding', 'e-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .e-add-post-item .e-add-content-area' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                    'style_items!' => ['default', 'template', 'html'],
                    'contentstyle_popover' => 'yes',
                ],
                    ]
            );
            $this->add_control(
                    'contentzone_border_radius', [
                'label' => esc_html__('Border Radius', 'e-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                //'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .e-add-post-item .e-add-content-area' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                    'style_items!' => ['default', 'template', 'html'],
                    'contentstyle_popover' => 'yes',
                ],
                    ]
            );

            $this->end_popover();

            // +********************* Content Zone Transform: Overlay, TextZone, Float
            $this->add_control(
                    'contenttransform_popover', [
                'label' => esc_html__('Transform', 'e-addons'),
                'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
                'label_off' => esc_html__('Default', 'e-addons'),
                'label_on' => esc_html__('Custom', 'e-addons'),
                'return_value' => 'yes',
                'render_type' => 'ui',
                'condition' => [
                    '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                    'style_items' => ['overlay', 'textzone', 'float'],
                ],
                    ]
            );
            $this->start_popover();
            $this->add_responsive_control(
                    'contentzone_x', [
                'label' => esc_html__('X', 'e-addons'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'default' => [
                    'size' => '',
                    'unit' => '%',
                ],
                'range' => [
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                        'step' => 0.1
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .e-add-content-area' => 'margin-left: {{SIZE}}%;',
                ],
                'condition' => [
                    'contenttransform_popover' => 'yes',
                    '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                    'style_items' => ['overlay', 'textzone', 'float'],
                ],
                    ]
            );
            $this->add_responsive_control(
                    'contentzone_y', [
                'label' => esc_html__('Y', 'e-addons'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '',
                    'unit' => '%',
                ],
                'size_units' => ['%'],
                'range' => [
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                        'step' => 0.1
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .e-add-content-area' => 'margin-top: {{SIZE}}%;',
                ],
                'condition' => [
                    'contenttransform_popover' => 'yes',
                    '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                    'style_items' => ['overlay', 'textzone', 'float'],
                ],
                    ]
            );
            $this->add_responsive_control(
                    'contentzone_width', [
                'label' => esc_html__('Width (%)', 'e-addons'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '',
                    'unit' => '%',
                ],
                'size_units' => ['%'],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                        'step' => 0.1
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .e-add-content-area' => 'width: {{SIZE}}%;',
                ],
                'condition' => [
                    'contenttransform_popover' => 'yes',
                    '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                    'style_items' => ['overlay', 'textzone', 'float'],
                ],
                    ]
            );
            $this->add_responsive_control(
                    'contentzone_height', [
                'label' => esc_html__('Height (%)', 'e-addons'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '',
                    'unit' => '%',
                ],
                'size_units' => ['%'],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                        'step' => 0.1
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .e-add-content-area' => 'height: {{SIZE}}%;',
                ],
                'condition' => [
                    'contenttransform_popover' => 'yes',
                    '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                    'style_items' => ['float'],
                ],
                    ]
            );
            $this->end_popover();
            // +********************* Content Zone: BoxShadow
            $this->add_group_control(
                    Group_Control_Box_Shadow::get_type(), [
                'name' => 'contentzone_box_shadow',
                'selector' => '{{WRAPPER}} .e-add-post-item .e-add-content-area',
                'condition' => [
                    '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                    'style_items!' => ['default', 'template', 'html'],
                ],
                'popover' => true
                    ]
            );
            // +********************* Content Zone: Float interaction
            $this->add_control(
                    'float_interaction', [
                'label' => '<i class="eaddicon fas fa-ban" aria-hidden="true"></i> ' . esc_html__('Stop interaction on content', 'e-addons'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before',
                'prefix_class' => 'disable-float-interaction-',
                'description' => esc_html__('This option allows you to stop the interactions on the content to give priority to the image lying behind.', 'e-addons'),
                'condition' => [
                    '_skin' => ['', 'grid', 'filters', 'carousel', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                    'style_items' => 'float',
                ],
                    ]
            );

            /* Responsive --------------- */
            $this->add_control(
                    'force_layout_default', [
                'label' => '<i class="eaddicon eicon-device-mobile" aria-hidden="true"></i> ' . esc_html__('Force default layout on mobile', 'e-addons'),
                'type' => Controls_Manager::SWITCHER,
                'separator' => 'before',
                'prefix_class' => 'force-default-mobile-',
                'condition' => [
                    '_skin' => ['', 'grid', 'filters', 'carousel', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                    'style_items' => ['left', 'right', 'alternate']
                ],
                    ]
            );
            
    }

}
