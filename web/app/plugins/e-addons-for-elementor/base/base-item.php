<?php

namespace EAddonsForElementor\Base;

use Elementor\Element_Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

abstract class Base_Item /*extends Element_Base*/ {

    use \EAddonsForElementor\Base\Traits\Base;

    public function __construct() {
        //parent::__construct();
        
        add_action( "e_addons/query/render_item/".$this->get_name(), [ $this, 'render' ], 10, 3 );
        add_action( "e_addons/query/item_controls/".$this->get_name(), [ $this, 'add_controls' ], 10, 2);
    }
    
    public function render($item, $key, $widget) {}
    
    public function add_controls($widget, $type = '') {}
    
    public function register($field_types) {
        $field_types[$this->get_name()] = $this->get_title();
        asort($field_types);
        return $field_types;
    }
    
    public function get_key() {
        return str_replace('item_', '', $this->get_name());
    }
    
    public function get_title() {
        return ucwords($this->get_key());
    }

}
