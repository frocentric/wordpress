<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WP_Customize_Control' ) ) {
	class GeneratePress_Background_Images_Customize_Control extends WP_Customize_Control {
		public $type = 'gp-background-images';

		public function enqueue() {
			wp_enqueue_script( 'gp-backgrounds-customizer', trailingslashit( plugin_dir_url( __FILE__ ) )  . 'js/backgrounds-customizer.js', array( 'customize-controls' ), GP_PREMIUM_VERSION, true );
		}

		public function to_json() {
			parent::to_json();

			$this->json[ 'position_title' ] = esc_html__( 'left top, x% y%, xpos ypos (px)', 'gp-premium' );
			$this->json[ 'position_placeholder' ] = esc_html__( 'Position', 'gp-premium' );

			foreach ( $this->settings as $setting_key => $setting_id ) {
				$this->json[ $setting_key ] = array(
					'link'  => $this->get_link( $setting_key ),
					'value' => $this->value( $setting_key ),
					'default' => isset( $setting_id->default ) ? $setting_id->default : '',
					'id' => isset( $setting_id->id ) ? $setting_id->id : ''
				);

				if ( 'repeat' === $setting_key ) {
					$this->json[ $setting_key ]['choices'] = $this->get_repeat_choices();
				}

				if ( 'size' === $setting_key ) {
					$this->json[ $setting_key ]['choices'] = $this->get_size_choices();
				}

				if ( 'attachment' === $setting_key ) {
					$this->json[ $setting_key ]['choices'] = $this->get_attachment_choices();
				}
			}
		}

		public function content_template() {
			?>
			<# if ( '' !== data.label ) { #>
				<span class="customize-control-title">{{ data.label }}</span>
			<# } #>

			<# if ( 'undefined' !== typeof ( data.repeat ) ) { #>
				<div class="generatepress-backgrounds-repeat">
					<label>
						<select {{{ data.repeat.link }}}>

							<# _.each( data.repeat.choices, function( label, choice ) { #>

								<option value="{{ choice }}" <# if ( choice === data.repeat.value ) { #> selected="selected" <# } #>>{{ label }}</option>

							<# } ) #>

						</select>
						<# if ( '' !== data.repeat_title ) { #>
							<p class="description">{{ data.repeat_title }}</p>
						<# } #>
					</label>
				</div>
			<# } #>

			<# if ( 'undefined' !== typeof ( data.size ) ) { #>
				<div class="generatepress-backgrounds-size">
					<label>
						<select {{{ data.size.link }}}>

							<# _.each( data.size.choices, function( label, choice ) { #>

								<option value="{{ choice }}" <# if ( choice === data.size.value ) { #> selected="selected" <# } #>>{{ label }}</option>

							<# } ) #>

						</select>
						<# if ( '' !== data.size_title ) { #>
							<p class="description">{{ data.size_title }}</p>
						<# } #>
					</label>
				</div>
			<# } #>

			<# if ( 'undefined' !== typeof ( data.attachment ) ) { #>
				<div class="generatepress-backgrounds-attachment">
					<label>
						<select {{{ data.attachment.link }}}>

							<# _.each( data.attachment.choices, function( label, choice ) { #>

								<option value="{{ choice }}" <# if ( choice === data.attachment.value ) { #> selected="selected" <# } #>>{{ label }}</option>

							<# } ) #>

						</select>
						<# if ( '' !== data.attachment_title ) { #>
							<p class="description">{{ data.attachment_title }}</p>
						<# } #>
					</label>
				</div>
			<# } #>

			<# if ( 'undefined' !== typeof ( data.position ) ) { #>
				<div class="generatepress-backgrounds-position">
					<label>
						<input name="{{{ data.position.id }}}" type="text" {{{ data.position.link }}} value="{{{ data.position.value }}}" placeholder="{{ data.position_placeholder }}" />
						<# if ( '' !== data.position_title ) { #>
							<p class="description">{{ data.position_title }}</p>
						<# } #>
					</label>
				</div>
			<# } #>
			<?php
		}

		public function get_repeat_choices() {
			return array(
				'' => esc_html__( 'Repeat', 'gp-premium' ),
				'repeat-x' => esc_html__( 'Repeat x', 'gp-premium' ),
				'repeat-y' => esc_html__( 'Repeat y', 'gp-premium' ),
				'no-repeat' => esc_html__( 'No Repeat', 'gp-premium' )
			);
		}

		public function get_size_choices() {
			return array(
				'' => esc_html__( 'Size (Auto)', 'gp-premium' ),
				'100' => esc_html__( '100% Width', 'gp-premium' ),
				'cover' => esc_html__( 'Cover', 'gp-premium' ),
				'contain' => esc_html__( 'Contain', 'gp-premium' )
			);
		}

		public function get_attachment_choices() {
			return array(
				'' => esc_html__( 'Attachment', 'gp-premium' ),
				'fixed' => esc_html__( 'Fixed', 'gp-premium' ),
				'local' => esc_html__( 'Local', 'gp-premium' ),
				'inherit' => esc_html__( 'Inherit', 'gp-premium' )
			);
		}
	}
}
