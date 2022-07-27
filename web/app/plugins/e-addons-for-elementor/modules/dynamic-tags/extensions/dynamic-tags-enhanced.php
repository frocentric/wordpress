<?php

namespace EAddonsForElementor\Modules\DynamicTags\Extensions;

use EAddonsForElementor\Core\Utils;
use Elementor\Controls_Manager;
use EAddonsForElementor\Base\Base_Tag;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Dynamic_Tags_Enhanced extends Base_Tag {

    public function get_pid() {
        return 1302;
    }

    public static $types = [
        Controls_Manager::TEXT,
        Controls_Manager::TEXTAREA,
        Controls_Manager::WYSIWYG,
        Controls_Manager::NUMBER,
        Controls_Manager::URL,
        Controls_Manager::COLOR,
        Controls_Manager::SLIDER,
        Controls_Manager::MEDIA,
        Controls_Manager::GALLERY,
        Controls_Manager::CODE,
    ];
    public $excluded = ['popup_timing'];

    public function __construct() {
        parent::__construct();
        $this->add_actions();
    }

    public function add_actions() {
        //add_action('elementor/element/before_section_end', [$this, 'add_dynamic_tags'], 11, 3);
        add_action( 'elementor/controls/controls_registered', [$this, 'add_controls_dynamic_tags'], 999 );
        
        // fix PRO Popup Triggers
        add_action('elementor/element/popup_triggers/triggers/before_section_end', [$this, 'remove_dynamic_tags'], 11, 2);
        add_action('elementor/element/popup_timing/timing/before_section_end', [$this, 'remove_dynamic_tags'], 11, 2);
        
        // fix section video background
        add_action("elementor/frontend/section/before_render", [$this, '_section_before_render']);
        add_action("elementor/frontend/section/after_render", [$this, '_section_after_render']);
    }

    public function get_name() {
        return 'e-dynamic-tags';
    }

    public function get_icon() {
        return 'eadd-enhanced-dynamic-tags';
    }
    
    public function add_controls_dynamic_tags($manager) {             
        $controls = $manager->get_controls();
        //var_dump($controls); die();
        foreach ($controls as $ckey => $control) {
            if (in_array($control->get_type(), self::$types)) {
                $dynamic = $control->get_settings('dynamic');        
                $dynamic['active'] = true;
                $control->set_settings('dynamic', $dynamic);  
            }
        }
    }

    public function remove_dynamic_tags($element, $args) {
        
        $stacks = \Elementor\Plugin::$instance->controls_manager->get_stacks();
        //var_dump(array_keys($stacks)); die();       
        
        if (!empty($stacks['popup_triggers'])) {
            foreach ($stacks['popup_triggers']['controls'] as $ckey => $control) {
                $element->update_control($control['name'], array('dynamic' => false));
            }
        }
        
        if (!empty($stacks['popup_timing'])) {
            foreach ($stacks['popup_timing']['controls'] as $ckey => $control) {
                $element->update_control($control['name'], array('dynamic' => false));
            }
        }
        
        /*$controls = $element->get_controls();        
        foreach ($controls as $ckey => $controls) {
            $controls = self::_remove_dynamic_tags($controls);
            $element->update_control($ckey, $controls);
        }*/
    }
    public static function _remove_dynamic_tags($controls) {
        if (!empty($controls)) {

            foreach ($controls as $key => $control) {
                if ($key != 'dynamic') {
                    if (is_array($control)) {
                        $controls[$key] = self::_remove_dynamic_tags($control);
                    }
                }
            }

            if (!empty($controls['type']) && !is_array($controls['type']) && in_array($controls['type'], self::$types)) {
                $controls_manager = \Elementor\Plugin::$instance->controls_manager;
                $control = $controls_manager->get_control($controls['type']);
                if ($control) {
                    $dynamic = $control->delete_setting('dynamic');                    
                }
            }
        }
        return $controls;
    }

    public function _section_before_render($element) {
        $settings = $element->get_settings_for_display();
        $frontend_settings = $element->get_frontend_settings();
        if (empty($frontend_settings['background_video_link']) && $settings['background_video_link']) {
            ob_start();
        }
    }

    public function _section_after_render($element) {
        $settings = $element->get_settings_for_display();
        $frontend_settings = $element->get_frontend_settings();

        if (empty($frontend_settings['background_video_link']) && $settings['background_video_link']) {
            $content = ob_get_clean();
            if (strpos($content, 'background_video_link') === false) {
                $content = str_replace('&quot;background_background&quot;:&quot;video&quot;', '&quot;background_background&quot;:&quot;video&quot;,&quot;background_video_link&quot;:&quot;' . wp_slash($settings['background_video_link']) . '&quot;', $content);
            }
            echo $content;
        }
    }

}
