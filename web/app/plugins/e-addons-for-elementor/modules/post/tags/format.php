<?php

namespace EAddonsForElementor\Modules\Post\Tags;

//use Elementor\Core\DynamicTags\Tag;
use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use Elementor\Modules\DynamicTags\Module;
use EAddonsForElementor\Base\Base_Tag;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Format extends Base_Tag {

    public function get_name() {
        return 'e-tag-post-format';
    }

    public function get_icon() {
        return 'eadd-dynamic-tag-post-format';
    }

    public function get_pid() {
        return 7461;
    }

    public function get_title() {
        return esc_html__('Post Format', 'e-addons');
    }

    public function get_group() {
        return 'post';
    }
    public static function _group() {
        return self::_groups('post');
    }

    public function get_categories() {
        return [
            'base', //\Elementor\Modules\DynamicTags\Module::BASE_GROUP
            'text', //\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
        ];
    }

    public function render() {
        $settings = $this->get_settings_for_display();
        if (empty($settings))
            return;

        $post_id = get_the_ID();

        if ($post_id) {
            $post_format = get_post_format($post_id);
            if ($post_format) {
                $meta = get_post_format_string($post_format);
                echo Utils::to_string($meta);
            }
        }
    }

}
