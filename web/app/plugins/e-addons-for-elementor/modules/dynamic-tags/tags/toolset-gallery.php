<?php

namespace EAddonsForElementor\Modules\DynamicTags\Tags;

use Elementor\Controls_Manager;
use EAddonsForElementor\Base\Base_Tag;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if ( function_exists( 'wpcf_admin_fields_get_groups' )
        && class_exists('\ElementorPro\Modules\DynamicTags\Toolset\Tags\Toolset_Gallery')) {
    class Toolset_Gallery extends \ElementorPro\Modules\DynamicTags\Toolset\Tags\Toolset_Gallery {
        use \EAddonsForElementor\Modules\DynamicTags\Traits\Background_Slideshow_Gallery;
    }
} else {
    class Toolset_Gallery extends Base_Tag {
        use \EAddonsForElementor\Modules\DynamicTags\Traits\Background_Slideshow_Gallery;
        public $ignore = true;
    }
}