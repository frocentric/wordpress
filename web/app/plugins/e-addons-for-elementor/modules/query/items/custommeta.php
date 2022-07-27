<?php

namespace EAddonsForElementor\Modules\Query\Items;

use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Item;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Custommeta extends Base_Item {

    public function __construct() {
        parent::__construct();
        add_filter('e_addons/query/item_types', [$this, 'register']);
    }

    public function get_name() {
        return 'item_custommeta';
    }

    public function get_title() {
        return esc_html__('Custom Field', 'e-addons');
    }

    public function render($settings, $item_index, $widget) {
        $skin = $widget->skin;

        $skin->render_item_custommeta($settings, $item_index);
    }

}
