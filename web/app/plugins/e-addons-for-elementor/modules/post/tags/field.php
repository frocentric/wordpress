<?php

namespace EAddonsForElementor\Modules\Post\Tags;

//use Elementor\Core\DynamicTags\Tag;
use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use Elementor\Modules\DynamicTags\Module;
use EAddonsForElementor\Base\Base_Tag;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Field extends Base_Tag {
    
    use \EAddonsForElementor\Modules\Post\Traits\Posts;

    public function get_name() {
        return 'e-tag-post-field';
    }

    public function get_icon() {
        return 'eadd-dynamic-tag-post-field';
    }

    public function get_pid() {
        return 7461;
    }

    public function get_title() {
        return esc_html__('Post Field', 'e-addons');
    }

    public function get_group() {
        return 'post';
    }

    public static function _group() {
        return self::_groups('post');
    }

    /*
    public function get_categories() {
        return [
            'base', //\Elementor\Modules\DynamicTags\Module::BASE_GROUP
            'text', //\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
            'url', //\Elementor\Modules\DynamicTags\Module::URL_CATEGORY
            'number', //\Elementor\Modules\DynamicTags\Module::URL_CATEGORY
        ];
    }
     * 
     */

    /**
     * Register Controls
     *
     * Registers the Dynamic tag controls
     *
     * @since 2.0.0
     * @access protected
     *
     * @return void
     */
    protected function register_controls() {
        
        $this->add_control(
                'tag_field',
                [
                    'label' => esc_html__('Field', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'groups' => [
                        [
                            'label' => esc_html__('Custom', 'e-addons'),
                            'options' => [
                                '' => esc_html__('Custom', 'e-addons'),
                            ],
                        ],
                        [
                            'label' => esc_html__('Common', 'e-addons'),
                            'options' => [                                
                                'post_title' => esc_html__('Title', 'e-addons'),
                                'post_content' => esc_html__('Content', 'e-addons'),
                                'post_excerpt' => esc_html__('Excerpt', 'e-addons'),
                            ],
                        ],
                        [
                            'label' => esc_html__('Link', 'e-addons'),
                            'options' => [
                                'permalink' => esc_html__('Permalink', 'e-addons'),
                                'guid' => esc_html__('Guid', 'e-addons'),
                            ],
                        ],
                        [
                            'label' => esc_html__('Date', 'e-addons'),
                            'options' => [
                                'post_date' => esc_html__('Creation Date', 'e-addons'),
                                'post_date_gmt' => esc_html__('Creation Date GMT', 'e-addons'),
                                'post_modified' => esc_html__('Modified Date', 'e-addons'),
                                'post_modified_gmt' => esc_html__('Modified Date GMT', 'e-addons'),
                            ],
                        ],
                        [
                            'label' => esc_html__('Comment', 'e-addons'),
                            'options' => [
                                'comment_status' => esc_html__('Comment Status', 'e-addons'),
                                'comment_count' => esc_html__('Comment Count', 'e-addons'),
                            ],
                        ],
                        [
                            'label' => esc_html__('Other', 'e-addons'),
                            'options' => [
                                'ID' => esc_html__('ID', 'e-addons'),
                                'post_name' => esc_html__('Name', 'e-addons'),
                                'post_type' => esc_html__('Post Type Slug', 'e-addons'),
                                'post_author' => esc_html__('Author ID', 'e-addons'),                                
                                'post_parent' => esc_html__('Parent ID', 'e-addons'),
                                'post_status' => esc_html__('Status', 'e-addons'),
                                'post_password' => esc_html__('Password', 'e-addons'),                                
                                'menu_order' => esc_html__('Menu Order', 'e-addons'),
                                'post_content_filtered' => esc_html__('Content Filtered', 'e-addons'),
                                'post_mime_type' => esc_html__('Mime Type', 'e-addons'),
                            ],
                        ],
                        [
                            'label' => esc_html__('Ping', 'e-addons'),
                            'options' => [
                                'ping_status' => esc_html__('Ping Status', 'e-addons'),
                                'to_ping' => esc_html__('To Ping', 'e-addons'),
                                'pinged	text' => esc_html__('Pinged Text', 'e-addons'),
                            ],
                        ],
                    ],
                //'options' => [],
                //'label_block' => true,
                ]
        );
        
        $this->add_control(
                'date_format',
                [
                    'label' => esc_html__('Date Format', 'elementor'),
                    'type' => Controls_Manager::TEXT,
                    'placeholder' => esc_html__('Y-m-h', 'elementor'),
                    'condition' => [
                        'tag_field' => ['post_date','post_date_gmt','post_modified','post_modified_gmt'],
                    ]
                ]
        );

        $this->add_control(
                'custom',
                [
                    'label' => esc_html__('Custom Meta', 'elementor'),
                    'type' => 'e-query',
                    'select2options' => ['tags' => true],
                    'placeholder' => esc_html__('Meta key or Field Name', 'elementor'),
                    'label_block' => true,
                    'query_type' => 'metas',
                    'object_type' => 'post',
                    'condition' => [
                        'tag_field' => '',
                    ]
                ]
        );
        
        $this->add_control(
                'filters',
                [
                    'label' => esc_html__('Filters', 'e-addons') ,
                    'type' => Controls_Manager::TEXTAREA,                    
                    'placeholder' => 'get_permalink',
                    'rows' => 2,
                ]
        );

        $this->add_source_controls();

        Utils::add_help_control($this);
    }

    public function render() {
        $settings = $this->get_settings_for_display();
        if (empty($settings))
            return;

        $post_id = $this->get_post_id();        
        //var_dump($post_id);
        
        if ($post_id) {
            if (!empty($settings['tag_field'])) {
                $field = $settings['tag_field'];
                if ($field == 'permalink') {
                    $meta = get_permalink($post_id);
                } else {
                    $meta = get_post_field($field, $post_id);
                }
                if (in_array($field, ['post_date','post_date_gmt','post_modified','post_modified_gmt'])) {
                    if ($settings['date_format']) {
                        $time = strtotime($meta);
                        $meta = date($settings['date_format'], $time);
                    }
                }
            } else {
                $field = $settings['custom'];
                $meta = Utils::get_post_field($field, $post_id);
            }
            
            if (!empty($settings['filters'])) {
                $meta = Utils::apply_filters($meta, $settings['filters']);
                //var_dump($value);
            }

            echo Utils::to_string($meta);
        }
    }

}
