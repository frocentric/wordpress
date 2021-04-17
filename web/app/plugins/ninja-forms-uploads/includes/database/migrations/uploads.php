<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NF_FU_Database_Migrations_Uploads extends NF_Abstracts_Migration {

	protected $version = '3.0.10';
	protected $version_key = 'uploads_table_version';

	public function __construct() {
		parent::__construct( 'ninja_forms_uploads', '' );
	}

	/**
	 * Create table
	 */
	public function run() {
		$table_name = $this->table_name;
		if ( method_exists( $this, 'table_name' ) ) {
			$table_name = $this->table_name();
		}

		$query = "
CREATE TABLE {$table_name} (
id int(11) NOT NULL AUTO_INCREMENT,
user_id int(11) DEFAULT NULL,
form_id int(11) NOT NULL,
field_id int(11) NOT NULL,
data longtext CHARACTER SET utf8 NOT NULL,
date_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (id)
) $this->charset_collate;";

		dbDelta( $query );
	}

	/**
	 * Install the table and apply any changes
	 */
	public function _run() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}
		
		$current_version = Ninja_Forms()->get_setting( $this->version_key, '0' );
		$version        = $this->version;

		if ( version_compare( $current_version, $version, '!=' ) ) {

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			// Run the migration
			$this->run();

			// Update our table version in the options
			Ninja_Forms()->update_setting( $this->version_key, $version );
		}
	}
}