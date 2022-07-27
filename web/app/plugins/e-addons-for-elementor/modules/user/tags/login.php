<?php

namespace EAddonsForElementor\Modules\User\Tags;

//use Elementor\Core\DynamicTags\Tag;
use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use Elementor\Modules\DynamicTags\Module;
use EAddonsForElementor\Base\Base_Tag;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Login extends Base_Tag {

    public function get_name() {
        return 'e-tag-user-login';
    }
    
    public function get_icon() {
        return 'eadd-dynamic-tag-user-loginurl';
    }
    
    public function get_pid() {
        return 7450;
    }

    public function get_title() {
        return esc_html__('User Login URL', 'e-addons');
    }
    
    public function get_group() {
        return 'user';
    }
    public static function _group() {
        return self::_groups('user');
    }
    
    public function get_categories() {
        return [
            'base', //\Elementor\Modules\DynamicTags\Module::BASE_GROUP
            //'text', //\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
            'url', //\Elementor\Modules\DynamicTags\Module::URL_CATEGORY
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
                'redirect',
                [
                    'label' => esc_html__('Redirect', 'elementor'),
                    'type' => Controls_Manager::URL,
                    'dynamic' => [
                        'active' => true,
                    ],
                    'placeholder' => esc_html__('https://your-link.com', 'elementor'),
                    'default' => [
                        'url' => home_url(),
                    ],
                ]
        );

        Utils::add_help_control($this);
    }

    public function render() {
        $settings = $this->get_settings();
        if (empty($settings))
            return;

        $redirect = get_permalink();
        if (!empty($settings['redirect']['url'])) {
            $redirect = $settings['redirect']['url'];
        }
        
        $link = esc_url( wp_login_url($redirect) );

        echo $link;
    }

}
