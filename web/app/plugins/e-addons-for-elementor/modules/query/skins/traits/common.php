<?php

namespace EAddonsForElementor\Modules\Query\Skins\Traits;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;
use Elementor\Core\Breakpoints\Manager as Breakpoints_Manager;
use Elementor\Icons_Manager;
use EAddonsForElementor\Core\Utils;

/**
 * Description of Common
 *
 * @author fra
 */
trait Common {

    public function render_item_image($settings, $item_index = '') {
        do_action('e_addons/query/render_item/item_image', $settings, $item_index, $this->parent);
    }
    
    // per gestire l'icona 
    public function render_item_icon($metaitem, $icon5_key, $icon4_key, $class_icon = '') {

        $querytype = $this->parent->get_querytype();
        if ($querytype == 'items') {
            $ic = $this->current_data['sl_icon'];
            $metaitem[$icon5_key] = $ic;
        }

        $migrated = isset($metaitem['__fa4_migrated'][$icon5_key]);
        $is_new = empty($metaitem[$icon4_key]) && Icons_Manager::is_migration_allowed();
        //
        if (!empty($metaitem[$icon4_key]) || !empty($metaitem[$icon5_key]['value'])) {
            ob_start();
            if ($is_new || $migrated) {
                Icons_Manager::render_icon($metaitem[$icon5_key], ['aria-hidden' => 'true', 'class' => $class_icon]);
            } else {
                $class_icon = $class_icon ? $class_icon . ' ' : '';
                ?>
                <i class="e-add-icon <?php echo $class_icon . esc_attr($metaitem[$icon4_key]); ?>" aria-hidden="true"></i>
                <?php
            }
            return ob_get_clean();
        }
        return '';
    }
    
    public function get_dynamic_data($content, $widget = null) {
        if (!$widget) {
            $widget = $this->parent;
        }
        $args = [
            $widget->get_querytype() => $this->current_data,
            'block' => $this->current_data,
        ];
        if ($widget->get_querytype() == 'product') {
            $args['post'] = get_post($this->current_id);
        }
        if ($widget->get_querytype() == 'user') {
            $args['author'] = $this->current_data;
        }
        return Utils::get_dynamic_data($content, $args);
    }

}
