<?php
/**
 * View: Week View - Day Selector Days
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/day-selector/days.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @var array  $day             Array of data of the day
 * @var bool   $is_current_week boolean containing if the selected week is the current week.
 * @var string $today_date      Today's date in `Y-m-d`.
 * @var string $week_start_date The week start date, in `Y-m-d` format.
 *
 * @version 5.0.3
 *
 */
$selected    = 'false';
$day_classes = [ 'tribe-events-pro-week-day-selector__day' ];

// If in the current week, and today's is the day.
// Or for the rest of the weeks, if it's the first day of the week.
if (
	( $is_current_week && $today_date === $day['datetime'] )
	|| ( ! $is_current_week && $day['datetime'] === $week_start_date )
) {
	$selected      = 'true';
	$day_classes[] = 'tribe-events-pro-week-day-selector__day--active';
}

/* translators: events (plural) */
$label = sprintf( __( 'Has %s', 'tribe-events-calendar-pro' ), tribe_get_event_label_plural_lowercase() );
?>
<li class="tribe-events-pro-week-day-selector__days-list-item">
	<button
		<?php tribe_classes( $day_classes ) ?>
		aria-expanded="<?php echo esc_attr( $selected ); ?>"
		aria-selected="<?php echo esc_attr( $selected ); ?>"
		aria-controls="tribe-events-pro-week-mobile-events-day-<?php echo esc_attr( $day[ 'datetime' ] ); ?>"
		data-js="tribe-events-pro-week-day-selector-day"
	>

		<?php if ( ! empty( $day['found_events'] ) ) : ?>
			<em
				class="tribe-events-pro-week-day-selector__events-icon"
				aria-label="<?php echo esc_attr( $label ); ?>"
				title="<?php echo esc_attr( $label ); ?>"
			>
			</em>
		<?php endif; ?>

		<time class="tribe-events-pro-week-day-selector__day-datetime" datetime="<?php echo esc_attr( $day[ 'datetime' ] ); ?>">

			<span class="tribe-events-pro-week-day-selector__day-weekday tribe-common-b3">
				<?php echo esc_html( $day[ 'weekday' ] ); ?>
			</span>

			<span class="tribe-events-pro-week-day-selector__day-daynum tribe-common-h4">
				<?php echo esc_html( $day[ 'daynum' ] ); ?>
			</span>

		</time>

	</button>
</li>
