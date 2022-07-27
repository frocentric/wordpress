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

class Image extends Base_Tag {
    
    use \EAddonsForElementor\Modules\Term\Traits\Terms;
    
    public $is_data = true;
    
    public function get_name() {
        return 'e-tag-term-image';
    }

    public function get_icon() {
        return 'eadd-dynamic-tag-term-image';
    }

    public function get_pid() {
        return 7459;
    }

    public function get_title() {
        return esc_html__('Term Image', 'e-addons');
    }

    public function get_group() {
        return 'term';
    }
    public static function _group() {
        return self::_groups('term');
    }

    public function get_categories() {
        return [
            //'base', //\Elementor\Modules\DynamicTags\Module::BASE_GROUP
            'image', //\Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY,
        ];
    }

    /**
     * @since 2.0.0
     * @access protected
     */
    protected function register_advanced_section() {
        
    }

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
                'image',
                [
                    'label' => esc_html__('Image', 'elementor'),
                    'type' => 'e-query',
                    'select2options' => ['tags' => true],
                    'placeholder' => esc_html__('Meta key or Name', 'elementor'),
                    'label_block' => true,
                    'query_type' => 'metas',
                    'object_type' => 'term',
                ]
        );
        
        $this->add_source_controls();                

        $this->add_control(
                'fallback_image',
                [
                    'label' => esc_html__('Fallback', 'elementor'),
                    'type' => Controls_Manager::MEDIA,
                    'dynamic' => [
                        'active' => true,
                    ],
                    'default' => [
                        'url' => Utils::get_placeholder_image_src(),
                    ],
                ]
        );

        Utils::add_help_control($this);
    }

    public function get_value(array $options = []) {
        $settings = $this->get_settings();
        if (empty($settings))
            return;

        $term_id = $this->get_term_id();

        $id = '';
        $url = '';
        if ($term_id) {
            // custom field
            $meta = Utils::get_term_field($settings['image'], $term_id);
            
            $img = Utils::get_image($meta);
            
            if (!Utils::empty($img) && !empty($img['url'])) {
                if (!empty($img['id'])) {
                    $id = $img['id'];
                }
                $url = $img['url'];
            } else {
                if (!empty($settings['fallback_image']['url'])) {
                    $id = $settings['fallback_image']['id'];
                    $url = $settings['fallback_image']['url'];
                }
            }
        }
        //var_dump($url);
        return [
            'id' => $id,
            'url' => $url,
        ];
    }

}
