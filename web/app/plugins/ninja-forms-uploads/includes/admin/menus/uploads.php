<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class NF_FU_Admin_Menus_Uploads extends NF_Abstracts_Submenu {

	public $parent_slug = 'ninja-forms';
	public $menu_slug = 'ninja-forms-uploads';
	public $priority = 14;
	public $table;

	/**
	 * NF_FU_Admin_Menus_Uploads constructor.
	 */
	public function __construct() {
		$this->page_title  = __( 'File Uploads', 'ninja-forms-uploads' );

		parent::__construct();

		if( isset( $_POST[ 'update_ninja_forms_settings' ] ) ) {
			$this->update_settings();
		}

		if ( ! defined( 'DOING_AJAX' ) ) {
			add_action( 'admin_init', array( $this, 'maybe_redirect_to_remove_referrer' ) );
			add_action( 'admin_init', array( 'NF_FU_Admin_UploadsTable', 'process_bulk_action' ) );
			add_action( 'admin_init', array( 'NF_FU_Admin_UploadsTable', 'delete_upload' ) );
			add_action( 'admin_notices', array( 'NF_FU_Admin_UploadsTable', 'action_notices' ) );
		}
	}

	/**
	 * Get URL to the settings page
	 *
	 * @param string $tab
	 * @param array  $args
	 *
	 * @param bool   $esc
	 *
	 * @return string
	 */
	public function get_url( $tab = '', $args = array(), $esc = true ) {
		$url = admin_url( 'admin.php' );

		$defaults = array(
			'page' => $this->menu_slug,
		);

		if ( $tab ) {
			$args['tab'] = $tab;
		}

		$args = array_merge( $args, $defaults );

		$url = add_query_arg( $args, $url );

		if ( $esc ) {
			$url = esc_url( $url );
		}

		return $url;
	}

	/**
	 * Get the tabs for the settings page
	 * 
	 * @return mixed|void
	 */
	protected function get_tabs() {
		$tabs = array(
			'browse'   => __( 'Browse Uploads', 'ninja-forms-uploads' ),
			'settings' => __( 'Upload Settings', 'ninja-forms-uploads' ),
		);

		return apply_filters( 'ninja_forms_uploads_tabs', $tabs );
	}

	/**
	 * @param null $tabs
	 *
	 * @return mixed
	 */
	protected function get_active_tab( $tabs = null ) {
		if ( is_null( $tabs ) ) {
			$tabs = $this->get_tabs();
		}
		$tab_keys = array_keys( $tabs );

		return ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : reset( $tab_keys );
	}

	public function maybe_redirect_to_remove_referrer() {
		if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'ninja-forms-uploads' ) {
			return;
		}

		if ( ! empty( $_GET['_wp_http_referer'] ) ) {
			wp_redirect( remove_query_arg( array(
				'_wp_http_referer',
				'_wpnonce',
			), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
			exit;
		}
	}

	/**
	 *
	 */
	public function display() {
		$tabs = $this->get_tabs();
		$active_tab = $this->get_active_tab( $tabs );
		$save_button_text = __( 'Save', 'ninja-forms-uploads');

		$table = false;
		if ( 'browse' === $active_tab ) {
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_style( 'jquery-ui-datepicker', Ninja_Forms::$url . 'deprecated/assets/css/jquery-ui-fresh.min.css' );
			$table = new NF_FU_Admin_UploadsTable();
			$table->prepare_items();
		}

		wp_enqueue_script( 'postbox' );
		wp_enqueue_script( 'jquery-ui-draggable' );

		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );

		NF_File_Uploads()->template( 'admin-menu-uploads', compact( 'tabs', 'active_tab', 'save_button_text', 'table' ) );
	}

	/**
	 * Handle saving settings
	 */
	protected function update_settings() {
		if ( ! isset( $_POST['update_ninja_forms_settings'] ) ) {
			return;
		}

		$plugin_settings = NF_File_Uploads()->controllers->settings->get_settings();
		$whitelist       = apply_filters( 'ninja_forms_file_uploads_settings_whitelist', NF_File_Uploads()->config( 'settings-upload' ) ) ;

		$updates = false;

		foreach ( $whitelist as $key => $setting ) {
			$value = filter_input( INPUT_POST, $key );
			if ( ! isset( $value ) ) {
				continue;
			}

			if ( isset( $plugin_settings[ $key ] ) && $value === $plugin_settings[ $key ] ) {
				continue;
			}

			if ( 'custom_upload_dir' !== $key ) {
				$value = sanitize_text_field( $value );
			}

			NF_File_Uploads()->controllers->settings->set_setting( $key, $value );
			$updates = true;
		}

		if ( $updates ) {
			NF_File_Uploads()->controllers->settings->update_settings();
		}
	}
}

