<?php if ( ! defined( 'ABSPATH' ) ) exit;

// Global options for licensing
define('FATCAT_APPS_ZAPIER_PLUGIN', 'Zapier');
define('FATCAT_APPS_ZAPIER_VERSION', '1.1.2');
define('FATCAT_APPS_ZAPIER_AUTHOR', 'Fatcat Apps');
$zapier_plugin  = 'Ninja Forms - Zapier';
$zapier_version = '1.1.2';
$zapier_author  = 'Fatcat Apps';

// Do not edit
$zapier_plugin_path = __FILE__;

// Include plugin files
include_once "includes/zapier-metabox.php";
include_once "includes/zapier-handler.php";
include_once "includes/zapier-cron.php";

function ninja_forms_zapier_license()
{
  if ( class_exists( 'NF_Extension_Updater' ) ) {
    $NF_Extension_Updater = new NF_Extension_Updater(
      FATCAT_APPS_ZAPIER_PLUGIN,        // Plugin name
      FATCAT_APPS_ZAPIER_VERSION,       // Plugin version
      FATCAT_APPS_ZAPIER_AUTHOR,        // Plugin author/company
      __FILE__,
      ''                     // License prefix (optional)
    );
  }
}
add_action( 'admin_init', 'ninja_forms_zapier_license' );

/**
 * On activation, set up scheduled action hook.
 */
function ninja_forms_zapier_activation() {
  wp_schedule_event( time(), 'hourly', 'ninja_forms_zapier_cron_hourly' );
}
register_activation_hook( __FILE__, 'ninja_forms_zapier_activation' );

/**
 * On deactivation, remove all functions from the scheduled action hook.
 */
function ninja_forms_zapier_deactivation() {
  wp_clear_scheduled_hook( 'ninja_forms_zapier_cron_hourly' );
}
register_deactivation_hook( __FILE__, 'ninja_forms_zapier_deactivation' );
