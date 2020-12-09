<?php
define( 'WPEA_LICENSE_SITE_URL', 'https://xylusthemes.com' );
define( 'WPEA_PRO_PLUGIN_NAME', 'WP Event Aggregator Pro' );

// the name of the settings page for the license input to be displayed
define( 'WPEA_PRO_PLUGIN_LICENSE_PAGE', 'wpea_license' );

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
}

function wpea_pro_plugin_updater() {

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'wpea_pro_license_key' ) );

	// setup the updater
	$edd_updater = new EDD_SL_Plugin_Updater( WPEA_LICENSE_SITE_URL, WPEAPRO_PLUGIN_FILE, array(
			'version'   => WPEAPRO_VERSION,      // current version number
			'license'   => $license_key,         // license key (used get_option above to retrieve from DB)
			'item_name' => WPEA_PRO_PLUGIN_NAME, // name of this plugin
			'author'    => 'Xylus Themes', 		 // author of this plugin
			'beta'		=> false
		)
	);

}
add_action( 'admin_init', 'wpea_pro_plugin_updater', 0 );

function wpea_pro_license_page() {
	$license = get_option( 'wpea_pro_license_key' );
	$status  = get_option( 'wpea_pro_license_status' );
	?>
	<div class="wpea_container">
    <div class="wpea_row">
        <div class="xtei-column wpea_well">
		<h3><?php _e( ' License Options', 'wp-event-aggregator-pro'); ?></h3>
		<form method="post" action="options.php">

			<?php settings_fields('wpea_pro_license'); ?>

			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" valign="top">
							<?php _e('License Key',  'wp-event-aggregator-pro'); ?>
						</th>
						<td>
							<input id="wpea_pro_license_key" name="wpea_pro_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" placeholder="<?php _e('Enter your license key',  'wp-event-aggregator-pro'); ?>" />
						</td>
					</tr>
					<?php if( false !== $license ) { ?>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e('Activate License', 'wp-event-aggregator-pro'); ?>
							</th>
							<td>
								<?php if( $status !== false && $status == 'valid' ) { ?>
									<span style="color:green;"><?php _e('active'); ?></span>
									<?php wp_nonce_field( 'wpea_pro_nonce', 'wpea_pro_nonce' ); ?>
									<input type="submit" class="button-secondary" name="wpea_pro_license_deactivate" value="<?php _e('Deactivate License', 'wp-event-aggregator-pro'); ?>"/>
								<?php } else {
									wp_nonce_field( 'wpea_pro_nonce', 'wpea_pro_nonce' ); ?>
									<input type="submit" class="button-secondary" name="wpea_pro_license_activate" value="<?php _e('Activate License', 'wp-event-aggregator-pro'); ?>"/>
								<?php } ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<?php submit_button(); ?>

		</form>
		</div>
	</div>
	</div>
	<?php
}

function wpea_pro_register_option() {
	// creates our settings in the options table
	register_setting('wpea_pro_license', 'wpea_pro_license_key', 'wpea_pro_sanitize_license' );
}
add_action('admin_init', 'wpea_pro_register_option');

function wpea_pro_sanitize_license( $new ) {
	$old = get_option( 'wpea_pro_license_key' );
	if( $old && $old != $new ) {
		delete_option( 'wpea_pro_license_status' ); // new license has been entered, so must reactivate
	}
	return $new;
}

// activate License
function wpea_pro_activate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['wpea_pro_license_activate'] ) ) {

		// run a quick security check
	 	if( ! check_admin_referer( 'wpea_pro_nonce', 'wpea_pro_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'wpea_pro_license_key' ) );


		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( WPEA_PRO_PLUGIN_NAME ), // the name of our product in EDD
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( WPEA_LICENSE_SITE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}

		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {

				switch( $license_data->error ) {

					case 'expired' :

						$message = sprintf(
							__( 'Your license key expired on %s.' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'revoked' :

						$message = __( 'Your license key has been disabled.' );
						break;

					case 'missing' :

						$message = __( 'Invalid license.' );
						break;

					case 'invalid' :
					case 'site_inactive' :

						$message = __( 'Your license is not active for this URL.' );
						break;

					case 'item_name_mismatch' :

						$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), WPEA_PRO_PLUGIN_NAME );
						break;

					case 'no_activations_left':

						$message = __( 'Your license key has reached its activation limit.' );
						break;

					default :

						$message = __( 'An error occurred, please try again.' );
						break;
				}

			}

		}

		// Check if anything passed on a message constituting a failure
		if ( ! empty( $message ) ) {
			$base_url = admin_url( 'admin.php?page=' . WPEA_PRO_PLUGIN_LICENSE_PAGE );
			$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

			wp_redirect( $redirect );
			exit();
		}

		// $license_data->license will be either "valid" or "invalid"

		update_option( 'wpea_pro_license_status', $license_data->license );
		wp_redirect( admin_url( 'admin.php?page=' . WPEA_PRO_PLUGIN_LICENSE_PAGE ) );
		exit();
	}
}
add_action('admin_init', 'wpea_pro_activate_license');

// Deactivate License
function wpea_pro_deactivate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['wpea_pro_license_deactivate'] ) ) {

		// run a quick security check
	 	if( ! check_admin_referer( 'wpea_pro_nonce', 'wpea_pro_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'wpea_pro_license_key' ) );


		// data to send in our API request
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_name'  => urlencode( WPEA_PRO_PLUGIN_NAME ), // the name of our product in EDD
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( WPEA_LICENSE_SITE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}

			$base_url = admin_url( 'admin.php?page=' . WPEA_PRO_PLUGIN_LICENSE_PAGE );
			$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

			wp_redirect( $redirect );
			exit();
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' ) {
			delete_option( 'wpea_pro_license_status' );
		}

		wp_redirect( admin_url( 'admin.php?page=' . WPEA_PRO_PLUGIN_LICENSE_PAGE ) );
		exit();

	}
}
add_action('admin_init', 'wpea_pro_deactivate_license');

// Check license is valid or not.
function wpea_pro_check_license() {

	global $wp_version;

	$license = trim( get_option( 'wpea_pro_license_key' ) );

	$api_params = array(
		'edd_action'=> 'check_license',
		'license' 	=> $license,
		'item_name' => urlencode( WPEA_PRO_PLUGIN_NAME ),
		'url'       => home_url()
	);

	// Call the custom API.
	$response = wp_remote_post( WPEA_LICENSE_SITE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

	if ( is_wp_error( $response ) )
		return false;

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	if( $license_data->license == 'valid' ) {
		echo 'valid'; exit;
		// this license is still valid
	} else {
		echo 'invalid'; exit;
		// this license is no longer valid
	}
}

function wpea_pro_admin_notices() {
	if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {

		switch( $_GET['sl_activation'] ) {

			case 'false':
				$message = urldecode( $_GET['message'] );
				?>
				<div class="error">
					<p><?php echo $message; ?></p>
				</div>
				<?php
				break;

			case 'true':
			default:
				// Developers can put a custom success message here for when activation is successful if they way.
				break;

		}
	}
}
add_action( 'admin_notices', 'wpea_pro_admin_notices' );