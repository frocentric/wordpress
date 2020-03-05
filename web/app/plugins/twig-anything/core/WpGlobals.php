<?php

namespace TwigAnything;

/**
 * Represents access to WordPress globals:
 * https://codex.wordpress.org/Global_Variables
 *
 * Class WpGlobals
 * @package TwigAnything
 */
class WpGlobals
{
    private static function getWpGlobal($name) {
        if (!array_key_exists($name, $GLOBALS) || !in_array($name, self::wpGlobalNames(), true)) {
            return null;
        }
        return $GLOBALS[$name];
    }

    private static function wpGlobalNames() {
        return array(
            # Inside the Loop variables
            'post',
            'authordata',
            'currentday',
            'currentday',
            'currentmonth',
            'page',
            'pages',
            'multipage',
            'more',
            'numpages',

            # Browser Detection Booleans
            'is_iphone',
            'is_chrome',
            'is_safari',
            'is_NS4',
            'is_opera',
            'is_macIE',
            'is_winIE',
            'is_gecko',
            'is_lynx',
            'is_IE',

            # Web Server Detection Booleans
            'is_apache',
            'is_IIS',
            'is_iis7',

            # Version Variables
            'wp_version',
            'wp_db_version',
            'tinymce_version',
            'manifest_version',
            'required_php_version',
            'required_mysql_version',

            # Misc
            'super_admins',
            'wp_query',
            'wp_rewrite',
            'wp',
            'wpdb',
            'wp_locale',
            'wp_admin_bar',
            'wp_roles',
            'wp_meta_boxes',

            # Admin Globals
            'pagenow',
            'post_type',
            'allowedposttags',
            'allowedtags',
            'menu',
        );
    }

    public function __isset($name) {
        return in_array($name, self::wpGlobalNames());
    }

    public function __get($name) {
        return self::getWpGlobal($name);
    }
}