<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Executed by scheduler. Reads directory and submits valid cached requests
 * to zapier.com
 */
function ninja_forms_zapier_cron_hourly()
{
  $upload_dir = wp_upload_dir();
  $path = $upload_dir['basedir'] . '/ninja-forms-zapier/';

  if (file_exists($path)) {
    if ($handle = opendir($path)) {
      while (false !== ($entry = readdir($handle))) {
        if ( preg_match("/^([0-9]{10}\-[0-9a-z]+\.txt)$/sim", $entry) ) {
          // Read file
          $data = ninja_forms_zapier_read_cache_file($path . $entry);

          // Process request
          if (isset($data['url']) && isset($data['fields'])) {
            ninja_forms_zapier_post_to_webhook($data['url'], $data['fields']);
          }

          // Remove cache file
          unlink($path . $entry);
        }
      }
      closedir($handle);
    }
  }
}
add_action( 'ninja_forms_zapier_cron_hourly', 'ninja_forms_zapier_cron_hourly' );

//-----------------------------------------------------------------------------

/**
 * Reads cache file
 * Called from within ninja_forms_zapier_process_request_cache()
 * @param  string $path  path to the file in filesystem
 */
function ninja_forms_zapier_read_cache_file($path)
{
  $content = file_get_contents($path);
  $data = unserialize($content);

  return $data;
}
