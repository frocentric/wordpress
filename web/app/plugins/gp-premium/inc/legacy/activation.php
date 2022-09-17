<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_enqueue_scripts', 'generatepress_premium_dashboard_scripts' );
/**
 * Enqueue scripts and styles for the GP Dashboard area.
 *
 * @since 1.6
 */
function generatepress_premium_dashboard_scripts() {
	$screen = get_current_screen();

	if ( 'appearance_page_generate-options' !== $screen->base ) {
		return;
	}

	wp_enqueue_style( 'generate-premium-dashboard', plugin_dir_url( __FILE__ ) . 'assets/dashboard.css', array(), GP_PREMIUM_VERSION );
	wp_enqueue_script( 'generate-premium-dashboard', plugin_dir_url( __FILE__ ) . 'assets/dashboard.js', array( 'jquery' ), GP_PREMIUM_VERSION, true );

	wp_localize_script(
		'generate-premium-dashboard',
		'dashboard',
		array(
			'deprecated_module' => esc_attr__( 'This module has been deprecated. Deactivating it will remove it from this list.', 'gp-premium' ),
		)
	);
}

if ( ! function_exists( 'generate_premium_notices' ) ) {
	add_action( 'admin_notices', 'generate_premium_notices' );
	/*
	* Set up errors and messages
	*/
	function generate_premium_notices() {
		if ( isset( $_GET['generate-message'] ) && 'addon_deactivated' == $_GET['generate-message'] ) {
			 add_settings_error( 'generate-premium-notices', 'addon_deactivated', __( 'Module deactivated.', 'gp-premium' ), 'updated' );
		}

		if ( isset( $_GET['generate-message'] ) && 'addon_activated' == $_GET['generate-message'] ) {
			 add_settings_error( 'generate-premium-notices', 'addon_activated', __( 'Module activated.', 'gp-premium' ), 'updated' );
		}

		settings_errors( 'generate-premium-notices' );
	}
}

if ( ! function_exists( 'generate_license_errors' ) ) {
	add_action( 'admin_notices', 'generate_license_errors' );
	/*
	* Set up errors and messages
	*/
	function generate_license_errors() {
		if ( isset( $_GET['generate-message'] ) && 'deactivation_passed' == $_GET['generate-message'] ) {
			add_settings_error( 'generate-license-notices', 'deactivation_passed', __( 'License deactivated.', 'gp-premium' ), 'updated' );
		}

		if ( isset( $_GET['generate-message'] ) && 'license_activated' == $_GET['generate-message'] ) {
			add_settings_error( 'generate-license-notices', 'license_activated', __( 'License activated.', 'gp-premium' ), 'updated' );
		}

		if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {

			switch ( $_GET['sl_activation'] ) {

				case 'false':
					$message = urldecode( $_GET['message'] );
					add_settings_error( 'generate-license-notices', 'license_failed', $message, 'error' );
				break;

				case 'true':
				default:
				break;

			}
		}

		settings_errors( 'generate-license-notices' );
	}
}

if ( ! function_exists( 'generate_super_package_addons' ) ) {
	add_action( 'generate_options_items', 'generate_super_package_addons', 5 );
	/**
	 * Build the area that allows us to activate and deactivate modules.
	 *
	 * @since 0.1
	 */
	function generate_super_package_addons() {
		$addons = array(
			'Backgrounds' => 'generate_package_backgrounds',
			'Blog' => 'generate_package_blog',
			'Colors' => 'generate_package_colors',
			'Copyright' => 'generate_package_copyright',
			'Disable Elements' => 'generate_package_disable_elements',
			'Elements' => 'generate_package_elements',
			'Hooks' => 'generate_package_hooks',
			'Menu Plus' => 'generate_package_menu_plus',
			'Page Header' => 'generate_package_page_header',
			'Secondary Nav' => 'generate_package_secondary_nav',
			'Sections' => 'generate_package_sections',
			'Spacing' => 'generate_package_spacing',
			'Typography' => 'generate_package_typography',
			'WooCommerce' => 'generate_package_woocommerce',
		);

		if ( version_compare( PHP_VERSION, '5.4', '>=' ) && ! defined( 'GENERATE_DISABLE_SITE_LIBRARY' ) ) {
			$addons['Site Library'] = 'generate_package_site_library';
		}

		if ( function_exists( 'generate_is_using_dynamic_typography' ) && generate_is_using_dynamic_typography() ) {
			unset( $addons['Typography'] );
		}

		if ( version_compare( generate_premium_get_theme_version(), '3.1.0-alpha.1', '>=' ) ) {
			unset( $addons['Colors'] );
		}

		ksort( $addons );

		$addon_count = 0;
		foreach ( $addons as $k => $v ) {
			if ( 'activated' == get_option( $v ) )
				$addon_count++;
		}

		$key = get_option( 'gen_premium_license_key_status', 'deactivated' );
		$version = ( defined( 'GP_PREMIUM_VERSION' ) ) ? GP_PREMIUM_VERSION  : '';

		?>
		<div class="postbox generate-metabox generatepress-admin-block" id="modules">
			<h3 class="hndle"><?php _e('GP Premium','gp-premium'); ?> <?php echo $version; ?></h3>
			<div class="inside" style="margin:0;padding:0;">
				<div class="premium-addons">
					<form method="post">
						<div class="add-on gp-clear addon-container grid-parent" style="background:#EFEFEF;border-left:5px solid #DDD;padding-left:10px !important;">
							<div class="addon-name column-addon-name">
								<input type="checkbox" id="generate-select-all" />
								<select name="generate_mass_activate" class="mass-activate-select">
									<option value=""><?php _e( 'Bulk Actions', 'gp-premium' ) ;?></option>
									<option value="activate-selected"><?php _e( 'Activate','gp-premium' ) ;?></option>
									<option value="deactivate-selected"><?php _e( 'Deactivate','gp-premium' ) ;?></option>
								</select>
								<?php wp_nonce_field( 'gp_premium_bulk_action_nonce', 'gp_premium_bulk_action_nonce' ); ?>
								<input type="submit" name="generate_multi_activate" class="button mass-activate-button" value="<?php _e( 'Apply','gp-premium' ); ?>" />
							</div>
						</div>
						<?php

						$deprecated_modules = apply_filters(
							'generate_premium_deprecated_modules',
							array(
								'Page Header',
								'Hooks',
								'Sections',
							)
						);

						foreach ( $addons as $k => $v ) :

							$key = get_option( $v );

							if( $key == 'activated' ) { ?>
								<div class="add-on activated gp-clear addon-container grid-parent">
									<div class="addon-name column-addon-name" style="">
										<input type="checkbox" class="addon-checkbox" name="generate_addon_checkbox[]" value="<?php echo $v; ?>" />
										<?php echo $k;?>
									</div>
									<div class="addon-action addon-addon-action" style="text-align:right;">
										<?php wp_nonce_field( $v . '_deactivate_nonce', $v . '_deactivate_nonce' ); ?>
										<input type="submit" name="<?php echo $v;?>_deactivate_package" value="<?php _e( 'Deactivate', 'gp-premium' );?>"/>
									</div>
								</div>
							<?php } else {
								// Don't output deprecated modules.
								if ( in_array( $k, $deprecated_modules ) ) {
									continue;
								}
								?>
								<div class="add-on gp-clear addon-container grid-parent">

									<div class="addon-name column-addon-name">
										<input <?php if ( 'WooCommerce' == $k && ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) { echo 'disabled'; } ?> type="checkbox" class="addon-checkbox" name="generate_addon_checkbox[]" value="<?php echo $v; ?>" />
										<?php echo $k;?>
									</div>

									<div class="addon-action addon-addon-action" style="text-align:right;">
										<?php if ( 'WooCommerce' == $k && ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>
											<?php _e( 'WooCommerce not activated.','gp-premium' ); ?>
										<?php } else { ?>
											<?php wp_nonce_field( $v . '_activate_nonce', $v . '_activate_nonce' ); ?>
											<input type="submit" name="<?php echo $v;?>_activate_package" value="<?php _e( 'Activate', 'gp-premium' );?>"/>
										<?php } ?>
									</div>

								</div>
							<?php }
							echo '<div class="gp-clear"></div>';
						endforeach;
						?>
					</form>
				</div>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'generate_multi_activate' ) ) {
	add_action( 'admin_init', 'generate_multi_activate' );

	function generate_multi_activate() {
		// Deactivate selected
		if ( isset( $_POST['generate_multi_activate'] ) ) {

			// If we didn't click the button, bail.
			if ( ! check_admin_referer( 'gp_premium_bulk_action_nonce', 'gp_premium_bulk_action_nonce' ) ) {
				return;
			}

			// If we're not an administrator, bail.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$name = ( isset( $_POST['generate_addon_checkbox'] ) ) ? $_POST['generate_addon_checkbox'] : '';
			$option = ( isset( $_POST['generate_addon_checkbox'] ) ) ? $_POST['generate_mass_activate'] : '';
			$autoload = null;

			if ( isset( $_POST['generate_addon_checkbox'] ) ) {

				if ( 'deactivate-selected' == $option ) {
					foreach ( $name as $id ) {
						if ( 'activated' == get_option( $id ) ) {
							if ( 'generate_package_site_library' === $id ) {
								$autoload = false;
							}

							update_option( $id, '', $autoload );
						}
					}
				}

				if ( 'activate-selected' == $option ) {
					foreach ( $name as $id ) {
						if ( 'activated' !== get_option( $id ) ) {
							if ( 'generate_package_site_library' === $id ) {
								$autoload = false;
							}

							update_option( $id, 'activated', $autoload );
						}
					}
				}

				wp_safe_redirect( admin_url( 'themes.php?page=generate-options' ) );
				exit;
			} else {
				wp_safe_redirect( admin_url( 'themes.php?page=generate-options' ) );
				exit;
			}
		}
	}
}

/***********************************************
* Activate the add-on
***********************************************/
if ( ! function_exists( 'generate_activate_super_package_addons' ) ) {
	add_action( 'admin_init', 'generate_activate_super_package_addons' );

	function generate_activate_super_package_addons() {
		$addons = array(
			'Typography' => 'generate_package_typography',
			'Colors' => 'generate_package_colors',
			'Backgrounds' => 'generate_package_backgrounds',
			'Page Header' => 'generate_package_page_header',
			'Sections' => 'generate_package_sections',
			'Copyright' => 'generate_package_copyright',
			'Disable Elements' => 'generate_package_disable_elements',
			'Elements' => 'generate_package_elements',
			'Blog' => 'generate_package_blog',
			'Hooks' => 'generate_package_hooks',
			'Spacing' => 'generate_package_spacing',
			'Secondary Nav' => 'generate_package_secondary_nav',
			'Menu Plus' => 'generate_package_menu_plus',
			'WooCommerce' => 'generate_package_woocommerce',
		);

		if ( version_compare( PHP_VERSION, '5.4', '>=' ) && ! defined( 'GENERATE_DISABLE_SITE_LIBRARY' ) ) {
			$addons['Site Library'] = 'generate_package_site_library';
		}

		foreach( $addons as $k => $v ) :

			if ( isset( $_POST[$v . '_activate_package'] ) ) {

				// If we didn't click the button, bail.
				if ( ! check_admin_referer( $v . '_activate_nonce', $v . '_activate_nonce' ) ) {
					return;
				}

				// If we're not an administrator, bail.
				if ( ! current_user_can( 'manage_options' ) ) {
					return;
				}

				$autoload = null;

				if ( 'generate_package_site_library' === $v ) {
					$autoload = false;
				}

				update_option( $v, 'activated', $autoload );
				wp_safe_redirect( admin_url( 'themes.php?page=generate-options&generate-message=addon_activated' ) );
				exit;
			}

		endforeach;
	}
}

/***********************************************
* Deactivate the plugin
***********************************************/
if ( ! function_exists( 'generate_deactivate_super_package_addons' ) ) {
	add_action( 'admin_init', 'generate_deactivate_super_package_addons' );

	function generate_deactivate_super_package_addons() {
		$addons = array(
			'Typography' => 'generate_package_typography',
			'Colors' => 'generate_package_colors',
			'Backgrounds' => 'generate_package_backgrounds',
			'Page Header' => 'generate_package_page_header',
			'Sections' => 'generate_package_sections',
			'Copyright' => 'generate_package_copyright',
			'Disable Elements' => 'generate_package_disable_elements',
			'Elements' => 'generate_package_elements',
			'Blog' => 'generate_package_blog',
			'Hooks' => 'generate_package_hooks',
			'Spacing' => 'generate_package_spacing',
			'Secondary Nav' => 'generate_package_secondary_nav',
			'Menu Plus' => 'generate_package_menu_plus',
			'WooCommerce' => 'generate_package_woocommerce',
		);

		if ( version_compare( PHP_VERSION, '5.4', '>=' ) && ! defined( 'GENERATE_DISABLE_SITE_LIBRARY' ) ) {
			$addons['Site Library'] = 'generate_package_site_library';
		}

		foreach( $addons as $k => $v ) :

			if ( isset( $_POST[$v . '_deactivate_package'] ) ) {

				// If we didn't click the button, bail.
				if ( ! check_admin_referer( $v . '_deactivate_nonce', $v . '_deactivate_nonce' ) ) {
					return;
				}

				// If we're not an administrator, bail.
				if ( ! current_user_can( 'manage_options' ) ) {
					return;
				}

				$autoload = null;

				if ( 'generate_package_site_library' === $v ) {
					$autoload = false;
				}

				update_option( $v, 'deactivated', $autoload );
				wp_safe_redirect( admin_url('themes.php?page=generate-options&generate-message=addon_deactivated' ) );
				exit;
			}

		endforeach;
	}
}

if ( ! function_exists( 'generate_premium_body_class' ) ) {
	add_filter( 'admin_body_class', 'generate_premium_body_class' );
	/**
	 * Add a class or many to the body in the dashboard
	 */
	function generate_premium_body_class( $classes ) {
	    return "$classes gp_premium";
	}
}

if ( ! function_exists( 'generate_activation_area' ) ) {
	add_action( 'generate_admin_right_panel', 'generate_activation_area' );

	function generate_activation_area() {
		$license = get_option( 'gen_premium_license_key', '' );
		$key = get_option( 'gen_premium_license_key_status', 'deactivated' );

		if ( 'valid' == $key ) {
			$message = sprintf( '<span class="license-key-message receiving-updates">%s</span>', __( 'Receiving updates', 'gp-premium' ) );
		} else {
			$message = sprintf( '<span class="license-key-message not-receiving-updates">%s</span>', __( 'Not receiving updates', 'gp-premium' ) );
		}
		?>
		<form method="post" action="options.php">
			<div class="postbox generate-metabox" id="generate-license-keys">
				<h3 class="hndle">
					<?php _e( 'Updates', 'gp-premium' );?>
					<span class="license-key-info">
						<?php echo $message; ?>
						<a title="<?php esc_attr_e( 'Help', 'gp-premium' ); ?>" href="https://docs.generatepress.com/article/updating-gp-premium/" target="_blank" rel="noopener">[?]</a>
					</span>
				</h3>

				<div class="inside" style="margin-bottom:0;">
					<div class="license-key-container" style="position:relative;">
						<p>
							<input spellcheck="false" class="license-key-input" id="generate_license_key_gp_premium" name="generate_license_key_gp_premium" type="<?php echo apply_filters( 'generate_premium_license_key_field', 'password' ); ?>" value="<?php echo $license; ?>" placeholder="<?php _e( 'License Key', 'gp-premium' ); ?>" />
						</p>

						<p class="beta-testing-container" <?php echo ( empty( $license ) ) ? 'style="display: none;"' : '';?>>
							<input type="checkbox" id="gp_premium_beta_testing" name="gp_premium_beta_testing" value="true" <?php echo ( get_option( 'gp_premium_beta_testing', false ) ) ? 'checked="checked"' : ''; ?> />
							<label for="gp_premium_beta_testing"><?php _e( 'Receive beta updates', 'gp-premium' ); ?> <a title="<?php esc_attr_e( 'Help', 'gp-premium' ); ?>" href="https://docs.generatepress.com/article/beta-testing/" target="_blank" rel="noopener">[?]</a></label>
						</p>

						<?php wp_nonce_field( 'generate_license_key_gp_premium_nonce', 'generate_license_key_gp_premium_nonce' ); ?>
						<input type="submit" class="button button-primary" name="gp_premium_license_key" value="<?php _e( 'Save', 'gp-premium' );?>" />
					</div>
				</div>
			</div>
		</form>
		<?php
	}
}

add_action( 'admin_init', 'generatepress_premium_process_license_key', 5 );
/**
 * Process our saved license key.
 *
 * @since 1.6
 */
function generatepress_premium_process_license_key() {
	// Has our button been clicked?
	if ( isset( $_POST[ 'gp_premium_license_key' ] ) ) {

		// Get out if we didn't click the button
		if ( ! check_admin_referer( 'generate_license_key_gp_premium_nonce', 'generate_license_key_gp_premium_nonce' ) ) {
			return;
		}

		// If we're not an administrator, bail.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Set our beta testing option if it's checked.
		if ( ! empty( $_POST['gp_premium_beta_testing'] ) ) {
			update_option( 'gp_premium_beta_testing', true, false );
		} else {
			delete_option( 'gp_premium_beta_testing' );
		}

		// Grab the value being saved
		$new = sanitize_key( $_POST['generate_license_key_gp_premium'] );

		// Get the previously saved value
		$old = get_option( 'gen_premium_license_key' );

		// Still here? Update our option with the new license key
		update_option( 'gen_premium_license_key', $new );

		// If we have a value, run activation.
		if ( '' !== $new ) {
			$api_params = array(
				'edd_action' => 'activate_license',
				'license' => $new,
				'item_name' => urlencode( 'GP Premium' ),
				'url' => home_url()
			);
		}

		// If we don't have a value (it's been cleared), run deactivation.
		if ( '' == $new && 'valid' == get_option( 'gen_premium_license_key_status' ) ) {
			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license' => $old,
				'item_name' => urlencode( 'GP Premium' ),
				'url' => home_url()
			);
		}

		// Nothing? Get out of here.
		if ( ! isset( $api_params ) ) {
			wp_safe_redirect( admin_url( 'themes.php?page=generate-options' ) );
			exit;
		}

		// Phone home.
		$license_response = wp_remote_post( 'https://generatepress.com', array(
			'timeout'   => 60,
			'sslverify' => false,
			'body'      => $api_params
		) );

		// Make sure the response came back okay.
		if ( is_wp_error( $license_response ) || 200 !== wp_remote_retrieve_response_code( $license_response ) ) {
			if ( is_object( $license_response ) ) {
				$message = $license_response->get_error_message();
			} elseif ( is_array( $license_response ) && isset( $license_response['response']['message'] ) ) {
				if ( 'Forbidden' === $license_response['response']['message'] ) {
					$message = __( '403 Forbidden. Your server is not able to communicate with generatepress.com in order to activate your license key.', 'gp-premium' );
				} else {
					$message = $license_response['response']['message'];
				}
			}
		} else {

			// Still here? Decode our response.
			$license_data = json_decode( wp_remote_retrieve_body( $license_response ) );

			if ( false === $license_data->success ) {

				switch ( $license_data->error ) {

				case 'expired' :

					$message = sprintf(
						__( 'Your license key expired on %s.', 'gp-premium' ),
						date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
					);
					break;

				case 'revoked' :

					$message = __( 'Your license key has been disabled.', 'gp-premium' );
					break;

				case 'missing' :

					$message = __( 'Invalid license.', 'gp-premium' );
					break;

				case 'invalid' :
				case 'site_inactive' :

					$message = __( 'Your license is not active for this URL.', 'gp-premium' );
					break;

				case 'item_name_mismatch' :

					$message = __( 'This appears to be an invalid license key for GP Premium.', 'gp-premium' );
					break;

				case 'no_activations_left':

					$message = __( 'Your license key has reached its activation limit.', 'gp-premium' );
					break;

				default :

					$message = __( 'An error occurred, please try again.', 'gp-premium' );
					break;
				}

			}

		}

		// Check if anything passed on a message constituting a failure
		if ( ! empty( $message ) ) {
			delete_option( 'gen_premium_license_key_status' );
			$base_url = admin_url( 'themes.php?page=generate-options' );
			$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), esc_url( $base_url ) );
			wp_redirect( $redirect );
			exit();
		}

		// Update our license key status
		update_option( 'gen_premium_license_key_status', $license_data->license );

		if ( 'valid' == $license_data->license ) {
			// Validated, go tell them
			wp_safe_redirect( admin_url( 'themes.php?page=generate-options&generate-message=license_activated' ) );
			exit;
		} elseif ( 'deactivated' == $license_data->license ) {
			// Deactivated, go tell them
			wp_safe_redirect( admin_url( 'themes.php?page=generate-options&generate-message=deactivation_passed' ) );
			exit;
		} else {
			// Failed, go tell them
			wp_safe_redirect( admin_url( 'themes.php?page=generate-options&generate-message=license_failed' ) );
			exit;
		}
	}
}

if ( ! function_exists( 'generate_license_missing' ) ) {
	add_action( 'in_plugin_update_message-gp-premium/gp-premium.php', 'generate_license_missing', 10, 2 );
	/**
	 * Add a message to the plugin update area if no license key is set
	 */
	function generate_license_missing() {
		$license = get_option( 'gen_premium_license_key_status' );

		if ( 'valid' !== $license ) {
			echo '&nbsp;<strong><a href="' . esc_url( admin_url('themes.php?page=generate-options' ) ) . '">' . __( 'Enter valid license key for automatic updates.', 'gp-premium' ) . '</a></strong>';
		}
	}
}

add_filter( 'generate_premium_beta_tester', 'generatepress_premium_beta_tester' );
/**
 * Enable beta testing if our option is set.
 *
 * @since 1.6
 */
function generatepress_premium_beta_tester( $value ) {
	if ( get_option( 'gp_premium_beta_testing', false ) ) {
		return true;
	}

	return $value;
}
