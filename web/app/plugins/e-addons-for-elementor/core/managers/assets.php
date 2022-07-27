<?php

namespace EAddonsForElementor\Core\Managers;

use EAddonsForElementor\Core\Utils;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

final class Assets {

    public static $assets = [];
    public static $styles = [];
    public static $scripts = [];

    public function __construct() {
        $this->register_core_assets();
        add_action('elementor/editor/before_enqueue_styles', [$this, 'register_core_assets']);
        add_action('elementor/editor/after_enqueue_styles', [$this, 'enqueue_editor_styles']);
        add_action('wp_footer', [$this, 'print_styles'], 100);
        add_action('wp_footer', [$this, 'print_scripts'], 100);
        do_action('e_addons/assets');

        if (DIRECTORY_SEPARATOR == '\\') {
            add_filter('_wp_relative_upload_path', [$this, '_wp_relative_upload_path'], 10, 2); // fix Windows path
        }
    }

    /**
     * Return relative path to an uploaded file.
     *
     * The path is relative to the current upload dir.
     *
     * @since 2.9.0
     * @access private
     *
     * @param string $path Full path to the file.
     * @return string Relative path on success, unchanged path on failure.
     */
    function _wp_relative_upload_path($new_path, $path) {
        $uploads = wp_get_upload_dir();
        $basedir = str_replace('/', DIRECTORY_SEPARATOR, $uploads['basedir']);
        if (0 === strpos($path, $basedir)) {
            $new_path = str_replace($basedir, '', $path);
            $new_path = ltrim($new_path, DIRECTORY_SEPARATOR);
            $new_path = str_replace(DIRECTORY_SEPARATOR, '/', $new_path);
        }
        return $new_path;
    }

    /**
     * Enqueue admin styles
     *
     * @since 1.0.1
     *
     * @access public
     */
    public function register_core_assets() {
        $assets_path = E_ADDONS_PATH . 'assets' . DIRECTORY_SEPARATOR;
        self::register_assets($assets_path);
    }

    public function enqueue_editor_styles() {
        // Register styles
        wp_enqueue_style('e-addons-icons');
        //if (!empty($_GET['post']) && !empty($_GET['action']) && $_GET['action'] == 'elementor') {
        wp_enqueue_style('e-addons-editor');
        //}
        wp_enqueue_style(
                'font-awesome-5-all',
                ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/all.min.css',
                [],
                ELEMENTOR_VERSION
        );
    }

    public static function find_assets($assets_path = '', $type = 'css') {
        $assets = array();
        if (is_dir($assets_path . $type)) {
            $files = Utils::glob($assets_path . $type . DIRECTORY_SEPARATOR . '*.' . $type);
            foreach ($files as $ass) {
                $assets[] = $ass;
                self::$assets[] = $ass;
            }
        }
        return $assets;
    }

    public static function register_assets($assets_path = '', $assets = '') {

        if (empty($assets) || $assets == 'css') {
            // CSS
            $css = self::find_assets($assets_path, 'css');
            if (!empty($css)) {
                $wp_plugin_dir = Utils::get_wp_plugin_dir();
                foreach ($css as $acss) {
                    $tmp = explode($wp_plugin_dir . DIRECTORY_SEPARATOR, $acss, 2);
                    if (count($tmp) == 2) {
                        list($path, $url) = $tmp;
                        //var_dump(DIRECTORY_SEPARATOR.PLUGINDIR.DIRECTORY_SEPARATOR.$url); die();
                        $url = str_replace('/-', '/', $url);
                        // Register styles
                        wp_register_style(
                                pathinfo($acss, PATHINFO_FILENAME), plugins_url($url)
                        );
                    }
                }
            }
        }

        if (empty($assets) || $assets == 'js') {
            // JS
            $js = self::find_assets($assets_path, 'js');
            if (!empty($js)) {
                $wp_plugin_dir = Utils::get_wp_plugin_dir();
                foreach ($js as $ajs) {
                    $tmp = explode($wp_plugin_dir . DIRECTORY_SEPARATOR, $ajs, 2);
                    if (count($tmp) == 2) {
                        list($path, $url) = $tmp;
                        $url = str_replace('/-', '/', $url);
                        $handle = pathinfo($ajs, PATHINFO_FILENAME);
                        if (!wp_script_is($handle, 'registered')) {
                            // Register scripts
                            wp_register_script(
                                    $handle, plugins_url($url), ['jquery'], null, true
                            );
                        } else {
                            //echo 'WARNING - Script already registered: '.$handle;
                        }
                    }
                }
            }
        }
    }

    public static function enqueue_asset($handle, $code = '', $type = 'css', $element_id = false) {
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            return $code;
        }

        if ($type == 'css') {
            if (empty(self::$styles[$handle])) {
                self::$styles[$handle] = $code;
            } else {
                self::$styles[$handle] .= $code;
            }
        }

        if ($type == 'js') {
            if (empty(self::$scripts[$handle])) {
                self::$scripts[$handle] = $code;
            } else {
                self::$scripts[$handle] .= $code;
            }
        }

        return false;
    }

    public static function enqueue_style($handle, $css = '', $element_id = false) {
        return self::enqueue_asset($handle, $css, 'css', $element_id);
    }

    public static function enqueue_script($handle, $js = '', $element_id = false) {
        return self::enqueue_asset($handle, $js, 'js', $element_id);
    }

    public static function print_styles() {
        $style = '';
        if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            if (!empty(self::$styles)) {
                foreach (self::$styles as $skey => $sstyle) {
                    $sstyle = self::clean($sstyle);
                    if ($sstyle) {
                        $style .= '<style id="e-addons-' . $skey . '">' . $sstyle . '</style>';
                    }
                }
            }
        }
        echo $style;
    }

    public static function print_scripts() {
        $script = '';
        if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            if (!empty(self::$scripts)) {
                foreach (self::$scripts as $skey => $sscript) {
                    $sscript = self::clean($sscript);
                    if ($sscript) {
                        $script .= '<script id="e-addons-' . $skey . '">' . $sscript . '</script>';
                    }
                }
            }
        }
        echo $script;
    }

    public function enqueue_icons() {
        $assets_path = E_ADDONS_PATH . 'assets/';
        self::register_assets($assets_path);
        wp_print_styles('e-addons-icons');
        //add_action('wp_footer', [self, 'print_icons']);
        //add_action('admin_footer', [self, 'print_icons']);
    }

    public function print_icons() {
        wp_print_styles('e-addons-icons');
    }

    public static function clean($content) {
        $content = Utils::strip_tag('script', $content);
        $content = Utils::strip_tag('style', $content);
        return $content;
    }

}
