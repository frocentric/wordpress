<?php

namespace EAddonsDev\Modules\Php\Widgets;

use Elementor\Controls_Manager;
use EAddonsForElementor\Base\Base_Widget;
use EAddonsForElementor\Core\Utils;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Elementor PhpRaw
 *
 * Elementor widget for e-addons
 *
 */
class Pure_Php extends Base_Widget {

    /**
     * Enqueue admin styles
     *
     * @since 0.7.0
     *
     * @access public
     */
    public function enqueue_editor_assets() {
        wp_enqueue_script('e-addons-editor-php');
    }

    public function get_name() {
        return 'e-pure-php';
    }

    public function get_title() {
        return esc_html__('Pure PHP', 'e-addons');
    }

    public function get_pid() {
        return 218;
    }

    public function get_icon() {
        return 'eadd-pure-php';
    }

    protected function register_controls() {

        add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueue_editor_assets']);

        $this->start_controls_section(
                'section_pure_php', [
            'label' => esc_html__('Pure PHP', 'e-addons'),
                ]
        );

        $this->add_control(
                'custom_php',
                [
                    'label' => esc_html__('PHP Snippet', 'e-addons'),
                    'type' => Controls_Manager::CODE,
                    'language' => 'php',
                    'default' => 'echo "Hello World!";',
                ]
        );
        
        $this->add_control(
                'custom_php_editor',
                [
                    'label' => esc_html__('Execute in Editor', 'e-addons'),
                    'type' => Controls_Manager::SWITCHER,
                    'description' => esc_html__('Prevent PHP errors while writing your own custom code. Enable only if needed.', 'e-addons'),
                ]
        );

        $this->add_control(
                'custom_php_error',
                [
                    'raw' => '<strong>' . esc_html__('WARNING!', 'elementor') . '</strong> ' . esc_html__('Your PHP code appears to be in error, check it before saving, or your page will be damaged by a fatal error!', 'elementor'),
                    'type' => Controls_Manager::RAW_HTML,
                    'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning e-addons-php-error',
                ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        if ($this->get_settings_for_display('custom_php_editor') || !Utils::is_preview()) {
            $php = $this->get_settings_for_display('custom_php');        
            @eval($php);
        }
    }

}
