<?php

namespace EAddonsDev\Modules\Query\Tags;

//use Elementor\Core\DynamicTags\Tag;
use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use Elementor\Modules\DynamicTags\Module;
use EAddonsForElementor\Base\Base_Tag;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Custom_Field extends Base_Tag {

    public function get_name() {
        return 'e-tag-custom-field';
    }
    
    public function get_icon() {
        return 'eadd-dynamic-tag-rowcustomfield';
    }
    
    public function get_pid() {
        return 35123;
    }
    
    public function get_title() {
        return esc_html__('Row Custom Field', 'e-addons');
    }

    public function get_group() {
        return 'archive';
    }
    public static function _group() {
        return self::_groups('archive');
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
                'row_field',
                [
                    'label' => esc_html__('Row Custom Field', 'e-addons'),
                    'type' => Controls_Manager::TEXT,
                    'placeholder' => esc_html__('Write the Row Field Custom ID', 'e-addons'),
                    'label_block' => true,
                ]
        );
        
        $options = array('' => esc_html__('Automatic'), 'date' => esc_html__('Date'), 'array' => esc_html__('Array'));
        foreach (Utils::get_dynamic_tags_categories() as $category) {
            $options[$category] = $category;
        }
        $this->add_control(
                'category',
                [
                    'label' => esc_html__('Tag Category', 'e-addons') ,
                    'type' => Controls_Manager::SELECT,                    
                    'options' => $options,
                ]
        );
        
        $this->add_control(
                'sub_field',
                [
                    'label' => esc_html__('Sub Field', 'e-addons') ,
                    'type' => Controls_Manager::TEXT,
                    'condition' => [
                        'category' => ['gallery'],
                    ]
                ]
        );
        
        $this->add_control(
                'date_format',
                [
                    'label' => esc_html__('DateTime Format', 'e-addons') ,
                    'type' => Controls_Manager::TEXT,                    
                    'placeholder' => 'Y-m-d H:i:s',
                    'condition' => [
                        'category' => ['date'],
                    ]
                ]
        );
        
        $this->add_control(
                'json_decode',
                [
                    'label' => esc_html__('JSON Decode', 'e-addons') ,
                    'type' => Controls_Manager::SWITCHER,                    
                    'condition' => [
                        'category' => ['array'],
                    ]
                ]
        );
        
        $this->add_control(
                'filters',
                [
                    'label' => esc_html__('Filters', 'e-addons') ,
                    'type' => Controls_Manager::TEXTAREA,                    
                    'placeholder' => 'trim',
                    'rows' => 2,
                ]
        );
        
        $this->add_control(
                'fallback_img',
                [
                    'label' => esc_html__('Fallback Media', 'e-addons') ,
                    'type' => Controls_Manager::MEDIA,
                    'condition' => [
                        'category' => ['image', 'media', 'gallery'],
                    ]
                ]
        );

        Utils::add_help_control($this);
    }
    
    public function render() {
        $settings = $this->get_settings();
        if (empty($settings))
            return;

        $value = $this->get_field_value($settings['category']);
        echo Utils::to_string($value);

    }

    public function get_field_value($field_category = 'base', $options = []) {
        $settings = $this->get_settings_for_display();
        if (empty($settings))
            return;
        
        if (!empty($settings['row_field'])) {
            $field_id = $settings['row_field'];
            //var_dump($field_id);

            global $e_widget_query;
            if (!empty($e_widget_query)) {
                $value = false;
            
                //var_dump($e_widget_query->current_data); die();
                
                if (is_array($e_widget_query->current_data)) {
                    if (!empty($e_widget_query->current_data[$field_id])) {
                        $value = $e_widget_query->current_data[$field_id]; 
                    } else {
                        $field_ids = Utils::explode($field_id,'.');
                        $value = Utils::get_array_value($e_widget_query->current_data, $field_ids);
                    }
                }
                if (is_object($e_widget_query->current_data)) {
                    if (!empty($e_widget_query->current_data->{$field_id})) {
                        $value = $e_widget_query->current_data->{$field_id}; 
                    }
                }
                
                //var_dump($value);
                if ($value == 'null' || $value == 'NULL' || $value == '%{value}') {
                    $value = false;
                }
                
                if ($value) {
                    //var_dump($value); var_dump($field_category);// die();
                    
                    
                    if (!empty($settings['filters'])) {
                        $value = Utils::apply_filters($value, $settings['filters']);
                        //var_dump($value);
                    }
                    
                    if (!empty($value)) {         
                        
                        if (in_array($field_category, array('image', 'media'))) {
                            
                            
                            if (filter_var($value, FILTER_VALIDATE_URL)) {
                                $image_data = [
                                    'url' => $value,
                                ];
                                $thumbnail_id = Utils::url_to_postid($value);
                                //if ($thumbnail_id) {
                                    $image_data['id'] = $thumbnail_id;
                                //}
                                //var_dump($image_data);
                                return $image_data;
                            }

                            if (is_numeric($value)) {
                                $image_data = [
                                    'id' => $value,
                                ];
                                $image_url = wp_get_attachment_image_url($value, 'full');
                                if ($image_url) {
                                    $image_data['url'] = $image_url;
                                }
                                //var_dump($image_data);
                                return $image_data;
                            }

                        }

                        if (is_array($value) && in_array($field_category, array('image', 'gallery'))) {
                            $images = [];
                            foreach ( $value as $image ) {
                                if (!empty($settings['sub_field'])) {
                                    $ids = Utils::explode($settings['sub_field'],'.');                                    
                                    $image = Utils::get_array_value($image, $ids);                                    
                                }
                                
                                if (!\Elementor\Utils::is_empty($settings, 'before')) {
                                    $image = wp_kses_post($settings['before']) . $image;
                                }
                                if (!\Elementor\Utils::is_empty($settings, 'after')) {
                                    $image .= wp_kses_post($settings['after']);
                                }
                                
                                if (filter_var($image, FILTER_VALIDATE_URL)) {
                                    $images[] = [
                                        'id' => '',
                                        'url' => $image,
                                    ];
                                } else {
                                    $image_url = wp_get_attachment_image_url($image, 'full');
                                    $images[] = [
                                        'id' => $image,
                                        'url' => $image_url,
                                    ];
                                }
                            }
                            
                            return $images;
                        }
                        
                        if (in_array($field_category, array('date', 'time', 'datetime'))) {                        
                            if (!empty($settings['date_format'])) {
                                if (is_numeric($value)) {
                                    if (strlen($value) == 8) {
                                        //ACF date -> 'Ymd'
                                        $value = substr($value,0,4).'-'.substr($value,4,2).'-'.substr($value,6,2);
                                        $time = strtotime($value); //ACF
                                    } else {
                                        $time = $value; // unix timestamp?
                                    }
                                } else {
                                    $time = strtotime($value);
                                }
                                $value = date($settings['date_format'], $time);
                            }
                        }
                        
                    }
                    
                    if (is_string($value)) {
                        $value = str_replace('“', '"', $value);
                    }
                    
                    if (!in_array($field_category, array('array'))) {
                        $value = Utils::to_string($value);
                    } else {
                        //var_dump($value); die();
                        if (!empty($settings['json_decode'])) {
                            if (is_array($value)) {
                                foreach ($value as $key => $val) {
                                    if (is_string($val)) {
                                        $value[$key] = json_decode($val, true);
                                    }
                                }
                            } else {
                                if (is_string($value)) {
                                    $value = json_decode($value, true);
                                }
                            }
                        }
                    }
                    
                    return $value;
                }
            }
            
            if (Utils::is_preview()) {
                return $field_id;
            }
        }
        
        return false;
        
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

        $field_category = (empty($settings['category'])) ? 'text' : $settings['category'];
        //var_dump($field_category);
        
        //"base" "text" "url" "image" "media" "post_meta" "gallery" "number" "color"
        if (in_array($field_category, array('image', 'gallery', 'media', 'array'))) {
            
            $value = $this->get_field_value($field_category, $options);            
            if (empty($value)) {
                if (in_array($field_category, array('image', 'gallery', 'media'))) {
                    if (!\Elementor\Utils::is_empty($settings, 'fallback_img', 'id')) {                    
                        $value = $settings['fallback_img'];         
                        if ($field_category == 'gallery') {
                            $value = array($value);
                        }
                    }
                }
            }
            
        } else {
            
            ob_start();
            $this->render();
            $value = ob_get_clean();
            
            if (!empty($value)) {
                // TODO: fix spaces in `before`/`after` if WRAPPED_TAG ( conflicted with .elementor-tag { display: inline-flex; } );
                if (!\Elementor\Utils::is_empty($settings, 'before')) {
                    $value = wp_kses_post($settings['before']) . $value;
                }

                if (!\Elementor\Utils::is_empty($settings, 'after')) {
                    $value .= wp_kses_post($settings['after']);
                }
            } elseif (!\Elementor\Utils::is_empty($settings, 'fallback')) {
                $value = $settings['fallback'];
                $value = Utils::get_dynamic_data($value);
            }
            
        }

        return $value;
    }
/*
    public function table() {
        $value = [
            """["Prima colonna", "Seconda colonna"]""",
            """["Prima riga", "Prima riga 1"]""",
            """["Seconda riga", "Seconda riga 2"]""",
            """["terza riga", "terza riga"]"""
        ];
    }
 * 
 */
}