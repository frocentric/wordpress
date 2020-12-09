<?php
_deprecated_file( __FILE__, '4.3', 'Tribe__Admin__Notices' );

class Tribe__Events__Pro__Recurrence__Admin_Notices {
	protected static $instance;

	public static function instance() {
		_deprecated_function( __METHOD__, '4.3', 'Tribe__Admin__Notices' );

		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function display_editing_all_recurrences_notice() {
		_deprecated_function( __METHOD__, '4.3', 'Tribe__Admin__Notices' );
	}

	public function display_created_recurrences_notice() {
		_deprecated_function( __METHOD__, '4.3', 'Tribe__Admin__Notices' );
	}
}