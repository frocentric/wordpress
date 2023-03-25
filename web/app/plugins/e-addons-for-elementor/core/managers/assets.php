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
        
        add_action( 'elementor/core/files/clear_cache', [ $this, '_clear_all_cache' ] );
        $addon = \EAddonsForElementor\Plugin::instance()->get_addon('e-addons-for-elementor');
        if (!empty($addon['Version'])) {
            $version = $addon['Version'];
            //var_dump($version); die();
            $assets_version = get_option('e_addons_version');
            //var_dump($assets_version); die();
            if ((!$assets_version && $version) || ($assets_version != $version) || (Utils::version_compare($assets_version, $version, '<'))) {
                $this->_clear_all_cache();
                update_option('e_addons_version', $version);
                //var_dump($assets_version); die();
            }
        }

        if (DIRECTORY_SEPARATOR == '\\') {
            add_filter('_wp_relative_upload_path', [$this, '_wp_relative_upload_path'], 10, 2); // fix Windows path
        }
    }
    
    public function _clear_all_cache() {
        $this->_clear_cache();
        $this->_clear_cache('css');
    }
    public function _clear_cache($ext = 'js') {
        // delete all js
        $path = \Elementor\Core\Files\Base::get_base_uploads_dir() . $ext . DIRECTORY_SEPARATOR . '*.'.$ext;
        $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        //var_dump($path); die();
        foreach ( glob( $path ) as $file_path ) {
            if (substr(basename($file_path), 0, 5) != 'post-') { // preserve Elementor assets...
                unlink( $file_path );
            }
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

        $wp_upload_dir = wp_upload_dir();
        //var_dump($wp_upload_dir); die();
        //$elementor_uploads_path = $wp_upload_dir['basedir'] .DIRECTORY_SEPARATOR.'elementor' .DIRECTORY_SEPARATOR;
        $elementor_uploads_path = \Elementor\Core\Files\Base::get_base_uploads_dir();         
        $elementor_uploads_path = str_replace('/', DIRECTORY_SEPARATOR, $elementor_uploads_path);
        
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
                        
                        if (SCRIPT_DEBUG) {
                            $url = plugins_url($url);
                        } else {
                            // minimize it
                            $folder = str_replace('.css','.min.css', $url);
                            $tmp = explode(DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR, $folder, 2);
                            if (count($tmp) == 2) {
                                $folder = $tmp[1];
                            }
                            $path = $elementor_uploads_path.'css';
                            wp_mkdir_p($path);
                            $min = $path.DIRECTORY_SEPARATOR.$folder;
                            $min_path = str_replace('/',DIRECTORY_SEPARATOR, $min);
                            if (!file_exists($min_path)) {
                                $minifier = new \MatthiasMullie\Minify\CSS($acss);
                                $minifier->minify($min_path);
                            }
                            $min_url = Utils::path_to_url($min_path);
                            $url = $min_url;
                        }
                        //var_dump($url);
                        // Register styles
                        wp_register_style(
                            pathinfo($acss, PATHINFO_FILENAME), $url
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
                        
                        if (SCRIPT_DEBUG) {
                            $url = plugins_url($url);
                        } else {
                            // minimize it
                            $folder = str_replace('.js','.min.js', $url);
                            $tmp = explode(DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR, $folder, 2);
                            if (count($tmp) == 2) {
                                $folder = $tmp[1];
                            }
                            $path = $elementor_uploads_path.'js';
                            wp_mkdir_p($path);
                            $min = $path.DIRECTORY_SEPARATOR.$folder;
                            $min_path = str_replace('/',DIRECTORY_SEPARATOR, $min);
                            if (!file_exists($min_path)) {
                                $minifier = new \MatthiasMullie\Minify\JS($ajs);
                                $minifier->minify($min_path);
                            }
                            $min_url = Utils::path_to_url($min_path);
                            $url = $min_url;
                        }
                        
                        $handle = pathinfo($ajs, PATHINFO_FILENAME);
                        if (!wp_script_is($handle, 'registered')) {
                            // Register scripts
                            wp_register_script(
                                    $handle, $url, ['jquery'], null, true
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
                        $style .= self::get_style($skey, $sstyle);
                    }
                }
            }
        }
        echo $style;
    }
    public static function get_style($handle, $style = '') {
        //var_dump($handle);
        if (empty($style) && empty(self::$styles[$handle])) {
            $wp_styles = wp_styles();
            if (isset($wp_styles->registered[$handle])) {
                ob_start();
                $concat = $wp_styles->do_concat;
                $wp_styles->do_concat = false;
                $tmp = $wp_styles->do_item($handle);
                //wp_print_styles($handle);
                $wp_styles->do_concat = $concat;
                $style = ob_end_clean().$tmp;
                if ($style != '11') return $style;
                
                if ($wp_styles->registered[$handle]->src) {
                    return '<link id="'.$handle.'" href="'.$wp_styles->registered[$handle]->src.'" rel="stylesheet">';
                } else {
                    $style = $wp_styles->print_inline_style( $handle, false );
                }
            }
        }
        $style = self::clean($style);
        if ($style) {
            return '<style id="e-addons-' . $handle . '">' . $style . '</style>';
        }
        return '';
    }
    public static function print_style($handle, $style) {
        echo self::get_style($handle, $style);
    }
    public static function print_scripts() {
        $script = '';
        if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            if (!empty(self::$scripts)) {
                foreach (self::$scripts as $skey => $sscript) {
                    $script .= self::get_script($skey, $sscript);
                }
            }
        }
        echo $script;
    }
    public static function get_script($handle, $script = '') {
        //var_dump($handle);
        if (empty($script) && empty(self::$scripts[$handle])) {
            $wp_scripts = wp_scripts();
            if (isset($wp_scripts->registered[$handle])) {
                ob_start();
                $concat = $wp_scripts->do_concat;
                $wp_scripts->do_concat = false;
                $tmp = $wp_scripts->do_item($handle);
                //wp_print_scripts($handle);
                $wp_scripts->do_concat = $concat;
                $script = ob_end_clean().$tmp;
                if ($wp_scripts->registered[$handle]->src) {
                    return '<script id="'.$handle.'" src="'.$wp_scripts->registered[$handle]->src.'"></script>';
                } else {
                    $script = $wp_scripts->print_inline_script( $handle, false );
                }
            }
        }
        $script = self::clean($script);
        if ($script) {
            return '<script id="e-addons-' . $handle . '">' . $script . '</script>';
        }
        return '';
    }
    public static function print_script($handle, $script) {
        echo self::get_script($handle, $script);
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
