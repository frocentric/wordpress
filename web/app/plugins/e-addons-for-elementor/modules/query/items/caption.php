<?php

namespace EAddonsForElementor\Modules\Query\Items;

use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Item;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Caption extends Base_Item {
    
    public function __construct() {
        parent::__construct();
        add_filter('e_addons/query/attachment/item_types', [$this, 'register']);
    }

    public function get_name() {
        return 'item_caption';
    }

    public function get_title() {
        return esc_html__('Caption', 'e-addons');
    }

    public function render($settings, $item_index, $widget) {
        $skin = $widget->skin;
        
        if (!empty($skin->current_data->post_excerpt)) {
            echo $skin->render_label_before_item($settings,'Caption: '); 
            echo '<' . $settings['html_tag'] . '>' . $skin->current_data->post_excerpt . '</' . $settings['html_tag'] . '>';
            echo $skin->render_label_after_item($settings);
        }
    }

}
