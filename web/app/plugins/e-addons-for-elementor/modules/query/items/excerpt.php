<?php

namespace EAddonsForElementor\Modules\Query\Items;

use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Item;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Excerpt extends Base_Item {
    
    public function __construct() {
        parent::__construct();
        add_filter('e_addons/query/post/item_types', [$this, 'register']);
    }

    public function get_name() {
        return 'item_excerpt';
    }

    public function get_title() {
        return esc_html__('Excerpt', 'e-addons');
    }

    public function render($settings, $item_index, $widget) {
        $skin = $widget->skin;
                
        // Settings ------------------------------
        $textcontent_limit = $settings['textcontent_limit'];
        $querytype = $widget->get_querytype();
        // ---------------------------------------
        echo '<div class="e-add-post-excerpt">';
        // Content
        switch ($querytype) {
            case 'post':
            default:
                $post = get_post();
                $post_excerpt = $post->post_excerpt;
                if ($textcontent_limit) {
                    $post_excerpt = $skin->limit_text($post_excerpt, $textcontent_limit);
                }
                echo wpautop($post_excerpt); //$this->limit_excerpt( $settings['textcontent_limit'] ); //

                /*
                  // Da valutare se fare così...
                  add_filter( 'excerpt_more', [ $this, 'filter_excerpt_more' ], 20 );
                  add_filter( 'excerpt_length', [ $this, 'filter_excerpt_length' ], 20 );

                  ?>

                  <?php the_excerpt(); ?>

                  <?php

                  remove_filter( 'excerpt_length', [ $this, 'filter_excerpt_length' ], 20 );
                  remove_filter( 'excerpt_more', [ $this, 'filter_excerpt_more' ], 20 );
                 */
        }
        echo '</div>';
    }

}
