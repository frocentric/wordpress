<?php
namespace EAddonsForElementor\Modules\DynamicTags\Tags;

use Elementor\Controls_Manager;
use EAddonsForElementor\Base\Base_Tag;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if ( class_exists( '\acf' ) && function_exists( 'acf_get_field_groups' )
        && class_exists('\ElementorPro\Modules\DynamicTags\ACF\Tags\ACF_Gallery')) {
    class ACF_Gallery extends \ElementorPro\Modules\DynamicTags\ACF\Tags\ACF_Gallery {        
        use \EAddonsForElementor\Modules\DynamicTags\Traits\Background_Slideshow_Gallery;
    }
} else {
    class ACF_Gallery extends Base_Tag {
        use \EAddonsForElementor\Modules\DynamicTags\Traits\Background_Slideshow_Gallery;
        public $ignore = true;
    }
}