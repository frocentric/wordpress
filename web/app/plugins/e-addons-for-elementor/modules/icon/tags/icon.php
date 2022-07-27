<?php

namespace EAddonsForElementor\Modules\Icon\Tags;

//use Elementor\Core\DynamicTags\Tag;
use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Tag;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Icon extends Base_Tag {

    public function get_name() {
        return 'e-tag-icon-field';
    }
    
    public function get_icon() {
        return 'eadd-dynamic-tag-icon';
    }
    
    public function get_pid() {
        return 14918;
    }
    
    public function get_title() {
        return __('Icon Text', 'e-addons');
    }

    public function get_categories() {
        return [
            'base', //\Elementor\Modules\DynamicTags\Module::BASE_GROUP
            'text', //\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
        ];
    }

    /**
     * Register Controls
     *
     * Registers the Dynamic tag controls
     *
     * @since 2.0.0
     * @access protected
     *
     * @return void
     */
    protected function register_controls() {

        $this->add_control(
                'icon',
                [
                    'label' => __('Icon', 'elementor'),
                    'type' => Controls_Manager::ICONS,
                    //'fa4compatibility' => 'icon',
                    'default' => [
                        'value' => 'fas fa-star',
                        'library' => 'fa-solid',
                    ],
                ]
        );
        /*
        $this->add_responsive_control(
                'size',
                [
                        'label' => esc_html__( 'Size', 'elementor' ),
                        'type' => Controls_Manager::SLIDER,
                        'range' => [
                                'px' => [
                                        'min' => 6,
                                        'max' => 300,
                                ],
                        ],
                        'selectors' => [
                                '{{WRAPPER}} .e-add-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                        ],
                ]
        );
        */
        Utils::add_help_control($this);
    }

    public function render() {
        $settings = $this->get_settings();
        if (empty($settings['icon']))
            return;
        ob_start();
        \Elementor\Icons_Manager::render_icon( $settings['icon'], [
						'aria-hidden' => 'true',
					] );
        $icon = ob_get_clean();
        $icon = str_replace('class="', 'class="e-add-icon ', $icon);
        echo $icon;
        \Elementor\Icons_Manager::enqueue_shim();
    }

}
