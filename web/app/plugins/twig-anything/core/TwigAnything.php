<?php
/**
 * Copyright (c) 2015-present by ANTON ANDRIIEVSKYI, the plugin author.
 * See LICENSE.txt for license details.
 */

namespace TwigAnything;

use TwigAnything\DataSources\DataSourcesRegister;
use TwigAnything\Formats\FormatsRegister;

class TwigAnything
{
    const VERSION = '1.6.5';
    const POST_TYPE = 'twig_anything_tmplt';

    /**
     * @var DataSourcesRegister
     */
    private $dataSources;

    /**
     * @var FormatsRegister
     */
    private $formats;

    /**
     * @var PostMetaBoxes
     */
    private $postMetaBoxes;

    /**
     * @var VisualComposerIntegrator
     */
    private $visualComposerIntegrator;

    function __construct() {
        $this->dataSources = new DataSourcesRegister;
        $this->formats = new FormatsRegister;
        $this->postMetaBoxes = new PostMetaBoxes;
        $this->visualComposerIntegrator = new VisualComposerIntegrator;
    }

    public static function pluginDir() {
        return dirname(__DIR__);
    }

    public static function pluginFile() {
        return self::pluginDir() . '/twig-anything.php';
    }

    /**
     * @return DataSourcesRegister
     */
    public function getDataSources() {
        return $this->dataSources;
    }

    /**
     * @return FormatsRegister
     */
    public function getFormats() {
        return $this->formats;
    }

    /**
     * The main plugin initialization routine. Call in the main plugin file:
     * (new TwigAnything)->setup();
     */
    public function setup() {
        register_activation_hook(TwigAnything::pluginFile(), array($this, 'onActivate'));

        add_action('init', array($this, 'onInit'));
        add_action('admin_enqueue_scripts', array($this, 'onAdminEnqueueScripts'));

        add_action('add_meta_boxes_twig_anything_tmplt', array($this, 'onAddMetaBoxesTwigAnythingTmplt'));

        add_action('save_post', array($this->postMetaBoxes, 'onSavePost'));
        add_action('pre_post_update', array($this->postMetaBoxes, 'onPrePostUpdate'));

        # Replace twig template by a real rendered output when viewing single post
        add_filter('the_content', array($this, 'onTheContent'), 1);

        # Filter the single_template with our custom function
        # to provide with our own template file
        add_filter('single_template', array($this, 'onSingleTemplate'));

        # Integrate with Visual Composer
        $this->visualComposerIntegrator->integrate();

        # Add custom widgets
        add_action('widgets_init', array($this, 'onWidgetsInit'));
    }

    /**
     * WordPress activation hook
     */
    public function onActivate() {
        # Flush rewrite rules so that users can access custom post types on the
        # front-end right away
        $this->registerPostType();
        flush_rewrite_rules();
    }

    /**
     * 'init' WordPress action
     */
    public function onInit() {
        $this->dataSources->registerStandardDataSources();
        $this->formats->registerStandardFormats();
        $this->registerShortcodes();
    }

    /**
     * 'add_metaBoxesTwigAnythingTmplt' WordPress action
     */
    public function onAddMetaBoxesTwigAnythingTmplt() {
        $this->postMetaBoxes->addMetaBoxes();
    }

    public static function registerReactJs() {
        # React with Add-ons
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $scriptFile = 'js/react-with-addons-0.14.7.js';
        }
        else {
            $scriptFile = 'js/react-with-addons-0.14.7.min.js';
        }
        wp_register_script(
            'twig_anything_react_with_addons',
            plugins_url($scriptFile, self::pluginFile()),
            array(),
            $ver = '0.14.7'
        );

        # React DOM
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $scriptFile = 'js/react-dom-0.14.7.js';
        }
        else {
            $scriptFile = 'js/react-dom-0.14.7.min.js';
        }
        wp_register_script(
            'twig_anything_react_dom',
            plugins_url($scriptFile, self::pluginFile()),
            array('twig_anything_react_with_addons'),
            $ver = '0.14.7'
        );
    }

    /**
     * 'admin_enqueue_scripts' WordPress action
     */
    public function onAdminEnqueueScripts() {
        $screen = get_current_screen();
        if (!is_admin() || $screen->post_type != TwigAnything::POST_TYPE || $screen->base !== 'post') {
            return;
        }

        $pluginFile = self::pluginFile();

        # Register REACT to make it available by its handle name for add-ons
        self::registerReactJs();

        # MAIN METABOX REACT COMPONENT
        # Note that we only include React DOM: it depends on React With Add-ons,
        # so the latter will be uploaded automatically
        wp_enqueue_script(
            'twig_anything_edit_twig_anything_tmpl',
            plugins_url('jsx/edit_twig_anything_tmpl.js', $pluginFile),
            array('jquery', 'twig_anything_react_dom'),
            $ver = '5'
        );

        # CODEMIRROR
        # Combination of: css, htmlmixed, javascript, twig, xml,
        # fullscreen, multiplex, placeholder
        wp_enqueue_script(
            'twig_anything_codemirror-compressed',
            plugins_url('js/codemirror-compressed.js', $pluginFile),
            $deps = array(),
            $ver = '5.32.0-repack4'
        );
        wp_enqueue_style(
            'twig_anything_code_mirror',
            plugins_url('css/codemirror.css', $pluginFile),
            $deps = array(),
            $ver = '5.32.0-repack4'
        );

        # TWIG TEMPLATE EDITOR CSS (should come after codemirror styles)
        wp_enqueue_style(
            'twig_anything_edit_twig_anything_tmpl',
            plugins_url('css/edit_twig_anything_tmpl.css', $pluginFile),
            $deps = array(),
            $ver = '3'
        );

        # CLIPBOARD.JS
        wp_enqueue_script(
            'twig_anything_clipboard',
            plugins_url('js/clipboard/1.7.1/clipboard.min.js', $pluginFile),
            array(),
            '1.7.1'
        );

        # DATA SOURCES REACT COMPONENTS AND STYLESHEETS
        $dataSources = $this->dataSources->dataSources();
        foreach($dataSources as $ds) {
            $url = $ds->getUrlToComponentJs();
            if (!empty($url)) {
                wp_enqueue_script(
                    'twig_anything_data_source_component_'.$ds->getSlug(),
                    $url,
                    $deps = array(),
                    $ds->getVersion()
                );
            }
            if (is_callable(array($ds, 'getUrlToComponentStylesheet'))) {
                $url = call_user_func(array($ds, 'getUrlToComponentStylesheet'));
                if (!empty($url)) {
                    wp_enqueue_style(
                        'twig_anything_data_source_stylesheet_'.$ds->getSlug(),
                        $url,
                        $deps = array(),
                        $ds->getVersion()
                    );
                }
            }
        }

        # FORMATS REACT COMPONENTS AND STYLESHEETS
        $formats = $this->formats->getFormats();
        foreach($formats as $fmt) {
            $url = $fmt->getUrlToComponentJs();
            if (!empty($url)) {
                wp_enqueue_script(
                    'twig_anything_format_component_'.$fmt->getSlug(),
                    $url,
                    array(),
                    $fmt->getVersion()
                );
            }
            if (is_callable(array($fmt, 'getUrlToComponentStylesheet'))) {
                $url = call_user_func(array($fmt, 'getUrlToComponentStylesheet'));
                if (!empty($url)) {
                    wp_enqueue_style(
                        'twig_anything_format_stylesheet_'.$fmt->getSlug(),
                        $url,
                        $deps = array(),
                        $fmt->getVersion()
                    );
                }
            }
        }
    }

    private function registerPostType() {
        # Register our own post type to manage Twig Templates
        $res = register_post_type( TwigAnything::POST_TYPE, array(
            'label' => 'Twig Templates',
            'labels' => array(
                'name' => __('Twig Templates', 'twig-anything'),
                'singular_name' => __('Twig Template', 'twig-anything'),
                'menu_name' => __('Twig Templates', 'twig-anything'),
                'name_admin_bar' => __('Twig Template', 'twig-anything'),
                'all_items' => __('Twig Templates', 'twig-anything'),
                'add_new' => _x('Add New', TwigAnything::POST_TYPE, 'twig-anything'),
                'add_new_item' => __('Add New Twig Template', 'twig-anything'),
                'edit_item' => __('Edit Twig Template', 'twig-anything'),
                'new_item' => __('New Twig Template', 'twig-anything'),
                'view_item' => __('View rendered template', 'twig-anything'),
                'search_items' => __('Search Twig Templates', 'twig-anything'),
                'not_found' => __('No Twig Templates found', 'twig-anything'),
                'not_found_in_trash' => __('No Twig Templates found in trash', 'twig-anything'),
                'parent_item_colon' => __('Parent Twig Templates found in trash', 'twig-anything'),
            ),
            'description' => __('Twig templates used by the Twig Anything plugin.', 'twig-anything'),
            'exclude_from_search' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_nav_menus' => false,
            'show_in_menu' => true,
            'show_in_admin_bar' => true,
            'menu_position' => 20,
            'menu_icon' => 'dashicons-images-alt2',
            'hierarchical' => false,
            'supports' => array('title', 'editor', 'revisions'),
            'has_archive' => false,
            'public' => true
        ));
        if (is_wp_error($res)) {
            throw new TwigAnythingException('Twig Anything cannot register custom post type "twig_anything_tmplt".');
        }
    }

    public function registerShortcodes() {
        $shortcodes = new Shortcodes();
        add_shortcode( 'twig-anything', array($shortcodes, 'shortcodeTwigAnything') );
        //add_shortcode( 'twig-anything-template-code', [$shortcodes, 'shortcodeTwigAnythingTemplateCode'] );

        # Disable TinyMCE editor for our custom post type - DISABLE FOR NOW
        add_filter('user_can_richedit', function($default){
            global $post;
            if (TwigAnything::POST_TYPE == get_post_type($post))
                return false;
            return $default;
        });

        $this->registerPostType();
    }

    /**
     * 'the_content' WordPress action.
     *
     * @param string $content
     * @return string
     */
    public function onTheContent($content) {
        if (!is_singular(TwigAnything::POST_TYPE)) {
            return $content;
        }

        # The ID of the post being displayed.
        # Even for preview posts, returns the parent ID
        $postId = get_the_ID();

        if (is_preview()) {
            $preview = wp_get_post_autosave($postId);
            if (!empty($preview)) {
                $postId = $preview->ID;
            }
            $configOverride = array(
                'commonSettings' => array(
                    'cache_seconds' => 0,
                    'on_data_error' => 'always_display_error'
                )
            );
        }
        else {
            $configOverride = array();
        }

        $post = get_post($postId);
        if (empty($post)) {
            return $content;
        }

        $config = PostMetaBoxes::loadDataSourceMetaBoxSettings($postId);
        $twigTemplate = new TwigTemplate(twigAnything(), $postId, $post->post_content, $config);
        try {

            return $twigTemplate->render($configOverride);
        }
        catch (TwigAnythingException $e) {
            $msg = wp_kses_post($e->getMessage());
            $html = "<div style=\"color: red;\">$msg</div>";
            return $html;
        }
    }

    /**
     * 'single_template' WordPress action.
     * Forces using our custom post template for our post type.
     *
     * @param string $path
     * @return string
     */
    public function onSingleTemplate($path) {
        global $post;
        if ($post->post_type != TwigAnything::POST_TYPE) {
            return $path;
        }
        $path = self::pluginDir().'/wp-overrides/single-twig_anything_tmpl.php';
        return $path;
    }

    /**
     * 'widgets_init' WordPress action.
     *  Register Twig Template widget
     */
    public function onWidgetsInit() {
        register_widget('TwigAnything\Widgets\Template');
    }
}

class TwigAnythingException extends \RuntimeException{};