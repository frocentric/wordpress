<?php

namespace EAddonsForElementor\Modules\DynamicTags\Tags;

use Elementor\Controls_Manager;
use EAddonsForElementor\Base\Base_Tag;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if ( function_exists( 'pods' )
        && class_exists('\ElementorPro\Modules\DynamicTags\Pods\Tags\Pods_Gallery')) {
    class Pods_Gallery extends \ElementorPro\Modules\DynamicTags\Pods\Tags\Pods_Gallery {
        use \EAddonsForElementor\Modules\DynamicTags\Traits\Background_Slideshow_Gallery;
    }
} else {
    class Pods_Gallery extends Base_Tag {
        use \EAddonsForElementor\Modules\DynamicTags\Traits\Background_Slideshow_Gallery;
        public $ignore = true;
    }
}