<?php

if( ! function_exists( 'ninja_forms_conditionals_setup_license' ) ) {
    function ninja_forms_conditionals_setup_license()
    {
        if (class_exists('NF_Extension_Updater')) {
            $NF_Extension_Updater = new NF_Extension_Updater('Conditional Logic', NINJA_FORMS_CON_VERSION, 'WP Ninjas', __FILE__, 'conditionals');
        }
    }
}

add_action( 'admin_init', 'ninja_forms_conditionals_setup_license' );

/**
 * Load translations for add-on.
 * First, look in WP_LANG_DIR subfolder, then fallback to add-on plugin folder.
 */
if( ! function_exists( 'ninja_forms_conditionals_load_translations' ) ) {
    function ninja_forms_conditionals_load_translations()
    {

        /** Set our unique textdomain string */
        $textdomain = 'ninja-forms-conditionals';

        /** The 'plugin_locale' filter is also used by default in load_plugin_textdomain() */
        $locale = apply_filters('plugin_locale', get_locale(), $textdomain);

        /** Set filter for WordPress languages directory */
        $wp_lang_dir = apply_filters(
            'ninja_forms_conditionals_wp_lang_dir',
            trailingslashit(WP_LANG_DIR) . 'ninja-forms-conditionals/' . $textdomain . '-' . $locale . '.mo'
        );

        /** Translations: First, look in WordPress' "languages" folder = custom & update-secure! */
        load_textdomain($textdomain, $wp_lang_dir);

        /** Translations: Secondly, look in plugin's "lang" folder = default */
        $plugin_dir = trailingslashit(basename(dirname(__FILE__)));
        $lang_dir = apply_filters('ninja_forms_conditionals_lang_dir', $plugin_dir . 'lang/');
        load_plugin_textdomain($textdomain, FALSE, $lang_dir);

    }
}
add_action( 'plugins_loaded', 'ninja_forms_conditionals_load_translations' );

// Get our current Ninja Forms plugin version.
$plugin_settings = get_option( 'ninja_forms_settings' );
$nf_plugin_version = isset( $plugin_settings['version'] ) ? $plugin_settings['version'] : '';

if ( version_compare ( $nf_plugin_version, '2.9', '<' ) ) { // If our current version of Ninja Forms is before 2.9, include our deprecated files.
	require_once( NINJA_FORMS_CON_DIR."/includes/deprecated/scripts.php" );
	require_once( NINJA_FORMS_CON_DIR."/includes/deprecated/register-edit-field-section.php" );
} else {
	require_once( NINJA_FORMS_CON_DIR."/includes/admin/scripts.php" );
	require_once( NINJA_FORMS_CON_DIR."/includes/admin/register-edit-field-section.php" );
}

require_once( NINJA_FORMS_CON_DIR."/classes/trigger-base.php" );
require_once( NINJA_FORMS_CON_DIR."/includes/admin/ajax.php" );
require_once( NINJA_FORMS_CON_DIR."/includes/admin/after-import.php" );
require_once( NINJA_FORMS_CON_DIR."/includes/admin/view-subs-header-filter.php" );
require_once( NINJA_FORMS_CON_DIR."/includes/admin/notifications.php" );
require_once( NINJA_FORMS_CON_DIR."/includes/admin/upgrades/nf-update-notice.php" );
require_once( NINJA_FORMS_CON_DIR."/includes/admin/mp-copy-page.php" );
require_once( NINJA_FORMS_CON_DIR."/includes/functions.php" );

require_once( NINJA_FORMS_CON_DIR."/includes/display/display-conditionals.php" );
require_once( NINJA_FORMS_CON_DIR."/includes/display/scripts.php" );
require_once( NINJA_FORMS_CON_DIR."/includes/display/field-filter.php" );
require_once( NINJA_FORMS_CON_DIR."/includes/display/field-class-filter.php" );

if( ! function_exists( 'ninja_forms_conditional_compare' ) ) {
    function ninja_forms_conditional_compare($param1, $param2, $operator)
    {
        switch ($operator) {
            case "==":
                if (is_array($param1)) {
                    return in_array($param2, $param1);
                } else {
                    return $param1 == $param2;
                }
            case "!=":
                if (is_array($param1)) {
                    if (in_array($param2, $param1)) {
                        return false;
                    } else {
                        return true;
                    }
                } else {
                    return $param1 != $param2;
                }
            case "<":
                return $param1 < $param2;
            case ">":
                return $param1 > $param2;
            case "contains":
                if (stripos($param1, $param2) !== false) {
                    return true;
                } else {
                    return false;
                }
            case "notcontains":
                if (stripos($param1, $param2) === false) {
                    return true;
                } else {
                    return false;
                }
            case "on":
                $plugin_settings = nf_get_settings();
                if (strtolower(substr($plugin_settings['date_format'], 0, 1)) == 'd') {
                    $param1 = str_replace('/', '-', $param1);
                    $param2 = str_replace('/', '-', $param2);
                }

                $date1 = new DateTime($param1);
                $date2 = new DateTime($param2);

                return $date1 == $date2;

            case "before":
                $plugin_settings = nf_get_settings();
                if (strtolower(substr($plugin_settings['date_format'], 0, 1)) == 'd') {
                    $param1 = str_replace('/', '-', $param1);
                    $param2 = str_replace('/', '-', $param2);
                }

                $date1 = new DateTime($param1);
                $date2 = new DateTime($param2);

                return $date1 < $date2;
            case "after":
                $plugin_settings = nf_get_settings();
                if (strtolower(substr($plugin_settings['date_format'], 0, 1)) == 'd') {
                    $param1 = str_replace('/', '-', $param1);
                    $param2 = str_replace('/', '-', $param2);
                }

                $date1 = new DateTime($param1);
                $date2 = new DateTime($param2);

                return $date1 > $date2;
        }
    }
}

/**
 * Hook into our nf_init action and register our trigger types.
 *
 * @since 1.2.8
 * @return void
 */
if( ! function_exists( 'nf_cl_init' ) ) {
    function nf_cl_init($instance)
    {
        $instance->cl_triggers = array();
        $instance->cl_triggers['date_submitted'] = require_once(NINJA_FORMS_CON_DIR . '/classes/trigger-date-submitted.php');
        // $instance->cl_triggers['sub_count'] = require_once( NINJA_FORMS_CON_DIR . '/classes/trigger-sub-count.php' );

        $instance->cl_triggers = apply_filters('nf_cl_criteria_triggers', $instance->cl_triggers);
    }
}

add_action( 'nf_init', 'nf_cl_init' );