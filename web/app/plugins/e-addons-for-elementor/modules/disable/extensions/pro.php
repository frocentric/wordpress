<?php
namespace EAddonsForElementor\Modules\Disable\Extensions;

use EAddonsForElementor\Base\Base_Extension;
use EAddonsForElementor\Core\Utils;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Pro extends Base_Extension {
    
    
    public function get_pid() {
        return 6871;
    }
    
    public function get_icon() {
        return 'eadd-enabledisabe_elementorwidgets';
    }
    
    /**
     * Get Label
     *
     * Returns the action label
     *
     * @access public
     * @return string
     */
    public function get_label() {
        return esc_html__('Disable PRO in Editor Free', 'e-addons');
    }
    
    public function __construct() {
        parent::__construct();
        if (!Utils::is_plugin_active('elementor-pro')) {
            add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueue_editor_assets']);
        }
    }

    /**
     * Enqueue admin styles
     *
     * @since 0.7.0
     *
     * @access public
     */
    public function enqueue_editor_assets() {
        wp_enqueue_style('e-addons-editor-no-pro');
    }

}
