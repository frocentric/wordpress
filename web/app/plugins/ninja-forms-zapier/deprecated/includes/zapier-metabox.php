<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Enqueues the JS and CSS necessary for our Zapier metabox
 */
function ninja_forms_zapier_enqueue_js()
{
  global $zapier_plugin_path;

  wp_enqueue_script('ninja-forms-zapier', plugin_dir_url( $zapier_plugin_path ) . 'js/zapier.js', array('jquery'));
  wp_enqueue_style('ninja-forms-zapier', plugin_dir_url( $zapier_plugin_path ) . 'css/style.css' );
}
add_action( 'admin_enqueue_scripts', 'ninja_forms_zapier_enqueue_js' );

//-----------------------------------------------------------------------------

/**
 * Registers our Zapier metabox
 */
function ninja_forms_zapier_register_metabox()
{

  $args = array(
    'page' => 'ninja-forms',
    'tab' => 'form_settings',
    'slug' => 'zapier-settings',
    'title' => __( 'Zapier Settings', 'ninja-forms-zapier' ),
    'display_function' => 'ninja_forms_zapier_metabox_content',
    'save_function' => 'ninja_forms_zapier_sync_message_check',
    'state' => 'closed',
  );

  if (function_exists('ninja_forms_register_tab_metabox')) {
    ninja_forms_register_tab_metabox($args);
  }

}
add_action( 'admin_init', 'ninja_forms_zapier_register_metabox', 100 );

//-----------------------------------------------------------------------------

/**
 * Formats the data into required data structures
 * Called from within ninja_forms_zapier_metabox_content()
 * @param  string $form_id  the id of our Ninja Form
 */
function ninja_forms_zapier_metabox_content_data($form_id = '')
{
  $data = array();
  if ( !class_exists( 'Ninja_Forms' ) ) {
      return $data;
  }

  if (is_int($form_id) && preg_match('/^([0-9]+)+$/sim', $form_id)) {
    $i = 0;
    $form_data = Ninja_Forms()->form( $form_id )->settings;
    if (isset($form_data) &&
        isset($form_data['zap_ids']) &&
        is_array($form_data['zap_ids']) &&
        count($form_data['zap_ids']) > 0)
    {
      foreach ($form_data['zap_ids'] as $zap_id) {
        $data[] = array(
          'zap_id' => $zap_id,
          'zap_name' => (isset($form_data['zap_names'][$i])?$form_data['zap_names'][$i]:''),
          'zap_webhook_url' => (isset($form_data['zap_webhook_urls'][$i])?$form_data['zap_webhook_urls'][$i]:''),
          'zap_status' => (isset($form_data['zap_statuss'][$i])?$form_data['zap_statuss'][$i]:'')
        );
        $i++;
      }
    }
  }

  return $data;
}

//-----------------------------------------------------------------------------

/**
 * Generates the content of our Zapier metabox.
 * Called from within ninja_forms_zapier_register_metabox()
 * @param  string $form_id  the id of our Ninja Form
 */
function ninja_forms_zapier_metabox_content($form_id = '')
{
  $data = ninja_forms_zapier_metabox_content_data($form_id);
  ?>

  <h2 class="zap-metabox-1">
    <?php _e('All Zaps', 'ninja-forms-zapier'); ?>
    <a href="#" class="add-new-h2">
      <?php _e('Add New', 'ninja-forms-zapier');?>
    </a>
  </h2>

  <table class="wp-list-table widefat fixed zap-list-table zap-metabox-1">
    <thead>
      <tr>
        <th scope="col" class="manage-column column-zap-name">
          <?php _e('Name', 'ninja-forms-zapier');?>
        </th>
        <th scope="col" class="manage-column column-zap-webhook-url">
          <?php _e('Webhook URL', 'ninja-forms-zapier'); ?>
        </th>
        <th scope="col" class="manage-column column-zap-status">
          <?php _e('Status', 'ninja-forms-zapier'); ?>
        </th>
      </tr>
    </thead>
    <tbody class="zap-list">
      <?php echo ninja_forms_zapier_table($data); ?>
    </tbody>
  </table>

  <div class="zap-metabox-2" style="display:none;">
    <?php echo ninja_forms_zapier_form(); ?>
  </div>

  <div class="zap-metabox-0">
    <?php echo ninja_forms_zapier_fields($data); ?>
  </div>

  <?php
}

//-----------------------------------------------------------------------------

/**
 * Generates the HTML table + form for our Zapier metabox.
 * Called form within ninja_forms_zapier_metabox_content.
*/
function ninja_forms_zapier_form ()
{
  ?>
  <table class="form-table">
    <tbody>
      <tr id="row_zap_name">
        <th scope="row">
          <label for="zap_name"><?php _e('Name', 'ninja-forms-zapier'); ?></label>
        </th>
        <td>
          <input type="text" name="zap_name" id="zap_name" class="widefat" value="" />
        </td>
      </tr>
      <tr id="row_zap_webhook_url">
        <th scope="row">
          <label for="zap_webhook_url"><?php _e('Webhook URL', 'ninja-forms-zapier'); ?></label>
        </th>
        <td>
          <input type="text" name="zap_webhook_url" id="zap_webhook_url" class="widefat" value=""/>
          <p class="description">Webhook URL will be provided during the wizard when creating new zap on <a href="https://zapier.com">zapier.com</a></p>
        </td>
      </tr>

      <tr id="row_zap_status">
        <th scope="row">
          <label for="zap_status"><?php _e('Active', 'ninja-forms-zapier'); ?></label>
        </th>
        <td>
          <input type="checkbox" name="zap_status" id="zap_status"/>
        </td>
      </tr>
      <tr id="row_button">
        <th colspan="2">
          <input type="hidden" id="zap_id" name="zap_id" value=""/>
          <input type="button" class="button" id="zap_cancel" value="<?php _e('Cancel', 'ninja-forms-zapier'); ?>"/>
          <input type="button" class="button button-primary" id="zap_submit" value="<?php _e('Save Zap', 'ninja-forms-zapier'); ?>"/>
        </th>
      </tr>
    </tbody>
  </table>
  <?php
}

//-----------------------------------------------------------------------------

/**
 * Generates the table and fills it with data.
 * Called from within ninja_forms_zapier_delete and ninja_forms_zapier_save.
 * @param  array  $data  an array filled with elements such as zap_name  or zap_webhook_url
 */
function ninja_forms_zapier_table($data = array())
{
  $content = '';
  if (count($data) > 0) {
    $row = 'alternate';
    foreach ($data as $item) {
      $content .=
        '<tr id="zap-' . $item['zap_id'] . '" class="zap-' . $item['zap_id'] . ' format-standard ' . $row . ' iedit author-self level-0">' .
          '<td class="column-title">' .
            '<strong>' . $item['zap_name'] . '</strong>' .
            '<div class="row-actions">' .
              '<span class="edit"><a href="'.admin_url('admin-ajax.php?action=ninja-forms-zapier-edit&zap_id=' . $item['zap_id']).'">'.__('Edit', 'ninja-forms-zapier').'</a> | </span>' .
              '<span class="trash"><a href="'.admin_url('admin-ajax.php?action=ninja-forms-zapier-delete&zap_id=' . $item['zap_id']).'">'.__('Trash', 'ninja-forms-zapier').'</a></span>' .
            '</div>' .
          '</td>' .
          '<td>' . $item['zap_webhook_url'] . '</td>' .
          '<td>' .
            (($item['zap_status'] == 'true') ? __('Active', 'ninja_forms_zapier') : __('Inactive', 'ninja_forms_zapier')) .
          '</td>' .
        '</tr>';

      $row = ($row == 'alternate')?'':'alternate';
    }
  }

  if (count($data) == 0) {
    $content =
      '<tr>' .
        '<td colspan="3">' .
          __('No zaps were found for this form.', 'ninja-forms-zapier') .
        '</td>' .
      '</tr>';
  }

  return $content;
}

//-----------------------------------------------------------------------------

/**
 * [ninja_forms_zapier_fields description]
 * @param  array  $data [description]
 * @return string       the content
 */
function ninja_forms_zapier_fields($data = array())
{
  $content = '';

  if (count($data) > 0) {
    foreach ($data as $item) {
      $status = (isset($item['zap_status']) && $item['zap_status'] == 'true')?'true':'';
      $content .=
        '<div class="zap-metabox-0-group">'.
          '<input type="hidden" name="zap_ids[]" value="' . $item['zap_id'] . '"/>' .
          '<input type="hidden" name="zap_names[]" value="' . $item['zap_name'] . '"/>' .
          '<input type="hidden" name="zap_webhook_urls[]" value="' . $item['zap_webhook_url'] . '"/>' .
          '<input type="hidden" name="zap_statuss[]" value="' . $status . '"/>' .
        '</div>';
      $content .= "\n";
    }
  } else {
    $content =
    '<div class="zap-metabox-0-none">'.
      '<input type="hidden" name="zap_ids" value=""/>' .
      '<input type="hidden" name="zap_names" value=""/>' .
      '<input type="hidden" name="zap_webhook_urls" value=""/>' .
      '<input type="hidden" name="zap_statuss" value=""/>' .
    '</div>';
  }

  return $content;
}

//-----------------------------------------------------------------------------

/**
 * To delete a zap
 */
function ninja_forms_zapier_delete()
{
  // Check if id's are set
  $zap_id  = isset($_GET['zap_id'])?$_GET['zap_id']:'';
  if (!preg_match("/^([0-9a-z]+)+$/sim", $zap_id))
  {
    echo json_encode( array( 'success' => false ) );
    exit();
  }

  // Remove entry
  parse_str($_POST['raw'], $form);
  $zapier_hooks = ninja_forms_zapier_parse_form($form);
  $new_zapier_hooks = array();

  foreach ($zapier_hooks as $item) {
    if ($item['zap_id'] != $zap_id) {
      $new_zapier_hooks[] = $item;
    }
  }

  // Return html content
  $table   = ninja_forms_zapier_table($new_zapier_hooks);
  $metabox = ninja_forms_zapier_fields($new_zapier_hooks);
  echo json_encode ( array( 'success' => true, 'table' => $table, 'metabox' => $metabox ) );
  exit();
}
add_action('wp_ajax_ninja-forms-zapier-delete', 'ninja_forms_zapier_delete');

//-----------------------------------------------------------------------------

/**
 * To edit a zap
 */
function ninja_forms_zapier_edit()
{

  // Check if id's are set
  $zap_id  = isset($_GET['zap_id'])?$_GET['zap_id']:'';
  if (!preg_match("/^([0-9a-z]+)+$/sim", $zap_id))
  {
    echo json_encode( array( 'success' => false ) );
    exit();
  }

  // Edit entry
  parse_str($_POST['raw'], $form);
  $zapier_hooks = ninja_forms_zapier_parse_form($form);

  foreach ($zapier_hooks as $item) {
    if ($item['zap_id'] == $zap_id) {
      echo json_encode(
        array(
          'zap_id' => $item['zap_id'],
          'zap_name' => $item['zap_name'],
          'zap_webhook_url' => $item['zap_webhook_url'],
          'zap_status' => ((isset($item['zap_status']) && $item['zap_status'] == 'true')?'true':'false'),
          'success' => true,
        )
      );
      exit();
    }
  }

  echo json_encode( array( 'success' => false ) );
  exit();
}
add_action('wp_ajax_ninja-forms-zapier-edit', 'ninja_forms_zapier_edit');

//-----------------------------------------------------------------------------

/**
 * To save a zap
 */
function ninja_forms_zapier_save()
{
  // Form data
  parse_str($_POST['raw'], $form);
  $zap_id = (isset($form['zap_id']) && $form['zap_id'])?$form['zap_id']:uniqid();
  $zapier_hooks = ninja_forms_zapier_parse_form($form);

  // Add/Update
  if (isset($form['zap_id']) && preg_match('/^([0-9a-z]+)+$/sim', $form['zap_id'])) {
    $new_zapier_hooks = array();
    foreach ($zapier_hooks as $item) {
      if ($item['zap_id'] == $zap_id) {
        $new_zapier_hooks[] = array(
          'zap_id' => $item['zap_id'],
          'zap_name' => $form['zap_name'],
          'zap_webhook_url' => $form['zap_webhook_url'],
          'zap_status' => ((isset($form['zap_status']) && $form['zap_status'])?'true':'')
        );
      } else {
        $new_zapier_hooks[] = $item;
      }
    }

    $zapier_hooks = $new_zapier_hooks;
  } else {
    $zapier_hooks[] = array(
      'zap_id' => $zap_id,
      'zap_name' => $form['zap_name'],
      'zap_webhook_url' => $form['zap_webhook_url'],
      'zap_status' => (isset($form['zap_status'])?'true':'false')
    );
  }

  // Return html content
  $table   = ninja_forms_zapier_table($zapier_hooks);
  $metabox = ninja_forms_zapier_fields($zapier_hooks);
  echo json_encode ( array( 'success' => true, 'table' => $table, 'metabox' => $metabox ) );
  exit();
}
add_action('wp_ajax_ninja-forms-zapier-save', 'ninja_forms_zapier_save');

//-----------------------------------------------------------------------------

/**
 * Takes the data from our Zapier metabox form and returns an
 * @param  $form the form
 * @return array $zapier_hooks contains the hooks that will be passed on to the Zapier API
 */
function ninja_forms_zapier_parse_form($form)
{
  $zapier_hooks = array();

  if (isset($form['zap_ids'])) {
    $i = 0;
    if (is_array($form['zap_ids']) && count($form['zap_ids']) > 0) {
      foreach ($form['zap_ids'] as $id) {
        $zapier_hooks[] = array(
          'zap_id' => $id,
          'zap_name' => (isset($form['zap_names'][$i])?$form['zap_names'][$i]:''),
          'zap_webhook_url' => (isset($form['zap_webhook_urls'][$i])?$form['zap_webhook_urls'][$i]:''),
          'zap_status' => ((isset($form['zap_statuss'][$i]) && $form['zap_statuss'][$i] == 'true')?'true':'')
        );
        $i++;
      }
    }
  }

  return $zapier_hooks;
}

//-----------------------------------------------------------------------------

/**
 * Check if sync with Zapier is required and enqueues admin notice if needed.
 */
function ninja_forms_zapier_sync_message_check($form_id, $data)
{
  if (isset($data['zap_ids']) && !empty($data['zap_ids'])) {
    $settings = Ninja_Forms()->form( $form_id )->get_all_settings();

    $zap_ids = ninja_forms_zapier_identical_values( $data['zap_ids'], $settings['zap_ids'] );
    $zap_webhook_urls = ninja_forms_zapier_identical_values( $data['zap_webhook_urls'], $settings['zap_webhook_urls'] );
    $zap_statuss = ninja_forms_zapier_identical_values( $data['zap_statuss'], $settings['zap_statuss'] );

    if ( (false == $zap_ids || false == $zap_webhook_urls || false == $zap_statuss)
      && is_array( $data['zap_ids'] ) && count( $data['zap_ids'] ) > 0 ) {

      add_action( 'admin_notices', 'ninja_forms_zapier_sync_message' );
    }
  }
}

//-----------------------------------------------------------------------------

/**
 * Compares to arrays, returns false if they not identical.
 */
function ninja_forms_zapier_identical_values( $arrayA , $arrayB )
{
  if ( is_array($arrayA) ) {
    sort( $arrayA );
  }

  if ( is_array($arrayB) ) {
    sort( $arrayB );
  }

  return $arrayA == $arrayB;
}

//-----------------------------------------------------------------------------

/**
 * Displays sync message to user
 */
function ninja_forms_zapier_sync_message( $return = FALSE )
{
  $form_id = $_GET['form_id'];

  ob_start();
  ?>
    <div class="error" id="ninja_forms_zapier_sync">
      <p><?php _e( "You've modified your form. Please sync your changes with Zapier when you're done editing.", "ninja_forms_zapier" ); ?></p>
      <button class="button button-primary" data-form-id="<?php echo $form_id; ?>"><?php _e("Sync", "ninja_forms_zapier"); ?></button>
      <div class="clear"></div>
    </div>
  <?php
  $r = ob_get_clean();

  return $return ? $r : print( $r );
}

//-----------------------------------------------------------------------------

/**
 * Sync dummy form data with Zapier
 */
function nija_forms_zapier_sync()
{
  $defaults = array(
    '_text' => __('Hello', 'ninja_forms_zapier'),
    '_list' => __('Option 1', 'ninja_forms_zapier'),
    '_honeypot' => '',
    '_textarea' => __('I just wanted to say hi =)', 'ninja_forms_zapier'),
    '_rating' => '5',
    '_number' => '1',
    '_checkbox' => 'checked',
    '_hidden' => '',
    '_spam' => __('I\'m human.', 'ninja_forms_zapier'),
    '_timed_submit' => '0',
    '_profile_pass' => 'uUEWmhfFP6',
    '_calc' => '0.00',
    '_hr' => '0',
    '_desc' => '',
    '_country' => 'US',
    '_tax' => '10%',
  );

  $defautls_by_label = array(
    'First Name' => __('Jeffrey', 'ninja_forms_zapier'),
    'Last Name' => __('Bower', 'ninja_forms_zapier'),
    'Address 1' => __('Apt C', 'ninja_forms_zapier'),
    'Address 2' => __('Address 2', 'ninja_forms_zapier'),
    'City' => __('Birmingham', 'ninja_forms_zapier'),
    'State' => __('AL', 'ninja_forms_zapier'),
    'Zip / Post Code' => '35291',
    'Email' => 'jeff@domain.com',
    'Phone' => '205-390-6565',
    'Sub Total' => '200.00',
    'Total' => '220.00'
  );

  $success = false;
  $form_id = absint($_REQUEST['form_id']);
  if (preg_match('/^([0-9]+)+$/sim', $form_id)) {
    $settings   = Ninja_Forms()->form( $form_id )->get_all_settings();
    $fields = ninja_forms_get_fields_by_form_id($form_id);

    // Extract form fields and populate with dummy data
    $zapier_fields = array(
      'Date' => date('Y-m-d H:i:s')
    );

    foreach ($fields as $field) {
      if ($field['type'] != '_submit' && isset($field['data']['label']) && $field['data']['label'] ) {
        if ( array_key_exists( $field['data']['label'], $defautls_by_label ) ) {
          $zapier_fields[$field['data']['label']] = $defautls_by_label[$field['data']['label']];
        } else {
          if ( array_key_exists( $field['type'], $defaults ) ) {
            $zapier_fields[$field['data']['label']] = $defaults[$field['type']];
          } else {
            $zapier_fields[$field['data']['label']] = '';
          }
        }
      }else if ($field['type'] != '_submit' 
                && (isset($field['data']['calc_display_type']) && $field['data']['calc_display_type'] == 'html')
                && (isset($field['data']['calc_name']) && $field['data']['calc_name']) ) {       
                if ( array_key_exists( $field['type'], $defaults ) ) {
                  $zapier_fields[$field['data']['calc_name']] = $defaults[$field['type']];
                } else {
                  $zapier_fields[$field['data']['calc_name']] = '';
                }
      }      
    }

    // Execute all active zaps
    if ( isset( $settings ) && isset( $settings['zap_webhook_urls'] ) ) {
      $hooks = count( $settings['zap_webhook_urls'] );
      if ($hooks > 0) {
        for ($i=0; $i<$hooks; $i++) {
          if ( isset( $settings['zap_statuss'] )
            && isset( $settings['zap_statuss'][$i] )
            && 'true' == $settings['zap_statuss'][$i] ) {

            // Execute (cache) request to Zapier
            ninja_forms_zapier_post_to_webhook(
              $settings['zap_webhook_urls'][$i],
              $zapier_fields
            );
          }
        }
      }
    }

    // Update sync status
    ninja_forms_zapier_save_sync_status($form_id, '0');

    // Result
    $success = true;
  }

  echo json_encode(array('success' => $success));
  exit();
}
add_action('wp_ajax_ninja-forms-zapier-sync', 'nija_forms_zapier_sync');

//-----------------------------------------------------------------------------

/**
 * Show admin notice when fields are updated
 */
function ninja_forms_zapier_form_fields_saved( $current_tab )
{
  if ( 'form_settings' == $current_tab ) {
    if ( isset($_GET['form_id']) ) {
      $form_id = absint( $_GET['form_id'] );
      $settings = Ninja_Forms()->form( $form_id )->get_all_settings();

      if ( isset($settings['zap_ids']) && is_array($settings['zap_ids'])
        && count($settings['zap_ids']) > 0 ) {

        // Save status
        ninja_forms_zapier_save_sync_status($form_id, '1');

        // Show notification
        add_action( 'admin_notices', 'ninja_forms_zapier_sync_message' );
      }
    }
  }
}
add_action('ninja_forms_save_admin_tab', 'ninja_forms_zapier_form_fields_saved');

//-----------------------------------------------------------------------------

/**
 * Show sync message when new form is being created with at least one zap
 */
function ninja_forms_zapier_new_form_created()
{

  // Set cookie (for sync) if user is creating new form
  if ( isset( $_GET['page'] ) && 'ninja-forms' == $_GET['page'] && isset( $_GET['form_id'] ) ) {
    $form_id = $_GET['form_id'];
    if ( 'new' == $form_id ) {
      setcookie('ninja_forms_zapier_sync', '1', strtotime('+1 hour'));
    } else {
      setcookie('ninja_forms_zapier_sync', null, strtotime('-1 day'));
    }
  }

  // Show sync message
  if ( isset($_COOKIE['ninja_forms_zapier_sync']) && isset( $_GET['form_id'] ) ) {
    $form_id = absint( $_GET['form_id'] );
    if ( $form_id ) {
      $settings = Ninja_Forms()->form( $form_id )->get_all_settings();
      if ( isset($settings['zap_ids']) && is_array($settings['zap_ids'])
        &&count($settings['zap_ids']) ) {

        // Save status
        ninja_forms_zapier_save_sync_status($form_id, '1');

        // Show notification
        add_action( 'admin_notices', 'ninja_forms_zapier_sync_message' );
      }
    }

    setcookie('ninja_forms_zapier_sync', '', strtotime('-1 day'));
  }
}
add_action('admin_init', 'ninja_forms_zapier_new_form_created');

//-----------------------------------------------------------------------------

/**
 * Saves zapier sync status inside ninja forms table
 */
function ninja_forms_zapier_save_sync_status( $form_id, $status = '0')
{
  global $wpdb;

  $settings = Ninja_Forms()->form( $form_id )->get_all_settings();
  $settings['zap_sync'] = $status;

  $wpdb->update( NINJA_FORMS_TABLE_NAME, array( 'data' => serialize( $settings ) ), array( 'id' => $form_id ) );
}

//-----------------------------------------------------------------------------

/**
 * Performs check and adds admin_notices action if form is yet not synced.
 */
function ninja_forms_zapier_check_sync_status()
{
  if ( isset( $_GET['page'] ) && 'ninja-forms' == $_GET['page'] && isset( $_GET['form_id'] ) ) {
    $form_id = absint($_GET['form_id']);
    $settings = Ninja_Forms()->form( $form_id )->get_all_settings();
    if ( isset( $settings )  && isset( $settings['zap_sync'] ) && 1 == $settings['zap_sync'] ) {
      add_action( 'admin_notices', 'ninja_forms_zapier_sync_message' );
    }
  }
}
add_action('admin_init', 'ninja_forms_zapier_check_sync_status');

//-----------------------------------------------------------------------------

/**
 * Prompts user for syncing if after saving in builder tab
 */
add_action( 'admin_footer', 'ninja_forms_zapier_check_sync_status_2' );
function ninja_forms_zapier_check_sync_status_2() {

  $is_NF = 1
    && isset( $_GET[ 'page' ] )
    && 'ninja-forms' === $_GET[ 'page' ]
  ;

  $is_NF_builder = 1
    && $is_NF
    && isset( $_GET[ 'tab' ] )
    && 'builder' === $_GET[ 'tab' ]
  ;

  if ( ! $is_NF_builder ) {
    return;
  }

  $msg = json_encode( array (
    'str' => ninja_forms_zapier_sync_message( true ),
  ) );

  ?><script>

    jQuery( document ).ready( function( $ ) {

      $( document ).ajaxComplete( function( event,request, settings ) {

        qs = settings.data
        qs_obj = (settings.data || document.location.search).replace(/(^\?)/,'').split("&").map(function(n){return n = n.split("="),this[n[0]] = n[1],this}.bind({}))[0];

        if ( "nf_admin_save_builder" === qs_obj.action ) {

          var msg = <?php echo $msg; ?>;

          if ( ! $( '#ninja_forms_zapier_sync' ).length ) {
            $( '#message' ).after( msg.str );
          }
          $( '#ninja_forms_zapier_sync' ).fadeIn();
        }
      });
    });
  </script><?php
}
