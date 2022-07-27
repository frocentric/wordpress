<?php

namespace EAddonsDev\Modules\External\Widgets;

use Elementor\Controls_Manager;
use EAddonsForElementor\Base\Base_Widget;
use EAddonsForElementor\Core\Utils;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Elementor Iframe
 *
 * Elementor widget for e-addons
 *
 */
class Iframe extends Base_Widget {

    public function get_name() {
        return 'e-iframe';
    }

    public function get_title() {
        return esc_html__('Iframe', 'e-addons-for-elementor');
    }

    public function get_pid() {
        return 617;
    }
    
    public function get_icon() {
        return 'eadd-remote-iframe';
    }

    protected function register_controls() {
        $this->start_controls_section(
                'section_iframe', [
            'label' => esc_html__('Iframe', 'e-addons-for-elementor'),
                ]
        );

        $this->add_control(
                'url', [
            'label' => esc_html__('Iframe SRC', 'e-addons-for-elementor'),
            'description' => esc_html__('Insert a full URL to use as src of the Iframe', 'e-addons-for-elementor'),
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'https://e-addons.com',
            'label_block' => true,
                ]
        );

        $this->add_control(
                'iframe_doc', [
            'label' => esc_html__('Use Google Document preview', 'e-addons-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'description' => esc_html__('Render any Document like PDF, DOC, XLS and many other', 'e-addons-for-elementor'),
            'condition' => [
                'url!' => '',
            ],
                ]
        );

        $this->add_responsive_control(
            'iframe_height',
            [
                'label' => esc_html__('Height', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '100',
                    'unit' => 'vh',
                ],
                'size_units' => ['px', '%', 'vh'],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 1600,
                        'step' => 1,
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} iframe' => 'min-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        if (!empty($settings['url'])) {
            if ($settings['iframe_doc']) {
                $settings['url'] = 'https://docs.google.com/viewer?embedded=true&url=' . urlencode($settings['url']);
            }
            ?>
            <iframe frameborder="0" width="100%" src="<?php echo $settings['url']; ?>"></iframe>
            <?php
        }
    }

}
