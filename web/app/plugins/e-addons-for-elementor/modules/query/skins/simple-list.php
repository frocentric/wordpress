<?php

namespace EAddonsForElementor\Modules\Query\Skins;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use EAddonsForElementor\Modules\Query\Skins\Base;

use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Core\Utils\Query as Query_Utils;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Simple List Skin
 *
 * Elementor widget query-posts for e-addons
 *
 */
class Simple_List extends Base {

    public function get_id() {
        return 'list';
    }
    
    public function get_pid() {
        return 13051;
    }

    public function get_title() {
        return esc_html__('List', 'e-addons');
    }

    public function get_icon() {
        return 'eadd-skin-simple-list';
    }
    public function get_style_depends() {
        return ['e-addons-common-query','e-addons-query-list'];
    }

    public function register_additional_controls() {
        //var_dump($this->get_id());
        //var_dump($this->parent->get_settings('_skin')); //->get_current_skin()->get_id();

        
        $this->start_controls_section(
            'section_list', [
                'label' => '<i class="eaddicon eadd-skin-list"></i> ' . esc_html__('List', 'e-addons'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'direction',
            [
                'label' => esc_html__('Direction', 'e-addons'),
                'type' => Controls_Manager::CHOOSE,
                'toggle' => false,
                'options' => [
                    'vertical' => [
                        'title' => esc_html__('Vertical', 'e-addons'),
                        'icon' => 'fas fa-ellipsis-v',
                    ],
                    'horizontal' => [
                        'title' => esc_html__('Horizontal', 'e-addons'),
                        'icon' => 'fas fa-ellipsis-h',
                    ]
                ],
                'prefix_class' => 'e-add-list-',
                'default' => 'vertical',
            //     'selectors' => [                       
            //         '{{WRAPPER}} .e-add-simplelist-container li' => 'float: {{VALUE}}',
            //     ],   
            ]
        );
        // --------------------------------------------
        $this->add_responsive_control(
            'columns_list', [
                'label' => esc_html__('Columns', 'e-addons'),
                'type' => Controls_Manager::NUMBER,
                //'default' => '1',
                'min' => 1,
                'max' => 6,
                'step' => 1,
                'selectors' => [
                    '{{WRAPPER}} .e-add-simplelist-container' => 'column-count: {{VALUE}}', 
                    '{{WRAPPER}} .e-add-simplelist-container > li' => '-webkit-column-break-inside: avoid; page-break-inside: avoid; break-inside: avoid;', 
                ],
                'condition' => [
                    $this->get_control_id('direction') => 'vertical',
                ]
            ]
        );
        //space
        $this->add_responsive_control(
			'list_space', [
				'label' => esc_html__('Space', 'e-addons'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'default' => [
					'size' => '',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 80,
						'step' => 1
					],
				],
				'selectors' => [
					'{{WRAPPER}}.e-add-list-horizontal .e-add-simplelist-container > *' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.e-add-list-vertical .e-add-simplelist-container > *:not(last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				//'frontend_available' => true,
			]
		);
        $this->add_responsive_control(
			'list_column_gap', [
				'label' => esc_html__('Column Gap', 'e-addons'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'default' => [
					'size' => '',
				],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 80,
						'step' => 1
					],
				],
				'selectors' => [
                    '{{WRAPPER}} .e-add-simplelist-container' => 'column-gap: {{SIZE}}{{UNIT}}',        
                ],
                'condition' => [
                    $this->get_control_id('direction') => 'vertical',
                    $this->get_control_id('columns_list!') => 1,
                ]
				//'frontend_available' => true,
			]
		);
        $this->add_responsive_control(
			'list_row_gap', [
				'label' => esc_html__('Row Gap', 'e-addons'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'default' => [
					'size' => '',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 80,
						'step' => 1
					],
				],
				'selectors' => [
                    '{{WRAPPER}}.e-add-list-horizontal .e-add-simplelist-container > *' => 'margin-bottom: {{SIZE}}{{UNIT}};',        
                ],
                'condition' => [
                    $this->get_control_id('direction') => 'horizontal',
                ]
				//'frontend_available' => true,
			]
		);
        
        //select: none, default, icon, image
        // DEPRECATO..
        $this->add_responsive_control(
            'list_type', [
                'label' => '<i class="fas fa-circle"></i>&nbsp;' . esc_html__('List type', 'e-addons'),
                'type' => Controls_Manager::HIDDEN, //SELECT,
                'default' => '',
                'options' => [
                    '' => 'Default',
                    'icon' => 'Icon',
                    'image' => 'Image'
                ]
            ]
        );
        
        //style
        $this->add_control(
            'list_style', [
                'label' => esc_html__('List Style', 'e-addons'),
                'type' => 'ui_selector',
                'toggle' => false,
                'label_block' => false,
                'type_selector' => 'image',
                'columns_grid' => 3,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left','e-addons'),
                        'return_val' => 'val',
                        'image' => E_ADDONS_URL.'modules/query/assets/img/list/list_1.svg',
                    ],
                    'top' => [
                        'title' => esc_html__('Top','e-addons'),
                        'return_val' => 'val',
                        'image' => E_ADDONS_URL.'modules/query/assets/img/list/list_2.svg',
                    ],
                ],
                'default' => 'left',
                /*'selectors' => [
                    '{{WRAPPER}} .e-add-posts-container.e-add-skin-grid > .e-add-wrapper-grid' => 'align-items: {{VALUE}};',
                ],*/
                'condition' => [
                    $this->get_control_id('list_type') => ['image','icon'],
                ],
            ]
        );
        $this->add_control(
            'type',
            [
                'label' => esc_html__('Ordered or Unordered', 'e-addons'),
                'type' => Controls_Manager::CHOOSE,
                'toggle' => false,
                'options' => [
                    'ul' => [
                        'title' => esc_html__('Unordered', 'e-addons'),
                        'icon' => 'fas fa-list-ul',
                    ],
                    'ol' => [
                        'title' => esc_html__('Ordered', 'e-addons'),
                        'icon' => 'fas fa-list-ol',
                    ],
                    'image' => [
                        'title' => esc_html__('Image', 'e-addons'),
                        'icon' => 'fas fa-image',
                    ],
                    'icon' => [
                        'title' => esc_html__('Icon', 'e-addons'),
                        'icon' => 'fas fa-icons',
                    ],

                    // '' => [
                    //     'title' => esc_html__('None', 'e-addons'),
                    //     'icon' => 'fas fa-ban',
                    // ],
                ],
                'default' => 'ul',
                'prefix_class' => 'e-add-list-type-',
                'render_type' => 'template',
                'condition' => [
                    $this->get_control_id('list_type') => '',
                ],
            ]
        );
        
        //default --------------------
        /*
        $this->add_control(
        'style_type_poosition',
            [
                'label' => esc_html__('Position', 'e-addons'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'inside' => esc_html__('Inside', 'e-addons'),
                    'outside' => esc_html__('Outside', 'e-addons'),
                ],
                'selectors' => [                       
                    '{{WRAPPER}} li' => 'list-style-position: {{VALUE}}',
                ],                    
                'condition' => [
                    $this->get_control_id('list_type') => '',
                ],
            ]
        );
        */
        $this->add_control(
            'style_type_ul',
            [
                'label' => esc_html__('Style Type', 'e-addons'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => esc_html__('Default', 'e-addons'),
                    'none' => esc_html__('None', 'e-addons'),
                    'circle' => esc_html__('Circle', 'e-addons'),
                    'disc' => esc_html__('Disc', 'e-addons'),
                    'square' => esc_html__('Square', 'e-addons'),
                ],
                'selectors' => [                       
                    '{{WRAPPER}} li' => 'list-style: {{VALUE}}',
                ],                    
                'condition' => [
                    $this->get_control_id('list_type') => '',
                    $this->get_control_id('type') => 'ul',
                    //'list_type' => 'ul',
                ],
            ]
        );
        $this->add_control(
            'style_type_ol',
            [
                'label' => esc_html__('Style Type', 'e-addons'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => esc_html__('Default', 'e-addons'),
                    'none' => esc_html__('None', 'e-addons'),
                    'armenian' => esc_html__('Armenian', 'e-addons'),
                    'cjk-ideographic' => esc_html__('Cjk-ideographic', 'e-addons'),
                    'decimal' => esc_html__('decimal', 'e-addons'),
                    'decimal-leading-zero' => esc_html__('decimal-leading-zero', 'e-addons'),
                    'georgian' => esc_html__('georgian', 'e-addons'),
                    'decimal' => esc_html__('decimal', 'e-addons'),
                    'hebrew' => esc_html__('hebrew', 'e-addons'),
                    'hiragana' => esc_html__('hiragana', 'e-addons'),
                    'hiragana-iroha' => esc_html__('hiragana-iroha', 'e-addons'),
                    'katakana' => esc_html__('katakana', 'e-addons'),
                    'katakana-iroha' => esc_html__('katakana-iroha', 'e-addons'),
                    'lower-alpha' => esc_html__('lower-alpha', 'e-addons'),
                    'lower-greek' => esc_html__('lower-greek', 'e-addons'),
                    'lower-latin' => esc_html__('lower-latin', 'e-addons'),
                    'lower-roman' => esc_html__('lower-roman', 'e-addons'),
                    'upper-alpha' => esc_html__('upper-alpha', 'e-addons'),
                    'upper-greek' => esc_html__('upper-greek', 'e-addons'),
                    'upper-latin' => esc_html__('upper-latin', 'e-addons'),
                    'upper-roman' => esc_html__('upper-roman', 'e-addons'),
                ],
                'selectors' => [                       
                    '{{WRAPPER}} li' => 'list-style: {{VALUE}}',
                ],
                'condition' => [
                    $this->get_control_id('list_type') => '',
                    $this->get_control_id('type') => 'ol',
                ],
            ]
        );
        $this->add_control(
			'list_icon',
			[
				'label' => esc_html__( 'Icon', 'elementor' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-plus',
					'library' => 'fa-solid',
				],
				'recommended' => [
					'fa-solid' => [
                        'plus',
                        'square',
                        'plus-square',
                        'circle',
						'chevron-right',
						'angle-right',
						'angle-double-right',
						'caret-right',
						'caret-square-right',
                        'chevron-left',
						'angle-left',
						'angle-double-left',
						'caret-left',
						'caret-square-left',
					],
					'fa-regular' => [
						'chevron-right',
					],
				],
				'skin' => 'inline',
				'label_block' => false,
                'condition' => [
                    $this->get_control_id('list_type') => '',
                    $this->get_control_id('type') => 'icon',
                ],
			]
		);
        // position
        /*$this->add_control(
			'list_icon_align',
			[
				'label' => esc_html__( 'Alignment', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Start', 'elementor' ),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__( 'End', 'elementor' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'default' => is_rtl() ? 'right' : 'left',
				'toggle' => false,
                'condition' => [
                    $this->get_control_id('list_type') => '',
                    $this->get_control_id('type') => 'icon',
                ],
			]
		);*/
        // size
        $this->add_responsive_control(
			'list_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
                'condition' => [
                    $this->get_control_id('list_type') => '',
                    $this->get_control_id('type') => 'icon',
                ],
				'selectors' => [
					'{{WRAPPER}}.e-add-list-type-icon li .e-add-icon-list i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.e-add-list-type-icon li .e-add-icon-list svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};'
				],
			]
		);
        // space
        $this->add_responsive_control(
			'list_icon_space',
			[
				'label' => esc_html__( 'Icon Spacing', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
                'condition' => [
                    $this->get_control_id('list_type') => '',
                    $this->get_control_id('type') => 'icon',
                ],
				'selectors' => [
					'{{WRAPPER}}.e-add-list-type-icon li .e-add-icon-list' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);
        $this->add_control(
			'style_type_image', [
				'label' => '<i class="fas fa-file-image"></i> ' . esc_html__('Image', 'e-addons'),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'condition' => [
                    $this->get_control_id('list_type') => '',
                    $this->get_control_id('type') => 'image',
                ],
                'selectors' => [                       
                    '{{WRAPPER}}  .e-add-simplelist-container li' => 'list-style: none; background-image: url({{URL}}); background-repeat: no-repeat; background-position: left top; background-size: 30px; padding-left: 40px;'
                ],
			]
		);




        //icon --------------------
        

        //image --------------------
        /*
        $type = $this->parent->get_querytype();
    

        //@p se mi trovo in post scelgo tra Featured o Custom image 
        //@p se mi trovo in user scelgo tra Avatar o Custom image
        //@p se mi trovo in repeater scego il subField repeater
        if ($type == 'post' || $type == 'user') {
            //@p questa è solo l'etichetta string
            if ($type == 'post') {
                $defIm = 'featured';
            } else if ($type == 'user') {
                $defIm = 'avatar';
            }
            $this->add_control(
                'image_content_heading', [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="fas fa-image"></i> <b>' . esc_html__('Image', 'e-addons') . '</b>',
                    'content_classes' => 'e-add-inner-heading',
                    'separator' => 'before',
                    'condition' => [
                        $this->get_control_id('list_type') => 'image',
                    ]
                ]
            );
            $this->add_control(
                'image_type', [
                    'label' => esc_html__('Image type', 'e-addons'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'featuredimage' => esc_html__(ucfirst($defIm . ' image'), 'e-addons'),
                        'customimage' => esc_html__('Custom meta image', 'e-addons'),
                        'mediaimage' => esc_html__('Custom meta image', 'e-addons'),
                    ],
                    'default' => $defIm . 'image',
                    'condition' => [
                        $this->get_control_id('list_type') => 'image',
                    ]
                ]
            );

            $this->add_control(
                'image_custom_metafield', [
                    'label' => esc_html__('Image Meta Field', 'e-addons'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('Meta key', 'e-addons'),
                    'label_block' => true,
                    'query_type' => 'metas',
                    'object_type' => $type,
                    'separator' => 'after',
                    'condition' => [
                        $this->get_control_id('list_type') => 'image',
                        $this->get_control_id('image_type') => 'customimage',
                    ]
                ]
            );
            
        } else if ($type == 'term') {
            //@p altrimeti in termine è solo la custom
            $this->add_control(
                'image_custom_metafield', [
                    'label' => esc_html__('Image Meta Field', 'e-addons'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('Meta key', 'e-addons'),
                    'label_block' => true,
                    'query_type' => 'metas',
                    'object_type' => $type,
                    'separator' => 'after',
                    'condition' => [
                        $this->get_control_id('list_type') => 'image',
                        $this->get_control_id('image_type') => 'customimage',
                    ]
                ]
            );
        }
        $this->add_control(
			'list_image_custom', [
				'label' => '<i class="fas fa-file-image"></i> ' . esc_html__('Image', 'e-addons'),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'frontend_available' => true,
				'condition' => [
					$this->get_control_id('image_type') => 'mediaimage',
				]
			]
		);
        $this->add_group_control(
            Group_Control_Image_Size::get_type(), [
                'name' => 'list_thumbnail_size',
                'label' => esc_html__('Image Format', 'e-addons'),
                'default' => 'large',
                'condition' => [
                    $this->get_control_id('list_type') => 'image',
                ]
            ]
        );
        if ($type == 'user') {
            $this->add_control(
                'list_avatar_size', [
                    'label' => esc_html__('Avatar size', 'e-addons'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 200,
                    'condition' => [
                        $this->get_control_id('list_type') => 'image',
                    ]
                ]
            );
        }*/
        $this->end_controls_section();
    }

    public function register_style_controls() {
        parent::register_style_controls();

        $this->start_controls_section(
            'section_style_list',
            [
                'label' => '<i class="eaddicon eadd-skin-list"></i> ' . esc_html__('List', 'e-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_responsive_control(
            'list_align',
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
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .e-add-simplelist-container' => 'text-align: {{VALUE}};',
                ]
            ]
        );
        //dot size
        $this->add_responsive_control(
			'list_dot_size', [
				'label' => esc_html__('Dot Size', 'e-addons'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'default' => [
					'size' => '',
				],
				'range' => [
					'px' => [
						'min' => 2,
						'max' => 80,
						'step' => 1
					],
				],
				'selectors' => [
					'{{WRAPPER}} .e-add-simplelist-container li::marker' => 'font-size: {{SIZE}}{{UNIT}};',
				],
                'condition' => [
                    $this->get_control_id('type!') => ['','image'],
                ],
				//'frontend_available' => true,
			]
		);
        $this->add_control(
            'list_wrapper', [
                'label' => esc_html__('List Wrapper', 'e-addons'),
                'type' => Controls_Manager::HEADING,
            ]
        );
        $this->add_responsive_control(
                'oul_padding', [
                    'label' => esc_html__('Padding', 'e-addons'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', '%', 'em'],
                    'selectors' => [
                        '{{WRAPPER}} ul, {{WRAPPER}} ol' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );
        $this->add_responsive_control(
            'oul_margin', [
                'label' => esc_html__('Margin', 'e-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} ul, {{WRAPPER}} ol' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],                        
            ]
        );
        $this->add_control(
            'list_item', [
                'label' => esc_html__('List Item', 'e-addons'),
                'type' => Controls_Manager::HEADING,                        
                'separator' => 'before',
            ]
        );
        $this->add_responsive_control(
            'li_padding', [
                'label' => esc_html__('Padding', 'e-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ],
            ]
        );
        $this->add_responsive_control(
            'li_margin', [
                'label' => esc_html__('Margin', 'e-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ],                        
            ]
        );
        $this->add_responsive_control(
            'li_radius', [
                'label' => esc_html__('Radius', 'e-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],                        
            ]
        );
        $this->add_control(
            'li_color', [
                'label' => esc_html__('Color', 'e-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} li' => 'color: {{VALUE}};'
                ],
            ]
        );
        $this->add_control(
            'li_bgcolor', [
                'label' => esc_html__('Background Color', 'e-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} li' => 'background-color: {{VALUE}};'
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(), [
                'name' => 'li_border',
                'selector' => '{{WRAPPER}} li',
            ]
        );
        $this->add_control(
            'list_icon_heading', [
                'label' => esc_html__('Icon', 'e-addons'),
                'type' => Controls_Manager::HEADING,                        
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'icon_color', [
                'label' => esc_html__('Color', 'e-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} li' => 'color: {{VALUE}};'
                ],
                'selectors' => [
					'{{WRAPPER}}.e-add-list-type-icon li .e-add-icon-list i' => 'color: {{VALUE}};',
                    '{{WRAPPER}}.e-add-list-type-icon li .e-add-icon-list svg' => 'fill: {{VALUE}};',
				],
            ]
        );
        $this->add_control(
            'icon_bgcolor', [
                'label' => esc_html__('Background Color', 'e-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} li' => 'color: {{VALUE}};'
                ],
                'selectors' => [
					'{{WRAPPER}}.e-add-list-type-icon li .e-add-icon-list' => 'background-color: {{VALUE}};',
				],
            ]
        );
        $this->add_responsive_control(
			'icon_padding', [
				'label' => esc_html__('Padding', 'e-addons'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'default' => [
					'size' => '',
				],
				'range' => [
					'px' => [
						'min' => 2,
						'max' => 80,
						'step' => 1
					],
				],
                'condition' => [
                    $this->get_control_id('list_type') => '',
                    $this->get_control_id('type') => 'icon',
                ],
				'selectors' => [
					'{{WRAPPER}}.e-add-list-type-icon li .e-add-icon-list' => 'padding: {{SIZE}}{{UNIT}};',
				],
				//'frontend_available' => true,
			]
		);
        $this->add_responsive_control(
			'icon_radius', [
				'label' => esc_html__('Radius', 'e-addons'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'default' => [
					'size' => '',
				],
				'range' => [
					'px' => [
						'min' => 2,
						'max' => 80,
						'step' => 1
					],
				],
                'condition' => [
                    $this->get_control_id('list_type') => '',
                    $this->get_control_id('type') => 'icon',
                ],
				'selectors' => [
					'{{WRAPPER}}.e-add-list-type-icon li .e-add-icon-list' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
				//'frontend_available' => true,
			]
		);
        $this->add_group_control(
            Group_Control_Border::get_type(), [
                'name' => 'icon_border',
                'selector' => '{{WRAPPER}}.e-add-list-type-icon li .e-add-icon-list',
            ]
        );
        $this->end_controls_section();
    }

    public function render_element_item() {
        $settings = $this->parent->get_settings_for_display();
        
        // l'ellemmento di sx che può essere: none, default, immagine, icona/svg, numero

        $this->index++;
        $this->render_item_start();
       
        $this->render_items();
        
        //echo '<div class="e-add-simplelist-style-"'.$listStyle.'>';
        //$this->render_item_title($settings);
        //echo '</div>';
        
        $this->render_item_end();

        $this->counter++;
    }

    public function render_iteminner_before(){
         // l'icona prima del titolo
         if($this->get_instance_value('list_icon') && $this->get_instance_value('type') == 'icon'){
            echo '<span class="e-add-icon-list">';
            Icons_Manager::render_icon( $this->get_instance_value('list_icon') );
            echo '</span>';
        }
    }

    public function render_loop_start() {
        $this->parent->add_render_attribute('eaddposts_container', [
            'class' => [
                'e-list',
                'e-add-posts-container',
                'e-add-posts',
                'e-add-posts-wrapper',
                $this->get_scrollreveal_class(), //@p prevedo le classi per generare il reveal,
                $this->get_container_class(), //@p una classe personalizzata per lo skin
                $this->parent->get_container_class(),
            ],
        ]);
        // UL oppure OL
        $tag = $this->get_instance_value('type');
        if($tag == ''){
            $tag = 'div';
        }
        if($tag == 'image' || $tag == 'icon'){
            $tag = 'ul';
        }
        echo '<'.$tag.' ' . $this->parent->get_render_attribute_string('eaddposts_container') . '>';
    }

    public function render_loop_end() {
        $tag = $this->get_instance_value('type');
        if($tag == ''){
            $tag = 'div';
        }
        if($tag == 'image' || $tag == 'icon'){
            $tag = 'ul';
        }
        echo '</'.$tag.'>';
    }

    public function render_item_start($key = 'post') {
        //@p data post ID
        $data_post_id = ' data-e-add-id="' . $this->current_id . '"';
        //@p data post INDEX
        $data_post_index = ' data-e-add-index="' . $this->counter . '"';
        //@p una classe personalizzata per lo skin
        $item_class = ' ' . $this->get_item_class() . ' ' . $this->parent->get_item_class();
        
        $taglist = $this->get_instance_value('type');
        if($taglist == ''){
            $taglist = 'div';
        }else{
            $taglist = 'li';
        }
        echo '<'.$taglist.' class="e-add-item e-add-post-item-' . $this->parent->get_id() . ' e-add-item-' . $this->parent->get_id() . $item_class . '"' . $data_post_id . $data_post_index . '>';
        
        
    }

        public function render_item_end() {

            $taglist = $this->get_instance_value('type');
            if($taglist == ''){
                $taglist = 'div';
            }else{
                $taglist = 'li';
            }
            
            echo '</'.$taglist.'>';
        }
        
       
        public function get_container_class() {
            return 'e-add-simplelist-container e-add-skin-' . $this->get_id() . ' e-add-skin-' . parent::get_id() . ' e-add-skin-' . $this->get_id() . '-' . $this->get_instance_value('list_style');
        }

        public function get_wrapper_class() {
            return 'e-add-simplelist-wrapper e-add-wrapper-' . $this->get_id() . ' e-add-wrapper-' . parent::get_id();
        }

        public function get_item_class() {
            return 'e-add-simplelist-item e-add-item-' . $this->get_id() . ' e-add-item-' . parent::get_id();
        }
        
    }
    