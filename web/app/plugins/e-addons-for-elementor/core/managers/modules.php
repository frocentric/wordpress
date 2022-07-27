<?php

namespace EAddonsForElementor\Core\Managers;

use EAddonsForElementor\Core\Utils;
use Elementor\Core\Base\Module as Module_Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

final class Modules {

    /**
     * @var Module_Base[]
     */
    private $modules = [];

    public function __construct() {

        $modules = $this->find_modules(E_ADDONS_PATH);
        $this->add_modules($modules);

        do_action('e_addons/modules');
    }

    public function find_modules($plugin_path = '') {

        if (substr($plugin_path, -1, 1) != DIRECTORY_SEPARATOR) {
            $plugin_path = $plugin_path . DIRECTORY_SEPARATOR;
        }

        $modules = array();
        if (is_dir($plugin_path . 'modules')) {
            $modules_path = glob($plugin_path . 'modules' . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
            foreach ($modules_path as $amodule) {
                $modules[] = basename($amodule);
            }
        }
        return $modules;
    }

    public function add_modules($modules = array(), $domain = 'EAddonsForElementor') {

        foreach ($modules as $module_name) {
            //include_once(WP_PLUGIN_DIR.'/'.Utils::camel_to_slug($domain).'/modules/'.$module_name.'/'.$module_name.'.php');
            $class_name = Utils::slug_to_camel($module_name);
            $module_class = Utils::slug_to_camel($module_name, '_');
            $full_class_name = '\\' . $domain . '\Modules\\' . $class_name . '\\' . $module_class;
            //var_dump($full_class_name);echo '<br>';
            if (class_exists($full_class_name)) {
                if ($full_class_name::is_active()) {
                    $this->modules[$module_name] = $full_class_name::instance();
                }
            } else {
                echo 'ERROR loading module: ' . $full_class_name . ' (' . WP_PLUGIN_DIR . '/' . Utils::camel_to_slug($domain) . '/modules/' . $module_name . '/' . $module_name . '.php)';
            }
        }
    }

    /**
     * @param string $module_name
     *
     * @return Module_Base|Module_Base[]
     */
    public function get_modules($module_name = '') {
        if ($module_name) {
            if (isset($this->modules[$module_name])) {
                return $this->modules[$module_name];
            }

            return null;
        }

        return $this->modules;
    }

}
