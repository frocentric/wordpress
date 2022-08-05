<?php
$spinnerchief_username = '';
if ( isset( $this->settings['spinnerchief_username'] ) ) {
	$spinnerchief_username = $this->settings['spinnerchief_username'];
}

$spinnerchief_password = '';
if ( isset( $this->settings['spinnerchief_password'] ) ) {
	$spinnerchief_password = $this->settings['spinnerchief_password'];
}

$spinnerchief_key = '';
if ( isset( $this->settings['spinnerchief_key'] ) ) {
	$spinnerchief_key = $this->settings['spinnerchief_key'];
}

$_status                 = 'Invalid';
$spinnerchief_licence    = '';
$licence_status_color    = 'red';
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
		<div class="fz-form-row">
			<div class="fz-form-col-6">
				<div class="fz-form-group">
					<label class="form-label"><?php esc_html_e( 'The SpinnerChief username:', 'feedzy-rss-feeds' ); ?></label>
					<input type="text" class="form-control" id="spinnerchief_username" name="spinnerchief_username" value="<?php echo esc_attr( $spinnerchief_username ); ?>" placeholder="<?php echo esc_html_x( 'SpinnerChief Username', 'Username for SpinnerChief service', 'feedzy-rss-feeds' ); ?>"/>
				</div>
			</div>
			<div class="fz-form-col-6">
				<div class="fz-form-group">
					<label class="form-label"><?php echo esc_html_x( 'The SpinnerChief password:', 'Password for SpinnerChief service', 'feedzy-rss-feeds' ); ?></label>
					<input type="password" class="form-control" id="spinnerchief_password" name="spinnerchief_password" value="<?php echo esc_attr( $spinnerchief_password ); ?>" placeholder="<?php echo esc_html_x( 'SpinnerChief Password', 'Password for SpinnerChief service', 'feedzy-rss-feeds' ); ?>"/>
				</div>
			</div>
		</div>
		<div class="fz-form-group">
			<label class="form-label"><?php esc_html_e( 'SpinnerChief API key', 'feedzy-rss-feeds' ); ?></label>
			<div class="fz-input-group">
				<div class="fz-input-group-left">
					<input type="password" id="spinnerchief_key" class="form-control" name="spinnerchief_key" value="<?php echo esc_attr( $spinnerchief_key ); ?>" placeholder="<?php esc_attr_e( 'SpinnerChief API Key', 'feedzy-rss-feeds' ); ?>"/>
					<div class="help-text"><?php echo wp_kses_post( wp_sprintf( __( 'API Status: %1$s | Last check: %2$s', 'feedzy-rss-feeds' ), $_status, $spinnerchief_last_check ) ); ?></div>
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
			'spinnerchief_username': jQuery( '#spinnerchief_username' ).val(),
			'spinnerchief_password': jQuery( '#spinnerchief_password' ).val(),
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
