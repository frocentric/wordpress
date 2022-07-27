<?php

namespace EAddonsForElementor\Base;

use Elementor\Element_Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Base_Shortcode extends Element_Base {

    use \EAddonsForElementor\Base\Traits\Base;

    public function get_name() {
        return 'e-addons-shortcode';
    }

    /**
     * Get widget icon.
     *
     * Retrieve the widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eicon-apps';
    }

    public function do_shortcode($atts) {
        return false;
    }

}
