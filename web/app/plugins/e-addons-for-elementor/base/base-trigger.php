<?php

namespace EAddonsForElementor\Base;

use Elementor\Element_Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

abstract class Base_Trigger extends Element_Base {

    public $parent;

    use \EAddonsForElementor\Base\Traits\Base;

    public function __construct($display = false) {
        parent::__construct();
        if ($display) {
            $this->parent = $display;
        }
    }
    
    public function get_pid() {
        return 210;
    }
    
    /*public function get_name() {
        $class_name = get_class($this);
        var_dump($class_name); die();
    }*/
    
    public function get_trigger_controls($element) {

    }

    public function is_triggered($element, $settings) {

    }

    public function print_trigger_scripts($element, $settings = array()) {

    }
    
    public function has_triggered($trigger) {
        return isset($this->parent->triggered[$trigger]);
    }

    public function add_triggered($trigger) {
        //$control = $this->parent->get_control($trigger);
        $this->parent->triggered[$trigger] = $trigger; //$control['label'];
    }

    public function add_conditions($trigger) {
        //$control = $this->parent->get_control($trigger);
        $this->parent->conditions[$trigger] = $trigger; //$control['label'];
    }

}
