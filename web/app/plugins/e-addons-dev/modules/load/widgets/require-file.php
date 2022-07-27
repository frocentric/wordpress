<?php

namespace EAddonsDev\Modules\Load\Widgets;

use Elementor\Controls_Manager;
use EAddonsForElementor\Base\Base_Widget;
use EAddonsForElementor\Core\Utils;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Elementor Do Shortcode
 *
 * Elementor widget for e-addons
 *
 */
class Require_File extends Base_Widget {

    public function get_name() {
        return 'e-include-file';
    }

    public function get_title() {
        return esc_html__('Require File', 'e-addons');
    }

    public function get_pid() {
        return 219;
    }

    public function get_icon() {
        return 'eadd-include-file';
    }

    protected function register_controls() {
        $this->start_controls_section(
                'section_includefile', [
            'label' => esc_html__('Require File', 'e-addons'),
                ]
        );

        $this->add_control(
                'file', [
            'label' => esc_html__('File Path', 'e-addons'),
            'description' => esc_html__('The path to the file to include (ex: folder/file.html). It start from the Root folder.', 'e-addons'),
            'placeholder' => 'Ex: "wp-content/themes/hello-elementor/my-file.php"',
            'type' => 'e-query',
            'query_type' => 'files',
            'label_block' => true,
                ]
        );
        
        $this->add_control(
                'once', [
            'label' => esc_html__('Once', 'e-addons'),
            'query_type' => Controls_Manager::SWITCHER,
                ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        if (!empty($settings['file'])) {
            $file_path = ABSPATH . $settings['file'];
            if (file_exists($file_path)) {
                if (empty($settings['once'])) {
                    include($file_path);
                } else {
                    include_once($file_path);
                }
            }
        }
        
    }

}
