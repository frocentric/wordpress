<?php

namespace EAddonsForElementor\Core\Traits;

/**
 * @author francesco
 */
trait Elementor {

    public static $documents = [];
    public static $element_settings = [];

    public static function get_current_element() {
        return apply_filters('e_addons/current_element', false);
    }

    public static function get_current_post_id() {
        if (isset(\Elementor\Plugin::instance()->documents)) {
            return \Elementor\Plugin::instance()->documents->get_current()->get_main_id();
        }
        return get_the_ID();
    }

    public static function get_template_by_element_id($e_id, $p_id = 0) {
        global $wpdb;
        $t_id = false;
        if (empty(self::$documents[$e_id])) {
            // search element settings elsewhere (maybe in a template)            
            $q = "SELECT post_id FROM " . $wpdb->prefix . "postmeta WHERE meta_key LIKE '_elementor_data'";
            $q .= ' AND meta_value LIKE \'%"id":"' . $e_id . '",%\'';
            if (empty($p_id)) {
                $current = $q . ' AND post_id = ' . get_the_ID();
                $results = $wpdb->get_col($current);
                if (empty($results)) {
                    $q .= " AND post_id IN ( SELECT id FROM " . $wpdb->prefix . "posts WHERE post_status LIKE 'publish' )";
                } else {
                    $p_id = reset($results);
                }
            } else {
                $q .= ' AND post_id = ' . $p_id;
            }

            if ($p_id <= 0) {
                $results = $wpdb->get_col($q);
                $p_id = -1;
                if (!empty($results)) {
                    $p_id = reset($results);
                }
            }
            self::$documents[$e_id] = $t_id = $p_id;
            //var_dump(get_the_ID()); var_dump(self::$documents[$e_id]);
        } else {
            $t_id = self::$documents[$e_id];
        }
        if ($p_id && !$t_id) {
            $t_id = self::get_template_by_element_id($e_id);
        }
        return $t_id;
    }

    public static function get_widget_type_by_id($e_id, $p_id = null) {
        $p_id = self::get_element_post_id($e_id, $p_id);
        if (intval($p_id) > 0) {
            $document = \Elementor\Plugin::$instance->documents->get($p_id);
            if ($document) {
                $e_raw = self::get_element_from_data($document->get_elements_data(), $e_id);
                if (!empty($e_raw['widgetType'])) {
                    return $e_raw['widgetType'];
                }
            }
        }
        return false;
    }

    public static function get_element_post_id($e_id, $p_id = 0) {
        if (!$p_id && $e_id) {
            $p_id = self::get_template_by_element_id($e_id);
        }
        if (!$p_id && !empty($_REQUEST['post_id'])) {
            $p_id = absint($_REQUEST['post_id']);
        }
        if (!$p_id && !empty($_REQUEST['post'])) {
            $p_id = absint($_REQUEST['post']);
        }
        if (!$p_id || $p_id < 0) {
            $p_id = get_the_ID();
        }
        return $p_id;
    }

    public static function get_element_instance_by_id($e_id, $p_id = null) {
        $p_id = self::get_element_post_id($e_id, $p_id);
        //var_dump($p_id);
        if (intval($p_id) > 0) {
            $document = \Elementor\Plugin::$instance->documents->get($p_id);
            if ($document) {
                $e_raw = self::get_element_from_data($document->get_elements_data(), $e_id);
                if ($e_raw) {
                    return \Elementor\Plugin::$instance->elements_manager->create_element_instance($e_raw);
                } else {
                    //var_dump($e_id); var_dump($document->get_elements_data()); die();
                    $t_id = self::get_template_by_element_id($e_id);
                    if ($t_id && $t_id > 0 && $t_id != $p_id) {
                        return self::get_element_instance_by_id($e_id, $t_id);
                    }
                }
            }
        }
        return false;
    }

    public static function get_settings_by_element_id($e_id = null, $p_id = null) {
        if ($e_id && isset(self::$element_settings[$e_id])) {
            return self::$element_settings[$e_id];
        }
        $element = self::get_element_instance_by_id($e_id, $p_id);
        if ($element) {
            $settings = $element->get_settings_for_display();
            if ($e_id) {
                self::$element_settings[$e_id] = $settings;
            }
            return $settings;
        }
        return false;
    }

    // similar to \ElementorPro\Modules\Forms\Module::find_element_recursive
    public static function get_element_from_data($elements, $e_id) {
        if (!empty($elements)) {
            foreach ($elements as $element) {
                if ($e_id === $element['id']) {
                    return $element;
                }
                if (!empty($element['elements'])) {
                    $element = self::get_element_from_data($element['elements'], $e_id);
                    if ($element) {
                        return $element;
                    }
                }
            }
        }
        //$element = \ElementorPro\Modules\Forms\Module::find_element_recursive($elements->get_elements_data(), $e_id);
        return false;
    }

    public static function get_elements_from_data($e_data, $type = '') {
        $elements = array();
        if (is_string($e_data)) {
            $e_data = json_decode($e_data);
        }
        //var_dump($e_data); die();
        if (!empty($e_data)) {
            foreach ($e_data as $element) {
                //var_dump($element);
                if ($type && !empty($element->widgetType) && $element->widgetType == $type) {
                    $elements[$element->id] = $element->settings;
                }
                if (!empty($element->elements)) {
                    $elements_tmp = self::get_elements_from_data($element->elements, $type);
                    if (!empty($elements_tmp)) {
                        foreach ($elements_tmp as $key => $value) {
                            $elements[$key] = $value;
                        }
                    }
                }
            }
        }
        return $elements;
    }

    public static function get_elementor_elements($type = '', $p_id = null, $instance = false) {
        global $wpdb;
        $sql_query = "SELECT * FROM " . $wpdb->prefix . "postmeta
		WHERE meta_key LIKE '_elementor_data'
		AND meta_value LIKE '%\"widgetType\":\"" . $type . "\"%'
            AND post_id IN (
            SELECT ID FROM " . $wpdb->prefix . "posts
            WHERE post_status LIKE 'publish' " . ($p_id ? 'AND ID = ' . $p_id : '') . "
          )";

        $results = $wpdb->get_results($sql_query);
        if (!count($results)) {
            return false;
        }
        $elements = array();
        foreach ($results as $result) {
            $p_id = $result->post_id;
            $e_data = $result->meta_value;
            $elements_tmp = self::get_elements_from_data($e_data, $type);
            if (!empty($elements_tmp)) {
                foreach ($elements_tmp as $key => $settings) {
                    /* if ($instance) {
                      //var_dump($value);
                      $value = \Elementor\Plugin::$instance->elements_manager->create_element_instance($value);
                      } */
                    $settings = json_decode(json_encode($settings), true);
                    $elements[$p_id][$key] = $settings;
                }
            }
        }

        return $elements;
    }

    public static function is_preview($editor_mode = false) {
        return !empty($_GET['elementor-preview']) || (!empty($_GET['post']) && !empty($_GET['action']) && $_GET['action'] == 'elementor') || (wp_doing_ajax() && !empty($_POST['action']) && $_POST['action'] == 'elementor_ajax') || ($editor_mode && \Elementor\Plugin::$instance->editor->is_edit_mode());
    }

    public static function get_template_from_html($content = '') {
        $tmp = explode('class="elementor elementor-', $content, 2);
        if (count($tmp) > 1) {
            $tmp = str_replace('"', ' ', end($tmp));
            list($id, $more) = explode(' ', $tmp, 2);
            return intval($id);
        }
        return false;
    }

    /**
     * Generate trigger URL based on the popup ID and the trigger type.
     *
     * @param integer $p_id
     * @param string $action
     * @return string
     */
    public function get_popup_url($id, $action = 'open') {
        $url = '';
        // Generate the URL based on its action using the native Elementor's function.
        switch ($action) {
            case 'close':
            case 'close-forever':
                $url = \Elementor\Plugin::instance()->frontend->create_action_hash(
                        'popup:close',
                        array(
                            'do_not_show_again' => 'close-forever' === $action ? 'yes' : '',
                        )
                );
                break;
            case 'open':
            case 'toggle':
            default:
                $url = \Elementor\Plugin::instance()->frontend->create_action_hash(
                        'popup:open',
                        array(
                            'id' => strval($id),
                            'toggle' => 'toggle' === $action,
                        )
                );
                break;
        }
        $url = str_replace('%23', '#', $url);
        return $url;
    }

    public static function get_dynamic_tags_categories() {
        //return ['base', 'text', 'url', 'image', 'media', 'post_meta', 'gallery', 'number', 'color'];
        //new \Elementor\Modules\DynamicTags\Module();
        $reflection = new \ReflectionClass('\Elementor\Modules\DynamicTags\Module');
        $categories = $reflection->getConstants();
        return array_values($categories);
    }

    public static function add_help_control($base, $element = false) {
        if (!$element) {
            $element = $base;
        }
        $element->add_control(
                'e_' . $base->get_name() . '_help', [
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw' => '<div id="elementor-panel__editor__help" class="p-0"><a id="elementor-panel__editor__help__link" href="' . $base->get_docs() . '" target="_blank">' . esc_html__('Need Help', 'elementor') . ' <i class="eicon-help-o"></i></a></div>',
            'separator' => 'before',
                ]
        );
    }

    public static function get_placeholder_image_src() {
        return \Elementor\Utils::get_placeholder_image_src();
    }

    public static function get_current_template($post_id = false) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        if (\Elementor\Plugin::instance()->documents->get( $post_id )->is_built_with_elementor()) {
            return $post_id;
        } else {
            if (\EAddonsForElementor\Core\Utils::is_plugin_active('elementor-pro')) {
                $locations = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_locations_manager()->get_locations();
                if (!empty($locations['single'])) {
                    $documents_for_location = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_conditions_manager()->get_documents_for_location('single');
                    if (!empty($documents_for_location)) {
                        foreach ($documents_for_location as $document_id => $document) {
                            return $document_id;
                        }
                    }
                }
                $document = \Elementor\Plugin::instance()->documents->get($post_id);
                if ($document) {
                    return $document->get_main_id();
                }
            }
        }
        return false;
    }

    /**
     * Add render attributes.
     *
     * Used to add attributes to the current element wrapper HTML tag.
     *
     * @since 3.1.0
     * @access protected
     */
    public static function add_render_attributes($element) {
        $id = $element->get_id();

        $settings = $element->get_settings_for_display();
        $frontend_settings = $element->get_frontend_settings();
        $controls = $element->get_controls();

        $element->add_render_attribute('_wrapper', [
            'class' => [
                'elementor-element',
                'elementor-element-' . $id,
            ],
            'data-id' => $id,
            'data-element_type' => $element->get_type(),
        ]);

        $class_settings = [];

        foreach ($settings as $setting_key => $setting) {
            if (isset($controls[$setting_key]['prefix_class'])) {
                $class_settings[$setting_key] = $setting;
            }
        }

        foreach ($class_settings as $setting_key => $setting) {
            if (empty($setting) && '0' !== $setting) {
                continue;
            }

            $element->add_render_attribute('_wrapper', 'class', $controls[$setting_key]['prefix_class'] . $setting);
        }

        $_animation = !empty($settings['_animation']);
        $animation = !empty($settings['animation']);
        $has_animation = $_animation && 'none' !== $settings['_animation'] || $animation && 'none' !== $settings['animation'];

        if ($has_animation) {
            $is_static_render_mode = Plugin::$instance->frontend->is_static_render_mode();

            if (!$is_static_render_mode) {
                // Hide the element until the animation begins
                $element->add_render_attribute('_wrapper', 'class', 'elementor-invisible');
            }
        }

        if (!empty($settings['_element_id'])) {
            $element->add_render_attribute('_wrapper', 'id', trim($settings['_element_id']));
        }

        if ($frontend_settings) {
            $element->add_render_attribute('_wrapper', 'data-settings', wp_json_encode($frontend_settings));
        }

        /**
         * After element attribute rendered.
         *
         * Fires after the attributes of the element HTML tag are rendered.
         *
         * @since 2.3.0
         *
         * @param Element_Base $this The element.
         */
        do_action('elementor/element/after_add_attributes', $element);
    }

    /**
     * Get image sizes.
     *
     * Retrieve available image sizes after filtering `include` and `exclude` arguments.
     *
     * @since 2.0.0
     * @access private
     *
     * @return array Filtered image sizes.
     */
    public static function get_image_sizes() {
        $wp_image_sizes = \Elementor\Group_Control_Image_Size::get_all_image_sizes();

        $image_sizes = [];
        $image_sizes[''] = _x('Default', 'Image Size Control', 'elementor');
        $image_sizes['full'] = _x('Full', 'Image Size Control', 'elementor');
        foreach ($wp_image_sizes as $size_key => $size_attributes) {
            $control_title = ucwords(str_replace('_', ' ', $size_key));
            if (is_array($size_attributes)) {
                $control_title .= sprintf(' - %d x %d', $size_attributes['width'], $size_attributes['height']);
            }
            $image_sizes[$size_key] = $control_title;
        }
        $image_sizes['custom'] = _x('Custom', 'Image Size Control', 'elementor');

        return $image_sizes;
    }

    public static function enqueue_element_assets($element) {
        switch ($element->get_type()) {
            case 'widget':
                $skin = $element->get_current_skin();
                if ($skin) {
                    //$skin->enqueue_scripts();
                    if (method_exists($skin, 'get_script_depends')) {
                        foreach ($skin->get_script_depends() as $script) {
                            wp_enqueue_script($script);
                        }
                    }
                    //$skin->enqueue_styles();
                    if (method_exists($skin, 'get_style_depends')) {
                        foreach ($skin->get_style_depends() as $style) {
                            wp_enqueue_style($style);
                        }
                    }
                } else {
                    self::enqueue_children_assets($element);
                }

                if (in_array($element->get_name(), array('template', 'e-template'))) {
                    $template_id = $element->get_settings_for_display('template_id');
                    if ($template_id) {
                        $document = \Elementor\Plugin::$instance->documents->get_doc_for_frontend($template_id);
                        if ($document && $document->is_built_with_elementor()) {
                            //\Elementor\Plugin::$instance->documents->switch_to_document( $document );
                            $data = $document->get_elements_data();
                            foreach ($data as $element_data) {
                                $section = \Elementor\Plugin::$instance->elements_manager->create_element_instance($element_data);
                                if (!$section) {
                                    continue;
                                }
                                self::enqueue_children_assets($section);
                            }
                        }
                    }
                }
                if (in_array($element->get_name(), array('form'))) {
                    $fields_types = array();
                    $form_module = \ElementorPro\Modules\Forms\Module::instance();
                    if (!empty($form_module->field_types)) {
                        $fields_types = $form_module->field_types;
                    }
                    if (!empty($form_module->fields_registrar)) {
                        $fields_types = $form_module->fields_registrar->get();
                    }
                    if (!empty($fields_types)) {
                        $form_fields = $element->get_settings_for_display('form_fields');
                        foreach ($form_fields as $field) {
                            //var_dump($field['field_type']);
                            if (!empty($fields_types[$field['field_type']])) {
                                $fields_type = $fields_types[$field['field_type']];
                                foreach ($fields_type->depended_scripts as $script) {
                                    wp_enqueue_script($script);
                                }
                                foreach ($fields_type->depended_styles as $style) {
                                    wp_enqueue_style($style);
                                }
                            }
                        }
                    }
                }
                break;
            case 'section':
            case 'column':
                self::enqueue_children_assets($element);
        }
    }

    public static function enqueue_children_assets($element) {
        foreach ($element->get_children() as $child) {
            self::enqueue_element_assets($child);
        }
        $element->enqueue_scripts();
        $element->enqueue_styles();
    }

    public static function get_extra_css($p_id = false, $theme = false) {
        $css = '';
        $upload = wp_upload_dir();
        $styles = array();

        // FLEX
        $css .= '.elementor-column.elementor-col-10, .elementor-column[data-col="10"] { width: 10%; }
                .elementor-column.elementor-col-11, .elementor-column[data-col="11"] { width: 11.111%; }
                .elementor-column.elementor-col-12, .elementor-column[data-col="12"] { width: 12.5%; }
                .elementor-column.elementor-col-14, .elementor-column[data-col="14"] { width: 14.285%; }
                .elementor-column.elementor-col-16, .elementor-column[data-col="16"] { width: 16.666%; }
                .elementor-column.elementor-col-20, .elementor-column[data-col="20"] { width: 20%; }
                .elementor-column.elementor-col-25, .elementor-column[data-col="25"] { width: 25%; }
                .elementor-column.elementor-col-30, .elementor-column[data-col="30"] { width: 30%; }
                .elementor-column.elementor-col-33, .elementor-column[data-col="33"] { width: 33.333%; }
                .elementor-column.elementor-col-40, .elementor-column[data-col="40"] { width: 40%; }
                .elementor-column.elementor-col-50, .elementor-column[data-col="50"] { width: 50%; }
                .elementor-column.elementor-col-60, .elementor-column[data-col="60"] { width: 60%; }
                .elementor-column.elementor-col-66, .elementor-column[data-col="66"] { width: 66.666%; }
                .elementor-column.elementor-col-70, .elementor-column[data-col="70"] { width: 70%; }
                .elementor-column.elementor-col-75, .elementor-column[data-col="75"] { width: 75%; }
                .elementor-column.elementor-col-80, .elementor-column[data-col="80"] { width: 80%; }
                .elementor-column.elementor-col-83, .elementor-column[data-col="83"] { width: 83.333%; }
                .elementor-column.elementor-col-90, .elementor-column[data-col="90"] { width: 90%; }
                .elementor-column.elementor-col-100, .elementor-column[data-col="100"] { width: 100%; } }
                .elementor-column { display:block; float:left; }
                .elementor-section .elementor-container { display:block; }
                .elementor-image-gallery .gallery-columns-1 .gallery-item { width: 100%; }
                .elementor-image-gallery .gallery-columns-2 .gallery-item { width: 50%; }
                .elementor-image-gallery .gallery-columns-3 .gallery-item { width: 33.33%; }
                .elementor-image-gallery .gallery-columns-4 .gallery-item { width: 25%; }
                .elementor-image-gallery .gallery-columns-5 .gallery-item { width: 20%; }
                .elementor-image-gallery .gallery-columns-6 .gallery-item { width: 16.666%; }
                .elementor-image-gallery .gallery-columns-7 .gallery-item { width: 14.28%; }
                .elementor-image-gallery .gallery-columns-8 .gallery-item { width: 12.5%; }
                .elementor-image-gallery .gallery-columns-9 .gallery-item { width: 11.11% }
                .elementor-image-gallery .gallery-columns-10 .gallery-item { width: 10%; }
                .elementor-image-gallery .gallery-item { float: left; }
                .e-add-post { float: left; }';

        // ELEMENTOR
        $styles['elementor-global'] = $upload['basedir'] . '/elementor/css/global.css';
        $styles['elementor-common'] = ELEMENTOR_ASSETS_PATH . 'css/common.min.css';
        $styles['elementor-custom-frontend'] = ELEMENTOR_ASSETS_PATH . 'css/custom-frontend.css';
        $styles['elementor-frontend'] = ELEMENTOR_ASSETS_PATH . 'css/frontend.min.css';
        //$styles['elementor-fontawesome'] = ELEMENTOR_ASSETS_PATH . 'lib/font-awesome/css/fontawesome.min.css';
        //$styles['elementor-font-awesome'] = ELEMENTOR_ASSETS_PATH . 'lib/font-awesome/css/all.min.css';             
        //$styles['e-addons-query'] = E_ADDONS_PATH . '/modules/query/assets/css/e-addons-common-query.css';
        // THEME
        if ($theme) {
            $styles['theme-style'] = STYLESHEETPATH . '/style.css';
            if (is_child_theme()) {
                $styles['theme-style-child'] = TEMPLATEPATH . '/style.css';
                $styles['theme-assets-style'] = TEMPLATEPATH . '/assets/css/style.css';
            }
        }

        // POST
        if ($p_id) {
            $styles['elementor-post-' . $p_id . '-css'] = $upload['basedir'] . '/elementor/css/post-' . $p_id . '.css';
        }

        // PRO
        if (self::is_plugin_active('elementor-pro')) {
            $styles['elementor-pro-frontend'] = ELEMENTOR_PRO_ASSETS_PATH . 'css/frontend.min.css';
        }

        // KITs
        $query_kit = new \WP_Query(array('post_type' => 'elementor_library', 'meta_field' => '_elementor_template_type', 'meta_value' => 'kit'));
        //var_dump($query_kit); die();
        if ($query_kit->have_posts()) {
            $post_ids = wp_list_pluck($query_kit->posts, 'ID');
            //var_dump($post_ids); die();
            foreach ($post_ids as $p_id) {
                $styles['elementor-post-' . $p_id . '-css'] = $upload['basedir'] . '/elementor/css/post-' . $p_id . '.css';
            }
        }

        //var_dump($styles); die();
        foreach ($styles as $key => $style) {
            if (file_exists($style)) {
                $css .= file_get_contents($style);
            } else {
                //var_dump($key);
            }
        }

        // fix global vars
        //$tmp = str_replace("{--", ";--", $css);
        $vars = explode("--e-global-", $css);
        foreach ($vars as $key => $tmp) {
            if ($key) {
                list($var, $tmp) = explode(':', $tmp, 2);
                if (strpos($var, ')') === false && strpos($var, '}') === false) {
                    list($val, $tmp) = explode(';', $tmp, 2);
                    //var_dump($var); var_dump($val); die();
                    $css = str_replace("var( --e-global-" . $var . " )", $val, $css);
                }
            }
        }

        // fix calc values
        for ($i = 1; $i <= 12; $i++) {
            $size = 100 / $i;
            //$size = floor(100/$i);
            $css = str_replace('calc(100% / ' . $i . ')', $size . '%', $css);
        }

        return $css;
    }

    public static function get_elementor_capability() {
        $can = false;
        if (is_user_logged_in()) {
            if (is_singular()) {
                $post_type = get_post_type(get_queried_object_id());
                if (\Elementor\User::is_current_user_can_edit_post_type($post_type)) {
                    $can = true;
                }
            } else {
                $can = \Elementor\User::is_current_user_can_edit_post_type('elementor_library');
            }
            if (is_super_admin()) {
                $can = true;
            }
        }
        return $can;
    }

    public static function get_elementor_stats($post_id = 0, $widget = '') {
        global $wpdb;
        $used = [];
        $posts = $post_id ? self::implode($post_id) : "SELECT `ID` FROM `" . $wpdb->prefix . "posts` WHERE `post_status` LIKE 'publish'";
        $sql = "SELECT `post_id` FROM `" . $wpdb->postmeta . "` WHERE `meta_key` = '_elementor_data' AND `post_id` IN ( " . $posts . " )";
        //var_dump($sql);
        $post_ids = $wpdb->get_col($sql);
        //var_dump($post_ids);
        foreach ($post_ids as $post_id) {
            $template_data = \Elementor\Plugin::$instance->documents->get($post_id);
            if (is_object($template_data)) {
                $template_data = $template_data->get_elements_data();
            }
            if (!empty($template_data)) {
                \Elementor\Plugin::$instance->db->iterate_data($template_data, function ($element) use (&$used) {
                    if (!empty($element['widgetType'])) {
                        //var_dump($element['widgetType']);
                        $used[$element['widgetType']] = empty($used[$element['widgetType']]) ? 1 : $used[$element['widgetType']] + 1;
                        if ($element['widgetType'] == 'form') {
                            if (!empty($element['settings']['submit_actions'])) {
                                foreach ($element['settings']['submit_actions'] as $action) {
                                    $used['actions'][$action] = empty($used['actions'][$action]) ? 1 : $used['actions'][$action] + 1;
                                }
                            }
                            if (!empty($element['settings']['form_fields'])) {
                                //var_dump($element['settings']['form_fields']);
                                foreach ($element['settings']['form_fields'] as $field) {
                                    if (!empty($field['field_type'])) {
                                        $type = $field['field_type'];
                                        $used['fields'][$type] = empty($used['fields'][$type]) ? 1 : $used['fields'][$type] + 1;
                                    }
                                }
                            }
                        }
                        if (!empty($element['settings']['_skin'])) {
                            $name = $element['settings']['_skin'];
                            $used['skins'][$name] = empty($used['skins'][$name]) ? 1 : $used['skins'][$name] + 1;
                        }
                    }
                    if (!empty($element['settings']['__dynamic__'])) {
                        foreach ($element['settings']['__dynamic__'] as $tag) {
                            list($pre, $next) = explode('name="', $tag, 2);
                            list($name, $more) = explode('"', $next, 2);
                            $used['tags'][$name] = empty($used['tags'][$name]) ? 1 : $used['tags'][$name] + 1;
                        }
                    }
                    if (!empty($element['settings'])) {
                        foreach ($element['settings'] as $setting) {
                            //if (is_array($setting)) { echo '<pre>';var_dump($setting);echo '</pre>'; }
                            if (is_array($setting)) {
                                // form
                                if (!empty($setting['__dynamic__'])) {
                                    foreach ($setting['__dynamic__'] as $tag) {
                                        list($pre, $next) = explode('name="', $tag, 2);
                                        list($name, $more) = explode('"', $next, 2);
                                        $used['tags'][$name] = empty($used['tags'][$name]) ? 1 : $used['tags'][$name] + 1;
                                    }
                                } else {
                                    // repeater
                                    foreach ($setting as $row) {
                                        if (!empty($row['__dynamic__'])) {
                                            foreach ($row['__dynamic__'] as $tag) {
                                                list($pre, $next) = explode('name="', $tag, 2);
                                                list($name, $more) = explode('"', $next, 2);
                                                $used['tags'][$name] = empty($used['tags'][$name]) ? 1 : $used['tags'][$name] + 1;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }
        if ($widget) {
            return isset($used[$widget]) ? $used[$widget] : 0;
        }
        //var_dump($used['fields']);
        return $used;
    }

}
