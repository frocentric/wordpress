<?php

namespace EAddonsForElementor\Core\Traits;

use EAddonsForElementor\Core\Utils;

/**
 * @author francesco
 */
trait Pagination {
    
    
    public static function get_current_page_num() {
        if (!empty($_REQUEST['page'])) return intval($_REQUEST['page']);
        if (!empty($_REQUEST['paged'])) return intval($_REQUEST['paged']);
        return max(1, get_query_var('paged'), get_query_var('page'));
    }
    
    //
    public static function get_linkpage($i) {
        if (!is_singular() || is_front_page()) {
            return get_pagenum_link($i);
        }

        // Based on wp-includes/post-template.php:957 `_wp_link_page`.
        global $wp_rewrite;
        $id_page = get_the_ID();
        $post = get_post();
        $query_args = [];
        $url = get_permalink($id_page);

        if ($i > 1) {
            if ('' === get_option('permalink_structure') || in_array($post->post_status, ['draft', 'pending'])) {
                $url = add_query_arg('page', $i, $url);
            } elseif (get_option('show_on_front') === 'page' && (int) get_option('page_on_front') === $post->ID) {
                $url = trailingslashit($url) . user_trailingslashit("$wp_rewrite->pagination_base/" . $i, 'single_paged');
            } else {
                $url = trailingslashit($url) . user_trailingslashit($i, 'single_paged');
            }
        }

        if (is_preview()) {
            if (( 'draft' !== $post->post_status ) && isset($_GET['preview_id'], $_GET['preview_nonce'])) {
                $query_args['preview_id'] = wp_unslash($_GET['preview_id']);
                $query_args['preview_nonce'] = wp_unslash($_GET['preview_nonce']);
            }

            $url = get_preview_post_link($post, $query_args, $url);
        }
        
        if (!wp_doing_ajax() && !empty($_GET)) {
            foreach($_GET as $gkey => $gvalue) {
                $url = add_query_arg($gkey, $gvalue, $url);
            }
        }

        return $url;
    }
    
    static public function clean_wp_query() {
        global $wp_query;
        $wp_query_copy = wp_json_encode( $wp_query );
        $wp_query_copy = json_decode($wp_query_copy, true);
        if (!empty($wp_query_copy['posts'])) {
            $wp_query_copy['posts'] = wp_list_pluck($wp_query_copy['posts'], 'ID');
        }
        if (!empty($wp_query_copy['queried_object']['post_content'])) {
            $wp_query_copy['queried_object']['post_content'] = '';
        }
        if (!empty($wp_query_copy['post']['post_content'])) {
            $wp_query_copy['post']['post_content'] = '';
        }

        // strip technical db info
        if (!empty($wp_query_copy['request'])) {
            $wp_query_copy['request'] = '';
        }
        if (!empty($wp_query_copy['tax_query']['primary_table'])) {
            $wp_query_copy['tax_query']['primary_table'] = '';
        }
        if (!empty($wp_query_copy['meta_query']['primary_table'])) {
            $wp_query_copy['meta_query']['primary_table'] = '';
        }
        if (!empty($wp_query_copy['meta_query']['meta_table'])) {
            $wp_query_copy['meta_query']['meta_table'] = '';
        }
        return wp_json_encode($wp_query_copy);
    }
    
    static public function set_wp_query() {
        global $post, $wp_query;
        if (!empty($_POST['wp_query'])) {
            if (is_array($_POST['wp_query'])) {
                $pre_wp_query = $_POST['wp_query'];
            } else {
                $pre_wp_query = stripslashes($_POST['wp_query']);
                $pre_wp_query = json_decode($pre_wp_query, true);
            }
            //$wp_query = $pre_wp_query;
            //$wp_query->query = json_decode(json_encode($pre_wp_query->query), true);
            //$wp_query->query_vars = json_decode(json_encode($pre_wp_query->query_vars), true);
            foreach ($pre_wp_query as $key => $value) {
                $wp_query->{$key} = $value;
            }
        }
        //var_dump($wp_query);
        if ($wp_query->is_singular) {
            // force $post in ajax
            $queried_id = empty($_POST['queried_id']) ? $wp_query->queried_object_id : absint($_POST['queried_id']);                    
            $post = get_post($queried_id);
            $wp_query->queried_object = $post;
            $wp_query->queried_object_id = $queried_id;
            if (Utils::is_plugin_active('woocommerce')) {
                global $product;
                $product = wc_get_product($queried_id);
            }
        }
        
        if ($wp_query->is_archive && ($wp_query->is_tax || $wp_query->is_category || $wp_query->is_tag)) {
            if (!empty($wp_query->queried_object_id)) {
                // force $term in ajax
                $queried_id = absint($wp_query->queried_object_id);                    
                $term = get_term($queried_id);
                $wp_query->queried_object = $term;
            }
        }
        
        $wp_query->get_posts();
        //var_dump($wp_query);
  
    }
    
    static public function fix_ajax_pagination($content, $element, $fields = array()) {
        $pagination = false;
        if ($element) {
            //var_dump($element->get_name());
            switch ($element->get_name()) {
                case 'posts':
                case 'archive-posts':
                    //case 'wc-archive-products':
                    $pagination = $element->get_settings_for_display('pagination_type');
                    break;
                case 'e-query-users':
                case 'e-query-terms':
                case 'e-query-media':
                case 'e-query-products':
                case 'e-query-posts':
                    $pagination = $element->get_settings_for_display('pagination_enable') || $element->get_settings_for_display('infiniteScroll_enable');
                    break;
            }
        }
        
        $nav_start = '<nav ';
        if ($pagination || strpos($content, $nav_start) !== false) { //'role="navigation"'
            //wp_json_encode($fields);
            $params = '';
            $form_id = \EAddonsForElementor\Core\Utils\Form::get_form_id();
            if ($form_id) {                
                $fields['form_id'] = $form_id;
                if (empty($fields['post_id']) && !empty($_POST['post_id'])) {
                    $fields['post_id'] = $_POST['post_id'];
                }
                if (empty($fields['queried_id']) && !empty($_POST['queried_id'])) {
                    $fields['queried_id'] = $_POST['queried_id'];
                }
                foreach ($fields as $fkey => $field) {
                    if ($field) {
                        if ($params) {
                            $params .= '&';
                        } else {
                            $params .= '?';
                        }
                        $field = Utils::to_string($field);
                        $params .= $fkey . '=' . urlencode($field);
                    }
                }
            }
            $current_url = Utils::get_current_url();
            $base_url = Utils::get_current_url(true);

            if (wp_doing_ajax()) {
                $current_url = $ajax_url = admin_url('admin-ajax.php');
                if (strpos($content, $current_url) === false) {
                    list($archive, $navigation) = explode($nav_start, $content, 2);
                    list($pre, $href) = explode('href="', $navigation, 2);
                    list($current_url, $more) = explode('"', $href, 2);
                    $tmp = explode('?', $current_url, 2);
                    if (count($tmp) > 1) {
                        $tmp2 = explode('/', end($tmp));
                        if (count($tmp2) > 1) {
                            $current_url = reset($tmp).'?'.reset($tmp2);
                        }
                    }
                    //var_dump('Current: '.$current_url);
                }
                if (empty($_POST['url']) && empty($_POST['referrer'])) {
                    self::set_wp_query();
                    $queried_id = empty($_POST['queried_id']) ? get_queried_object_id() : $_POST['queried_id'];
                    $queried_object = get_queried_object();
                    if ($queried_object && is_object($queried_object) && get_class($queried_object) == 'WP_Term') {
                        $base_url = get_term_link($queried_id);
                    } else {
                        $base_url = get_permalink($queried_id);        
                    }
                    
                } else {
                    if (!empty($_POST['url'])) {
                        $base_url = esc_url_raw($_POST['url']);
                    } else {
                        $base_url = esc_url_raw($_POST['referrer']);
                        $tmp = explode('?', $base_url, 2);
                        $base_url = reset($tmp);
                    }
                    //var_dump(Utils::get_current_page_num());
                    if (Utils::get_current_page_num() > 1) {
                        $base_url = remove_query_arg('page', $base_url);
                        $base_url = remove_query_arg('paged', $base_url);
                        $tmpp = explode('?', $base_url);
                        $tmp = explode('/page/', $base_url);
                        if (count($tmp) > 1) {
                            $base_url = reset($tmp).'/';
                            if (count($tmpp) > 1) {
                                $base_url .= '?'.end($tmpp);
                            }
                        }
                    }
                    
                }
                if ($ajax_url == $current_url || strpos($current_url, $base_url) === false) {
                    $content = str_replace($current_url . '/', $base_url, $content);
                    $content = str_replace($current_url, $base_url, $content);
                }
            }
            //var_dump($_POST);
            //var_dump('Current: '.$current_url);
            //var_dump('Base: '.$base_url);
            //die();

                
            $tmp = explode($nav_start, $content);
            // fix pagination link
            if (count($tmp) == 2) {
                $pre = reset($tmp);
                $nav = end($tmp);
                $base_href = 'href="' . $base_url;
                $current_href = 'href="' . $current_url;
                $tmp = explode($base_href, $nav); 
                $quote = '"';
                //var_dump($nav);
                if (count($tmp) == 1) {
                    $quote = "'";
                    $base_href = "href='" . $base_url;
                    $tmp = explode($base_href, $nav); 
                }
                if (count($tmp) > 1) {
                    $contentmp = '';
                    foreach ($tmp as $key => $href) {
                        if ($key) {
                            list($get, $other) = explode($quote, $href, 2);
                            
                            // in some cases is mypage/2, so I add the mypage/page/2
                            $tmp = explode('/', $get);
                            $get2 = '';
                            foreach($tmp as $hkey => $value) {
                                if (intval($value)) {
                                    if ($tmp[$hkey-1] != 'page') {
                                        $value = 'page/'.$value;
                                    }
                                }
                                $get2 = $get2 ? $get2.'/'.$value : $get2.$value;
                            }
                            $get = $get2;
                            
                            // add extra form parameters
                            if (strpos($get, 'form_id=') === false) {
                                if (strpos($get, '?') === false) {
                                    $contentmp .= $base_href . $get . $params . $quote . $other;
                                } else {                                        
                                    $contentmp .= $base_href . $get . '&' . ltrim($params, '?') . $quote . $other;
                                }
                            } else {
                                $contentmp .= $base_href . $href;
                            }
                        } else {
                            $contentmp = $href;
                        }
                    }
                    $content = $pre . $nav_start . $contentmp;
                }
            }
        }
        return $content;
    }

}
