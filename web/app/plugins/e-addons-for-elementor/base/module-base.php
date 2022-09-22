<?php

namespace EAddonsForElementor\Base;

use EAddonsForElementor\Core\Utils;
use Elementor\Core\Base\Module;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

abstract class Module_Base extends Module {

    public $disabled = [];
    
    public static $elements = [];
    public static $widgets = [];
    public static $actions = [];
    public static $fields = [];
    public static $items = [];
    public static $dynamic_tags = [];
    public static $script_attrs = [];

    public function __construct() {
        
        // not load in admin pages
        if (is_admin() 
                && (!empty($_GET['action']) && $_GET['action'] != 'elementor')
                && (empty($_GET['page']) || (!empty($_GET['page']) && $_GET['page'] == 'e_addons'))) {
            //echo 'skip admin'; die();
            //return false;
        }

        $this->disabled = get_option('e_addons_disabled', array());

        if ($this->has_elements('controls') || $this->has_elements('controls'. DIRECTORY_SEPARATOR .'groups')) {
            add_action('elementor/controls/controls_registered', [$this, 'init_controls']);
        }
        
        if ($this->has_elements('widgets')) {
            add_action('elementor/elements/categories_registered', [$this, 'init_categories']);
            add_action('elementor/widgets/widgets_registered', [$this, 'init_widgets']);
        }
        
        if ($this->has_elements('elements')) {
            add_action('elementor/elements/elements_registered', [$this, 'init_elements']);
            add_filter('elementor/document/config', [$this, 'add_configuration'], 10, 2 );
        }
        
        $priority = Utils::is_plugin_active('acf') ? 20 : 10;
        if ($this->has_elements('tags')) {
            if (version_compare(ELEMENTOR_VERSION, '3.5.0', '<')) {
                add_action('elementor/dynamic_tags/register_tags', [$this, 'init_tags'], $priority); // < 3.5.0 - TODO: REMOVE IT SHORTLY
            } else {
                add_action('elementor/dynamic_tags/register', [$this, 'init_tags'], $priority); // >= 3.5.0
            }
        }

        add_action('admin_enqueue_scripts', [$this, 'init_assets']);
        //add_action('init', [$this, 'init_assets']);

        add_action('elementor/frontend/before_enqueue_styles', [$this, 'init_assets']);
        add_action('elementor/editor/before_enqueue_scripts', [$this, 'init_assets']);
        add_action('elementor/preview/enqueue_styles', [$this, 'init_assets']);
        add_filter('script_loader_tag', [$this, 'add_attrs_to_script'], 10, 3 );
        
        //add_action('elementor/frontend/after_register_styles', [$this, 'init_styles']);
        //add_action('elementor/frontend/after_register_scripts', [$this, 'init_scripts']);

        $this->init_tabs();
        $this->init_extensions();
        
        $this->init_globals();

        //add_action('elementor/init', [$this, 'init_shortcodes']);
        $this->init_shortcodes();
        

        //add_action('e_addons/init_triggers', [$this, 'init_triggers']);
        
        //add_action('e_addons/init_items', [$this, 'init_items']);
        $this->init_items();
        
        //if (is_admin()) {
            $this->init_skins();
        //}
        
        $priority = 10;
        if (in_array($this->get_name(), array('payments', 'pdf'))) {
            $priority = 9;
        }
        
        if (defined('ELEMENTOR_PRO_VERSION') && (version_compare(ELEMENTOR_PRO_VERSION, '3.5.0') >= 0 || substr(ELEMENTOR_PRO_VERSION,0,4) == '3.5.')) {
            if ($this->has_elements('fields')) {     
                add_action('elementor_pro/forms/fields/register', [$this, 'init_fields']); // > Elementor PRO 3.5.x 
            }
            if ($this->has_elements('actions')) {     
                add_action('elementor_pro/forms/actions/register', [$this, 'init_actions'], $priority); // > Elementor PRO 3.5.x
            }
                
        } else {
            add_action('elementor_pro/init', [$this, 'init_fields']); // old Elementor PRO
            add_action('elementor_pro/init', [$this, 'init_actions'], $priority); // old Elementor PRO
            add_action('elementor_pro/forms/register_action', [$this, 'init_fields']); // > Elementor PRO 3.1.x
            add_action('elementor_pro/forms/register_action', [$this, 'init_actions'], $priority); // > Elementor PRO 3.1.x
        }       
    }

    /**
     * Get Name
     *
     * Get the name of the module
     *
     * @since  1.0.1
     * @return string
     */
    public function get_name() {
        $assets_name = $this->get_reflection()->getNamespaceName();
        $tmp = explode('\\', $assets_name);
        $module = end($tmp);
        $module = Utils::camel_to_slug($module);
        return $module;
    }

    /**
     * Get Name
     *
     * Get the name of the module
     *
     * @since  1.0.1
     * @return string
     */
    public function get_label() {
        $assets_name = $this->get_reflection()->getNamespaceName();
        $tmp = explode('\\', $assets_name);
        $module = end($tmp);
        $module = Utils::camel_to_slug($module, ' ');
        return ucfirst($module);
    }
    
    public function get_plugin_textdomain() {
        $assets_name = $this->get_reflection()->getNamespaceName();
        $tmp = explode('\\', $assets_name);
        $plugin = reset($tmp);
        $plugin = Utils::camel_to_slug($plugin, '-');
        return $plugin;
    }
    public function get_plugin_path() {
        $wp_plugin_dir = Utils::get_wp_plugin_dir();
        return $wp_plugin_dir.DIRECTORY_SEPARATOR.$this->get_plugin_textdomain().DIRECTORY_SEPARATOR;
    }
    
    public function has_elements($folder = 'widgets') {
        $module = $this->get_name();
        $class_name = $this->get_reflection()->getNamespaceName();
        $plugin_path = Utils::get_plugin_path($class_name);
        $path = $plugin_path . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR;        
        if (is_dir($path)) {
            $files = Utils::glob($path . '*.php');
            return !empty($files);
        }
        return false;
    }

    public function get_elements($folder = 'elements', $enabled = true) {
        $elements = array();
        $module = $this->get_name();
        $class_name = $this->get_reflection()->getNamespaceName();
        $plugin_path = Utils::get_plugin_path($class_name);
        $path = $plugin_path . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR;        
        //if ($folder == 'triggers' && $module == 'display') { return $elements; }
        if (is_dir($path)) {
            
            $files = Utils::glob($path . '*.php');
            //$files = array_filter(glob(DIRECTORY_SEPARATOR."*"), 'is_file');
            
            foreach ($files as $ele) {
                $file = basename($ele);
                $name = pathinfo($file, PATHINFO_FILENAME);
                if ($enabled) {
                    if (!empty($this->disabled[$folder][$module]) && in_array($name, $this->disabled[$folder][$module])) {
                        continue;
                    }
                }
                $elements[] = Utils::slug_to_camel($name, '_');
            }
        }
        return $elements;
    }

    public function init_controls() {
        $controls_manager = \Elementor\Plugin::$instance->controls_manager;
        foreach ($this->get_elements('controls') as $control) {
            $class_name = $this->get_reflection()->getNamespaceName() . '\Controls\\' . $control;
            $control_obj = new $class_name();
            if (version_compare(ELEMENTOR_VERSION, '3.5.0', '<')) {
                $controls_manager->register_control($control_obj->get_type(), $control_obj);
            } else {
                $controls_manager->register($control_obj);
            }
        }
        foreach ($this->get_elements('controls'. DIRECTORY_SEPARATOR .'groups') as $group) {
            $class_name = $this->get_reflection()->getNamespaceName() . '\Controls\Groups\\' . $group;
            $control_obj = new $class_name();
            $controls_manager->add_group_control($control_obj->get_type(), $control_obj);
        }
    }
    
    public function init_elements() {
        $elements_manager = \Elementor\Plugin::instance()->elements_manager;
        $elements = $this->get_elements('elements');
        foreach ($elements as $element) {
            $class_name = $this->get_reflection()->getNamespaceName() . '\Elements\\' . $element;
            if (empty(self::$elements[$class_name])) {
                self::$elements[$class_name] = new $class_name();
            }
            $element = self::$elements[$class_name];    
            $elements_manager->register_element_type($element);
        }
    }
    public function add_configuration($additional_config, $post_id) {
        //$additional_config['group3d'] = \Elementor\Plugin::$instance->elements_manager->get_element_types( 'group3d' )->get_config();
        $types = \Elementor\Plugin::$instance->elements_manager->get_element_types();
        
        foreach($types as $tkey => $type) {
            if (in_array($tkey, ['revisions', 'section', 'column', 'container'])) continue;
            $additional_config['widgets'][$tkey] = $type->get_config();
        }
        //var_dump($additional_config); die();
        return $additional_config;
    }

    public function init_widgets() {
        $widget_manager = \Elementor\Plugin::instance()->widgets_manager;
        foreach ($this->get_elements('widgets') as $widget) {
            $class_name = $this->get_reflection()->getNamespaceName() . '\Widgets\\' . $widget;
            if (empty(self::$widgets[$class_name])) {
                self::$widgets[$class_name] = new $class_name();
            }
            $widget = self::$widgets[$class_name];    
            if (version_compare(ELEMENTOR_VERSION, '3.5.0', '<')) {    
                $widget_manager->register_widget_type($widget);
            } else {
                $widget_manager->register($widget);
            }
        }
        //var_dump(array_keys($widget_manager->get_widget_types()));
    }

    public function init_categories($elements) {
        foreach ($this->get_elements('widgets') as $widget) {
            $class_name = $this->get_reflection()->getNamespaceName() . '\Widgets\\' . $widget;
            //var_dump($class_name);
            if (method_exists($class_name, 'get_categories_static')) {
                foreach ($class_name::get_categories_static() as $category) {
                    $title = ucwords($category);
                    $title = str_replace('-', ' ', $title);
                    $elements->add_category($category, array(
                        'title' => $title,
                    ));
                }
            }
            if (empty(self::$widgets[$class_name])) {
                self::$widgets[$class_name] = new $class_name();
            }
            $widget = self::$widgets[$class_name];            
            foreach ($widget->get_categories() as $category) {
                $title = ucwords($category);
                $title = str_replace('-', ' ', $title);
                $elements->add_category($category, array(
                    'title' => ucfirst($title),
                ));
            }
        }
        //var_dump($elements->get_categories()); die();
    }

    /*public function get_tag_classes_names() {
        return $this->get_elements('tags');
    }*/
    public function init_tags($dynamic_tags) {
        /** @var \Elementor\Core\DynamicTags\Manager $module */
        $module = \Elementor\Plugin::$instance->dynamic_tags;  
        //var_dump($module->get_tags()); die();
        /*$module->register_group('e-addons', [
            'title' => esc_html__('e-addons', 'e-addons-for-elementor'),
        ]);*/
        // get_tag_classes_names
        foreach ($this->get_elements('tags') as $tag) {
            $class_name = $this->get_reflection()->getNamespaceName() . '\Tags\\' . $tag;
            if (method_exists($class_name, '_group')) {
                $group = $class_name::_group();
                $module->register_group($group['name'], ['title' => $group['title']]);
            }
            //var_dump($class_name);
            if (!property_exists($class_name, 'ignore')) {
                if (empty(self::$dynamic_tags[$class_name])) {
                    self::$dynamic_tags[$class_name] = $tag = new $class_name();
                    if (version_compare(ELEMENTOR_VERSION, '3.5.0', '<')) {    
                        $module->register_tag($class_name);
                    } else {
                        $module->register($tag);
                    }
                }
            }
        }
    }
    
    public function init_assets() {
        $module = $this->get_name();

        $class_name = $this->get_reflection()->getNamespaceName();
        $plugin_path = Utils::get_plugin_path($class_name);
        $assets_path = $plugin_path . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR;

        \EAddonsForElementor\Core\Managers\Assets::register_assets($assets_path);
    }

    public function init_scripts() {
        $module = $this->get_name();

        $class_name = $this->get_reflection()->getNamespaceName();
        $plugin_path = Utils::get_plugin_path($class_name);
        $assets_path = $plugin_path . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR;

        \EAddonsForElementor\Core\Managers\Assets::register_assets($assets_path, 'js');
    }

    public function init_styles() {
        $module = $this->get_name();

        $class_name = $this->get_reflection()->getNamespaceName();
        $plugin_path = Utils::get_plugin_path($class_name);
        $assets_path = $plugin_path . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR;

        \EAddonsForElementor\Core\Managers\Assets::register_assets($assets_path, 'css');
    }

    public function init_tabs() {        
        $control_manager = \Elementor\Plugin::instance()->controls_manager;
        foreach ($this->get_elements('tabs') as $ext) {
            $class_name = $this->get_reflection()->getNamespaceName() . '\Tabs\\' . $ext;
            $tab_obj = new $class_name();
            $tab_obj->_register_tab();
            //$control_manager::add_tab($tab_obj->get_id(), $tab_obj->get_title());
        }
    }
    
    public function init_extensions() {        
        foreach ($this->get_elements('extensions') as $ext) {
            $class_name = $this->get_reflection()->getNamespaceName() . '\Extensions\\' . $ext;
            $ext_obj = new $class_name();
        }
    }

    public function init_globals() {
        foreach ($this->get_elements('globals') as $ext) {
            $class_name = $this->get_reflection()->getNamespaceName() . '\Globals\\' . $ext;
            $ext_obj = new $class_name();
        }
    }

    public function init_shortcodes() {
        foreach ($this->get_elements('shortcodes') as $short) {
            $class_name = $this->get_reflection()->getNamespaceName() . '\Shortcodes\\' . $short;
            $short_obj = new $class_name();
            add_shortcode($short_obj->get_name(), array($short_obj, 'do_shortcode'));
        }
    }

    public function init_triggers($display) {
        foreach ($this->get_elements('triggers') as $short) {
            $class_name = $this->get_reflection()->getNamespaceName() . '\Triggers\\' . $short;
            $slug = Utils::camel_to_slug($short);
            $display::$triggers[$slug] = new $class_name();
        }
    }
    
    public function init_items() {
        foreach ($this->get_elements('items') as $short) {
            $class_name = $this->get_reflection()->getNamespaceName() . '\Items\\' . $short;
            $slug = Utils::camel_to_slug($short);
            self::$items[$slug] = new $class_name();
        }
    }

    public function init_skins() {
        foreach ($this->get_elements('skins') as $skin) {
            $class_name = $this->get_reflection()->getNamespaceName() . '\Skins\\' . $skin;
            //var_dump($class_name);
            $skin_obj = new $class_name();
        }
    }

    public function init_fields($form_module = null) {   
        if (!$form_module) {
            $form_module = \ElementorPro\Plugin::instance()->modules_manager->get_modules('forms');
        }
        foreach ($this->get_elements('fields') as $field) {            
            $class_name = $this->get_reflection()->getNamespaceName() . '\Fields\\' . $field;
            if (empty(self::$fields[$class_name])) {
                $form_field = new $class_name();                
                if (method_exists($form_module, 'add_form_field_type')) {
                    $form_module->add_form_field_type( $form_field->get_type(), $form_field );
                }
                if (method_exists($form_module, 'register')) {
                    $form_module->register($form_field);
                }
                self::$fields[$class_name] = $form_field;
            }
        }
    }
    public function init_actions($form_module = null) {
        if (!$form_module) {
            $form_module = \ElementorPro\Plugin::instance()->modules_manager->get_modules('forms');
        }
        foreach ($this->get_elements('actions') as $action) {
            $class_name = $this->get_reflection()->getNamespaceName() . '\Actions\\' . $action;
            if (empty(self::$actions[$class_name])) {
                $form_action = new $class_name();
                // Register the action with form widget
                if (method_exists($form_module, 'add_form_action')) {
                    $form_module->add_form_action($form_action->get_name(), $form_action);
                }
                if (method_exists($form_module, 'register')) {
                    $form_module->register($form_action);
                }
                self::$actions[$class_name] = $form_action;
            }
        }
        //var_dump(array_keys(\ElementorPro\Plugin::instance()->modules_manager->get_modules('forms')->get_form_actions()));
    }

    public function register_style($handle, $path, $deps = [], $version = '', $media = 'all') {
        $assets_name = $this->get_reflection()->getNamespaceName();
        //var_dump($assets_name);var_dump(get_class($this));
        $tmp = explode('\\', $assets_name);
        $module = implode('/', $tmp);
        $module = Utils::camel_to_slug($module);
		$url = WP_PLUGIN_URL . '/' . $module . '/' . $path;
		$url = str_replace('/-', '/', $url);
        wp_register_style($handle, $url, $deps, $version, $media);
    }
    
    public function register_script($handle, $path, $deps = [], $version = '', $footer = true, $attrs = []) {
        $assets_name = $this->get_reflection()->getNamespaceName();
        $tmp = explode('\\', $assets_name);
        $module = implode('/', $tmp);
        $module = Utils::camel_to_slug($module);
		$url = WP_PLUGIN_URL . '/' . $module . '/' . $path;
		$url = str_replace('/-', '/', $url);
        wp_register_script($handle, $url, $deps, $version, $footer);
        if (!empty($attrs)) {
            self::$script_attrs[$handle] = $attrs;
        }
    }
    public function add_attrs_to_script( $tag, $handle, $src ) {
        if (!empty(self::$script_attrs[$handle])) {
            $attrs = '';
            foreach(self::$script_attrs[$handle] as $key => $value) {
                $attrs .= $key.'="'.$value.'" ';
            }
            $tag = str_replace('<script ', '<script '.$attrs, $tag);
        }
        return $tag;
    }

}