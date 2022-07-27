<?php

namespace EAddonsForElementor\Modules\User\Traits;

use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;

trait Users {

    public function add_source_controls() {
        
        if (in_array($this->get_name(), array('e-tag-author-field', 'e-tag-author-avatar'))) {
            $this->add_control(
                    'source',
                    [
                        'type' => Controls_Manager::HIDDEN,
                        'default' => 'author',
                    ]
            );
        } else {
            $this->add_control(
                    'source',
                    [
                        'label' => esc_html__('Source', 'elementor'),
                        'type' => Controls_Manager::SELECT,
                        'options' => [
                            '' => esc_html__('Current (Logged In)', 'e-addons'),
                            'author' => esc_html__('Author', 'e-addons'),
                            'other' => esc_html__('Other', 'e-addons'),
                        ],
                    //'label_block' => true,
                    ]
            );
        }
        
        $this->add_control(
                'user_id',
                [
                    'label' => esc_html__('User', 'elementor'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('Select other User', 'elementor'),
                    'label_block' => true,
                    'query_type' => 'users',
                    'condition' => [
                        'source' => 'other',
                    ]
                ]
        );
    }
    
    public function get_author_id() {
        global $authordata;
        if (empty($authordata->ID)) {
            $post = get_post();
            if (!empty($post)) {
                $authordata = get_userdata($post->post_author); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
            }
        }
        return get_the_author_meta('ID');
    }

    public function get_user_id() {
        $settings = $this->get_settings_for_display();
        if (empty($settings))
            return;

        $user_id = get_current_user_id();

        if (!empty($settings['source'])) {
            if ($settings['source'] == 'author') {
                $user_id = $this->get_author_id();
            }
            if ($settings['source'] == 'other') {
                if (!empty($settings['user_id'])) {
                    $user_id = $settings['user_id'];
                }
            }
        }        

        return $user_id;
    }

}
