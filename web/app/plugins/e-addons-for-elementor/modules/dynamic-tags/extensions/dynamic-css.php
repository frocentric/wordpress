<?php

namespace EAddonsForElementor\Modules\DynamicTags\Extensions;

use EAddonsForElementor\Core\Utils;
use Elementor\Controls_Manager;
use EAddonsForElementor\Base\Base_Global;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Dynamic_Css extends Base_Global {

    public function get_pid() {
        return 1302;
    }

    public function __construct() {
        parent::__construct();
        add_action('elementor/element/parse_css', [$this, 'parse_css'], 10, 2);
    }

    public function get_name() {
        return 'e-dynamic-css';
    }

    public function get_icon() {
        return 'eadd-enhanced-dynamic-tags';
    }
    
    /**
     * After element parse CSS.
     *
     * Fires after the CSS of the element is parsed.
     *
     * @since 1.2.0
     *
     * @param Post         $this    The post CSS file.
     * @param Element_Base $element The element.
     */
    public function parse_css($css_post, $element) {
        //var_dump($css_post->get_post_id().'-'.$element->get_id());
        //if (empty(self::$parsed[$css_post->get_post_id()])) {
            
            $element_settings = $element->get_settings_for_display();
            //$settings = $element->get_settings();
            if (!empty($element_settings["__dynamic__"])){
                $controls = $css_post->get_style_controls( $element, null, $element->get_parsed_dynamic_settings() );
                //if ('slides' == $element->get_name()) echo '<pre>';var_dump($element_settings);var_dump($controls);echo '</pre>'; die();
                $controls_dynamic = [];
                foreach($element_settings["__dynamic__"] as $dkey => $dshort) {
                    if (!empty($controls[$dkey])) {
                        $controls_dynamic[$dkey] = $controls[$dkey];
                        /*if ($dkey == 'background_color_b') {
                            if (!empty($element_settings['background_gradient_position'])) {
                                $controls_dynamic['background_gradient_position'] = $controls['background_gradient_position'];
                            }
                            if (!empty($element_settings['background_gradient_angle'])) {
                                $controls_dynamic['background_gradient_angle'] = $controls['background_gradient_angle'];
                            }
                            //echo '<pre>';var_dump($controls_dynamic);echo '</pre>'; die();
                        }*/
                    }
                }
                //echo '<pre>';var_dump($controls_dynamic);echo '</pre>'; die();
                $css_post->add_controls_stack_style_rules( $element, $controls_dynamic, $element_settings, [ '{{ID}}', '{{WRAPPER}}' ], [ $element->get_id(), $css_post->get_element_unique_selector( $element ) ] );
            }
            //self::$parsed[$css_post->get_post_id()] = true;
        //}
    }

}
