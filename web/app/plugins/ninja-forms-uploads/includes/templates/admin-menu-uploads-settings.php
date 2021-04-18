<form action="" method="POST">

	<?php if ( isset( $errors ) && $errors ): ?>
		<?php foreach ( $errors as $error_id => $error ): ?>
			<?php $message = $error . " <a href='#$error_id'>" . __( 'Fix it.', 'ninja-forms-uploads' ) . '</a>'; ?>
			<?php Ninja_Forms::template( 'admin-notice.html.php', array(
				'class'   => 'error',
				'message' => $message,
			) ); ?>
		<?php endforeach; ?>
	<?php endif; ?>

	<?php
	$args = array(
		'group'       => 'general',
		'group_label' => __( 'Upload Settings', 'ninja-forms-uploads' ),
		'settings'    => NF_File_Uploads()->config( 'settings-upload' ),
	);

	NF_File_Uploads()->template( 'admin-menu-meta-box', $args ); ?>

	<input type="hidden" name="update_ninja_forms_settings_nonce" value="<?php echo wp_create_nonce( "ninja_forms_settings_nonce" ); ?>">
	<input type="hidden" name="update_ninja_forms_settings">
	<input type="submit" class="button button-primary" value="<?php echo $save_button_text; ?>">

</form>