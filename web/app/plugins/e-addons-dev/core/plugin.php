<?php
namespace EAddonsDev;

use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Plugin as Plugin_Core;

/**
 * Main Plugin Class
 *
 * Register new elementor Extension
 *
 * @since 1.0.1
 */
class Plugin extends Plugin_Core {

    /**
     * Constructor
     *
     * @since 0.0.1
     *
     * @access public
     */
    public function __construct() {
        parent::__construct();
    }

    public function setup_hooks() {
        
        // fire actions
        add_action('e_addons/init', [$this, 'on_e_addons_init']);
        
    }

    /**
     * Add Actions
     *
     * @since 0.0.1
     *
     * @access private
     */
    public function on_e_addons_init() {
        $modules_manager = Plugin_Core::$instance->modules_manager;
        $plugin_path = Utils::get_plugin_path(__FILE__);
        
        $modules = $modules_manager->find_modules($plugin_path);
        $modules_manager->add_modules($modules, __NAMESPACE__);
        
    }
    

}
