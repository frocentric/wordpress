<?php
if ( !class_exists( 'NF_Notification_Base_Type' ) ) {
	return FALSE;
}
class NF_MailChimp_Notification extends NF_Notification_Base_Type {

	/**
	 * Get things rolling
	 */
	function __construct() {
		$this->name = __( 'MailChimp', 'ninja-forms-mc' );
	}

	/**
	 * Output our edit screen
	 *
	 * @access public
	 * @since 1.3
	 * @return void
	 */
	public function edit_screen( $id = '' ) {

		$lists = ninja_forms_mc_get_mailchimp_lists();

		if( is_wp_error( $lists ) || ! is_array( $lists ) ) {
			return;
		}

		$saved_id = nf_get_object_meta_value( $id, 'list-id' );
?>
		<tr>
			<th scope="row">
				<label for="settings-list-id"><?php _e( 'List', 'ninja-forms-mc' ); ?></label>
			</th>
			<td>
				<select name="settings[list-id]" id="settings-list-id">
				<?php foreach( $lists as $list ) : ?>
					<option value="<?php echo esc_attr( $list['value'] ); ?>"<?php selected( $saved_id, $list['value'] ); ?>><?php echo esc_html( $list['name'] ); ?></option>
				<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<?php if( empty( $saved_id ) ) return; ?>
		<tr>
			<th scope="row"><?php _e( 'Merge Vars', 'ninja-forms-mc' ); ?></th>
			<td>
				<?php
				$saved_vars = maybe_unserialize( nf_get_object_meta_value( $id, 'merge-vars' ) );
				foreach( ninja_forms_mc_get_merge_vars( $saved_id ) as $var ) : ?>
					<?php $value = isset( $saved_vars[ $var['tag'] ] ) ? $saved_vars[ $var['tag'] ] : ''; ?>
					<p>
						<input type="text" name="settings[merge-vars][<?php echo $var['tag']; ?>]" value="<?php echo esc_attr( $value ); ?>" class="nf-tokenize" data-token-limit="1" data-key="merge-vars[<?php echo $var['tag']; ?>]" data-type="all"/>
						<span class="description"><?php echo esc_html( $var['name'] . ' (' . $var['tag'] . ')' ); ?></span>
						<?php if( ! empty( $var['req'] ) ) : ?>
							<span class="description required"><strong><?php _e( 'This field is required by MailChimp. You must use it.', 'ninja-forms-mc' ); ?></strong></span>
						<?php endif; ?>
					</p><br/>
				<?php
				endforeach;
				?>
			</td>
		</tr>
		<?php
		$groups = ninja_forms_mc_get_groups( $saved_id );
		if( $groups ) : ?>
		<tr>
			<th scope="row"><?php _e( 'Groups', 'ninja-forms-mc' ); ?></th>
			<td>
				<?php
				$saved_groups = maybe_unserialize( nf_get_object_meta_value( $id, 'groups' ) );
				if( is_array( $groups ) ) {
					foreach( $groups as $key => $group ) { ?>
						<p>
							<input type="checkbox" name="settings[groups][<?php echo $group['id']; ?>][]" id="settings_groups_<?php echo $group['id']; ?>" value="1"<?php checked( true, ! empty( $saved_groups[ $group['id'] ] ) ); ?>/>
							<label for="settings_groups_<?php echo $group['id']; ?>"><?php echo esc_html( $group['name'] . ' (' . $group['id'] . ')' ); ?></label><br/>

							<?php foreach( $group['groups'] as $g ) : ?>
								&nbsp;&nbsp;&nbsp;<input type="checkbox" name="settings[groups][<?php echo $group['id']; ?>][<?php echo $g['id']; ?>]" id="settings_groups_<?php echo $group['id'] . '_' . $g['id']; ?>" value="<?php echo esc_attr( $g['name'] ); ?>"<?php checked( true, isset( $saved_groups[ $group['id'] ][ $g['id'] ] ) ); ?>/>
								<label for="settings_groups_<?php echo $group['id'] . '_' . $g['id']; ?>"><?php echo esc_html( $g['name'] ); ?></label><br/>
							<?php endforeach; ?>
						</p><br/>
					<?php
					}
				}
				?>
				<p><?php _e( 'Select the groups you would like subscribers added to', 'ninja-forms-mc' ); ?></p>
			</td>
		</tr>
		<?php endif; ?>
		<tr>
			<th scope="row">
				<label for="settings-double-opt"><?php _e( 'Double Opt-In', 'ninja-forms-mc' ); ?></label>
			</th>
			<td>
				<select name="settings[double-opt]" id="settings-double-opt">
					<option value="yes"<?php selected( nf_get_object_meta_value( $id, 'double-opt' ), 'yes' ); ?>><?php _e( 'Yes', 'ninja-forms-mc' ); ?></option>
					<option value="no"<?php selected( nf_get_object_meta_value( $id, 'double-opt' ), 'no' ); ?>><?php _e( 'No', 'ninja-forms-mc' ); ?></option>
				</select>
				<span class="description"><?php _e( 'Should subscribers be required to confirm their susbcription?', 'ninja-forms-mc' ); ?></span>
			</td>
		</tr>
<?php
	}

	/**
	 * Process our MailChimp notification
	 *
	 * @access public
	 * @since 1.3
	 * @return void
	 */
	public function process( $id ) {

		global $ninja_forms_processing;

		$list_id       = Ninja_Forms()->notification( $id )->get_setting( 'list-id' );
		$groups        = maybe_unserialize( Ninja_Forms()->notification( $id )->get_setting( 'groups' ) );
		$mapped_vars   = maybe_unserialize( Ninja_Forms()->notification( $id )->get_setting( 'merge-vars' ) );
		$double_opt_in = Ninja_Forms()->notification( $id )->get_setting( 'double-opt' );
		$double_opt_in = ! empty( $double_opt_in ) && 'yes' === strtolower( $double_opt_in );
		$needs_opt_int = false;
		$opted_in      = true;

		// Check if Mail Chimp is enabled for this form
		if ( empty( $list_id ) ) {
			return;
		}


		// Get all the user submitted values
		$all_fields = $ninja_forms_processing->get_all_fields();

		// Look for an opt-in checkbox
		foreach ( $all_fields as $field_id => $value ) {

			$field = $ninja_forms_processing->get_field_settings( $field_id );

			if( '_checkbox' !== $field['type'] ) {
				continue;
			}

			if( ! empty( $field['data']['nf_mc_opt_in'] ) ) {

				$opted_in = 'checked' === $all_fields[ $field_id ];

			}

		}

		// Return if user did not opt in
		if( ! $opted_in ) {
			return;
		}

		$subscriber = array(
			'merge_vars' => array(
				'optin_ip' => ninja_forms_get_ip()
			)
		);

		if( ! empty( $groups ) ) {

			$groupings = array();

			foreach( $groups as $group_id => $group ) {

				foreach( $group as $key => $g ) {

					// NF has a bug that adds 0 => 1 key value pair. It has to be removed since it's not a valid group
					if( 1 === intval( $g ) ) {
						unset( $group[ $key ] );
					}
				}

				$groupings[] = array(
					'id'     => $group_id,
					'groups' => array_values( $group )
				);
			}

			$subscriber['merge_vars']['groupings'] = $groupings;
		}

		if ( is_array( $all_fields ) && is_array( $mapped_vars ) ) { //Make sure $all_fields is an array.

			$mapped_vars = array_map( array( $this, 'sanitize_key' ), $mapped_vars );

			foreach( $mapped_vars as $var => $field_id ) {

				$subscriber['merge_vars'][ $var ] = isset( $all_fields[ $field_id ] ) ? $all_fields[ $field_id ] : '';

			}

			if( ! empty( $subscriber['merge_vars']['EMAIL'] ) ) {

				$subscriber['email'] = $subscriber['merge_vars']['EMAIL'];

			} else {

				// Loop through each of our submitted values and find an email
				foreach ( $all_fields as $field_id => $value ) {

					$field = $ninja_forms_processing->get_field_settings( $field_id );

					if ( ! empty( $field['data']['email'] ) && is_email( $value ) ) {
						$subscriber['email'] = $value;
					}

				}

			}

			if ( ! empty( $subscriber ) ) {
				$success = ninja_forms_mc_subscribe_email( $subscriber, $list_id, $double_opt_in );

			}
		}

	}

	/**
	 * Removes field_ from field IDs
	 *
	 * @access public
	 * @since 1.3
	 * @return int
	 */
	private function sanitize_key( $key = '' ) {
		return absint( str_replace( 'field_', '', $key ) );
	}

}
