<?php
$spinnerchief_key = '';
if ( isset( $this->settings['spinnerchief_key'] ) ) {
	$spinnerchief_key = $this->settings['spinnerchief_key'];
}

$_status                 = 'Invalid';
$spinnerchief_licence    = '';
$licence_status_color    = '#F00';
$spinnerchief_last_check = __( 'Never', 'feedzy-rss-feeds' );
if ( isset( $this->settings['spinnerchief_licence'] ) ) {
	$spinnerchief_licence = $this->settings['spinnerchief_licence'];
	if ( 'yes' === $spinnerchief_licence ) {
		$_status              = 'Valid';
		$licence_status_color = '#62c370';
	}
}
if ( isset( $this->settings['spinnerchief_last_check'] ) ) {
	$spinnerchief_last_check = $this->settings['spinnerchief_last_check'];
}
if ( isset( $this->settings['spinnerchief_message'] ) && ! empty( $this->settings['spinnerchief_message'] ) ) {
	$_status = $this->settings['spinnerchief_message'];
}

?>
<div class="fz-form-wrap">
	<div class="form-block">
		<div class="fz-form-group">
			<label class="form-label"><?php esc_html_e( 'SpinnerChief API key', 'feedzy-rss-feeds' ); ?></label>
			<div class="fz-input-group">
				<div class="fz-input-group-left">
					<input type="password" id="spinnerchief_key" class="form-control" name="spinnerchief_key" value="<?php echo esc_attr( $spinnerchief_key ); ?>" placeholder="<?php esc_attr_e( 'SpinnerChief API Key', 'feedzy-rss-feeds' ); ?>"/>
					<div class="help-text"><?php echo wp_kses_post( wp_sprintf( __( 'API Status: <span style="color:%1$s;">%2$s</span> | Last check: %3$s', 'feedzy-rss-feeds' ), $licence_status_color, $_status, $spinnerchief_last_check ) ); ?></div>
				</div>
				<div class="fz-input-group-right">
					<button id="check_spinnerchief_api" type="button" class="btn btn-outline-primary" onclick="return ajaxUpdate();"><?php echo esc_html_x( 'Validate connection', 'Check and save action button', 'feedzy-rss-feeds' ); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">
	function ajaxUpdate() {

		var spinnerchief_data = {
			'spinnerchief_key': jQuery( '#spinnerchief_key' ).val(),
		}

		var data = {
			'action': 'update_settings_page',
			'feedzy_settings': spinnerchief_data,
			'_wpnonce': '<?php echo esc_js( wp_create_nonce( 'update_settings_page' ) ); ?>',
		};

		jQuery( '#check_spinnerchief_api' ).prop( 'disabled', true );
		jQuery( '#check_spinnerchief_api' ).html('<?php echo esc_html__( 'Checking ...', 'feedzy-rss-feeds' ); ?>');
		jQuery.post( ajaxurl, data, function( response ) {
			jQuery( '#check_spinnerchief_api' ).prop( 'disabled', false );
			jQuery( '#check_spinnerchief_api' ).html('<?php echo esc_html__( 'Validate connection', 'feedzy-rss-feeds' ); ?>');
			location.reload();
		}, 'json');

		return false;
	};
</script>
