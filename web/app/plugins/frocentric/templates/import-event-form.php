<?php
/**
 * Renders a form to import an event
 *
 * @link       https://hq.frocentric.io
 * @since      1.0.0
 *
 * @package    Frocentric
 * @subpackage Frocentric/templates
 */

$events_label_singular = tribe_get_event_label_singular();
?>

<div class="tribe-section tribe-section-datetime event-datepickers event-time eventForm">
	<div class="tribe-section-header">
		<h3>
		<?php
			// translators: %s is singular event label
			printf( esc_html__( ' Import %s', 'froware' ), $events_label_singular );
		?>
		</h3>
	</div>
	<div class="tribe-section-content">
		<form method="post">
			<fieldset>
				<legend><?php esc_html_e( 'Import event', 'froware' ); ?></legend>
				<input id="import-event-url" name="event_url" />
				<input type="button" id="import-event" value="<?php esc_attr_e( 'Import event', 'froware' ); ?>" />
				<?php wp_nonce_field( 'tribe-aggregator-save-import', 'tribe_aggregator_nonce', false ); ?>
				<div class="validation-message"></div>
			</fieldset>
		</form>
	</div>
</div>
