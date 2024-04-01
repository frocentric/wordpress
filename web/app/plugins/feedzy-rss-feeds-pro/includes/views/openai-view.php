<?php
$openai_api_key = '';
if ( isset( $this->settings['openai_api_key'] ) ) {
	$openai_api_key = $this->settings['openai_api_key'];
}

$openai_api_model = '';
if ( isset( $this->settings['openai_api_model'] ) ) {
	$openai_api_model = $this->settings['openai_api_model'];
}

$_status              = 'Invalid';
$openai_licence       = '';
$license_status_color = '#F00';
$openai_last_check    = __( 'Never', 'feedzy-rss-feeds' );
if ( isset( $this->settings['openai_licence'] ) ) {
	$openai_licence = $this->settings['openai_licence'];
	if ( 'yes' === $openai_licence ) {
		$_status              = 'Valid';
		$license_status_color = '#62c370';
	}
}
if ( isset( $this->settings['openai_last_check'] ) ) {
	$openai_last_check = $this->settings['openai_last_check'];
}
if ( isset( $this->settings['openai_message'] ) && ! empty( $this->settings['openai_message'] ) ) {
	$_status = $this->settings['openai_message'];
}
?>			
<div class="fz-form-wrap">
	<div class="form-block">
		<div class="fz-form-group mb-24">
			<label class="form-label"><?php esc_html_e( 'The OpenAI account API key:', 'feedzy-rss-feeds' ); ?></label>
			<input type="password" class="form-control" id="openai_api_key" name="openai_api_key" value="<?php echo esc_attr( $openai_api_key ); ?>" placeholder="<?php echo esc_attr( __( 'API key', 'feedzy-rss-feeds' ) ); ?>"/>
		</div>
		<div class="fz-form-group">
			<label class="form-label"><?php esc_html_e( 'The OpenAI model:', 'feedzy-rss-feeds' ); ?></label>
			<div class="fz-input-group">
				<div class="fz-input-group-left">
					<select name="openai_api_model" id="openai_api_model" class="form-control fz-select-control">
						<?php
						$openai_models = apply_filters(
							'feedzy_openai_models',
							array(
								'gpt-3.5-turbo-instruct',
								'text-davinci-002',
								'text-davinci-003',
								'text-curie-001',
								'text-babbage-001',
								'text-ada-001',
								'davinci',
							),
						);
						foreach ( $openai_models as $key => $openai_model ) {
							echo '<option value="' . esc_attr( $openai_model ) . '" ' . selected( $openai_api_model, $openai_model ) . '>' . esc_html( $openai_model ) . '</option>';
						}
						?>
					</select>
					<div class="help-text"><?php echo wp_kses_post( wp_sprintf( __( 'API Status: <span style="color:%1$s;">%2$s</span> | Last check: %3$s', 'feedzy-rss-feeds' ), $license_status_color, $_status, $openai_last_check ) ); ?></div>
				</div>
				<div class="fz-input-group-right">
					<div class="fz-input-group-btn">
						<button id="check_openai_api" type="button" class="btn btn-outline-primary" onclick="return ajaxUpdate();"><?php esc_html_e( 'Validate connection', 'feedzy-rss-feeds' ); ?></button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	function ajaxUpdate() {

		var openai_data = {
			'openai_api_key': jQuery( '#openai_api_key' ).val(),
			'openai_api_model': jQuery( '#openai_api_model' ).val(),
		}

		var data = {
			'action': 'update_settings_page',
			'feedzy_settings': openai_data,
			'_wpnonce': '<?php echo esc_js( wp_create_nonce( 'update_settings_page' ) ); ?>',
		};

		jQuery( '#check_openai_api' ).prop( 'disabled', true );
		jQuery( '#check_openai_api' ).html('<?php esc_html_e( 'Checking ...', 'feedzy-rss-feeds' ); ?>');
		jQuery.post( ajaxurl, data, function( response ) {
			jQuery( '#check_openai_api' ).prop( 'disabled', false );
			jQuery( '#check_openai_api' ).html('<?php esc_html_e( 'Validate connection', 'feedzy-rss-feeds' ); ?>');
			location.reload();
		}, 'json');

		return false;
	};
</script>
