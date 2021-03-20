<?php
/**
 * View: Top Bar Hide Recurring Events Toggle
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/recurrence/hide-recurring.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.0.3
 *
 */
?>
<div class="tribe-common-form-control-toggle tribe-events-c-top-bar__hide-recurring">
	<input
		class="tribe-common-form-control-toggle__input tribe-events-c-top-bar__hide-recurring-input"
		id="hide-recurring"
		name="hide-recurring"
		type="checkbox"
		data-js="tribe-events-pro-top-bar-toggle-recurrence"
		<?php echo checked( tribe_events_template_var( [ 'bar', 'hide_recurring' ], false ) ) ?>
		autocomplete="off"
	/>
	<label
		class="tribe-common-form-control-toggle__label tribe-events-c-top-bar__hide-recurring-label"
		for="hide-recurring"
	>
		<?php
		echo esc_html(
			sprintf(
				/* translators: %s: Events (plural) */
				__( 'Hide Recurring %s', 'tribe-events-calendar-pro' ),
				tribe_get_event_label_plural()
			)
		);
		?>
	</label>
</div>
