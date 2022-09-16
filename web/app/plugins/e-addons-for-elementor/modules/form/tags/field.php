<?php

namespace EAddonsForElementor\Modules\Form\Tags;

//use Elementor\Core\DynamicTags\Tag;
use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Core\Utils\Form;
use Elementor\Modules\DynamicTags\Module;
use EAddonsForElementor\Base\Base_Tag;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Field extends Base_Tag {

    public function get_name() {
        return 'e-tag-form-field';
    }

    public function get_icon() {
        return 'eadd-dynamic-tag-form-field';
    }

    public function get_pid() {
        return 12799;
    }

    public function get_title() {
        return esc_html__('Form Field', 'e-addons');
    }

    public function get_group() {
        return 'form';
    }

    public static function _group() {
        return self::_groups('form');
    }

    public function get_categories() {
        if (Utils::is_plugin_active('elementor-pro')) {
            return [
                'base', //\Elementor\Modules\DynamicTags\Module::BASE_GROUP
                'text', //\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
                'url', //\Elementor\Modules\DynamicTags\Module::URL_CATEGORY
                'image', //\Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY,
                'gallery', //\Elementor\Modules\DynamicTags\Module::GALLERY_CATEGORY,
                'number', //\Elementor\Modules\DynamicTags\Module::NUMBER_CATEGORY,
                'post_meta', //\Elementor\Modules\DynamicTags\Module::POST_META_CATEGORY,
            ];
        }
        return [];
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
                'form_field',
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
                                'post_id' => esc_html__('Post ID', 'e-addons'),
                                'queried_id' => esc_html__('Queried Object ID', 'e-addons'),
                                'form_id' => esc_html__('Form ID', 'e-addons'),
                                'form_name' => esc_html__('Form Name', 'e-addons'),
                                'all_fields' => esc_html__('All Fields', 'e-addons'),
                                'all_fields_not_empty' => esc_html__('All Fields (not empty)', 'e-addons'),
                                'e_fields' => esc_html__('Enhanced Fields', 'e-addons'),
                                'e_fields_not_empty' => esc_html__('Enhanced Fields (not empty)', 'e-addons'),
                            //'all_fields_labels' => esc_html__('All Fields (with Labels)', 'e-addons'),
                            ],
                        ]
                    ],
                ]
        );

        $this->add_control(
                'custom',
                [
                    'label' => esc_html__('Custom ID', 'elementor'),
                    'type' => Controls_Manager::TEXT, //'e-query',
                    'select2options' => ['tags' => true],
                    'placeholder' => esc_html__('Form Field Custom ID', 'elementor'),
                    'label_block' => true,
                    //'query_type' => 'metas',
                    //'object_type' => 'term',
                    'condition' => [
                        'form_field' => '',
                    ]
                ]
        );

        /* $this->add_control(
          'form_id',
          [
          'label' => esc_html__('Form ID', 'elementor'),
          'type' => Controls_Manager::TEXT, //'e-query',
          'placeholder' => esc_html__('Form ID', 'elementor'),
          'label_block' => true,
          'condition' => [
          'form_field' => '',
          ]
          ]
          ); */

        $this->add_control(
                'form_return',
                [
                    'label' => esc_html__('Return', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => esc_html__('Value', 'e-addons'),
                        'option' => esc_html__('Option Name', 'e-addons'),
                        'label' => esc_html__('Label', 'e-addons'),
                        'raw' => esc_html__('Raw', 'e-addons'),
                        'placeholder' => esc_html__('Placeholder', 'e-addons'),
                        'type' => esc_html__('Type', 'e-addons'),
                        'media' => esc_html__('Image', 'e-addons'),
                        'gallery' => esc_html__('Gallery', 'e-addons'),
                    ],
                    'condition' => [
                        'form_field' => '',
                    ]
                ]
        );

        Utils::add_help_control($this);
    }

    public function render() {
        $settings = $this->get_settings();
        if (empty($settings))
            return;

        $value = $this->_render($settings);

        echo $value;
    }

    public function _render($settings = array()) {
        if (empty($settings))
            $settings = $this->get_settings_for_display();
        if (empty($settings))
            return;

        global $e_form;
        if (empty($e_form)) {
            if(!empty($_POST['form_fields'])) {
                $e_form = $_POST['form_fields'];
            }
        }

        if (Utils::is_preview() || empty($e_form)) {

            if (!empty($settings['form_field'])) {
                $field = $settings['form_field'];
                switch ($field) {
                    case 'form_name':
                        $field = 'Form Name';
                        break;
                    case 'all_fields_not_empty':
                        $field = '[all-fields|!empty]';
                        break;
                    case 'all_fields':
                        $field = '[all-fields]';
                        break;
                    case 'e_fields_not_empty':
                        $field = '[e-fields|!empty]';
                        break;
                    case 'e_fields':
                        $field = '[e-fields]';
                        break;
                }
            } else {
                $field = $settings['custom'];
            }

            if (empty($settings['form_return'])) {
                echo $field;
            } else {
                switch ($settings['form_return']) {
                    case 'media':
                        return [
                            'id' => '',
                            'url' => Utils::get_placeholder_image_src(),
                        ];
                        break;
                    case 'gallery':
                        return [
                            [
                                'id' => '',
                                'url' => Utils::get_placeholder_image_src(),
                            ],
                            [
                                'id' => '',
                                'url' => Utils::get_placeholder_image_src(),
                            ],
                        ];
                        break;
                    default:
                        echo $field;
                }
            }
            return;
        }

        $meta = false;
        if ($e_form) {
            if (!empty($settings['form_field'])) {
                $field = $settings['form_field'];

                if (isset($_POST[$field])) {
                    $meta = $_POST[$field];
                } else {
                    switch ($settings['form_field']) {
                        case 'form_name':
                            $form_settings = Utils::get_settings_by_element_id($_POST['form_id']);
                            $meta = $form_settings['form_name'];
                            break;
                        case 'all_fields_not_empty':
                            $meta = '[all-fields|!empty]';
                            break;
                        case 'all_fields':
                        default:
                            $meta = '[all-fields]';
                    }
                    $meta = Form::replace_content_shortcodes($meta, $e_form);
                }
            } else {
                $field = $settings['custom'];


                switch ($settings['form_return']) {

                    case 'label':
                        $meta = Form::get_field_label($field);
                        break;
                    case 'placeholder':
                        if (!empty($_POST['form_id'])) {
                            $form_settings = Utils::get_settings_by_element_id($_POST['form_id']);
                            $form_field = Form::get_field($field, $form_settings);
                            $meta = $form_field['placeholder'];
                        }
                        break;
                    case 'type':
                        if (!empty($_POST['form_id'])) {
                            $form_settings = Utils::get_settings_by_element_id($_POST['form_id']);
                            $meta = Form::get_field_type($field, $form_settings);
                        }
                        break;
                    case 'raw':
                        if (isset($_FORM['form_fields'][$field])) {
                            $meta = $_FORM['form_fields'][$field];
                        }
                        break;
                    case 'media':
                        if (isset($e_form[$field])) {
                            $id = '';
                            $url = $e_form[$field];
                            return [
                                'id' => $id,
                                'url' => $url,
                            ];
                        }
                        break;
                    case 'media':
                        if (isset($e_form[$field])) {
                            $imgs = Utils::explode($e_form[$field]);
                            $gallery = [];
                            foreach ($imgs as $img) {
                                $img = Utils::get_image($img);
                                if (!empty($img)) {
                                    $gallery[] = $img;
                                }
                            }                        
                            return $gallery;
                        }
                        break;
                    default:
                        if (isset($e_form[$field])) {
                            $meta = $e_form[$field];
                        }
                        if (isset($_POST[$field])) {
                            $meta = $_POST[$field];
                        }
                        if (isset($_POST['form_fields'][$field])) {
                            $meta = $_POST['form_fields'][$field];
                        }
                        //var_dump($_POST);
                        if ($settings['form_return'] == 'option') {
                            //var_dump($field); var_dump($meta);
                            $meta = Form::get_field_option_label($field, $meta);
                            //var_dump($field); var_dump($meta); die();
                        }
                }
            }

            echo Utils::to_string($meta);
        }
    }

    public function get_value(array $options = []) {
        $settings = $this->get_settings_for_display();
        if (empty($settings))
            return;

        $value = $this->_render($settings);

        $value = Utils::maybe_media($value, $this);

        return $value;
    }

    /**
     * @since 2.0.0
     * @access public
     *
     * @param array $options
     *
     * @return string
     */
    public function get_content(array $options = []) {
        $settings = $this->get_settings();

        if ($settings['form_return'] == 'media') {
            $this->is_data = true;
        }
        
        return parent::get_content($options);
    }

}
