<?php

namespace EAddonsForElementor\Core\Managers;

use EAddonsForElementor\Core\Utils;
use Elementor\Core\Base\Module as Module_Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

final class Controls {

    /**
     * @var Controls[]
     */
    public $controls = [];

    public function __construct() {
        add_action('elementor/controls/controls_registered', [$this, 'init_controls']);
    }

    public function get_controls() {
        $elements = array();
        $path = E_ADDONS_PATH . 'core' . DIRECTORY_SEPARATOR . 'controls' . DIRECTORY_SEPARATOR;
        if (is_dir($path)) {
            $files = Utils::glob($path . '*.php');
            //$files = array_filter(glob(DIRECTORY_SEPARATOR."*"), 'is_file');
            foreach ($files as $ele) {
                $file = basename($ele);
                $name = pathinfo($file, PATHINFO_FILENAME);
                $elements[] = Utils::slug_to_camel($name, '_');
            }
        }
        return $elements;
    }
    public function get_groups() {
        $elements = array();
        $path = E_ADDONS_PATH . 'core' . DIRECTORY_SEPARATOR . 'controls' . DIRECTORY_SEPARATOR. 'groups' . DIRECTORY_SEPARATOR;
        if (is_dir($path)) {
            $files = Utils::glob($path . '*.php');
            foreach ($files as $ele) {
                $file = basename($ele);
                $name = pathinfo($file, PATHINFO_FILENAME);
                $elements[] = Utils::slug_to_camel($name, '_');
            }
        }
        return $elements;
    }

    public function init_controls() {
        $controls_manager = \Elementor\Plugin::$instance->controls_manager;
        $controls = $this->get_controls();
        
        $tmp = explode('\\', __NAMESPACE__);
        array_pop($tmp);
        $namespace = implode('\\', $tmp);
        
        foreach ($controls as $control) {
            $class_name = $namespace.'\\Controls\\' . $control;
            $control_obj = new $class_name();
            if (version_compare(ELEMENTOR_VERSION, '3.5.0', '<')) {
                $controls_manager->register_control($control_obj->get_type(), $control_obj);
            } else {
                $controls_manager->register($control_obj);//, $control_obj->get_type());
            }
            $this->controls[$control_obj->get_type()] = $control_obj;
        }
        
        foreach ($this->get_groups() as $group) {
            $class_name = $namespace.'\\Controls\Groups\\' . $group;
            $control_obj = new $class_name();
            $controls_manager->add_group_control($control_obj->get_type(), $control_obj);
        }
        
        \EAddonsForElementor\Plugin::instance()->assets_manager->register_core_assets(); // TODO: check why this is needed!
        do_action('e_addons/controls');
    }

}
