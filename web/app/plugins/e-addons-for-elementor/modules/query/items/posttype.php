<?php

namespace EAddonsForElementor\Modules\Query\Items;

use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Item;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Posttype extends Base_Item {

    public function __construct() {
        parent::__construct();
        add_filter('e_addons/query/post/item_types', [$this, 'register']);
        add_action('e_addons/query/item/content/controls', [$this, 'add_content_controls'], 10, 2);
    }

    public function get_name() {
        return 'item_posttype';
    }

    public function get_title() {
        return esc_html__('Post Type', 'e-addons');
    }

    public function add_content_controls($widget, $target) {
        if ($widget instanceof \EAddonsForElementor\Modules\Query\Widgets\Query_Posts) {
            $target->add_control(
                    'posttype_label', [
                'label' => esc_html__('Post Type Label ', 'e-addons'),
                'type' => Controls_Manager::SELECT,
                'default' => 'plural',
                'options' => [
                    'plural' => esc_html__('Plural', 'e-addons'),
                    'singular' => esc_html__('Singular', 'e-addons'),
                ],
                'condition' => [
                    'item_type' => $this->get_name(),
                ],
                    ]
            );
        }
    }

    public function render($settings, $item_index, $widget) {
        $skin = $widget->skin;

        $posttype_label = $settings['posttype_label'];
        $type = get_post_type();
        //
        switch ($posttype_label) {
            case 'plural' :
                $posttype = get_post_type_object($type)->labels->name;
                break;
            case 'singular' :
            default:
                $posttype = get_post_type_object($type)->labels->singular_name;
                break;
        }
        if (!empty($posttype)) {
            //@p label before
            echo $skin->render_label_before_item($settings, 'Type: ');
            echo '<div class="e-add-post-ptype">';
            echo $posttype;
            echo '</div>';
            echo $skin->render_label_after_item($settings);
        }
    }

}
