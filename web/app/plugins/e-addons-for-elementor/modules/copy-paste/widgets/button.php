<?php

namespace EAddonsForElementor\Modules\CopyPaste\Widgets;

use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use EAddonsForElementor\Base\Base_Widget;
use EAddonsForElementor\Core\Utils;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Copy button
 *
 * Elementor widget for e-addons
 * Extend Elementor Free Button Widget
 */
class Button extends Base_Widget {

    //use \Elementor\Includes\Widgets\Traits\Button;

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);
        wp_register_script('clipboard', home_url("/wp-includes/js/clipboard.min.js"));
        //$this->register_script('assets/lib/clipboard.js/clipboard.min.js'); // from module folder
    }

    public function get_name() {
        return 'copy-button';
    }

    public function get_title() {
        return esc_html__('Copy Button', 'e-addons');
    }

    public function get_icon() {
        return 'eadd-button-copy';
    }

    public function get_categories() {
        return ['e-addons'];
    }

    public function get_pid() {
        return 221;
    }

    /**
     * A list of scripts that the widgets is depended in
     * @since 1.3.0
     * */
    public function get_script_depends() {
        return ['clipboard', 'e-addons-copy-btn'];
    }
    
    /**
     * A list of styles that the widgets is depended in
     * @since 1.3.0
     * */
    public function get_style_depends() {
        return ['e-animations'];
    }

    /**
     * Register button widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls() {

        //use \Elementor\Includes\Widgets\Traits\Button;

        $this->start_controls_section(
                'section_button',
                [
                    'label' => esc_html__('Button', 'elementor'),
                ]
        );

        //$this->register_button_content_controls();

        $this->add_control(
                'button_type',
                [
                    'label' => esc_html__('Type', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => esc_html__('Default', 'elementor'),
                        'info' => esc_html__('Info', 'elementor'),
                        'success' => esc_html__('Success', 'elementor'),
                        'warning' => esc_html__('Warning', 'elementor'),
                        'danger' => esc_html__('Danger', 'elementor'),
                    ],
                    'prefix_class' => 'elementor-button-',
                ]
        );

        $this->add_control(
                'text',
                [
                    'label' => esc_html__('Text', 'elementor'),
                    'type' => Controls_Manager::TEXT,
                    'dynamic' => [
                        'active' => true,
                    ],
                    'placeholder' => esc_html__('Copy to Clipboard', 'e-addons'),
                ]
        );

        $this->add_responsive_control(
                'align',
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
                            'title' => esc_html__('Justified', 'elementor'),
                            'icon' => 'eicon-text-align-justify',
                        ],
                    ],
                    'render_type' => 'template',
                    'prefix_class' => 'elementor%s-align-',
                    'default' => '',
                ]
        );

        $this->add_control(
                'size',
                [
                    'label' => esc_html__('Size', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'sm',
                    'options' => [
                        'xs' => esc_html__('Extra Small', 'elementor'),
                        'sm' => esc_html__('Small', 'elementor'),
                        'md' => esc_html__('Medium', 'elementor'),
                        'lg' => esc_html__('Large', 'elementor'),
                        'xl' => esc_html__('Extra Large', 'elementor'),
                    ],
                    'style_transfer' => true,
                ]
        );

        $this->add_control(
                'selected_icon',
                [
                    'label' => esc_html__('Icon', 'elementor'),
                    'type' => Controls_Manager::ICONS,
                    'label_block' => true,
                    'render_type' => 'template',
                    'fa4compatibility' => 'icon',
                    'default' => ['value' => 'far fa-clipboard', 'library' => 'fa-regular'],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-button .elementor-align-icon-left' => 'margin-left: 0; margin-right: 0;',
                    ],
                ]
        );

        $this->add_control(
                'icon_align',
                [
                    'label' => esc_html__('Icon Position', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'left',
                    'options' => [
                        'left' => esc_html__('Before', 'elementor'),
                        'right' => esc_html__('After', 'elementor'),
                    ],
                    'condition' => [
                        'selected_icon[value]!' => '',
                    ],
                ]
        );

        $this->add_control(
                'icon_indent',
                [
                    'label' => esc_html__('Icon Spacing', 'elementor'),
                    'type' => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'max' => 50,
                        ],
                    ],
                    'default' => ['size' => 15, 'unit' => 'px'],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-button .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .elementor-button .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'text!' => '',
                        'selected_icon[value]!' => '',
                    ]
                ]
        );
        $this->add_control(
                'icon_size',
                [
                    'label' => esc_html__('Icon Size', 'elementor'),
                    'type' => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => 10,
                            'max' => 60,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-button .elementor-button-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .elementor-button .elementor-button-text' => 'line-height: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'selected_icon[value]!' => '',
                    ]
                ]
        );

        $this->add_control(
                'button_css_id',
                [
                    'label' => esc_html__('Button ID', 'elementor'),
                    'type' => Controls_Manager::TEXT,
                    'dynamic' => [
                        'active' => true,
                    ],
                    'default' => '',
                    'title' => esc_html__('Add your custom id WITHOUT the Pound key. e.g: my-id', 'elementor'),
                    'label_block' => false,
                    'description' => esc_html__('Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'elementor'),
                    'separator' => 'before',
                ]
        );

        /*         * ************ */

        $animations = \Elementor\Control_Animation::get_animations();
        if (!empty($animations['Attention Seekers'])) {
            $animations = array('' => esc_html__('None', 'elementor')) + $animations['Attention Seekers'];
        }
        $this->add_control(
                'click_animation',
                [
                    'label' => esc_html__('Click Animation', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'options' => $animations,
                    'default' => 'jello',
                ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
                'section_style',
                [
                    'label' => esc_html__('Button', 'elementor'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        //$this->register_button_style_controls();

        $args = [
            'section_condition' => [],
        ];

        $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'typography',
                    'global' => [
                        'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                    ],
                    'selector' => '{{WRAPPER}} .elementor-button',
                    'condition' => $args['section_condition'],
                ]
        );

        $this->add_group_control(
                Group_Control_Text_Shadow::get_type(),
                [
                    'name' => 'text_shadow',
                    'selector' => '{{WRAPPER}} .elementor-button',
                    'condition' => $args['section_condition'],
                ]
        );

        $this->start_controls_tabs('tabs_button_style', [
            'condition' => $args['section_condition'],
        ]);

        $this->start_controls_tab(
                'tab_button_normal',
                [
                    'label' => esc_html__('Normal', 'elementor'),
                    'condition' => $args['section_condition'],
                ]
        );

        $this->add_control(
                'button_text_color',
                [
                    'label' => esc_html__('Text Color', 'elementor'),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .elementor-button' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    ],
                    'condition' => $args['section_condition'],
                ]
        );

        $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'background',
                    'label' => esc_html__('Background', 'elementor'),
                    'types' => ['classic', 'gradient'],
                    'exclude' => ['image'],
                    'selector' => '{{WRAPPER}} .elementor-button',
                    'fields_options' => [
                        'background' => [
                            'default' => 'classic',
                        ],
                        'color' => [
                            'global' => [
                                'default' => Global_Colors::COLOR_ACCENT,
                            ],
                        ],
                    ],
                    'condition' => $args['section_condition'],
                ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
                'tab_button_hover',
                [
                    'label' => esc_html__('Hover', 'elementor'),
                    'condition' => $args['section_condition'],
                ]
        );

        $this->add_control(
                'hover_color',
                [
                    'label' => esc_html__('Text Color', 'elementor'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .elementor-button:hover svg, {{WRAPPER}} .elementor-button:focus svg' => 'fill: {{VALUE}};',
                    ],
                    'condition' => $args['section_condition'],
                ]
        );

        $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'button_background_hover',
                    'label' => esc_html__('Background', 'elementor'),
                    'types' => ['classic', 'gradient'],
                    'exclude' => ['image'],
                    'selector' => '{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus',
                    'fields_options' => [
                        'background' => [
                            'default' => 'classic',
                        ],
                    ],
                    'condition' => $args['section_condition'],
                ]
        );

        $this->add_control(
                'button_hover_border_color',
                [
                    'label' => esc_html__('Border Color', 'elementor'),
                    'type' => Controls_Manager::COLOR,
                    'condition' => [
                        'border_border!' => '',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'border-color: {{VALUE}};',
                    ],
                    'condition' => $args['section_condition'],
                ]
        );

        $this->add_control(
                'hover_animation',
                [
                    'label' => esc_html__('Hover Animation', 'elementor'),
                    'type' => Controls_Manager::HOVER_ANIMATION,
                    'condition' => $args['section_condition'],
                ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'border',
                    'selector' => '{{WRAPPER}} .elementor-button',
                    'separator' => 'before',
                    'condition' => $args['section_condition'],
                ]
        );

        $this->add_control(
                'border_radius',
                [
                    'label' => esc_html__('Border Radius', 'elementor'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', '%', 'em'],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'condition' => $args['section_condition'],
                ]
        );

        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'button_box_shadow',
                    'selector' => '{{WRAPPER}} .elementor-button',
                    'condition' => $args['section_condition'],
                ]
        );

        $this->add_responsive_control(
                'text_padding',
                [
                    'label' => esc_html__('Padding', 'elementor'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' => 'before',
                    'condition' => $args['section_condition'],
                ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
                'section_content',
                [
                    'label' => esc_html__('Content', 'elementor'),
                ]
        );

        $this->add_control(
                'e_clipboard_type',
                [
                    'label' => esc_html__('Type', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'text' => esc_html__('Text', 'elementor'),
                        'target' => esc_html__('Target', 'e-addons'),
                    ],
                    'default' => 'text',
                ]
        );

        $this->add_control(
                'e_clipboard_target', [
            'label' => esc_html__('Target', 'elementor'),
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => '#my_section',
            'condition' => [
                'e_clipboard_type' => 'target',
            ]
                ]
        );

        $this->add_control(
                'e_clipboard_text', [
            'label' => esc_html__('Content', 'elementor'),
            'type' => Controls_Manager::TEXTAREA,
            'label_block' => true,
            'default' => 'Sample demo text.' . PHP_EOL . 'Find more at e-addons.com',
            'condition' => [
                'e_clipboard_type' => 'text',
            ]
                ]
        );

        $this->add_control(
                'e_clipboard_visible', [
            'label' => esc_html__('Show Content', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'yes' => [
                    'title' => esc_html__('Yes', 'elementor'),
                    'icon' => 'eicon-text-field',
                ],
                'no' => [
                    'title' => esc_html__('No', 'elementor'),
                    'icon' => 'eicon-button',
                ],
            ],
            'default' => 'yes',
            'render_type' => 'template',
            'selectors' => [
                '{{WRAPPER}} .e-offscreen' => 'position: absolute; left: -999em; display: block !important;',
                '{{WRAPPER}} .e-input-group' => 'display: flex;position: relative;flex-wrap: wrap;align-items: stretch;width: 100%;',
                '{{WRAPPER}} .e-input-group-append, {{WRAPPER}} .e-input-group-prepend' => 'display: flex;',
                //'{{WRAPPER}} .e-input-group-append' => 'margin-left: -1px;',
                '{{WRAPPER}} .e-input-group > .e-form-control' => 'position: relative; flex: 1 1 auto; width: 1%; margin-bottom: 0;',
            ],
            'condition' => [
                'e_clipboard_type' => 'text',
            ]
                ]
        );

        $this->add_control(
                'e_clipboard_btn_position', [
            'label' => esc_html__('Button Position', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                '' => [
                    'title' => esc_html__('Inline', 'elementor'),
                    'icon' => 'eicon-h-align-stretch',
                ],
                'before' => [
                    'title' => esc_html__('Before', 'elementor'),
                    'icon' => 'eicon-v-align-top',
                ],
                'after' => [
                    'title' => esc_html__('After', 'elementor'),
                    'icon' => 'eicon-v-align-bottom',
                ],
            ],
            'condition' => [
                'e_clipboard_visible' => 'yes',
                'e_clipboard_type' => 'text',
            ]
                ]
        );

        $this->add_control(
                'e_clipboard_readonly', [
            'label' => esc_html__('Read Only', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'condition' => [
                'e_clipboard_visible' => 'yes',
                'e_clipboard_type' => 'text',
            ]
                ]
        );

        $this->add_control(
                'e_clipboard_entities', [
            'label' => esc_html__('Fix HtmlEntities', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
                ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
                'section_style_value',
                [
                    'label' => esc_html__('Content', 'elementor'),
                    'tab' => Controls_Manager::TAB_STYLE,
                    'condition' => [
                        'e_clipboard_visible' => 'yes',
                        'e_clipboard_type' => 'text',
                    ]
                ]
        );

        $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'typography_value',
                    'selector' => '{{WRAPPER}} .elementor-field-textual',
                ]
        );

        $this->add_group_control(
                Group_Control_Text_Shadow::get_type(),
                [
                    'name' => 'text_shadow_value',
                    'selector' => '{{WRAPPER}} .elementor-field-textual',
                ]
        );

        $this->start_controls_tabs('tabs_button_style_value');

        $this->start_controls_tab(
                'tab_button_normal_value',
                [
                    'label' => esc_html__('Normal', 'elementor'),
                ]
        );

        $this->add_control(
                'button_text_color_value',
                [
                    'label' => esc_html__('Text Color', 'elementor'),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .elementor-field-textual' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    ],
                ]
        );

        $this->add_control(
                'background_color_value',
                [
                    'label' => esc_html__('Background Color', 'elementor'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .elementor-field-textual' => 'background-color: {{VALUE}};',
                    ],
                ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
                'tab_button_hover_value',
                [
                    'label' => esc_html__('Hover', 'elementor'),
                ]
        );

        $this->add_control(
                'hover_color_value',
                [
                    'label' => esc_html__('Text Color', 'elementor'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .elementor-field-textual:hover, {{WRAPPER}} .elementor-field-textual:focus' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .elementor-field-textual:hover svg, {{WRAPPER}} .elementor-field-textual:focus svg' => 'fill: {{VALUE}};',
                    ],
                ]
        );

        $this->add_control(
                'button_background_hover_color_value',
                [
                    'label' => esc_html__('Background Color', 'elementor'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .elementor-field-textual:hover, {{WRAPPER}} .elementor-field-textual:focus' => 'background-color: {{VALUE}};',
                    ],
                ]
        );

        $this->add_control(
                'button_hover_border_color_value',
                [
                    'label' => esc_html__('Border Color', 'elementor'),
                    'type' => Controls_Manager::COLOR,
                    'condition' => [
                        'border_border!' => '',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-field-textual:hover, {{WRAPPER}} .elementor-field-textual:focus' => 'border-color: {{VALUE}};',
                    ],
                ]
        );

        $this->add_control(
                'hover_animation_value',
                [
                    'label' => esc_html__('Hover Animation', 'elementor'),
                    'type' => Controls_Manager::HOVER_ANIMATION,
                ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'border_value',
                    'selector' => '{{WRAPPER}} .elementor-field-textual',
                    'separator' => 'before',
                ]
        );

        $this->add_control(
                'border_radius_value',
                [
                    'label' => esc_html__('Border Radius', 'elementor'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-field-textual' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
        );

        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'button_box_shadow_value',
                    'selector' => '{{WRAPPER}} .elementor-field-textual',
                ]
        );

        $this->add_responsive_control(
                'text_padding_value',
                [
                    'label' => esc_html__('Padding', 'elementor'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-field-textual' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' => 'before',
                ]
        );

        $this->end_controls_section();
    }

    /**
     * Render button widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        if (empty($settings))
            return;

        $value = ($settings['e_clipboard_type'] == 'text') ? $settings['e_clipboard_text'] : '';
        if (!empty($settings['e_clipboard_entities'])) {
            $value = htmlentities($value);
        }
        $value_lines = Utils::explode($value, PHP_EOL);

        if ($settings['e_clipboard_visible'] == 'yes') {
            if (!$settings['e_clipboard_btn_position']) {
                if ($settings['align'] == 'right') {
                    $this->add_render_attribute('wrapper-btn', 'class', 'e-input-group-append');
                    $this->add_render_attribute('wrapper', 'class', 'elementor-field-group');
                    $this->add_render_attribute('wrapper', 'class', 'e-input-group');
                }
                if ($settings['align'] == 'left') {
                    $this->add_render_attribute('wrapper-btn', 'class', 'e-input-group-prepend');
                    $this->add_render_attribute('wrapper', 'class', 'elementor-field-group');
                    $this->add_render_attribute('wrapper', 'class', 'e-input-group');
                }
            }
            $this->add_render_attribute('wrapper-btn', 'class', 'elementor-field-type-submit');
        }

        $this->add_render_attribute('button', 'class', 'elementor-button');
        if (!empty($settings['button_css_id'])) {
            $this->add_render_attribute('button', 'id', $settings['button_css_id']);
        }
        if ($settings['hover_animation']) {
            $this->add_render_attribute('button', 'class', 'elementor-animation-' . $settings['hover_animation']);
        }

        if (!empty($settings['size'])) {
            $this->add_render_attribute('button', 'class', 'elementor-size-' . $settings['size']);
            $this->add_render_attribute('input', 'class', 'elementor-size-' . $settings['size']);
        }

        switch ($settings['e_clipboard_type']) {
            case 'target':
                $target = $settings['e_clipboard_target'];
                $this->add_render_attribute('button', 'data-copy-target', $target);
            default:
                $target = '#e-clipboard-value-' . $this->get_id() . '-' . get_the_id();
                $this->add_render_attribute('button', 'data-clipboard-target', $target);
        }

        $this->add_render_attribute('button', 'type', 'button');
        $this->add_render_attribute('button', 'id', 'e-clipboard-btn-' . $this->get_id() . '-' . get_the_id());

        if (empty($value) || $settings['e_clipboard_visible'] == 'no') {
            $this->add_render_attribute('input', 'aria-hidden', 'true');
            if (Utils::is_preview()) {
                $this->add_render_attribute('input', 'class', 'elementor-hidden');
            } else {
                $this->add_render_attribute('input', 'class', 'e-offscreen');
            }
        }

        $this->add_render_attribute('input', 'id', 'e-clipboard-value-' . $this->get_id() . '-' . get_the_id());
        $this->add_render_attribute('input', 'class', 'elementor-field-textual');
        if (!empty($settings['e_clipboard_readonly'])) {
            $this->add_render_attribute('input', 'readonly');
        }
        ?>
        <div <?php echo $this->get_render_attribute_string('wrapper'); ?>>
            <?php
            $this->add_render_attribute('input', 'type', 'text');
            $this->add_render_attribute('input', 'value', $value);
            $this->add_render_attribute('input', 'class', 'e-form-control');

            $btn_rendered = false;
            if ((empty($settings['e_clipboard_btn_position']) && $settings['align'] != 'right') || $settings['e_clipboard_btn_position'] == 'before') {
                $this->render_button();
                $btn_rendered = true;
            }
            //var_dump($value_lines);
            if (count($value_lines) < 2) {
                ?>
                <input <?php echo $this->get_render_attribute_string('input'); ?>>            
                <?php } else {
                ?>
                <textarea <?php echo $this->get_render_attribute_string('input'); ?>><?php echo $value; ?></textarea>    
                <?php
            }
            if (!$btn_rendered) {
                $this->render_button();
            }
            ?>
        </div>
        <?php
    }

    /**
     * Render button text.
     *
     * Render button widget text.
     *
     * @since 1.5.0
     * @access protected
     */
    protected function render_button() {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute([
            'content-wrapper' => [
                'class' => ['elementor-button-content-wrapper', 'e-flexbox'],
            ],
            'icon-align' => [
                'class' => [
                    'elementor-button-icon',
                    'elementor-align-icon-' . $settings['icon_align'],
                ],
            ],
            'text' => [
                'class' => 'elementor-button-text',
            ],
        ]);

        if (!empty($settings['click_animation'])) {
            $this->add_render_attribute('button', 'data-animation', $settings['click_animation']);
        }

        $this->add_inline_editing_attributes('text', 'none');
        ?>
        <div <?php echo $this->get_render_attribute_string('wrapper-btn'); ?>>       
            <button <?php echo $this->get_render_attribute_string('button'); ?>>
                <span <?php echo $this->get_render_attribute_string('content-wrapper'); ?>>
                        <?php if (!empty($settings['icon']) || !empty($settings['selected_icon']['value'])) : ?>
                        <span <?php echo $this->get_render_attribute_string('icon-align'); ?>>
                        <?php Icons_Manager::render_icon($settings['selected_icon'], ['aria-hidden' => 'true']); ?>
                        </span>
        <?php endif; ?>
                    <span <?php echo $this->get_render_attribute_string('text'); ?>><?php echo $settings['text']; ?></span>
                </span>
            </button>
        </div>
        <?php
    }

    public
            function on_import($element) {
        return Icons_Manager::on_import_migration($element, 'icon', 'selected_icon');
    }

}
