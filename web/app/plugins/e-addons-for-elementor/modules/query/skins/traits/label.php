<?php

namespace EAddonsForElementor\Modules\Query\Skins\Traits;

use EAddonsForElementor\Core\Utils;

/**
 * Description of Common
 *
 * @author fra
 */
trait Label {

    public function get_item_label($item) {
        $label = !empty($item['item_text_label']) ? $item['item_text_label'] : '';
        if (empty($label)) {
            if ($item['item_type'] == 'item_custommeta') {
                $label = ucfirst($item['metafield_key']);
                $label = str_replace('-', ' ', $label);
                $label = str_replace('_', ' ', $label);
            } else {
                $label = ucfirst(str_replace('item_', '', $item['item_type']));
            }
        }
        return $label;
    }

    public function render_label_before_item($settings, $default_label = '') {
        $the_label = '';
        if (!empty($settings['use_label_before'])) {
            $label_text = $this->get_item_label($settings); //!empty($settings['item_text_label']) ? $settings['item_text_label'] : '';

            if ($default_label)
                $the_label = $default_label;
            if ($label_text)
                $the_label = $label_text;

            if ($the_label)
                $the_label = '<span class="e-add-label-before">' . $the_label . '</span>';
        }
        return $the_label;
    }

    public function render_label_after_item($settings, $default_label = '') {
        $the_label = '';
        if ($default_label)
            $the_label = $default_label;
        $the_label = !empty($settings['use_label_after']) ? $settings['use_label_after'] : $the_label;
        if ($the_label)
            $the_label = '<span class="e-add-label-after">' . $the_label . '</span>';
        return $the_label;
    }

}
