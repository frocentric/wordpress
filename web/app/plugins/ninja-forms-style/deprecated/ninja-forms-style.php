<?php

if( ! function_exists( 'ninja_forms_style_setup_license' ) ) {
    function ninja_forms_style_setup_license()
    {
        if (class_exists('NF_Extension_Updater')) {
            $NF_Extension_Updater = new NF_Extension_Updater('Layout and Styles', NINJA_FORMS_STYLE_VERSION, 'WP Ninjas', __FILE__, 'style');
        }
    }
}

add_action( 'admin_init', 'ninja_forms_style_setup_license' );


/**
 * Load translations for add-on.
 * First, look in WP_LANG_DIR subfolder, then fallback to add-on plugin folder.
 */
if( ! function_exists( 'ninja_forms_style_load_translations' ) ) {
    function ninja_forms_style_load_translations()
    {

        /** Set our unique textdomain string */
        $textdomain = 'ninja-forms-style';

        /** The 'plugin_locale' filter is also used by default in load_plugin_textdomain() */
        $locale = apply_filters('plugin_locale', get_locale(), $textdomain);

        /** Set filter for WordPress languages directory */
        $wp_lang_dir = apply_filters(
            'ninja_forms_style_wp_lang_dir',
            trailingslashit(WP_LANG_DIR) . 'ninja-forms-style/' . $textdomain . '-' . $locale . '.mo'
        );

        /** Translations: First, look in WordPress' "languages" folder = custom & update-secure! */
        load_textdomain($textdomain, $wp_lang_dir);

        /** Translations: Secondly, look in plugin's "lang" folder = default */
        $plugin_dir = trailingslashit(basename(dirname(__FILE__)));
        $lang_dir = apply_filters('ninja_forms_style_lang_dir', $plugin_dir . 'lang/');
        load_plugin_textdomain($textdomain, FALSE, $lang_dir);

    }
}
add_action( 'plugins_loaded', 'ninja_forms_style_load_translations' );


require_once(NINJA_FORMS_STYLE_DIR."/includes/admin/admin.php");
require_once(NINJA_FORMS_STYLE_DIR."/includes/functions.php");
require_once(NINJA_FORMS_STYLE_DIR."/includes/admin/pages/ninja-forms-style/tabs/form-settings/form-settings.php");
require_once(NINJA_FORMS_STYLE_DIR."/includes/admin/pages/ninja-forms-style/tabs/field-settings/field-settings.php");
require_once(NINJA_FORMS_STYLE_DIR."/includes/admin/pages/ninja-forms-style/tabs/field-type-settings/field-type-settings.php");
require_once(NINJA_FORMS_STYLE_DIR."/includes/admin/pages/ninja-forms-style/tabs/field-type-settings/sidebars/select-field.php");
require_once(NINJA_FORMS_STYLE_DIR."/includes/admin/pages/ninja-forms-style/tabs/error-settings/error-settings.php");
require_once(NINJA_FORMS_STYLE_DIR."/includes/admin/pages/ninja-forms-style/tabs/datepicker-settings/datepicker-settings.php");
require_once(NINJA_FORMS_STYLE_DIR."/includes/admin/pages/ninja-forms-style/tabs/multipart-settings/multipart-settings.php");

require_once(NINJA_FORMS_STYLE_DIR."/includes/admin/pages/ninja-forms/tabs/form-layout/form-layout.php");
require_once(NINJA_FORMS_STYLE_DIR."/includes/admin/pages/ninja-forms/tabs/form-layout/form-layout-div.php");
require_once(NINJA_FORMS_STYLE_DIR."/includes/admin/pages/ninja-forms/tabs/form-layout/form-layout-mp-div.php");
require_once(NINJA_FORMS_STYLE_DIR."/includes/admin/pages/ninja-forms/tabs/form-layout/form-layout-output-ul.php");
require_once(NINJA_FORMS_STYLE_DIR."/includes/admin/pages/ninja-forms/tabs/form-layout/default-field-metaboxes.php");
require_once(NINJA_FORMS_STYLE_DIR."/includes/admin/pages/ninja-forms/tabs/form-layout/list-field-metaboxes.php");
require_once(NINJA_FORMS_STYLE_DIR."/includes/admin/pages/ninja-forms/tabs/form-layout/rating-field-metaboxes.php");

require_once(NINJA_FORMS_STYLE_DIR."/includes/admin/pages/ninja-forms-impexp/tabs/style/impexp-style.php");

require_once(NINJA_FORMS_STYLE_DIR."/includes/admin/ajax.php");
require_once(NINJA_FORMS_STYLE_DIR."/includes/admin/register.php");
require_once(NINJA_FORMS_STYLE_DIR."/includes/admin/scripts.php");
require_once(NINJA_FORMS_STYLE_DIR."/includes/admin/style-metabox-output.php");

require_once(NINJA_FORMS_STYLE_DIR."/includes/display/div-output.php");
require_once(NINJA_FORMS_STYLE_DIR."/includes/display/scripts.php");