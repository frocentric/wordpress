<?php

namespace EAddonsForElementor\Modules\Query\Items;

use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Item;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Template extends Base_Item {

    public function __construct() {
        parent::__construct();
        add_filter('e_addons/query/item_types', [$this, 'register']);
    }

    public function get_name() {
        return 'item_template';
    }

    public function get_title() {
        return esc_html__('Template', 'e-addons');
    }

    public function add_controls($target, $type = '') {
        if (!$type) {
            if ($target instanceof \EAddonsForElementor\Modules\Query\Base\Query) {
                $type = $target->get_querytype();
            }
        }
        //
        $target->add_control(
                'template_item_id',
                [
                    'label' => esc_html__('Template', 'e-addons'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('Search Template', 'e-addons'),
                    'label_block' => true,
                    'query_type' => 'posts',
                    'render_type' => 'template',
                    'object_type' => 'elementor_library',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'value' => $this->get_name(),
                            ]
                        ]
                    ]
                ]
        );
    }

    public function render($settings, $item_index, $widget) {
        $skin = $widget->skin;

        $item_template_id = $settings['template_item_id'];
        if (!empty($item_template_id))
            $skin->render_e_template($item_template_id);
    }

}
