<?php

namespace EAddonsForElementor\Modules\Query\Items;

use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Item;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Mimetype extends Base_Item {
    
    public function __construct() {
        parent::__construct();
        add_filter('e_addons/query/attachment/item_types', [$this, 'register']);
    }

    public function get_name() {
        return 'item_mimetype';
    }

    public function get_title() {
        return esc_html__('Mime Type', 'e-addons');
    }

    public function render($settings, $item_index, $widget) {
        $skin = $widget->skin;
        
        if ($skin->current_data->post_mime_type){
            echo $skin->render_label_before_item($settings,'Mime Type: ');
            echo '<' . $settings['html_tag'] . '>' . $skin->current_data->post_mime_type . '</' . $settings['html_tag'] . '>';
            echo $skin->render_label_after_item($settings);
        }
    }

}
