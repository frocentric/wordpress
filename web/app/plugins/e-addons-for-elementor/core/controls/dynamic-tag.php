<?php

namespace EAddonsForElementor\Core\Controls;

use \Elementor\Modules\DynamicTags\Module as TagsModule;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * FileSelect control.
 *
 * A control for selecting any type of files.
 *
 * @since 1.0.0
 */
class Dynamic_Tag extends \Elementor\Control_Base_Multiple {

    use Traits\Base;
    
    const CONTROL_TYPE = 'd-tag';

    /**
     * Get control type.
     *
     * Retrieve the control type, in this case `FILESELECT`.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Control type.
     */
    public function get_type() {
        return self::CONTROL_TYPE;
    }

    /**
     * Get default settings.
     *
     * @since 1.0.0
     * @access protected
     *
     * @return array Control default settings.
     */
    protected function get_default_settings() {
        return [
            'label_block' => true,
            'dynamic' => [
                'active' => true,
                'categories' => [
                    TagsModule::BASE_GROUP,
                    TagsModule::TEXT_CATEGORY,
                ],
                'returnType' => 'object',
            ],
        ];
    }

    /**
     * Render control output in the editor.
     *
     * @since 1.0.0
     * @access public
     */
    public function content_template() {
        $control_uid = $this->get_control_uid();
        ?>
        <div class="elementor-control-field">
            <# if ( data.label ) {#>
            <label for="<?php echo esc_attr($control_uid); ?>" class="elementor-control-title">{{{ data.label }}}</label>
            <# } #>
            <div class="elementor-control-input-wrapper elementor-control-dynamic-switcher-wrapper elementor-control-unit-5">
                <div class="elementor-hidden elementor-control-tag-area e-dynamic-tag" id="<?php echo esc_attr($control_uid); ?>" data-setting="{{ data.name }}">Use Dynamic Tag</div>
            </div>
        </div>
        <# if ( data.description ) { #>
        <div class="elementor-control-field-description">{{{ data.description }}}</div>
        <# } #>
        <?php
    }

}
