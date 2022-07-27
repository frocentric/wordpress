<?php

namespace EAddonsForElementor\Modules\Translate\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use EAddonsForElementor\Base\Base_Widget;
use EAddonsForElementor\Core\Utils;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Translate widget
 *
 * @since 1.0.1
 */
class Select extends Base_widget {

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        //WPML - Add a floating language switcher to the footer
        //add_action('wp_footer', [$this, 'wpml_floating_language_switcher']);
    }

    public function show_in_panel() {
        return Utils::is_plugin_active('wpml');
    }

    // public function get_pid() {
    //     return 30611;
    // }
    public function get_name() {
        return 'e-wpml-select';
    }

    public function get_title() {
        return esc_html__('WPML Languages Selector', 'e-addons');
    }

    public function get_description() {
        return esc_html__('Widget WPML languages Selector', 'e-addons');
    }

    public function get_categories() {
        return ['e-addons'];
    }

    public function get_icon() {
        return 'eadd-wpml-langselector';
    }

    //
    public function get_style_depends() {
        return ['e-addons-wpml-selector'];
    }

    public function get_script_depends() {
        return ['e-add-select-fx', 'e-addons-wpml-selector'];
    }

    protected function register_controls() {
        
        if (!Utils::is_plugin_active('wpml')) return false;

        $this->start_controls_section(
                'section_wpmllangselector', [
            'label' => '<i class="eadd-wpml-langselector e-add-ic-left"></i> ' . '<i class="e-add-logo-e-addons e-add-ic-right"></i> ' . esc_html__('WPML Selector', 'e-addons'),
                ]
        );
        $this->add_control(
                'wpmllangselector_type', [
            'label' => esc_html__('Type', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'native' => esc_html__('Native', 'e-addons'),
                'list' => esc_html__('List', 'e-addons'),
                'select' => esc_html__('Select', 'e-addons'),
                'dropdown' => esc_html__('Dropdown', 'e-addons'),
            //'overlay' => 'Overlay' //to do
            ],
            'frontend_available' => true,
            'default' => 'native'
                ]
        );

        // ---------------------------
        $this->add_control(
                'wpmllangselector_style',
                [
                    'label' => esc_html__('Native Language switcher type', 'e-addons'),
                    'type' => Controls_Manager::SELECT,
                    'default' => '',
                    'options' => [
                        '' => esc_html__('From Settings', 'e-addons'),
                        'custom' => esc_html__('Custom', 'e-addons'),
                        //'header' => esc_html__( 'Header', 'e-addons' ),
                        'footer' => esc_html__('Footer', 'e-addons'),
                        'post_translations' => esc_html__('Post Translations', 'e-addons'),
                    ],
                    'condition' => [
                        'wpmllangselector_type' => 'native'
                    ],
                    'separator' => 'before'
                ]
        );

        $this->add_control(
                'wpmllangselector_display_flag',
                [
                    'label' => esc_html__('Display Flag', 'e-addons'),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 1,
                    'default' => 1,
                    'condition' => [
                        'wpmllangselector_type' => 'native',
                        'wpmllangselector_style!' => ''
                    ]
                ]
        );
        //
        $this->add_control(
                'wpmllangselector_native_language_name',
                [
                    'label' => esc_html__('Native language name', 'e-addons'),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 1,
                    'default' => 1,
                    'condition' => [
                        'wpmllangselector_type' => 'native',
                        'wpmllangselector_style!' => ''
                    ]
                ]
        );

        $this->add_control(
                'wpmllangselector_language_name_current_language',
                [
                    'label' => esc_html__('Language name in current language', 'e-addons'),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 1,
                    'default' => 1,
                    'condition' => [
                        'wpmllangselector_type' => 'native',
                        'wpmllangselector_style!' => ''
                    ]
                ]
        );

        // ---------------------------


        $this->add_control(
                'wpmllangselector_items', [
            'label' => esc_html__('Items', 'e-addons'),
            'type' => Controls_Manager::SELECT2,
            'options' => [
                'translated_name' => esc_html__('Name', 'e-addons'), //Italian
                //'active' => 'Is active',//0
                'native_name' => esc_html__('Native Name', 'e-addons'), //Italiano
                //'missing' => 'Missing',//0
                'language_code' => esc_html__('Code', 'e-addons'), //it
                'flag' => esc_html__('Flag'), //http://yourdomain/wpmlpath/res/flags/it.png
                //'url' => 'Url',//http://yourdomain/it/circa
                'default_locale' => esc_html__('Locale', 'e-addons'), //it_IT
                'id' => esc_html__('ID', 'e-addons'), //27
            ],
            'placeholder' => esc_html__('Select items', 'e-addons'),
            'multiple' => true,
            'label_block' => true,
            'default' => ['flag', 'translated_name'],
            'condition' => [
                'wpmllangselector_type!' => ['native', 'select']
            ]
                ]
        );

        // SELECT
        $this->add_control(
                'wpmllangselector_items_select', [
            'label' => esc_html__('Item', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                //'active' => 'Is active',//0
                'translated_name' => esc_html__('Name', 'e-addons'), //Italian
                'native_name' => esc_html__('Native Name', 'e-addons'), //Italiano
                //'missing' => 'Missing',//0
                'language_code' => esc_html__('Code', 'e-addons'), //it
                //'country_flag_url' => 'Flag',//http://yourdomain/wpmlpath/res/flags/it.png
                //'url' => 'Url',//http://yourdomain/it/circa
                'default_locale' => esc_html__('Locale', 'e-addons'), //it_IT
                'id' => esc_html__('ID', 'e-addons'), //27
            ],
            'default' => 'translated_name',
            'condition' => [
                'wpmllangselector_type' => 'select'
            ]
                ]
        );
        $this->add_responsive_control(
                'mlmenu_select_width', [
            'label' => esc_html__('Select Width', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'em', '%'],
            'default' => [
                'size' => '',
                'unit' => 'px',
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 400,
                ]
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-language-switcher-type-select, {{WRAPPER}} .e-add-language-switcher-type-dropdown' => 'width: {{SIZE}}{{UNIT}};'
            ],
            'condition' => [
                'wpmllangselector_type' => 'select'
            ]
                ]
        );
        // LIST
        // direction (inline or block)
        $this->add_responsive_control(
                'direction_list', [
            'label' => esc_html__('Direction', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'default' => 'block',
            'separator' => 'before',
            'toggle' => true,
            'options' => [
                'block' => [
                    'title' => esc_html__('Block', 'e-addons'),
                    'icon' => 'fas fa-bars',
                ],
                'list' => [
                    'title' => esc_html__('List', 'e-addons'),
                    'icon' => 'fas fa-ellipsis-h',
                ]
            ]/* ,
              'selectors_dictionary' => [
              'list' => 'flex-direction: row; display: flex',
              'block' => 'flex-direction: column; display: block',
              ],
              'selectors' => [
              '{{WRAPPER}} .e-add-language-switcher-type-list ul, {{WRAPPER}} .e-add-language-switcher-type-list ul li' => '{{VALUE}};',
              ] */,
            'condition' => [
                'wpmllangselector_type' => 'list'
            ]
                ]
        );
        //separator type
        $this->add_responsive_control(
                'separator_type', [
            'label' => esc_html__('Separator Type', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'toggle' => false,
            'default' => '',
            'options' => [
                '' => [
                    'title' => esc_html__('None', 'e-addons'),
                    'icon' => 'fas fa-ban',
                ],
                'border' => [
                    'title' => esc_html__('Border', 'e-addons'),
                    'icon' => 'fas fa-grip-lines-vertical',
                ],
                'chart' => [
                    'title' => esc_html__('Chart', 'e-addons'),
                    'icon' => 'fas fa-font',
                ],
            ],
            'condition' => [
                'wpmllangselector_type' => 'list',
                'direction_list' => 'list'
            ],
                ]
        );

        // text separator
        $this->add_control(
                'separator_chart', [
            'label' => esc_html__('Separator', 'e-addons'),
            'separator' => 'before',
            'type' => Controls_Manager::TEXT,
            'default' => '|',
            'condition' => [
                'wpmllangselector_type' => 'list',
                'direction_list' => 'list',
                'separator_type' => 'chart'
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-language-switcher-type-list ul li:not(:last-child):after' => 'content: \'{{VALUE}}\';',
            ]
                ]
        );
        //border color
        $this->add_control(
                'separator_border_color', [
            'label' => esc_html__('Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'separator' => 'before',
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .e-add-language-switcher-type-list ul.e-add-language-switcher-separator-border.e-add-language-switcher-direction-list li:not(:last-child) .e-add-lang-item' => 'border-right-color: {{VALUE}};',
                '{{WRAPPER}} .e-add-language-switcher-type-list ul.e-add-language-switcher-direction-block li:not(:last-child) .e-add-lang-item' => 'border-bottom-color: {{VALUE}};'
            ],
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'wpmllangselector_type',
                                'operator' => '==',
                                'value' => 'list',
                            ],
                            [
                                'name' => 'direction_list',
                                'operator' => '==',
                                'value' => 'block',
                            ],
                        ]
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'wpmllangselector_type',
                                'operator' => '==',
                                'value' => 'list',
                            ],
                            [
                                'name' => 'direction_list',
                                'operator' => '==',
                                'value' => 'list',
                            ],
                            [
                                'name' => 'separator_type',
                                'operator' => '!in',
                                'value' => ['', 'chart'],
                            ]
                        ]
                    ]
                ],
            ]
                ]
        );
        //border weight
        $this->add_control(
                'separator_border_width', [
            'label' => esc_html__('Border Width', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => 2,
            'min' => 0,
            'max' => 30,
            'step' => 1,
            'selectors' => [
                '{{WRAPPER}} .e-add-language-switcher-type-list ul.e-add-language-switcher-separator-border.e-add-language-switcher-direction-list li:not(:last-child) .e-add-lang-item' => 'border-right-width: {{VALUE}}px;',
                '{{WRAPPER}} .e-add-language-switcher-type-list ul.e-add-language-switcher-direction-block li:not(:last-child) .e-add-lang-item' => 'border-bottom-width: {{VALUE}}px;'
            ],
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'wpmllangselector_type',
                                'operator' => '==',
                                'value' => 'list',
                            ],
                            [
                                'name' => 'direction_list',
                                'operator' => '==',
                                'value' => 'block',
                            ],
                        ]
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'wpmllangselector_type',
                                'operator' => '==',
                                'value' => 'list',
                            ],
                            [
                                'name' => 'direction_list',
                                'operator' => '==',
                                'value' => 'list',
                            ],
                            [
                                'name' => 'separator_type',
                                'operator' => '!in',
                                'value' => ['', 'chart'],
                            ]
                        ]
                    ]
                ],
            ]
                ]
        );

        $this->add_control(
                'wpmllangselector_flag_pos',
                [
                    'label' => esc_html__('Flag Position', 'e-addons'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'before',
                    'separator' => 'before',
                    'options' => [
                        'before' => esc_html__('Before', 'e-addons'),
                        'after' => esc_html__('After', 'e-addons'),
                    ],
                    'condition' => [
                        'wpmllangselector_type!' => ['native', 'select']
                    ],
                ]
        );
        //@p da valutare...
        $this->add_control(
                'wpmllangselector_link_current',
                [
                    'label' => esc_html__('Show current language', 'e-addons'),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 1,
                    'default' => 1,
                    'separator' => 'before',
                    'condition' => [
                        'wpmllangselector_type' => ['native', 'list', 'select'],
                    ]
                ]
        );
        $this->add_control(
                'wpmllangselector_link_current_txt',
                [
                    'label' => esc_html__('Select language', 'e-addons'),
                    'type' => Controls_Manager::TEXT,
                    'default' => esc_html__('Select Language...', 'e-addons'),
                    'condition' => [
                        'wpmllangselector_link_current' => '',
                        'wpmllangselector_type' => ['select'],
                    ]
                ]
        );
        $this->add_control(
                'wpmllangselector_active_langs',
                [
                    'label' => esc_html__('Show only languages that have translation', 'e-addons'),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 1,
                    'default' => 1,
                    'separator' => 'before',
                    'condition' => [
                        'wpmllangselector_type' => ['select', 'dropdown', 'list'],
                    ]
                ]
        );
        /* $this->add_control(
          'wpmllangselector_enable_link',
          [
          'label' => esc_html__('Link of current', 'e-addons'),
          'deescription' => esc_html__('Link to home of language for missing translations ','e-addons')
          'type' => Controls_Manager::SWITCHER,
          'return_value' => 1,
          'default' => 1,
          'separator' => 'before',
          'condition' => [
          'wpmllangselector_type' => ['list'],
          ]
          ]
          ); */
        // ---------------
        //DROPDOWN
        // arrow
        $this->add_control(
                'icon_heading', [
            'type' => Controls_Manager::RAW_HTML,
            'show_label' => false,
            'raw' => '<i class="fas fa-star"></i> <b>' . esc_html__('Icon', 'e-addons') . '</b>',
            'content_classes' => 'e-add-inner-heading',
            'separator' => 'before',
            'condition' => [
                'wpmllangselector_type' => 'dropdown'
            ],
                ]
        );
        //icon/image
        $this->add_control(
                'wpmllangselector_icon',
                [
                    'label' => esc_html__('Icon', 'elementor'),
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
                            'chevron-down',
                            'angle-down',
                            'angle-double-down',
                            'caret-down',
                            'caret-square-down',
                            'chevron-left',
                            'angle-left',
                            'angle-double-left',
                            'caret-left',
                            'caret-square-left',
                        ],
                        'fa-regular' => [
                            'plus',
                        ],
                    ],
                    'skin' => 'inline',
                    'label_block' => false,
                    'condition' => [
                        'wpmllangselector_type' => 'dropdown'
                    ],
                ]
        );
        $this->add_control(
                'wpmllangselector_active_icon',
                [
                    'label' => esc_html__('Active Icon', 'elementor'),
                    'type' => Controls_Manager::ICONS,
                    'fa4compatibility' => 'icon_active',
                    'default' => [
                        'value' => 'fas fa-minus',
                        'library' => 'fa-solid',
                    ],
                    'recommended' => [
                        'fa-solid' => [
                            'minus',
                            'chevron-up',
                            'angle-up',
                            'angle-double-up',
                            'caret-up',
                            'caret-square-up',
                        ],
                        'fa-regular' => [
                            'caret-square-up',
                        ],
                    ],
                    'skin' => 'inline',
                    'label_block' => false,
                    'condition' => [
                        'wpmllangselector_icon[value]!' => '',
                        'wpmllangselector_type' => 'dropdown'
                    ]
                ]
        );
        // position
        $this->add_control(
                'wpmllangselector_icon_align',
                [
                    'label' => esc_html__('Alignment', 'elementor'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left' => [
                            'title' => esc_html__('Start', 'elementor'),
                            'icon' => 'eicon-h-align-left',
                        ],
                        'right' => [
                            'title' => esc_html__('End', 'elementor'),
                            'icon' => 'eicon-h-align-right',
                        ],
                    ],
                    'default' => 'right',
                    'toggle' => false,
                    'condition' => [
                        'wpmllangselector_type' => 'dropdown'
                    ],
                ]
        );

        // ---------------
        //OVERLAY

        $this->end_controls_section();

        // -----------------------------------  STYLE
        $this->start_controls_section(
                'section_mlmenu_select_style',
                [
                    'label' => '<i class="eadd-e-addoons-wpml e-add-ic-left"></i> ' . '<i class="e-add-logo-e-addons e-add-ic-right"></i> ' . esc_html__('WPML Selector', 'e-addons'),
                    'tab' => Controls_Manager::TAB_STYLE,
                    'condition' => [
                        'wpmllangselector_type' => 'select',
                    ],
                ]
        );
        //position
        $this->add_responsive_control(
                'select_position', [
            'label' => esc_html__('Position', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'toggle' => false,
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
                ],
            ],
            'default' => 'flex-start',
            'selectors' => [
                '{{WRAPPER}} .e-add-language-switcher' => 'justify-content: {{VALUE}};',
            ]
                ]
        );
        // typography
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'select_typography',
            'label' => esc_html__('Tipography', 'e-addons'),
            'selector' => '{{WRAPPER}} .e-add-language-switcher-type-select, {{WRAPPER}} .e-add-language-switcher-type-select select',
            'separator' => 'before'
                ]
        );
        //color
        $this->add_control(
                'select_color', [
            'label' => esc_html__('Text Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .e-add-language-switcher-type-select select' => 'color: {{VALUE}};',
            ]
                ]
        );
        $this->add_control(
                'select_bg_color', [
            'label' => esc_html__('BG Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .e-add-language-switcher-type-select' => 'background-color: {{VALUE}};',
            ]
                ]
        );
        $this->add_control(
                'select_arrow_color', [
            'label' => esc_html__('Arrow Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .e-add-language-switcher-type-select::after' => 'border-color: {{VALUE}};',
            ]
                ]
        );
        //border
        $this->add_group_control(
                Group_Control_Border::get_type(), [
            'name' => 'select_border',
            'label' => esc_html__('Border', 'e-addons'),
            'default' => '',
            'selector' => '{{WRAPPER}} .e-add-language-switcher-type-select',
                ]
        );
        //padding
        $this->add_responsive_control(
                'select_padding', [
            'label' => esc_html__('Padding', 'e-addons'),
            'type' => Controls_Manager::DIMENSIONS,
            'separator' => 'before',
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .e-add-language-switcher-type-select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ]
                ]
        );
        //radius
        $this->add_responsive_control(
                'select_radius', [
            'label' => esc_html__('Radius', 'e-addons'),
            'type' => Controls_Manager::DIMENSIONS,
            'separator' => 'before',
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .e-add-language-switcher-type-select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ]
                ]
        );

        $this->end_controls_section();

        // ITEMS FOR: NATIVE, LIST & DROPDOWN..
        $this->start_controls_section(
                'section_mlmenu_items_style',
                [
                    'label' => '<i class="eadd-e-addoons-wpml e-add-ic-left"></i> ' . '<i class="e-add-logo-e-addons e-add-ic-right"></i> ' . esc_html__('WPML Selector', 'e-addons'),
                    'tab' => Controls_Manager::TAB_STYLE,
                    'condition' => [
                        'wpmllangselector_type!' => 'select',
                    ],
                ]
        );
        $this->add_responsive_control(
                'position', [
            'label' => esc_html__('Position', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'toggle' => false,
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
                ],
            ],
            'default' => 'flex-start',
            'selectors' => [
                '{{WRAPPER}} .e-add-language-switcher' => 'justify-content: {{VALUE}};',
            ]
                ]
        );
        $this->add_responsive_control(
                'alignment', [
            'label' => esc_html__('Alignment', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'toggle' => false,
            'options' => [
                'left' => [
                    'title' => esc_html__('Left', 'e-addons'),
                    'icon' => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' => esc_html__('Center', 'e-addons'),
                    'icon' => 'eicon-text-align-center',
                ],
                'right' => [
                    'title' => esc_html__('Right', 'e-addons'),
                    'icon' => 'eicon-text-align-right',
                ],
            ],
            'default' => 'flex-start',
            'selectors' => [
                '{{WRAPPER}} .e-add-language-switcher' => 'text-align: {{VALUE}};',
            ],
            'condition' => [
                'direction_list' => 'block'
            ]
                ]
        );
        $this->add_control(
                'mlmenu_items_heading',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="fas fa-bars"></i> &nbsp;<b>' . esc_html__('Items', 'e-addons') . '</b>',
                    'content_classes' => 'e-add-inner-heading',
                    'separator' => 'before',
                ]
        );
        // typography
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'items_typography',
            'label' => esc_html__('Tipography', 'e-addons'),
            'selector' => '{{WRAPPER}} .e-add-lang-item',
            'separator' => 'before'
                ]
        );
        $this->add_responsive_control(
                'mlmenu_items_space', [
            'label' => esc_html__('Space', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => 'px',
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                ]
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-language-switcher .e-add-lang-item > span:not(.e-add-lang-icon)' => 'margin: 0 {{SIZE}}{{UNIT}};'
            ]
                ]
        );
        $this->add_responsive_control(
                'mlmenu_items_distance', [
            'label' => esc_html__('Distance', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => 'px',
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 600,
                ]
            ],
            'condition' => [
                //'wpmllangselector_type!' => ['native', 'select']
                'wpmllangselector_type' => 'list'
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-language-switcher.e-add-language-switcher-type-list ul.e-add-language-switcher-direction-list li:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .e-add-language-switcher.e-add-language-switcher-type-list ul.e-add-language-switcher-direction-block li:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};'
            ]
                ]
        );
        // NORMAL - HOVER - ACTIVE
        $this->start_controls_tabs('items_colors');

        $this->start_controls_tab(
                'items_style_normal',
                [
                    'label' => esc_html__('Normal', 'e-addons'),
                ]
        );
        // color text/icon/bars
        $this->add_control(
                'items_color', [
            'label' => esc_html__('Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} li.e-add-lang-li a.e-add-lang-item, {{WRAPPER}} li.wpml-ls-item a.wpml-ls-link' => 'color: {{VALUE}};',
            ]
                ]
        );
        // color background
        $this->add_control(
                'items_bgcolor', [
            'label' => esc_html__('Background Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} li.e-add-lang-li a.e-add-lang-item, {{WRAPPER}} li.wpml-ls-item a.wpml-ls-link' => 'background-color: {{VALUE}};',
            ]
                ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
                'items_style_hover',
                [
                    'label' => esc_html__('Hover', 'e-addons'),
                ]
        );
        // color text/icon/bars
        $this->add_control(
                'items_hover_color', [
            'label' => esc_html__('Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} li.e-add-lang-li a.e-add-lang-item:hover, {{WRAPPER}} li.wpml-ls-item a.wpml-ls-link:hover' => 'color: {{VALUE}};',
            ]
                ]
        );
        // color background
        $this->add_control(
                'items_hover_bgcolor', [
            'label' => esc_html__('Background Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} li.e-add-lang-li a.e-add-lang-item:hover, {{WRAPPER}} li.wpml-ls-item a.wpml-ls-link:hover' => 'background-color: {{VALUE}};',
            ]
                ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
                'items_style_active',
                [
                    'label' => esc_html__('Active', 'e-addons'),
                    'condition' => [
                    ]
                ]
        );
        // color text/icon/bars
        $this->add_control(
                'items_active_color', [
            'label' => esc_html__('Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .e-add-active-lang.e-add-lang-item, {{WRAPPER}} li.wpml-ls-current-language a.wpml-ls-link' => 'color: {{VALUE}};',
            ]
                ]
        );
        // color background
        $this->add_control(
                'items_active_bgcolor', [
            'label' => esc_html__('Background Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .e-add-active-lang.e-add-lang-item, {{WRAPPER}} li.wpml-ls-current-language a.wpml-ls-link' => 'background-color: {{VALUE}};',
            ]
                ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
                'items_padding', [
            'label' => esc_html__('Padding', 'e-addons'),
            'type' => Controls_Manager::DIMENSIONS,
            'separator' => 'before',
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                //considerare il tipo di separatore
                '{{WRAPPER}} .e-add-lang-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => [
            ]
                ]
        );
        $this->add_responsive_control(
                'items_radius', [
            'label' => esc_html__('Radius', 'e-addons'),
            'type' => Controls_Manager::DIMENSIONS,
            'separator' => 'before',
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                //considerare il tipo di separatore
                '{{WRAPPER}} .e-add-lang-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => [
            ]
                ]
        );

        $this->end_controls_section();

        // --------------------- SEPARATOR list
        $this->start_controls_section(
                'separatorlist_style',
                [
                    'label' => esc_html__('Separator', 'e-addons'),
                    'tab' => Controls_Manager::TAB_STYLE,
                    'condition' => [
                        'wpmllangselector_type' => 'list',
                        'direction_list' => 'list',
                        'separator_type' => 'chart'
                    ],
                ]
        );

        $this->add_control(
                'separator_color', [
            'label' => esc_html__('Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .e-add-language-switcher ul li:not(:last-child):after' => 'color: {{VALUE}};',
            ]
                ]
        );
        $this->add_responsive_control(
                'separator_size', [
            'label' => esc_html__('Size', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => 'px',
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 600,
                ]
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-language-switcher ul li:not(:last-child):after' => 'font-size: {{SIZE}}{{UNIT}};'
            ]
                ]
        );
        $this->add_responsive_control(
                'separator_space', [
            'label' => esc_html__('Space', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => 'px',
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 600,
                ]
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-language-switcher ul li:not(:last-child):after' => 'padding: 0 {{SIZE}}{{UNIT}};'
            ]
                ]
        );
        $this->end_controls_section();

        // --------------------- DROPDOWN
        $this->start_controls_section(
                'dropdown_style',
                [
                    'label' => esc_html__('Dropdown', 'e-addons'),
                    'tab' => Controls_Manager::TAB_STYLE,
                    'condition' => [
                        'wpmllangselector_type' => 'dropdown',
                    ],
                ]
        );
        // color background
        $this->add_control(
                'panel_bgcolor', [
            'label' => esc_html__('Background Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .e-add-lang-panel .e-add-lang-ul' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
            ]
                ]
        );
        $this->add_group_control(
                Group_Control_Border::get_type(), [
            'name' => 'panel_border',
            'label' => esc_html__('Border', 'e-addons'),
            'default' => '',
            'selector' => '{{WRAPPER}} .e-add-lang-panel .e-add-lang-ul',
            'condition' => [
            ]
                ]
        );
        // padding
        $this->add_responsive_control(
                'panel_padding', [
            'label' => esc_html__('Padding', 'e-addons'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .e-add-lang-panel .e-add-lang-ul' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => [
            ]
                ]
        );
        // radius
        $this->add_control(
                'panel_borderradius', [
            'label' => esc_html__('Border Radius', 'e-addons'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .e-add-lang-panel .e-add-lang-ul' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 0;',
            ],
            'condition' => [
            ]
                ]
        );

        // margin
        $this->add_responsive_control(
                'panel_margin', [
            'label' => esc_html__('Margin', 'e-addons'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .e-add-lang-panel .e-add-lang-ul' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => [
            ]
                ]
        );

        //
        //... ICON ...
        $this->add_control(
                'icon_style_heading', [
            'type' => Controls_Manager::RAW_HTML,
            'show_label' => false,
            'raw' => '<i class="fas fa-star"></i> <b>' . esc_html__('Icon', 'e-addons') . '</b>',
            'content_classes' => 'e-add-inner-heading',
            'separator' => 'before',
                ]
        );

        // size
        // color
        // echo '<span class="e-add-lang-icon">';
        // echo '<span class="e-add-lang-icon-opened">';
        $this->add_control(
                'dropdown_icon_color',
                [
                    'label' => esc_html__('Color', 'elementor'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        // .e-add-lang-item.e-add-active-lang
                        '{{WRAPPER}} .e-add-lang-item.e-add-active-lang .e-add-lang-icon .e-add-lang-icon-opened i:before' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .e-add-lang-item.e-add-active-lang .e-add-lang-icon .e-add-lang-icon-opened svg' => 'fill: {{VALUE}};',
                    ],
                ]
        );
        // color active
        $this->add_control(
                'dropdown_icon_active_color',
                [
                    'label' => esc_html__('Active Color', 'elementor'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .e-add-lang-item.e-add-active-lang .e-add-lang-icon .e-add-lang-icon-closed i:before' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .e-add-lang-item.e-add-active-lang .e-add-lang-icon .e-add-lang-icon-closed svg' => 'fill: {{VALUE}};',
                    ],
                ]
        );
        // size
        $this->add_responsive_control(
                'dropdown_icon_size',
                [
                    'label' => esc_html__('Size', 'elementor'),
                    'type' => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 40,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .e-add-lang-item .e-add-lang-icon i:before' => 'font-size: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .e-add-lang-item .e-add-lang-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                    ],
                ]
        );
        // space
        $this->add_responsive_control(
                'dropdown_icon_space',
                [
                    'label' => esc_html__('Spacing', 'elementor'),
                    'type' => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .e-add-language-switcher-icon-align-left .e-add-lang-item .e-add-lang-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .e-add-language-switcher-icon-align-right .e-add-lang-item .e-add-lang-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
                    ],
                ]
        );
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        if (empty($settings))
            return;

        if (!Utils::is_plugin_active('wpml')) {
            if (Utils::is_preview()) {
                esc_html_e('WPML plugin is not active', 'e-addons-for-elementor');
            }
            return;
        }



        $id_widget = $this->get_id();
        $id_page = get_the_ID();

        //@p il tipo di selettore
        $lanSelectorType = $settings['wpmllangselector_type'];

        //
        $classDir = '';
        $classSeparator = '';
        if ($lanSelectorType == 'list') {
            $classDir = ' e-add-language-switcher-direction-' . $settings['direction_list'];
            $classSeparator = ' e-add-language-switcher-separator-' . $settings['separator_type'];
        }
        $classIcon = '';
        if ($lanSelectorType == 'dropdown') {
            $classIcon = ' e-add-language-switcher-icon-align-' . $settings['wpmllangselector_icon_align'];
        }
        //
        echo '<div class="e-add-language-switcher">';

        /*
          - Native
          - List
          - Dropdown Select
          - Overlay
         */


        if ($lanSelectorType == 'native') {

            if ($settings['wpmllangselector_style']) {
                $args = array(
                    'display_link_for_current_lang' => $settings['wpmllangselector_link_current'],
                    'flags' => $settings['wpmllangselector_display_flag'],
                    'native' => $settings['wpmllangselector_native_language_name'],
                    'translated' => $settings['wpmllangselector_language_name_current_language'],
                    'type' => $settings['wpmllangselector_style'],
                );
                if ('custom' === $settings['wpmllangselector_style']) {
                    //forcing in dropdown case
                    $args['wpmllangselector_display_link_for_current_lang'] = 1;
                }
                do_action('wpml_language_switcher', $args);
            } else {
                do_action('wpml_add_language_selector');
            }
        } else {
            /*
              [id] => 27
              [active] => 0
              [default_locale] => it_IT
              [native_name] => Italiano
              [missing] => 0
              [translated_name] => Italian
              [language_code] => it
              [country_flag_url] => http://yourdomain/wpmlpath/res/flags/it.png
              [url] => http://yourdomain/it/circa
             */
            //id|code|name

            $missingLang = 0;
            if ($settings['wpmllangselector_active_langs']) {
                $missingLang = 1;
            }


            $languages = apply_filters('wpml_active_languages', NULL, 'skip_missing=' . $missingLang . '&orderby=code&order=desc');
            if (!empty($languages)) {
                //@ la classe che determina se la lingua visualizzata è una sola per cui non genera ne select ne dropdow
                $singleLang = '';
                if (count($languages) == 1) {
                    $singleLang = ' e-add-single-lang';
                }

                $classType = ' e-add-language-switcher-type-' . $lanSelectorType;
                echo '<div id="e-add-language-switcher-' . $id_widget . '" class="e-add-language-switcher' . $classType . $singleLang . $classIcon . '">';

                //@p la lingua attiva sta sopra l'ul(fuori)
                if ($lanSelectorType == 'dropdown')
                    $this->wpml_languages_list($languages, $settings, 0);
                //
                if ($lanSelectorType == 'list' || $lanSelectorType == 'dropdown') {
                    if ($lanSelectorType == 'dropdown')
                        echo '<div class="e-add-lang-panel">'; //open panel
                    echo '<ul class="e-add-lang-ul' . $classDir . $classSeparator . '">';
                } else {
                    //select
                    if (count($languages) > 1)
                        echo '<select class="e-add-language-switcher-select" onChange="location.replace(jQuery(this).find(\'option[value=\'+jQuery(this).val()+\']\').data(\'link\'))">';
                }

                //--------------------------------------------------------------------- 
                if ($lanSelectorType == 'list' || $lanSelectorType == 'select') {
                    $this->wpml_languages_list($languages, $settings, 1);
                } else {
                    //select
                    $this->wpml_languages_list($languages, $settings, 2);
                }
                //---------------------------------------------------------------------

                if ($lanSelectorType == 'list' || $lanSelectorType == 'dropdown') {
                    echo '</ul>';
                    if ($lanSelectorType == 'dropdown')
                        echo '</div>'; //close panel
                    
                } else {
                    if (count($languages) > 1)
                        echo '</select>';
                }

                echo '</div>';
            }
        }

        echo '</div>';
    }

    public function wpml_languages_list($languages, $settings, $activemode = 1) {
        //@p$activemode: 0=solo la lingua attiva - 1=tutte - 2=solo le non attive
        //@p il tipo di selettore
        $lanSelectorType = $settings['wpmllangselector_type'];

        foreach ($languages as $key => $l) {
            $classActive = $l['active'] ? ' e-add-active-lang' : '';

            //@p separatore di lista
            //if( !empty($settings['separator_chart']) && $lanSelectorType == 'list' && $key > 0) $settings['separator_chart'];
            //@p per visualizzare gli elementi
            $langsItems = $settings['wpmllangselector_items'];

            if (!empty($langsItems)) {

                $dataItem = '';

                
                foreach ($langsItems as $value) {
                    if($value == 'flag'){
                        if ($settings['wpmllangselector_flag_pos'] == 'before') {
                            //@p in caso di bandierina
                            if ($l['country_flag_url']) {
                                $dataItem .= '<span><img src="' . $l['country_flag_url'] . '" alt="' . $l['native_name'] . '" /></span>';
                            }
                        }
                    }
                    switch ($value) {
                        case "id":
                            $dataItem .= '<span>' . $l['id'] . '</span>';

                            break;
                        case "default_locale":
                            $dataItem .= '<span>' . $l['default_locale'] . '</span>';

                            break;
                        case "native_name":
                            $dataItem .= '<span>' . $l['native_name'] . '</span>';

                            break;
                        case "translated_name":
                            $dataItem .= '<span>' . $l['translated_name'] . '</span>';

                            break;
                        case "language_code":
                            $dataItem .= '<span>' . $l['language_code'] . '</span>';

                            break;
                    }
                    if($value == 'flag'){
                        if ($settings['wpmllangselector_flag_pos'] == 'after') {
                            //@p in caso di bandierina
                            if ($l['country_flag_url']) {
                                $dataItem .= '<span><img src="' . $l['country_flag_url'] . '" alt="' . $l['native_name'] . '" /></span>';
                            }
                        }
                    }
                }// end foreach langsitems
            }// end - if


            //risultato...
            //@p in caso di SELECT
            if ($lanSelectorType == 'select') {
                //...
            }

            //$native_name, $translated_name = false, $show_native_name = false, $show_translate_name = false, $include_html = true
            //echo apply_filters( 'wpml_display_language_names', NULL, $l['native_name'], $l['translated_name'] );

            $exit = false;

            if ($activemode == 0 && $l['active']) {
                $exit = true;
            } else if ($activemode == 1) {
                $exit = true;
            } else if ($activemode == 2 && !$l['active']) {
                $exit = true;
            }
            // 

            if ($exit) {
                //@p in caso di LIST (li)
                if ($lanSelectorType == 'list' || $lanSelectorType == 'dropdown') {

                    //@p linkato solo se non è la lingua attiva altrimenti è un div
                    if (!$l['active']) {
                        echo '<li class="e-add-lang-li">';
                        echo '<a class="e-add-lang-item" href="' . $l['url'] . '">';

                        echo $dataItem;
                    } else {
                        if ($activemode == 1) {
                            //@p se i linguaggio corrente è disabilitato non si vede
                            if ($settings['wpmllangselector_link_current']) {
                                echo '<li class="e-add-lang-li">';
                                echo '<span class="e-add-active-lang e-add-lang-item">';

                                echo $dataItem;
                            }
                        } else {
                            $singleLang = count($languages) > 1 ? '' : ' e-add-single-lang';
                            echo '<div class="e-add-lang-item e-add-active-lang' . $singleLang . '">';
                            //
                            if ($settings['wpmllangselector_icon_align'] == 'left' && count($languages) > 1) {
                                $this->wpml_languages_icon($settings['wpmllangselector_icon'], $settings['wpmllangselector_active_icon']);
                            }

                            echo $dataItem;
                        }
                    }



                    //@p chiudo in caso di lingua non attiva faccio a altrimenti un div.. per controllaree gli stili
                    if (!$l['active']) {
                        echo '</a>';
                        echo '</li>';
                    } else {
                        if ($activemode == 1) {
                            if ($settings['wpmllangselector_link_current']) {
                                echo '</span></li>';
                            }
                        } else {
                            if ($settings['wpmllangselector_icon_align'] == 'right' && count($languages) > 1) {
                                $this->wpml_languages_icon($settings['wpmllangselector_icon'], $settings['wpmllangselector_active_icon']);
                            }
                            //
                            echo '</div>';
                        }
                    }
                } else {
                    //@p in caso di SELECT ..
                    if ($lanSelectorType == 'select') {
                        $keyOfItem = $settings['wpmllangselector_items_select'];
                    }

                    if ($l['active']) {
                        if ($settings['wpmllangselector_link_current']) {
                            //@p se è maggiore di 1 genero il select altrimenti è un testo
                            if (count($languages) > 1) {
                                $isSelected = $l['active'] ? ' selected' : '';
                                echo '<option' . $isSelected . ' value="' . $l['default_locale'] . '" data-class="e-add-lang-li" data-link="' . $l['url'] . '">' . $l[$keyOfItem] . '</option>';
                            } else {
                                echo '<div class="e-add-lang-item e-add-active-lang">';
                                echo $l[$keyOfItem];
                                echo '</div>';
                            }
                        } else {
                            if (count($languages) > 1) {
                                $isSelected = $l['active'] ? ' selected' : '';
                                echo '<option' . $isSelected . ' value="' . $l['default_locale'] . '" data-class="e-add-lang-li" data-link="' . $l['url'] . '">' . $settings['wpmllangselector_link_current_txt'] . '</option>';
                            }
                        }
                    } else {
                        echo '<option value="' . $l['default_locale'] . '" data-class="e-add-lang-li" data-link="' . $l['url'] . '">' . $l[$keyOfItem] . '</option>';
                    }
                }
            }
        }// end foreach languages
    }

    public function wpml_languages_icon($o, $c) {
        echo '<span class="e-add-lang-icon">';
        echo '<span class="e-add-lang-icon-opened">';
        Icons_Manager::render_icon($o);
        echo '</span>';
        echo '<span class="e-add-lang-icon-closed">';
        Icons_Manager::render_icon($c);
        echo '</span>';
        echo '</span>';
    }

    public function wpml_floating_language_switcher() {
        echo '<div class="e-add-language-switcher">';
        //PHP action to display the language switcher (see https://wpml.org/documentation/getting-started-guide/language-setup/language-switcher-options/#using-php-actions)
        do_action('wpml_add_language_selector');
        echo '</div>';
    }

    /* public function eadd_wpml_language_switcher($native_name, $translated_name = false, $show_native_name = false, $show_translate_name = false, $include_html = true) {
      return '.......';
      } */
}