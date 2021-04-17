<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NF_FU_Database_Models_Upload
 */
final class NF_FU_Database_Models_Upload {

	/**
	 * @var wpdb
	 */
	protected $_db;

	/**
	 * @var string
	 */
	protected $_table_name = 'ninja_forms_uploads';

	/**
	 * @var array
	 */
	protected $_columns = array(
		'user_id',
		'form_id',
		'field_id',
		'data',
		'date_updated',
	);

	public function __construct() {
		global $wpdb;

		$this->_db         = $wpdb;
		$this->_table_name = $this->_db->prefix . $this->_table_name;
	}

	/**
	 * Insert a file upload
	 *
	 * @param int   $user_id
	 * @param int   $form_id
	 * @param int   $field_id
	 * @param array $data
	 *
	 * @return bool|int
	 */
	public function insert( $user_id, $form_id, $field_id, $data ) {
		$data = array(
			'user_id'  => $user_id,
			'form_id'  => $form_id,
			'field_id' => $field_id,
			'data'     => serialize( $data ),
		);

		$result = $this->_db->insert( $this->_table_name, $data );

		if ( false === $result ) {
			// TODO error handling

			return false;
		}

		return $this->_db->insert_id;
	}

	/**
	 * Update a file upload
	 *
	 * @param int   $id
	 * @param array $file_data
	 *
	 * @return false|int
	 */
	public function update( $id, $file_data ) {
		$data = array(
			'data' => serialize( $file_data ),
		);

		return $this->_db->update( $this->_table_name, $data, array( 'id' => $id ) );
	}

	/**
	 * Delete a file upload
	 *
	 * @param int $id
	 *
	 * @return false|int
	 */
	public function delete( $id ) {
		$sql = $this->_db->prepare( "DELETE FROM {$this->_table_name} WHERE id = %d", $id );

		return $this->_db->query( $sql );
	}

	/**
	 * Fetch uploads
	 *
	 * @param string $where_clause
	 *
	 * @return array|null|object
	 */
	public function fetch( $where_clause = '' ) {
		return $this->_db->get_results( "SELECT * FROM {$this->_table_name} {$where_clause} ORDER BY `date_updated` DESC", ARRAY_A );
	}

	/**
	 * Get single upload by ID
	 *
	 * @param int $id
	 *
	 * @return array|null|object|void
	 */
	public function get( $id ) {
		$sql = $this->_db->prepare( "SELECT * FROM {$this->_table_name} WHERE id = %d", $id );

		return $this->_db->get_row( $sql );
	}
}
