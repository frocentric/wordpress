<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

class GeneratePress_Elements_Metabox {
	/**
	 * Instance.
	 *
	 * @since 1.7
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 * Initiator
	 *
	 * @since 1.7
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Build it.
	 *
	 * @since 1.7
	 */
	public function __construct() {
		add_action( 'admin_body_class', array( $this, 'body_class' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
		add_action( 'save_post', array( $this, 'save' ) );
		add_action( 'wp_ajax_generate_elements_get_location_terms', array( $this, 'get_terms' ) );
		add_action( 'wp_ajax_generate_elements_get_location_posts', array( $this, 'get_posts' ) );
	}

	/**
	 * Add a body class if we're using the Header element type.
	 * We do this so we can hide the Template Tags metabox for other types.
	 *
	 * @since 1.7
	 *
	 * @param string $classes
	 * @return string
	 */
	public function body_class( $classes ) {
		if ( ! get_post_meta( get_the_ID(), '_generate_element_type', true ) ) {
			$classes .= ' no-element-type';
		} elseif ( 'gp_elements' === get_post_type() && 'header' === get_post_meta( get_the_ID(), '_generate_element_type', true ) ) {
			$classes .= ' header-element-type';
		}

		return $classes;
	}

	/**
	 * Enqueue any necessary scripts and styles.
	 *
	 * @since 1.7
	 *
	 * @param string $hook The current page hook.
	 */
	public function scripts( $hook ) {
		if ( in_array( $hook, array( "post.php", "post-new.php" ) ) ) {
			if ( 'gp_elements' == get_post_type() ) {
				$deps = array( 'jquery' );

				if ( function_exists( 'wp_enqueue_code_editor' ) ) {
					$settings = wp_enqueue_code_editor(
						array(
							'type'       => 'application/x-httpd-php',
							'codemirror' => array(
								'indentUnit' => 2,
								'tabSize'    => 2,
							),
						)
					);

					$deps[] = 'code-editor';
				} else {
					$settings = false;
				}

				wp_enqueue_script( 'generate-elements-metabox', plugin_dir_url( __FILE__ ) . 'assets/admin/metabox.js', $deps, GP_PREMIUM_VERSION );
				wp_localize_script( 'generate-elements-metabox', 'elements', array(
					'nonce' => wp_create_nonce( 'generate-elements-location' ),
					'settings' => $settings ? wp_json_encode( $settings ) : false,
					'type' => get_post_meta( get_the_ID(), '_generate_element_type', true ),
					'custom_image' => __( 'Custom Image', 'gp-premium' ),
					'fallback_image' => __( 'Fallback Image', 'gp-premium' ),
					'choose' => __( 'Choose...', 'gp-premium' ),
					'showID' => apply_filters( 'generate_elements_show_object_ids', false ),
				) );

				wp_enqueue_style( 'generate-elements-metabox', plugin_dir_url( __FILE__ ) . 'assets/admin/metabox.css', array(), GP_PREMIUM_VERSION );
				wp_enqueue_style( 'generate-elements-balloon', plugin_dir_url( __FILE__ ) . 'assets/admin/balloon.css', array(), GP_PREMIUM_VERSION );

				wp_enqueue_media();
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker-alpha', plugin_dir_url( __FILE__ ) . 'assets/admin/wp-color-picker-alpha.min.js', array( 'wp-color-picker' ), GP_PREMIUM_VERSION );

				if ( function_exists( 'wp_add_inline_script' ) && function_exists( 'generate_get_default_color_palettes' ) ) {
					// Grab our palette array and turn it into JS
					$palettes = json_encode( generate_get_default_color_palettes() );

					// Add our custom palettes
					// json_encode takes care of escaping
					wp_add_inline_script( 'wp-color-picker', 'jQuery.wp.wpColorPicker.prototype.options.palettes = ' . $palettes . ';' );
				}

				wp_enqueue_style( 'generate-select2', GP_LIBRARY_DIRECTORY_URL . 'select2/select2.min.css', array(), GP_PREMIUM_VERSION );
				wp_enqueue_script( 'generate-select2', GP_LIBRARY_DIRECTORY_URL . 'select2/select2.full.min.js', array( 'jquery', 'generate-elements-metabox' ), GP_PREMIUM_VERSION );
			}
		}
	}

	/**
	 * Register our metabox.
	 *
	 * @since 1.7
	 */
	public function register_metabox() {
		// Title not translated on purpose.
		add_meta_box( 'generate_premium_elements', 'Element', array( $this, 'element_fields' ), 'gp_elements', 'normal' );
		add_meta_box( 'generate_page_hero_template_tags', __( 'Template Tags', 'gp-premium' ), array( $this, 'template_tags' ), 'gp_elements', 'side', 'low' );
		remove_meta_box( 'slugdiv', 'gp_elements', 'normal' );
	}

	/**
	 * Output all of our metabox fields.
	 *
	 * @since 1.7
	 *
	 * @param object $post Our post object.
	 */
	public function element_fields( $post ) {
		wp_nonce_field( basename( __FILE__ ), 'generate_elements_nonce' );

		$type = get_post_meta( get_the_ID(), '_generate_element_type', true );
		$type_chosen = '' !== $type ? true : false;
		$merge = get_post_meta( get_the_ID(), '_generate_site_header_merge', true );
		$conditions_set = get_post_meta( get_the_ID(), '_generate_element_display_conditions', true );
	    ?>
		<div class="choose-element-type-parent" <?php echo $type_chosen ? 'style="display: none;"' : ''; ?>>
			<div class="choose-element-type">
				<h2><?php _e( 'Choose Element Type', 'gp-premium' ); ?></h2>
				<select class="select-type" name="_generate_element_type">
					<option <?php selected( get_post_meta( get_the_ID(), '_generate_element_type', true ), '' ); ?> value=""><?php esc_attr_e( 'Choose...', 'gp-premium' ); ?></option>
					<option <?php selected( get_post_meta( get_the_ID(), '_generate_element_type', true ), 'header' ); ?> value="header"><?php esc_attr_e( 'Header', 'gp-premium' ); ?></option>
					<option <?php selected( get_post_meta( get_the_ID(), '_generate_element_type', true ), 'hook' ); ?> value="hook"><?php esc_attr_e( 'Hook', 'gp-premium' ); ?></option>
					<option <?php selected( get_post_meta( get_the_ID(), '_generate_element_type', true ), 'layout' ); ?> value="layout"><?php esc_attr_e( 'Layout', 'gp-premium' ); ?></option>
				</select>
			</div>
		</div>

		<?php if ( $type_chosen && ! $conditions_set ) : ?>
			<div class="error notice">
				<p><?php _e( 'This element needs a location set within the Display Rules tab in order to display.', 'gp-premium' ); ?></p>
			</div>
		<?php endif; ?>

		<div class="element-settings <?php echo $type_chosen ? $type : 'no-element-type'; ?>">
			<textarea id="generate-element-content" name="_generate_element_content"><?php echo esc_textarea( get_post_meta( get_the_ID(), '_generate_element_content', true ) ); ?></textarea>

			<ul class="element-metabox-tabs">
				<li data-type="header" <?php echo 'header' === $type ? 'class="is-selected" ' : ''; ?>data-tab="hero">
					<a href="#">
						<?php _e( 'Page Hero', 'gp-premium' ); ?>
					</a>
				</li>

				<li data-type="header" data-tab="site-header">
					<a href="#">
						<?php _e( 'Site Header', 'gp-premium' ); ?>
					</a>
				</li>

				<li data-type="hook" <?php echo 'hook' === $type ? 'class="is-selected" ' : ''; ?>data-tab="hook-settings">
					<a href="#">
						<?php _e( 'Settings', 'gp-premium' ); ?>
					</a>
				</li>

				<li data-type="layout" <?php echo 'layout' === $type ? 'class="is-selected" ' : ''; ?>data-tab="sidebars">
					<a href="#">
						<?php _e( 'Sidebar', 'gp-premium' ); ?>
					</a>
				</li>

				<li data-type="layout" data-tab="footer-widgets">
					<a href="#">
						<?php _e( 'Footer', 'gp-premium' ); ?>
					</a>
				</li>

				<?php if ( function_exists( 'generate_disable_elements' ) ) : ?>
					<li data-type="layout" data-tab="disable-elements">
						<a href="#">
							<?php _e( 'Disable Elements', 'gp-premium' ); ?>
						</a>
					</li>
				<?php endif; ?>

				<li data-type="layout" data-tab="content">
					<a href="#">
						<?php _e( 'Content', 'gp-premium' ); ?>
					</a>
				</li>

				<li data-tab="display-rules">
					<a href="#">
						<?php _e( 'Display Rules', 'gp-premium' ); ?>
					</a>
				</li>

				<li data-type="all" data-tab="internal-notes">
					<a href="#">
						<?php _e( 'Internal Notes', 'gp-premium' ); ?>
					</a>
				</li>
			</ul>

			<table class="generate-elements-settings" data-type="hook" data-tab="hook-settings">
				<tbody>
					<tr id="hook-row" class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_hook"><?php _e( 'Hook', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<select id="_generate_hook" name="_generate_hook">
								<?php
								$hooks = self::get_available_hooks();

								foreach ( ( array ) $hooks as $group => $data ) {
									?>
									<optgroup label="<?php echo $data['group']; ?>">
										<?php foreach ( $data['hooks'] as $val ) {
											printf(
												'<option value="%1$s" %2$s>%3$s</option>',
												$val,
												selected( get_post_meta( get_the_ID(), '_generate_hook', true ), $val ),
												str_replace( 'generate_', '', $val )
											);
										} ?>
									</optgroup>
									<?php
								}
								?>
								<optgroup label="<?php esc_attr_e( 'Custom', 'gp-premium' ); ?>">
									<option <?php selected( get_post_meta( get_the_ID(), '_generate_hook', true ), 'custom' ); ?> value="custom"><?php esc_attr_e( 'Custom Hook', 'gp-premium' ); ?></option>
								</optgroup>
							</select>
						</td>
					</tr>

					<tr class="generate-element-row custom-hook-name" <?php echo 'custom' !== get_post_meta( get_the_ID(), '_generate_hook', true ) ? 'style="display: none;"' : ''; ?>>
						<td class="generate-element-row-heading">
							<label for="_generate_custom_hook"><?php _e( 'Custom Hook Name', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input type="text" name="_generate_custom_hook" id="_generate_custom_hook" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_generate_custom_hook', true ) ); ?>" />
						</td>
					</tr>

					<tr class="generate-element-row disable-header-hook" <?php echo 'generate_header' !== get_post_meta( get_the_ID(), '_generate_hook', true ) ? 'style="display: none;"' : ''; ?>>
						<td class="generate-element-row-heading">
							<label for="_generate_hook_disable_site_header"><?php _e( 'Disable Site Header', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input type="checkbox" id="_generate_hook_disable_site_header" name="_generate_hook_disable_site_header" value="true" <?php checked( get_post_meta( get_the_ID(), '_generate_hook_disable_site_header', true ), 'true' ); ?> />
						</td>
					</tr>

					<tr class="generate-element-row disable-footer-hook" <?php echo 'generate_footer' !== get_post_meta( get_the_ID(), '_generate_hook', true ) ? 'style="display: none;"' : ''; ?>>
						<td class="generate-element-row-heading">
							<label for="_generate_hook_disable_site_footer"><?php _e( 'Disable Site Footer', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input type="checkbox" id="_generate_hook_disable_site_footer" name="_generate_hook_disable_site_footer" value="true" <?php checked( get_post_meta( get_the_ID(), '_generate_hook_disable_site_footer', true ), 'true' ); ?> />
						</td>
					</tr>

					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_hook_execute_shortcodes"><?php _e( 'Execute Shortcodes', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input type="checkbox" id="_generate_hook_execute_shortcodes" name="_generate_hook_execute_shortcodes" value="true" <?php checked( get_post_meta( get_the_ID(), '_generate_hook_execute_shortcodes', true ), 'true' ); ?> />
						</td>
					</tr>

					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_hook_execute_php"><?php _e( 'Execute PHP', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<?php if ( ! GeneratePress_Elements_Helper::should_execute_php() ) : ?>
								<?php
								printf(
									/* translators: %s is DISALLOW_FILE_EDIT constant */
									esc_html__( 'Unable to execute PHP as %s is defined.', 'gp-premium' ), '<code>DISALLOW_FILE_EDIT</code>'
								); ?>
							<?php else : ?>
								<input type="checkbox" id="_generate_hook_execute_php" name="_generate_hook_execute_php" value="true" <?php checked( get_post_meta( get_the_ID(), '_generate_hook_execute_php', true ), 'true' ); ?> />
							<?php endif; ?>
						</td>
					</tr>

					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_hook_priority"><?php _e( 'Priority', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input type="number" id="_generate_hook_priority" name="_generate_hook_priority" placeholder="10" value="<?php echo get_post_meta( get_the_ID(), '_generate_hook_priority', true ); ?>" />
						</td>
					</tr>

				</tbody>
			</table>

			<table class="generate-elements-settings" data-type="header" data-tab="hero">
				<tbody>

					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_hero_custom_classes"><?php _e( 'Element Classes', 'gp-premium' ); ?></label>
							<span class="tip" data-balloon="<?php esc_attr_e( 'Add custom classes to the Page Hero element.', 'gp-premium' ); ?>" data-balloon-pos="down">?</span>
						</td>
						<td class="generate-element-row-content">
							<input type="text" name="_generate_hero_custom_classes" id="_generate_hero_custom_classes" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_generate_hero_custom_classes', true ) ); ?>" />
						</td>
					</tr>

					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_hero_container"><?php _e( 'Container', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<select id="_generate_hero_container" name="_generate_hero_container">
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_container', true ), '' ); ?> value=""><?php esc_attr_e( 'Full Width', 'gp-premium' ); ?></option>
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_container', true ), 'contained' ); ?> value="contained"><?php echo esc_attr_x( 'Contained', 'Width', 'gp-premium' ); ?></option>
							</select>
						</td>
					</tr>

					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_hero_inner_container"><?php _e( 'Inner Container', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<select id="_generate_hero_inner_container" name="_generate_hero_inner_container">
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_inner_container', true ), '' ); ?> value=""><?php echo esc_attr_x( 'Contained', 'Width', 'gp-premium' ); ?></option>
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_inner_container', true ), 'full-width' ); ?> value="full-width"><?php esc_attr_e( 'Full Width', 'gp-premium' ); ?></option>
							</select>
						</td>
					</tr>

					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_hero_horizontal_alignment"><?php _e( 'Horizontal Alignment', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<select id="_generate_hero_horizontal_alignment" name="_generate_hero_horizontal_alignment">
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_horizontal_alignment', true ), '' ); ?> value=""><?php esc_attr_e( 'Left', 'gp-premium' ); ?></option>
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_horizontal_alignment', true ), 'center' ); ?> value="center"><?php esc_attr_e( 'Center', 'gp-premium' ); ?></option>
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_horizontal_alignment', true ), 'right' ); ?> value="right"><?php esc_attr_e( 'Right', 'gp-premium' ); ?></option>
							</select>
						</td>
					</tr>

					<tr class="generate-element-row requires-header-merge" <?php echo ! $merge ? 'style="display: none;"' : ''; ?>>
						<td class="generate-element-row-heading">
							<label for="_generate_hero_full_screen"><?php _e( 'Full Screen', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input type="checkbox" name="_generate_hero_full_screen" id="_generate_hero_full_screen" value="true" <?php checked( get_post_meta( get_the_ID(), '_generate_hero_full_screen', true ), 'true' ); ?> />
						</td>
					</tr>

					<tr class="generate-element-row requires-full-screen" <?php echo ! $merge || ! get_post_meta( get_the_ID(), '_generate_hero_full_screen', true ) ? 'style="display: none;"' : ''; ?>>
						<td class="generate-element-row-heading">
							<label for="_generate_hero_vertical_alignment"><?php _e( 'Vertical Alignment', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<select id="_generate_hero_vertical_alignment" name="_generate_hero_vertical_alignment">
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_vertical_alignment', true ), '' ); ?> value=""><?php esc_attr_e( 'Top', 'gp-premium' ); ?></option>
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_vertical_alignment', true ), 'center' ); ?> value="center"><?php esc_attr_e( 'Center', 'gp-premium' ); ?></option>
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_vertical_alignment', true ), 'bottom' ); ?> value="bottom"><?php esc_attr_e( 'Bottom', 'gp-premium' ); ?></option>
							</select>
						</td>
					</tr>

					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_hero_padding"><?php _e( 'Padding', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<div class="responsive-controls">
								<a href="#" class="is-selected" data-control="desktop"><span class="dashicons dashicons-desktop"></span></a>
								<a href="#" data-control="mobile"><span class="dashicons dashicons-smartphone"></span></a>
							</div>

							<div class="padding-container desktop">
								<div class="padding-element">
									<input type="number" id="_generate_hero_padding_top" name="_generate_hero_padding_top" value="<?php echo get_post_meta( get_the_ID(), '_generate_hero_padding_top', true ); ?>" /><select id="_generate_hero_padding_top_unit" name="_generate_hero_padding_top_unit">
										<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_padding_top_unit', true ), '' ); ?> value="">px</option>
										<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_padding_top_unit', true ), '%' ); ?> value="%">%</option>
									</select>

									<span><?php esc_html_e( 'Top', 'gp-premium' ); ?></span>
								</div>

								<div class="padding-element">
									<input type="number" id="_generate_hero_padding_right" name="_generate_hero_padding_right" value="<?php echo get_post_meta( get_the_ID(), '_generate_hero_padding_right', true ); ?>" /><select id="_generate_hero_padding_right_unit" name="_generate_hero_padding_right_unit">
										<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_padding_right_unit', true ), '' ); ?> value="">px</option>
										<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_padding_right_unit', true ), '%' ); ?> value="%">%</option>
									</select>

									<span><?php esc_html_e( 'Right', 'gp-premium' ); ?></span>
								</div>

								<div class="padding-element">
									<input type="number" id="_generate_hero_padding_bottom" name="_generate_hero_padding_bottom" value="<?php echo get_post_meta( get_the_ID(), '_generate_hero_padding_bottom', true ); ?>" /><select id="_generate_hero_padding_bottom_unit" name="_generate_hero_padding_bottom_unit">
										<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_padding_bottom_unit', true ), '' ); ?> value="">px</option>
										<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_padding_bottom_unit', true ), '%' ); ?> value="%">%</option>
									</select>

									<span><?php esc_html_e( 'Bottom', 'gp-premium' ); ?></span>
								</div>

								<div class="padding-element">
									<input type="number" id="_generate_hero_padding_left" name="_generate_hero_padding_left" value="<?php echo get_post_meta( get_the_ID(), '_generate_hero_padding_left', true ); ?>" /><select id="_generate_hero_padding_left_unit" name="_generate_hero_padding_left_unit">
										<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_padding_left_unit', true ), '' ); ?> value="">px</option>
										<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_padding_left_unit', true ), '%' ); ?> value="%">%</option>
									</select>

									<span><?php esc_html_e( 'Left', 'gp-premium' ); ?></span>
								</div>
							</div>

							<div class="padding-container mobile" style="display: none;">
								<div class="padding-element">
									<input type="number" id="_generate_hero_padding_top_mobile" name="_generate_hero_padding_top_mobile" value="<?php echo get_post_meta( get_the_ID(), '_generate_hero_padding_top_mobile', true ); ?>" /><select id="_generate_hero_padding_top_unit_mobile" name="_generate_hero_padding_top_unit_mobile">
										<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_padding_top_unit_mobile', true ), '' ); ?> value="">px</option>
										<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_padding_top_unit_mobile', true ), '%' ); ?> value="%">%</option>
									</select>

									<span><?php esc_html_e( 'Top', 'gp-premium' ); ?></span>
								</div>

								<div class="padding-element">
									<input type="number" id="_generate_hero_padding_right_mobile" name="_generate_hero_padding_right_mobile" value="<?php echo get_post_meta( get_the_ID(), '_generate_hero_padding_right_mobile', true ); ?>" /><select id="_generate_hero_padding_right_unit_mobile" name="_generate_hero_padding_right_unit_mobile">
										<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_padding_right_unit_mobile', true ), '' ); ?> value="">px</option>
										<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_padding_right_unit_mobile', true ), '%' ); ?> value="%">%</option>
									</select>

									<span><?php esc_html_e( 'Right', 'gp-premium' ); ?></span>
								</div>

								<div class="padding-element">
									<input type="number" id="_generate_hero_padding_bottom_mobile" name="_generate_hero_padding_bottom_mobile" value="<?php echo get_post_meta( get_the_ID(), '_generate_hero_padding_bottom_mobile', true ); ?>" /><select id="_generate_hero_padding_bottom_unit_mobile" name="_generate_hero_padding_bottom_unit_mobile">
										<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_padding_bottom_unit_mobile', true ), '' ); ?> value="">px</option>
										<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_padding_bottom_unit_mobile', true ), '%' ); ?> value="%">%</option>
									</select>

									<span><?php esc_html_e( 'Bottom', 'gp-premium' ); ?></span>
								</div>

								<div class="padding-element">
									<input type="number" id="_generate_hero_padding_left_mobile" name="_generate_hero_padding_left_mobile" value="<?php echo get_post_meta( get_the_ID(), '_generate_hero_padding_left_mobile', true ); ?>" /><select id="_generate_hero_padding_left_unit_mobile" name="_generate_hero_padding_left_unit_mobile">
										<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_padding_left_unit_mobile', true ), '' ); ?> value="">px</option>
										<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_padding_left_unit_mobile', true ), '%' ); ?> value="%">%</option>
									</select>

									<span><?php esc_html_e( 'Left', 'gp-premium' ); ?></span>
								</div>
							</div>
						</td>
					</tr>

					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_hero_background_image"><?php _e( 'Background Image', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<select id="_generate_hero_background_image" name="_generate_hero_background_image">
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_background_image', true ), '' ); ?> value=""><?php esc_attr_e( 'No Background Image', 'gp-premium' ); ?></option>
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_background_image', true ), 'featured-image' ); ?> value="featured-image"><?php esc_attr_e( 'Featured Image', 'gp-premium' ); ?></option>
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_background_image', true ), 'custom-image' ); ?> value="custom-image"><?php esc_attr_e( 'Custom Image', 'gp-premium' ); ?></option>
							</select>
						</td>
					</tr>

					<?php
						$background_image = get_post_meta( get_the_ID(), '_generate_hero_background_image', true );

						if ( 'custom-image' === $background_image ) {
							$image_text = __( 'Custom Image', 'gp-premium' );
						} else {
							$image_text = __( 'Fallback Image', 'gp-premium' );
						}
					?>

					<tr class="generate-element-row requires-background-image" <?php echo '' === $background_image ? 'style="display: none;"' : ''; ?>>
						<td class="generate-element-row-heading">
							<label for="_generate_hero_background_link_color_hover"><span class="image-text"><?php echo $image_text; ?></span></label>
						</td>
						<td class="generate-element-row-content">
							<div class="change-featured-image" <?php echo ! has_post_thumbnail() ? 'style="display: none;"' : ''; ?>>
								<div class="image-preview">
									<?php the_post_thumbnail( 'thumbnail', array( 9999, 50 ) ); ?>
								</div>
								<?php
								printf(
									'<a class="button" href="#">%s</a>',
									esc_html__( 'Change', 'gp-premium' )
								);

								printf(
									' <a class="button remove-image" href="#">%s</a>',
									esc_html__( 'Remove', 'gp-premium' )
								);
								?>
							</div>

							<div class="set-featured-image" <?php echo has_post_thumbnail() ? 'style="display: none;"' : ''; ?>>
								<?php
								printf(
									'<a class="button" href="#">%s</a>',
									sprintf(
										esc_html__( 'Upload %s', 'gp-premium' ),
										'<span class="image-text">' . $image_text . '</span>'
									)
								);
								?>
							</div>
						</td>
					</tr>

					<tr class="generate-element-row requires-background-image" <?php echo ! $background_image ? 'style="display: none;"' : ''; ?>>
						<td class="generate-element-row-heading">
							<label for="_generate_hero_background_position"><?php _e( 'Background Position', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<select id="_generate_hero_background_position" name="_generate_hero_background_position">
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_background_position', true ), '' ); ?> value="">left top</option>
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_background_position', true ), 'left center' ); ?> value="left center" <?php echo get_post_meta( get_the_ID(), '_generate_hero_background_parallax', true ) ? 'disabled' : ''; ?>>left center</option>
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_background_position', true ), 'left bottom' ); ?> value="left bottom" <?php echo get_post_meta( get_the_ID(), '_generate_hero_background_parallax', true ) ? 'disabled' : ''; ?>>left bottom</option>
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_background_position', true ), 'right top' ); ?> value="right top">right top</option>
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_background_position', true ), 'right center' ); ?> value="right center" <?php echo get_post_meta( get_the_ID(), '_generate_hero_background_parallax', true ) ? 'disabled' : ''; ?>>right center</option>
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_background_position', true ), 'right bottom' ); ?> value="right bottom" <?php echo get_post_meta( get_the_ID(), '_generate_hero_background_parallax', true ) ? 'disabled' : ''; ?>>right bottom</option>
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_background_position', true ), 'center top' ); ?> value="center top">center top</option>
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_background_position', true ), 'center center' ); ?> value="center center" <?php echo get_post_meta( get_the_ID(), '_generate_hero_background_parallax', true ) ? 'disabled' : ''; ?>>center center</option>
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_hero_background_position', true ), 'center bottom' ); ?> value="center bottom" <?php echo get_post_meta( get_the_ID(), '_generate_hero_background_parallax', true ) ? 'disabled' : ''; ?>>center bottom</option>
							</select>
						</td>
					</tr>

					<tr class="generate-element-row requires-background-image" <?php echo ! $background_image ? 'style="display: none;"' : ''; ?>>
						<td class="generate-element-row-heading">
							<label for="_generate_hero_background_parallax"><?php _e( 'Parallax', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input type="checkbox" name="_generate_hero_background_parallax" id="_generate_hero_background_parallax" value="true" <?php checked( get_post_meta( get_the_ID(), '_generate_hero_background_parallax', true ), 'true' ); ?> />
						</td>
					</tr>

					<tr class="generate-element-row requires-background-image" <?php echo ! $background_image ? 'style="display: none;"' : ''; ?>>
						<td class="generate-element-row-heading">
							<label for="_generate_hero_disable_featured_image"><?php _e( 'Disable Featured Image', 'gp-premium' ); ?></label>
							<span class="tip" data-balloon="<?php esc_attr_e( 'Disable the featured image on posts with this hero area.', 'gp-premium' ); ?>" data-balloon-pos="down">?</span>
						</td>
						<td class="generate-element-row-content">
							<input type="checkbox" name="_generate_hero_disable_featured_image" id="_generate_hero_disable_featured_image" value="true" <?php checked( get_post_meta( get_the_ID(), '_generate_hero_disable_featured_image', true ), 'true' ); ?> />
						</td>
					</tr>

					<tr class="generate-element-row requires-background-image" <?php echo ! $background_image ? 'style="display: none;"' : ''; ?>>
						<td class="generate-element-row-heading">
							<label for="_generate_hero_background_overlay"><?php _e( 'Background Overlay', 'gp-premium' ); ?></label>
							<span class="tip" data-balloon="<?php esc_attr_e( 'Use the background color as a background overlay.', 'gp-premium' ); ?>" data-balloon-pos="down">?</span>
						</td>
						<td class="generate-element-row-content">
							<input type="checkbox" name="_generate_hero_background_overlay" id="_generate_hero_background_overlay" value="true" <?php checked( get_post_meta( get_the_ID(), '_generate_hero_background_overlay', true ), 'true' ); ?> />
						</td>
					</tr>

					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_hero_background_color"><?php _e( 'Background Color', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input class="color-picker" data-alpha="true" type="text" name="_generate_hero_background_color" id="_generate_hero_background_color" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_generate_hero_background_color', true ) ); ?>" />
						</td>
					</tr>

					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_hero_text_color"><?php _e( 'Text Color', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input class="color-picker" type="text" name="_generate_hero_text_color" id="_generate_hero_text_color" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_generate_hero_text_color', true ) ); ?>" />
						</td>
					</tr>

					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_hero_link_color"><?php _e( 'Link Color', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input class="color-picker" type="text" name="_generate_hero_link_color" id="_generate_hero_link_color" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_generate_hero_link_color', true ) ); ?>" />
						</td>
					</tr>

					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_hero_background_link_color_hover"><?php _e( 'Link Color Hover', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input class="color-picker" type="text" name="_generate_hero_background_link_color_hover" id="_generate_hero_background_link_color_hover" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_generate_hero_background_link_color_hover', true ) ); ?>" />
						</td>
					</tr>

				</tbody>
			</table>

			<table class="generate-elements-settings" data-type="header" data-tab="site-header">
				<tbody>
					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_site_header_merge"><?php _e( 'Merge with Content', 'gp-premium' ); ?></label>
							<span class="tip" data-balloon="<?php esc_attr_e( 'Place your site header on top of the element below it.', 'gp-premium' ); ?>" data-balloon-pos="down">?</span>
						</td>
						<td class="generate-element-row-content">
							<select id="_generate_site_header_merge" name="_generate_site_header_merge">
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_site_header_merge', true ), '' ); ?> value=""><?php esc_attr_e( 'No Merge', 'gp-premium' ); ?></option>
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_site_header_merge', true ), 'merge' ); ?> value="merge"><?php esc_attr_e( 'Merge', 'gp-premium' ); ?></option>
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_site_header_merge', true ), 'merge-desktop' ); ?> value="merge-desktop"><?php esc_attr_e( 'Merge on Desktop Only', 'gp-premium' ); ?></option>
							</select>
						</td>
					</tr>

					<tr class="generate-element-row requires-header-merge" <?php echo ! $merge ? 'style="display: none;"' : ''; ?>>
						<td class="generate-element-row-heading">
							<label for="_generate_site_header_height"><?php _e( 'Offset Site Header Height', 'gp-premium' ); ?></label>
							<span class="tip" data-balloon="<?php esc_attr_e( 'Add to the top padding of your Page Hero to prevent overlapping.', 'gp-premium' ); ?>" data-balloon-pos="down">?</span>
						</td>
						<td class="generate-element-row-content">

							<div class="responsive-controls single-responsive-value">
								<a href="#" class="is-selected" data-control="desktop"><span class="dashicons dashicons-desktop"></span></a>
								<a href="#" data-control="mobile"><span class="dashicons dashicons-smartphone"></span></a>
							</div>

							<div class="padding-container single-value-padding-container desktop">
								<div class="padding-element">
									<input type="number" id="_generate_site_header_height" name="_generate_site_header_height" value="<?php echo get_post_meta( get_the_ID(), '_generate_site_header_height', true ); ?>" />
									<span class="unit">px</span>
								</div>
							</div>

							<div class="padding-container single-value-padding-container mobile" style="display: none;">
								<div class="padding-element">
									<input type="number" id="_generate_site_header_height_mobile" name="_generate_site_header_height_mobile" value="<?php echo get_post_meta( get_the_ID(), '_generate_site_header_height_mobile', true ); ?>" />
									<span class="unit">px</span>
								</div>
							</div>
						</td>
					</tr>

					<tr class="generate-element-row requires-header-merge" <?php echo ! $merge ? 'style="display: none;"' : ''; ?>>
						<td class="generate-element-row-heading">
							<label for="_generate_site_header_background_color"><?php _e( 'Header Background', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input class="color-picker" data-alpha="true" type="text" name="_generate_site_header_background_color" id="_generate_site_header_background_color" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_generate_site_header_background_color', true ) ); ?>" />
						</td>
					</tr>

					<?php if ( GeneratePress_Elements_Helper::does_option_exist( 'site-title' ) ) : ?>
						<tr class="generate-element-row requires-header-merge" <?php echo ! $merge ? 'style="display: none;"' : ''; ?>>
							<td class="generate-element-row-heading">
								<label for="_generate_site_header_title_color"><?php _e( 'Site Title', 'gp-premium' ); ?></label>
							</td>
							<td class="generate-element-row-content">
								<input class="color-picker" type="text" name="_generate_site_header_title_color" id="_generate_site_header_title_color" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_generate_site_header_title_color', true ) ); ?>" />
							</td>
						</tr>
					<?php endif; ?>

					<?php if ( GeneratePress_Elements_Helper::does_option_exist( 'site-tagline' ) ) : ?>
						<tr class="generate-element-row requires-header-merge" <?php echo ! $merge ? 'style="display: none;"' : ''; ?>>
							<td class="generate-element-row-heading">
								<label for="_generate_site_header_tagline_color"><?php _e( 'Site Tagline', 'gp-premium' ); ?></label>
							</td>
							<td class="generate-element-row-content">
								<input class="color-picker" type="text" name="_generate_site_header_tagline_color" id="_generate_site_header_tagline_color" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_generate_site_header_tagline_color', true ) ); ?>" />
							</td>
						</tr>
					<?php endif; ?>

					<?php if ( GeneratePress_Elements_Helper::does_option_exist( 'site-logo' ) ) : ?>
						<tr class="generate-element-row">
							<td class="generate-element-row-heading">
								<label for="_generate_site_header_merge"><?php _e( 'Site Logo', 'gp-premium' ); ?></label>
							</td>
							<td class="generate-element-row-content">
								<div class="media-container">
									<div class="gp-media-preview">
										<?php
											$site_logo_id = get_post_meta( get_the_ID(), '_generate_site_logo', true );
											$site_logo_url = wp_get_attachment_url( absint( $site_logo_id ) );

											if ( $site_logo_url ) {
												echo '<img src="' . esc_url( $site_logo_url ) . '" alt="" height="30" />';
											}
										?>
									</div>
									<input type="button" data-title="<?php esc_attr_e( 'Site Logo', 'gp-premium' ); ?>" data-preview="true" class="button generate-upload-file" value="<?php esc_attr_e( 'Upload', 'gp-premium' ); ?>" />
									<input type="button" class="button remove-field" value="<?php esc_attr_e( 'Remove', 'gp-premium' ); ?>" <?php echo ! $site_logo_id ? 'style="display: none;"' : ''; ?> />

									<input type="hidden" class="media-field" name="_generate_site_logo" value="<?php echo esc_attr( $site_logo_id ); ?>" />
								</div>
							</td>
						</tr>
					<?php endif; ?>

					<?php if ( GeneratePress_Elements_Helper::does_option_exist( 'retina-logo' ) ) : ?>
						<tr class="generate-element-row">
							<td class="generate-element-row-heading">
								<label for="_generate_retina_logo"><?php _e( 'Retina Logo', 'gp-premium' ); ?></label>
							</td>
							<td class="generate-element-row-content">
								<div class="media-container">
									<div class="gp-media-preview">
										<?php
											$retina_site_logo_id = get_post_meta( get_the_ID(), '_generate_retina_logo', true );
											$retina_site_logo_url = wp_get_attachment_url( absint( $retina_site_logo_id ) );

											if ( $retina_site_logo_url ) {
												echo '<img src="' . esc_url( $retina_site_logo_url ) . '" alt="" height="30" />';
											}
										?>
									</div>
									<input type="button" data-title="<?php esc_attr_e( 'Retina Logo', 'gp-premium' ); ?>" data-preview="true" class="button generate-upload-file" value="<?php esc_attr_e( 'Upload', 'gp-premium' ); ?>" />
									<input type="button" class="button remove-field" value="<?php esc_attr_e( 'Remove', 'gp-premium' ); ?>" <?php echo ! $retina_site_logo_id ? 'style="display: none;"' : ''; ?> />

									<input type="hidden" class="media-field" name="_generate_retina_logo" value="<?php echo esc_attr( $retina_site_logo_id ); ?>" />
								</div>
							</td>
						</tr>
					<?php endif; ?>

					<?php if ( GeneratePress_Elements_Helper::does_option_exist( 'navigation-logo' ) ) : ?>
						<tr class="generate-element-row">
							<td class="generate-element-row-heading">
								<label for="_generate_navigation_logo"><?php _e( 'Navigation Logo', 'gp-premium' ); ?></label>
							</td>
							<td class="generate-element-row-content">
								<div class="media-container">
									<div class="gp-media-preview">
										<?php
											$navigation_logo_id = get_post_meta( get_the_ID(), '_generate_navigation_logo', true );
											$navigation_logo_url = wp_get_attachment_url( absint( $navigation_logo_id ) );

											if ( $navigation_logo_url ) {
												echo '<img src="' . esc_url( $navigation_logo_url ) . '" alt="" height="30" />';
											}
										?>
									</div>
									<input type="button" data-title="<?php esc_attr_e( 'Retina Logo', 'gp-premium' ); ?>" data-preview="true" class="button generate-upload-file" value="<?php esc_attr_e( 'Upload', 'gp-premium' ); ?>" />
									<input type="button" class="button remove-field" value="<?php esc_attr_e( 'Remove', 'gp-premium' ); ?>" <?php echo ! $navigation_logo_id ? 'style="display: none;"' : ''; ?> />

									<input type="hidden" class="media-field" name="_generate_navigation_logo" value="<?php echo esc_attr( $navigation_logo_id ); ?>" />
								</div>
							</td>
						</tr>
					<?php endif; ?>

					<?php if ( GeneratePress_Elements_Helper::does_option_exist( 'mobile-logo' ) ) : ?>
						<tr class="generate-element-row">
							<td class="generate-element-row-heading">
								<label for="_generate_mobile_logo"><?php _e( 'Mobile Header Logo', 'gp-premium' ); ?></label>
							</td>
							<td class="generate-element-row-content">
								<div class="media-container">
									<div class="gp-media-preview">
										<?php
											$mobile_logo_id = get_post_meta( get_the_ID(), '_generate_mobile_logo', true );
											$mobile_logo_url = wp_get_attachment_url( absint( $mobile_logo_id ) );

											if ( $mobile_logo_url ) {
												echo '<img src="' . esc_url( $mobile_logo_url ) . '" alt="" height="30" />';
											}
										?>
									</div>
									<input type="button" data-title="<?php esc_attr_e( 'Retina Logo', 'gp-premium' ); ?>" data-preview="true" class="button generate-upload-file" value="<?php esc_attr_e( 'Upload', 'gp-premium' ); ?>" />
									<input type="button" class="button remove-field" value="<?php esc_attr_e( 'Remove', 'gp-premium' ); ?>" <?php echo ! $mobile_logo_id ? 'style="display: none;"' : ''; ?> />

									<input type="hidden" class="media-field" name="_generate_mobile_logo" value="<?php echo esc_attr( $mobile_logo_id ); ?>" />
								</div>
							</td>
						</tr>
					<?php endif; ?>

					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_navigation_location"><?php _e( 'Navigation Location', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<select id="_generate_navigation_location" name="_generate_navigation_location">
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_navigation_location', true ), '' ); ?> value=""><?php esc_attr_e( 'Default', 'gp-premium' ); ?></option>
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_navigation_location', true ), 'nav-below-header' ); ?> value="nav-below-header"><?php esc_attr_e( 'Below Header', 'gp-premium' ); ?></option>
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_navigation_location', true ), 'nav-above-header' ); ?> value="nav-above-header"><?php esc_attr_e( 'Above Header', 'gp-premium' ); ?></option>
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_navigation_location', true ), 'nav-float-right' ); ?> value="nav-float-right"><?php esc_attr_e( 'Float Right', 'gp-premium' ); ?></option>
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_navigation_location', true ), 'nav-float-left' ); ?> value="nav-float-left"><?php esc_attr_e( 'Float Left', 'gp-premium' ); ?></option>
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_navigation_location', true ), 'nav-left-sidebar' ); ?> value="nav-left-sidebar"><?php esc_attr_e( 'Left Sidebar', 'gp-premium' ); ?></option>
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_navigation_location', true ), 'nav-right-sidebar' ); ?> value="nav-right-sidebar"><?php esc_attr_e( 'Right Sidebar', 'gp-premium' ); ?></option>
								<option <?php selected( get_post_meta( get_the_ID(), '_generate_navigation_location', true ), 'no-navigation' ); ?> value="no-navigation"><?php esc_attr_e( 'No Navigation', 'gp-premium' ); ?></option>
							</select>
						</td>
					</tr>

					<tr class="generate-element-row requires-header-merge" <?php echo ! $merge ? 'style="display: none;"' : ''; ?>>
						<td class="generate-element-row-heading">
							<label for="_generate_navigation_colors"><?php _e( 'Navigation Colors', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input type="checkbox" name="_generate_navigation_colors" id="_generate_navigation_colors" value="true" <?php checked( get_post_meta( get_the_ID(), '_generate_navigation_colors', true ), 'true' ); ?> />
						</td>
					</tr>

					<tr class="generate-element-row requires-navigation-colors" <?php echo ! $merge || ! get_post_meta( get_the_ID(), '_generate_navigation_colors', true ) ? 'style="display: none;"' : ''; ?>>
						<td class="generate-element-row-heading">
							<label for="_generate_navigation_background_color"><?php _e( 'Navigation Background', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input class="color-picker" data-alpha="true" type="text" name="_generate_navigation_background_color" id="_generate_navigation_background_color" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_generate_navigation_background_color', true ) ); ?>" />
						</td>
					</tr>

					<tr class="generate-element-row requires-navigation-colors" <?php echo ! $merge || ! get_post_meta( get_the_ID(), '_generate_navigation_colors', true ) ? 'style="display: none;"' : ''; ?>>
						<td class="generate-element-row-heading">
							<label for="_generate_navigation_text_color"><?php _e( 'Navigation Text', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input class="color-picker" type="text" name="_generate_navigation_text_color" id="_generate_navigation_text_color" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_generate_navigation_text_color', true ) ); ?>" />
						</td>
					</tr>

					<tr class="generate-element-row requires-navigation-colors" <?php echo ! $merge || ! get_post_meta( get_the_ID(), '_generate_navigation_colors', true ) ? 'style="display: none;"' : ''; ?>>
						<td class="generate-element-row-heading">
							<label for="_generate_navigation_background_color_hover"><?php _e( 'Navigation Background Hover', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input class="color-picker" data-alpha="true" type="text" name="_generate_navigation_background_color_hover" id="_generate_navigation_background_color_hover" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_generate_navigation_background_color_hover', true ) ); ?>" />
						</td>
					</tr>

					<tr class="generate-element-row requires-navigation-colors" <?php echo ! $merge || ! get_post_meta( get_the_ID(), '_generate_navigation_colors', true ) ? 'style="display: none;"' : ''; ?>>
						<td class="generate-element-row-heading">
							<label for="_generate_navigation_text_color_hover"><?php _e( 'Navigation Text Hover', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input class="color-picker" type="text" name="_generate_navigation_text_color_hover" id="_generate_navigation_text_color_hover" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_generate_navigation_text_color_hover', true ) ); ?>" />
						</td>
					</tr>

					<tr class="generate-element-row requires-navigation-colors" <?php echo ! $merge || ! get_post_meta( get_the_ID(), '_generate_navigation_colors', true ) ? 'style="display: none;"' : ''; ?>>
						<td class="generate-element-row-heading">
							<label for="_generate_navigation_background_color_current"><?php _e( 'Navigation Background Current', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input class="color-picker" data-alpha="true" type="text" name="_generate_navigation_background_color_current" id="_generate_navigation_background_color_current" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_generate_navigation_background_color_current', true ) ); ?>" />
						</td>
					</tr>

					<tr class="generate-element-row requires-navigation-colors" <?php echo ! $merge || ! get_post_meta( get_the_ID(), '_generate_navigation_colors', true ) ? 'style="display: none;"' : ''; ?>>
						<td class="generate-element-row-heading">
							<label for="_generate_navigation_text_color_current"><?php _e( 'Navigation Text Current', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input class="color-picker" type="text" name="_generate_navigation_text_color_current" id="_generate_navigation_text_color_current" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_generate_navigation_text_color_current', true ) ); ?>" />
						</td>
					</tr>
				</tbody>
			</table>

			<table class="generate-elements-settings" data-type="layout" data-tab="sidebars">
				<tbody>
					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_sidebar_layout"><?php _e( 'Sidebar Layout', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<div class="layout-radio-item">
								<label for="default-sidebar-layout">
									<input type="radio" name="_generate_sidebar_layout" id="default-sidebar-layout" <?php checked( get_post_meta( get_the_ID(), '_generate_sidebar_layout', true ), '' ); ?> value="">
									<?php esc_html_e( 'Default', 'gp-premium' );?>
								</label>
							</div>

							<div class="layout-radio-item">
								<label for="right-sidebar-layout" title="<?php esc_attr_e( 'Right Sidebar', 'gp-premium' );?>">
									<input type="radio" name="_generate_sidebar_layout" id="right-sidebar-layout" <?php checked( get_post_meta( get_the_ID(), '_generate_sidebar_layout', true ), 'right-sidebar' ); ?> value="right-sidebar">
									<?php esc_html_e( 'Content', 'gp-premium' );?> / <strong><?php echo esc_html_x( 'Sidebar', 'Short name for meta box', 'gp-premium' ); ?></strong>
								</label>
							</div>

							<div class="layout-radio-item">
								<label for="left-sidebar-layout" title="<?php esc_attr_e( 'Left Sidebar', 'gp-premium' );?>">
									<input type="radio" name="_generate_sidebar_layout" id="left-sidebar-layout" <?php checked( get_post_meta( get_the_ID(), '_generate_sidebar_layout', true ), 'left-sidebar' ); ?> value="left-sidebar">
									<strong><?php echo esc_html_x( 'Sidebar', 'Short name for meta box', 'gp-premium' ); ?></strong> / <?php esc_html_e( 'Content', 'gp-premium' );?>
								</label>
							</div>

							<div class="layout-radio-item">
								<label for="no-sidebar-layout" title="<?php esc_attr_e( 'No Sidebars', 'gp-premium' );?>">
									<input type="radio" name="_generate_sidebar_layout" id="no-sidebar-layout" <?php checked( get_post_meta( get_the_ID(), '_generate_sidebar_layout', true ), 'no-sidebar' ); ?> value="no-sidebar">
									<?php esc_html_e( 'Content (no sidebars)', 'gp-premium' );?>
								</label>
							</div>

							<div class="layout-radio-item">
								<label for="both-sidebars-layout" title="<?php esc_attr_e( 'Both Sidebars', 'gp-premium' );?>">
									<input type="radio" name="_generate_sidebar_layout" id="both-sidebars-layout" <?php checked( get_post_meta( get_the_ID(), '_generate_sidebar_layout', true ), 'both-sidebars' ); ?> value="both-sidebars">
									<strong><?php echo esc_html_x( 'Sidebar', 'Short name for meta box', 'gp-premium' ); ?></strong> / <?php esc_html_e( 'Content', 'gp-premium' );?> / <strong><?php echo esc_html_x( 'Sidebar', 'Short name for meta box', 'gp-premium' ); ?></strong>
								</label>
							</div>

							<div class="layout-radio-item">
								<label for="both-sidebars-left-layout" title="<?php esc_attr_e( 'Both Sidebars on Left', 'gp-premium' );?>">
									<input type="radio" name="_generate_sidebar_layout" id="both-sidebars-left-layout" <?php checked( get_post_meta( get_the_ID(), '_generate_sidebar_layout', true ), 'both-left' ); ?> value="both-left">
									<strong><?php echo esc_html_x( 'Sidebar', 'Short name for meta box', 'gp-premium' ); ?></strong> / <strong><?php echo esc_html_x( 'Sidebar', 'Short name for meta box', 'gp-premium' ); ?></strong> / <?php esc_html_e( 'Content', 'gp-premium' );?>
								</label>
							</div>

							<div class="layout-radio-item">
								<label for="both-sidebars-right-layout" title="<?php esc_attr_e( 'Both Sidebars on Right', 'gp-premium' );?>">
									<input type="radio" name="_generate_sidebar_layout" id="both-sidebars-right-layout" <?php checked( get_post_meta( get_the_ID(), '_generate_sidebar_layout', true ), 'both-right' ); ?> value="both-right">
									<?php esc_html_e( 'Content', 'gp-premium' );?> / <strong><?php echo esc_html_x( 'Sidebar', 'Short name for meta box', 'gp-premium' ); ?></strong> / <strong><?php echo esc_html_x( 'Sidebar', 'Short name for meta box', 'gp-premium' ); ?></strong>
								</label>
							</div>
						</td>
					</tr>
				</tbody>
			</table>

			<table class="generate-elements-settings" data-type="layout" data-tab="footer-widgets">
				<tbody>
					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_footer_widgets"><?php _e( 'Footer Widgets', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<div class="layout-radio-item">
								<label for="default-footer-widgets">
									<input type="radio" name="_generate_footer_widgets" id="default-footer-widgets" <?php checked( get_post_meta( get_the_ID(), '_generate_footer_widgets', true ), '' ); ?> value="">
									<?php esc_html_e( 'Default', 'gp-premium' );?>
								</label>
							</div>

							<div class="layout-radio-item">
								<label for="footer-widget-0">
									<input type="radio" name="_generate_footer_widgets" id="footer-widget-0" <?php checked( get_post_meta( get_the_ID(), '_generate_footer_widgets', true ), 'no-widgets' ); ?> value="no-widgets">
									<?php esc_attr_e( '0 Widgets', 'gp-premium' ); ?>
								</label>
							</div>

							<div class="layout-radio-item">
								<label for="footer-widget-1">
									<input type="radio" name="_generate_footer_widgets" id="footer-widget-1" <?php checked( get_post_meta( get_the_ID(), '_generate_footer_widgets', true ), '1' ); ?> value="1">
									<?php esc_attr_e( '1 Widget', 'gp-premium' ); ?>
								</label>
							</div>

							<div class="layout-radio-item">
								<label for="footer-widget-2">
									<input type="radio" name="_generate_footer_widgets" id="footer-widget-2" <?php checked( get_post_meta( get_the_ID(), '_generate_footer_widgets', true ), '2' ); ?> value="2">
									<?php esc_attr_e( '2 Widgets', 'gp-premium' ); ?>
								</label>
							</div>

							<div class="layout-radio-item">
								<label for="footer-widget-3">
									<input type="radio" name="_generate_footer_widgets" id="footer-widget-3" <?php checked( get_post_meta( get_the_ID(), '_generate_footer_widgets', true ), '3' ); ?> value="3">
									<?php esc_attr_e( '3 Widgets', 'gp-premium' ); ?>
								</label>
							</div>

							<div class="layout-radio-item">
								<label for="footer-widget-4">
									<input type="radio" name="_generate_footer_widgets" id="footer-widget-4" <?php checked( get_post_meta( get_the_ID(), '_generate_footer_widgets', true ), '4' ); ?> value="4">
									<?php esc_attr_e( '4 Widgets', 'gp-premium' ); ?>
								</label>
							</div>

							<div class="layout-radio-item">
								<label for="footer-widget-5">
									<input type="radio" name="_generate_footer_widgets" id="footer-widget-5" <?php checked( get_post_meta( get_the_ID(), '_generate_footer_widgets', true ), '5' ); ?> value="5">
									<?php esc_attr_e( '5 Widgets', 'gp-premium' ); ?>
								</label>
							</div>
						</td>
					</tr>
				</tbody>
			</table>

			<table class="generate-elements-settings" data-tab="disable-elements" data-type="layout">
				<tbody>
					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_disable_site_header"><?php _e( 'Site Header', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input type="checkbox" name="_generate_disable_site_header" id="_generate_disable_site_header" value="true" <?php checked( get_post_meta( get_the_ID(), '_generate_disable_site_header', true ), 'true' ); ?> />
						</td>
					</tr>

					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_disable_top_bar"><?php _e( 'Top Bar', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input type="checkbox" name="_generate_disable_top_bar" id="_generate_disable_top_bar" value="true" <?php checked( get_post_meta( get_the_ID(), '_generate_disable_top_bar', true ), 'true' ); ?> />
						</td>
					</tr>

					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_disable_primary_navigation"><?php _e( 'Primary Navigation', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input type="checkbox" name="_generate_disable_primary_navigation" id="_generate_disable_primary_navigation" value="true" <?php checked( get_post_meta( get_the_ID(), '_generate_disable_primary_navigation', true ), 'true' ); ?> />
						</td>
					</tr>

					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_disable_secondary_navigation"><?php _e( 'Secondary Navigation', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input type="checkbox" name="_generate_disable_secondary_navigation" id="_generate_disable_secondary_navigation" value="true" <?php checked( get_post_meta( get_the_ID(), '_generate_disable_secondary_navigation', true ), 'true' ); ?> />
						</td>
					</tr>

					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_disable_featured_image"><?php _e( 'Featured Image', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input type="checkbox" name="_generate_disable_featured_image" id="_generate_disable_featured_image" value="true" <?php checked( get_post_meta( get_the_ID(), '_generate_disable_featured_image', true ), 'true' ); ?> />
						</td>
					</tr>

					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_disable_content_title"><?php _e( 'Content Title', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input type="checkbox" name="_generate_disable_content_title" id="_generate_disable_content_title" value="true" <?php checked( get_post_meta( get_the_ID(), '_generate_disable_content_title', true ), 'true' ); ?> />
						</td>
					</tr>

					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_disable_footer"><?php _e( 'Footer', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input type="checkbox" name="_generate_disable_footer" id="_generate_disable_footer" value="true" <?php checked( get_post_meta( get_the_ID(), '_generate_disable_footer', true ), 'true' ); ?> />
						</td>
					</tr>
				</body>
			</table>

			<table class="generate-elements-settings" data-type="layout" data-tab="content">
				<tbody>
					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_content_area"><?php _e( 'Content Area', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<div class="layout-radio-item">
								<label for="default-container">
									<input type="radio" name="_generate_content_area" id="default-container" <?php checked( get_post_meta( get_the_ID(), '_generate_content_area', true ), '' ); ?> value="">
									<?php esc_html_e( 'Default', 'gp-premium' );?>
								</label>
							</div>

							<div class="layout-radio-item">
								<label for="full-width-container">
									<input type="radio" name="_generate_content_area" id="full-width-container" <?php checked( get_post_meta( get_the_ID(), '_generate_content_area', true ), 'full-width' ); ?> value="full-width">
									<?php esc_html_e( 'Full Width (no padding)', 'gp-premium' );?>
								</label>
							</div>

							<div class="layout-radio-item">
								<label for="contained-container">
									<input type="radio" name="_generate_content_area" id="contained-container" <?php checked( get_post_meta( get_the_ID(), '_generate_content_area', true ), 'contained' ); ?> value="contained">
									<?php echo esc_html_x( 'Contained (no padding)', 'Width', 'gp-premium' );?>
								</label>
							</div>
						</td>
					</tr>
					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label for="_generate_content_width"><?php _e( 'Content Width', 'gp-premium' ); ?></label>
						</td>
						<td class="generate-element-row-content">
							<input type="number" id="_generate_content_width" name="_generate_content_width" value="<?php echo get_post_meta( get_the_ID(), '_generate_content_width', true ); ?>">
							<span class="unit">px</span>
						</td>
					</tr>
				</tbody>
			</table>

			<table class="generate-elements-settings" data-tab="display-rules" style="display: none;">
				<tbody>
					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label><?php _e( 'Location', 'gp-premium' ); ?></label>
							<span class="tip" data-balloon="<?php esc_attr_e( 'Choose when this element should display.', 'gp-premium' ); ?>" data-balloon-pos="down">?</span>
						</td>
						<td class="generate-element-row-content">
							<?php
							$display_conditionals = get_post_meta( get_the_ID(), '_generate_element_display_conditions', true );
							$conditions = GeneratePress_Conditions::get_conditions();

							if ( $display_conditionals ) {
								foreach ( $display_conditionals as $field => $value ) {
									$type = explode( ':', $value['rule'] );

									if ( in_array( 'post', $type ) || in_array( 'taxonomy', $type ) ) {
										$show = true;
									} else {
										$show = false;
									}
									?>
									<div class="condition <?php echo $show ? 'generate-elements-rule-objects-visible' : ''; ?>">
										<select class="condition-select" name="display-condition[]">
											<option value=""><?php esc_attr_e( 'Choose...', 'gp-premium' ); ?></option>
											<?php foreach ( $conditions as $type ) { ?>
												<optgroup label="<?php echo $type['label']; ?>">
													<?php foreach ( $type['locations'] as $id => $label ) {
														printf(
															'<option value="%1$s" %2$s>%3$s</option>',
															 $id,
															selected( $value['rule'], $id ),
															$label
														);
													} ?>
												</optgroup>
											<?php } ?>
										</select>

										<select class="condition-object-select" data-saved-value="<?php echo isset( $value['object'] ) ? $value['object'] : ''; ?>" name="display-condition-object[]">
											<option value="0"></option>
										</select>

										<button class="remove-condition"><span class="screen-reader-text"><?php _e( 'Remove', 'gp-premium' ); ?></span></button>
									</div>
									<?php
								}
							} else {
								?>
								<div class="condition">
									<select class="condition-select" name="display-condition[]">
										<option value=""><?php esc_attr_e( 'Choose...', 'gp-premium' ); ?></option>
										<?php foreach ( $conditions as $type ) { ?>
											<optgroup label="<?php echo $type['label']; ?>">
												<?php foreach ( $type['locations'] as $id => $label ) {
													printf(
														'<option value="%1$s">%2$s</option>',
														$id,
														$label
													);
												} ?>
											</optgroup>
										<?php } ?>
									</select>

									<select class="condition-object-select" name="display-condition-object[]">
										<option value="0"></option>
									</select>

									<button class="remove-condition"><span class="screen-reader-text"><?php _e( 'Remove', 'gp-premium' ); ?></span></button>
								</div>
								<?php
							}
							?>
							<div class="condition hidden screen-reader-text">
								<select class="condition-select" name="display-condition[]">
									<option value=""><?php esc_attr_e( 'Choose...', 'gp-premium' ); ?></option>
									<?php foreach ( $conditions as $type ) { ?>
										<optgroup label="<?php echo $type['label']; ?>">
											<?php foreach ( $type['locations'] as $id => $label ) {
												printf(
													'<option value="%1$s">%2$s</option>',
													$id,
													$label
												);
											} ?>
										</optgroup>
									<?php } ?>
								</select>

								<select class="condition-object-select" name="display-condition-object[]">
									<option value="0"></option>
								</select>

								<button class="remove-condition"><span class="screen-reader-text"><?php _e( 'Remove', 'gp-premium' ); ?></span></button>
							</div>

							<button class="button add-condition"><?php _e( 'Add Location Rule', 'gp-premium' ); ?></button>
						</td>
					</tr>

					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label><?php _e( 'Exclude', 'gp-premium' ); ?></label>
							<span class="tip" data-balloon="<?php esc_attr_e( 'Choose when this element should not display.', 'gp-premium' ); ?>" data-balloon-pos="down">?</span>
						</td>
						<td class="generate-element-row-content">
							<?php
							$exclude_conditionals = get_post_meta( get_the_ID(), '_generate_element_exclude_conditions', true );
							$conditions = GeneratePress_Conditions::get_conditions();

							if ( $exclude_conditionals ) {
								foreach ( $exclude_conditionals as $field => $value ) {
									$type = explode( ':', $value['rule'] );

									if ( in_array( 'post', $type ) || in_array( 'taxonomy', $type ) ) {
										$show = true;
									} else {
										$show = false;
									}
									?>
									<div class="condition <?php echo $show ? 'generate-elements-rule-objects-visible' : ''; ?>">
										<select class="condition-select" name="exclude-condition[]">
											<option value=""><?php esc_attr_e( 'Choose...', 'gp-premium' ); ?></option>
											<?php foreach ( $conditions as $type ) { ?>
												<optgroup label="<?php echo $type['label']; ?>">
													<?php foreach ( $type['locations'] as $id => $label ) {
														printf(
															'<option value="%1$s" %2$s>%3$s</option>',
															 $id,
															selected( $value['rule'], $id ),
															$label
														);
													} ?>
												</optgroup>
											<?php } ?>
										</select>

										<select class="condition-object-select" data-saved-value="<?php echo isset( $value['object'] ) ? $value['object'] : ''; ?>" name="exclude-condition-object[]">
											<option value="0"></option>
										</select>

										<button class="remove-condition"><span class="screen-reader-text"><?php _e( 'Remove', 'gp-premium' ); ?></span></button>
									</div>
									<?php
								}
							} else {
								?>
								<div class="condition">
									<select class="condition-select" name="exclude-condition[]">
										<option value=""><?php esc_attr_e( 'Choose...', 'gp-premium' ); ?></option>
										<?php foreach ( $conditions as $type ) { ?>
											<optgroup label="<?php echo $type['label']; ?>">
												<?php foreach ( $type['locations'] as $id => $label ) {
													printf(
														'<option value="%1$s">%2$s</option>',
														$id,
														$label
													);
												} ?>
											</optgroup>
										<?php } ?>
									</select>

									<select class="condition-object-select" name="exclude-condition-object[]">
										<option value="0"></option>
									</select>

									<button class="remove-condition"><span class="screen-reader-text"><?php _e( 'Remove', 'gp-premium' ); ?></span></button>
								</div>
								<?php
							}
							?>
							<div class="condition hidden screen-reader-text">
								<select class="condition-select" name="exclude-condition[]">
									<option value=""><?php esc_attr_e( 'Choose...', 'gp-premium' ); ?></option>
									<?php foreach ( $conditions as $type ) { ?>
										<optgroup label="<?php echo $type['label']; ?>">
											<?php foreach ( $type['locations'] as $id => $label ) {
												printf(
													'<option value="%1$s">%2$s</option>',
													$id,
													$label
												);
											} ?>
										</optgroup>
									<?php } ?>
								</select>

								<select class="condition-object-select" name="exclude-condition-object[]">
									<option value="0"></option>
								</select>

								<button class="remove-condition"><span class="screen-reader-text"><?php _e( 'Remove', 'gp-premium' ); ?></span></button>
							</div>

							<button class="button add-condition"><?php _e( 'Add Exclusion Rule', 'gp-premium' ); ?></button>
						</td>
					</tr>

					<tr class="generate-element-row">
						<td class="generate-element-row-heading">
							<label><?php _e( 'Users', 'gp-premium' ); ?></label>
							<span class="tip" data-balloon="<?php esc_attr_e( 'Display this element for specific user roles.', 'gp-premium' ); ?>" data-balloon-pos="down">?</span>
						</td>
						<td class="generate-element-row-content">
							<?php
							$user_conditionals = get_post_meta( get_the_ID(), '_generate_element_user_conditions', true );
							$conditions = GeneratePress_Conditions::get_user_conditions();

							if ( $user_conditionals ) {

								foreach ( $user_conditionals as $field => $value ) {
									?>
									<div class="condition">
										<select class="condition-select" name="user-condition[]">
											<option value=""><?php esc_attr_e( 'Choose...', 'gp-premium' ); ?></option>
											<?php foreach ( $conditions as $type ) { ?>
												<optgroup label="<?php echo $type['label']; ?>">
													<?php foreach ( $type['rules'] as $id => $label ) {
														printf(
															'<option value="%1$s" %2$s>%3$s</option>',
															 $id,
															selected( $value, $id ),
															$label
														);
													} ?>
												</optgroup>
											<?php } ?>
										</select>

										<button class="remove-condition"><span class="screen-reader-text"><?php _e( 'Remove', 'gp-premium' ); ?></span></button>
									</div>
									<?php
								}
							} else {
								?>
								<div class="condition">
									<select class="condition-select" name="user-condition[]">
										<option value=""><?php esc_attr_e( 'Choose...', 'gp-premium' ); ?></option>
										<?php foreach ( $conditions as $type ) { ?>
											<optgroup label="<?php echo $type['label']; ?>">
												<?php foreach ( $type['rules'] as $id => $label ) {
													printf(
														'<option value="%1$s">%2$s</option>',
														$id,
														$label
													);
												} ?>
											</optgroup>
										<?php } ?>
									</select>

									<button class="remove-condition"><span class="screen-reader-text"><?php _e( 'Remove', 'gp-premium' ); ?></span></button>
								</div>
								<?php
							}
							?>
							<div class="condition hidden screen-reader-text">
								<select class="condition-select" name="user-condition[]">
									<option value=""><?php esc_attr_e( 'Choose...', 'gp-premium' ); ?></option>
									<?php foreach ( $conditions as $type ) { ?>
										<optgroup label="<?php echo $type['label']; ?>">
											<?php foreach ( $type['rules'] as $id => $label ) {
												printf(
													'<option value="%1$s">%2$s</option>',
													$id,
													$label
												);
											} ?>
										</optgroup>
									<?php } ?>
								</select>

								<button class="remove-condition"><span class="screen-reader-text"><?php _e( 'Remove', 'gp-premium' ); ?></span></button>
							</div>

							<button class="button add-condition"><?php _e( 'Add User Rule', 'gp-premium' ); ?></button>
						</td>
					</tr>
					<tr class="generate-element-row" <?php if ( ! function_exists( 'pll_get_post_language' ) ) echo 'style="display: none;"'; ?>>
						<td class="generate-element-row-heading">
							<label for="_generate_element_ignore_languages"><?php _e( 'Ignore Languages', 'gp-premium' ); ?></label>
							<span class="tip" data-balloon="<?php esc_attr_e( 'Show this Element to all languages.', 'gp-premium' ); ?>" data-balloon-pos="up">?</span>
						</td>
						<td class="generate-element-row-content">
							<input type="checkbox" name="_generate_element_ignore_languages" id="_generate_element_ignore_languages" value="true" <?php checked( get_post_meta( get_the_ID(), '_generate_element_ignore_languages', true ), 'true' ); ?> />
						</td>
					</tr>
				</tbody>
			</table>

			<table class="generate-elements-settings" data-type="all" data-tab="internal-notes" style="display: none;">
				<tbody>
					<tr id="hook-row" class="generate-element-row">
						<td class="generate-element-row-content">
							<textarea id="_generate_element_internal_notes" name="_generate_element_internal_notes"><?php echo get_post_meta( get_the_ID(), '_generate_element_internal_notes', true ); ?></textarea>
							<p style="margin: 5px 0 0;"><?php _e( 'Internal notes can be helpful to remember why this element was added.', 'gp-premium' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Save all of our metabox values.
	 *
	 * @since 1.7
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save( $post_id ) {
		$is_autosave = wp_is_post_autosave( $post_id );
		$is_revision = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST[ 'generate_elements_nonce' ] ) && wp_verify_nonce( $_POST[ 'generate_elements_nonce' ], basename( __FILE__ ) ) ) ? true : false;

		// Exits script depending on save status
		if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
			return;
		}

		// Check that the logged in user has permission to edit this post
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		if ( ! isset( $_POST['_generate_element_content'] ) ) {
			return $post_id;
		}

		if ( ! isset( $_POST['_generate_element_type'] ) ) {
			return $post_id;
		}

		// Save the type of element.
		$type_key = '_generate_element_type';
		$type_value = sanitize_key( $_POST[ $type_key ] );

		if ( $type_value ) {
			update_post_meta( $post_id, $type_key, $type_value );
		} else {
			delete_post_meta( $post_id, $type_key );
		}

		// Content.
		$key = '_generate_element_content';

		if ( current_user_can( 'unfiltered_html' ) ) {
			$value = $_POST[ $key ];
		} else {
			$value = wp_kses_post( $_POST[ $key ] );
		}

		if ( $value ) {
			update_post_meta( $post_id, $key, $value );
		} else {
			delete_post_meta( $post_id, $key );
		}

		// Save Hooks type.
		if ( 'hook' === $type_value ) {
			$hook_values = array(
				'_generate_hook' 						=> 'text',
				'_generate_custom_hook' 				=> 'text',
				'_generate_hook_disable_site_header'	=> 'key',
				'_generate_hook_disable_site_footer' 	=> 'key',
				'_generate_hook_execute_shortcodes' 	=> 'key',
				'_generate_hook_execute_php' 			=> 'key',
				'_generate_hook_priority' 				=> 'number',
			);

			// We don't want people to be able to use these hooks.
			$blacklist = array(
				'muplugins_loaded',
				'registered_taxonomy',
				'plugins_loaded',
				'setup_theme',
				'after_setup_theme',
				'init',
				'widgets_init',
				'wp_loaded',
				'pre_get_posts',
				'wp',
				'template_redirect',
				'get_header',
				'wp_enqueue_scripts',
				'the_post',
				'dynamic_sidebar',
				'get_footer',
				'get_sidebar',
				'wp_print_footer_scripts',
				'shutdown',
			);

			foreach ( $hook_values as $key => $type ) {
				$value = false;

				if ( isset( $_POST[ $key ] ) ) {
					// Bail if we're using a blacklisted hook.
					if ( '_generate_custom_hook' === $key ) {
						if ( in_array( $_POST[ $key ], $blacklist ) ) {
							continue;
						}
					}

					if ( 'number' === $type ) {
						$value = absint( $_POST[ $key ] );
					} elseif ( 'key' === $type ) {
						$value = sanitize_key( $_POST[ $key ] );
					} else {
						$value = sanitize_text_field( $_POST[ $key ] );
					}

					// Need to temporarily change the $value so it returns true.
					if ( '_generate_hook_priority' === $key ) {
						if ( '0' === $_POST[ $key ] ) {
							$value = 'zero';
						}
					}
				}

				if ( $value ) {
					if ( 'zero' === $value ) {
						$value = '0';
					}

					update_post_meta( $post_id, $key, $value );
				} else {
					delete_post_meta( $post_id, $key );
				}
			}
		}

		// Page Header type.
		if ( 'header' === $type_value ) {
			$hero_values = array(
				'_generate_hero_custom_classes'					=> 'attribute',
				'_generate_hero_container'						=> 'text',
				'_generate_hero_inner_container'				=> 'text',
				'_generate_hero_horizontal_alignment'			=> 'text',
				'_generate_hero_full_screen'					=> 'key',
				'_generate_hero_vertical_alignment'				=> 'text',
				'_generate_hero_padding_top' 					=> 'number',
				'_generate_hero_padding_top_unit' 				=> 'text',
				'_generate_hero_padding_right' 					=> 'number',
				'_generate_hero_padding_right_unit' 			=> 'text',
				'_generate_hero_padding_bottom' 				=> 'number',
				'_generate_hero_padding_bottom_unit'			=> 'text',
				'_generate_hero_padding_left' 					=> 'number',
				'_generate_hero_padding_left_unit'				=> 'text',
				'_generate_hero_padding_top_mobile' 			=> 'number',
				'_generate_hero_padding_top_unit_mobile' 		=> 'text',
				'_generate_hero_padding_right_mobile' 			=> 'number',
				'_generate_hero_padding_right_unit_mobile' 		=> 'text',
				'_generate_hero_padding_bottom_mobile' 			=> 'number',
				'_generate_hero_padding_bottom_unit_mobile'		=> 'text',
				'_generate_hero_padding_left_mobile' 			=> 'number',
				'_generate_hero_padding_left_unit_mobile'		=> 'text',
				'_generate_hero_background_image'				=> 'key',
				'_generate_hero_disable_featured_image'			=> 'key',
				'_generate_hero_background_color'				=> 'color',
				'_generate_hero_text_color'						=> 'color',
				'_generate_hero_link_color'						=> 'color',
				'_generate_hero_background_link_color_hover'	=> 'color',
				'_generate_hero_background_overlay'				=> 'key',
				'_generate_hero_background_position'			=> 'text',
				'_generate_hero_background_parallax'			=> 'key',
				'_generate_site_header_merge'					=> 'key',
				'_generate_site_header_height'					=> 'number',
				'_generate_site_header_height_mobile'			=> 'number',
				'_generate_navigation_colors'					=> 'key',
				'_generate_site_logo'							=> 'number',
				'_generate_retina_logo'							=> 'number',
				'_generate_navigation_logo'						=> 'number',
				'_generate_mobile_logo'							=> 'number',
				'_generate_navigation_location'					=> 'key',
				'_generate_site_header_background_color'		=> 'text',
				'_generate_site_header_title_color'				=> 'text',
				'_generate_site_header_tagline_color'			=> 'text',
				'_generate_navigation_background_color'			=> 'text',
				'_generate_navigation_text_color'				=> 'text',
				'_generate_navigation_background_color_hover'	=> 'text',
				'_generate_navigation_text_color_hover'			=> 'text',
				'_generate_navigation_background_color_current'	=> 'text',
				'_generate_navigation_text_color_current'		=> 'text',
			);

			foreach ( $hero_values as $key => $type ) {
				$value = false;

				if ( isset( $_POST[ $key ] ) ) {
					if ( 'number' === $type ) {
						$value = absint( $_POST[ $key ] );
					} elseif ( 'key' === $type ) {
						$value = sanitize_key( $_POST[ $key ] );
					} elseif ( 'attribute' === $type ) {
						$value = esc_attr( $_POST[ $key ] );
					} else {
						$value = sanitize_text_field( $_POST[ $key ] );
					}
				}

				if (
					'_generate_hero_padding_top_mobile' === $key ||
					'_generate_hero_padding_right_mobile' === $key ||
					'_generate_hero_padding_bottom_mobile' === $key ||
					'_generate_hero_padding_left_mobile' === $key
				) {
					if ( '0' === $_POST[ $key ] ) {
						$value = 'zero';
					}
				}

				if ( $value ) {
					if ( 'zero' === $value ) {
						$value = '0'; // String on purpose.
					}

					update_post_meta( $post_id, $key, $value );
				} else {
					delete_post_meta( $post_id, $key );
				}
			}
		}

		// Save Layout type.
		if ( 'layout' === $type_value ) {
			$layout_values = array(
				'_generate_sidebar_layout'					=> 'key',
				'_generate_footer_widgets'					=> 'key',
				'_generate_disable_site_header' 			=> 'key',
				'_generate_disable_top_bar'					=> 'key',
				'_generate_disable_primary_navigation'		=> 'key',
				'_generate_disable_secondary_navigation'	=> 'key',
				'_generate_disable_featured_image'			=> 'key',
				'_generate_disable_content_title'			=> 'key',
				'_generate_disable_footer'					=> 'key',
				'_generate_content_area'					=> 'key',
				'_generate_content_width'					=> 'number',
			);

			foreach ( $layout_values as $key => $type ) {
				$value = false;

				if ( isset( $_POST[ $key ] ) ) {
					if ( 'number' === $type ) {
						$value = absint( $_POST[ $key ] );
					} elseif ( 'key' === $type ) {
						$value = sanitize_key( $_POST[ $key ] );
					} else {
						$value = sanitize_text_field( $_POST[ $key ] );
					}
				}

				if ( $value ) {
					update_post_meta( $post_id, $key, $value );
				} else {
					delete_post_meta( $post_id, $key );
				}
			}
		}

		$ignore_languages = false;

		if ( isset( $_POST['_generate_element_ignore_languages'] ) ) {
			$ignore_languages = sanitize_key( $_POST['_generate_element_ignore_languages'] );
		}

		if ( $ignore_languages ) {
			update_post_meta( $post_id, '_generate_element_ignore_languages', $ignore_languages );
		} else {
			delete_post_meta( $post_id, '_generate_element_ignore_languages' );
		}

		// Display conditions.
		$conditions = get_post_meta( $post_id, '_generate_element_display_conditions', true );
		$new_conditions = array();

		$rules = $_POST['display-condition'];
		$objects = $_POST['display-condition-object'];

		$count = count( $rules );

		for ( $i = 0; $i < $count; $i++ ) {
			if ( '' !== $rules[ $i ] ) {

				if ( in_array( $rules[ $i ], $rules ) ) {
					$new_conditions[ $i ]['rule'] = sanitize_text_field( $rules[ $i ] );
					$new_conditions[ $i ]['object'] = sanitize_key( $objects[ $i ] );
				} else {
					$new_conditions[ $i ]['rule'] = '';
					$new_conditions[ $i ]['object'] = '';
				}

			}
		}

		if ( ! empty( $new_conditions ) && $new_conditions !== $conditions ) {
			update_post_meta( $post_id, '_generate_element_display_conditions', $new_conditions );
		} elseif ( empty( $new_conditions ) && $conditions ) {
			delete_post_meta( $post_id, '_generate_element_display_conditions', $conditions );
		}

		// Exclude conditions.
		$exclude_conditions = get_post_meta( $post_id, '_generate_element_exclude_conditions', true );
		$new_exclude_conditions = array();

		$exclude_rules = $_POST['exclude-condition'];
		$exclude_objects = $_POST['exclude-condition-object'];

		$exclude_count = count( $exclude_rules );

		for ( $i = 0; $i < $exclude_count; $i++ ) {
			if ( '' !== $exclude_rules[ $i ] ) {

				if ( in_array( $exclude_rules[ $i ], $exclude_rules ) ) {
					$new_exclude_conditions[ $i ]['rule'] = sanitize_text_field( $exclude_rules[ $i ] );
					$new_exclude_conditions[ $i ]['object'] = sanitize_key( $exclude_objects[ $i ] );
				} else {
					$new_exclude_conditions[ $i ]['rule'] = '';
					$new_exclude_conditions[ $i ]['object'] = '';
				}

			}
		}

		if ( ! empty( $new_exclude_conditions ) && $new_exclude_conditions !== $exclude_conditions ) {
			update_post_meta( $post_id, '_generate_element_exclude_conditions', $new_exclude_conditions );
		} elseif ( empty( $new_exclude_conditions ) && $exclude_conditions ) {
			delete_post_meta( $post_id, '_generate_element_exclude_conditions', $exclude_conditions );
		}

		// User conditions.
		$user_conditions = get_post_meta( $post_id, '_generate_element_user_conditions', true );
		$new_user_conditions = array();

		$user_rules = $_POST['user-condition'];
		$user_count = count( $user_rules );

		for ( $i = 0; $i < $user_count; $i++ ) {
			if ( '' !== $user_rules[ $i ] ) {

				if ( in_array( $user_rules[ $i ], $user_rules ) ) {
					$new_user_conditions[ $i ] = sanitize_text_field( $user_rules[ $i ] );
				} else {
					$new_user_conditions[ $i ] = '';
				}

			}
		}

		if ( ! empty( $new_user_conditions ) && $new_user_conditions !== $user_conditions ) {
			update_post_meta( $post_id, '_generate_element_user_conditions', $new_user_conditions );
		} elseif ( empty( $new_user_conditions ) && $user_conditions ) {
			delete_post_meta( $post_id, '_generate_element_user_conditions', $user_conditions );
		}

		// Internal notes.
		$notes_key = '_generate_element_internal_notes';

		if ( isset( $_POST[ $notes_key ] ) ) {
			if ( function_exists( 'sanitize_textarea_field' ) ) {
				$notes_value = sanitize_textarea_field( $_POST[ $notes_key ] );
			} else {
				$notes_value = sanitize_text_field( $_POST[ $notes_key ] );
			}

			if ( $notes_value ) {
				update_post_meta( $post_id, $notes_key, $notes_value );
			} else {
				delete_post_meta( $post_id, $notes_key );
			}
		}
	}

	/**
	 * Get terms of a set taxonomy.
	 *
	 * @since 1.7
	 */
	public function get_terms() {
		check_ajax_referer( 'generate-elements-location', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! isset( $_POST['id'] ) ) {
			return;
		}

		$tax_id = sanitize_text_field( $_POST['id'] );

		echo json_encode( self::get_taxonomy_terms( $tax_id ) );

		die();
	}

	/**
	 * Get all posts inside a specific post type.
	 *
	 * @since 1.7
	 */
	public function get_posts() {
		check_ajax_referer( 'generate-elements-location', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! isset( $_POST['id'] ) ) {
			return;
		}

		$post_type = sanitize_text_field( $_POST['id'] );

		echo json_encode( self::get_post_type_posts( $post_type ) );

		die();
	}

	/**
	 * Look up posts inside a post type.
	 *
	 * @since 1.7
	 *
	 * @param string $post_type The post type to look up.
	 * @return array
	 */
	public static function get_post_type_posts( $post_type ) {

		global $wpdb;

		$post_status = array( 'publish', 'future', 'draft', 'pending', 'private' );

		$object = get_post_type_object( $post_type );

		$data = array(
			'type'     => 'posts',
			'postType' => $post_type,
			'label'    => $object->label,
			'objects'  => array(),
		);

		if ( 'attachment' === $post_type ) {
			$posts = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_title from $wpdb->posts where post_type = %s ORDER BY post_title", $post_type ) );

		} else {
			$format = implode( ', ', array_fill( 0, count( $post_status ), '%s' ) );
			$query = sprintf( "SELECT ID, post_title from $wpdb->posts where post_type = '%s' AND post_status IN(%s) ORDER BY post_title", $post_type, $format );
			// @codingStandardsIgnoreLine
			$posts = $wpdb->get_results( $wpdb->prepare( $query, $post_status ) );
		}

		foreach ( $posts as $post ) {
			$title = ( '' != $post->post_title ) ? esc_attr( $post->post_title ) : $post_type . '-' . $post->ID;
			$data['objects'][] = array(
				'id'    => $post->ID,
				'name'  => $title,
			);
		}

		return $data;
	}

	/**
	 * Get taxonomy terms for a specific taxonomy.
	 *
	 * @since 1.7
	 *
	 * @param $tax_id
	 * @return array
	 */
	public static function get_taxonomy_terms( $tax_id ) {
		$tax = get_taxonomy( $tax_id );

		$terms = get_terms( array(
			'taxonomy'   => $tax_id,
			'hide_empty' => false,
		) );

		$data = array(
			'type'     => 'terms',
			'taxonomy' => $tax_id,
			'label'    => $tax->label,
			'objects'  => array(),
		);

		foreach ( $terms as $term ) {
			$data['objects'][] = array(
				'id'    => $term->term_id,
				'name'  => esc_attr( $term->name ),
			);
		}

		return $data;
	}

	/**
	 * Build our entire list of hooks to display.
	 *
	 * @since 1.7
	 *
	 * @return array Our list of hooks.
	 */
	public static function get_available_hooks() {
		$hooks = array(
			'scripts' => array(
				'group' => esc_attr__( 'Scripts & Styles', 'gp-premium' ),
				'hooks' => array(
					'wp_head',
					'wp_body_open',
					'wp_footer',
				),
			),
			'header' => array(
				'group' => esc_attr__( 'Header', 'gp-premium' ),
				'hooks' => array(
					'generate_before_header',
					'generate_after_header',
					'generate_before_header_content',
					'generate_after_header_content',
					'generate_before_logo',
					'generate_after_logo',
					'generate_header',
				),
			),
			'navigation' => array(
				'group' => esc_attr__( 'Navigation', 'gp-premium' ),
				'hooks' => array(
					'generate_inside_navigation',
					'generate_inside_secondary_navigation',
					'generate_inside_mobile_menu',
					'generate_inside_mobile_menu_bar',
					'generate_inside_mobile_header',
					'generate_inside_slideout_navigation',
					'generate_after_slideout_navigation',
				),
			),
			'content' => array(
				'group' => esc_attr__( 'Content', 'gp-premium' ),
				'hooks' => array(
					'generate_inside_site_container',
					'generate_inside_container',
					'generate_before_main_content',
					'generate_after_main_content',
					'generate_before_content',
					'generate_after_content',
					'generate_after_entry_content',
					'generate_after_primary_content_area',
					'generate_before_entry_title',
					'generate_after_entry_title',
					'generate_after_entry_header',
					'generate_before_archive_title',
					'generate_after_archive_title',
					'generate_after_archive_description',
				),
			),
			'comments' => array(
				'group' => esc_attr__( 'Comments', 'gp-premium' ),
				'hooks' => array(
					'generate_before_comments_container',
					'generate_before_comments',
					'generate_inside_comments',
					'generate_below_comments_title',
				),
			),
			'sidebars' => array(
				'group' => esc_attr__( 'Sidebars', 'gp-premium' ),
				'hooks' => array(
					'generate_before_right_sidebar_content',
					'generate_after_right_sidebar_content',
					'generate_before_left_sidebar_content',
					'generate_after_left_sidebar_content',
				),
			),
			'footer' => array(
				'group' => esc_attr__( 'Footer', 'gp-premium' ),
				'hooks' => array(
					'generate_before_footer',
					'generate_after_footer',
					'generate_after_footer_widgets',
					'generate_before_footer_content',
					'generate_after_footer_content',
					'generate_footer',
				),
			),
		);

		if ( class_exists( 'WooCommerce' ) ) {
			$hooks['navigation']['hooks'][] = 'generate_mobile_cart_items';

			$hooks['woocommerce-global'] = array(
				'group' => esc_attr__( 'WooCommerce - Global', 'gp-premium' ),
				'hooks' => array(
					'woocommerce_before_main_content',
					'woocommerce_after_main_content',
					'woocommerce_sidebar',
					'woocommerce_breadcrumb',
				),
			);

			$hooks['woocommerce-shop'] = array(
				'group' => esc_attr__( 'WooCommerce - Shop', 'gp-premium' ),
				'hooks' => array(
					'woocommerce_archive_description',
					'woocommerce_before_shop_loop',
					'woocommerce_after_shop_loop',
					'woocommerce_before_shop_loop_item_title',
					'woocommerce_after_shop_loop_item_title',
				),
			);

			$hooks['woocommerce-product'] = array(
				'group' => esc_attr__( 'WooCommerce - Product', 'gp-premium' ),
				'hooks' => array(
					'woocommerce_before_single_product',
					'woocommerce_before_single_product_summary',
					'woocommerce_after_single_product_summary',
					'woocommerce_single_product_summary',
					'woocommerce_share',
					'woocommerce_simple_add_to_cart',
					'woocommerce_before_add_to_cart_form',
					'woocommerce_after_add_to_cart_form',
					'woocommerce_before_add_to_cart_button',
					'woocommerce_after_add_to_cart_button',
					'woocommerce_before_add_to_cart_quantity',
					'woocommerce_after_add_to_cart_quantity',
					'woocommerce_product_meta_start',
					'woocommerce_product_meta_end',
					'woocommerce_after_single_product',
				),
			);

			$hooks['woocommerce-cart'] = array(
				'group' => esc_attr__( 'WooCommerce - Cart', 'gp-premium' ),
				'hooks' => array(
					'woocommerce_before_calculate_totals',
					'woocommerce_after_calculate_totals',
					'woocommerce_before_cart',
					'woocommerce_after_cart_table',
					'woocommerce_before_cart_table',
					'woocommerce_before_cart_contents',
					'woocommerce_cart_contents',
					'woocommerce_after_cart_contents',
					'woocommerce_cart_coupon',
					'woocommerce_cart_actions',
					'woocommerce_before_cart_totals',
					'woocommerce_cart_totals_before_order_total',
					'woocommerce_cart_totals_after_order_total',
					'woocommerce_proceed_to_checkout',
					'woocommerce_after_cart_totals',
					'woocommerce_after_cart',
				),
			);

			$hooks['woocommerce-checkout'] = array(
				'group' => esc_attr__( 'WooCommerce - Checkout', 'gp-premium' ),
				'hooks' => array(
					'woocommerce_before_checkout_form',
					'woocommerce_checkout_before_customer_details',
					'woocommerce_checkout_after_customer_details',
					'woocommerce_checkout_billing',
					'woocommerce_before_checkout_billing_form',
					'woocommerce_after_checkout_billing_form',
					'woocommerce_before_order_notes',
					'woocommerce_after_order_notes',
					'woocommerce_checkout_shipping',
					'woocommerce_checkout_before_order_review',
					'woocommerce_checkout_order_review',
					'woocommerce_review_order_before_cart_contents',
					'woocommerce_review_order_after_cart_contents',
					'woocommerce_review_order_before_order_total',
					'woocommerce_review_order_after_order_total',
					'woocommerce_review_order_before_payment',
					'woocommerce_review_order_before_submit',
					'woocommerce_review_order_after_submit',
					'woocommerce_review_order_after_payment',
					'woocommerce_checkout_after_order_review',
					'woocommerce_after_checkout_form',
				),
			);

			$hooks['woocommerce-account'] = array(
				'group' => esc_attr__( 'WooCommerce - Account', 'gp-premium' ),
				'hooks' => array(
					'woocommerce_before_account_navigation',
					'woocommerce_account_navigation',
					'woocommerce_after_account_navigation',
				),
			);
		}

		return apply_filters( 'generate_hooks_list', $hooks );
	}

	public static function template_tags() {
		?>
		<input type="text" readonly="readonly" value="{{post_title}}" />
		<p class="decription" style="margin-top:0;opacity:0.8;font-size:85%;">
			<?php _e( 'The content title of the current post/taxonomy.', 'gp-premium' ); ?>
		</p>

		<input type="text" readonly="readonly" value="{{post_date}}" />
		<p class="decription" style="margin-top:0;opacity:0.8;font-size:85%;">
			<?php _e( 'The published date of the current post.', 'gp-premium' ); ?>
		</p>

		<input type="text" readonly="readonly" value="{{post_author}}" />
		<p class="decription" style="margin-top:0;opacity:0.8;font-size:85%;">
			<?php _e( 'The author of the current post.', 'gp-premium' ); ?>
		</p>

		<input type="text" readonly="readonly" value="{{post_terms.taxonomy}}" />
		<p class="decription" style="margin-top:0;opacity:0.8;font-size:85%;">
			<?php _e( 'The terms attached to the chosen taxonomy (category, post_tag, product_cat).', 'gp-premium' ); ?>
		</p>

		<input type="text" readonly="readonly" value='{{custom_field.name}}' />
		<p class="decription" style="margin-top:0;opacity:0.8;font-size:85%;">
			<?php _e( 'Custom post meta. Replace "name" with the name of your custom field.', 'gp-premium' ); ?>
		</p>
		<?php
	}
}

GeneratePress_Elements_Metabox::get_instance();
