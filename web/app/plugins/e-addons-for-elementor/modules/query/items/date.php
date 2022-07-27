<?php

namespace EAddonsForElementor\Modules\Query\Items;

use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Item;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Date extends Base_Item {

    public function __construct() {
        parent::__construct();
        add_filter('e_addons/query/post/item_types', [$this, 'register']);
        add_filter('e_addons/query/term/item_types', [$this, 'register']);
        add_filter('e_addons/query/user/item_types', [$this, 'register']);
        add_filter('e_addons/query/items/item_types', [$this, 'register']);
        add_filter('e_addons/query/repeater/item_types', [$this, 'register']);
    }

    public function get_name() {
        return 'item_date';
    }

    public function get_title() {
        return esc_html__('Date', 'e-addons');
    }

    public function render($settings, $i, $widget) {
        $skin = $widget->skin;

        $querytype = $widget->get_querytype();
        // Settings ------------------------------
        $date_format = $settings['date_format'];
        $icon_enable = $settings['icon_enable'];
        // ---------------------------------------
        if (empty($date_format)) {
            $date_format = get_option('date_format');
        }
        $icon = '';
        if (!empty($icon_enable)) {
            $icon = '<i class="e-add-icon e-add-query-icon fas fa-calendar" aria-hidden="true"></i> ';
        }
        $date = '';
        switch ($querytype) {
            case 'attachment':
                $date = get_the_date($date_format, '');
                break;
            case 'product':
            case 'post':
                $date_type = $settings['date_type'];
                //se mi trovo in post
                switch ($date_type) {
                    case 'modified' :
                        $date = get_the_modified_date($date_format, '');

                        break;

                    case 'publish' :
                    default:
                        $date = get_the_date($date_format, '');

                        break;
                }
                break;
            case 'user':
                $date = $skin->current_data->user_registered;
                $date = date($date_format, strtotime($date));
                break;
            case 'items':
                //se mi trovo in item_list
                //$date = $this->current_data['sl_date'];
                if (!empty($skin->current_data['sl_date'])) {
                    $date = date_create($skin->current_data['sl_date']);
                    $date = date_format($date, $date_format);
                }
                break;
            case 'repeater':
                //se mi trovo in repeater
                //$date = $this->current_data['sl_date'];

                if (!empty($skin->current_data['item_date_' . $i])) {
                    $date = date_create($skin->current_data['item_date_' . $i]);
                    $date = date_format($date, $date_format);
                }
                break;
        }

        if (!empty($date)) {
            $html_tag = !empty($settings['html_tag']) ? $settings['html_tag'] : 'div';
            //@p label before
            echo $skin->render_label_before_item($settings, 'Date: ');
            echo '<' . $html_tag . ' class="e-add-post-date">' . $icon . $date . '</' . $html_tag . '>';
            echo $skin->render_label_after_item($settings);
        }
        ?>
        <?php
    }

}
