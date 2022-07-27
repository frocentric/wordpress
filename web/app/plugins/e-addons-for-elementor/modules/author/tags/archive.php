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

class Archive extends Base_Tag {
    
    use \EAddonsForElementor\Modules\User\Traits\Users;

    public function get_name() {
        return 'e-tag-author-archive';
    }
    
    public function get_icon() {
        return 'eadd-dynamic-tag-author-archiveurl';
    }
    
    public function get_pid() {
        return 7457;
    }

    public function get_title() {
        return esc_html__('Author Posts Archive URL', 'e-addons');
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
            //'text', //\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
            'url', //\Elementor\Modules\DynamicTags\Module::URL_CATEGORY
        ];
    }

    public function render() {
        $settings = $this->get_settings();
        if (empty($settings))
            return;

        $user_id = $this->get_author_id();
        $url = get_author_posts_url($user_id);
        echo $url;
    }

}
