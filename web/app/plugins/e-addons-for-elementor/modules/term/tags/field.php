<?php

namespace EAddonsForElementor\Modules\Term\Tags;

//use Elementor\Core\DynamicTags\Tag;
use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use Elementor\Modules\DynamicTags\Module;
use EAddonsForElementor\Base\Base_Tag;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Field extends Base_Tag {
    
    use \EAddonsForElementor\Modules\Term\Traits\Terms;

    public function get_name() {
        return 'e-tag-term-field';
    }

    public function get_icon() {
        return 'eadd-dynamic-tag-term-field';
    }

    public function get_pid() {
        return 7459;
    }

    public function get_title() {
        return esc_html__('Term Field', 'e-addons');
    }

    public function get_group() {
        return 'term';
    }
    public static function _group() {
        return self::_groups('term');
    }

    /*
    public function get_categories() {
        return [
            'base', //\Elementor\Modules\DynamicTags\Module::BASE_GROUP
            'text', //\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
            'url', //\Elementor\Modules\DynamicTags\Module::URL_CATEGORY
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
                                'name' => esc_html__('Name', 'e-addons'),
                                "description" => esc_html__('Description', 'e-addons'),                        
                                "count" => esc_html__('Count', 'e-addons'),                                
                            ],
                        ],
                        [
                            'label' => esc_html__('Link', 'e-addons'),
                            'options' => [
                                "link" => esc_html__('Link (to Posts Archive)', 'e-addons'),
                            ],
                        ],
                        [
                            'label' => esc_html__('Other', 'e-addons'),
                            'options' => [                                
                                'term_id' => esc_html__('ID', 'e-addons'),
                                "parent" => esc_html__('Parent ID', 'e-addons'),
                                'slug' => esc_html__('Slug', 'e-addons'),
                                "filter" => esc_html__('Filter', 'e-addons'),
                                'term_group' => esc_html__('Term Group', 'e-addons'),
                            ],
                        ],                       
                    ],
                    //'options' => [],
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
                    'object_type' => 'term',
                    'condition' => [
                        'tag_field' => '',
                    ]
                ]
        );
        
        $this->add_source_controls();

        Utils::add_help_control($this);
    }

    public function render() {
        $settings = $this->get_settings_for_display();
        if (empty($settings))
            return;

        $term_id = $this->get_term_id();        

        if ($term_id) {
            if (!empty($settings['tag_field'])) {
                $field = $settings['tag_field'];
                if ($field == 'link') {
                    //$meta = Utils::get_term_url($term_id);
                    $meta = get_term_link($term_id);
                    if (is_wp_error($meta) || !filter_var($meta, FILTER_VALIDATE_URL)) {
                        $meta = ''; // Empty Term.                        
                    }
                } else {
                    $meta = get_term_field($field, $term_id);
                }
                if ($field == 'parent') {
                    if ($meta == 0) {
                        $meta = '';
                    }
                }
            } else {
                $field = $settings['custom'];
                $meta = Utils::get_term_field($field, $term_id);
            }
            
            echo Utils::to_string($meta);
        }
    }

}
