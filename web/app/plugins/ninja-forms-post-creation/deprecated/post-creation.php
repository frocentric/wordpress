<?php

function ninja_forms_post_creation_setup_license() {
	if ( class_exists( 'NF_Extension_Updater' ) ) {
		$NF_Extension_Updater = new NF_Extension_Updater( 'Front-End Posting', NINJA_FORMS_POST_VERSION, 'WP Ninjas', __FILE__, 'post_creation' );
	}
}

add_action( 'admin_init', 'ninja_forms_post_creation_setup_license' );

/**
 * Load translations for add-on.
 * First, look in WP_LANG_DIR subfolder, then fallback to add-on plugin folder.
 */
function ninja_forms_post_creation_load_translations() {

  /** Set our unique textdomain string */
  $textdomain = 'ninja-forms-pc';

  /** The 'plugin_locale' filter is also used by default in load_plugin_textdomain() */
  $locale = apply_filters( 'plugin_locale', get_locale(), $textdomain );

  /** Set filter for WordPress languages directory */
  $wp_lang_dir = apply_filters(
    'ninja_forms_post_creation_wp_lang_dir',
    trailingslashit( WP_LANG_DIR ) . 'ninja-forms-pc/' . $textdomain . '-' . $locale . '.mo'
  );

  /** Translations: First, look in WordPress' "languages" folder = custom & update-secure! */
  load_textdomain( $textdomain, $wp_lang_dir );

  /** Translations: Secondly, look in plugin's "lang" folder = default */
  $plugin_dir = trailingslashit( basename( dirname( __FILE__ ) ) );
  $lang_dir = apply_filters( 'ninja_forms_post_creation_lang_dir', $plugin_dir . 'lang/' );
  load_plugin_textdomain( $textdomain, FALSE, $lang_dir );

}
add_action( 'plugins_loaded', 'ninja_forms_post_creation_load_translations' );

require_once(NINJA_FORMS_POST_DIR."/includes/admin/form-settings-metabox.php");
require_once(NINJA_FORMS_POST_DIR."/includes/admin/field-settings-sidebar.php");
require_once(NINJA_FORMS_POST_DIR."/includes/admin/scripts.php");
require_once(NINJA_FORMS_POST_DIR."/includes/admin/list-terms.php");

require_once(NINJA_FORMS_POST_DIR."/includes/display/processing/process.php");
require_once(NINJA_FORMS_POST_DIR."/includes/display/processing/update-terms.php");
require_once(NINJA_FORMS_POST_DIR."/includes/display/processing/term-name-filter.php");
require_once(NINJA_FORMS_POST_DIR."/includes/display/processing/update-post-meta.php");

require_once(NINJA_FORMS_POST_DIR."/includes/display/scripts.php");
require_once(NINJA_FORMS_POST_DIR."/includes/display/list-term-filter.php");

require_once(NINJA_FORMS_POST_DIR."/includes/fields/post-title.php");
require_once(NINJA_FORMS_POST_DIR."/includes/fields/post-content.php");
require_once(NINJA_FORMS_POST_DIR."/includes/fields/post-tags.php");
require_once(NINJA_FORMS_POST_DIR."/includes/fields/post-terms.php");
require_once(NINJA_FORMS_POST_DIR."/includes/fields/post-excerpt.php");

require_once(NINJA_FORMS_POST_DIR."/includes/ajax.php");
require_once(NINJA_FORMS_POST_DIR."/includes/meta-values.php");
require_once(NINJA_FORMS_POST_DIR."/includes/field-type-group.php");
require_once(NINJA_FORMS_POST_DIR."/includes/shortcodes.php");