<?php

namespace EAddonsForElementor\Modules\Query\Items;

use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Item;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Content extends Base_Item {
    
    public function __construct() {
        parent::__construct();
        add_filter('e_addons/query/post/item_types', [$this, 'register']);
    }

    public function get_name() {
        return 'item_content';
    }

    public function get_title() {
        return esc_html__('Content', 'e-addons');
    }

    public function render($settings, $item_index, $widget) {
        $skin = $widget->skin;
                
        // Settings ------------------------------
        $textcontent_limit = $settings['textcontent_limit'];
        $querytype = $widget->get_querytype();
        // ---------------------------------------
        echo '<div class="e-add-post-content">';
        // Content
        switch ($querytype) {
            case 'attachment':
                $content = $skin->current_data->post_content;
                if ($content) {
                    if ($textcontent_limit) {
                        echo substr(wp_strip_all_tags($content), 0, $textcontent_limit) . ' ...'; //
                    } else {
                        echo $content;
                    }
                }
                break;
            case 'post':
            default:
                // CONTENT
                if ($textcontent_limit) {
                    echo $skin->limit_content($textcontent_limit);
                } else {
                    echo wpautop(get_the_content());
                }
        }
        echo '</div>';
    }

}
