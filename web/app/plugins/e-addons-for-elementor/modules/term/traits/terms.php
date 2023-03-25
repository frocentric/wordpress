<?php

namespace EAddonsForElementor\Modules\Term\Traits;

use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;

trait Terms {

    public function add_source_controls() {
        $this->add_control(
                'source',
                [
                    'label' => esc_html__('Source', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => esc_html__('Current (Term Archive)', 'e-addons'),
                        //'query' => esc_html__('Current (Query Term Widget)', 'e-addons'),
                        'parent' => esc_html__('Parent', 'e-addons'),
                        'root' => esc_html__('Root', 'e-addons'),
                        'post' => esc_html__('Current Post', 'e-addons'),
                        'other' => esc_html__('Other', 'e-addons'),
                    ],
                //'label_block' => true,
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
                        'source' => 'post',
                    ]
                ]
        );

        $this->add_control(
                'term_id',
                [
                    'label' => esc_html__('Term', 'elementor'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('Select other Term', 'elementor'),
                    'label_block' => true,
                    'query_type' => 'terms',
                    'condition' => [
                        'source' => 'other',
                    ]
                ]
        );
    }

    public function get_term_id() {
        $settings = $this->get_settings_for_display();
        if (empty($settings))
            return;

        $term_id = $this->get_module()->get_term_id();

        if ($settings['source']) {
            switch ($settings['source']) {
                case 'query':
                    global $e_widget_query;
                    if (!empty($e_widget_query)) {
                        if (is_object($e_widget_query->current_data) && get_class($e_widget_query->current_data) == 'WP_Term') {
                            return $e_widget_query->current_id;
                        }
                    }
                    break;
                case 'other':
                    if ($settings['term_id']) {
                        $term_id = $settings['term_id'];
                        //return $term_id;
                    }
                    break;
                case 'post':
                    //$post_id = get_the_ID();
                    $post_type = get_post_type();
                    $taxonomy = 'category';
                    if ($post_type && $post_type != 'post') {
                        $taxonomies = get_post_taxonomies();
                        if (!empty($taxonomies)) {
                            $taxonomy = reset($taxonomies);
                        }
                    }
                    $taxonomy = $settings['taxonomy'] ? $settings['taxonomy'] : $taxonomy;
                    $terms = get_the_terms(get_the_ID(), $taxonomy);
                    if (!empty($terms)) {
                        $term = reset($terms);
                        $term_id = $term->term_id;
                        //return $term_id;
                    }
                    break;
                case 'root':
                case 'parent':
                    if ($term_id) {
                        do {
                            $term = Utils::get_term($term_id);
                            $parent_id = $term->parent;
                            if ($settings['source'] == 'parent') {
                                //return $parent_id;
                                $term_id = $parent_id;
                                break;
                            }
                            if ($parent_id) {
                                $term_id = $parent_id;
                            }
                        } while ($parent_id);
                        //return $term_id;
                    }
            }
        }

        if (Utils::is_plugin_active('wpml')) {
            $term = get_term($term_id);
            $term_id = apply_filters('wpml_object_id', $term_id, $term->taxonomy, true);
        }

        return $term_id;
    }

}
