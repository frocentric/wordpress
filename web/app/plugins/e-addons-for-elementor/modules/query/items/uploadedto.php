<?php

namespace EAddonsForElementor\Modules\Query\Items;

use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Item;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Uploadedto extends Base_Item {
    
    public function __construct() {
        parent::__construct();
        add_filter('e_addons/query/attachment/item_types', [$this, 'register']);
    }

    public function get_name() {
        return 'item_uploadedto';
    }

    public function get_title() {
        return esc_html__('Uploaded to', 'e-addons');
    }

    public function render($settings, $item_index, $widget) {
        $skin = $widget->skin;
        
        if ($skin->current_data->post_parent){
            echo $skin->render_label_before_item($settings,'Uploaded to: ');
            echo '<' . $settings['html_tag'] . '>' . get_the_title($skin->current_data->post_parent) . '</' . $settings['html_tag'] . '>';
            echo $skin->render_label_after_item($settings);
        }
    }

}
