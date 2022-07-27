<?php

namespace EAddonsForElementor\Modules\Query\Items;

use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Item;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Alternativetext extends Base_Item {
    
    public function __construct() {
        parent::__construct();
        add_filter('e_addons/query/attachment/item_types', [$this, 'register']);
    }

    public function get_name() {
        return 'item_alternativetext';
    }

    public function get_title() {
        return esc_html__('Alternative Text', 'e-addons');
    }

    public function render($settings, $item_index, $widget) {
        $skin = $widget->skin;
        
        $alt = get_post_meta(get_the_ID(), '_wp_attachment_image_alt', TRUE);
        if ($alt){
            echo $skin->render_label_before_item($settings,'Alt: ');
            echo '<' . $settings['html_tag'] . '>' . $alt . '</' . $settings['html_tag'] . '>';
            echo $skin->render_label_after_item($settings);
        }
    }

}
