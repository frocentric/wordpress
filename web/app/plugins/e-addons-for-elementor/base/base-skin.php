<?php

namespace EAddonsForElementor\Base;

use Elementor\Element_Base;
use Elementor\Widget_Base;
use Elementor\Group_Control_Image_Size;
use EAddonsForElementor\Core\Utils;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Base_Skin extends \Elementor\Skin_Base {

    use \EAddonsForElementor\Base\Traits\Base;

    //public static $instance;

    /**
     * Skin base constructor.
     *
     * Initializing the skin base class by setting parent widget and registering
     * controls actions.
     *
     * @since 1.0.0
     * @access public
     * @param Widget_Base $parent
     */
    public function __construct($parent = []) {
        if (!empty($parent)) {
            parent::__construct($parent);
        }
        //var_dump(get_class($this));
        //self::$instance = $this;
    }

    public function get_id() {
        return 'e-skin';
    }

    public function _enqueue_scripts() {
        $scripts = $this->get_script_depends();
        if (!empty($scripts)) {
            wp_enqueue_script('elementor-frontend');
            foreach ($scripts as $script) {
                wp_enqueue_script($script);
            }
        }
    }

    public function _enqueue_styles() {
        $styles = $this->get_style_depends();
        if (!empty($styles)) {
            foreach ($styles as $style) {
                wp_enqueue_style($style);
            }
        }
    }

    public function _print_styles() {
        $styles = $this->get_style_depends();
        if (!empty($styles)) {
            foreach ($styles as $style) {
                wp_print_styles(array($style));
            }
        }
    }

    public function _print_scripts() {
        $scripts = $this->get_script_depends();
        if (!empty($scripts)) {
            foreach ($scripts as $script) {
                wp_print_scripts(array($script));
            }
        }
    }

    public function preview_enqueue() {
        // @p Se mi trovo in editor li sparo tutti
        $this->_enqueue_styles();
        $this->_enqueue_scripts();
    }

    public function enqueue() {
        // @p Se mi trovo in frontend uso solo quelli che servono in base allo skin
        if (!empty($this->parent->get_settings('_skin')) && $this->get_id() == $this->parent->get_settings('_skin')) {
            $this->_enqueue_styles();
            $this->_enqueue_scripts();
        }
    }

    public function print_assets() {
        $this->_print_styles();
        $this->_print_scripts();
    }

    public function render() {
        
    }

    public function register_controls(\Elementor\Widget_Base $widget) {
        
    }

    public function register_style_sections(\Elementor\Widget_Base $widget) {
        
    }

}
