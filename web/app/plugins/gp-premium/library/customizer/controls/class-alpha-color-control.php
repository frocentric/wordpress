<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'GeneratePress_Alpha_Color_Customize_Control' ) ) :
class GeneratePress_Alpha_Color_Customize_Control extends WP_Customize_Control {
	/**
	 * Official control name.
	 */
	public $type = 'gp-alpha-color';
	/**
	 * Add support for palettes to be passed in.
	 *
	 * Supported palette values are true, false, or an array of RGBa and Hex colors.
	 */
	public $palette;
	/**
	 * Add support for showing the opacity value on the slider handle.
	 */
	public $show_opacity;
	/**
	 * Enqueue scripts and styles.
	 *
	 * Ideally these would get registered and given proper paths before this control object
	 * gets initialized, then we could simply enqueue them here, but for completeness as a
	 * stand alone class we'll register and enqueue them here.
	 */
	public function enqueue() {
		wp_enqueue_script(
			'gp-alpha-color-picker',
			trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/alpha-color-picker.js',
			array( 'jquery', 'wp-color-picker' ),
			GP_PREMIUM_VERSION,
			true
		);
		wp_enqueue_style(
			'gp-alpha-color-picker',
			trailingslashit( plugin_dir_url( __FILE__ ) ) . 'css/alpha-color-picker.css',
			array( 'wp-color-picker' ),
			GP_PREMIUM_VERSION
		);
	}

	public function to_json() {
		parent::to_json();
		$this->json['palette'] = $this->palette;
		$this->json['defaultValue'] = $this->setting->default;
		$this->json[ 'link' ] = $this->get_link();
		$this->json[ 'show_opacity' ] = $this->show_opacity;

		if ( is_array( $this->json['palette'] ) ) {
			$this->json['palette'] = implode( '|', $this->json['palette'] );
		} else {
			// Default to true.
			$this->json['palette'] = ( false === $this->json['palette'] || 'false' === $this->json['palette'] ) ? 'false' : 'true';
		}

		// Support passing show_opacity as string or boolean. Default to true.
		$this->json[ 'show_opacity' ] = ( false === $this->json[ 'show_opacity' ] || 'false' === $this->json[ 'show_opacity' ] ) ? 'false' : 'true';
	}

	/**
	 * Render the control.
	 */
	public function render_content() {}

	public function content_template() {
		?>
		<# if ( data.label && '' !== data.label ) { #>
			<span class="customize-control-title">{{ data.label }}</span>
		<# } #>
		<input class="gp-alpha-color-control" type="text" data-palette="{{{ data.palette }}}" data-default-color="{{ data.defaultValue }}" {{{ data.link }}} />
		<?php
	}
}
endif;