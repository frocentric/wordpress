<?php
/**
 * Copyright (c) 2016 by ANTON ANDRIIEVSKYI, the plugin author.
 * See LICENSE.txt for license details.
 */

namespace TwigAnything;

use Twig_Autoloader;
use Twig_Environment;
use Twig_Error;
use Twig_Loader_Array;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

class TwigHelper
{
    /**
     * Creates a new empty twig environment.
     * Loads Twig library if still not loaded.
     *
     * @param array $twigConfigOverride
     * @return Twig_Environment
     */
    public static function newTwigEnvironment($twigConfigOverride = array()) {
        # Load Twig library
        if (!class_exists('Twig_Autoloader', false)) {
            require_once dirname(__DIR__).'/lib/Twig-1.18.2/lib/Twig/Autoloader.php';
            Twig_Autoloader::register();
        }

        $config = array(
            'debug' => defined('WP_DEBUG')? (bool)WP_DEBUG : false
        );

        $config = Utils::arrayMergeDeep($config, $twigConfigOverride);

        $twig = new Twig_Environment(null, $config);

        return $twig;
    }

    public static function configureTwigEnvironment(Twig_Environment $twig, $configOverride = array()) {
        $defaultConfig = array(
            'addGlobals' => true,
            'addPostAndBlogIDsFunctions' => true,
            'addCommonFilters' => true,
            'addConditionals' => true,
            'addCurrentUserFunctions' => true,
            'addPostFunctions' => true,
            'addRequestFunctions' => true
        );
        $config = Utils::arrayMergeDeep($defaultConfig, $configOverride);
        if ($config['addGlobals']) {
            self::addGlobals($twig);
        }

        if ($config['addPostAndBlogIDsFunctions']) {
            self::addPostAndBlogIDsFunctions($twig);
        }

        if ($config['addCommonFilters']) {
            self::addCommonFilters($twig);
        }

        if ($config['addConditionals']) {
            self::addConditionals($twig);
        }

        if ($config['addCurrentUserFunctions']) {
            self::addCurrentUserFunctions($twig);
        }

        if ($config['addPostFunctions']) {
            self::addPostFunctions($twig);
        }

        if ($config['addRequestFunctions']) {
            self::addRequestFunctions($twig);
        }
    }

    public static function getDefaultTwigEnvironment() {
        static $twig = null;
        if (is_null($twig)) {
            $twig = self::newTwigEnvironment();
            self::configureTwigEnvironment($twig);
        }
        return $twig;
    }

    public static function renderTemplateInTwigEnvironment(Twig_Environment $twig, $templateCode, $context = array(), $configOverride = array()) {
        $defaultConfig = array(
            'isRemoveLineBreaksFromTemplate' => true
        );
        $config = Utils::arrayMergeDeep($defaultConfig, $configOverride);

        if ($config['isRemoveLineBreaksFromTemplate']) {
            $templateCode = preg_replace('/[\\r\\n\\t]+/is', '', $templateCode);
        }

        # Render
        $twig->setLoader(new Twig_Loader_Array(array(
            'template' => $templateCode
        )));
        $twigTemplate = $twig->loadTemplate('template');
        $rendered = $twigTemplate->render($context);
        return $rendered;
    }

    public static function renderTemplateInDefaultTwigEnvironment($templateCode, $context = array(), $configOverride = array()) {
        $twig = self::getDefaultTwigEnvironment();
        return self::renderTemplateInTwigEnvironment($twig, $templateCode, $context, $configOverride);
    }

    public static function renderTemplate($templateCode, $data = null, $configOverride = array(), $twigConfigOverride = array()) {
        $defaultConfig = array(
            'isRemoveLineBreaksFromTemplate' => true
        );
        $config = Utils::arrayMergeDeep($defaultConfig, $configOverride);

        $twig = self::newTwigEnvironment($twigConfigOverride);
        self::configureTwigEnvironment($twig, $config);

        if ($config['isRemoveLineBreaksFromTemplate']) {
            $templateCode = preg_replace('/[\\r\\n\\t]+/is', '', $templateCode);
        }

        # Render
        $twig->setLoader(new Twig_Loader_Array(array(
            'template' => $templateCode
        )));
        $twigTemplate = $twig->loadTemplate('template');
        $rendered = $twigTemplate->render(array('data' => $data));
        return $rendered;
    }

    public static function renderTwigAnythingShortcode($templateCode, $data, $twigConfigOverride = array()) {
        if (!empty($twigConfigOverride) && count($twigConfigOverride)) {
            $twig = self::newTwigEnvironment($twigConfigOverride);
            self::configureTwigEnvironment($twig);
        }
        else {
            $twig = self::getDefaultTwigEnvironment();
        }

        # Render
        $twig->setLoader(new Twig_Loader_Array(array(
            'template_code' => $templateCode
        )));
        $twigTemplate = $twig->loadTemplate('template_code');
        $rendered = $twigTemplate->render(array('data' => $data));
        return $rendered;
    }

    /**
     * Adds most WordPress globals found by this URL:
     * https://codex.wordpress.org/Global_Variables
     *
     * @param Twig_Environment $twig
     */
    public static function addGlobals(Twig_Environment $twig) {
        $twig->addGlobal('wp_globals', new WpGlobals);
    }

    public static function addPostAndBlogIDsFunctions(Twig_Environment $twig) {
        $twig->addFunction(new Twig_SimpleFunction('get_the_ID', 'get_the_ID'));
        $twig->addFunction(new Twig_SimpleFunction('get_current_blog_id', 'get_current_blog_id'));
        $twig->addFunction(new Twig_SimpleFunction('get_the_guid', 'get_the_guid'));
    }

    public static function addCommonFilters(Twig_Environment $twig) {
        $filter = new Twig_SimpleFilter('json', function ($string) {
            return json_encode($string, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
        });
        $twig->addFilter($filter);

        $filter = new Twig_SimpleFilter('json_pretty_print', function ($string) {
            return json_encode($string, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_PRETTY_PRINT);
        });
        $twig->addFilter($filter);
    }

    /**
     * Register conditional tag WordPress functions:
     * https://codex.wordpress.org/Conditional_Tags
     *
     * @param Twig_Environment $twig
     */
    public static function addConditionals(Twig_Environment $twig) {
        # The main page
        $twig->addFunction(new Twig_SimpleFunction('is_home', 'is_home'));
        $twig->addFunction(new Twig_SimpleFunction('is_front_page', 'is_front_page'));
        $twig->addFunction(new Twig_SimpleFunction('is_front_page', 'is_front_page'));

        # The Administration Panels
        $twig->addFunction(new Twig_SimpleFunction('is_admin', 'is_admin'));
        $twig->addFunction(new Twig_SimpleFunction('is_network_admin', 'is_network_admin'));
        $twig->addFunction(new Twig_SimpleFunction('is_network_admin', 'is_network_admin'));

        # The Admin Bar
        $twig->addFunction(new Twig_SimpleFunction('is_admin_bar_showing', 'is_admin_bar_showing'));

        # A Single Post Page
        $twig->addFunction(new Twig_SimpleFunction('is_single', 'is_single'));

        # A Sticky Post
        $twig->addFunction(new Twig_SimpleFunction('is_sticky', 'is_sticky'));

        # A Post Type is Hierarchical
        $twig->addFunction(new Twig_SimpleFunction('is_post_type_hierarchical', 'is_post_type_hierarchical'));

        # A Post Type Archive
        $twig->addFunction(new Twig_SimpleFunction('is_post_type_archive', 'is_post_type_archive'));

        # A Comments Popup
        $twig->addFunction(new Twig_SimpleFunction('is_comments_popup', 'is_comments_popup'));

        # Any Page Containing Posts
        $twig->addFunction(new Twig_SimpleFunction('comments_open', 'comments_open'));
        $twig->addFunction(new Twig_SimpleFunction('pings_open', 'pings_open'));

        # A PAGE Page
        $twig->addFunction(new Twig_SimpleFunction('is_page', 'is_page'));

        # Is a Page Template
        $twig->addFunction(new Twig_SimpleFunction('is_page_template', 'is_page_template'));

        # A Category Page
        $twig->addFunction(new Twig_SimpleFunction('is_category', 'is_category'));
        $twig->addFunction(new Twig_SimpleFunction('in_category', 'in_category'));

        # A Tag Page
        $twig->addFunction(new Twig_SimpleFunction('is_tag', 'is_tag'));
        $twig->addFunction(new Twig_SimpleFunction('has_tag', 'has_tag'));

        # A Taxonomy Page (and related)
        $twig->addFunction(new Twig_SimpleFunction('is_tax', 'is_tax'));
        $twig->addFunction(new Twig_SimpleFunction('has_term', 'has_term'));
        $twig->addFunction(new Twig_SimpleFunction('term_exists', 'term_exists'));
        $twig->addFunction(new Twig_SimpleFunction('is_taxonomy_hierarchical', 'is_taxonomy_hierarchical'));
        $twig->addFunction(new Twig_SimpleFunction('taxonomy_exists', 'taxonomy_exists'));

        # An Author Page
        $twig->addFunction(new Twig_SimpleFunction('is_author', 'is_author'));

        # A Multi-author Site
        $twig->addFunction(new Twig_SimpleFunction('is_multi_author', 'is_multi_author'));

        # A Date Page
        $twig->addFunction(new Twig_SimpleFunction('is_date', 'is_date'));
        $twig->addFunction(new Twig_SimpleFunction('is_year', 'is_year'));
        $twig->addFunction(new Twig_SimpleFunction('is_month', 'is_month'));
        $twig->addFunction(new Twig_SimpleFunction('is_day', 'is_day'));
        $twig->addFunction(new Twig_SimpleFunction('is_time', 'is_time'));
        $twig->addFunction(new Twig_SimpleFunction('is_new_day', 'is_new_day'));

        # Any Archive Page
        $twig->addFunction(new Twig_SimpleFunction('is_archive', 'is_archive'));

        # A Search Result Page
        $twig->addFunction(new Twig_SimpleFunction('is_search', 'is_search'));

        # A 404 Not Found Page
        $twig->addFunction(new Twig_SimpleFunction('is_404', 'is_404'));

        # A Paged Page
        $twig->addFunction(new Twig_SimpleFunction('is_paged', 'is_paged'));

        # An Attachment
        $twig->addFunction(new Twig_SimpleFunction('is_attachment', 'is_attachment'));

        # Attachment Is Image
        $twig->addFunction(new Twig_SimpleFunction('wp_attachment_is_image', 'wp_attachment_is_image'));

        # A Local Attachment
        $twig->addFunction(new Twig_SimpleFunction('is_local_attachment', 'is_local_attachment'));

        # A Single Page, a Single Post, an Attachment or Any Other Custom Post Type
        $twig->addFunction(new Twig_SimpleFunction('is_singular', 'is_singular'));

        # Post Type Exists
        $twig->addFunction(new Twig_SimpleFunction('post_type_exists', 'post_type_exists'));

        # Is Main Query
        $twig->addFunction(new Twig_SimpleFunction('is_main_query', 'is_main_query'));

        # A Syndication
        $twig->addFunction(new Twig_SimpleFunction('is_feed', 'is_feed'));

        # A Trackback
        $twig->addFunction(new Twig_SimpleFunction('is_trackback', 'is_trackback'));

        # A Preview
        $twig->addFunction(new Twig_SimpleFunction('is_preview', 'is_preview'));

        # Has An Excerpt
        $twig->addFunction(new Twig_SimpleFunction('has_excerpt', 'has_excerpt'));

        # Has A Nav Menu Assigned
        $twig->addFunction(new Twig_SimpleFunction('has_nav_menu', 'has_nav_menu'));

        # Inside The Loop
        $twig->addFunction(new Twig_SimpleFunction('in_the_loop', 'in_the_loop'));

        # Is Dynamic SideBar
        $twig->addFunction(new Twig_SimpleFunction('is_dynamic_sidebar', 'is_dynamic_sidebar'));

        # Is Sidebar Active
        $twig->addFunction(new Twig_SimpleFunction('is_active_sidebar', 'is_active_sidebar'));

        # Is Widget Active
        $twig->addFunction(new Twig_SimpleFunction('is_active_widget', 'is_active_widget'));

        # Is Blog Installed
        $twig->addFunction(new Twig_SimpleFunction('is_blog_installed', 'is_blog_installed'));

        # Right To Left Reading
        $twig->addFunction(new Twig_SimpleFunction('is_rtl', 'is_rtl'));

        # Part of a Network (Multisite)
        $twig->addFunction(new Twig_SimpleFunction('is_multisite', 'is_multisite'));

        # Main Site (Multisite)
        $twig->addFunction(new Twig_SimpleFunction('is_main_site', 'is_main_site'));

        # Admin of a Network (Multisite)
        $twig->addFunction(new Twig_SimpleFunction('is_super_admin', 'is_super_admin'));

        # Is User Logged in
        $twig->addFunction(new Twig_SimpleFunction('is_user_logged_in', 'is_user_logged_in'));

        # Email Exists
        $twig->addFunction(new Twig_SimpleFunction('email_exists', 'email_exists'));

        # Username Exists
        $twig->addFunction(new Twig_SimpleFunction('username_exists', 'username_exists'));

        # An Active Plugin - DO NOT INCLUDE (CAN'T SEE ANY REASON?)

        # A Child Theme
        $twig->addFunction(new Twig_SimpleFunction('is_child_theme', 'is_child_theme'));

        # Theme supports a feature
        $twig->addFunction(new Twig_SimpleFunction('current_theme_support', 'current_theme_support'));

        # Has Post Thumbnail
        $twig->addFunction(new Twig_SimpleFunction('has_post_thumbnail', 'has_post_thumbnail'));
    }

    public static function addCurrentUserFunctions(Twig_Environment $twig) {
        $twig->addFunction(new Twig_SimpleFunction('wp_get_current_user', 'wp_get_current_user'));
        $twig->addFunction(new Twig_SimpleFunction('get_current_user_meta', function($key = '', $single = false) {
            return get_user_meta(get_current_user_id(), $key, $single);
        }));
    }

    public static function addRequestFunctions(Twig_Environment $twig) {
        $twig->addFunction(new Twig_SimpleFunction('request', function($name = '', $default = null) {
            if (!array_key_exists($name, $_REQUEST)) {
                return $default;
            }
            return wp_unslash($_REQUEST[$name]);
        }));
    }

    public static function addPostFunctions(Twig_Environment $twig) {
        $twig->addFunction(new Twig_SimpleFunction('get_post_meta', 'get_post_meta'));
        $twig->addFunction(new Twig_SimpleFunction('get_current_post_meta', function($key = '', $single = false) {
            return get_post_meta(get_the_ID(), $key, $single);
        }));
    }
}