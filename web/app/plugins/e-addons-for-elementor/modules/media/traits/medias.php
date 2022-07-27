<?php

namespace EAddonsForElementor\Modules\Media\Traits;

use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;

trait Medias {
    
    public function add_source_controls() {
        $this->add_control(
                'source',
                [
                    'label' => esc_html__('Source', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => esc_html__('Featured Image', 'e-addons'),
                        'meta_post' => esc_html__('Post Meta Field', 'e-addons'),
                        'meta_user' => esc_html__('User Meta Field', 'e-addons'),
                        'meta_author' => esc_html__('Author Meta Field', 'e-addons'),
                        'site_option' => esc_html__('Site Option', 'e-addons'),
                        'other' => esc_html__('Other', 'e-addons'),
                    ],
                //'label_block' => true,
                ]
        );
        
        $this->add_control(
                'meta_post_name',
                [
                    'label' => esc_html__('Post Meta field', 'elementor'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('Select Post Meta field', 'elementor'),
                    'label_block' => true,
                    'query_type' => 'metas',
                    'object_type' => 'post',
                    'condition' => [
                        'source' => 'meta_post',
                    ]
                ]
        );
        $this->add_control(
                'meta_user_name',
                [
                    'label' => esc_html__('User Meta field', 'elementor'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('Select User Meta field', 'elementor'),
                    'label_block' => true,
                    'query_type' => 'metas',
                    'object_type' => 'user',
                    'condition' => [
                        'source' => ['meta_user', 'meta_author'],
                    ]
                ]
        );
        $this->add_control(
                'option_name',
                [
                    'label' => esc_html__('Site Option field', 'elementor'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('Select Site Option', 'elementor'),
                    'description' => esc_html__('Leave empty for Site Logo', 'elementor'),
                    'label_block' => true,
                    'query_type' => 'options',
                    'condition' => [
                        'source' => ['site_option'],
                    ]
                ]
        );
        
        $this->add_control(
                'media_id',
                [
                    'label' => esc_html__('Media', 'elementor'),
                    'type' => 'file',
                    'placeholder' => esc_html__('Select other Media', 'elementor'),
                    'condition' => [
                        'source' => 'other',
                    ]
                ]
        );

    }

    public function get_media_id() {
        $settings = $this->get_settings_for_display();
        if (empty($settings))
            return;

        global $post;
        
        $media_id = get_post_thumbnail_id();
        if (!empty($post) && $post->post_type == 'attachment') {
            $media_id = $post->ID;
        }
        $queried_object = get_queried_object();
        if (!empty($queried_object) && get_class($queried_object) == 'WP_Post' && $queried_object->post_type == 'attachment') {
            $media_id = $queried_object->ID;
        }
        
        if ($settings['source']) {
            switch ($settings['source']) {

                case 'site_option':
                    if (!empty($settings['option_name'])) {
                        return get_option($settings['option_name']);
                    }
                    return get_theme_mod( 'custom_logo' );
                    
                case 'meta_post':
                    return get_post_meta(get_the_ID(), $settings['meta_post_name'], true);
                    
                case 'meta_user':
                    $user_id = get_current_user_id();
                case 'meta_author':
                    if (empty($user_id)) {
                        $user_id = get_the_author_meta('ID');
                    }
                    return get_user_meta($user_id, $settings['meta_user_name'], true);

                case 'other':
                    if ($settings['media_id']) {
                        return $settings['media_id'];
                    }
                    break;

                default:
                    // media page
                    return get_the_ID();
                    
            }
        }

        return $media_id;
    }
}
