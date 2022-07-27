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

class Field extends Base_Tag {

    public function get_name() {
        return 'e-tag-site-field';
    }

    public function get_icon() {
        return 'eadd-dynamic-tag-options-site-field';
    }
    
    public function get_pid() {
        return 7717;
    }

    public function get_title() {
        return esc_html__('Site Field', 'e-addons');
    }

    public function get_group() {
        return 'site';
    }
    public static function _group() {
        return self::_groups('site');
    }

    /*
    public function get_categories() {
        return [
            'base', //\Elementor\Modules\DynamicTags\Module::BASE_GROUP
            'text', //\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
            'url', //\Elementor\Modules\DynamicTags\Module::URL_CATEGORY
        ];
    }
     * 
     */

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
                'tag_field',
                [
                    'label' => esc_html__('Option', 'elementor'),
                    'type' => 'e-query',
                    'select2options' => ['tags' => true],
                    'placeholder' => esc_html__('Options key', 'elementor'),
                    'label_block' => true,
                    'query_type' => 'options',
                    'description' => esc_html__('Search global value from the Options', 'elementor').'<br><a target="_blank" href="'.admin_url('options.php').'">'.esc_html__('See all Options', 'elementor').'</a>',
                ]
        );

        Utils::add_help_control($this);
    }

    public function render() {
        $settings = $this->get_settings_for_display();
        if (empty($settings))
            return;

        if (!empty($settings['tag_field'])) {
            $opt = get_option($settings['tag_field']);
            echo Utils::to_string($opt);
        }

        
    }

}
