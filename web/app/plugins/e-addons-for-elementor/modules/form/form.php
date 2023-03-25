<?php

namespace EAddonsForElementor\Modules\Form;

use EAddonsForElementor\Base\Module_Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Form extends Module_Base {

    public function __construct() {
        parent::__construct();
        
        add_action("elementor/widget/render_content", array($this, 'update_form'), 10, 2);
        
    }
    
    public function update_form($content, $widget) {
        if ($widget->get_name() == 'form') {       
            $post_id = get_the_ID();
            if ($post_id) {
                $pis = '<input type="hidden" name="post_id" value="';
                $pie = '"/>';
                $cp = '<input type="hidden" name="current_post_id" value="'.$post_id.'"/>';
                $tmp = explode($pis, $content, 2);
                if (count($tmp) == 2) {
                    //replace it, not want the template ID
                    list($template_id, $more) = explode($pie, end($tmp), 2);
                    if ($template_id != $post_id) {
                        $content = str_replace($pis, $cp.$pis, $content);
                        //$content = reset($tmp).$pis.$post_id.$pie.$more;
                    }
                } else {
                    // add it ?

                }
            }
                    
            $queried_object = get_queried_object();
            $queried_object_id = get_queried_object_id();
            if (!empty($queried_object) && is_object($queried_object)) {
                $qi = '<input type="hidden" name="queried_id"';
                if ( !is_singular() ) {
                    if ($queried_object_id ) {
                        $fi = '<input type="hidden" name="form_id"';
                        $qi = $qi.' value="'.$queried_object_id.'"/>';
                        $content = str_replace($fi, $qi.$fi, $content);
                    }
                }
                $type = get_class($queried_object);
                $qt = '<input type="hidden" name="queried_type" value="'.$type.'"/>';
                $content = str_replace($qi, $qt.$qi, $content);
            }
        }
        return $content;
    }

}
