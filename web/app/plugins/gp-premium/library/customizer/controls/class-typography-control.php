<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'GeneratePress_Pro_Typography_Customize_Control' ) ) :
class GeneratePress_Pro_Typography_Customize_Control extends WP_Customize_Control
{
    public $type = 'gp-pro-customizer-typography';

	public function enqueue() {
		wp_enqueue_script( 'generatepress-pro-typography-selectWoo', trailingslashit( plugin_dir_url( __FILE__ ) )  . 'js/selectWoo.min.js', array( 'customize-controls', 'jquery' ), GP_PREMIUM_VERSION, true );
		wp_enqueue_style( 'generatepress-pro-typography-selectWoo', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'css/selectWoo.min.css', array(), GP_PREMIUM_VERSION );

		wp_enqueue_script( 'generatepress-pro-typography-customizer', trailingslashit( plugin_dir_url( __FILE__ ) )  . 'js/typography-customizer.js', array( 'customize-controls', 'generatepress-pro-typography-selectWoo' ), GP_PREMIUM_VERSION, true );

		wp_enqueue_style( 'generatepress-pro-typography-customizer', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'css/typography-customizer.css', array(), GP_PREMIUM_VERSION );
	}

	public function to_json() {
		parent::to_json();

		$this->json[ 'default_fonts_title'] = __( 'System Fonts', 'gp-premium' );
		$this->json[ 'google_fonts_title'] = __( 'Google Fonts', 'gp-premium' );
		$this->json[ 'default_fonts' ] = generate_typography_default_fonts();
		$this->json[ 'family_title' ] = esc_html__( 'Font family', 'gp-premium' );
		$this->json[ 'weight_title' ] = esc_html__( 'Font weight', 'gp-premium' );
		$this->json[ 'transform_title' ] = esc_html__( 'Text transform', 'gp-premium' );
		$this->json[ 'category_title' ] = '';
		$this->json[ 'variant_title' ] = esc_html__( 'Variants', 'gp-premium' );

		foreach ( $this->settings as $setting_key => $setting_id ) {
			$this->json[ $setting_key ] = array(
				'link'  => $this->get_link( $setting_key ),
				'value' => $this->value( $setting_key ),
				'default' => isset( $setting_id->default ) ? $setting_id->default : '',
				'id' => isset( $setting_id->id ) ? $setting_id->id : ''
			);

			if ( 'weight' === $setting_key ) {
				$this->json[ $setting_key ]['choices'] = $this->get_font_weight_choices();
			}

			if ( 'transform' === $setting_key ) {
				$this->json[ $setting_key ]['choices'] = $this->get_font_transform_choices();
			}
		}
	}

	public function content_template() {
		?>
		<# if ( '' !== data.label ) { #>
			<span class="customize-control-title">{{ data.label }}</span>
		<# } #>
		<# if ( 'undefined' !== typeof ( data.family ) ) { #>
			<div class="generatepress-font-family">
				<label>
					<select {{{ data.family.link }}} data-category="{{{ data.category.id }}}" data-variants="{{{ data.variant.id }}}" style="width:100%;">
						<optgroup label="{{ data.default_fonts_title }}">
							<# for ( var key in data.default_fonts ) { #>
								<# var name = data.default_fonts[ key ].split(',')[0]; #>
								<option value="{{ data.default_fonts[ key ] }}"  <# if ( data.default_fonts[ key ] === data.family.value ) { #>selected="selected"<# } #>>{{ name }}</option>
							<# } #>
						</optgroup>
						<optgroup label="{{ data.google_fonts_title }}">
							<# for ( var key in generatePressTypography.googleFonts ) { #>
								<option value="{{ generatePressTypography.googleFonts[ key ].name }}"  <# if ( generatePressTypography.googleFonts[ key ].name === data.family.value ) { #>selected="selected"<# } #>>{{ generatePressTypography.googleFonts[ key ].name }}</option>
							<# } #>
						</optgroup>
					</select>
					<# if ( '' !== data.family_title ) { #>
						<p class="description">{{ data.family_title }}</p>
					<# } #>
				</label>
			</div>
		<# } #>

		<# if ( 'undefined' !== typeof ( data.variant ) ) { #>
			<#
			var id = data.family.value.split(' ').join('_').toLowerCase();
			var font_data = generatePressTypography.googleFonts[id];
			var variants = '';
			if ( typeof font_data !== 'undefined' ) {
				variants = font_data.variants;
			}

			if ( null === data.variant.value ) {
				data.variant.value = data.variant.default;
			}
			#>
			<div id={{{ data.variant.id }}}" class="generatepress-font-variant" data-saved-value="{{ data.variant.value }}">
				<label>
					<select name="{{{ data.variant.id }}}" multiple class="typography-multi-select" style="width:100%;" {{{ data.variant.link }}}>
						<# _.each( variants, function( label, choice ) { #>
							<option value="{{ label }}">{{ label }}</option>
						<# } ) #>
					</select>

					<# if ( '' !== data.variant_title ) { #>
						<p class="description">{{ data.variant_title }}</p>
					<# } #>
				</label>
			</div>
		<# } #>

		<# if ( 'undefined' !== typeof ( data.category ) ) { #>
			<div class="generatepress-font-category">
				<label>
						<input name="{{{ data.category.id }}}" type="hidden" {{{ data.category.link }}} value="{{{ data.category.value }}}" class="gp-hidden-input" />
					<# if ( '' !== data.category_title ) { #>
						<p class="description">{{ data.category_title }}</p>
					<# } #>
				</label>
			</div>
		<# } #>

		<div class="generatepress-weight-transform-wrapper">
			<# if ( 'undefined' !== typeof ( data.weight ) ) { #>
				<div class="generatepress-font-weight">
					<label>
						<select {{{ data.weight.link }}}>

							<# _.each( data.weight.choices, function( label, choice ) { #>

								<option value="{{ choice }}" <# if ( choice === data.weight.value ) { #> selected="selected" <# } #>>{{ label }}</option>

							<# } ) #>

						</select>
						<# if ( '' !== data.weight_title ) { #>
							<p class="description">{{ data.weight_title }}</p>
						<# } #>
					</label>
				</div>
			<# } #>

			<# if ( 'undefined' !== typeof ( data.transform ) ) { #>
				<div class="generatepress-font-transform">
					<label>
						<select {{{ data.transform.link }}}>

							<# _.each( data.transform.choices, function( label, choice ) { #>

								<option value="{{ choice }}" <# if ( choice === data.transform.value ) { #> selected="selected" <# } #>>{{ label }}</option>

							<# } ) #>

						</select>
						<# if ( '' !== data.transform_title ) { #>
							<p class="description">{{ data.transform_title }}</p>
						<# } #>
					</label>
				</div>
			<# } #>
		</div>
		<?php
	}

	public function get_font_weight_choices() {
		return array(
			'' => esc_html__( 'inherit', 'gp-premium' ),
			'normal' => esc_html__( 'normal', 'gp-premium' ),
			'bold' => esc_html__( 'bold', 'gp-premium' ),
			'100' => esc_html( '100' ),
			'200' => esc_html( '200' ),
			'300' => esc_html( '300' ),
			'400' => esc_html( '400' ),
			'500' => esc_html( '500' ),
			'600' => esc_html( '600' ),
			'700' => esc_html( '700' ),
			'800' => esc_html( '800' ),
			'900' => esc_html( '900' ),
		);
	}

	public function get_font_transform_choices() {
		return array(
			'' => esc_html__( 'inherit', 'gp-premium' ),
			'none' => esc_html__( 'none', 'gp-premium' ),
			'capitalize' => esc_html__( 'capitalize', 'gp-premium' ),
			'uppercase' => esc_html__( 'uppercase', 'gp-premium' ),
			'lowercase' => esc_html__( 'lowercase', 'gp-premium' ),
		);
	}
}
endif;
