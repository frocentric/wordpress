<?php

define( 'NINJA_FORMS_EDD_MC_PRODUCT_NAME', 'Mail Chimp' );

if ( ! function_exists( 'curl_version' ) ) {
	add_action( 'admin_notices', 'ninja_forms_mailchimp_2_9_x_curl_error' );
	function ninja_forms_mailchimp_2_9_x_curl_error() {
		?>
		<div class="notice notice-error">
			<p>
				<?php _e( '<strong>Please contact your host:</strong> PHP cUrl is not installed. Mailchimp for Ninja Forms requires cUrl and will not function properly. ', 'ninja-forms-mailchimp' ); ?>
			</p>
		</div>

		<?php
	}
	return false;
}

function ninja_forms_includes() {

	if( is_admin() ) {
		require_once plugin_dir_path( __FILE__ ) . 'includes/upgrades.php';
	}
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-notification-mailchimp.php';
}
add_action( 'plugins_loaded', 'ninja_forms_includes' );


/**
 * Plugin text domain
 *
 * @since       1.0
 * @return      void
 */
function ninja_forms_mc_textdomain() {

	// Set filter for plugin's languages directory
	$edd_lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
	$edd_lang_dir = apply_filters( 'ninja_forms_mc_languages_directory', $edd_lang_dir );

	// Load the translations
	load_plugin_textdomain( 'ninja-forms-mc', false, $edd_lang_dir );
}
add_action( 'init', 'ninja_forms_mc_textdomain' );

/**
 * Registers the new notification type
 *
 * @since       1.3
 * @return      void
 */
function ninja_forms_mc_register_notification( $types ) {

	$types['mailchimp'] = new NF_MailChimp_Notification;

	return $types;
}
add_filter( 'nf_notification_types', 'ninja_forms_mc_register_notification' );

/**
 * Add the Mail Chimp tab to the Plugin Settings screen
 *
 * @since       1.0
 * @return      void
 */
function ninja_forms_mc_add_tab() {

	if ( ! function_exists( 'ninja_forms_register_tab_metabox_options' ) )
		return;

	$tab_args              = array(
		'name'             => 'Mail Chimp',
		'page'             => 'ninja-forms-settings',
		'display_function' => '',
		'save_function'    => 'ninja_forms_save_license_settings',
	);
	ninja_forms_register_tab( 'mail_chimp', $tab_args );

}
add_action( 'admin_init', 'ninja_forms_mc_add_tab' );


/**
 * PRegister the settings in the Mail Chimp Tab
 *
 * @since       1.0
 * @return      void
 */
function ninja_forms_mc_add_plugin_settings() {

	if ( ! function_exists( 'ninja_forms_register_tab_metabox_options' ) )
		return;

	$mc_args = array(
		'page'     => 'ninja-forms-settings',
		'tab'      => 'mail_chimp',
		'slug'     => 'mail_chimp',
		'title'    => __( ' Mail Chimp', 'ninja-forms-mc' ),
		'settings' => array(
			array(
				'name' => 'ninja_forms_mc_api',
				'label' => __( 'Mail Chimp API Key', 'ninja-forms-mc' ),
				'desc' => __( 'Enter your Mail Chimp API key. This is found in your MailChimp "Extras" settings and looks like this: <em>xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-xxx</em>', 'ninja-forms-mc' ),
				'type' => 'text',
				'size' => 'regular'
			),
			array(
				'name' => 'ninja_forms_mc_disable_ssl_verify',
				'label' => __( 'Disable SSL Verification', 'ninja-forms-mc' ),
				'desc' => __( 'If you receive an error about validating the SSL certificate, enable this option', 'ninja-forms-mc' ),
				'type' => 'checkbox',
			)
		)
	);
	ninja_forms_register_tab_metabox( $mc_args );
}
add_action( 'admin_init', 'ninja_forms_mc_add_plugin_settings', 100 );

/**
 * Retrieve an array of Mail Chimp lists
 *
 * @since       1.0
 * @return      array
 */
function ninja_forms_mc_get_mailchimp_lists() {

	global $pagenow, $edd_settings_page;

	//if ( ! isset( $_GET['page'] ) || ! isset( $_GET['tab'] ) || $_GET['page'] != 'ninja-forms' || $_GET['tab'] != 'form_settings' )
	//	return;

	$options = get_option( "ninja_forms_settings" );

	if ( isset( $options['ninja_forms_mc_api'] ) && strlen( trim( $options['ninja_forms_mc_api'] ) ) > 0 ) {

		$lists = get_transient( 'nf_mailchimp_lists' );

		if( false === $lists ) {

			$lists = array();
			if ( ! class_exists( 'Mailchimp' ) ) {
				require_once 'Mailchimp.php';
			}

			try {

				$verify_ssl = isset( $options['ninja_forms_mc_disable_ssl_verify'] ) ? false : true;

				$opts = array(
					'debug' => defined( 'WP_DEBUG' ) && WP_DEBUG,
					'ssl_verifypeer' => $verify_ssl,
				);

				$api       = new Mailchimp( trim( $options['ninja_forms_mc_api'] ), $opts );
				$list_data = $api->call( 'lists/list', array( 'limit' => 100 ) );
				if ( $list_data ) {
					foreach ( $list_data['data'] as $key => $list ) {
						$lists[] = array(
							'value' => $list['id'],
							'name'  => $list['name']
						);
					}
				}

				set_transient( 'nf_mailchimp_lists', $lists, 60*60*60 );

			} catch( Exception $e ) {

				$lists = new WP_Error( 'invalid_api_key', __( 'The API key you have entered appears to be invalid', 'ninja-forms-mc' ) );

			}

		}

		return $lists;
	}
	return array();
}


/**
 * Subscribe an email address to a Mail Chimp list
 *
 * @since       1.0
 * @return      bool
 */
function ninja_forms_mc_subscribe_email( $subscriber = array(), $list_id = '', $double_opt = true ) {

	$options = get_option( "ninja_forms_settings" );

	if ( empty( $list_id ) || empty( $subscriber ) )
		return false;

	if ( ! class_exists( 'Mailchimp' ) ) {
		require_once 'Mailchimp.php';
	}

	$verify_ssl = isset( $options['ninja_forms_mc_disable_ssl_verify'] ) ? false : true;

	$opts = array(
		'debug' => defined( 'WP_DEBUG' ) && WP_DEBUG,
		'ssl_verifypeer' => $verify_ssl,
	);

	$api = new Mailchimp( trim( $options['ninja_forms_mc_api'] ), $opts );

	try {
		$result = $api->call( 'lists/subscribe', array(
			'id'                => $list_id,
			'email'             => array( 'email' => $subscriber['email'] ),
			'merge_vars'        => $subscriber['merge_vars'],
			'double_optin'      => $double_opt,
			'update_existing'   => true,
			'replace_interests' => false,
			'send_welcome'      => false,
		) );

	} catch( Mailchimp_Error $e ) {
		if( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			wp_die( __( 'Error', 'ninja-forms-mc' ), print_r( $e, true ), array( 'response' => 400 ) );
		}

		$result = false;

	} catch( Exception $e ) {
		if( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			wp_die( __( 'Error', 'ninja-forms-mc' ), print_r( $e, true ), array( 'response' => 400 ) );
		}

		$result = false;

	}

	return (bool) $result;
}

/**
 * Plugin Updater / licensing
 *
 * @since       1.0.2
 * @return      void
 */

function ninja_forms_mc_extension_setup_license() {
    if ( class_exists( 'NF_Extension_Updater' ) ) {
        $NF_Extension_Updater = new NF_Extension_Updater( 'MailChimp', '3.1.1', 'Pippin Williamson', __FILE__, 'mailchimp' );
    }
}
add_action( 'admin_init', 'ninja_forms_mc_extension_setup_license' );

if( ! function_exists( 'ninja_forms_get_ip' ) ) {

	/**
	 * Get User IP
	 *
	 * Returns the IP address of the current visitor
	 *
	 * @since 1.1
	 * @return string $ip User's IP address
	 */
	function ninja_forms_get_ip() {

		$ip = '127.0.0.1';

		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			//check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			//to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return apply_filters( 'ninja_forms_get_ip', $ip );
	}

}

/**
 * Validate the API key upon save
 *
 * @since       1.1.3
 * @return      void
 */
function ninja_forms_mc_validate_api_key() {

	if( ! isset( $_GET['page'] ) || 'ninja-forms-settings' != $_GET['page'] ) {
		return;
	}

	$options = get_option( 'ninja_forms_settings' );

	if( empty( $options['ninja_forms_mc_api'] ) ) {
		return;
	}

	if( ! empty( $_POST['ninja_forms_mc_api'] ) || ( isset( $options['ninja_forms_mc_api'] ) && $options['ninja_forms_mc_api'] !== $_POST['ninja_forms_mc_api'] ) ) {

		delete_transient( 'nf_mailchimp_lists' );

		if ( ! class_exists( 'Mailchimp' ) ) {
			require_once 'Mailchimp.php';
		}

		$verify_ssl = isset( $options['ninja_forms_mc_disable_ssl_verify'] ) ? false : true;

		try {

			$opts = array(
				'debug' => defined( 'WP_DEBUG' ) && WP_DEBUG,
				'ssl_verifypeer' => $verify_ssl,
			);
			$api = new Mailchimp( trim( $_POST['ninja_forms_mc_api'] ), $opts );
			$list_data = $api->call( 'lists/list', array( 'limit' => 1 ) );

		} catch( Exception $e ) {

			wp_die( sprintf( __( 'The API key you have entered appears to be invalid. Please go back and re-enter it. Error message: %s', 'ninja-forms-mc' ), $e->getMessage() ), __( 'Error', 'ninja-forms-mc' ), array( 'back_link' => true, 'response' => 401 ) );

		}

	} 

}
add_action( 'ninja_forms_save_admin_tab', 'ninja_forms_mc_validate_api_key' );

/**
 * Retrieve all merge vars
 *
 * @since       1.3
 * @return      void
 */
function ninja_forms_mc_get_merge_vars( $list_id = '' ) {

	if ( ! class_exists( 'Mailchimp' ) ) {
		require_once 'Mailchimp.php';
	}

	$options = get_option( 'ninja_forms_settings' );

	$verify_ssl = isset( $options['ninja_forms_mc_disable_ssl_verify'] ) ? false : true;

	$opts = array(
		'debug' => defined( 'WP_DEBUG' ) && WP_DEBUG,
		'ssl_verifypeer' => $verify_ssl,
	);

	$api  = new Mailchimp( trim( $options['ninja_forms_mc_api'] ), $opts );
	$vars = $api->lists->mergeVars( array( $list_id ) );

	if( ! empty( $vars['data'][0] ) ) {

		return $vars['data'][0]['merge_vars'];

	}

	return false;
}

/**
 * Retrieve all segments
 *
 * @since       1.3
 * @return      void
 */
function ninja_forms_mc_get_groups( $list_id = '' ) {

	if ( ! class_exists( 'Mailchimp' ) ) {
		require_once 'Mailchimp.php';
	}

	$data = get_transient( 'nf_mailchimp_groupings_' . $list_id );

	if( false === $data ) {

		$options = get_option( 'ninja_forms_settings' );

		$verify_ssl = isset( $options['ninja_forms_mc_disable_ssl_verify'] ) ? false : true;

		$opts = array(
			'debug' => defined( 'WP_DEBUG' ) && WP_DEBUG,
			'ssl_verifypeer' => $verify_ssl,
		);

		$segs = array();
		$api  = new Mailchimp( trim( $options['ninja_forms_mc_api'] ), $opts );
		$list_data = $api->call( 'lists/list', array( 'filters' => array( 'list_id' => $list_id ) ) );

		if ( $list_data['data'][0]['stats']['group_count'] > 0 ) {
			$data = $api->lists->interestGroupings( $list_id );			
		}

		set_transient( 'nf_mailchimp_groupings_' . $list_id, $data, 24*24*24 );

	}

	$groups_data = array();

	if( $data && ! isset( $data->status ) ) {

		foreach( $data as $key => $grouping ) {
			if ( is_array( $grouping['groups'] ) ) {
				$groups_data[ $key ] = array(
					'id'     => $grouping['id'],
					'name'   => $grouping['name'],
					'groups' => array()
				);

			
				foreach( $grouping['groups'] as $groups ) {

					$groups_data[ $key ]['groups'][] = array(
						'id'   => $groups['id'],
						'name' => $groups['name']
					);

				}				
			}
		}
	}

	return $groups_data;
}

/**
 * Adds a MailChimp opt-in option to the checkbox field
 *
 * @since       1.3
 * @return      void
 */
function ninja_forms_mc_field_checkbox_opt_in( $field_id, $field_data ) {

	$field = ninja_forms_get_field_by_id( $field_id );

	if ( '_checkbox' != $field['type'] ) {
		return false;
	}

	$nf_mc_opt_in = isset( $field_data['nf_mc_opt_in'] ) ? $field_data['nf_mc_opt_in'] : '';
?>
	<div class="description description-wide">
<?php
	ninja_forms_edit_field_el_output( $field_id, 'checkbox', __( 'Enable Opt-In Checkbox for MailChimp', 'ninja-forms' ), 'nf_mc_opt_in', $nf_mc_opt_in, 'wide', '', '', '' );
	?>
	</div>
<?php
}
add_action( 'nf_edit_field_advanced', 'ninja_forms_mc_field_checkbox_opt_in', 9, 2 );