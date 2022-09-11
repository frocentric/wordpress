<?php
/**
 * This file handles the import/export functionality.
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

/**
 * Import/export class.
 */
class GeneratePress_Import_Export {
	/**
	 * Instance.
	 *
	 * @access private
	 * @var object Instance
	 * @since 1.7
	 */
	private static $instance;

	/**
	 * Initiator.
	 *
	 * @since 1.7
	 * @return object initialized object of class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Add necessary actions.
	 *
	 * @since 1.7
	 */
	public function __construct() {
		add_action( 'generate_admin_right_panel', array( $this, 'build_html' ), 15 );
		add_action( 'admin_init', array( $this, 'export' ) );
		add_action( 'admin_init', array( $this, 'import' ) );
	}

	/**
	 * Build our export and import HTML.
	 *
	 * @since 1.7
	 */
	public static function build_html() {
		?>
		<div class="postbox generate-metabox" id="generate-ie">
			<h3 class="hndle"><?php esc_html_e( 'Import/Export', 'gp-premium' ); ?></h3>
			<div class="inside">
				<form method="post">
					<h3 style="font-size: 15px;"><?php esc_html_e( 'Export', 'gp-premium' ); ?></h3>
					<span class="show-advanced"><?php _e( 'Advanced', 'gp-premium' ); ?></span>
					<div class="export-choices advanced-choices">
						<label>
							<input type="checkbox" name="module_group[]" value="generate_settings" checked />
							<?php _ex( 'Core', 'Module name', 'gp-premium' ); ?>
						</label>

						<?php if ( generatepress_is_module_active( 'generate_package_backgrounds', 'GENERATE_BACKGROUNDS' ) ) : ?>
							<label>
								<input type="checkbox" name="module_group[]" value="generate_background_settings" checked />
								<?php _ex( 'Backgrounds', 'Module name', 'gp-premium' ); ?>
							</label>
						<?php endif; ?>

						<?php if ( generatepress_is_module_active( 'generate_package_blog', 'GENERATE_BLOG' ) ) : ?>
							<label>
								<input type="checkbox" name="module_group[]" value="generate_blog_settings" checked />
								<?php _ex( 'Blog', 'Module name', 'gp-premium' ); ?>
							</label>
						<?php endif; ?>

						<?php if ( generatepress_is_module_active( 'generate_package_hooks', 'GENERATE_HOOKS' ) ) : ?>
							<label>
								<input type="checkbox" name="module_group[]" value="generate_hooks" checked />
								<?php _ex( 'Hooks', 'Module name', 'gp-premium' ); ?>
							</label>
						<?php endif; ?>

						<?php if ( generatepress_is_module_active( 'generate_package_page_header', 'GENERATE_PAGE_HEADER' ) ) : ?>
							<label>
								<input type="checkbox" name="module_group[]" value="generate_page_header_settings" checked />
								<?php _ex( 'Page Header', 'Module name', 'gp-premium' ); ?>
							</label>
						<?php endif; ?>

						<?php if ( generatepress_is_module_active( 'generate_package_secondary_nav', 'GENERATE_SECONDARY_NAV' ) ) : ?>
							<label>
								<input type="checkbox" name="module_group[]" value="generate_secondary_nav_settings" checked />
								<?php _ex( 'Secondary Navigation', 'Module name', 'gp-premium' ); ?>
							</label>
						<?php endif; ?>

						<?php if ( generatepress_is_module_active( 'generate_package_spacing', 'GENERATE_SPACING' ) ) : ?>
							<label>
								<input type="checkbox" name="module_group[]" value="generate_spacing_settings" checked />
								<?php _ex( 'Spacing', 'Module name', 'gp-premium' ); ?>
							</label>
						<?php endif; ?>

						<?php if ( generatepress_is_module_active( 'generate_package_menu_plus', 'GENERATE_MENU_PLUS' ) ) : ?>
							<label>
								<input type="checkbox" name="module_group[]" value="generate_menu_plus_settings" checked />
								<?php _ex( 'Menu Plus', 'Module name', 'gp-premium' ); ?>
							</label>
						<?php endif; ?>

						<?php if ( generatepress_is_module_active( 'generate_package_woocommerce', 'GENERATE_WOOCOMMERCE' ) ) : ?>
							<label>
								<input type="checkbox" name="module_group[]" value="generate_woocommerce_settings" checked />
								<?php _ex( 'WooCommerce', 'Module name', 'gp-premium' ); ?>
							</label>
						<?php endif; ?>

						<?php if ( generatepress_is_module_active( 'generate_package_copyright', 'GENERATE_COPYRIGHT' ) ) : ?>
							<label>
								<input type="checkbox" name="module_group[]" value="copyright" checked />
								<?php _ex( 'Copyright', 'Module name', 'gp-premium' ); ?>
							</label>
						<?php endif; ?>

						<?php do_action( 'generate_export_items' ); ?>
					</div>
					<p><input type="hidden" name="generate_action" value="export_settings" /></p>
					<p style="margin-bottom:0">
						<?php wp_nonce_field( 'generate_export_nonce', 'generate_export_nonce' ); ?>
						<?php submit_button( __( 'Export', 'gp-premium' ), 'button-primary', 'submit', false, array( 'id' => '' ) ); ?>
					</p>
				</form>

				<h3 style="font-size: 15px;margin-top: 30px;"><?php esc_html_e( 'Import', 'gp-premium' ); ?></h3>
				<form method="post" enctype="multipart/form-data">
					<p>
						<input type="file" name="import_file"/>
					</p>
					<p style="margin-bottom:0">
						<input type="hidden" name="generate_action" value="import_settings" />
						<?php wp_nonce_field( 'generate_import_nonce', 'generate_import_nonce' ); ?>
						<?php submit_button( __( 'Import', 'gp-premium' ), 'button-primary', 'submit', false, array( 'id' => '' ) ); ?>
					</p>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Export our chosen options.
	 *
	 * @since 1.7
	 */
	public static function export() {
		if ( empty( $_POST['generate_action'] ) || 'export_settings' !== $_POST['generate_action'] ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['generate_export_nonce'], 'generate_export_nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$modules = self::get_modules();
		$theme_mods = self::get_theme_mods();
		$settings = self::get_settings();

		$data = array(
			'modules' => array(),
			'mods' => array(),
			'options' => array(),
		);

		foreach ( $modules as $name => $value ) {
			if ( 'activated' === get_option( $value ) ) {
				$data['modules'][ $name ] = $value;
			}
		}

		foreach ( $theme_mods as $theme_mod ) {
			if ( 'generate_copyright' === $theme_mod ) {
				if ( in_array( 'copyright', $_POST['module_group'] ) ) {
					$data['mods'][ $theme_mod ] = get_theme_mod( $theme_mod );
				}
			} else {
				if ( in_array( 'generate_settings', $_POST['module_group'] ) ) {
					$data['mods'][ $theme_mod ] = get_theme_mod( $theme_mod );
				}
			}
		}

		foreach ( $settings as $setting ) {
			if ( in_array( $setting, $_POST['module_group'] ) ) {
				$data['options'][ $setting ] = get_option( $setting );
			}
		}

		$data = apply_filters( 'generate_export_data', $data );

		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=generate-settings-export-' . date( 'Ymd' ) . '.json' ); // phpcs:ignore -- Prefer date().
		header( 'Expires: 0' );

		echo wp_json_encode( $data );
		exit;
	}

	/**
	 * Import our exported file.
	 *
	 * @since 1.7
	 */
	public static function import() {
		if ( empty( $_POST['generate_action'] ) || 'import_settings' !== $_POST['generate_action'] ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['generate_import_nonce'], 'generate_import_nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$filename = $_FILES['import_file']['name'];
		$extension = end( explode( '.', $_FILES['import_file']['name'] ) );

		if ( 'json' !== $extension ) {
			wp_die( __( 'Please upload a valid .json file', 'gp-premium' ) );
		}

		$import_file = $_FILES['import_file']['tmp_name'];

		if ( empty( $import_file ) ) {
			wp_die( __( 'Please upload a file to import', 'gp-premium' ) );
		}

		// Retrieve the settings from the file and convert the json object to an array.
		$settings = json_decode( file_get_contents( $import_file ), true ); // phpcs:ignore -- file_get_contents() is fine here.

		foreach ( (array) $settings['modules'] as $key => $val ) {
			if ( in_array( $val, self::get_modules() ) ) {
				update_option( $val, 'activated' );
			}
		}

		foreach ( (array) $settings['mods'] as $key => $val ) {
			if ( in_array( $key, self::get_theme_mods() ) ) {
				set_theme_mod( $key, $val );
			}
		}

		foreach ( (array) $settings['options'] as $key => $val ) {
			if ( in_array( $key, self::get_settings() ) ) {
				update_option( $key, $val );
			}
		}

		// Delete existing dynamic CSS cache.
		delete_option( 'generate_dynamic_css_output' );
		delete_option( 'generate_dynamic_css_cached_version' );

		$dynamic_css_data = get_option( 'generatepress_dynamic_css_data', array() );

		if ( isset( $dynamic_css_data['updated_time'] ) ) {
			unset( $dynamic_css_data['updated_time'] );
		}

		update_option( 'generatepress_dynamic_css_data', $dynamic_css_data );

		wp_safe_redirect( admin_url( 'admin.php?page=generate-options&status=imported' ) );
		exit;
	}

	/**
	 * List out our available modules.
	 *
	 * @since 1.7
	 */
	public static function get_modules() {
		return array(
			'Backgrounds' => 'generate_package_backgrounds',
			'Blog' => 'generate_package_blog',
			'Colors' => 'generate_package_colors',
			'Copyright' => 'generate_package_copyright',
			'Elements' => 'generate_package_elements',
			'Disable Elements' => 'generate_package_disable_elements',
			'Hooks' => 'generate_package_hooks',
			'Menu Plus' => 'generate_package_menu_plus',
			'Page Header' => 'generate_package_page_header',
			'Secondary Nav' => 'generate_package_secondary_nav',
			'Sections' => 'generate_package_sections',
			'Spacing' => 'generate_package_spacing',
			'Typography' => 'generate_package_typography',
			'WooCommerce' => 'generate_package_woocommerce',
		);
	}

	/**
	 * List our our set theme mods.
	 *
	 * @since 1.7
	 */
	public static function get_theme_mods() {
		return array(
			'font_body_variants',
			'font_body_category',
			'font_site_title_variants',
			'font_site_title_category',
			'font_site_tagline_variants',
			'font_site_tagline_category',
			'font_navigation_variants',
			'font_navigation_category',
			'font_secondary_navigation_variants',
			'font_secondary_navigation_category',
			'font_buttons_variants',
			'font_buttons_category',
			'font_heading_1_variants',
			'font_heading_1_category',
			'font_heading_2_variants',
			'font_heading_2_category',
			'font_heading_3_variants',
			'font_heading_3_category',
			'font_heading_4_variants',
			'font_heading_4_category',
			'font_heading_5_variants',
			'font_heading_5_category',
			'font_heading_6_variants',
			'font_heading_6_category',
			'font_widget_title_variants',
			'font_widget_title_category',
			'font_footer_variants',
			'font_footer_category',
			'generate_copyright',
		);
	}

	/**
	 * List out our available settings.
	 *
	 * @since 1.7
	 */
	public static function get_settings() {
		return array(
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
	}
}

GeneratePress_Import_Export::get_instance();
