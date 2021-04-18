<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class NF_FU_Admin_UploadsTable extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( array(
			'singular' => __( 'Upload', 'ninja-forms-uploads' ),
			//singular name of the listed records
			'plural'   => __( 'Uploads', 'ninja-forms-uploads' ),
			//plural name of the listed records
			'ajax'     => false
			//should this table support ajax?
		) );
	}

	public function no_items() {
		_e( 'No uploads found.', 'ninja-forms-uploads' );
	}

	/**
	 * Prepare the items for the table to process
	 *
	 * @return Void
	 */
	public function prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		$form_id    = ! empty( $_REQUEST['form_id'] ) ? trim( wp_unslash( $_REQUEST['form_id'] ) ) : '';
		$begin_date = ! empty( $_REQUEST['begin_date'] ) ? trim( wp_unslash( $_REQUEST['begin_date'] ) ) : '';
		$end_date   = ! empty( $_REQUEST['end_date'] ) ? trim( wp_unslash( $_REQUEST['end_date'] ) ) : '';
		$search     = ! empty( $_REQUEST['s'] ) ? trim( wp_unslash( $_REQUEST['s'] ) ) : '';

		$data = $this->table_data( $form_id, $begin_date, $end_date, $search );
		usort( $data, array( &$this, 'sort_data' ) );

		$perPage     = 20;
		$currentPage = $this->get_pagenum();
		$totalItems  = count( $data );

		$this->set_pagination_args( array(
			'total_items' => $totalItems,
			'per_page'    => $perPage,
		) );

		$data = array_slice( $data, ( ( $currentPage - 1 ) * $perPage ), $perPage );

		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items           = $data;
	}

	/**
	 * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @return Array
	 */
	public function get_columns() {
		$columns = array(
			'cb'                => '<input type="checkbox" />',
			'filename'          => __( 'File Name', 'ninja-forms-uploads' ),
			'original_filename' => __( 'Original File name', 'ninja-forms-uploads' ),
			'date'              => __( 'Created', 'ninja-forms-uploads' ),
			'form_name'         => __( 'Form Name', 'ninja-forms-uploads' ),
			'user_name'         => __( 'User Name', 'ninja-forms-uploads' ),
			'file_location'     => __( 'File Location', 'ninja-forms-uploads' ),
		);

		return $columns;
	}

	/**
	 * Define which columns are hidden
	 *
	 * @return Array
	 */
	public function get_hidden_columns() {
		return array();
	}

	/**
	 * Define the sortable columns
	 *
	 * @return Array
	 */
	public function get_sortable_columns() {
		return array(
			'filename'          => array( 'filename', true ),
			'original_filename' => array( 'original_filename', true ),
			'date'              => array( 'date', true ),
			'form_name'         => array( 'form_name', true ),
			'user_name'         => array( 'user_name', true ),
			'file_location'     => array( 'file_location', true ),
		);
	}

	/**
	 * Get the table data
	 *
	 * @param string $form_id
	 * @param string $begin_date
	 * @param string $end_date
	 * @param string $search
	 *
	 * @return array
	 */
	private function table_data( $form_id = '', $begin_date = '', $end_date = '', $search = '' ) {
		$data = array();

		$where = array();
		if ( ! empty( $search ) ) {
			$where[] = "`data` LIKE '%$search%'";
		}

		if ( ! empty( $form_id ) ) {
			$where[] = "`form_id` = $form_id";
		}

		if ( ! empty( $begin_date ) ) {
			$begin_date = date( "Y-m-d 00:00:00", strtotime( $begin_date ) );
			$where[]    = "`date_updated` >= '$begin_date'";
		}

		if ( ! empty( $end_date ) ) {
			$end_date = date( "Y-m-d 23:59:59", strtotime( $end_date ) );
			$where[]  = "`date_updated` <= '$end_date'";
		}

		if ( ! empty( $where ) ) {
			$where = 'WHERE ' . implode( ' AND ', $where );
		} else {
			$where = '';
		}

		$uploads = NF_File_Uploads()->model->fetch( $where );

		$forms = array();
		$users = array();

		foreach ( $uploads as $upload ) {
			$upload_data = unserialize( $upload['data'] );

			if ( ! NF_File_Uploads()->controllers->uploads->file_exists( $upload_data ) ) {
				// Don't display a row for an upload that has been removed
				continue;
			}

			if ( isset( $forms[ $upload['form_id'] ] ) ) {
				$form_name = $forms[ $upload['form_id'] ];
			} else {
				$form                        = Ninja_Forms()->form( $upload['form_id'] )->get();
				$title                       = $form->get_setting( 'title' );
				$form_name                   = isset( $title ) ? $title : $form->get_id();
				$forms[ $upload['form_id'] ] = $form_name;
			}

			$user_name = __( 'Guest', 'ninja-forms-uploads' );
			if ( isset( $users[ $upload['user_id'] ] ) ) {
				$user_name = $users[ $upload['user_id'] ];
			} else {
				$user = get_user_by( 'id', $upload['user_id'] );
				if ( $user ) {
					$user_name                   = $user->user_nicename;
					$users[ $upload['user_id'] ] = $user_name;
				}
			}

			$data[] = array(
				'id'                => $upload['id'],
				'filename'          => $upload_data['file_name'],
				'original_filename' => $upload_data['user_file_name'],
				'date'              => $upload['date_updated'],
				'form_name'         => $form_name,
				'user_name'         => $user_name,
				'file_url'          => $upload_data['file_url'],
				'upload_location'   => $upload_data['upload_location'],
				'upload_id'         => isset( $upload_data['upload_id'] ) ? $upload_data['upload_id'] : 0,
				'sub_id'            => isset( $upload_data['sub_id'] ) ? $upload_data['sub_id'] : false,
			);
		}

		return $data;
	}

	/**
	 * Define what data to show on each column of the table
	 *
	 * @param  Array  $item        Data
	 * @param  String $column_name - Current column name
	 *
	 * @return Mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'filename':
				if ( isset( $item['file_url'] ) ) {
					$url = NF_File_Uploads()->controllers->uploads->get_file_url( $item['file_url'], $item );

					$value = sprintf( '<a href="%s" target="_blank">%s</a>', $url, $item[ $column_name ] );
				} else {
					$value = $item[ $column_name ];
				}
				$value .= '<div class="row-actions">';
				if ( isset( $url ) ) {
					$value .= '<span class="view"><a class="view" title="' . __( 'View this file', 'ninja-forms-uploads' ) . '" href="' . $url . '" target="_blank">' . __( 'View', 'ninja-forms-uploads' ) . '</a></span> | ';
				}

				if ( $item['sub_id'] ) {
					$sub_url = admin_url( 'post.php?post=' . $item['sub_id'] . '&action=edit' );
					$value   .= '<span class="view"><a class="view" title="' . __( 'View submission', 'ninja-forms-uploads' ) . '" href="' . $sub_url . '" target="_blank">' . __( 'View Submission', 'ninja-forms-uploads' ) . '</a></span> | ';
				}

				$delete_link = NF_File_Uploads()->page->get_url( '', array(
					'action' => 'delete',
					'upload' => $item['id'],
				), false );
				$delete_link = wp_nonce_url( $delete_link, "delete-upload_{$item['id']}" );
				$value .= '<span class="trash"><a class="trash ninja-forms-delete-upload" title="' . __( 'Delete this file', 'ninja-forms-uploads' ) . '" href="'. $delete_link . '">' . __( 'Delete', 'ninja-forms-uploads' ) . '</a></span>';
				$value .= '</div>';
				break;
			case 'file_location':
				$value = apply_filters( 'ninja_forms_uploads_file_location', $item['upload_location'] );
				break;
			default:
				$value = $item[ $column_name ];
		}

		return $value;
	}

	/**
	 * Allows you to sort the data by the variables set in the $_GET
	 *
	 * @return Mixed
	 */
	private function sort_data( $a, $b ) {
		// Set defaults
		$orderby = 'id';
		$order   = 'asc';

		// If orderby is set, use this as the sort column
		if ( ! empty( $_GET['orderby'] ) ) {
			$orderby = $_GET['orderby'];
		}

		// If order is set use this as the order
		if ( ! empty( $_GET['order'] ) ) {
			$order = $_GET['order'];
		}


		$result = strnatcmp( $a[ $orderby ], $b[ $orderby ] );

		if ( $order === 'asc' ) {
			return $result;
		}

		return -$result;
	}

	function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="bulk-delete-upload[]" value="%s" />', $item['id'] );
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array(
			'bulk-delete-upload' => __( 'Delete', 'ninja-forms-uploads' ),
		);

		return $actions;
	}

	/**
	 * Process the bulk delete
	 */
	public static function process_bulk_action() {
		// If the delete bulk action is triggered
		if ( ( isset( $_GET['action'] ) && 'bulk-delete-upload' === $_GET['action'] ) || ( isset( $_GET['action2'] ) && 'bulk-delete-upload' === $_GET['action2'] ) ) {

			$delete_ids = esc_sql( $_GET['bulk-delete-upload'] );

			$count = 0;
			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				$result = self::delete_item( absint( $id ) );
				if ( $result ) {
					$count++;
				}
			}

			if ( 0 === $count ) {
				$args = array( 'delete-error' => count( $delete_ids ) );
			} else {
				$args = array( 'deleted' => $count );
			}

			$redirect = add_query_arg( $args, wp_get_referer() );
			wp_redirect( $redirect );
			exit;
		}
	}

	/**
	 * Delete single upload
	 */
	public static function delete_upload() {
		if ( ! NF_FU_Helper::is_page() ) {
			return;
		}

		if ( ! isset( $_GET['action'] ) || 'delete' !== $_GET['action'] ) {
			return;
		}

		$upload_id = filter_input( INPUT_GET, 'upload', FILTER_VALIDATE_INT );

		if ( ! isset( $upload_id ) ) {
			return;
		}

		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'delete-upload_' . $upload_id ) ) {
			return;
		}

		$result = self::delete_item( $upload_id );

		$args = array();

		if ( $result ) {
			$args['deleted'] = 1;
		} else {
			$args['delete-error'] = 1;
		}

		$redirect = add_query_arg( $args, wp_get_referer() );
		wp_redirect( $redirect );
		exit;
	}

	/**
	 * Display notice for delete actions
	 */
	public static function action_notices() {
		if ( ! NF_FU_Helper::is_page() ) {
			return;
		}

		if ( isset( $_GET['deleted'] ) ) {
			Ninja_Forms::template( 'admin-notice.html.php', array(
				'class'   => 'updated notice notice-success',
				'message' => sprintf( _n( '%s upload deleted', '%s uploads deleted', $_GET['deleted'], 'ninja-forms-upload' ), $_GET['deleted'] ),
			) );
		}

		if ( isset( $_GET['delete-error'] ) ) {
			Ninja_Forms::template( 'admin-notice.html.php', array(
				'class'   => 'error notice notice-error',
				'message' => _n( 'There was a problem deleting the upload', 'There was a problem deleting the uploads', $_GET['delete-error'], 'ninja-forms-upload' ),
			) );
		}
	}

	/**
	 * Delete the item from the file uploads table and remove file from server.
	 *
	 * @param int $id
	 *
	 * @return false|int
	 */
	public static function delete_item( $id ) {
		$upload = NF_File_Uploads()->controllers->uploads->get( $id );

		if ( file_exists( $upload->file_path ) ) {
			unlink( $upload->file_path );
		}

		do_action( 'ninja_forms_upload_delete_upload', $upload );

		return NF_File_Uploads()->model->delete( $id );
	}

	public function extra_tablenav( $which ) {
		if ( 'top' !== $which ) {
			return;
		}
		$forms = Ninja_Forms()->form()->get_forms();

		$form_options = array();
		foreach ( $forms as $form ) {
			$form_options[ $form->get_id() ] = $form->get_setting( 'title' );
		}

		if ( isset( $_REQUEST['form_id'] ) ) {
			$form_selected = $_REQUEST['form_id'];
		} else {
			$form_selected = 0;
		}

		if ( isset( $_REQUEST['begin_date'] ) ) {
			$begin_date = $_REQUEST['begin_date'];
		} else {
			$begin_date = '';
		}

		if ( isset( $_REQUEST['end_date'] ) ) {
			$end_date = $_REQUEST['end_date'];
		} else {
			$end_date = '';
		}

		NF_File_Uploads()->template( 'admin-menu-uploads-filter', compact( 'form_options', 'form_selected', 'begin_date', 'end_date' ) );
	}
}
