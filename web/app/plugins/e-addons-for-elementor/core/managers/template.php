<?php

namespace EAddonsForElementor\Core\Managers;

use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Core\Managers\Assets;

/**
 * Description of template-manager
 *
 * @author fra
 */
class Template {

    public static $templates = [];

    public function __construct() {

        add_action("elementor/frontend/the_content", array($this, 'fix_template_class'));
        add_action("elementor/frontend/widget/after_render", array($this, 'render_style'));
        add_action("elementor/frontend/container/after_render", array($this, 'render_style'));
        add_action("elementor/frontend/column/after_render", array($this, 'render_style'));
        add_action("elementor/frontend/section/after_render", array($this, 'render_style'));

        add_action('wp_ajax_e_elementor_template', array($this, 'ajax_template'));
        add_action('wp_ajax_nopriv_e_elementor_template', array($this, 'ajax_template'));

        add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueue_editor_assets']);
        
        /*add_filter('elementor/frontend/builder_content_data', function($data, $post_id) {
            //var_dump($post_id);
            //var_dump(get_the_ID());
            if ($post_id != get_the_ID()) {
                $data['post_id'] = get_the_ID();
            }
            return $data;
        }, 10, 2);*/
        
        if (wp_doing_ajax()) {
            add_action( 'elementor/frontend/before_get_builder_content', [$this, 'restore_post'], 999, 2);
            add_action( "elementor/css-file/dynamic/enqueue", [$this, 'restore_post'], 999 );
        }
    }
    
    
    public function restore_post($document, $is_excerpt = null ) {
        //restore post object
        $queried_object = get_queried_object();
        if ($queried_object && is_object($queried_object) && get_class($queried_object) == 'WP_Post') {
            global $post;
            $post = $queried_object;
        }        
    }

    /**
     * Enqueue editor assets
     *
     * @since 1.0.1
     *
     * @access public
     */
    public function enqueue_editor_assets() {
        //wp_enqueue_script('e-addons-editor-template');
    }

    public static function get_builder_content($post_id, $with_css = false) {
        $content = '';
        if (Utils::is_plugin_active('wpml')) {
            $post_id = apply_filters('wpml_object_id', $post_id, 'elementor_library', true);
        }
        $is_elementor = get_post_meta($post_id, '_elementor_edit_mode', true);
        if ($is_elementor) {
            //var_dump($with_css);
            $content = self::get_builder_content_for_display($post_id, $with_css);
            $content = self::fix_template_class($content, $post_id);
        } else {
            if ($post_id) {
                $post = get_post($post_id);
                if ($post) {
                    \Elementor\Plugin::instance()->frontend->remove_content_filter();
                    $content = apply_filters('the_content', $post->post_content);
                    \Elementor\Plugin::instance()->frontend->add_content_filter();
                }
            }
        }
        return $content;
    }
    
    public static function get_builder_content_for_display($post_id, $with_css = false) {
        //$document = \Elementor\Plugin::instance()->documents->get_current();
        //var_dump($document);
        ob_start();
        $content = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($post_id, $with_css);
        echo $content;
        $tmp = ob_end_clean();
        if (is_string($tmp)) {
            $content = $tmp;
        }
        if (empty($content)) {
            ob_start();
            echo \Elementor\Plugin::instance()->frontend->get_builder_content($post_id, $with_css);
            $tmp = ob_end_clean();
            if (is_string($tmp)) {
                    $content = $tmp;
            }
        }
        
        /*
        $document = \Elementor\Plugin::$instance->documents->get_doc_for_frontend( $post_id );
        
        //cache for Query Widgets
        if (empty(self::$templates[$post_id])) {
            $data = $document->get_elements_data();
            self::$templates[$post_id] = $data;
        }
        $data = self::$templates[$post_id];
        
        ob_start();
        $css_file = \Elementor\Core\Files\CSS\Post::create( $post_id );
        if ( $with_css ) {    
            $css_file->print_css();
        } else {
            $css_file->enqueue();
        }
        $document->print_elements_with_wrapper( $data );
        $content = ob_get_clean();
        */
        
        return $content;
    }
 
    public function ajax_template() {
        if (!empty($_POST['template_id']) && is_numeric($_POST['template_id'])) {
            $tpl_id = absint($_POST['template_id']);
            $args = array();
            if (!empty($_POST['post_id']) && is_numeric($_POST['post_id'])) {
                $args['post_id'] = absint($_POST['post_id']);
            }


            if (!empty($_POST['user_id']) && is_numeric($_POST['user_id'])) {
                $args['user_id'] = absint($_POST['user_id']);
            }
            if (!empty($_POST['term_id']) && is_numeric($_POST['term_id'])) {
                $args['term_id'] = absint($_POST['term_id']);
            }
            if (!empty($_POST['author_id']) && is_numeric($_POST['author_id'])) {
                $args['author_id'] = absint($_POST['author_id']);
            }
            if (empty($args['post_id']) && !empty($_POST['post_href'])) {
                $args['post_id'] = url_to_postid($_POST['post_href']);
            }
            if (empty($args['post_id']) && !empty($_SERVER['HTTP_REFERER'])) {
                $args['post_id'] = url_to_postid($_SERVER['HTTP_REFERER']);
            }
            if (!$tpl_id) {
                if (!empty($args['post_id'])) {
                    $tpl_id = $args['post_id'];
                    $args['ajax'] = true;
                }
            }
            if (empty($args['css']) && !empty($_POST['css'])) {
                $args['css'] = (bool) $_POST['css'];
            }
            if (empty($args['title']) && !empty($_POST['title'])) {
                $args['title'] = (bool) $_POST['title'];
            }

            if ($tpl_id) {
                echo self::e_template($tpl_id, $args);
            }
        }

        wp_die();
    }

    /**
     * Execute the Shortcode
     *
     * @since 1.0.1
     *
     * @access public
     */
    public static function e_template($tpl_id, $args = array()) {

        if (empty($tpl_id) || !intval($tpl_id)) {
            return false;
        }

        $tpl_id = intval($tpl_id);

        if ($tpl_id) {
            global $wp_query, $post, $authordata, $user, $current_user, $term;

            $initial_queried_object = $wp_query->queried_object;
            $initial_queried_object_id = $wp_query->queried_object_id;
            $initial_wp_query = clone $wp_query;

            if (!empty($args['post_id']) && intval($args['post_id'])) {
                //var_dump($args['post_id']);
                $initial_post = $post;
                $post = get_post($args['post_id']);
                if ($post) {
                    $wp_query->queried_object = $post;
                    $wp_query->queried_object_id = $args['post_id'];
                    if (wp_doing_ajax()) {
                        $wp_query->is_singular = true; // Form Fix
                    }
                }
            }
            if (!empty($args['author_id']) && intval($args['author_id'])) {
                $initial_author = $authordata;
                $authordata = get_user_by('ID', $args['author_id']);
                if ($authordata) {
                    $wp_query->queried_object = $authordata;
                    $wp_query->queried_object_id = $args['author_id'];
                    $wp_query->is_author = true;
                }
            }
            if (!empty($args['user_id']) && intval($args['user_id'])) {
                $initial_user = $current_user;
                $user = $current_user = get_user_by('ID', $args['user_id']);
                if ($user) {
                    $wp_query->queried_object = $user;
                    $wp_query->queried_object_id = $args['user_id'];
                }
            }

            if (!empty($args['term_id']) && intval($args['term_id'])) {
                $term = get_term($args['term_id']);
                if ($term) {
                    $wp_query->queried_object = $term;
                    $wp_query->queried_object_id = $args['term_id'];
                    $wp_query->is_singular = false;
                    $wp_query->is_category = $wp_query->is_tag = $wp_query->is_tax = false;
                    switch ($term->taxonomy) {
                        case 'category':
                            $wp_query->is_category = true;
                            break;
                        case 'post_tag':
                            $wp_query->is_tag = true;
                            break;
                        default:
                            $wp_query->is_tax = true;
                    }
                    //var_dump($wp_query); die();
                }
            }

            $with_css = (!empty($args['css']) && ($args['css'] == 'true' || $args['css'] === true));
            if ((\Elementor\Plugin::$instance->editor->is_edit_mode() || wp_doing_ajax()) && (!empty($args['css']) && $args['css'] !== false)) {
                $with_css = true;
            }

            if (!Utils::is_preview(true) && !empty($args['ajax'])) {
                $with_css = true;
            }
            
            $tpl_html = false;

            if (!Utils::is_preview(true) && !empty($args['loading']) && $args['loading'] == 'lazy') {
                $params = '';
                $attributes = wp_slash(wp_json_encode($args));
                foreach ($args as $akey => $value) {
                    if (in_array($attributes, array('post_id', 'user_id', 'term_id', 'author_id'))) {
                        $key = str_replace('_id', '', $akey);
                        $params .= ' data-' . $key . '="' . $value . '"';
                    }
                }
                $tpl_html = '<div class="e-elementor-template-placeholder" data-id="' . $tpl_id . '"' . $params . '>';
                ob_start();
                ?>
                <script>
                    (function ($) {
                        jQuery(window).on("elementor/frontend/init", function () {
                            elementorFrontend.hooks.addAction("frontend/element_ready/widget", function ($scope) {
                                jQuery('.e-elementor-template-placeholder').each(function () {
                                    elementorFrontend.waypoint(jQuery(this), function (dir) {
                                        var e_data = {
                                            "action": "e_elementor_template",
                                            "template_id": jQuery(this).data('id'),
                                        };
                                        jQuery.ajax({
                                            url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                            dataType: "html",
                                            context: jQuery(this),
                                            type: "POST",
                                            data: e_data,
                                            error: function () {
                                                console.log("error");
                                            },
                                            success: function (data, status, xhr) {
                                                //console.log(data);
                                                jQuery(this).html(data);
                                                //jQuery(this).children(".elementor").addClass("e-elementor-template-loaded").unwrap().hide().fadeIn("slow");
                                            },
                                        });
                                    }, {offset: "100%", triggerOnce: true});
                                });
                            });
                        });
                    })(jQuery);
                </script>
                <?php
                $tpl_script = ob_get_clean();
                $tpl_html .= $tpl_script;
                $tpl_html .= '</div>';
            } else {
                $tpl_html = self::get_builder_content($tpl_id, $with_css);
            }

            if (!empty($args['title']) && $args['title']) {
                $tpl_html = preg_replace('/data-elementor-id="/', 'data-title="' . wp_slash($post->post_title) . '" data-elementor-id="', $tpl_html, 1);
            }

            if (!empty($args['post_id'])) {
                $post = $initial_post;
            }
            if (!empty($args['author_id'])) {
                $authordata = $initial_author;
            }
            if (!empty($args['user_id'])) {
                $user = $initial_user;
                $current_user = $initial_user;
            }
            
            if (wp_doing_ajax()) {
                // fix for forms like Gravity
                $referer  = wp_get_referer();
                $tpl_html = str_replace('action="/wp-admin/admin-ajax.php"', 'action=""', $tpl_html);
                $tpl_html = str_replace("action='/wp-admin/admin-ajax.php'", "action=''", $tpl_html);
            }
            
            $wp_query = $initial_wp_query;
            wp_reset_query();
            $wp_query->queried_object = $initial_queried_object;
            $wp_query->queried_object_id = $initial_queried_object_id;
            //wp_reset_postdata();
            
            /* if (wp_doing_ajax()) {
              $tpl_html .= $this->render_style($element);
              } */
            
            //var_dump($tpl_html); 
            return $tpl_html;
        }
    }

    public static function fix_template_class($content = '', $tpl_id = 0) {
        if ($content) {
            $tpl_html_id = Utils::get_template_from_html($content);
            if ($tpl_html_id) {
                if ($tpl_id && $tpl_id != $tpl_html_id) {
                    $content = str_replace('class="elementor elementor-' . $tpl_html_id . ' ', 'class="elementor elementor-' . $tpl_id . ' ', $content);
                } else {
                    $tpl_id = $tpl_html_id;
                }
            }
            if ($tpl_id) {
                $template_type = get_post_meta($tpl_id, '_elementor_template_type', true);
                if (in_array($template_type, array('page', 'section', 'single', 'single-post', 'single-page', 'product', 'popup'))) {
                    $q_o = self::get_queried_object();
                    $element_class = 'e-' . $q_o['type'] . '-' . $q_o['id'];
                    
                    global $wp_query;
                    if (!empty($wp_query->in_repeater_loop)) {
                        global $e_widget_query;
                        if (is_object($e_widget_query)) {
                            if (!empty($e_widget_query->counter)) {
                                $element_class .= ' e-repeater-row-'.$e_widget_query->counter;
                            }
                        }
                    }
                    
                    //if (Utils::is_preview(true) || strpos($content, $element_class) === false || wp_doing_ajax()) {
                    if (strpos($content, ' ' . $element_class) === false) {
                        $content = str_replace('class="elementor elementor-' . $tpl_id . ' ', 'class="elementor elementor-' . $tpl_id . ' ' . $element_class . ' ', $content);
                        $content = str_replace('class="elementor elementor-' . $tpl_id . '"', 'class="elementor elementor-' . $tpl_id . ' ' . $element_class . '"', $content);
                        $content = preg_replace('/data-elementor-id="/', 'data-' . $q_o['type'] . '-id="' . $q_o['id'] . '" data-obj-id="' . $q_o['id'] . '" data-elementor-id="', $content, 1);
                    }
                }
            }
        }
        return $content;
    }

    public static function get_queried_object() {
        $q_o = array('obj' => get_queried_object());
        $q_o['id'] = get_queried_object_id();
        $q_o['type'] = Utils::get_queried_object_type();
        if ($q_o['type'] == 'post') {
            $q_o['id'] = get_the_ID();
        }
        if (Utils::is_plugin_active('advanced-custom-fields-pro')) {
            if (acf_get_loop('active')) {
                $q_o['id'] = get_row_index();
                $q_o['type'] = 'row';
            }
        }
        return $q_o;
    }

    public function render_style($element) {
        self::render_element_style($element);
    }

    public static function render_element_style($element) {
        if (is_array($element)) {
            //var_dump($element);
            $element = reset($element);
        }
        $settings = $element->get_settings_for_display();
        $element_id = $element->get_id();
        $element_controls = $element->get_controls();
        $q_o = self::get_queried_object();

        if (!empty($settings['__globals__'])) {
            // TODO
            // "reveal_bgcolor" => "globals/colors?id=33547690"
            /*
              $kit = \Elementor\Plugin::instance()->kits_manager->get_active_kit_for_frontend();
              $kit_settings = $kit->get_settings_for_display();
              foreach ($settings['__globals__'] as $skey => $sglob) {
              $id = end(explode('id=', $sglob));
              foreach ( $kit_settings as $setting) {
              foreach ( $setting as $index => $item ) {
              if ($id == $item['_id']) {
              $settings[$skey] = $item['color'];
              }
              }
              }
              }
             */
        }
        //var_dump($settings['__dynamic__']);
        if (!empty($settings['__dynamic__'])) {
            $style = '';
            global $wp_query, $e_widget_query;
            $all_devices_no_desktop = \Elementor\Plugin::$instance->breakpoints->get_active_devices_list();
            unset($all_devices_no_desktop[array_search('desktop', $all_devices_no_desktop)]); // remove desktop
            foreach (array_keys($settings['__dynamic__']) as $dynamic) {
                
                $tmp = explode('_', $dynamic);
                $device = end($tmp);
                $devices = array('desktop' => $dynamic);
                
                if (in_array($device, $all_devices_no_desktop)) {
                    $devices = array($device => $dynamic);
                }
                foreach ($devices as $device => $device_value) {
                    $selector = '.elementor:is(.e-' . $q_o['type'] . '-' . $q_o['id'].',.e-loop-item-' . $q_o['id'].')';

                    if (!empty($wp_query->in_repeater_loop)) {                        
                        if (is_object($e_widget_query)) {
                            if (!empty($e_widget_query->counter)) {
                                $selector .= '.e-repeater-row-'.$e_widget_query->counter;
                            }
                        }
                    }
                    
                    $selector = ($device == 'desktop') ? $selector : '[data-elementor-device-mode="' . $device . '"] ' . $selector;
                    $wrapper = $selector . ' .elementor-element.elementor-element-' . $element_id;
                    if (!empty($element_controls[$device_value])) {
                        if (!empty($element_controls[$dynamic]['selectors'])) {
                            foreach ($element_controls[$dynamic]['selectors'] as $skey => $svalue) {
                                $control_selector = str_replace('{{WRAPPER}}', $wrapper, $skey);
                                if (!empty($settings[$device_value])) {
                                    $setting_value = '';
                                    if (is_array($settings[$device_value])) {
                                        if (!empty($settings[$device_value]['url'])) {
                                            $setting_value = str_replace('{{URL}}', $settings[$device_value]['url'], $svalue);
                                        }
                                    } else {
                                        $setting_value = str_replace('{{VALUE}}', $settings[$device_value], $svalue);
                                    }
                                    $extra_setting_value = '';
                                    if ($device_value == 'background_image') {
                                        if ($setting_value) {
                                            $tmp = explode('"', $setting_value);
                                            if (count($tmp) == 3) {
                                                $extra_setting_value .= '--e-bg-lazyload:url("'.$tmp[1].'");';
                                            }
                                            if (!empty($settings['background_position'])) {
                                                $extra_setting_value .= 'background-position: '.$settings['background_position'].';';
                                            }
                                            if (!empty($settings['background_size'])) {
                                                $extra_setting_value .= 'background-size: '.$settings['background_size'].';';
                                            }
                                            if (!empty($settings['background_attachment'])) {
                                                $extra_setting_value .= 'background-attachment: '.$settings['background_attachment'].';';
                                            }
                                            if (!empty($settings['background_repeat'])) {
                                                $extra_setting_value .= 'background-repeat: '.$settings['background_repeat'].';';
                                            }
                                            if (!empty($settings['background_image_size'])) {
                                                // fix for Extended
                                                $image_url = '';
                                                $image_id = $settings[$device_value]['id'];
                                                $size = $settings['background_image_size'];
                                                if ($size && $size != 'full') {
                                                    if ($size == 'custom') {
                                                        $image_url = \Elementor\Group_Control_Image_Size::get_attachment_image_src($image_id, 'background', $settings);
                                                    } else {
                                                        $image = wp_get_attachment_image_src($image_id, $size);
                                                        if (!empty($image)) {
                                                            $image_url = reset($image);
                                                        }
                                                    }
                                                }
                                                if ($image_url) {
                                                    $setting_value = str_replace('{{URL}}', $image_url, $svalue);
                                                }
                                            }
                                        }
                                    }
                                    $style .= ($setting_value) ? $control_selector . '{' . $setting_value . $extra_setting_value . '}' : $setting_value;
                                }
                            }
                        }
                    }
                }
            }
            if (!empty($style)) {
                if (!wp_doing_ajax()) {
                    //echo 'NOAJAX';
                    $style = Assets::enqueue_style('template-dynamic-' . $element->get_id() . '-inline', $style);
                } else {
                    //echo 'AJAX';
                }
                if (!empty($style)) {
                    echo '<style>' . $style . '</style>';
                }
            }
        }
    }

}
