<?php

namespace EAddonsForElementor\Modules\Query\Items;

use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Item;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Author extends Base_Item {

    public function __construct() {
        parent::__construct();
        add_filter('e_addons/query/post/item_types', [$this, 'register']);
        add_action('e_addons/query/item/content/controls', [$this, 'add_content_controls'], 10, 2);
    }

    public function get_name() {
        return 'item_author';
    }

    public function get_title() {
        return esc_html__('Author', 'e-addons');
    }

    public function add_content_controls($widget, $target) {
        if ($widget instanceof \EAddonsForElementor\Modules\Query\Widgets\Query_Posts) {
            $target->add_control(
                    'author_displayname', [
                'label' => esc_html__('Show Name', 'e-addons'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'item_type' => $this->get_name(),
                ]
                    ]
            );
            $target->add_control(
                    'author_bio', [
                'label' => esc_html__('Show biography', 'e-addons'),
                'type' => Controls_Manager::SWITCHER,
                'condition' => [
                    'item_type' => $this->get_name(),
                ]
                    ]
            );
            $target->add_control(
                    'author_image', [
                'label' => esc_html__('Show Avatar', 'e-addons'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'item_type' => $this->get_name(),
                ]
                    ]
            );
            $target->add_control(
                    'author_image_size', [
                'label' => esc_html__('Avatar size', 'e-addons'),
                'type' => Controls_Manager::NUMBER,
                'default' => '50',
                'render_type' => 'template',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'item_type',
                            'value' => $this->get_name(),
                        ],
                        [
                            'name' => 'author_image',
                            'value' => 'yes',
                        ]
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-author-image .e-add-img' => 'width: {{VALUE}}px; height: {{VALUE}}px;',
                ]
                    ]
            );
            $target->add_control(
                    'author_user_key',
                    [
                        'label' => esc_html__('Custom Field', 'e-addons'),
                        'type' => 'e-query',
                        'placeholder' => esc_html__('Search User Custom Field', 'e-addons'),
                        'label_block' => true,
                        'multiple' => true,
                        'separator' => 'after',
                        'query_type' => 'fields',
                        'object_type' => 'user',
                        'condition' => [
                            'item_type' => $this->get_name(),
                        ]
                    ]
            );

            $target->add_control(
                    'author_link', [
                'label' => esc_html__('Link to Author archive', 'e-addons'),
                'type' => Controls_Manager::SWITCHER,
                'condition' => [
                    'item_type' => $this->get_name(),
                ]
                    ]
            );
        }
    }

    public function render($settings, $item_index, $widget) {
        $skin = $widget->skin;

        // Settings ------------------------------
        $avatar_image_size = $settings['author_image_size'];
        $use_link = $skin->get_item_link($settings);
        $blanklink = $settings['blanklink_enable'];

        $author_user_key = array();
        if (!empty($settings['author_user_key']))
            $author_user_key = $settings['author_user_key'];
        // ---------------------------------------
        if ($settings['author_image'])
            array_unshift($author_user_key, 'avatar');
        if ($settings['author_displayname'])
            array_push($author_user_key, 'display_name');
        if ($settings['author_bio'])
            array_push($author_user_key, 'description');
        // ---------------------------------------
        $author = [];

        $avatar_args['size'] = $avatar_image_size;

        $post_id = $skin->current_id;
        $author_id = get_post_field( 'post_author', $post_id );
        //$author_id = get_the_author_meta('ID');
        
        $author['avatar'] = get_avatar_url($author_id, $avatar_args);
        $author['posts_url'] = get_author_posts_url($author_id);

        $author_link = '<a href="' . $author['posts_url'] . '">';

        if (!empty($author_user_key)) {
            echo '<div class="e-add-post-author">';
            foreach ($author_user_key as $akey => $author_value) {
                if ($author_value == 'avatar') {
                    ?>
                    <div class="e-add-author-image">
                        <div class="e-add-author-avatar">
                            <?php echo (!empty($settings['author_link'])) ? $author_link : ''; ?>
                            <img class="e-add-img" src="<?php echo $author['avatar']; ?>" alt="<?php echo get_the_author_meta('display_name'); ?>" />
                            <?php echo (!empty($settings['author_link'])) ? '</a>' : ''; ?>
                        </div>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="e-add-author-text">
                        <?php
                        echo '<div class="e-add-author-' . $author_value . '">';
                        echo (!empty($settings['author_link'])) ? $author_link : '';
                        echo Utils::to_string(get_the_author_meta($author_value));
                        echo (!empty($settings['author_link'])) ? '</a>' : '';
                        echo '</div>';
                        ?>
                    </div>
                    <?php
                }
            }
            echo '</div>';
        }
    }

}
