<?php

namespace EAddonsForElementor\Core\Traits;

/**
 * @author francesco
 */
trait Data {
    
    public static $image_extensions = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png', 'webp' );
    public static $last_filters = array('implode', 'explode', 'date', 'date_i18n', 'get_the_author_meta', 'str_replace', 'preg_replace', 'get_field');

    /**
     * Split a string by a string
     * <p>Returns an array of strings, each of which is a substring of <code>string</code> formed by splitting it on boundaries formed by the string <code>delimiter</code>.</p>
     * @param string $delimiter <p>The boundary string.</p>
     * @param string $string <p>The input string.</p>
     * @param int $limit <p>If <code>limit</code> is set and positive, the returned array will contain a maximum of <code>limit</code> elements with the last element containing the rest of <code>string</code>.</p> <p>If the <code>limit</code> parameter is negative, all components except the last -<code>limit</code> are returned.</p> <p>If the <code>limit</code> parameter is zero, then this is treated as 1.</p>
     * @param string $format <p>Perform a function an chunk, use functions like trim, intval, absint.</p>
     * @return array <p>Returns an <code>array</code> of <code>string</code>s created by splitting the <code>string</code> parameter on boundaries formed by the <code>delimiter</code>.</p><p>If <code>delimiter</code> is an empty <code>string</code> (""), <b>explode()</b> will return <b><code>FALSE</code></b>. If <code>delimiter</code> contains a value that is not contained in <code>string</code> and a negative <code>limit</code> is used, then an empty <code>array</code> will be returned, otherwise an <code>array</code> containing <code>string</code> will be returned.</p>
     */
    public static function explode($string = '', $delimiter = ',', $limit = PHP_INT_MAX, $format = null) {
        //$string = '45.68174362, 5.91081238'; $delimeter = ','; $limit = PHP_INT_MAX; $format = 'trim';
        /*if ($limit == PHP_INT_MAX) {
            $limit = -1;
        }*/
        if (empty($delimiter) || !is_string($delimiter)) {
            $delimiter = ',';
        }
        if (is_null($string)) {
            $string = [];
        }
        if (is_numeric($string)) {
            $string = array($string);
        }
        if (is_string($string)) {
            $tmp = array();
            if ($delimiter == PHP_EOL) {
                $string = preg_split( "/\\r\\n|\\r|\\n/", $string, $limit );
            } else {
                $string = explode($delimiter, $string, $limit);
            }
            
            $string = array_map('trim', $string);
            foreach ($string as $value) {
                if ($value != '') {
                    $tmp[] = $value;
                }
            }
            $string = $tmp;
        }
        if (!empty($string) && is_array($string) && $format) {
            $string = array_map($format, $string);
        }
        //var_dump($string); die();
        return $string;
    }

    /**
     * Join array elements with a string
     * <p>Join array elements with a <code>glue</code> string.</p><p><b>Note</b>:</p><p><b>implode()</b> can, for historical reasons, accept its parameters in either order. For consistency with <code>explode()</code>, however, it may be less confusing to use the documented order of arguments.</p>
     * @param string $glue <p>Defaults to an empty string.</p>
     * @param array $pieces <p>The array of strings to implode.</p>
     * @param bool $listed <p>Return array as a list, maybe use it with empty glue.</p>
     * @return string <p>Returns a string containing a string representation of all the array elements in the same order, with the glue string between each element.</p>
     */
    public static function implode($pieces = array(), $glue = ', ', $listed = false) {
        $string = '';
        if (is_string($pieces)) {
            $string = $pieces;
        }
        if (!empty($pieces) && is_array($pieces)) {
            if ($listed) {
                $string .= (is_string($listed)) ? '<' . $listed . '>' : '<ul>';
            }
            $i = 0;
            foreach ($pieces as $av) {
                if ($listed) {
                    $string .= '<li>';
                }
                if (is_object($av)) {
                    $av = self::to_string($av);
                }
                if (is_array($av)) {
                    $string .= self::implode($av, $glue, $listed);
                } else {
                    if ($i) {
                        $string .= $glue;
                    }
                    $string .= $av;
                }
                if ($listed) {
                    $string .= '</li>';
                }
                $i++;
            }
            if ($listed) {
                $string .= (is_string($listed)) ? '</' . $listed . '>' : '</ul>';
            }
        }
        return $string;
    }

    /**
     * Maybe JSON Decode — Decodes a JSON string if valid
     *
     * @param  string $json
     * @param  bool   $associative
     * @param  int    $depth
     * @param  bitmask$flags
     *
     * @return array
     */
    public static function maybe_json_decode($json, $associative = null, $depth = null, $flags = null) {
        return self::json_validate($json) ? json_decode($json, $associative) : $json;
    }

    /**
     * Test if given object is a JSON string or not.
     *
     * @param  mixed $json
     *
     * @return bool
     */
    public static function json_validate($json) {
        return is_string($json) && is_array(json_decode($json, true)) && json_last_error() === JSON_ERROR_NONE;
    }
    
    public static function maybe_date_convert($date, $format = 'Y-m-d') {
        if (is_string($date) && strlen($date) == 8) {
            $y = substr($date, 0, 4);
            $m = substr($date, 4, 2);
            $d = substr($date, 6, 2);
            $date = str_replace('Y', $y, $format);
            $date = str_replace('m', $m, $date);
            $date = str_replace('d', $d, $date);
        }
        return $date;
    }
    
    public static function maybe_media($value = null, $tag = null) {
        if ($tag && is_object($tag)) {
            if (!$tag->is_data) return $value;
        }
        if (is_string($value)) {
            $value = trim($value);
        }
        // for MEDIA Control
        $thumbnail_id = false;
        if (is_numeric($value) || is_int($value)) {
            $thumbnail_id = intval($value);
        }
        //var_dump($value);
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            $thumbnail_id = self::url_to_postid($value);
        }
        //var_dump($thumbnail_id); die();
        if ($thumbnail_id) {
            $media = get_post($thumbnail_id);
            if (!$media || $media->post_type != 'attachment') {
                return $value;
            }
            $image_data = [
                'id' => $thumbnail_id,
                'url' => $media->guid,
            ];
            return $image_data;
        }        
        
        // check if is an image
        if ($value && is_string($value)) {
            $check = wp_check_filetype( $value );
            if ( !empty( $check['ext'] ) ) {        
                if (in_array( $check['ext'], self::$image_extensions, true )) {
                    $image_data = [
                        'url' => $value,
                        'id' => 0,
                    ];
                    return $image_data;
                }
            }
        }
        
        // maybe something for GALLERY?

        return $value;
    }

    public static function adjust_data($value, $single = true) {
        if (!empty($value)) {
            if (is_array($value)) {
                if ($single === true || count($value) == 1) {
                    return self::adjust_data(reset($value), $single);
                }
            }
            return $value;
        }
        return '';
    }

    public static function strip_tag($tag, $content = '') {
        $content = preg_replace('/<' . $tag . '[^>]*>/i', '', $content);
        $content = preg_replace('/<\/' . $tag . '>/i', '', $content);
        return $content;
    }

    public static function array_search_key_multi($arr = [], $key = '') {
        $val = false;
        if (is_array($arr)) {
            foreach ($arr as $akey => $val) {
                if ($akey == $key) { 
                    return $val;
                } else {
                    $sub = self::array_search_key_multi($val, $key);
                    if ($sub !== false) { return $sub; }
                }
            }
        }
        return $val;
    }

    public static function get_array_value($val = [], $keys = []) {
        if (is_array($keys) && !empty($keys)) {
            $key = array_shift($keys);
            if (!isset($val[$key])) {
                return false;
            } else {
                $val = $val[$key];
                if (is_array($val)) {
                    $val = self::get_array_value($val, $keys);
                }
            }
        }
        return $val;
    }

    public static function get_object_method($obj, $method, $params = array()) {
        $value = false;
        $reflection = new \ReflectionMethod(get_class($obj), $method);
        if ($reflection->isStatic()) {
            if (!empty($params)) {
                $value = $obj::{$method}($params);
            } else {
                $value = $obj::{$method}();
            }
        } else {
            if (!empty($params)) {
                $value = $obj->{$method}($params);
            } else {
                $value = $obj->{$method}();
            }
        }
        return $value;
    }

    public static function to_string($data, $listed = false) {
        if (is_object($data)) {
            switch (get_class($data)) {
                case 'WP_Term':
                    return $data->name;
                case 'WP_Post':
                    return $data->post_title;
                case 'WP_User':
                    return $data->display_name;
                case 'WP_Comment':
                    return $data->comment_content;
                default:
                    $data = (array) $data;
            }
        }
        if (is_array($data)) {
            if (!empty($data['post_title'])) {
                return $data['post_title'];
            }
            if (!empty($data['display_name'])) {
                return $data['display_name'];
            }
            if (!empty($data['name'])) {
                return $data['name'];
            }
            if (!empty($data['comment_content'])) {
                return $data['comment_content'];
            }            
            if (count($data) == 1) {
                $first = reset($data);
                return self::to_string($first);
            }
            return self::implode($data, ', ', $listed);
        }
        return $data;
    }

    public static function empty($source, $key = false) {
        if (is_array($source)) {
            $source = array_filter($source);
        }
        if ($key) {
            return \Elementor\Utils::is_empty($source, $key);
        }
        return empty($source);
    }

    public static function get_field_category($key, $value = null) {

        $categories = self::get_dynamic_tags_categories();
        //var_dump($categories); die();
        //"base" "text" "url" "image" "media" "post_meta" "gallery" "number" "color"
        $category = 'base';
        $type = false;

        // ACF
        if (self::is_plugin_active('acf')) {
            $field = \EAddonsForElementor\Core\Utils\Acf::get_acf_field($key);
            if (!empty($field['type'])) {
                $type = $field['type'];
            }
        }

        // JET
        if (self::is_plugin_active('jet-engine')) {
            $field = \EAddonsForElementor\Core\Utils\Jet::get_jet_field($key);
            if (!empty($field['type'])) {
                $type = $field['type'];
            }
        }

        // PODS
        if (self::is_plugin_active('pods')) {
            $field = get_page_by_path($key, OBJECT, '_pods_field');
            if ($field) {
                $type = get_post_meta($field->ID, 'type', true);
            }
        }

        switch ($type) {
            case "text":
            case "textarea":
            case "email":
            case "password":
            case "wysiwyg":
            case "message":
            case "select":
            case "radio":
            case "checkbox":
            case 'html':
            case 'iconpicker':
                $category = 'text';
                break;

            case "number":
            case "range":
                $category = 'number';
                break;

            case "image":
                $category = 'image';
                break;

            case 'media':
            case "file":
            case "oembed":
                $category = 'media';
                break;

            case "url":
            case "link":
            case "page_link":
                $category = 'url';
                break;

            case "color_picker":
            case 'colorpicker':
                $category = 'color';
                break;

            case "gallery":
                $category = 'gallery';
                break;

            case "checkbox":
            case "button_group":
            case "true_false":
            case 'switcher':

            case "post_object":
            case "relationship":
            case 'posts':
            case "taxonomy":
            case "user":

            case "google_map":

            case 'date':
            case 'time':
            case 'datetime-local':
            case "date_picker":
            case "date_time_picker":
            case "time_picker":

            case "accordion":
            case "tab":
            case "group":
            case "repeater":
            case "flexible_content":
            case "clone":
            default:
            //$category = 'text';
        }

        if (!$type && !empty($value)) {

            $category = 'text';

            if (is_numeric($value)) {
                $category = 'number';
            }

            if (substr($value, 0, 4) == 'http') {
                $category = 'url';
            }

            if (filter_var($value, FILTER_VALIDATE_EMAIL) !== false) {
                $category = 'text';
            }
        }

        return $category;
    }

    public static function apply_filters($value = false, $filters_string = '') {
        if (is_string($filters_string)) {
            $filters_string = self::get_dynamic_data($filters_string, 'value', $value);
            $filters_raw = explode(PHP_EOL, $filters_string);
        }
        if (is_array($filters_string)) {
            $filters_raw = $filters_string;
        }

        $filters = array();
        foreach ($filters_raw as $afilter) {
            if (!is_numeric($afilter) && !intval($afilter) > 0) {
                $afilterTmp = explode('(', $afilter, 2);
                if (count($afilterTmp) == 2) {
                    $parameter_string = substr(end($afilterTmp), 0, -1);
                    $separator = ',';
                    if (substr($parameter_string, 0, 1) == '"') {
                        if (substr_count($parameter_string, '"') > 2) {
                            $separator = '",';
                        } else {
                            $separator = false;
                        }
                    }
                    if (substr($parameter_string, 0, 1) == "'") {
                        if (substr_count($parameter_string, "'") > 2) {
                            $separator = "',";
                        } else {
                            $separator = false;
                        }
                    }
                    if ($separator) {
                        $parameters = explode($separator, $parameter_string);
                    } else {
                        $parameters = array($parameter_string);
                    }
                    $parameters = self::remove_quote($parameters);
                    $parameters = array_filter($parameters, function ($k) {
                        return $k || in_array($k, array(' ', '0', 0, 'false'));
                    });
                    if (empty($parameters)) {
                        $parameters[] = $parameter_string;
                        $parameters = self::remove_quote($parameters);
                    }
                    $kfilter = reset($afilterTmp);
                    $filters[$kfilter] = $parameters;
                } else {
                    $filters[$afilter] = array(); // no params
                }
            }
        }
        //var_dump($filters);
        // APPLY FILTERS
        if (!empty($filters)) {
            // https://www.w3schools.com/Php/php_ref_string.asp
            // https://www.php.net/manual/en/ref.strings.php
            foreach ($filters as $afilter => $parameters) {

                if ($afilter == 'eval') {
                    echo 'EVAL is not a secure function, please do NOT use it.';
                    continue;
                }

                if ($afilter == 'prepend') {
                    $string = reset($parameters);
                    if ($string) {
                        if (is_string($value) || is_numeric($value)) {
                            $value = $string . $value;
                        }
                        if (is_array($value)) {
                            foreach ($value as $key => $aval) {
                                $value[$key] = $string . $aval;
                            }
                        }
                    }
                    continue;
                }
                if ($afilter == 'append') {
                    $string = reset($parameters);
                    if ($string) {
                        if (is_string($value) || is_numeric($value)) {
                            $value .= $string;
                        }
                        if (is_array($value)) {
                            foreach ($value as $key => $aval) {
                                $value[$key] = $aval . $string;
                            }
                        }
                    }
                    continue;
                }

                if ($afilter && is_callable($afilter) && $value != '') {

                    if (empty($parameters)) {
                        $value = $afilter($value);
                        //$value = call_user_func_array($afilter, $value);
                    } else {
                        if ($afilter == 'date') {
                            $afilter = 'date_i18n';
                        }
                        if (in_array($afilter, self::$last_filters)) {
                            // these functions require the value in the last position
                            $parameters[] = $value;
                        } else {
                            array_unshift($parameters, $value);
                        }
                        $value = call_user_func_array($afilter, $parameters);
                    }
                }
            }
        }
        return $value;
    }
    
    public static function set_array_value($arr = [], $keys = [], $val = false) {
        if (is_array($keys) && !empty($keys)) {            
            if (!is_array($arr)) { $arr = []; }
            $key = array_shift($keys);
            if (!isset($arr[$key])) { $arr[$key] = []; }
            $arr[$key] = self::set_array_value($arr[$key], $keys, $val);
            if (is_null($arr[$key]) && is_null($val)) { unset($arr[$key]); }
        } else { return $val; }
        return $arr;
    }

    public static function remove_quote($parameters = array()) {
        if (!empty($parameters)) {
            foreach ($parameters as $pkey => $pvalue) {
                //$parameters[$pkey] = trim($pvalue);
                if ((substr($pvalue, 0, 1) == '"' && substr($pvalue, -1) == '"') || (substr($pvalue, 0, 1) == "'" && substr($pvalue, -1) == "'")) {
                    $parameters[$pkey] = substr($pvalue, 1, -1); // remove quote
                }
                if ((substr($pvalue, 0, 1) == '"' && substr($pvalue, -1) != '"') || (substr($pvalue, 0, 1) == "'" && substr($pvalue, -1) != "'")) {
                    $parameters[$pkey] = substr($pvalue, 1); // remove quote
                }
            }
        }
        return $parameters;
    }

    public static function remove_quotes($results) {
        if (is_array($results)) {
            foreach ($results as $key => $value) {
                $results[$key] = self::remove_quotes($value);
            }
        } else {
            $results = stripslashes($results);
            if (substr($results, 0, 1) == '"') {
                $results = substr($results, 1);
            }
            if (substr($results, -1, 1) == '"') {
                $results = substr($results, 0, -1);
            }
        }
        return $results;
    }
    
    

}
