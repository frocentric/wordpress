<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NF_FU_Database_Migrations extends NF_Database_Migrations {

	protected $migrations = array();

	public function __construct() {
		$this->migrations['uploads'] = new NF_FU_Database_Migrations_Uploads();
	}
}