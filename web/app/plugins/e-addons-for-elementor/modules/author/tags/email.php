<?php

namespace EAddonsForElementor\Modules\Author\Tags;

//use Elementor\Core\DynamicTags\Tag;
use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use Elementor\Modules\DynamicTags\Module;
use EAddonsForElementor\Base\Base_Tag;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Email extends Base_Tag {

    public function get_name() {
        return 'e-tag-author-email';
    }
    
    public function get_icon() {
        return 'eadd-dynamic-tag-author-email';
    }
    
    public function get_pid() {
        return 7457;
    }

    public function get_title() {
        return esc_html__('Author Email', 'e-addons');
    }
    
    public function get_group() {
        return 'author';
    }
    public static function _group() {
        return self::_groups('author');
    }
    
    public function get_categories() {
        return [
            'base', //\Elementor\Modules\DynamicTags\Module::BASE_GROUP
            'text', //\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
            'url', //\Elementor\Modules\DynamicTags\Module::URL_CATEGORY
        ];
    }

    public function render() {
        $settings = $this->get_settings();
        if (empty($settings))
            return;

        $mail = get_the_author_meta('user_email');
        echo $mail;
    }

}
