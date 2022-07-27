<?php

namespace EAddonsForElementor\Modules\Query\Items;

use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Item;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Index extends Base_Item {

    public function __construct() {
        parent::__construct();
        add_filter('e_addons/query/item_types', [$this, 'register']);
    }

    public function get_name() {
        return 'item_index';
    }

    public function get_title() {
        return esc_html__('Loop Index', 'e-addons');
    }

    public function render($settings, $item_index, $widget) {
        $skin = $widget->skin;

        $use_link = $skin->get_item_link($settings);

        if ($use_link) {
            $attribute_link = ' href="' . $use_link . '"';

            $attribute_target = '';
            if (!empty($settings['blanklink_enable']))
                $attribute_target = ' target="_blank"';

            echo '<a' . $attribute_link . $attribute_target . ' class="e-add-index">';
        } else {
            echo '<div class="e-add-index">';
        }
        echo $skin->render_label_before_item($settings);
        echo $skin->index;
        echo $skin->render_label_after_item($settings);
        if ($use_link) {
            echo '</a>';
        } else {
            echo '</div>';
        }
    }

}
