<?php
$wordai_username = '';
if ( isset( $this->settings['wordai_username'] ) ) {
	$wordai_username = $this->settings['wordai_username'];
}

$wordai_pass = '';
if ( isset( $this->settings['wordai_hash'] ) ) {
	$wordai_pass = $this->settings['wordai_hash'];
}

$_status              = 'Invalid';
$wordai_licence       = '';
$license_status_color = 'red';
$wordai_last_check    = __( 'Never', 'feedzy-rss-feeds' );
if ( isset( $this->settings['wordai_licence'] ) ) {
	$wordai_licence = $this->settings['wordai_licence'];
	if ( 'yes' === $wordai_licence ) {
		$_status              = 'Valid';
		$license_status_color = '#62c370';
	}
}
if ( isset( $this->settings['wordai_last_check'] ) ) {
	$wordai_last_check = $this->settings['wordai_last_check'];
}
if ( isset( $this->settings['wordai_message'] ) && ! empty( $this->settings['wordai_message'] ) ) {
	$_status = $this->settings['wordai_message'];
}
?>			
<div class="fz-form-wrap">
	<div class="form-block">
		<div class="fz-form-group mb-24">
			<label class="form-label"><?php esc_html_e( 'The WordAi account email:', 'feedzy-rss-feeds' ); ?></label>
			<input type="text" class="form-control" id="wordai_username" name="wordai_username" value="<?php echo esc_attr( $wordai_username ); ?>" placeholder="<?php echo esc_attr( __( 'WordAi Email', 'feedzy-rss-feeds' ) ); ?>"/>
		</div>
		<div class="fz-form-group">
			<label class="form-label"><?php esc_html_e( 'The WordAi account API key:', 'feedzy-rss-feeds' ); ?></label>
			<div class="fz-input-group">
				<div class="fz-input-group-left">
					<input type="password" id="wordai_pass" class="form-control" name="wordai_pass" value="<?php echo esc_attr( $wordai_pass ); ?>" placeholder="<?php echo esc_attr( __( 'WordAI API key', 'feedzy-rss-feeds' ) ); ?>"/>
					<div class="help-text"><?php echo wp_kses_post( wp_sprintf( __( 'API Status: %1$s | Last check: %2$s', 'feedzy-rss-feeds' ), $_status, $wordai_last_check ) ); ?></div>
				</div>
				<div class="fz-input-group-right">
					<div class="fz-input-group-btn">
						<button id="check_wordai_api" type="button" class="btn btn-outline-primary" onclick="return ajaxUpdate();"><?php esc_html_e( 'Validate connection', 'feedzy-rss-feeds' ); ?></button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	function ajaxUpdate() {

		var wordai_data = {
			'wordai_username': jQuery( '#wordai_username' ).val(),
			'wordai_pass': jQuery( '#wordai_pass' ).val(),
		}

		var data = {
			'action': 'update_settings_page',
			'feedzy_settings': wordai_data,
			'_wpnonce': '<?php echo esc_js( wp_create_nonce( 'update_settings_page' ) ); ?>',
		};

		jQuery( '#check_wordai_api' ).prop( 'disabled', true );
		jQuery( '#check_wordai_api' ).html('<?php esc_html_e( 'Checking ...', 'feedzy-rss-feeds' ); ?>');
		jQuery.post( ajaxurl, data, function( response ) {
			jQuery( '#check_wordai_api' ).prop( 'disabled', false );
			jQuery( '#check_wordai_api' ).html('<?php esc_html_e( 'Validate connection', 'feedzy-rss-feeds' ); ?>');
			location.reload();
		}, 'json');

		return false;
	};
</script>
