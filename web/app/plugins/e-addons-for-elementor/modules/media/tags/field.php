<?php

namespace EAddonsForElementor\Modules\Media\Tags;

//use Elementor\Core\DynamicTags\Tag;
use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use Elementor\Modules\DynamicTags\Module;
use EAddonsForElementor\Base\Base_Tag;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Field extends Base_Tag {
    
    use \EAddonsForElementor\Modules\Media\Traits\Medias;

    public function get_name() {
        return 'e-tag-media-field';
    }

    public function get_icon() {
        return 'eadd-dynamic-tag-media';
    }

    public function get_pid() {
        return 19071;
    }

    public function get_title() {
        return esc_html__('Media Field', 'e-addons');
    }

    public function get_group() {
        return 'media';
    }

    public static function _group() {
        return self::_groups('post');
    }

    public function get_categories() {
        return [
            'base', //\Elementor\Modules\DynamicTags\Module::BASE_GROUP
            'text', //\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
            'url', //\Elementor\Modules\DynamicTags\Module::URL_CATEGORY
        ];
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
                                '_wp_attachment_image_alt' => esc_html__('Alternative Text', 'e-addons'),
                                'post_excerpt' => esc_html__('Caption', 'e-addons'),
                                'post_content' => esc_html__('Description', 'e-addons'),
                                '_wp_attachment_metadata' => esc_html__('Meta Data', 'e-addons'),
                                'post_mime_type' => esc_html__('Mime Type', 'e-addons'),                                
                            ],
                        ],
                        [
                            'label' => esc_html__('Link', 'e-addons'),
                            'options' => [
                                'permalink' => esc_html__('Permalink', 'e-addons'),
                                'guid' => esc_html__('File URL', 'e-addons'),
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
                                'post_author' => esc_html__('Author ID', 'e-addons'),                                
                                'post_parent' => esc_html__('Uploaded to', 'e-addons'),
                                'post_password' => esc_html__('Password', 'e-addons'),                                
                                'menu_order' => esc_html__('Menu Order', 'e-addons'),
                                'post_content_filtered' => esc_html__('Content Filtered', 'e-addons'),
                            ],
                        ],
                    ],
                    'default' => 'guid',
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
                'metadata',
                [
                    'label' => esc_html__('Meta Data', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'width' => esc_html__('The width of the attachment', 'elementor'),
                        'height' => esc_html__('The height of the attachment', 'elementor'),
                        'file' => esc_html__('The file path relative to wp-content/uploads', 'elementor'),
                        'sizes' => esc_html__('Sizes', 'elementor'),
                        'image_meta' => esc_html__('Image metadata', 'elementor'),
                    ],
                    'default' => 'file',
                    'condition' => [
                        'tag_field' => ['_wp_attachment_metadata'],
                    ]
                ]
        );
        $this->add_control(
                'metadata_key',
                [
                    'label' => esc_html__('Image Metadata', 'elementor'),
                    'type' => Controls_Manager::TEXT,
                    'placeholder' => esc_html__('iso', 'elementor'),
                    'condition' => [
                        'tag_field' => ['_wp_attachment_metadata'],
                        'metadata' => ['image_meta'],
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
                    'object_type' => 'attachment',
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

        $media_id = $this->get_media_id();        
        
        if ($media_id) {
            if (!empty($settings['tag_field'])) {
                $field = $settings['tag_field'];
                switch ($field) {
                    case 'permalink':
                        $meta = get_permalink($media_id);
                        break;
                    case '_wp_attachment_metadata':
                    case '_wp_attachment_image_alt':                        
                    default:
                        $meta = Utils::get_post_field($field, $media_id);
                }
                if ($field == '_wp_attachment_metadata') {
                    switch($settings['metadata']) {
                        case 'width':
                        case 'height':
                        case 'file':
                            $meta = $meta[$settings['metadata']];
                            break;
                        case 'sizes':
                            $meta = array_keys($meta['sizes']);
                            break;
                        case 'image_meta':
                            if (empty($settings['metadata_key'])) {
                                $meta = array_keys($meta['image_meta']);
                            } else {
                                if (empty($meta['image_meta'][$settings['metadata_key']])) {
                                    $meta = '';
                                } else {
                                    $meta = $meta['image_meta'][$settings['metadata_key']];
                                }
                            }
                    }
                }
                if (in_array($field, ['post_date','post_date_gmt','post_modified','post_modified_gmt'])) {
                    if ($settings['date_format']) {
                        $time = strtotime($meta);
                        $meta = date($settings['date_format'], $time);
                    }
                }
            } else {
                $field = $settings['custom'];
                $meta = Utils::get_post_field($field, $media_id);
            }

            echo Utils::to_string($meta);
        }
    }

}
