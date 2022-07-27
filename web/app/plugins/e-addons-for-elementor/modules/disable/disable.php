<?php

namespace EAddonsForElementor\Modules\Disable;

use EAddonsForElementor\Base\Module_Base;
use EAddonsForElementor\Core\Utils;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Disable extends Module_Base {

    public function __construct() {
        parent::__construct();
        
        $module = $this->get_plugin_textdomain();
        add_filter('e_addons/'.$module.'/modules/disable/widgets', [$this, 'get_elementor_widgets']); 
        
        add_action('elementor/widgets/widgets_registered', [$this, 'disable_widgets'], 99);
    }
    
    public function get_elementor_widgets($widgets) {
        $widgets = Utils::glob(ELEMENTOR_PATH.'includes'.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.'*.php');
        foreach ($widgets as $key => $widget) {
            include_once($widget);
        }
        return $widgets;
    }
    
    public function disable_widgets() {        
        $e_addons_disabled = get_option('e_addons_disabled', array());  
        $widget_manager = \Elementor\Plugin::instance()->widgets_manager;
        //$types = $widget_manager->get_widget_types(); var_dump(array_keys($types)); die();        
        if (!empty($e_addons_disabled['widgets'][$this->get_name()])) {
            foreach($e_addons_disabled['widgets'][$this->get_name()] as $widget) {
                $widget_manager->unregister_widget_type($widget);
            }
        }
    }
    
    public function get_pid() {
        return 6871;
    }

}
