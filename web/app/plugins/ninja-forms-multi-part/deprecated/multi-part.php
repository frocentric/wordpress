<?php

if( ! function_exists( 'ninja_forms_mp_setup_license' ) ) {
    function ninja_forms_mp_setup_license()
    {
        if (class_exists('NF_Extension_Updater')) {
            $NF_Extension_Updater = new NF_Extension_Updater('Multi-Part Forms', NINJA_FORMS_MP_VERSION, 'WP Ninjas', __FILE__, 'mp');
        }
    }
}

add_action( 'admin_init', 'ninja_forms_mp_setup_license' );

/**
 * Load translations for add-on.
 * First, look in WP_LANG_DIR subfolder, then fallback to add-on plugin folder.
 */
if( ! function_exists( 'ninja_forms_mp_load_translations' ) ) {
    function ninja_forms_mp_load_translations()
    {

        /** Set our unique textdomain string */
        $textdomain = 'ninja-forms-mp';

        /** The 'plugin_locale' filter is also used by default in load_plugin_textdomain() */
        $locale = apply_filters('plugin_locale', get_locale(), $textdomain);

        /** Set filter for WordPress languages directory */
        $wp_lang_dir = apply_filters(
            'ninja_forms_mp_wp_lang_dir',
            trailingslashit(WP_LANG_DIR) . 'ninja-forms-mp/' . $textdomain . '-' . $locale . '.mo'
        );

        /** Translations: First, look in WordPress' "languages" folder = custom & update-secure! */
        load_textdomain($textdomain, $wp_lang_dir);

        /** Translations: Secondly, look in plugin's "lang" folder = default */
        $plugin_dir = trailingslashit(basename(dirname(__FILE__)));
        $lang_dir = apply_filters('ninja_forms_mp_lang_dir', $plugin_dir . 'lang/');
        load_plugin_textdomain($textdomain, FALSE, $lang_dir);

    }
}
add_action( 'plugins_loaded', 'ninja_forms_mp_load_translations' );

// Get our current Ninja Forms plugin version.
$plugin_settings = get_option( 'ninja_forms_settings' );
$nf_plugin_version = isset( $plugin_settings['version'] ) ? $plugin_settings['version'] : '';

if ( version_compare ( $nf_plugin_version, '2.9', '<' ) ) { // If our current version of Ninja Forms is before 2.9, include our deprecated files.
  require_once(NINJA_FORMS_MP_DIR."/includes/deprecated/open-div.php");
  require_once(NINJA_FORMS_MP_DIR."/includes/deprecated/close-div.php");
  require_once(NINJA_FORMS_MP_DIR."/includes/deprecated/edit-field-ul.php");
  require_once(NINJA_FORMS_MP_DIR."/includes/deprecated/page-divider.php");
  require_once(NINJA_FORMS_MP_DIR."/includes/deprecated/admin-scripts.php");
  require_once(NINJA_FORMS_MP_DIR."/includes/deprecated/display-scripts.php");
  require_once(NINJA_FORMS_MP_DIR."/includes/deprecated/breadcrumb.php");
  require_once(NINJA_FORMS_MP_DIR."/includes/deprecated/nav.php");
  require_once(NINJA_FORMS_MP_DIR."/includes/deprecated/output-divs.php");
  require_once(NINJA_FORMS_MP_DIR."/includes/deprecated/before-pre-process.php");
  require_once(NINJA_FORMS_MP_DIR."/includes/deprecated/post-process.php");
  require_once(NINJA_FORMS_MP_DIR."/includes/deprecated/functions.php");
  require_once(NINJA_FORMS_MP_DIR."/includes/deprecated/form-settings-metabox.php");
} else { // If we're using a version of Ninja Forms >= 2.9, include the non-deprecated stuff.
  require_once(NINJA_FORMS_MP_DIR."/includes/admin/open-div.php");
  require_once(NINJA_FORMS_MP_DIR."/includes/admin/close-div.php");
  require_once(NINJA_FORMS_MP_DIR."/includes/admin/edit-field-ul.php");
  require_once(NINJA_FORMS_MP_DIR."/includes/fields/page-divider.php");
  require_once(NINJA_FORMS_MP_DIR."/includes/admin/scripts.php");
  require_once(NINJA_FORMS_MP_DIR."/includes/display/scripts.php");
  require_once(NINJA_FORMS_MP_DIR."/includes/display/breadcrumb.php");
  require_once(NINJA_FORMS_MP_DIR."/includes/display/nav.php");
  require_once(NINJA_FORMS_MP_DIR."/includes/display/output-divs.php");
  require_once(NINJA_FORMS_MP_DIR."/includes/display/processing/before-pre-process.php");
  require_once(NINJA_FORMS_MP_DIR."/includes/display/processing/post-process.php");
  require_once(NINJA_FORMS_MP_DIR."/includes/display/scripts.php");
  require_once(NINJA_FORMS_MP_DIR."/includes/functions.php");
  require_once(NINJA_FORMS_MP_DIR."/includes/admin/form-settings-metabox.php");
}

require_once(NINJA_FORMS_MP_DIR."/includes/admin/ajax.php");
require_once(NINJA_FORMS_MP_DIR."/includes/admin/labels.php");

require_once(NINJA_FORMS_MP_DIR."/includes/display/progress-bar.php");
require_once(NINJA_FORMS_MP_DIR."/includes/display/page-title.php");
require_once(NINJA_FORMS_MP_DIR."/includes/display/form/confirm.php");

require_once(NINJA_FORMS_MP_DIR."/includes/display/processing/confirm.php");