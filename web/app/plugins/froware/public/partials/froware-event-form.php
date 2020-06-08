<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://ingenyus.com
 * @since      1.0.0
 *
 * @package    Froware
 * @subpackage Froware/public/partials
 */

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<form method="post">
	<fieldset>
		<legend><?php esc_html_e( 'Import event', 'froware' ); ?></legend>
		<input id="import-event-url" name="event_url" />
		<input type="button" id="import-event" value="<?php esc_attr_e( 'Import event', 'froware' ); ?>" />
		<?php wp_nonce_field( 'wpea_import_form_nonce_action', 'wpea_import_form_nonce', false ); ?>
	</fieldset>
</form>
