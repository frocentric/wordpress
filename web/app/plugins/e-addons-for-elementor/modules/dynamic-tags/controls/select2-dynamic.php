<?php

namespace EAddonsForElementor\Modules\DynamicTags\Controls;

use \Elementor\Modules\DynamicTags\Module as TagsModule;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor select control.
 *
 * A base control for creating select control. Displays a simple select box.
 * It accepts an array in which the `key` is the option value and the `value` is
 * the option name.
 *
 * @since 1.0.0
 */
class Select2_Dynamic extends \Elementor\Control_Select2 {

    use \EAddonsForElementor\Base\Traits\Base;

    public function get_icon() {
        return 'eicon-select';
    }

    public function get_pid() {
        return 1302;
    }
    
    /**
	 * Get select2 control default settings.
	 *
	 * Retrieve the default settings of the select2 control. Used to return the
	 * default settings while initializing the select2 control.
	 *
	 * @since 1.8.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return [
			'options' => [],
                        'dynamic' => [
                            'active' => true,
                            'categories' => [
                                TagsModule::BASE_GROUP,
                                TagsModule::TEXT_CATEGORY,
                            ],
                        ],
			'multiple' => false,
			// Select2 library options
			'select2options' => [],
			// the lockedOptions array can be passed option keys. The passed option keys will be non-deletable.
			'lockedOptions' => [],
		];
	}

    /**
     * Render select control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     * @since 1.0.0
     * @access public
     */
    public function content_template() {
        ob_start();
        parent::content_template();
        $template = ob_get_clean();
        $template = str_replace('elementor-control-input-wrapper', 'elementor-control-input-wrapper elementor-control-dynamic-switcher-wrapper', $template);
        $template = str_replace('<select ', '<select class="elementor-control-tag-area" ', $template);
        echo $template;
    }

}
