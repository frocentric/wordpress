<?php

namespace EAddonsForElementor\Modules\DynamicTags\Tags;

use Elementor\Controls_Manager;
use EAddonsForElementor\Base\Base_Tag;
use EAddonsForElementor\Core\Utils;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if ( class_exists( '\Jet_Engine' ) ) {
    
    class Jet_Gallery extends Base_Tag {
        use \EAddonsForElementor\Modules\DynamicTags\Traits\Background_Slideshow_Gallery;
    }    
    add_action( 'jet-engine/elementor-views/dynamic-tags/register', function ($dynamic_tags) {
        $namespace = '\EAddonsForElementor\Modules\DynamicTags\Tags\\';
        if ( class_exists( '\Jet_Engine' )
                && class_exists('\Jet_Engine_Options_Gallery_Tag')) {   
            class Jet_Engine_Options_Gallery_Tag_Override extends \Jet_Engine_Options_Gallery_Tag {
                use \EAddonsForElementor\Modules\DynamicTags\Traits\Background_Slideshow_Gallery;
                protected function register_controls() {
                    parent::register_controls();
                }
            }
            $class_name = $namespace.'Jet_Engine_Options_Gallery_Tag_Override';
            if (Utils::version_compare(ELEMENTOR_VERSION, '3.5.0', '<')) {    
                $dynamic_tags->register_tag($class_name);
            } else {
                $dynamic_tags->register(new $class_name());
            }
        }        
        if ( class_exists( '\Jet_Engine' )
                && class_exists('\Jet_Engine_Custom_Gallery_Tag')) { 
            class Jet_Engine_Custom_Gallery_Tag_Override extends \Jet_Engine_Custom_Gallery_Tag {                
                use \EAddonsForElementor\Modules\DynamicTags\Traits\Background_Slideshow_Gallery;
                protected function register_controls() {
                    parent::register_controls();
                }
            }          
            $class_name = $namespace.'Jet_Engine_Custom_Gallery_Tag_Override';
            if (Utils::version_compare(ELEMENTOR_VERSION, '3.5.0', '<')) {    
                $dynamic_tags->register_tag($class_name);
            } else {
                $dynamic_tags->register(new $class_name());
            }
        }
        //echo '<pre>';var_dump($dynamic_tags);echo '</pre>';die();
    } );
} else {
    class Jet_Gallery extends Base_Tag {   
        use \EAddonsForElementor\Modules\DynamicTags\Traits\Background_Slideshow_Gallery;
        public $ignore = true;
    }
}