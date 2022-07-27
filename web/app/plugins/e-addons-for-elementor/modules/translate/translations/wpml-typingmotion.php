<?php
namespace EAddonsForElementor\Modules\Translate\Translations;

/**
 * Class WPML_Elementor_Query
 */
class Wpml_Typingmotion extends \WPML_Elementor_Module_With_Items {

    /**
     * @return string
     */
    public function get_items_field() {
        return 'texts_sequence';
    }

    /**
     * @return array
     */
    public function get_fields() {
        return array(
            'text_word',
        );
    }

    /**
     * @param string $field
     *
     * @return string
     */
    protected function get_title($field) {
        switch ($field) {
            case 'text_word':
                return esc_html__('Typing Motion: Sequence of Texts', 'wpml-string-translation');

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
