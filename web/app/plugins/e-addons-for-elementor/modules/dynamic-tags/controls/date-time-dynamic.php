<?php

namespace EAddonsForElementor\Modules\DynamicTags\Controls;

use \Elementor\Modules\DynamicTags\Module as TagsModule;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor date/time control.
 *
 * A base control for creating date time control. Displays a date/time picker
 * based on the Flatpickr library @see https://chmln.github.io/flatpickr/ .
 *
 * @since 1.0.0
 */
class Date_Time_Dynamic extends \Elementor\Control_Date_Time {

    use \EAddonsForElementor\Base\Traits\Base;

    public function get_icon() {
        return 'eadd-dynamic-tag-datetime';
    }

    public function get_pid() {
        return 7750; // 1302;
    }
    
    /**
     * Get date time control default settings.
     *
     * Retrieve the default settings of the date time control. Used to return the
     * default settings while initializing the date time control.
     *
     * @since 1.8.0
     * @access protected
     *
     * @return array Control default settings.
     */
    protected function get_default_settings() {
        return [
            'label_block' => true,
            'picker_options' => [],
            'dynamic' => [
                'active' => true,
                'categories' => [
                    TagsModule::BASE_GROUP,
                    TagsModule::TEXT_CATEGORY,
                ],
            ],
        ];
    }

    /**
     * Render date time control output in the editor.
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
        $template = str_replace('elementor-date-time-picker', 'elementor-date-time-picker elementor-control-tag-area', $template);
        echo $template;
    }

}
