<?php
namespace EAddonsForElementor\Modules\Translate\Translations;

/**
 * Class WPML_Elementor_Query
 */
class Wpml_Section extends \WPML_Elementor_Module_With_Items {

    /**
     * @return string
     */
    public function get_items_field() {
        return 'accordionsection_label';
    }

    /**
     * @return array
     */
    public function get_fields() {
        return array(
            'accordionsection_sections',
        );
    }

    /**
     * @param string $field
     *
     * @return string
     */
    protected function get_title($field) {
        switch ($field) {
            case 'accordionsection_sections':
                return esc_html__('Accordion Section: Section Labels', 'wpml-string-translation');

            default:
                return '';
        }
    }

    /**
     * @param string $field
     *
     * @return string
     */
    protected function get_editor_type($field) {
        switch ($field) {
            default:
                return 'LINE';

        }
    }

}
