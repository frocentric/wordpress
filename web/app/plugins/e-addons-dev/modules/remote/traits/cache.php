<?php

namespace EAddonsDev\Modules\Remote\Traits;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Repeater;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Core\Utils\Query as Query_Utils;

/**
 * Description of label
 *
 * @author fra
 */
trait Cache {
    
    public static $headers = [];
    public static $cache = [];
    
    
    public function add_cache_options() {
        $this->add_control(
                'data_cache', [
            'label' => esc_html__('Enable Cache', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'description' => esc_html__('If the remote resource is slow to respond or unreachable, it is best to enable the cache. To force the update, disable, save and reactivate.', 'e-addons'),
            'separator' => 'before',
                ]
        );
        $this->add_control(
                'data_cache_maxage', [
            'label' => esc_html__('Cache life', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => 86400,
            'description' => esc_html__('How long the cache is valid in seconds (86400 = 1 day)', 'e-addons'),
            'condition' => [
                'data_cache!' => '',
            ],
                ]
        );
    }
    
    public function add_remote_options() {
        $this->add_control(
                'require_authorization', [
            'label' => esc_html__('Authorization needed', 'e-addons'),
            'separator' => 'before',
            'type' => Controls_Manager::SWITCHER,
                ]
        );
        $this->add_control(
                'authorization_user', [
            'label' => esc_html__('Basic HTTP User', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'condition' => [
                'require_authorization!' => '',
            ],
                ]
        );
        $this->add_control(
                'authorization_pass', [
            'label' => esc_html__('Basic HTTP Password', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'condition' => [
                'require_authorization!' => '',
            ],            
                ]
        );
        $this->add_control(
                'authorization_header', [
            'label' => esc_html__('Custom Header Authorization', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'Bearer <token>',
            'label_block' => true,
            'condition' => [
                'require_authorization!' => '',
            ],
            'separator' => 'after',
                ]
        );
        
        $repeater_headers = new \Elementor\Repeater();
        $repeater_headers->add_control(
                'header_key', [
            'label' => esc_html__('Header Key', 'e-addons'),
            'placeholder' => 'Content-Type',
            'type' => Controls_Manager::TEXT,
                ]
        );
        $repeater_headers->add_control(
                'header_value', [
            'label' => esc_html__('Header Value', 'e-addons'),
            'placeholder' => 'application/json',
            'type' => Controls_Manager::TEXT,
                ]
        );
        $this->add_control(
                'headers', [
            'label' => esc_html__('Extra Headers', 'e-addons'),
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => $repeater_headers->get_controls(),
            'title_field' => '{{{ header_key }}}: {{{ header_value }}}',
            'prevent_empty' => false,
                ]
        );

        $this->add_control(
                'connect_port', [
            'label' => esc_html__('Connection Port', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'placeholder' => '80',
                ]
        );
        
        $this->add_control(
                'connect_timeout', [
            'label' => esc_html__('Connection Timeout', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'description' => esc_html__('Maximum time in seconds that your server waits for a response from the destination server', 'e-addons'),
                ]
        );
        
        $this->add_control(
                'disable_sslverify', [
            'label' => esc_html__('Ignore SSL Certificate', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
                ]
        );
    }

    public function maybe_get_cache() {
        $settings = $this->get_settings_for_display();
        //$settings['_id'] = $this->get_id();
        
        $cache = false;
                
        $current_post_id = get_the_ID();
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            if (!$settings['data_cache']) {
                delete_post_meta($current_post_id, '_elementor_data_cache_content_' . $this->get_id());
                delete_post_meta($current_post_id, '_elementor_data_cache_refresh_' . $this->get_id());
            }
        } else {
            if ($settings['data_cache']) {
                // check cache                
                $data_cache_content = get_post_meta($current_post_id, '_elementor_data_cache_content_' . $this->get_id(), true);
                $data_cache_refresh = intval(get_post_meta($current_post_id, '_elementor_data_cache_refresh_' . $this->get_id(), true));
                if ($data_cache_content && $data_cache_refresh + $settings['data_cache_maxage'] > time()) {
                    $cache = stripslashes($data_cache_content);
                    $cache = base64_decode($cache);
                }
            }
        }
        if (!$cache) {
            $cache = self::get_remote($settings);

            if ($settings['data_cache'] && $cache) {
                if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                    // store in cache
                    $cache_encoded = base64_encode($cache);
                    update_post_meta($current_post_id, '_elementor_data_cache_content_' . $this->get_id(), $cache_encoded);
                    update_post_meta($current_post_id, '_elementor_data_cache_refresh_' . $this->get_id(), time());
                }
            }
        }

        return $cache;
    }

    public static function get_remote($settings) {

        if (empty($settings['url']) || !filter_var($settings['url'], FILTER_VALIDATE_URL)) {
            return false;
        }

        // get fresh version
        $args = self::set_headers($settings);
        $url = self::get_url($settings);

        //var_dump($settings['url']);
        //$settings['url'] = '"'.$settings['url'].'"';
        //$settings['url'] = str_replace('&', '\&', $settings['url']);
        
        /*
        // create curl resource
        $ch = curl_init();
        // set url
        curl_setopt($ch, CURLOPT_URL, $url);
        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $output contains the output string
        $output = curl_exec($ch);
        var_dump($output);
        // close curl resource to free up system resources
        curl_close($ch); 
        */
        
        /* $tmp = explode('?', $url);
          if (count($tmp) > 1) {
          $url = reset($tmp);
          $params = explode('&', end($tmp));
          if (!empty($params)) {
          foreach($params as $aparam) {
          $tmparam = explode('=', $aparam);
          if (count($tmparam) == 2) {
          $args['body'][reset($tmparam)] = end($tmparam);
          } else {
          $args['body'][$aparam] = true;
          }
          }
          }
          } */
        //var_dump($url);

        if (!empty($settings['data_dsl'])) {
            $parsed = self::parse_dsl($settings['data_dsl']);
            
            if (!empty($parsed['command'])) {
                $args['method'] = $parsed['method'];
                //var_dump($command);
                $url .= (substr($url, -1) == '/') ? $parsed['command'] : '/' . $parsed['command'];  
                
                /*if (empty($parsed['json']) && !empty($settings['pagination_enable'])) {                    
                    $parsed['json'] = '{}';
                }*/
                
                if (!empty($parsed['json'])) {
                    $args['method'] = 'POST';                    
                    
                    //$keys = array_keys($data);
                    //$first_key = reset($keys);
                    //var_dump($first_key);
                    //$data = wp_json_encode($data[$first_key]);
                    //$args['body'][$first_key] = $data;
                    //$settings['data_post'][] = ['data_post_key' => '', 'data_post_value' => $data];
                    if (!empty($settings['pagination_enable'])) {
                        $data = json_decode($parsed['json'], true);
                        $rows_per_page = empty($settings['rows_per_page']) ? get_option('posts_per_page') : intval($settings['rows_per_page']);
                        if ($rows_per_page > 0) {
                            $paged = Utils::get_current_page_num();
                            $data["from"] = $rows_per_page * ($paged - 1);
                            $data["size"] = $rows_per_page;
                        }
                        //var_dump($data);
                        $data = json_encode($data);
                    }
                    //var_dump($data);
                    $args['body'] = $data;
                    //var_dump($data);
                    $args['headers']['Content-Type'] = 'application/json';
                }
            }
        }
        
        //$args['headers']['User-Agent'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:100.0) Gecko/20100101 Firefox/100.0';
        //$args['headers']['Accept'] = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8';
        //$args['headers']['Accept-Language'] = 'en-US,en;q=0.5';
        //$args['headers']['Accept-Encoding'] = 'gzip, deflate, br';
        //$args['headers']['DNT'] = '1';
        //$args['headers']['Connection'] = 'keep-alive';
        //$args['headers']['Upgrade-Insecure-Requests'] = '1';
        //$args['headers']['Sec-Fetch-Dest'] = 'document';
        //$args['headers']['Sec-Fetch-Mode'] = 'navigate';
        //$args['headers']['Sec-Fetch-Site'] = 'none';
        //$args['headers']['Sec-Fetch-User'] = '?1';
        
        //var_dump($args); die();

        if (empty($settings['data_post'])) {
            $response = wp_remote_get($url, $args);
        } else {
            foreach ($settings['data_post'] as $api_post) {
                if ($api_post['data_post_key']) {
                    $pvalue = Utils::maybe_json_decode($api_post['data_post_value'], true);
                    if (is_array($pvalue)) {
                        $args['headers']['Content-Type'] = 'application/json';
                    }
                    if ($pvalue === 'true' || $pvalue === 'TRUE') {
                        $pvalue = true;
                    }
                    if ($pvalue === 'false' || $pvalue === 'FALSE') {
                        $pvalue = false;
                    }
                    $args['body'][$api_post['data_post_key']] = $pvalue;
                }
            }            
            if ($args['headers']['Content-Type'] == 'application/json') {
                if (!empty($args['body'])) {
                    $args['body'] = json_encode($args['body']);
                }
            }
            $response = wp_remote_post($url, $args);
        }
        
        /*
        echo $url;
        echo '<pre>';var_dump($args);echo '</pre>';
        echo '<pre>';var_dump($response);echo '</pre>';
        */
        
        $cache = false;
        if ($response !== false && !is_wp_error($response)) {
            self::$headers[$settings['url']] = wp_remote_retrieve_headers($response);
            
            $cache = wp_remote_retrieve_body($response);
            $code = wp_remote_retrieve_response_code($response);
            //echo '<pre>';var_dump($cache);echo '</pre>';

            $cache = str_replace('\\\n', '', $cache);

            if ($code < 300) {

                // fill all document _id reference
                if (!empty($settings['data_load_id'])) {
                    $tmp = json_decode($cache, true);
                    $data = $tmp;
                    $depth = empty($settings['data_load_id_depth']) ? 1 : intval($settings['data_load_id_depth']);
                    for ($i = 0; $i < $depth; $i++) {
                        $data = self::replace_document_ids($data, $settings); //, $settings['archive_path']);
                    }
                    $cache = json_encode($data);
                }
                
                if (!empty($settings['data_wp_load_fields'])) {
                    $data = json_decode($cache, true);
                    $data = self::replace_wp_ids($data, $settings); //, $settings['archive_path']);
                    $cache = json_encode($data);
                }
            } else {
                $cache = false;
            }
        }

        return $cache;
    }
    
    public static function parse_dsl($dsl) {
        $dsl = Utils::get_dynamic_data($dsl);
        $parsed = array('command' => '/_search', 'method' => 'GET', 'json' => '');
        $dsl = str_replace(array("\n", "\r"), ' ', $dsl);
        $tmp = explode(' ', $dsl);
        if (count($tmp) > 1) {
            $parsed['method'] = reset($tmp);
            $parsed['command'] = trim($tmp[1]);
            //var_dump($command);           
            $tmp = explode('{', $dsl, 2);
            if (count($tmp) == 2) {
                $args['method'] = 'POST';
                list ($other, $json) = $tmp;
                $parsed['json'] = '{' . $json;
            }
        }
        return $parsed;
    }

    public static function get_url($settings) {
        $url = $settings['url'];
        $url = str_replace('amp;', '', $url);

        if (!empty($settings['connect_port'])) {
            $port = intval($settings['connect_port']);
            $tmp = explode('/', $url);
            if (count($tmp) > 2) {
                $url = array_shift($tmp) . '/' . array_shift($tmp) . '/' . array_shift($tmp) . ':' . $port . implode('/', $tmp);
            } else {
                $url .= ':' . $port;
            }
        }

        if (!empty($settings['method'])) {
            
            if ($settings['method'] == 'WP') {
            
                if (!empty($settings['data_wp_post_type'])) {
                    $url = self::get_wp_json_url($url, $settings['data_wp_post_type']);
                }
                
                $rows_per_page = empty($settings['rows_per_page']) ? get_option('posts_per_page') : intval($settings['rows_per_page']);
                if (!empty($rows_per_page)) {
                    if (strpos($url, 'per_page=') === false) {
                        $url = add_query_arg('per_page', $rows_per_page, $url);
                    }
                }
                
                $page = Utils::get_current_page_num();
                if ($page > 1) {
                    $url = add_query_arg('page', $page, $url);
                }

            }
            
            if (!empty($settings['data_get'])) {
                if (!$settings['method'] || in_array($settings['method'], array('WP'))) {
                    foreach ($settings['data_get'] as $api_get) {
                        if ($api_get['data_get_key']) {
                            $url = add_query_arg($api_get['data_get_key'], $api_get['data_get_value'], $url);
                        }
                    }
                }
            }
            
        }
        
        $url = trim($url);
        
        return $url;
    }
    
    public static function replace_wp_ids($cache = '', $settings = array()) {
        if (!empty($cache)) {
            if (is_array($cache)) {
                foreach ($cache as $key => $sub) {
                    if ($key && in_array($key, $settings['data_wp_load_fields']) && self::is_document_id($sub)) {                              
                        $cache[$key] = self::get_wp_obj_by_id($sub, $settings, $key);
                    } else {
                        if (is_array($sub)) {
                            $cache[$key] = self::replace_wp_ids($sub, $settings);
                        }
                    }
                }
            }
        }
        return $cache;
    }

    public static function set_headers($settings, $args = array()) {
        //$args['headers']['Content-Type'] = 'application/json; charset=utf-8';
        if (!empty($settings['connect_timeout'])) {
            $args['timeout'] = intval($settings['connect_timeout']);
        }
        if (!empty($settings['disable_sslverify'])) {
            //CURLOPT_SSL_VERIFYHOST
            //CURLOPT_SSL_VERIFYPEER
            $args['sslverify'] = false;
        }
        if (!empty($settings['require_authorization'])) {
            if (!empty($settings['authorization_header'])) {
                $args['headers']['Authorization'] = $settings['authorization_header'];
            }
            if (!empty($settings['authorization_user']) && !empty($settings['authorization_pass'])) {
                $args['headers']['Authorization'] = 'Basic ' . base64_encode($settings['authorization_user'] . ':' . $settings['authorization_pass']);
            }
        }
        if (!empty($settings['headers'])) {
            foreach ($settings['headers'] as $aheader) {
                if ($aheader['header_key']) {
                    $args['headers'][$aheader['header_key']] = $aheader['header_value'];
                }
            }
        }
        
        return $args;
    }

    public static function replace_document_ids($cache = '', $settings = array(), $path = '') {
        if (!empty($cache)) {
            if (is_array($cache)) {
                foreach ($cache as $key => $sub) {
                    $pkey = empty($path) ? $key : $path . '.' . $key;
                    $keys = Utils::explode($settings['data_load_id_fields'], PHP_EOL);
                    if (count($keys) == 1) {
                        $keys = Utils::explode(reset($keys));
                    }
                    $skey = $pkey;
                    
                    if (!empty($settings['archive_path'])) {
                        $archive_path = $settings['archive_path'].'.';
                        if (substr($pkey, 0, strlen($archive_path)) == $archive_path) {                    
                            $skey = substr($pkey, strlen($archive_path));
                            $tmp = explode('.', $skey);
                            if (is_numeric(reset($tmp))) {
                                array_shift($tmp);
                            }
                            $skey = implode('.', $tmp);
                        }
                    }
                    //var_dump($pkey); //var_dump($keys);
                    //if (in_array($pkey, $keys)) {
                    $match = false;
                    foreach ($keys as $akey) {
                        /*if (str_ends_with($pkey, $akey)) {
                            $match = true;
                            break;
                        }*/
                        //var_dump($skey);
                        if ($skey == $akey) {                            
                            $match = true;
                        } else {
                            $tmp = explode('.', $skey);
                            foreach ($tmp as $tkey => $tval) {
                                if (is_numeric($tval)) {
                                    $tmp[$tkey] = '*';
                                }
                            }
                            $rkey = implode('.', $tmp);
                            //var_dump($rkey);
                            if ($rkey == $akey) {
                                $match = true;
                            }
                        }
                    }

                    if ($match && self::is_document_id($sub)) {
                        $cache[$key] = self::get_document_by_id($sub, $settings);
                    } else {
                        if (is_array($sub)) {
                            $cache[$key] = self::replace_document_ids($sub, $settings, $pkey);
                        }
                    }
                }
            }
        }
        return $cache;
    }

    public static function is_document_id($id) {
        // is a string or a number (or an array of them)
        if (is_array($id)) {
            foreach ($id as $aid) {
                if (is_array($aid)) {
                    return false;
                }
            }
        }
        return true;
    }
    
    public static function get_wp_json_url($url, $type = '') {
        if (!empty($type)) {
            $jbase = '/wp-json/wp/v2/';
            if (strpos($url, $jbase) === false) {
                $tmp = explode('?', $url);
                $url = array_shift($tmp).$jbase.$type;
            } else {
                $tmp = explode('?', $url);
                $tmp2 = explode($jbase, array_shift($tmp));
                $url = reset($tmp2).$jbase.$type;
            }
            if (!empty($tmp)) {
                $url .= '?'.reset($tmp);
            }  
        }
        return $url;
    }

    public static function get_wp_obj_by_id($id, $settings = array(), $key = '') {
        // fill all document _id reference
        if (is_array($id)) {
            foreach ($id as $skey => $aid) {
                $id[$skey] = self::get_wp_obj_by_id($aid, $settings, $key);
            }
        } else {          
            if (intval($id)) {
                $args = self::set_headers($settings);
                $type = false;            
                switch($key) {
                    case 'author':
                        $type = 'users';
                        break;
                    case 'categories':
                        $type = 'categories';
                        break;
                    case 'tags':
                        $type = 'tags';
                        break;
                    case 'featured_media':
                        $type = 'media';
                        break;
                    case 'posts':
                        $type = 'posts';
                        break;
                }
                if ($type) {
                    $base_url = self::get_wp_json_url($settings['url'], $type);
                    $url = $base_url.'/'.$id;
                    //var_dump($url);
                    $cache = false;
                    if (empty(self::$cache[$url])) {
                        $response = wp_remote_get($url, $args);
                        if ($response && wp_remote_retrieve_response_code($response) == 200) {                
                            $cache = wp_remote_retrieve_body($response);
                            self::$cache[$url] = $cache;
                        }
                    } else {
                        $cache = self::$cache[$url];
                    }
                    if ($cache) {
                        return json_decode($cache, true);
                    }
                }
            }
        }
        return $id;
    }
    
    
    public static function get_document_by_id($id, $settings = array()) {
        // fill all document _id reference
        if (is_array($id)) {            
            foreach ($id as $key => $aid) {
                $id[$key] = self::get_document_by_id($aid, $settings);
            }
        } else {            
            $dsl = self::get_archive_index($settings) . '/_doc/' . $id;
            $args = self::set_headers($settings);
            $url = self::get_url($settings);
            $url .= substr($url, -1) == '/' ? $dsl : '/' . $dsl;
            //var_dump($url);
            $cache = false;
            if (empty(self::$cache[$id])) {
                $response = wp_remote_get($url, $args);
                if ($response && wp_remote_retrieve_response_code($response) == 200) {                
                    $cache = wp_remote_retrieve_body($response);
                    self::$cache[$id] = $cache;
                }
            }  else {
                $cache = self::$cache[$id];
            }
            if ($cache) {
                return json_decode($cache, true);
            }
        }
        return $id;
    }

    public static function get_archive_index($settings) {
        $archive = '';
        if (!empty($settings['data_dsl'])) {
            $tmp = explode(' ', $settings['data_dsl']);
            $tmp = Utils::explode($tmp[1], '/');
            $archive = reset($tmp);
        }
        if (!$archive) {
            $tmp = Utils::explode($settings['url'], '/');
            if (count($tmp) > 2) {
                $archive = $tmp[3];
            }
        }
        return $archive;
    }
    
    

}
