<?php

namespace EAddonsForElementor\Modules\Post\Traits;

use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;

trait Posts {
    
    public function add_source_controls() {
        $this->add_control(
                'source',
                [
                    'label' => esc_html__('Source', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => esc_html__('Current', 'e-addons'),
                        'parent' => esc_html__('Parent', 'e-addons'),
                        'root' => esc_html__('Root', 'e-addons'),
                        'previous' => esc_html__('Previous', 'e-addons'),
                        'next' => esc_html__('Next', 'e-addons'),
                        'other' => esc_html__('Other', 'e-addons'),
                    ],
                //'label_block' => true,
                ]
        );
        
        /*
        $this->add_control(
                'from_relationship',
                [
                    'label' => esc_html__('From Relationship', 'elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'condition' => [                        
                        'source' => ['previous', 'next'],
                    ]
                ]
        );
        $this->add_control(
                'from_relationship_field',
                [
                    'label' => esc_html__('Relationship field', 'elementor'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('Select Relationship field', 'elementor'),
                    'label_block' => true,
                    'query_type' => 'metas',
                    'object_type' => 'post',
                    'condition' => [
                        'from_relationship!' => '',
                        'source' => ['previous', 'next'],
                    ]
                ]
        );
        $this->add_group_control(
                \EAddonsForElementor\Core\Controls\Groups\Sourcetype::get_type(),
            [
                'name' => 'from_relationship_field_source',
                'label' => esc_html__('Select from other source', 'e-addons'),                
            ]
        );
         */
        
        
        $this->add_control(
                'post_id',
                [
                    'label' => esc_html__('Post', 'elementor'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('Select other Post', 'elementor'),
                    'label_block' => true,
                    'query_type' => 'posts',
                    'condition' => [
                        'source' => 'other',
                    ]
                ]
        );

        $this->add_control(
                'excluded_terms',
                [
                    'label' => esc_html__('Excluded Terms', 'elementor'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('Select excluded Terms', 'elementor'),
                    'label_block' => true,
                    'query_type' => 'terms',
                    'multiple' => true,
                    'separator' => 'before',
                    'condition' => [
                        //'from_relationship' => '',
                        'source' => ['previous', 'next'],
                    ]
                ]
        );
        $this->add_control(
                'in_same_term',
                [
                    'label' => esc_html__('In same Term', 'elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'separator' => 'before',
                    'condition' => [
                        //'from_relationship' => '',
                        'source' => ['previous', 'next'],
                    ]
                ]
        );
        $this->add_control(
                'taxonomy',
                [
                    'label' => esc_html__('Taxonomy', 'elementor'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('Select Taxonomy', 'elementor'),
                    'label_block' => true,
                    'query_type' => 'taxonomies',
                    'condition' => [
                        //'from_relationship' => '',
                        'in_same_term!' => '',
                        'source' => ['previous', 'next'],
                    ]
                ]
        );
    }
    
    public function get_post_taxonomy($post_id) {
        $post = get_post($post_id);
        if ($post->post_type != 'post') {
            $taxonomies = Utils::get_taxonomies($post->post_type);
            if (!empty($taxonomies)) {
                $taxonomies_keys = array_keys($taxonomies);
                return end($taxonomies_keys);
            }
        }
        return 'category';
    }

    public function get_post_id() {
        $settings = $this->get_settings_for_display();
        if (empty($settings))
            return;

        $post_id = get_the_ID();
        
        global $post;        
        if (!empty($post) && $post->post_type == 'attachment') {
            $post_id = $post->ID;
        }
        /*$queried_object = get_queried_object();
        if (!empty($queried_object) && get_class($queried_object) == 'WP_Post' && $queried_object->post_type == 'attachment') {
            $post_id = $queried_object->ID;
        }*/

        if ($settings['source']) {
            switch ($settings['source']) {

                case 'previous':
                    $taxonomy = $settings['taxonomy'] ? $settings['taxonomy'] : $this->get_post_taxonomy($post_id);
                    $prev = get_adjacent_post((bool) $settings['in_same_term'], $settings['excluded_terms'], true, $taxonomy);
                    if ($prev && is_object($prev) && get_class($prev) == 'WP_Post') {
                        $post_id = $prev->ID;
                    }
                    break;

                case 'next':
                    $taxonomy = $settings['taxonomy'] ? $settings['taxonomy'] : $this->get_post_taxonomy($post_id);
                    $next = get_adjacent_post((bool) $settings['in_same_term'], $settings['excluded_terms'], false, $taxonomy);
                    if ($next && is_object($next) && get_class($next) == 'WP_Post') {
                        $post_id = $next->ID;
                    }
                    break;

                case 'other':
                    if ($settings['post_id']) {
                        $post_id = $settings['post_id'];
                    }
                    break;

                default:
                    //parent //root
                    if ($post_id) {
                        do {
                            $parent_id = wp_get_post_parent_id($post_id);
                            if ($settings['source'] == 'parent') {
                                $post_id = $parent_id;
                                break;
                            }
                            // root
                            if ($parent_id) {
                                $post_id = $parent_id;
                            }
                        } while ($parent_id);
                    }
            }
        }
        
        if (Utils::is_plugin_active('wpml')) {
            $post_id = apply_filters( 'wpml_object_id', $post_id, get_post_type($post_id), true );
        }

        return $post_id;
    }
}
