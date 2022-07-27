<?php

namespace EAddonsDev\Modules\Shortcode\Widgets;

use Elementor\Controls_Manager;
use EAddonsForElementor\Base\Base_Widget;
use EAddonsForElementor\Core\Utils;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Elementor Do Shortcode widget.
 *
 * Elementor widget that insert any shortcodes into the page.
 *
 * @since 1.0.0
 * Extend Elementor Shortcode Widget
 * /elementor/includes/widgets/shortcode.php
 */
class Do_Shortcode extends Base_Widget {

    /**
     * Get widget name.
     *
     * Retrieve shortcode widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'e-do-shortcode';
    }

    /**
     * Get widget title.
     *
     * Retrieve shortcode widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__('Do Shortcode', 'e-addons-for-elementor');
    }

    public function get_pid() {
        return 220;
    }

    /**
     * Get widget icon.
     *
     * Retrieve shortcode widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eadd-do-shortcode';
    }

    /**
     * Show in panel.
     *
     * Whether to show the widget in the panel or not. By default returns true.
     *
     * @since 1.0.0
     * @access public
     *
     * @return bool Whether to show the widget in the panel or not.
     */
    public function show_in_panel() {
        return false;
    }

    /**
     * Register shortcode widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 3.1.0
     * @access protected
     */
    protected function register_controls() {
        $this->start_controls_section(
                'section_doshortcode',
                [
                    'label' => esc_html__('Do Shortcode', 'elementor'),
                ]
        );

        $this->add_control(
                'doshortcode_string',
                [
                    'label' => esc_html__('Enter your shortcode', 'elementor'),
                    'type' => Controls_Manager::TEXTAREA,
                    'dynamic' => [
                        'active' => true,
                    ],
                    'placeholder' => '[gallery id="123" size="medium"]',
                    'default' => '',
                ]
        );

        $this->end_controls_section();
    }

    /**
     * Render shortcode widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render() {
        $shortcode = $this->get_settings_for_display('doshortcode_string');

        $shortcode = do_shortcode(shortcode_unautop($shortcode));
        ?>
        <div class="elementor-shortcode"><?php echo $shortcode; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped    ?></div>
        <?php
    }

}
