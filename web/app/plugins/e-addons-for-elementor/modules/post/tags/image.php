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

class Image extends Base_Tag {

    use \EAddonsForElementor\Modules\Post\Traits\Posts;

    public $is_data = true;

    public function get_name() {
        return 'e-tag-post-image';
    }

    public function get_icon() {
        return 'eadd-dynamic-tag-post-image';
    }

    public function get_pid() {
        return 7461;
    }

    public function get_title() {
        return esc_html__('Post Image', 'e-addons');
    }

    public function get_group() {
        return 'post';
    }

    public static function _group() {
        return self::_groups('post');
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
                'featured',
                [
                    'label' => esc_html__('Featured Image', 'elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'yes',
                ]
        );
        $this->add_control(
                'image',
                [
                    'label' => esc_html__('Custom Image', 'elementor'),
                    'type' => 'e-query',
                    'select2options' => ['tags' => true],
                    'placeholder' => esc_html__('Meta key or Name', 'elementor'),
                    'label_block' => true,
                    'query_type' => 'metas',
                    'object_type' => 'post',
                    'condition' => [
                        'featured' => '',
                    ]
                ]
        );

        $this->add_source_controls();

        $this->add_control('image_size',
                [
                    'label' => _x('Image Size', 'Image Size Control', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'options' => Utils::get_image_sizes(),
                    'separator' => 'before',
        ]);

        $this->add_control('image_custom_dimension',
                [
                    'label' => _x('Image Dimension', 'Image Size Control', 'elementor'),
                    'type' => Controls_Manager::IMAGE_DIMENSIONS,
                    'description' => esc_html__('You can crop the original image size to any custom size. You can also set a single value for height or width in order to keep the original size ratio.', 'elementor'),
                    'condition' => [
                        'image_size' => 'custom',
                    ],                    
        ]);

        $this->add_control(
                'fallback_image',
                [
                    'label' => esc_html__('Fallback', 'elementor'),
                    'type' => Controls_Manager::MEDIA,
                    'separator' => 'before',
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

        $post_id = $this->get_post_id();

        $id = '';
        $url = '';
        if ($post_id) {

            $post = get_post($post_id);
            if ($settings['featured'] && $post->post_type == 'attachment') {

                $id = $post_id;
                $url = $post->guid;
            } else {

                // custom field
                if ($settings['featured']) {
                    $meta = get_post_thumbnail_id($post_id);
                } else {
                    $meta = Utils::get_post_field($settings['image'], $post_id);
                }
                $img = Utils::get_image($meta);
                if (!$img && filter_var($meta, FILTER_VALIDATE_URL)) {
                    $img['url'] = $meta;
                }
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
                
                if ($id) {
                    if (!empty($settings['image_size']) && $settings['image_size'] != 'full') {
                        $size = $settings['image_size'];
                        if ($size == 'custom') {
                            $url = \Elementor\Group_Control_Image_Size::get_attachment_image_src($id, 'image', $settings);                        
                        } else {
                            $image = wp_get_attachment_image_src($id, $size);
                            if (!empty($image)) {
                                $url = reset($image);
                            }
                        }
                        $id = null; // needed or return the size set in Widget Image
                    }
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
