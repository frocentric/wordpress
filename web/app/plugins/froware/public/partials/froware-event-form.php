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

$events_label_singular           = tribe_get_event_label_singular();
?>

<div class="tribe-section tribe-section-datetime event-datepickers event-time eventForm">
	<div class="tribe-section-header">
		<h3><?php printf( esc_html__( ' Import %s', 'froware' ), $events_label_singular ); ?></h3>
	</div>
	<div class="tribe-section-content">
		<form method="post">
			<fieldset>
				<input id="import-event-url" name="event_url" />
				<input type="button" id="import-event" value="<?php esc_attr_e( 'Import event', 'froware' ); ?>" />
				<?php wp_nonce_field( 'import_form_nonce_action', 'import_form_nonce', false ); ?>
			</fieldset>
		</form>
	</div>
</div>
