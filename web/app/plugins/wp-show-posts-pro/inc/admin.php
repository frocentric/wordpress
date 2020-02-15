<?php
/*
 WARNING: This is a core WP Show Posts file. DO NOT edit
 this file under any circumstances. Please do all modifications
 in the form of a child theme.
 */

/**
 * Creates the options page.
 *
 * This file is a core WP Show Posts file and should not be edited.
 *
 * @package  WP Show Posts
 * @author   Thomas Usborne
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     http://wpshowposts.com
 */

add_action( 'butterbean_register', 'wpsp_control_order', 100, 2 );
/*
 * Let's order our controls
 * I find this easier to see all in one place and adjust things
 */
function wpsp_control_order( $butterbean, $post_type ) {
	$manager = $butterbean->get_manager( 'wp_show_posts' );
	// Posts section
	$manager->get_control( 'wpsp_post_type' )->priority = 10;
	$manager->get_control( 'wpsp_taxonomy' )->priority = 20;
	$manager->get_control( 'wpsp_tax_term' )->priority = 30;
	$manager->get_control( 'wpsp_posts_per_page' )->priority = 40;
	$manager->get_control( 'wpsp_pagination' )->priority = 50;
	$manager->get_control( 'wpsp_ajax_pagination' )->priority = 60;

	// Columns section
	$manager->get_control( 'wpsp_columns' )->priority = 10;
	$manager->get_control( 'wpsp_columns_gutter' )->priority = 20;
	$manager->get_control( 'wpsp_padding' )->priority = 30;
	$manager->get_control( 'wpsp_masonry' )->priority = 40;
	$manager->get_control( 'wpsp_featured_post' )->priority = 50;
	$manager->get_control( 'wpsp_background' )->priority = 60;
	$manager->get_control( 'wpsp_background_hover' )->priority = 70;
	$manager->get_control( 'wpsp_border' )->priority = 80;
	$manager->get_control( 'wpsp_border_hover' )->priority = 90;

	// Images section
	$manager->get_control( 'wpsp_image' )->priority = 10;
	$manager->get_control( 'wpsp_image_width' )->priority = 20;
	$manager->get_control( 'wpsp_image_height' )->priority = 30;
	$manager->get_control( 'wpsp_image_alignment' )->priority = 40;
	$manager->get_control( 'wpsp_image_location' )->priority = 50;
	$manager->get_control( 'wpsp_image_overlay_color_static' )->priority = 59;
	$manager->get_control( 'wpsp_image_overlay_color' )->priority = 60;
	$manager->get_control( 'wpsp_image_overlay_icon' )->priority = 70;
	$manager->get_control( 'wpsp_image_hover_effect' )->priority = 80;
	$manager->get_control( 'wpsp_image_lightbox' )->priority = 90;
	$manager->get_control( 'wpsp_image_gallery' )->priority = 100;

	// Content section
	$manager->get_control( 'wpsp_content_type' )->priority = 10;
	$manager->get_control( 'wpsp_excerpt_length' )->priority = 20;
	$manager->get_control( 'wpsp_text' )->priority = 30;
	$manager->get_control( 'wpsp_include_title' )->priority = 40;
	$manager->get_control( 'wpsp_title_element' )->priority = 42;
	$manager->get_control( 'wpsp_title_font_size' )->priority = 45;
	$manager->get_control( 'wpsp_title_color' )->priority = 50;
	$manager->get_control( 'wpsp_title_color_hover' )->priority = 60;
	$manager->get_control( 'wpsp_read_more_text' )->priority = 70;
	$manager->get_control( 'wpsp_read_more_background_color' )->priority = 80;
	$manager->get_control( 'wpsp_read_more_background_color_hover' )->priority = 90;
	$manager->get_control( 'wpsp_read_more_text_color' )->priority = 100;
	$manager->get_control( 'wpsp_read_more_text_color_hover' )->priority = 110;
	$manager->get_control( 'wpsp_read_more_border_color' )->priority = 120;
	$manager->get_control( 'wpsp_read_more_border_color_hover' )->priority = 130;

	// Meta section
	$manager->get_control( 'wpsp_include_author' )->priority = 10;
	$manager->get_control( 'wpsp_author_location' )->priority = 20;
	$manager->get_control( 'wpsp_include_date' )->priority = 30;
	$manager->get_control( 'wpsp_date_location' )->priority = 40;
	$manager->get_control( 'wpsp_include_terms' )->priority = 50;
	$manager->get_control( 'wpsp_terms_location' )->priority = 60;
	$manager->get_control( 'wpsp_include_comments' )->priority = 65;
	$manager->get_control( 'wpsp_comments_location' )->priority = 66;
	$manager->get_control( 'wpsp_meta_color' )->priority = 70;
	$manager->get_control( 'wpsp_meta_color_hover' )->priority = 80;

	// Social section
	$manager->get_control( 'wpsp_social_sharing' )->priority = 10;
	$manager->get_control( 'wpsp_twitter' )->priority = 20;
	$manager->get_control( 'wpsp_twitter_color' )->priority = 25;
	$manager->get_control( 'wpsp_twitter_color_hover' )->priority = 26;
	$manager->get_control( 'wpsp_facebook' )->priority = 30;
	$manager->get_control( 'wpsp_facebook_color' )->priority = 35;
	$manager->get_control( 'wpsp_facebook_color_hover' )->priority = 36;
	$manager->get_control( 'wpsp_pinterest' )->priority = 50;
	$manager->get_control( 'wpsp_pinterest_color' )->priority = 55;
	$manager->get_control( 'wpsp_pinterest_color_hover' )->priority = 56;
	$manager->get_control( 'wpsp_love' )->priority = 60;
	$manager->get_control( 'wpsp_love_color' )->priority = 65;
	$manager->get_control( 'wpsp_love_color_hover' )->priority = 66;
	$manager->get_control( 'wpsp_social_sharing_alignment' )->priority = 70;
}

add_action( 'admin_menu', 'wpsp_create_menu' );
/**
 * Add our license menu item.
 */
function wpsp_create_menu() {
	add_submenu_page( 'edit.php?post_type=wp_show_posts', __( 'WP Show Posts Pro License','wp-show-posts-pro' ), __( 'License','wp-show-posts-pro' ), 'activate_plugins', 'wpsp_settings_page', 'wpsp_settings_page' );
}

function wpsp_settings_page() {
	?>
	<div class="wrap">
		<div class="metabox-holder">
			<div class="postbox-container" style="float: none;max-width:1120px;">
				<div class="grid-container grid-parent">

					<div class="form-metabox">
						<div class="postbox wpsp-metabox" id="gen-1" style="width:50%;">
							<h3 class="hndle">WP Show Posts <?php echo WPSP_VERSION; ?></h3>
							<div class="inside">
								<p>
									<span><?php printf( _x( 'Made with %s by Tom Usborne', 'made with love', 'wp-show-posts-pro' ), '<span style="color:#D04848" class="dashicons dashicons-heart"></span>' ); ?></span>
								</p>
								<p>
									<a class="button button-primary" href="<?php echo esc_url( 'https://wpshowposts.com/support/area/pro-support/' ); ?>" target="_blank"><?php _e('Support','wp-show-posts-pro');?></a>
									<a class="button button-primary" href="<?php echo esc_url( 'https://docs.wpshowposts.com' ); ?>" target="_blank"><?php _e('Documentation','wp-show-posts-pro');?></a>
								</p>
							</div>
						</div>
						<form method="post" action="options.php">
							<?php settings_fields( 'wpsp-license-group' ); ?>
							<?php do_settings_sections( 'wpsp-license-group' ); ?>
							<?php
							$license = get_option( 'wp_show_posts_license' );
							$key = get_option( 'wp_show_posts_license_status', 'deactivated' );
							?>
							<div class="postbox wpsp-metabox" id="wpsp-license-keys" style="width:50%;">
								<h3 class="hndle">WP Show Posts Pro <?php echo WPSP_PRO_VERSION; ?> <a class="update-help" title="<?php _e( 'Help' ); ?>" href="https://wpshowposts.com/support/area/pro-support/" target="_blank"><span class="dashicons dashicons-sos"></span></a></h3>
								<div class="inside" style="margin-bottom:0;">
									<div class="license-key-container" style="position:relative;">
										<div style="float:left;width:70%;">
											<span style="position:relative;">
												<?php if ( 'valid' == $key ) : ?>
													<span class="dashicons dashicons-yes status" style="color:green;"></span>
												<?php else : ?>
													<span class="dashicons dashicons-no status" style="color:red;"></span>
												<?php endif; ?>
												<input spellcheck="false" class="license-key-input" id="wp_show_posts_license_key" name="wp_show_posts_license_key" type="text" value="<?php echo $license; ?>" placeholder="<?php _e( 'License Key', 'wp-show-posts-pro' ); ?>" />
											</span>
										</div>
										<div class="grid-30 grid-parent" style="float:left;width:30%;">
											<?php wp_nonce_field( 'wp_show_posts_license_key_nonce', 'wp_show_posts_license_key_nonce' ); ?>
											<input type="submit" id="submit" class="button button-primary license-key-button" name="wp_show_posts_update_license_key" value="<?php _e( 'Update' );?>" />
										</div>
										<div class="clear" style="padding:0;margin:0;border:0;"></div>
									</div>
								</div>
							</div>

						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}

if ( ! function_exists( 'wpsp_process_license_key' ) ) {
	/***********************************************
	* Activate and deactivate license keys
	***********************************************/
	add_action( 'admin_init', 'wpsp_process_license_key' );
	function wpsp_process_license_key() {
		// Has our button been clicked?
		if( isset( $_POST[ 'wp_show_posts_update_license_key' ] ) ) {

			// Get out if we didn't click the button
			if ( ! check_admin_referer( 'wp_show_posts_license_key_nonce', 'wp_show_posts_license_key_nonce' ) ) {
				return;
			}

			// If we're not an administrator, bail.
			if ( ! current_user_can( 'administrator' ) ) {
				return;
			}

			// Grab the value being saved
			$new = $_POST[ 'wp_show_posts_license_key' ];

			// Get the previously saved value
			$old = get_option( 'wp_show_posts_license' );

			// If nothing has changed, bail
			if ( $new == $old ) {
				wp_safe_redirect( admin_url( 'edit.php?post_type=wp_show_posts&page=wpsp_settings_page' ) );
				exit;
			}

			// Still here? Update our option with the new license key
			update_option( 'wp_show_posts_license', sanitize_key( $new ) );

			// If we have a value, run activation.
			if ( '' !== $new ) {
				$api_params = array(
					'edd_action' => 'activate_license',
					'license' => sanitize_key( $new ),
					'item_name' => urlencode( 'WP Show Posts Pro' ),
					'url' => home_url()
				);
			}

			// If we don't have a value (it's been cleared), run deactivation.
			if ( '' == $new && 'valid' == get_option( 'wp_show_posts_license_status' ) ) {
				$api_params = array(
					'edd_action' => 'deactivate_license',
					'license' => sanitize_key( $old ),
					'item_name' => urlencode( 'WP Show Posts Pro' ),
					'url' => home_url()
				);
			}

			// Nothing? Get out of here.
			if ( ! $api_params ) {
				wp_safe_redirect( admin_url( 'edit.php?post_type=wp_show_posts&page=wpsp_settings_page' ) );
				exit;
			}

			// Phone home.
			$license_response = wp_remote_post( 'https://wpshowposts.com', array(
				'timeout'   => 60,
				'sslverify' => false,
				'body'      => $api_params
			) );

			// make sure the response came back okay
			if ( is_wp_error( $license_response ) || 200 !== wp_remote_retrieve_response_code( $license_response ) ) {
				$message = $license_response->get_error_message();
			} else {

				// Still here? Decode our response.
				$license_data = json_decode( wp_remote_retrieve_body( $license_response ) );

				if ( false === $license_data->success ) {

					switch( $license_data->error ) {

						case 'expired' :

							$message = sprintf(
								__( 'Your license key expired on %s.','wp-show-posts-pro' ),
								date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
							);
							break;

						case 'revoked' :

							$message = __( 'Your license key has been disabled.','wp-show-posts-pro' );
							break;

						case 'missing' :

							$message = __( 'Invalid license.','wp-show-posts-pro' );
							break;

						case 'invalid' :
						case 'site_inactive' :

							$message = __( 'Your license is not active for this URL.','wp-show-posts-pro' );
							break;

						case 'item_name_mismatch' :

							$message = __( 'This appears to be an invalid license key for WP Show Posts Pro.','wp-show-posts-pro' );
							break;

						case 'no_activations_left':

							$message = __( 'Your license key has reached its activation limit.','wp-show-posts-pro' );
							break;

						default :

							$message = __( 'An error occurred, please try again.','wp-show-posts-pro' );
							break;
					}

				}

			}

			// Check if anything passed on a message constituting a failure
			if ( ! empty( $message ) ) {
				delete_option( $license_key_status );
				$base_url = admin_url( 'edit.php?post_type=wp_show_posts' );
				$redirect = add_query_arg( array( 'page' => 'wpsp_settings_page', 'sl_activation' => 'false', 'message' => urlencode( $message ) ), esc_url( $base_url ) );
				wp_redirect( $redirect );
				exit();
			}

			// Update our license key status
			update_option( 'wp_show_posts_license_status', $license_data->license );

			if ( 'valid' == $license_data->license ) {
				// Validated, go tell them
				wp_safe_redirect( admin_url('edit.php?post_type=wp_show_posts&page=wpsp_settings_page&wpsp-message=license_activated' ) );
				exit;
			} elseif ( 'deactivated' == $license_data->license ) {
				// Deactivated, go tell them
				wp_safe_redirect( admin_url('edit.php?post_type=wp_show_posts&page=wpsp_settings_page&wpsp-message=deactivation_passed' ) );
				exit;
			} else {
				// Failed, go tell them
				wp_safe_redirect( admin_url('edit.php?post_type=wp_show_posts&page=wpsp_settings_page&wpsp-message=license_failed' ) );
				exit;
			}
		}
	}
}

if ( ! function_exists( 'wpsp_license_errors' ) ) {
	add_action( 'admin_notices', 'wpsp_license_errors' );
	/*
	* Set up errors and messages
	*/
	function wpsp_license_errors() {

		if ( isset( $_GET['wpsp-message'] ) && 'deactivation_passed' == $_GET['wpsp-message'] ) {
			 add_settings_error( 'wpsp-license-notices', 'deactivation_passed', __( 'License deactivated.', 'wp-show-posts-pro' ), 'updated' );
		}

		if ( isset( $_GET['wpsp-message'] ) && 'license_activated' == $_GET['wpsp-message'] ) {
			add_settings_error( 'wpsp-license-notices', 'license_activated', __( 'License activated.', 'wp-show-posts-pro' ), 'updated' );
		}

		if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {

			switch( $_GET['sl_activation'] ) {

				case 'false':
					$message = urldecode( $_GET['message'] );
					add_settings_error( 'wpsp-license-notices', 'license_failed', $message, 'error' );
					break;

				case 'true':
				default:
					break;

			}
		}

		settings_errors( 'wpsp-license-notices' );
	}
}

if ( ! function_exists( 'wpsp_verify_styles' ) ) {
	add_action( 'admin_head', 'wpsp_verify_styles' );
	function wpsp_verify_styles() {
		echo '<style>.license-key-container {
					margin-bottom:15px;
				}
				.license-key-container:last-child {
					margin:0;
				}
				.update-help {
					float: right;
					text-decoration: none;
				}
				.license-key-container label {
					font-size: 11px;
					font-weight: normal;
					color: #777;
					display: inline-block;
					margin-bottom: 0;
				}
				.license-key-container .status {
					position: absolute;
					right:10px;
					top:-1px;
					background:rgba(255,255,255,0.9);
				}
				.license-key-input {
					width:100%;
					box-sizing:border-box;
					padding:10px;
				}
				.license-key-button {
					position:relative;
					top:1px;
					width:100%;
					box-sizing:border-box;
					padding: 10px !important;
					height:auto !important;
					line-height:normal !important;
				}
				.wp_show_posts_page_wpsp_settings_page .updated,
				.wp_show_posts_page_wpsp_settings_page .error {
					max-width: 1120px;
					margin-left: 2px;
					margin-top: 25px;
					-moz-box-sizing: border-box;
					-webkit-box-sizing: border-box;
					box-sizing: border-box;
				}</style>';
	}
}

if ( ! function_exists( 'wpsp_pro_admin_scripts' ) ) {
	add_action( 'admin_print_scripts-post-new.php', 'wpsp_pro_admin_scripts', 12 );
	add_action( 'admin_print_scripts-post.php', 'wpsp_pro_admin_scripts', 12 );
	/**
	 * Add our admin scripts and styles
	 * @since 0.1
	 */
	function wpsp_pro_admin_scripts() {
		global $post_type, $post;
	    if ( 'wp_show_posts' == $post_type ) {
			wp_enqueue_script( 'wpsp-pro-admin-scripts', plugin_dir_url( __FILE__ ) . "js/admin-scripts.js", array( 'jquery' ), '', true );
			wp_enqueue_script( 'wpsp-pro-wp-color-alpha', plugin_dir_url( __FILE__ ) . "js/wp-color-picker-alpha.js", array( 'wp-color-picker' ), '', true );

			if ( function_exists( 'wpsp_get_defaults' ) ) {
				$defaults = wpsp_get_defaults();

				wp_localize_script( 'wpsp-pro-admin-scripts', 'wpsp', array(
					'defaults' => $defaults,
					'set_card_style' => esc_html__( 'Set Card Styles', 'wp-show-posts-pro' ),
					'confirm_card_style' => esc_html__( 'Doing this will reset some of your existing options to fit this card. It can not be undone.', 'wp-show-posts-pro' ),
					'card_style_description' => esc_html__( 'Setting card styles will design your card by changing various options in this list.', 'wp-show-posts-pro' ),
				) );
			}
		}
	}
}

add_action( 'admin_init', 'wpsp_pro_update_read_more_values' );
/**
 * Who thought it was smart to define a fixed list of colors? This guy.
 * This function converts all of our fixed colors into options so you can choose your own colors.
 * We can remove this function after a while - created December 1, 2016
 * @since 0.5
 */
function wpsp_pro_update_read_more_values() {
	$args = array(
		'posts_per_page'   => -1,
		'post_type'        => 'wp_show_posts',
		'showposts'		   => -1
	);
	$posts = get_posts( $args );

	$count = count( $posts );
	$types = array();
	if ( $count > 0 ) {
		foreach ( $posts as $post ) {

			// If we don't have a color, chances are this function has already ran and we're done
			if ( ! get_post_meta( $post->ID, 'wpsp_read_more_color', true ) )
				return;

			if ( 'black' == get_post_meta( $post->ID, 'wpsp_read_more_color', true ) ) {
				$background = '#222222';
				$color = '#FFFFFF';
				$highlight = '#000000';
			}

			if ( 'sun-flower' == get_post_meta( $post->ID, 'wpsp_read_more_color', true ) ) {
				$background = '#F1C40F';
				$color = '#FFFFFF';
				$highlight = 'E2B607';
			}

			if ( 'orange' == get_post_meta( $post->ID, 'wpsp_read_more_color', true ) ) {
				$background = '#F39C12';
				$color = '#FFFFFF';
				$highlight = '#E8930C';
			}

			if ( 'carrot' == get_post_meta( $post->ID, 'wpsp_read_more_color', true ) ) {
				$background = '#E67E22';
				$color = '#FFFFFF';
				$highlight = '#DA751C';
			}

			if ( 'pumpkin' == get_post_meta( $post->ID, 'wpsp_read_more_color', true ) ) {
				$background = '#D35400';
				$color = '#FFFFFF';
				$highlight = '#C54E00';
			}

			if ( 'alizarin' == get_post_meta( $post->ID, 'wpsp_read_more_color', true ) ) {
				$background = '#E74C3C';
				$color = '#FFFFFF';
				$highlight = '#DB4334';
			}

			if ( 'pomegranate' == get_post_meta( $post->ID, 'wpsp_read_more_color', true ) ) {
				$background = '#C0392B';
				$color = '#FFFFFF';
				$highlight = '#B53224';
			}

			if ( 'turquoise' == get_post_meta( $post->ID, 'wpsp_read_more_color', true ) ) {
				$background = '#1ABC9C';
				$color = '#FFFFFF';
				$highlight = '#12AB8D';
			}

			if ( 'green-sea' == get_post_meta( $post->ID, 'wpsp_read_more_color', true ) ) {
				$background = '#16A085';
				$color = '#FFFFFF';
				$highlight = '#14947B';
			}

			if ( 'emerald' == get_post_meta( $post->ID, 'wpsp_read_more_color', true ) ) {
				$background = '#2ECC71';
				$color = '#FFFFFF';
				$highlight = '#28BE68';
			}

			if ( 'nephritis' == get_post_meta( $post->ID, 'wpsp_read_more_color', true ) ) {
				$background = '#27AE60';
				$color = '#FFFFFF';
				$highlight = '#219D55';
			}

			if ( 'river' == get_post_meta( $post->ID, 'wpsp_read_more_color', true ) ) {
				$background = '#3498DB';
				$color = '#FFFFFF';
				$highlight = '#2A8BCC';
			}

			if ( 'ocean' == get_post_meta( $post->ID, 'wpsp_read_more_color', true ) ) {
				$background = '#2980B9';
				$color = '#FFFFFF';
				$highlight = '#2475AB';
			}

			if ( 'amethyst' == get_post_meta( $post->ID, 'wpsp_read_more_color', true ) ) {
				$background = '#9B59B6';
				$color = '#FFFFFF';
				$highlight = '#8D4CA7';
			}

			if ( 'wisteria' == get_post_meta( $post->ID, 'wpsp_read_more_color', true ) ) {
				$background = '#8E44AD';
				$color = '#FFFFFF';
				$highlight = '#80399D';
			}

			if ( 'wet-asphalt' == get_post_meta( $post->ID, 'wpsp_read_more_color', true ) ) {
				$background = '#34495E';
				$color = '#FFFFFF';
				$highlight = '#263849';
			}

			if ( 'midnight-blue' == get_post_meta( $post->ID, 'wpsp_read_more_color', true ) ) {
				$background = '#2C3E50';
				$color = '#FFFFFF';
				$highlight = '#22303F';
			}

			if ( 'silver' == get_post_meta( $post->ID, 'wpsp_read_more_color', true ) ) {
				$background = '#BDC3C7';
				$color = '#FFFFFF';
				$highlight = '#ACB2B7';
			}

			if ( 'concrete' == get_post_meta( $post->ID, 'wpsp_read_more_color', true ) ) {
				$background = '#7F8C8D';
				$color = '#FFFFFF';
				$highlight = '#6D7B7C';
			}

			if ( 'graphite' == get_post_meta( $post->ID, 'wpsp_read_more_color', true ) ) {
				$background = '#454545';
				$color = '#FFFFFF';
				$highlight = '#363535';
			}

			update_post_meta( $post->ID, 'wpsp_read_more_background_color', $background );
			update_post_meta( $post->ID, 'wpsp_read_more_text_color', $color );
			update_post_meta( $post->ID, 'wpsp_read_more_border_color', $highlight );

			update_post_meta( $post->ID, 'wpsp_read_more_background_color_hover', $highlight );
			update_post_meta( $post->ID, 'wpsp_read_more_text_color_hover', $color );
			update_post_meta( $post->ID, 'wpsp_read_more_border_color_hover', $highlight );


			// If we're using a hollow button, clear the read more background
			if ( 'hollow' == get_post_meta( $post->ID, 'wpsp_read_more_style', true ) ) {
				update_post_meta( $post->ID, 'wpsp_read_more_background_color', '' );
				update_post_meta( $post->ID, 'wpsp_read_more_text_color', $highlight );
			}

			// Let's clean up our old options
			delete_post_meta( $post->ID, 'wpsp_read_more_style' );
			delete_post_meta( $post->ID, 'wpsp_read_more_color' );

		}

	}
}
