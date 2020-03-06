<?php

function nf_cl_show_upgrade_notices() {
	// Show an update notice if the user's version of Ninja Forms is below 2.8.6.
	// Compare our Ninja Forms version.

	if ( defined( 'NF_PLUGIN_VERSION' ) ) {
		if ( version_compare( NF_PLUGIN_VERSION, '2.8.6', '<' ) ) {
			$display = true;
		} else {
			$display = false;
		}
	} else {
		$display = false;
	}
	
	if ( $display ) {
		printf( '<div class="update-nag">' . __( 'This version of Conditional Logic requires at least version 2.8.6 of Ninja Forms. Please visit your <a href="%s">plugins page</a> to update.', 'ninja-forms-conditionals' ) . '</div>',
			admin_url( 'plugins.php' )		
		);
	}
}

add_action( 'admin_notices', 'nf_cl_show_upgrade_notices' );
