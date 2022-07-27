<?php

namespace EAddonsForElementor\Modules\DynamicTags\Traits;

/**
 * @author francesco
 */
trait Background_Slideshow_Gallery {
    
    /*
    public function __construct() {
        parent::__construct();
        //echo $this->get_name();
        //$module = \Elementor\Plugin::$instance->dynamic_tags; 
        //var_dump($module->get_tags()); die();
        //$this->unregister_tag();        
    }
    */
    /*
    public function get_value( array $options = [] ) {
        $images = parent::get_value($options);
        return $images;
    }
    */
    
    /**
     * @since 2.0.0
     * @access public
     *
     * @param array $options
     *
     * @return mixed
     */
    public function get_content(array $options = []) {
        $value = $this->get_value($options);
        //var_dump($value); die();
        if (!empty($value) && is_array($value)) {
            foreach ($value as $key => $image) {
                if (!empty($image['id']) && empty($image['url'])) {
                    $value[$key]['url'] = wp_get_attachment_url($image['id']); // fix for Background Image Slider
                }
            }
        }
        //var_dump($value); die();
        return $value;
    }
    
    public function unregister_tag() {
        $module = \Elementor\Plugin::$instance->dynamic_tags;
        //var_dump($this->get_name());
        $module->unregister_tag( $this->get_name() );
        //var_dump($module->get_tags()); die();
        $module->register( $this );
        //var_dump($this);
        //var_dump($module->get_tags()); die();
    }
    
    public function get_title() {
        /*
        $tmp = explode('\\', __CLASS__);
        $title = end($tmp);
        $title = str_replace('_', ' ', $title);
        */
        $title = parent::get_title();
        if (isset($_GET['page']) && $_GET['page'] == 'e_addons_settings') {
            $title .= esc_html__(' Background Slider Fix');
        }
        return $title;
    }
    
    public function get_icon() {
        return 'eadd-dynamic-tag-bggalleryslider';
    }
    public function get_pid() {
        return 8849;
    }
}
