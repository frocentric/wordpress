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
				<h2>WordAi</h2>
				<div class="fz-form-group">
					<b><?php esc_html_e( 'API Status:', 'feedzy-rss-feeds' ); ?> </b> <span id="wordai_api_status" style="color:<?php echo esc_attr( $license_status_color ); ?>"><?php echo wp_kses_post( $_status ); ?></span><div> <?php echo esc_html__( 'Last check: ', 'feedzy-rss-feeds' ) . wp_kses_post( $wordai_last_check ); ?></div>
				</div>
				<div class="fz-form-group">
					<label><?php esc_html_e( 'The WordAi account email:', 'feedzy-rss-feeds' ); ?></label>
				</div>
				<div class="fz-form-group">
					<input type="text" id="wordai_username" class="fz-form-control" name="wordai_username" value="<?php echo esc_attr( $wordai_username ); ?>" placeholder="<?php echo esc_attr( __( 'WordAi Email', 'feedzy-rss-feeds' ) ); ?>"/>
				</div>
				<div class="fz-form-group">
					<label><?php esc_html_e( 'The WordAi account API key:', 'feedzy-rss-feeds' ); ?></label>
				</div>
				<div class="fz-form-group fz-input-group">
					<input type="password" id="wordai_pass" class="fz-form-control" name="wordai_pass" value="<?php echo esc_attr( $wordai_pass ); ?>" placeholder="<?php echo esc_attr( __( 'WordAI API key', 'feedzy-rss-feeds' ) ); ?>"/>
					<div class="fz-input-group-btn">
						<button id="check_wordai_api" type="button" class="fz-btn fz-btn-submit fz-btn-activate" onclick="return ajaxUpdate();"><?php esc_html_e( 'Check & Save', 'feedzy-rss-feeds' ); ?></button>
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
			jQuery( '#check_wordai_api' ).html('<?php esc_html_e( 'Check & Save', 'feedzy-rss-feeds' ); ?>');
			location.reload();
		}, 'json');

		return false;
	};
</script>
