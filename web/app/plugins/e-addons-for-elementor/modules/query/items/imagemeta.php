<?php

namespace EAddonsForElementor\Modules\Query\Items;

use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Item;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Imagemeta extends Base_Item {
    
    public function __construct() {
        parent::__construct();
        add_filter('e_addons/query/attachment/item_types', [$this, 'register']);
    }

    public function get_name() {
        return 'item_imagemeta';
    }

    public function get_title() {
        return esc_html__('Image Meta', 'e-addons');
    }

    public function render($settings, $item_index, $widget) {
        $skin = $widget->skin;
        
        //var_dump(wp_get_attachment_metadata($this->current_data->ID)['image_meta']);
        //var_dump(wp_get_attachment_metadata($this->current_data->ID));
        $metadata = wp_get_attachment_metadata($skin->current_data->ID);
        $sizeim = $settings['imagemedia_sizes'];
        $metas = $settings['imagemedia_metas'];

        if (!empty($metas)){
            echo $skin->render_label_before_item($settings,'Image meta: ');
            foreach ($metas as $m) {
                echo '<div class="e-add-imagemeta e-add-imagemeta-' . $m . '">';
                if ($m == 'dimension') {
                    if ($sizeim == 'full') {
                        echo $metadata['width'] . 'px x ' . $metadata['height'] . 'px';
                    } else {
                        echo $metadata['sizes'][$sizeim]['width'] . 'px x ' . $metadata['sizes'][$sizeim]['height'] . 'px';
                    }
                }
                if ($m == 'file') {
                    if ($sizeim == 'full') {
                        echo $metadata['file'];
                    } else {
                        echo $metadata['sizes'][$sizeim]['file'];
                    }
                }
                //@p todo: EXIF
                /*
                  ["image_meta"]=>
                  array(12) {
                  ["aperture"] => string(1) "0"
                  ["credit"] => string(0) ""
                  ["camera"] => string(0) ""
                  ["caption"] => string(0) ""
                  ["created_timestamp"] => string(1) "0"
                  ["copyright"] => string(0) ""
                  ["focal_length"] => string(1) "0"
                  ["iso"] => string(1) "0"
                  ["shutter_speed"] => string(1) "0"
                  ["title"] => string(0) ""
                  ["orientation"] => string(1) "0"
                  ["keywords"] => array(0) {
                  }
                  }
                 */
                echo '</div>';
            }
            echo $skin->render_label_after_item($settings);
        }

        //https://developer.wordpress.org/reference/functions/wp_get_attachment_metadata/
    }

}
