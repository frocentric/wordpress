<?php
defined( 'WPINC' ) or die;

class GeneratePress_Sites_Restore {
	/**
	 * Instance.
	 *
	 * @access private
	 * @var object Instance
	 * @since 1.9
	 */
	private static $instance;

	/**
	 * Initiator.
	 *
	 * @since 1.9
	 * @return object initialized object of class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {
		add_action( 'wp_ajax_generate_restore_theme_options', 	array( $this, 'theme_options' ) );
		add_action( 'wp_ajax_generate_restore_site_options', 	array( $this, 'site_options' ) );
		add_action( 'wp_ajax_generate_restore_content', 		array( $this, 'content' ) );
		add_action( 'wp_ajax_generate_restore_plugins', 		array( $this, 'plugins' ) );
		add_action( 'wp_ajax_generate_restore_widgets', 		array( $this, 'widgets' ) );
		add_action( 'wp_ajax_generate_restore_css', 			array( $this, 'css' ) );
		add_action( 'wp_ajax_generate_restore_site_clean_up', 	array( $this, 'clean_up' ) );
	}

	public function theme_options() {
		check_ajax_referer( 'generate_sites_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$backup_data = get_option( '_generatepress_site_library_backup', array() );

		if ( ! empty( $backup_data ) ) {

			if ( ! empty( $backup_data['theme_options']['mods'] ) ) {
				remove_theme_mods();
			}

			if ( ! empty( $backup_data['theme_options']['options'] ) ) {
				$option_keys = array(
					'generate_settings',
					'generate_background_settings',
					'generate_blog_settings',
					'generate_hooks',
					'generate_page_header_settings',
					'generate_secondary_nav_settings',
					'generate_spacing_settings',
					'generate_menu_plus_settings',
					'generate_woocommerce_settings',
				);

				foreach ( $option_keys as $key ) {
					delete_option( $key );
				}
			}

			$modules = generatepress_get_site_premium_modules();

			foreach ( $modules as $name => $key ) {
				delete_option( $key );
			}

			if ( ! empty( $backup_data['modules'] ) ) {
				foreach ( (array) $backup_data['modules'] as $name => $key ) {
					update_option( $key, 'activated' );
				}
			}

			// Theme options.
			foreach ( $backup_data['theme_options']['mods'] as $key => $val ) {
				// Only allow valid theme mods.
				if ( ! in_array( $key, GeneratePress_Sites_Helper::get_theme_mods() ) ) {
					GeneratePress_Sites_Helper::log( 'Bad theme mod key: ' . $key );
					continue;
				}

				set_theme_mod( $key, $val );
			}

			foreach ( $backup_data['theme_options']['options'] as $key => $val ) {
				// Only allow valid options.
				if ( ! in_array( $key, GeneratePress_Sites_Helper::get_theme_settings() ) ) {
					GeneratePress_Sites_Helper::log( 'Bad theme setting key: ' . $key );
					continue;
				}

				update_option( $key, $val );
			}
		}

		wp_send_json( __( 'Theme options restored.', 'gp-premium' ) );

		die();
	}

	public function site_options() {
		check_ajax_referer( 'generate_sites_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$backup_data = get_option( '_generatepress_site_library_backup', array() );

		if ( ! empty( $backup_data ) ) {
			foreach ( $backup_data['site_options'] as $key => $val ) {
				if ( in_array( $key, ( array ) generatepress_sites_disallowed_options() ) ) {
					GeneratePress_Sites_Helper::log( 'Disallowed option: ' . $key );
					continue;
				}

				if ( 'nav_menu_locations' === $key || 'custom_logo' === $key ) {
					set_theme_mod( $key, $val );
				} else {
					update_option( $key, $val );
				}
			}
		}

		wp_send_json( __( 'Site options restored.', 'gp-premium' ) );

		die();
	}

	public function content() {
		check_ajax_referer( 'generate_sites_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$backup_data = get_option( '_generatepress_site_library_backup', array() );

		if ( ! empty( $backup_data ) ) {
			global $wpdb;
			$post_ids = $wpdb->get_col( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_generatepress_sites_imported_post'" );
			$term_ids = $wpdb->get_col( "SELECT term_id FROM {$wpdb->termmeta} WHERE meta_key='_generatepress_sites_imported_term'" );

			foreach ( $post_ids as $id ) {
				wp_delete_post( $id, true );
			}
		}

		wp_send_json( __( 'Content restored.', 'gp-premium' ) );

		die();
	}

	public function plugins() {
		check_ajax_referer( 'generate_sites_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$backup_data = get_option( '_generatepress_site_library_backup', array() );

		if ( ! empty( $backup_data['plugins'] ) && ! empty( $backup_data['site_options'] ) ) {
			update_option( 'active_plugins', $backup_data['plugins'] );
		}

		wp_send_json( __( 'Plugins restored.', 'gp-premium' ) );

		die();
	}

	public function widgets() {
		check_ajax_referer( 'generate_sites_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$backup_data = get_option( '_generatepress_site_library_backup', array() );

		if ( ! empty( $backup_data['widgets'] ) ) {
			update_option( 'sidebars_widgets', $backup_data['widgets'] );
		}

		wp_send_json( __( 'Widgets restored.', 'gp-premium' ) );

		die();
	}

	public function css() {
		check_ajax_referer( 'generate_sites_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$backup_data = get_option( '_generatepress_site_library_backup', array() );

		if ( ! empty( $backup_data ) ) {
			$css = '';
			$current_css = wp_get_custom_css_post();

			if ( isset( $current_css->post_content ) ) {
				// Remove existing library CSS.
				$current_css->post_content = preg_replace( '#(/\\* GeneratePress Site CSS \\*/).*?(/\\* End GeneratePress Site CSS \\*/)#s', '', $current_css->post_content );
			}

			if ( ! empty( $backup_data['css'] ) ) {
				$current_css->post_content .= $backup_data['css'];
			}

			wp_update_custom_css_post( $current_css->post_content );
		}

		wp_send_json( __( 'CSS restored.', 'gp-premium' ) );

		die();
	}

	public function clean_up() {
		check_ajax_referer( 'generate_sites_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		delete_option( 'generate_dynamic_css_output' );
		delete_option( 'generate_dynamic_css_cached_version' );
		delete_option( '_generatepress_site_library_backup' );

		wp_send_json( __( 'Completed clean-up.', 'gp-premium' ) );

		die();
	}
}

GeneratePress_Sites_Restore::get_instance();