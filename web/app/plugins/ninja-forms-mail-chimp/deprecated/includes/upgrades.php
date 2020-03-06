<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display Upgrade Notices
 *
 * @since 1.3
 * @return void
 */
function ninja_forms_mailchimp_show_upgrade_notices() {

	// Convert notifications
	if ( isset ( $_GET['page'] ) && $_GET['page'] == 'nf-processing' ) {
		return; // Don't show notices on the processing page.
	}

	$upgrade_13 = get_option( 'nf_mc_13_upgrade_complete', false );

	if ( ! $upgrade_13 ) {
		$title = urlencode( __( 'Updating Ninja Forms MailChimp Options', 'ninja-forms-mc' ) );
		printf(
			'<div class="update-nag">' . __( 'Ninja Forms needs to upgrade your MailChimp form settings, click %shere%s to start the upgrade.', 'ninja-forms' ) . '</div>',
			'<a href="' . admin_url( 'index.php?page=nf-processing&action=upgrade_13_mailchimp&title=' . $title ) . '">', '</a>'
		);
	}

}
add_action( 'admin_notices', 'ninja_forms_mailchimp_show_upgrade_notices' );


/**
 * Upgrade handler
 *
 * @since 1.3
 * @return void
 */
if ( !class_exists( 'NF_Step_Processing' ) ) {
	return FALSE;
}
class Ninja_Forms_MC_Upgrade_13 extends NF_Step_Processing {

	function __construct() {
		$this->action = 'upgrade_13_mailchimp';

		parent::__construct();
	}

	public function loading() {

		// Get our total number of forms.
		$form_count      = nf_get_form_count();
		$completed_count = count( get_option( 'nf_mc_13_updated_forms', array() ) );

		if( $completed_count >= $form_count ) {
			return array( 'complete' => true );
		}

		// Get all our forms
		$forms = ninja_forms_get_all_forms( true );

		$x = 1;
		if ( is_array( $forms ) ) {
			foreach ( $forms as $form ) {
				$this->args['forms'][$x] = $form['id'];
				$x++;
			}
		}

		if( empty( $this->total_steps ) || $this->total_steps <= 1 ) {
			$this->total_steps = $form_count;
		}

		$args = array(
			'total_steps' 	=> $this->total_steps,
			'step' 			=> 1,
		);

		$this->redirect = admin_url( 'admin.php?page=ninja-forms' );

		return $args;
	}

	public function step() {

		// Get our form ID
		$form_id = $this->args['forms'][ $this->step ];

		// Get a list of forms that we've already converted.
		$completed_forms = get_option( 'nf_mc_13_updated_forms', array() );

		// Bail if we've already converted the notifications for this form.
		if ( in_array( $form_id, $completed_forms ) ) {
			return false;
		}

		$settings = nf_get_form_settings( $form_id );

		// Check if this is a MailChimp form
		if( empty( $settings['mailchimp_signup_form'] ) ) {
			return false;
		}

		$list_id = $settings['ninja_forms_mc_list'];
		$opt_in  = isset( $settings['ninja_forms_mc_double_opt_in'] ) ? 'yes' : 'no';

		// Find email and name fields
		$fields     = nf_get_fields_by_form_id( $form_id );
		$merge_vars = array();

		if ( is_array( $fields ) ) {

			//Loop through each of our submitted values.
			foreach ( $fields as $field_id => $field ) {

				if ( ! empty( $field['data']['email'] ) ) {
					$merge_vars['EMAIL'] = 'field_' . $field_id;
					continue;
				}

				if ( ! empty( $field['data']['first_name'] ) ) {
					$merge_vars['FNAME'] = 'field_' . $field_id;
					continue;
				}

				if ( ! empty( $field['data']['last_name'] ) ) {
					$merge_vars['LNAME'] = 'field_' . $field_id;
					continue;
				}
			}
		}

		$n_id = nf_insert_notification( $form_id );

		// Update our notification name
		Ninja_Forms()->notification( $n_id )->activate();
		Ninja_Forms()->notification( $n_id )->update_setting( 'type', 'mailchimp' );
		Ninja_Forms()->notification( $n_id )->update_setting( 'name', __( 'MailChimp', 'ninja-forms-mc' ) );
		Ninja_Forms()->notification( $n_id )->update_setting( 'list-id', $list_id );
		Ninja_Forms()->notification( $n_id )->update_setting( 'double-opt', $opt_in );
		Ninja_Forms()->notification( $n_id )->update_setting( 'merge-vars', $merge_vars );

		$completed_forms = get_option( 'nf_mc_13_updated_forms' );
		if ( ! is_array( $completed_forms ) || empty ( $completed_forms ) ) {
			$completed_forms = array( $form_id );
		} else {
			$completed_forms[] = $form_id;
		}
		update_option( 'nf_mc_13_updated_forms', $completed_forms );

	}

	public function complete() {
		update_option( 'nf_mc_13_upgrade_complete', true );
	}

}

if( is_admin() ) {
	$nf_mc_upgrade_13 = new Ninja_Forms_MC_Upgrade_13;
}
