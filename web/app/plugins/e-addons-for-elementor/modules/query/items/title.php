<?php

namespace EAddonsForElementor\Modules\Query\Items;

use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Item;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Title extends Base_Item {

    public function __construct() {
        parent::__construct();
        add_filter('e_addons/query/post/item_types', [$this, 'register']);
        add_filter('e_addons/query/term/item_types', [$this, 'register']);
        add_filter('e_addons/query/items/item_types', [$this, 'register']);
        add_filter('e_addons/query/repeater/item_types', [$this, 'register']);
    }

    public function get_name() {
        return 'item_title';
    }

    public function get_title() {
        return esc_html__('Title', 'e-addons');
    }

    public function render($settings, $i, $widget) {
        $skin = $widget->skin;

        // Settings ------------------------------
        $html_tag = !empty($settings['html_tag']) ? $settings['html_tag'] : 'h3';
        //
        $use_link = $skin->get_item_link($settings);
        $querytype = $widget->get_querytype();
        // ---------------------------------------
        echo sprintf('<%1$s class="e-add-post-title">', $html_tag);

        
        

        switch ($querytype) {
            case 'attachment':
            case 'product':
            case 'post':
                //se mi trovo in post
                $title = get_the_title() ? get_the_title() : get_the_ID();
                break;
            case 'term':
                //se mi trovo in term
                $term_info = $skin->current_data;
                $title = $term_info->name;
                break;
            case 'items':
                //se mi trovo in item_list
                $title = $skin->current_data['sl_title'];
                break;
            case 'repeater':
                //se mi trovo in item_list
                // ..... echo $this->current_data['sl_title'];
                if (!empty($skin->current_data['item_title_' . $i]))
                    $title = $skin->current_data['item_title_' . $i];
                //echo $settings['item_type'].' - '.$i;
                //echo $settings['item_type'].' - '.$this->counter.' - '.$this->itemindex;
                break;
        }
        
        if ($use_link) {
            $attribute_link = ' href="' . $use_link . '"';

            $attribute_target = '';
            if (!empty($settings['blanklink_enable']))
                $attribute_target = ' target="_blank"';

            $atitle = str_replace('"', "''", $title);
            echo '<a' . $attribute_link . $attribute_target . ' title="'.$atitle.'">';
        }
        echo $title;
        if ($use_link) {
            echo '</a>';
        }
        ?>
        <?php
        echo sprintf('</%s>', $html_tag);
        ?>
        <?php
    }

}
