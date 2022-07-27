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

class Field extends \EAddonsForElementor\Modules\User\Tags\Field {

    public function get_name() {
        return 'e-tag-author-field';
    }

    public function get_icon() {
        return 'eadd-dynamic-tag-author-fields';
    }
    
    public function get_pid() {
        return 7457;
    }

    public function get_title() {
        return esc_html__('Author Field', 'e-addons');
    }

    public function get_group() {
        return 'author';
    }
    public static function _group() {
        return self::_groups('author');
    }

}
