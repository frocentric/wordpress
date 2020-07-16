<?php
/**
 * View: Week View - Multiday Events - More Events
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/grid-body/multiday-events-day/more-events.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @version 5.0.0
 *
 * @var string $multiday_toggle_controls A space-separated list of entries for the `aria-controls` attribute.
 * @var int    $more_events              The number of events not showing in the stack due to the toggle.
 */

$more_text = sprintf(
	_n( '+1 more', '+ %s more', $more_events, 'tribe-events-calendar-pro' ),
	number_format_i18n( $more_events )
);
?>
<div class="tribe-events-pro-week-grid__multiday-more-events" data-js="tribe-events-pro-week-multiday-more-events-wrapper">
	<button
		class="tribe-events-pro-week-grid__multiday-more-events-button tribe-common-h8 tribe-common-h--alt tribe-common-anchor-thin"
		data-js="tribe-events-pro-week-multiday-more-events"
		aria-controls="<?php echo esc_attr( $multiday_toggle_controls ) ?>"
		aria-expanded="false"
		aria-selected="false"
	><?php echo esc_html( $more_text ) ?></button>
</div>
