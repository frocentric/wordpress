<?php

namespace EAddonsForElementor\Modules\Term\Tags;

//use Elementor\Core\DynamicTags\Tag;
use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use Elementor\Modules\DynamicTags\Module;
use EAddonsForElementor\Base\Base_Tag;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Archive extends Base_Tag {

    public function get_name() {
        return 'e-tag-term-archive';
    }

    public function get_icon() {
        return 'eadd-dynamic-tag-term-archiveurl';
    }

    public function get_pid() {
        return 7459;
    }

    public function get_title() {
        return esc_html__('Term Posts Archive URL', 'e-addons');
    }
    
    public function get_group() {
        return 'term';
    }
    public static function _group() {
        return self::_groups('term');
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

        $term_id = $this->get_module()->get_term_id();
        if ($term_id) {
            $url = get_term_link($term_id);
            echo $url;
        }
        
    }

}
