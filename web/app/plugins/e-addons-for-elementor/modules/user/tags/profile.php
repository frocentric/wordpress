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

class Profile extends Base_Tag {

    public function get_name() {
        return 'e-tag-user-profile';
    }

    public function get_icon() {
        return 'eadd-dynamic-tag-user-profileurl';
    }
    
    public function get_pid() {
        return 7450;
    }
    
    public function get_title() {
        return esc_html__('User Profile URL', 'e-addons');
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

    public function render() {
        $settings = $this->get_settings();
        if (empty($settings))
            return;

        $link = get_edit_profile_url();

        echo $link;
    }

}
