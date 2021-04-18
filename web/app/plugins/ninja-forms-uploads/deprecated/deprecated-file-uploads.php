<?php

global $wpdb;

define("NINJA_FORMS_UPLOADS_DIR", dirname( __FILE__ ) );
define("NINJA_FORMS_UPLOADS_URL", plugins_url()."/".basename( dirname( NF_File_Uploads()->plugin_file_path ) ) . '/deprecated'  );
define("NINJA_FORMS_UPLOADS_TABLE_NAME", $wpdb->prefix . "ninja_forms_uploads");
define("NINJA_FORMS_UPLOADS_VERSION", NF_File_Uploads()->plugin_version );
define("NINJA_FORMS_UPLOADS_DEFAULT_LOCATION", 'server' );

require_once(NINJA_FORMS_UPLOADS_DIR."/includes/admin/pages/ninja-forms-uploads/tabs/browse-uploads/browse-uploads.php");
require_once(NINJA_FORMS_UPLOADS_DIR."/includes/admin/pages/ninja-forms-uploads/tabs/browse-uploads/sidebars/select-uploads.php");
require_once(NINJA_FORMS_UPLOADS_DIR."/includes/admin/pages/ninja-forms-uploads/tabs/upload-settings/upload-settings.php");
require_once(NINJA_FORMS_UPLOADS_DIR."/includes/admin/pages/ninja-forms-uploads/tabs/external-settings/external-settings.php");
require_once(NINJA_FORMS_UPLOADS_DIR."/includes/admin/scripts.php");
require_once(NINJA_FORMS_UPLOADS_DIR."/includes/admin/help.php");
require_once(NINJA_FORMS_UPLOADS_DIR."/includes/admin/csv-filter.php");
require_once(NINJA_FORMS_UPLOADS_DIR."/includes/admin/add-attachment-type.php");
require_once(NINJA_FORMS_UPLOADS_DIR."/includes/admin/upgrade-functions.php");


// External location class loader
require_once( NINJA_FORMS_UPLOADS_DIR . '/includes/external/external.php' );
$external_dir = glob( NINJA_FORMS_UPLOADS_DIR . '/includes/external/*.php' );
if ( $external_dir ) {
	foreach ( $external_dir as $dir ) {
		if ( basename( $dir, '.php' ) == 'external' ) {
			continue;
		}
		$external = NF_Upload_External::instance( $dir, true );
	}
}

require_once(NINJA_FORMS_UPLOADS_DIR."/includes/display/processing/pre-process.php");
require_once(NINJA_FORMS_UPLOADS_DIR."/includes/display/processing/process.php");
require_once(NINJA_FORMS_UPLOADS_DIR."/includes/display/processing/attach-image.php");
require_once(NINJA_FORMS_UPLOADS_DIR."/includes/display/processing/shortcode-filter.php");
require_once(NINJA_FORMS_UPLOADS_DIR."/includes/display/processing/post-meta-filter.php");
require_once(NINJA_FORMS_UPLOADS_DIR."/includes/display/processing/email-value-filter.php");
require_once(NINJA_FORMS_UPLOADS_DIR."/includes/deprecated.php");

require_once(NINJA_FORMS_UPLOADS_DIR."/includes/display/scripts.php");
require_once(NINJA_FORMS_UPLOADS_DIR."/includes/display/mp-confirm-filter.php");

require_once(NINJA_FORMS_UPLOADS_DIR."/includes/fields/file-uploads.php");

require_once(NINJA_FORMS_UPLOADS_DIR."/includes/activation.php");
require_once(NINJA_FORMS_UPLOADS_DIR."/includes/ajax.php");
require_once(NINJA_FORMS_UPLOADS_DIR."/includes/functions.php");


//Add File Uploads to the admin menu
add_action('admin_menu', 'ninja_forms_add_upload_menu', 99);
function ninja_forms_add_upload_menu(){
	$capabilities = 'administrator';
	$capabilities = apply_filters( 'ninja_forms_admin_menu_capabilities', $capabilities );

	$uploads = add_submenu_page("ninja-forms", "File Uploads", "File Uploads", $capabilities, "ninja-forms-uploads", "ninja_forms_admin");
	add_action('admin_print_styles-' . $uploads, 'ninja_forms_admin_js');
	add_action('admin_print_styles-' . $uploads, 'ninja_forms_uploads_admin_js');
	add_action('admin_print_styles-' . $uploads, 'ninja_forms_admin_css');
}

register_activation_hook( NF_File_Uploads()->plugin_file_path, 'ninja_forms_uploads_activation' );

$plugin_settings = get_option( 'ninja_forms_settings' );

if( isset( $plugin_settings['uploads_version'] ) ){
	$current_version = $plugin_settings['uploads_version'];
}else{
	$current_version = 0.4;
}

if( version_compare( $current_version, '0.5', '<' ) ){
	ninja_forms_uploads_activation();
}

/**
 * Load translations for add-on.
 * First, look in WP_LANG_DIR subfolder, then fallback to add-on plugin folder.
 */
function ninja_forms_uploads_load_translations() {

	/** Set our unique textdomain string */
	$textdomain = 'ninja-forms-uploads';

	/** The 'plugin_locale' filter is also used by default in load_plugin_textdomain() */
	$locale = apply_filters( 'plugin_locale', get_locale(), $textdomain );

	/** Set filter for WordPress languages directory */
	$wp_lang_dir = apply_filters(
		'ninja_forms_uploads_wp_lang_dir',
		trailingslashit( WP_LANG_DIR ) . 'ninja-forms-uploads/' . $textdomain . '-' . $locale . '.mo'
	);

	/** Translations: First, look in WordPress' "languages" folder = custom & update-secure! */
	load_textdomain( $textdomain, $wp_lang_dir );

	/** Translations: Secondly, look in plugin's "lang" folder = default */
	$plugin_dir = trailingslashit( basename( dirname( NF_File_Uploads()->plugin_file_path ) ) );
	$lang_dir = apply_filters( 'ninja_forms_uploads_lang_dir', $plugin_dir . 'languages/' );
	load_plugin_textdomain( $textdomain, FALSE, $lang_dir );

}

add_action( 'init', 'ninja_forms_uploads_load_translations' );

function nf_fu_load_externals() {
	// External location class loader
	require_once( NINJA_FORMS_UPLOADS_DIR . '/includes/external/external.php' );
	$external_dir = glob( NINJA_FORMS_UPLOADS_DIR . '/includes/external/*.php' );
	if ( $external_dir ) {
		foreach ( $external_dir as $dir ) {
			if ( basename( $dir, '.php' ) == 'external' ) {
				continue;
			}
			$external = NF_Upload_External::instance( $dir, true );
		}
		$external = NF_Upload_External::instance( $dir, true );
	}
}


function nf_fu_pre_27() {
	if ( defined( 'NINJA_FORMS_VERSION' ) ) {
		if ( version_compare( NINJA_FORMS_VERSION, '2.7' ) == -1 ) {
			return true;
		} else {
			return false;
		}
	} else {
		return null;
	}
}

//Save User Progress Table Column
add_filter( 'nf_sp_user_sub_table' , 'nf_fu_sp_user_sub_table', 10, 2 );
function nf_fu_sp_user_sub_table( $user_value, $field_id ) {

	$field = ninja_forms_get_field_by_id( $field_id );

	if ( isset( $field['type'] ) AND '_upload' == $field['type'] ) {

		$file_names = array();

		foreach ( $user_value as $value ) {
			$file_names[] = $value['file_name'];
		}

		return implode( ', ', $file_names );
	}

	return $user_value;
}