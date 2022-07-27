<?php

namespace EAddonsForElementor\Modules\DynamicTags;

use EAddonsForElementor\Core\Utils;
use Elementor\Controls_Manager;
use EAddonsForElementor\Base\Module_Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Dynamic_Tags extends Module_Base {
    
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
        wp_enqueue_style('e-addons-editor-dynamic-tags');
    }

}
