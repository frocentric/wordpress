<?php

namespace EAddonsForElementor\Modules\Query\Items;

use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Item;
use EAddonsForElementor\Core\Utils\Query as Query_Utils;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Readmore extends Base_Item {

    public function __construct() {
        parent::__construct();
        add_filter('e_addons/query/item_types', [$this, 'register']);
    }

    public function get_name() {
        return 'item_readmore';
    }

    public function get_title() {
        return esc_html__('Read More', 'e-addons');
    }

    public function add_controls($target, $type = '') {
        if (!$type) {
            if ($target instanceof \EAddonsForElementor\Modules\Query\Base\Query) {
                $type = $target->get_querytype();
            }
        }
        
        $target->add_control(
                'readmore_text', [
            'label' => esc_html__('Text', 'e-addons'),
            //'description' => esc_html__('Separator caracters.','e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Read More', 'e-addons'),
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_readmore','item_guid'],
                    ]
                ]
            ]
                ]
        );
        $target->add_control(
                'readmore_size',
                [
                    'label' => esc_html__('Size', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'sm',
                    'options' => Query_Utils::get_button_sizes(),
                    'style_transfer' => true,
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'operator' => 'in',
                                'value' => ['item_readmore','item_guid'],
                            ]
                        ]
                    ]
                ]
        );
    }
    
    public function render($settings, $item_index, $widget) {
        $skin = $widget->skin;

        // Settings ------------------------------
        $readmore_text = $settings['readmore_text'];
        $readmore_size = $settings['readmore_size'];
        // ---------------------------------------
        $args = [
            $widget->get_querytype() => $skin->current_data,
            'block' => $skin->current_data,
        ];
        $readmore_text = Utils::get_dynamic_data($readmore_text, $args);
        $attribute_button = 'button_' . $settings['_id']; //$this->_id;

        $use_link = $skin->get_item_link($settings);

        $widget->set_render_attribute($attribute_button, 'href', $use_link);

        if (!empty($settings['blanklink_enable']))
            $widget->add_render_attribute($attribute_button, 'target', '_blank');
        //$this->parent->add_render_attribute($attribute_button, 'rel', 'nofollow');

        $widget->add_render_attribute($attribute_button, 'class', ['elementor-button-link', 'elementor-button', 'e-add-button']);
        $widget->add_render_attribute($attribute_button, 'role', 'button');

        if (!empty($readmore_size)) {
            $widget->add_render_attribute($attribute_button, 'class', 'elementor-size-' . $readmore_size);
        }


        $align_class = '';
        $devices = \Elementor\Plugin::$instance->breakpoints->get_active_devices_list();
        foreach ($devices as $device_name) {
            $device_to_replace = 'desktop' === $device_name ? '' : '-' . $device_name;
            $device_setting = 'desktop' === $device_name ? '' : '_' . $device_name;
            $value = empty($settings['item_align' . $device_setting]) ? false : $settings['item_align' . $device_setting];
            if ($value) {
                $align_class .= ' elementor' . $device_to_replace . '-align-' . $value;
            }
        }


        if (!empty($skin->current_permalink) && !is_wp_error($skin->current_permalink)) {
            ?>
            <div class="e-add-post-button<?php echo $align_class; ?>">
                <a <?php echo $widget->get_render_attribute_string($attribute_button); ?>>
                    <?php echo __($readmore_text, 'e-addons' . '_strings'); ?>
                </a>
            </div>
            <?php
        }
    }

}
