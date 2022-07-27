<?php

namespace EAddonsForElementor\Base;

use EAddonsForElementor\Core\Utils;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

abstract class Base_Widget extends Widget_Base {

    use \EAddonsForElementor\Base\Traits\Base;
    
    //public static $widgets = [];
    
    public function __construct($data = [], $args = null) {
        
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueue_editor_assets']);
        $this->add_actions();
        
        parent::__construct($data, $args);
        //self::$widgets[] = get_class($this);
    }
    
    public function add_actions() {
        
    }
    
    /**
     * Enqueue admin styles in Editor
     *
     * @access public
     */
    public function enqueue_editor_assets() {
        
    }

    public function get_categories() {
        $plugin = $this->get_plugin_name();
        $tmp = explode('-', $plugin, 3);
        if (count($tmp) > 2) {
            if (end($tmp) == 'for-elementor') {
                return ['e-addons'];
            }
            return [end($tmp)];
        }
        return [$plugin];
    }

    /**
     * Register widget skins - deprecated prefixed method
     *
     * @since 1.7.12
     * @access protected
     * @deprecated 3.1.0
     */
    //protected function _register_skins() {
        //Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.1.0', __CLASS__ . '::register_skins()' );
    //    $this->register_skins();
    //}

    /**
     * Register widget skins.
     *
     * This method is activated while initializing the widget base class. It is
     * used to assign skins to widgets with `add_skin()` method.
     *
     * Usage:
     *
     *    protected function register_skins() {
     *        $this->add_skin( new Skin_Classic( $this ) );
     *    }
     *
     * @since 3.1.0
     * @access protected
     */
    //protected function register_skins() {}

}
