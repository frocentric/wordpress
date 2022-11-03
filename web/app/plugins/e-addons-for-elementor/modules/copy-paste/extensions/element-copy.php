<?php

namespace EAddonsForElementor\Modules\CopyPaste\Extensions;

use EAddonsForElementor\Base\Base_Extension;
use EAddonsForElementor\Core\Utils;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Element_Copy extends Base_Extension {

    public $common = true;

    public function __construct() {
        parent::__construct();
        $this->add_actions();
    }

    public function get_name() {
        return 'copy';
    }

    /**
     * Get Label
     *
     * Returns the action label
     *
     * @access public
     * @return string
     */
    public function get_label() {
        return __('Frontend Download/Copy', 'e-addons');
    }

    public function get_pid() {
        return 14919;
    }

    public function get_icon() {
        return 'eadd-element-copy-download';
    }

    /**
     * Add Actions
     *
     * @access private
     */
    protected function add_actions() {
        foreach ($this->common_sections_actions as $el_type) {
            add_action('elementor/element/' . $el_type . '/e_section_' . $this->get_name() . '_advanced/before_section_end', [$this, 'add_controls'], 20, 2);
            add_action('elementor/element/' . $el_type . '/e_section_' . $this->get_name() . '_advanced/after_section_end', [$this, 'add_style_section'], 20, 2);
        }

        //add_action('elementor/frontend/before_render', [$this, '_before_render']);
        add_action('elementor/frontend/after_render', [$this, '_after_render']);
        add_action('elementor/widget/render_content', array($this, '_render_content'), 11, 2);
        //add_filter('elementor/widget/print_template', array($this, '_print_template'), 11, 2);
    }

    /**
     * Add Controls
     *
     * @since 0.5.5
     *
     * @access private
     */
    public function add_controls($element, $args = array()) {

        $element->add_control(
                'e_frontend_copy', [
            'label' => __('Enable Frontend Buttons', 'elementor'),
            'type' => Controls_Manager::SWITCHER,
            'selectors' => [
                '{{WRAPPER}} .e-offscreen' => 'position: absolute; left: -999em; display: block !important;',
                '{{WRAPPER}} .e-block' => 'display: block !important; width: 100%;',
                '{{WRAPPER}} .e-frontend-copy .elementor-button' => 'cursor: pointer;',
            ]
                ]
        );

        $element->add_control(
                'e_frontend_visitors', [
            'label' => __('Enable for Visitors', 'elementor'),
            'type' => Controls_Manager::SWITCHER,
            'condition' => [
                'e_frontend_copy!' => '',
            ]
                ]
        );

        $element->add_control(
                'e_frontend_copy_action',
                [
                    'label' => __('Action', 'elementor'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'copy' => [
                            'title' => __('Copy', 'elementor'),
                            'icon' => 'eicon-copy',
                        ],
                        'download' => [
                            'title' => __('Download', 'elementor'),
                            'icon' => 'eicon-download-button',
                        ],
                        'both' => [
                            'title' => __('Both', 'elementor'),
                            'icon' => 'eicon-dual-button',
                        ],
                    ],
                    'default' => 'copy',
                    'condition' => [
                        'e_frontend_copy!' => '',
                    ]
                ]
        );

        $element->add_control(
                'e_frontend_copy_text_copy',
                [
                    'label' => __('Text Copy', 'elementor'),
                    'type' => Controls_Manager::TEXT,
                    //'default' => __('Copy', 'elementor'),
                    'placeholder' => __('Copy', 'elementor'),
                    'condition' => [
                        'e_frontend_copy!' => '',
                        'e_frontend_copy_action' => ['copy', 'both'],
                    ]
                ]
        );
        $element->add_control(
                'e_frontend_copy_selected_icon_copy',
                [
                    'label' => __('Icon Copy', 'elementor'),
                    'type' => Controls_Manager::ICONS,
                    'fa4compatibility' => 'icon',
                    'default' => [
                        'value' => 'fas fa-copy',
                        'library' => 'fa-solid',
                    ],
                    'skin' => 'inline',
                    'label_block' => false,
                    'condition' => [
                        'e_frontend_copy!' => '',
                        'e_frontend_copy_action' => ['copy', 'both'],
                    ]
                ]
        );

        $element->add_control(
                'e_frontend_copy_text_download',
                [
                    'label' => __('Text Download', 'elementor'),
                    'type' => Controls_Manager::TEXT,
                    //'default' => __('Download', 'elementor'),
                    'placeholder' => __('Download', 'elementor'),
                    'condition' => [
                        'e_frontend_copy!' => '',
                        'e_frontend_copy_action' => ['download', 'both'],
                    ]
                ]
        );
        $element->add_control(
                'e_frontend_copy_selected_icon_download',
                [
                    'label' => __('Icon Download', 'elementor'),
                    'type' => Controls_Manager::ICONS,
                    'fa4compatibility' => 'icon',
                    'default' => [
                        'value' => 'fas fa-download',
                        'library' => 'fa-solid',
                    ],
                    'skin' => 'inline',
                    'label_block' => false,
                    'condition' => [
                        'e_frontend_copy!' => '',
                        'e_frontend_copy_action' => ['download', 'both'],
                    ]
                ]
        );
    }

    public function add_style_section($element, $args = array()) {
        $element->start_controls_section(
                'section_e_frontend_copy_style',
                [
                    'label' => __('Frontend Copy Button', 'elementor'),
                    'tab' => Controls_Manager::TAB_STYLE,
                    'condition' => [
                        'e_frontend_copy!' => '',
                    ]
                ]
        );

        $element->add_responsive_control(
                'e_frontend_copy_align',
                [
                    'label' => __('Alignment', 'elementor'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left' => [
                            'title' => __('Left', 'elementor'),
                            'icon' => 'eicon-text-align-left',
                        ],
                        'center' => [
                            'title' => __('Center', 'elementor'),
                            'icon' => 'eicon-text-align-center',
                        ],
                        'right' => [
                            'title' => __('Right', 'elementor'),
                            'icon' => 'eicon-text-align-right',
                        ],
                        'justify' => [
                            'title' => __('Justified', 'elementor'),
                            'icon' => 'eicon-text-align-justify',
                        ],
                    ],
                    //'prefix_class' => 'elementor%s-align-',
                    'default' => '',
                ]
        );

        $element->add_responsive_control(
                'e_frontend_copy_valign',
                [
                    'label' => __('Vertical Align', 'elementor'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        '0' => [
                            'title' => __('Top', 'elementor'),
                            'icon' => 'eicon-v-align-top',
                        ],
                        '50' => [
                            'title' => __('Middle', 'elementor'),
                            'icon' => 'eicon-v-align-middle',
                        ],
                        '100' => [
                            'title' => __('Bottom', 'elementor'),
                            'icon' => 'eicon-v-align-bottom',
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .e-frontend-copy' => 'position: absolute; left: 0; width: 100%; top: {{VALUE}}%;',
                    ],
                ]
        );

        $element->add_control(
                'e_frontend_copy_hover', [
            'label' => __('Visible on Hover', 'elementor'),
            'type' => Controls_Manager::SWITCHER,
            'selectors' => [
                '{{WRAPPER}}:hover .e-frontend-copy' => 'opacity: 1;',
                '{{WRAPPER}} .e-frontend-copy' => 'opacity: 0; transition: 0.5s all;',
            ]
                ]
        );

        $element->add_control(
                'e_frontend_copy_size',
                [
                    'label' => __('Size', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'sm',
                    'options' => [
                        'xs' => __('Extra Small', 'elementor'),
                        'sm' => __('Small', 'elementor'),
                        'md' => __('Medium', 'elementor'),
                        'lg' => __('Large', 'elementor'),
                        'xl' => __('Extra Large', 'elementor'),
                    ],
                    'style_transfer' => true,
                ]
        );

        $element->add_control(
                'e_frontend_copy_icon_align',
                [
                    'label' => __('Icon Position', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'left',
                    'options' => [
                        'left' => __('Before', 'elementor'),
                        'right' => __('After', 'elementor'),
                    ],
                ]
        );

        $element->add_control(
                'e_frontend_copy_icon_indent',
                [
                    'label' => __('Icon Spacing', 'elementor'),
                    'type' => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'max' => 50,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .e-frontend-copy .elementor-button .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .e-frontend-copy .elementor-button .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
                    ],
                ]
        );

        $element->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'e_frontend_copy_typography',
                    'global' => [
                        'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                    ],
                    'selector' => '{{WRAPPER}} .e-frontend-copy .elementor-button',
                ]
        );

        $element->add_group_control(
                Group_Control_Text_Shadow::get_type(),
                [
                    'name' => 'e_frontend_copy_text_shadow',
                    'selector' => '{{WRAPPER}} .e-frontend-copy .elementor-button',
                ]
        );

        $element->start_controls_tabs('e_frontend_copy_tabs_button_style');

        $element->start_controls_tab(
                'e_frontend_copy_tab_button_normal',
                [
                    'label' => __('Normal', 'elementor'),
                ]
        );

        $element->add_control(
                'e_frontend_copy_button_text_color',
                [
                    'label' => __('Text Color', 'elementor'),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .e-frontend-copy .elementor-button' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    ],
                ]
        );

        $element->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'e_frontend_copy_background',
                    'label' => __('Background', 'elementor'),
                    'types' => ['classic', 'gradient'],
                    'exclude' => ['image'],
                    'selector' => '{{WRAPPER}} .e-frontend-copy .elementor-button',
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
                ]
        );

        $element->end_controls_tab();

        $element->start_controls_tab(
                'e_frontend_copy_tab_button_hover',
                [
                    'label' => __('Hover', 'elementor'),
                ]
        );

        $element->add_control(
                'e_frontend_copy_hover_color',
                [
                    'label' => __('Text Color', 'elementor'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .e-frontend-copy .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .e-frontend-copy .elementor-button:hover svg, {{WRAPPER}} .elementor-button:focus svg' => 'fill: {{VALUE}};',
                    ],
                ]
        );

        $element->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'e_frontend_copy_button_background_hover',
                    'label' => __('Background', 'elementor'),
                    'types' => ['classic', 'gradient'],
                    'exclude' => ['image'],
                    'selector' => '{{WRAPPER}} .e-frontend-copy .elementor-button:hover, {{WRAPPER}} .elementor-button:focus',
                    'fields_options' => [
                        'background' => [
                            'default' => 'classic',
                        ],
                    ],
                ]
        );
        
        $element->add_control(
                'e_frontend_copy_button_hover_border_color',
                [
                    'label' => __('Border Color', 'elementor'),
                    'type' => Controls_Manager::COLOR,
                    'condition' => [
                        'e_frontend_copy_border_border!' => '',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .e-frontend-copy .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'border-color: {{VALUE}};',
                    ],
                ]
        );

        $element->add_control(
                'e_frontend_copy_hover_animation',
                [
                    'label' => __('Hover Animation', 'elementor'),
                    'type' => Controls_Manager::HOVER_ANIMATION,
                ]
        );

        $element->end_controls_tab();

        $element->end_controls_tabs();

        $element->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'e_frontend_copy_border',
                    'selector' => '{{WRAPPER}} .e-frontend-copy .elementor-button',
                    'separator' => 'before',
                ]
        );

        $element->add_control(
                'e_frontend_copy_border_radius',
                [
                    'label' => __('Border Radius', 'elementor'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', '%', 'em'],
                    'selectors' => [
                        '{{WRAPPER}} .e-frontend-copy .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
        );

        $element->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'e_frontend_copy_button_box_shadow',
                    'selector' => '{{WRAPPER}} .e-frontend-copy .elementor-button',
                ]
        );

        $element->add_responsive_control(
                'e_frontend_copy_text_margin',
                [
                    'label' => __('Margin', 'elementor'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .e-frontend-copy .elementor-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' => 'before',
                ]
        );
        $element->add_responsive_control(
                'e_frontend_copy_text_padding',
                [
                    'label' => __('Padding', 'elementor'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .e-frontend-copy .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
        );


        $element->end_controls_section();
    }

    public function _before_render($element) {
        $settings = $element->get_settings();
        if (!empty($settings['e_frontend_copy'])) {
            //ob_start();
        }
    }

    public function _after_render($element, $widget = false) {
        if ($element->get_type() != 'widget' || $widget) {
            $settings = $element->get_settings();
            if (!empty($settings['e_frontend_copy'])) {
                if (empty($settings['e_frontend_visitors'])) {
                    if (!get_current_user_id()) {
                        return;
                    }
                }
                //$content = ob_get_clean();
                //ob_start();
                $this->_render($element);
                //$btn = ob_get_clean();
                //echo $content;
                wp_enqueue_script('e-addons-element-copy');
                //wp_enqueue_script('e-addons-element-copy');
                //$this->register_script('assets/lib/clipboard.js/clipboard.min.js');
                wp_enqueue_script('clipboard', home_url("/wp-includes/js/clipboard.min.js"));
                //$content = ob_get_clean();
                //echo $content;
            }
        }
    }

    public function _render_content($content, $element) {
        $settings = $element->get_settings();
        if (!empty($settings['e_frontend_copy'])) {
            ob_start();
            $this->_after_render($element, true);
            $btn = ob_get_clean();
            $tmp = explode('</div>', $content);
            $temp = array_pop($tmp); //array_pop($tmp);//array_pop($tmp);
            $tmp[] = $temp . $btn; //$tmp[] = $temp; //$tmp[] = ''; //$tmp[] = '';
            $content = implode('</div>', $tmp);
        }
        return $content;
    }

    public function _print_template($content, $element) {
        $settings = $element->get_settings();
        if (!empty($settings['e_frontend_copy'])) {
            $content .= '<div class="elementor-button-copy-wrapper elementor-button-wrapper e-frontend-copy elementor-align-center e-block">
<a class="elementor-copy-button elementor-button elementor-size-xs" href="#"><span class="elementor-button-content-wrapper"><span class="elementor-button-icon elementor-align-icon-left"><i aria-hidden="true" class="fas fa-copy"></i></span><span class="elementor-button-text">Copy</span></span></a>
<a class="elementor-download-button elementor-button elementor-size-xs" href="#"><span class="elementor-button-content-wrapper elementor-button-content-wrapper"><span class="elementor-button-icon elementor-align-icon-left elementor-button-icon elementor-align-icon-left"><i aria-hidden="true" class="fas fa-download"></i></span><span class="elementor-button-text elementor-button-text">Download</span></span></a>
</div>';
        }
        return $content; //$this->_render_content($content, $element);
    }

    /**
     * Render button widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function _render($element) {
        $settings = $element->get_settings_for_display();
        $element->add_render_attribute('wrapper', 'class', 'elementor-button-copy-wrapper');
        $element->add_render_attribute('wrapper', 'class', 'elementor-button-wrapper');
        $element->add_render_attribute('wrapper', 'class', 'e-frontend-copy');
        $element->add_render_attribute('wrapper', 'class', 'elementor-hidden');
        if (!empty($settings['e_frontend_copy_align'])) {
            $element->add_render_attribute('wrapper', 'class', 'elementor-align-' . $settings['e_frontend_copy_align']);
        }

        $element->add_render_attribute('textarea', 'class', 'e-offscreen');
        $element->add_render_attribute('textarea', 'class', 'e-clipboard-value');
        $element->add_render_attribute('textarea', 'class', 'elementor-field-textual');
        $element->add_render_attribute('textarea', 'id', 'e-clipboard-value-' . $element->get_id() . '-' . get_the_id());

        $configuration = $element->get_raw_data();
        foreach ($configuration['settings'] as $key => $setting) {
            if (substr($key, 0, 15) == 'e_frontend_copy') {
                unset($configuration['settings'][$key]);
            }
        }
        $configuration = wp_json_encode(array($configuration));

        $value = $configuration;
        ?>
        <div <?php echo $element->get_render_attribute_string('wrapper'); ?>>
            <?php
            if ($settings['e_frontend_copy_action'] == 'both') {
                $settings['e_frontend_copy_action'] = 'copy';
                $this->render_text($element, $settings);
                $settings['e_frontend_copy_action'] = 'download';
                $this->render_text($element, $settings);
            } else {
                $this->render_text($element, $settings);
            }
            ?>
            <textarea <?php echo $element->get_render_attribute_string('textarea'); ?>><?php echo $value; ?></textarea>
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
    protected function render_text($element, $settings) {

        $target = '#e-clipboard-value-' . $element->get_id() . '-' . get_the_id();

        $element->add_render_attribute('button_' . $settings['e_frontend_copy_action'], 'id', 'elementor-' . $settings['e_frontend_copy_action'] . '-button-' . $element->get_id() . '-' . get_the_id());
        $element->add_render_attribute('button_' . $settings['e_frontend_copy_action'], 'class', 'elementor-' . $settings['e_frontend_copy_action'] . '-button');
        $element->add_render_attribute('button_' . $settings['e_frontend_copy_action'], 'class', 'elementor-button');
        //$element->add_render_attribute('button', 'role', 'button');
        //$element->add_render_attribute('button_'.$settings['e_frontend_copy_action'], 'href', $target);
        $element->add_render_attribute('button_' . $settings['e_frontend_copy_action'], 'data-clipboard-target', $target);
        if (!empty($settings['e_frontend_copy_size'])) {
            $element->add_render_attribute('button_' . $settings['e_frontend_copy_action'], 'class', 'elementor-size-' . $settings['e_frontend_copy_size']);
        }
        if (!empty($settings['e_frontend_copy_hover_animation'])) {
            $element->add_render_attribute('button_' . $settings['e_frontend_copy_action'], 'class', 'elementor-animation-' . $settings['e_frontend_copy_hover_animation']);
        }

        $element->add_render_attribute([
            'content-wrapper' => [
                'class' => 'elementor-button-content-wrapper',
            ],
            'icon-align' => [
                'class' => [
                    'elementor-button-icon',
                    'elementor-align-icon-' . (!empty($settings['e_frontend_copy_icon_align']) ? $settings['e_frontend_copy_icon_align'] : 'left'),
                ],
            ],
            'text' => [
                'class' => 'elementor-button-text',
            ],
        ]);
        ?>
        <a <?php echo $element->get_render_attribute_string('button_' . $settings['e_frontend_copy_action']); ?>>
            <span <?php echo $element->get_render_attribute_string('content-wrapper'); ?>>
                <?php if (!empty($settings['e_frontend_copy_selected_icon_' . $settings['e_frontend_copy_action']]['value'])) : ?>
                    <span <?php echo $element->get_render_attribute_string('icon-align'); ?>>
                        <?php Icons_Manager::render_icon($settings['e_frontend_copy_selected_icon_' . $settings['e_frontend_copy_action']], ['aria-hidden' => 'true']); ?>
                    </span>
                <?php endif; ?>
                <?php if (!empty($settings['e_frontend_copy_text_' . $settings['e_frontend_copy_action']])) { ?>
                    <span <?php echo $element->get_render_attribute_string('text'); ?>><?php echo $settings['e_frontend_copy_text_' . $settings['e_frontend_copy_action']]; ?></span>
                <?php } ?>
            </span>
        </a>
        <?php
    }

}
