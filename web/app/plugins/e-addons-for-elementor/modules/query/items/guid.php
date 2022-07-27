<?php

namespace EAddonsForElementor\Modules\Query\Items;

use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Item;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Guid extends Base_Item {

    public function __construct() {
        parent::__construct();
        add_filter('e_addons/query/post/item_types', [$this, 'register']);
    }

    public function get_name() {
        return 'item_guid';
    }

    public function get_title() {
        return esc_html__('Guid', 'e-addons');
    }

    public function render($settings, $item_index, $widget) {
        $skin = $widget->skin;

        // Settings ------------------------------
        $readmore_text = $settings['readmore_text'];
        $readmore_size = $settings['readmore_size'];
        // ---------------------------------------
        $attribute_button = 'button_' . $settings['_id']; //$this->_id;

        $guid = get_the_guid($skin->current_data->ID);

        if ($settings['use_link']) {
            if ($settings['use_link'] == 'yes') {
                $widget->set_render_attribute($attribute_button, 'href', $guid);
            } else {
                $use_link = $skin->get_item_link($settings);
                $widget->set_render_attribute($attribute_button, 'href', $use_link);
            }
        }

        if (!empty($settings['blanklink_enable']))
            $widget->add_render_attribute($attribute_button, 'target', '_blank');
        //$this->parent->add_render_attribute($attribute_button, 'rel', 'nofollow');

        if (!empty($readmore_text)) {
            $widget->add_render_attribute($attribute_button, 'class', ['elementor-button-link', 'elementor-button', 'e-add-button']);
            $widget->add_render_attribute($attribute_button, 'role', 'button');
            if (!empty($readmore_size)) {
                $widget->add_render_attribute($attribute_button, 'class', 'elementor-size-' . $readmore_size);
            }
        }

        if (empty($readmore_text)) {
            $readmore_text = $guid;
        }

        if (!empty($readmore_text)) {
            ?>
            <div class="e-add-post-button">
                <a <?php echo $widget->get_render_attribute_string($attribute_button); ?>>
                    <?php echo __($readmore_text, 'e-addons' . '_strings'); ?>
                </a>
            </div>
            <?php
        }
    }

}
