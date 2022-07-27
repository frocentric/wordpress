<?php

namespace EAddonsForElementor\Modules\Site\Tags;

use Elementor\Controls_Manager;
use EAddonsForElementor\Base\Base_Tag;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if ( class_exists('\ElementorPro\Modules\DynamicTags\Tags\Request_Parameter')) {
    class Request_Parameter extends \ElementorPro\Modules\DynamicTags\Tags\Request_Parameter {
        use \EAddonsForElementor\Base\Traits\Base;
        public function get_categories() {
            return [
                'text',
                'post_meta',
                'base',
                'number',
                'color',
            ];
	}
    }
} else {
    class Request_Parameter extends Base_Tag {
        public $ignore = true;
    }
}