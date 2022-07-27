<?php

namespace EAddonsForElementor\Modules\CopyPaste\Extensions;

use EAddonsForElementor\Base\Base_Extension;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Clipboard_Copy_Paste extends Base_Extension {
    
    public function get_pid() {
        return 209;
    }
    
    public function get_icon() {
        return 'eadd-copy-fromto';
    }
    
    public function __construct() {
        parent::__construct();
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueue_editor_assets']);
    }

    /**
     * Enqueue admin styles
     *
     * @since 0.7.0
     *
     * @access public
     */
    public function enqueue_editor_assets() {
        wp_enqueue_style('e-addons-editor-copy');
        wp_enqueue_script('e-addons-editor-copy');
    }

}
