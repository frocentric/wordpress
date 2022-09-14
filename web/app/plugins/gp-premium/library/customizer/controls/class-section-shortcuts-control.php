<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return;
}

/**
 * Add a button which needs javascript attached to it.
 */
class GeneratePress_Section_Shortcut_Control extends WP_Customize_Control {
	public $type = 'gp_section_shortcut';
	public $element = '';
	public $shortcuts = array();

	public function enqueue() {
		wp_enqueue_script( 'gp-section-shortcuts', trailingslashit( plugin_dir_url( __FILE__ ) )  . 'js/section-shortcuts.js', array( 'customize-controls' ), GP_PREMIUM_VERSION, true );
		wp_enqueue_style( 'gp-section-shortcuts', trailingslashit( plugin_dir_url( __FILE__ ) )  . 'css/section-shortcuts.css', false, GP_PREMIUM_VERSION );
	}

	public function to_json() {
		parent::to_json();

		$shortcuts = array();
		foreach( $this->shortcuts as $name => $id ) {
			if ( 'colors' === $name ) {
				$name = esc_html__( 'Colors', 'gp-premium' );

				if ( version_compare( generate_premium_get_theme_version(), '3.1.0-alpha.1', '>=' ) && 'generate_woocommerce_colors' !== $id ) {
					$id = 'generate_colors_section';
				}

				if ( ! generatepress_is_module_active( 'generate_package_colors', 'GENERATE_COLORS' ) ) {
					$id = false;
					$name = false;
				}
			}

			if ( 'typography' === $name ) {
				$name = esc_html__( 'Typography', 'gp-premium' );

				if ( function_exists( 'generate_is_using_dynamic_typography' ) && generate_is_using_dynamic_typography() ) {
					$id = 'generate_typography_section';
				}

				if ( ! generatepress_is_module_active( 'generate_package_typography', 'GENERATE_TYPOGRAPHY' ) ) {
					$id = false;
					$name = false;
				}
			}

			if ( 'backgrounds' === $name ) {
				$name = esc_html__( 'Backgrounds', 'gp-premium' );

				if ( ! generatepress_is_module_active( 'generate_package_backgrounds', 'GENERATE_BACKGROUNDS' ) ) {
					$id = false;
					$name = false;
				}
			}

			if ( 'layout' === $name ) {
				$name = esc_html__( 'Layout', 'gp-premium' );
			}

			if ( $id && $name ) {
				$shortcuts[ $id ] = $name;
			}
		}

		if ( ! empty( $shortcuts ) ) {
			$this->json['shortcuts'] = $shortcuts;
		} else {
			$this->json['shortcuts'] = false;
		}

		if ( 'WooCommerce' !== $this->element ) {
			$this->element = strtolower( $this->element );
		}

		$this->json['more'] = sprintf(
			__( 'More %s controls:', 'gp-premium' ),
			'<span class="more-element">' . $this->element . '</span>'
		);

		$this->json['return'] = __( 'Go Back', 'gp-premium' );

		$this->json['section'] = $this->section;

		if ( apply_filters( 'generate_disable_customizer_shortcuts', false ) ) {
			$this->json['shortcuts'] = false;
		}
	}

	public function content_template() {
		?>
			<div class="generatepress-shortcuts">
				<# if ( data.shortcuts ) { #>
					<div class="show-shortcuts">
						<span class="more-controls">
							{{{ data.more }}}
						</span>

						<span class="shortcuts">
							<# _.each( data.shortcuts, function( label, section ) { #>
								<span class="shortcut">
									<a href="#" data-section="{{{ section }}}" data-current-section="{{{ data.section }}}">{{{ label }}}</a>
								</span>
							<# } ) #>
						</span>
					</div>
				<# } #>

				<div class="return-shortcut" style="display: none;">
					<span class="dashicons dashicons-no-alt"></span>
					<a href="#">&larr; {{{ data.return }}}</a>
				</div>
			</div>

		<?php
	}
}
