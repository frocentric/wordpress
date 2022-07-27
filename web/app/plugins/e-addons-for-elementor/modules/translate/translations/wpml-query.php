<?php
namespace EAddonsForElementor\Modules\Translate\Translations;

/**
 * Class WPML_Elementor_Query
 */
class Wpml_Query extends \WPML_Elementor_Module_With_Items {
    
    /*
    public function __construct() {
        echo 'wpml query'; die();
    }
    */

    /**
     * @return string
     */
    public function get_items_field() {
        return 'list_items';
    }

    /**
     * @return array
     */
    public function get_fields() {
        return array(
            'readmore_text',
            'item_text_label',
            'use_label_after',
            'use_fallback',
        );
    }

    /**
     * @param string $field
     *
     * @return string
     */
    protected function get_title($field) {
        switch ($field) {
            case 'readmore_text':
                return esc_html__('Query: Read More button', 'wpml-string-translation');

            case 'item_text_label':
                return esc_html__('Query: Item Label Before', 'wpml-string-translation');

            case 'use_label_after':
                return esc_html__('Query: Item Label After', 'wpml-string-translation');
            
            case 'use_fallback':
                return esc_html__('Query: Item Fallback', 'wpml-string-translation');

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
            case 'readmore_text':         
            default:
                return 'LINE';

        }
    }

}
