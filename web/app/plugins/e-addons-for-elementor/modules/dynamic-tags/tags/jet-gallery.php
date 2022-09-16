<?php

namespace EAddonsForElementor\Modules\DynamicTags\Tags;

use Elementor\Controls_Manager;
use EAddonsForElementor\Base\Base_Tag;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if ( class_exists( '\Jet_Engine' ) ) {
    
    class Jet_Gallery extends Base_Tag {
        use \EAddonsForElementor\Modules\DynamicTags\Traits\Background_Slideshow_Gallery;
    }    
    add_action( 'jet-engine/elementor-views/dynamic-tags/register', function ($dynamic_tags) {
        //var_dump($dynamic_tags);die();  
        if ( class_exists( '\Jet_Engine' )
                && class_exists('\Jet_Engine_Options_Gallery_Tag')) {   
            class Jet_Engine_Options_Gallery_Tag_Override extends \Jet_Engine_Options_Gallery_Tag {
                use \EAddonsForElementor\Modules\DynamicTags\Traits\Background_Slideshow_Gallery;
                protected function register_controls() {
                    parent::register_controls();
                }
            }
            $dynamic_tags->register(new Jet_Engine_Options_Gallery_Tag_Override());
        }        
        if ( class_exists( '\Jet_Engine' )
                && class_exists('\Jet_Engine_Custom_Gallery_Tag')) { 
            class Jet_Engine_Custom_Gallery_Tag_Override extends \Jet_Engine_Custom_Gallery_Tag {                
                use \EAddonsForElementor\Modules\DynamicTags\Traits\Background_Slideshow_Gallery;
                protected function register_controls() {
                    parent::register_controls();
                }
            }            
            $dynamic_tags->register(new Jet_Engine_Custom_Gallery_Tag_Override());
        }
        //echo '<pre>';var_dump($dynamic_tags);echo '</pre>';die();
    } );
} else {
    class Jet_Gallery extends Base_Tag {   
        use \EAddonsForElementor\Modules\DynamicTags\Traits\Background_Slideshow_Gallery;
        public $ignore = true;
    }
}