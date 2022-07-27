<?php

namespace EAddonsForElementor\Modules\Site\Tags;

//use Elementor\Core\DynamicTags\Tag;
use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use Elementor\Modules\DynamicTags\Module;
use EAddonsForElementor\Base\Base_Tag;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Image extends Base_Tag {

    public $is_data = true;

    public function get_name() {
        return 'e-tag-site-image';
    }

    public function get_icon() {
        return 'eadd-dynamic-tag-options-site-image';
    }
    
    public function get_pid() {
        return 7717;
    }
    
    public function get_title() {
        return esc_html__('Site Image', 'e-addons');
    }

    public function get_group() {
        return 'site';
    }
    public static function _group() {
        return self::_groups('site');
    }

    public function get_categories() {
        return [
            //'base', //\Elementor\Modules\DynamicTags\Module::BASE_GROUP
            'image', //\Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY,
        ];
    }

    /**
     * @since 2.0.0
     * @access protected
     */
    protected function register_advanced_section() {
        
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
                'logo',
                [
                    'label' => esc_html__('Use Logo', 'elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'yes',
                ]
        );

        $this->add_control(
                'custom_image',
                [
                    'label' => esc_html__('Custom Image', 'elementor'),
                    'type' => 'e-query',
                    'select2options' => ['tags' => true],
                    'placeholder' => esc_html__('Option key', 'elementor'),
                    'label_block' => true,
                    'query_type' => 'options',
                    'condition' => [
                        'logo' => '',
                    ]
                ]
        );

        Utils::add_help_control($this);
    }

    public function get_value(array $options = []) {
        $settings = $this->get_settings();
        if (empty($settings))
            return;

        $id = '';
        $url = '';
        if ($settings['logo']) {                
            $id = get_theme_mod( 'custom_logo' );            
            if ($id) {
                $src = wp_get_attachment_image_src($id, 'full');
                $url = reset($src);
            }
        } else {
            // custom field
            $meta = get_option($settings['custom_image']);
            //var_dump($meta);
            $img = Utils::get_image($meta);
            if (!Utils::empty($img) && !empty($img['url'])) {
                if (!empty($img['id'])) {
                    $id = $img['id'];
                }
                $url = $img['url'];
            }
        }

        return [
            'id' => $id,
            'url' => $url,
        ];
    }

}
