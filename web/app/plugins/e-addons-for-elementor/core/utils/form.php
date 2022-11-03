<?php

namespace EAddonsForElementor\Core\Utils;

use EAddonsForElementor\Core\Utils;

/**
 * Description of Form Utils
 *
 */
class Form {
    
    public static $fields = [];

    public static function get_form_data($record = false, $raw = false, $extra = false, $settings = array()) {
        
        $fields = [];
        
        if (!empty($record)) {
            $raw_fields = $record->get('fields');
            //var_dump($raw_fields); die();
            foreach ($raw_fields as $id => $field) {
                if ($raw || empty($field['value'])) { // fix html tags content (ex: "<iframe src='xxx'></iframe>")
                    $fields[$id] = $field['raw_value'];
                } else {
                    $fields[$id] = $field['value'];
                }
            }
        }

        //var_dump($_POST);
        if (!empty($_POST['form_fields'])) {
            foreach ($_POST['form_fields'] as $id => $field) {
                $ide = str_replace('e-', '', $id);
                if (!isset($fields[$id]) && !isset($fields[$ide])) {                    
                    $fields[$id] = $field;
                }
            }
        }

        foreach ($fields as $id => $field) {
            $tmp = explode('__value', $id); // media field
            if (count($tmp) == 2) {
                $oid = reset($tmp);
                if (empty($fields[$oid])) {
                    $fields[$oid] = $fields[$id];
                    unset($fields[$id]);
                }
            }
        }

        if ($extra) {
            $extra_fields = self::get_form_extra_data($record, $fields, $settings);
            foreach ($extra_fields as $key => $value) {
                $fields[$key] = $value;
            }
        }

        global $e_form;
        if (!empty($e_form) && is_array($e_form)) {
            foreach ($fields as $key => $value) {
                $e_form[$key] = $value;
            }
        } else {
            $e_form = $fields; // for form twig
        }
        // set them globally in _POST var
        foreach ($fields as $key => $value) {
            $_POST[$key] = $value;
        }

        //$post_id = !empty($_POST['queried_id']) ? absint($_POST['queried_id']) : absint($_POST['post_id']);
        // force post for Dynamic Tags and Widgets
        global $post, $wp_query;
        $post_id = absint($_POST['post_id']);
        if ($post_id && empty($wp_query->queried_object)) {
            $post = get_post($post_id);
            if ($post) {
                $wp_query->queried_object = $post;
                $wp_query->queried_object_id = $post_id;
            }
        }

        //var_dump($fields); die();
        return $fields;
    }

    public static function get_form_extra_data($record, $fields = null, $settings = null) {

        $user_id = get_current_user_id();
        $queried_id = $post_id = get_queried_object_id();

        if (is_object($record)) {
            $form_name = $record->get_form_settings('form_name');
        } else {
            $form_name = !empty($settings['form_name']) ? $settings['form_name'] : '';
        }

        // get current page
        $document = get_queried_object();
        if ($document && get_class($document) == 'WP_Post') {
            $post_id = $document->ID;
        }
        if (isset($_POST['post_id'])) {
            $post_id = absint($_POST['post_id']);
            $document = get_post($post_id);
        }

        $referrer = isset($_POST['referrer']) ? esc_url($_POST['referrer']) : $_SERVER['HTTP_REFERER'];
        if ($referrer) {
            $queried_id_tmp = url_to_postid($referrer);
            if ($queried_id_tmp) {
                $post = get_post($queried_id_tmp);
                if ($post) {
                    $queried_id = $queried_id_tmp;
                }
            }
        }
        if (isset($_POST['queried_id'])) {
            $queried_id = absint($_POST['queried_id']);
            $post = get_post($queried_id);
        }

        return [
            'queried_id' => $queried_id,
            'post_id' => $post_id,
            'user_id' => $user_id,
            'ip_address' => \ElementorPro\Core\Utils::get_client_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'referrer' => $referrer,
            'form_name' => $form_name,
            'form_id' => sanitize_title($_POST['form_id']),
        ];
    }

    public static function do_setting_shortcodes($setting, $fields = array(), $urlencode = false) {
        // Shortcode can be `[field id="fds21fd"]` or `[field title="Email" id="fds21fd"]`, multiple shortcodes are allowed
        if (!empty($fields)) {
            if (strpos($setting, '[field id=') !== false || strpos($setting, '[option id=') !== false) {
                foreach ($fields as $fkey => $fvalue) {
                    
                    if (!is_object($fvalue)) {
                        $fvalue = Utils::to_string($fvalue);
                        if ($urlencode) {
                            $fvalue = urlencode($fvalue);
                        }
                        if (!is_object($fvalue)) {
                            $setting = str_replace('[field id=' . $fkey . ']', $fvalue, $setting);
                            $setting = str_replace("[field id='" . $fkey . "']", $fvalue, $setting);
                            $setting = str_replace("[field id=’" . $fkey . "’]", $fvalue, $setting);		
                            $setting = str_replace('[field id=”' . $fkey . '”]', $fvalue, $setting);
                            $setting = str_replace('[field id="' . $fkey . '"]', $fvalue, $setting);
                            
                            $setting = str_replace('[field id=”' . $fkey . '"]', $fvalue, $setting);
                            $setting = str_replace('[field id="' . $fkey . '”]', $fvalue, $setting);
                            
                            $setting = str_replace('[field id=\"' . $fkey . '\"]', $fvalue, $setting);
                            $setting = str_replace('[field id=&quot;' . $fkey . '&quot;]', $fvalue, $setting);
                            $setting = str_replace('[field id=&#8217;' . $fkey . '&#8217;]', $fvalue, $setting);
                            $setting = str_replace('[field id=&#8221;' . $fkey . '&#8221;]', $fvalue, $setting);
                            
                            $ovalue = self::get_field_option_label($fkey, $fvalue);
                            $setting = str_replace('[option id=”' . $fkey . '”]', $ovalue, $setting);
                            $setting = str_replace('[option id="' . $fkey . '"]', $ovalue, $setting);
                            $setting = str_replace("[option id='" . $fkey . "']", $ovalue, $setting);
                            
                            $lvalue = self::get_field_label($fkey);
                            $setting = str_replace('[label id=”' . $fkey . '”]', $lvalue, $setting);
                            $setting = str_replace('[label id="' . $fkey . '"]', $lvalue, $setting);
                            $setting = str_replace("[label id='" . $fkey . "']", $lvalue, $setting);
                            
                        }
                    }
                }
            }
        }

        $setting = preg_replace_callback('/(\[field[^]]*id="(\w+)"[^]]*\])/', function ($matches) use ($urlencode, $fields) {
            $value = '';
            if (isset($fields[$matches[2]])) {
                $value = $fields[$matches[2]];
            }
            if ($urlencode) {
                $value = urlencode($value);
            }
            return $value;
        }, $setting);

        return $setting;
    }

    public static function options_array($string = '', $val = 'pro') {
        if (is_string($string) && $val == 'flatpickr') {
           $string = str_replace('||', '!!OR!!', $string);
        }
        $arr = Utils::explode($string, PHP_EOL);
        //$arr = preg_split( "/\\r\\n|\\r|\\n/", $string );
        //$arr = array_filter($arr);
        if (count($arr) < 2) {
            $string = nl2br($string);
            $arr = Utils::explode($string, '<br />');
        }
        foreach ($arr as $akey => $astring) {
            $pieces = explode('|', $astring, 2);
            //if (count($pieces) > 1) {
                $reset = trim(reset($pieces));
                $end = trim(end($pieces));
            //}    
            if ($val == 'pro') {
                $arr[$akey] = array('text' => $reset, 'value' => $end);
            }
            if ($val == 'acf') {
                $arr[$akey] = array('text' => $end, 'value' => $reset);
            }
            if ($val == 'flatpickr') {
                if ($reset == $end) {
                    $reset = str_replace('!!OR!!', '||', $reset);
                    $arr[$akey] = $reset;
                } else {
                    $arr[$akey] = array('from' => $reset, 'to' => $end);
                }
            }
            
        }
        return $arr;
    }
    
    public static function array_to_options($arr = [], $val = 'pro', $format = '', $assoc = 'auto') {
        $arr = Utils::explode($arr, PHP_EOL);
        $string = '';
        if (!empty($arr)) {
            if ($assoc == 'auto') {
                $has_values = array_keys($arr) !== range(0, count($arr) - 1);
            } else {
                $has_values = $assoc;
            }
            $i = 1;
            foreach ($arr as $akey => $item) {
                $label = Utils::to_string($item);
                if ($format && is_array($item)) {
                    $label = $format;
                    $tmp = explode('[', $format);
                    foreach ($tmp as $k => $piece) {
                        if ($k) {
                            list($key, $more) = explode(']', $piece);
                            $value = isset($item[$key]) ? $item[$key] : '';
                            $label = str_replace('['.$key.']', $value, $label);
                        }
                    }
                }
                if ($val == 'pro') {
                    $string .= $label;
                    if ($has_values) {
                        $string .= '|'. $akey;
                    }
                }
                if ($val == 'acf') {
                    if ($has_values) {
                        $string .= $akey.' | ';
                    }
                    $string .= $label;
                }
                if ($i < count($arr)) {
                    $string .= PHP_EOL;
                }
                $i++;
            }
        }
        return $string;
    }

    public static function get_field($custom_id, $settings = array()) {
        if (isset(self::$fields[$custom_id])) {
            return self::$fields[$custom_id];
        }
        if (empty($settings)) {
            $form_id = self::get_form_id();
            if ($form_id) {
                $settings = Utils::get_settings_by_element_id($form_id);
            }
        }
        if (!empty($settings['form_fields'])) {
            foreach ($settings['form_fields'] as $afield) {
                if ($afield['custom_id'] == $custom_id) {
                    self::$fields[$custom_id] = $afield;
                    return $afield;
                }
            }
        }
        return false;
    }

    public static function get_field_type($custom_id, $settings = array()) {
        if (empty($settings)) {
            $form_id = self::get_form_id();
            if ($form_id) {
                $settings = Utils::get_settings_by_element_id($form_id);
            }
        }
        if (!empty($settings)) {
            $field = self::get_field($custom_id, $settings);
            if ($field && !empty($field['field_type'])) {
                return $field['field_type'];
            }
        }
        return false;
    }

    /**
     * @param string      $email_content
     * @param Form_Record $record
     *
     * @return string
     */
    public static function replace_content_shortcodes($email_content = '', $record = array(), $line_break = '<br>') {

        $shortcode = '[all-fields]';
        $text = self::get_shortcode_value($shortcode, $email_content, $record, $line_break);
        $email_content = str_replace($shortcode, $text, $email_content);

        $shortcode = '[all-fields|!empty]';
        $text = self::get_shortcode_value($shortcode, $email_content, $record, $line_break, false);
        $email_content = str_replace($shortcode, $text, $email_content);
        
        $shortcode = '[e-fields]';
        $text = self::get_shortcode_value($shortcode, $email_content, $record, $line_break);
        $email_content = str_replace($shortcode, $text, $email_content);
        
        $shortcode = '[e-fields|!empty]';
        $text = self::get_shortcode_value($shortcode, $email_content, $record, $line_break, false);
        $email_content = str_replace($shortcode, $text, $email_content);

        if ($email_content) {
            global $e_form;
            $pdf_form = '[form:pdf]';
            if (strpos($email_content, $pdf_form) !== false) {
                $value = '';
                if (!empty($e_form['pdf']['url'])) {
                    $value = $e_form['pdf']['url'];
                }
                $email_content = str_replace($pdf_form, $value, $email_content);
            }
            $pdf_form = '[form:pdf:';
            if (strpos($email_content, $pdf_form) !== false) {
                $tmp = explode($pdf_form, $email_content);
                foreach ($tmp as $key => $pdf) {
                    if ($key) {
                        list($field, $tmp) = explode(']', $pdf, 2);
                        $value = '';
                        if (!empty($e_form['pdf'][$field])) {
                            $value = $e_form['pdf'][$field];
                        }
                        $email_content = str_replace($pdf_form . $field . ']', $value, $email_content);
                    }
                }
            }
        }

        return $email_content;
    }

    public static function get_shortcode_value($shortcode = '[all-fields]', $email_content = '', $record = array(), $line_break = '<br>', $show_empty = true) {
        $text = '';
        if (false !== strpos($email_content, $shortcode)) {
            $fields = $record;
            if (is_object($record)) {
                $fields = $record->get('fields');
            }
            if (substr($shortcode,0,9) == '[e-fields') {
                foreach($fields as $fkey => $fvalue) {                    
                    $fields[$fkey] = array(
                        'title' => self::get_field_label($fkey, $fkey),
                        'value' => $fvalue ? self::get_field_option_label($fkey, $fvalue) : $fvalue,
                        'type' => self::get_field_type($fkey),
                    );
                }
            }
            foreach ($fields as $fkey => $field) {
                $formatted = '';
                if (is_string($field) || empty($field['type'])) {
                    $value = Utils::to_string($field);
                    if (!$show_empty && empty($value)) {
                        continue;
                    }
                    $formatted = $fkey . ': ' . $value;
                } else {
                    if (!empty($field['title'])) {
                        $formatted = sprintf('%s: %s', $field['title'], $field['value']);
                    } elseif (!empty($field['value'])) {
                        $formatted = sprintf('%s', $field['value']);
                    }
                    if (( 'textarea' === $field['type'] ) && ( '<br>' === $line_break )) {
                        $formatted = str_replace(["\r\n", "\n", "\r"], '<br />', $formatted);
                    }
                    if (!$show_empty && empty($field['value']))
                        continue;
                }
                $text .= $formatted . $line_break;
            }
        }
        return $text;
    }

    public static function get_plain_txt($e_message_content_txt, $line_break = PHP_EOL) {
        $e_message_content_txt = str_replace('</p>', '</p><br /><br />', $e_message_content_txt);
        $e_message_content_txt = str_replace('<br />', $line_break, $e_message_content_txt);
        $e_message_content_txt = str_replace('<br/>', $line_break, $e_message_content_txt);
        $e_message_content_txt = str_replace('<br>', $line_break, $e_message_content_txt);
        $e_message_content_txt = str_replace('\n', $line_break, $e_message_content_txt);
        $e_message_content_txt = strip_tags($e_message_content_txt);
        return $e_message_content_txt;
    }

    public static function get_attachments($fields, $settings, $amail = array(), $e_form_email_content = '', $url = false) {
        $attachments = array();

        if ($e_form_email_content) {
            $pdf_attachment = '[e_form_pdf:attachment]';
            $pdf_form = '[form:pdf]';
            if (strpos($e_form_email_content, $pdf_attachment) !== false 
                || strpos($e_form_email_content, $pdf_form) !== false
                || strpos($e_form_email_content, '[PDF]') !== false) {
                global $e_form;
                if (!empty($e_form['pdf'])) {
                    if ($url) {
                        if (!empty($e_form['pdf']['url'])) {
                            $attachments[] = $e_form['pdf']['url'];
                        }
                    } else {
                        if (!empty($e_form['pdf']['path'])) {
                            $attachments[] = $e_form['pdf']['path'];
                        }
                    }
                }
                $e_form_email_content = str_replace($pdf_attachment, '', $e_form_email_content);
                $e_form_email_content = str_replace($pdf_form, '', $e_form_email_content);
                $e_form_email_content = str_replace('[PDF]', '', $e_form_email_content);
            }
        }

        if (!empty($fields) && is_array($fields)) {
            foreach ($fields as $akey => $adatas) {
                $afield = self::get_field($akey, $settings);
                if ($afield) {
                    if ((empty($amail['e_form_attachments_fields']) || in_array($akey, $amail['e_form_attachments_fields']))) {
                        if ($afield['field_type'] == 'repeater') {
                            $adatas = self::list_to_array($adatas, $afield);
                            foreach ($afield['repeater_fields'] as $sub_field) {
                                $sfield = Form::get_field($sub_field, $settings);
                                foreach ($adatas as $row) {
                                    $attachments = self::add_field_attachments($attachments, $sfield, $row[$sub_field], $url);
                                }
                            }
                        } else {
                            $attachments = self::add_field_attachments($attachments, $afield, $adatas, $url);
                        }
                    }
                }
            }
        }

        if (!empty($amail) && !empty($amail['e_form_attachments_file'])) {
            $media_ids = Utils::explode($amail['e_form_attachments_file']);
            if (!empty($media_ids)) {
                foreach ($media_ids as $attachment_id) {
                    $file_path = get_attached_file($attachment_id);
                    if (!in_array($file_path, $attachments)) {
                        $attachments[] = $file_path;
                    }
                }
            }
        }
        //var_dump($attachments); die();
        return $attachments;
    }
    
    public static function add_field_attachments($attachments, $afield, $adatas, $url) {
        if (in_array($afield['field_type'], array('upload', 'media', 'signature'))) {
            $files = Utils::explode($adatas);
            //var_dump($files); die();
            if (!empty($files)) {
                foreach ($files as $adata) {
                    if (filter_var($adata, FILTER_VALIDATE_URL)) {
                        $file_path = Utils::url_to_path($adata);
                        if (is_file($file_path)) {
                            if ($url) {
                                if (!in_array($adata, $attachments)) {
                                    $attachments[] = $adata;
                                }
                            } else {
                                if (!in_array($file_path, $attachments)) {
                                    $attachments[] = $file_path;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $attachments;
    }

    public static function delete_attachments($fields, $settings) {
        $attachments = self::get_attachments($fields, $settings);
        //var_dump($attachments); die();
        if (!empty($attachments) && is_array($attachments)) {
            foreach ($attachments as $filename) {
                // media as attachment in library
                if (is_numeric($filename)) {
                    $filename = intval($filename);
                    $media = get_post($filename);
                    if ($media) {
                        $filename = Utils::url_to_path($media->guid);
                        if (file_exists($filename)) {
                            wp_delete_attachment($media->ID);
                        }
                    }
                }
                $url = Utils::path_to_url($filename);
                $media = Utils::url_to_postid($url);
                //var_dump($url); var_dump($media); die();
                if ($media) {
                    wp_delete_attachment($media);
                }
                if (file_exists($filename)) {
                    unlink($filename);
                }
            }
        }
    }

    public static function is_multiple($afield) {
        return $afield['field_type'] == 'checkbox' 
                || $afield['field_type'] == 'repeater'
                || $afield['field_type'] == 'group'
                || !empty($afield['allow_multiple']) 
                || !empty($afield['allow_multiple_upload'])
                || ($afield['field_type'] == 'query' && $afield['allow_multiple']) 
                || ($afield['field_type'] == 'select' && $afield['allow_multiple']) 
                || ($afield['field_type'] == 'library' && $afield['allow_multiple']) 
                || ($afield['field_type'] == 'upload' && $afield['allow_multiple_upload']) 
                || ($afield['field_type'] == 'media' && $afield['allow_multiple_upload']);
    }

    public static function save_upload_media($fields, $settings, $obj_id = 0) {
        if (!empty($fields) && is_array($fields)) {
            foreach ($fields as $akey => $adatas) {
                $afield = self::get_field($akey, $settings);
                if ($afield) {
                    if (in_array($afield['field_type'], array('upload', 'media', 'signature'))) {
                        $files = Utils::explode($adatas); 
                        //var_dump($files);
                        if (!empty($files)) {
                            $fields[$akey] = '';
                            foreach ($files as $adata) {
                                if (filter_var($adata, FILTER_VALIDATE_URL)) {
                                    //$adata = str_replace(get_bloginfo('url'), WP, $value);
                                    $filename = Utils::url_to_path($adata);
                                    if (is_file($filename)) {
                                        $attach_id = Utils::url_to_postid($adata); // check if exists
                                        if (!$attach_id) {
                                            // Check the type of file. We'll use this as the 'post_mime_type'.
                                            $filetype = wp_check_filetype(basename($filename), null);
                                            $fileinfo = pathinfo($filename);
                                            // Prepare an array of post data for the attachment.
                                            $attachment = array(
                                                'guid' => $adata,
                                                'post_mime_type' => $filetype['type'],
                                                'post_status' => 'inherit',
                                                'post_title' => $fileinfo['filename'],
                                                    //'post_content' => '',
                                            );
                                            if ($obj_id) {
                                                $attachment['post_parent'] = $obj_id;
                                            }
                                            // Insert the attachment.
                                            $attach_id = wp_insert_attachment($attachment, $filename, $obj_id);
                                            // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
                                            require_once( ABSPATH . 'wp-admin'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'image.php' );
                                            // Generate the metadata for the attachment, and update the database record.
                                            $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                                            wp_update_attachment_metadata($attach_id, $attach_data);
                                            
                                            //update_meta_cache('post', $attach_id);
                                            /*$new_path = get_post_meta( $attach_id, '_wp_attached_file', true );
                                            $file = \EAddonsForElementor\Plugin::instance()->assets_manager->_wp_relative_upload_path($new_path, $filename);
                                            if ($new_path != $file) {
                                                update_post_meta( $attach_id, '_wp_attached_file', $file );
                                            }*/
                                        }
                                        if ($afield['allow_multiple_upload']) {
                                            /* if (is_array($fields[$akey])) {
                                              $fields[$akey][] = $attach_id;
                                              } else {
                                              $fields[$akey] = array($attach_id);
                                              } */
                                            if ($fields[$akey]) {
                                                $fields[$akey] .= ',' . $attach_id;
                                            } else {
                                                $fields[$akey] = $attach_id;
                                            }
                                        } else {
                                            $fields[$akey] = $attach_id;
                                        }
                                    }
                                }
                            }
                        }
                        //var_dump($fields); die();
                    }
                }
            }
        }
        return $fields;
    }

    public static function save_extra($obj_id, $type, $settings, $fields) {

        if ($settings['e_form_save_' . ($type ? $type . '_' : '') . 'file']) {
            $fields = Form::save_upload_media($fields, $settings, $obj_id);
        }

        // update empty checkbox
        foreach ($settings['form_fields'] as $afield) {
            if (in_array($afield['field_type'], array('checkbox', 'radio', 'acceptance', 'media', 'repeater'))) {
                if (!isset($fields[$afield['custom_id']])) {
                    $fields[$afield['custom_id']] = '';
                }
            }
        }

        if (!empty($fields) && is_array($fields)) {
            if (!empty($settings['e_form_save_' . ($type ? $type . '_' : '') . 'metas']) && is_array($settings['e_form_save_' . ($type ? $type . '_' : '') . 'metas'])) {
                $settings['e_form_save_' . ($type ? $type . '_' : '') . 'metas'] = array_filter($settings['e_form_save_' . ($type ? $type . '_' : '') . 'metas']); // remove the "No field" empty value
            }
            foreach ($fields as $akey => $adata) {
                if (!empty($settings['e_form_save_' . ($type ? $type . '_' : '') . 'metas']) && !in_array($akey, $settings['e_form_save_' . ($type ? $type . '_' : '') . 'metas']))
                    continue;
                /* if ($settings['e_form_save_anonymous'] && ($akey == 'ip_address' || $akey == 'referrer' || $akey == 'user_id'))
                  continue; */
                if ($settings['e_form_save_' . ($type ? $type . '_' : '') . 'array']) {
                    $afield = self::get_field($akey, $settings);
                    if ($afield) {
                        $continue = false;
                        switch ($afield['field_type']) {
                            case 'repeater':
                                if (is_string($adata)) {
                                    $adata = self::list_to_array($adata, $afield);
                                    //var_dump($adata); die();
                                }

                                if ($settings['e_form_save_' . ($type ? $type . '_' : '') . 'file']) {
                                    foreach($adata as $row_id => $rfields) {
                                        $adata[$row_id] = Form::save_upload_media($rfields, $settings, $obj_id);
                                    }
                                }

                                if (!empty($adata)) {
                                    $raw = $_POST['form_fields'][$akey];
                                    if (Utils::is_plugin_active('advanced-custom-fields-pro')) {
                                        if (\EAddonsForElementor\Core\Utils\Acf::set_repeater($akey, $adata, $obj_id, $type)) {
                                            $continue = true;
                                        }
                                    }
                                    if (Utils::is_plugin_active('jet-engine')) {
                                        if (\EAddonsForElementor\Core\Utils\Jet::set_repeater($akey, $adata, $obj_id, $type)) {
                                            $continue = true;
                                        }
                                    }
                                    //var_dump($adata);
                                }
                                break;
                            case 'group':
                                if (is_string($adata)) {
                                    if (empty($afield['save_structure'])) {
                                        $adata = self::list_to_array($adata, $afield);
                                    } else {
                                        $adata = Utils::maybe_json_decode($adata, true);
                                    }
                                    //var_dump($adata); die();
                                }
                                break;
                            default:
                                if (Form::is_multiple($afield)) {
                                    if (!empty($_POST['form_fields'][$akey])) {
                                        $adata = $_POST['form_fields'][$akey];
                                        //var_dump($adata);
                                    } else {
                                        $adata = Utils::explode($adata);
                                    }
                                }
                        }
                        if ($continue) continue;
                    }
                }                
                if ($type == 'option') {
                    $exist_opt = false;
                    if ($settings['e_form_save_' . ($type ? $type . '_' : '') . 'override'] == 'add') {
                        $exist_opt = get_option($akey);
                    }
                    if ($settings['e_form_save_' . ($type ? $type . '_' : '') . 'override'] == 'update' || !$exist_opt) {
                        update_option($akey, $adata);
                    }
                } else {
                    //var_dump($akey); var_dump($adata); die();
                    update_metadata($type, $obj_id, $akey, $adata);
                }
            }
        }
    }
    
    public static function list_to_array($html, $item) {
        $arr = [];
        //var_dump($item['repeater_fields']); die();
        if (!empty($html)) {
            if (!empty($item['repeater_fields'])) {
                $i = 0;
                //var_dump($html); die();
                $tmp = explode('<ul>', $html);
                if (count($tmp) > 1) {                
                    foreach ($tmp as $key => $value) {
                        if ($key) {
                            $fields = explode('<li>', $value);
                            if (count($fields) > 1) {
                                foreach ($fields as $fkey => $fvalue) {
                                    if ($fkey && $fvalue) {
                                        list($fvalue, $more) = explode('</li>', $fvalue, 2);
                                        list($label, $val) = explode(':',$fvalue,2);
                                        if ($label) {
                                            if (!in_array($label, $item['repeater_fields']) && !empty($item['repeater_fields'][$fkey-1])) {
                                                $label = $item['repeater_fields'][$fkey-1];
                                            }
                                            $val = trim($val);
                                            if (!empty($_POST[$item['custom_id']][$i][$label])) {
                                                $val = $_POST[$item['custom_id']][$i][$label];
                                            }
                                            $rfield = self::get_field($label);
                                            if ($rfield && !empty($rfield['field_label']) && substr_count($rfield['field_label'], ':') > 0) {
                                                $tmp = explode(':', $val, substr_count($rfield['field_label'], ':')+1);
                                                $val = end($tmp);
                                            }
                                            if ($rfield && self::is_multiple($rfield)) {
                                                $val = Utils::explode($val);
                                            }
                                            $arr[$i][$label] = $val;
                                        }                                
                                    }
                                }
                            }
                            $i++;
                        }
                    }
                }
            }
            if (!empty($item['group_fields'])) {
                $fields = explode('<li>', $html);
                foreach ($fields as $fkey => $fvalue) {
                    if ($fkey) {
                        list($fvalue, $more) = explode('</li>', $fvalue, 2);
                        list($label, $val) = explode(':',$fvalue,2);
                        if ($label) {
                            if (!in_array($label, $item['group_fields']) && !empty($item['group_fields'][$fkey-1])) {
                                $label = $item['group_fields'][$fkey-1];
                            }
                            $val = trim($val);
                            if (!empty($_POST[$label])) {
                                $val = $_POST[$label];
                            }
                            $rfield = self::get_field($label);
                            if ($rfield && self::is_multiple($rfield)) {
                                $val = Utils::explode($val);
                            }
                            $arr[$label] = $val;
                        }                                
                    }
                }
                //var_dump($arr); die();
            }
        }
        return $arr;
    }

    public static function get_form_id($settings = array()) {
        if (!empty($settings['id'])) {
            return $settings['id'];
        }
        if (!empty($_REQUEST['form_id'])) {
            return sanitize_key($_REQUEST['form_id']);
        }
        return false;
    }
    
    public static function get_field_label($field) {
        $meta = $field;
        $form_id = self::get_form_id();
        if ($form_id) {
            $form_settings = Utils::get_settings_by_element_id($form_id);
            $form_field = Form::get_field($field, $form_settings);
            if (!empty($form_field['field_label'])) {
                $meta = $form_field['field_label'];
            }
        }
        return $meta;
    }

    public static function get_field_option_label($field, $meta = false) {
        $form_id = self::get_form_id();
        if (!in_array($field, ['post_id','form_id','queried_id', 'referer_title'])) {
            if ($form_id) {
                $form_settings = Utils::get_settings_by_element_id($form_id);
                $form_field = self::get_field($field, $form_settings);
                if (!empty($form_field)) {
                    $ret = array();
                    if (self::is_multiple($form_field)) {
                        $values = Utils::explode($meta);
                    } else {
                        $values = array($meta);
                    }
                    //var_dump($meta);
                    foreach ($values as $value) {
                        $meta = $value;
                        switch ($form_field['field_type']) {
                            case 'select':
                            case 'checkbox':
                            case 'radio':
                                $options = self::options_array($form_field['field_options']);
                                foreach ($options as $option) {
                                    if (!empty($option['value']) && $option['value'] == $value) {
                                        $meta = $option['text'];
                                    }
                                }
                                break;
                            case 'query':                            
                                switch ($form_field['e_query_type']) {
                                    case 'posts':
                                        $meta = get_the_title($value);
                                        break;
                                    case 'terms':
                                        if (intval($value)) {
                                            $term = get_term(intval($value));
                                            //var_dump($value);
                                            if (!is_wp_error($term)) {
                                                $meta = $term->name;
                                            }
                                        }                                    
                                        break;
                                    case 'users':
                                        $user = get_user_by('ID', $meta);
                                        $meta = $user->display_name;
                                        break;
                                    case 'metas':
                                    // if (is ACF) get_field_object($field);
                                }
                                break;
                            case 'signature':
                                $meta = '<img src="'.$meta.'">';
                                break;
                        }
                        $ret[] = $meta;
                    }
                    $meta = Utils::implode($ret);
                    $meta = Utils::to_string($meta);
                }
            }
        }
        return $meta;
    }

}
