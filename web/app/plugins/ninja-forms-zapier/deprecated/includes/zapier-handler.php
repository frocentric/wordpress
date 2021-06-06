<?php

if (!defined('ABSPATH'))
    exit;

/**
 * Parses form data and forwards webhook requests to
 * ninja_forms_zapier_post_to_webhook method for futher processing.
 */
function ninja_forms_zapier_ninja_forms_process() {
    if (!class_exists('Ninja_Forms')) {
        return;
    }

    global $ninja_forms_processing;

    // Get the current form_id
    $form_id = $ninja_forms_processing->get_form_ID();

    // Make sure id is set
    if (!$form_id) {
        return;
    }

    // Get fields
    $fields = array(
        'Date' => date('Y-m-d H:i:s')
    );
    $ninja_forms_fields = $ninja_forms_processing->get_all_fields();
    foreach ($ninja_forms_fields as $id => $value) {
        $field_settings = $ninja_forms_processing->get_field_settings($id);
        if ($field_settings['type'] != '_submit' && isset($field_settings['data']['label']) && $field_settings['data']['label']) {
            $fields[$field_settings['data']['label']] = $value;
        } else if ($field_settings['type'] != '_submit' 
                && (isset($field_settings['data']['calc_display_type']) && $field_settings['data']['calc_display_type'] == 'html')
                && (isset($field_settings['data']['calc_name']) && $field_settings['data']['calc_name']) ) {                   
                  $fields[$field_settings['data']['calc_name']] = $value;
      }         
    }

    // Send to active webhooks
    $form_data = Ninja_Forms()->form($form_id)->settings;
    $i = 0;
    if (isset($form_data) &&
            isset($form_data['zap_ids']) &&
            is_array($form_data['zap_ids']) &&
            count($form_data['zap_ids']) > 0) {
        foreach ($form_data['zap_ids'] as $zap_id) {
            if ($form_data['zap_statuss'][$i] == "true") {
                ninja_forms_zapier_post_to_webhook($form_data['zap_webhook_urls'][$i], $fields);
            }
            $i++;
        }
    }
}

add_action('ninja_forms_post_process', 'ninja_forms_zapier_ninja_forms_process');

//-----------------------------------------------------------------------------

/**
 * Tries to submit request to zapier.com.
 */
function ninja_forms_zapier_post_to_webhook($url, $fields) {
    // Headers
    $headers = array(
        'Accept: application/json',
        'Content-Type: application/json'
    );

    $response = wp_remote_post(
            $url, array(
        'method' => 'POST',
        'timeout' => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => $headers,
        'body' => $fields,
        'cookies' => array()
            )
    );

    // Cache request if failed
    if (is_wp_error($response)) {
        ninja_forms_zapier_save_to_file($url, $fields);
    }
}

//-----------------------------------------------------------------------------

/**
 * Saves failed request to zapier.com to file (cache).
 */
function ninja_forms_zapier_save_to_file($url, $fields) {
    $upload_dir = wp_upload_dir();
    $path = $upload_dir['basedir'] . '/ninja-forms-zapier/';
    $filename = time() . '-' . uniqid() . '.txt';

    // Create cache store directory if needed
    ninja_forms_zapier_create_store_dir($path);

    // Write cached request if directory is writable
    if (is_writable(dirname($path . $filename))) {
        $data = array(
            'url' => $url,
            'fields' => $fields
        );

        $request = fopen($path . $filename, 'w+');
        fwrite($request, serialize($data));
        fclose($request);
    }
}

//-----------------------------------------------------------------------------

/**
 * Creates a store directory under wp-content/uploads for plugin cache files.
 * Adds index.php to prevent directory listing.
 */
function ninja_forms_zapier_create_store_dir($path) {
    // Create path if not exists
    if (!file_exists($path)) {
        $is_created = mkdir($path, 0777, true);
        if (!$is_created) {
            return;
        }
    }

    // Add index.php to prevent directory listing
    if (!file_exists($path . 'index.php') &&
            is_writable(dirname($path . 'index.php'))) {

        $index = fopen($path . 'index.php', 'w');
        fwrite($index, "<?php" . "\n");
        fwrite($index, "// Silence is golden.");
        fclose($index);
    }
}
