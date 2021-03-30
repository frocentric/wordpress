<?php
/**
 * View: Week View - Grid Header
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/grid-header.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 * @var array $day Array of data for the day.
 * @var string $today_date Today's date in the `Y-m-d` format.
 */
$classes = [
	'tribe-events-pro-week-grid__header-column'          => true,
	'tribe-events-pro-week-grid__header-column--current' => $today_date === $day['datetime'],
];
?>
<div
	<?php tribe_classes( $classes ); ?>
	role="columnheader"
	aria-label="<?php echo esc_attr( $day[ 'full_date' ] ); ?>"
>
	<h3 class="tribe-events-pro-week-grid__header-column-title">
		<time
			class="tribe-events-pro-week-grid__header-column-datetime"
			datetime="<?php echo esc_attr( $day[ 'datetime' ] ); ?>"
		>
			<span class="tribe-events-pro-week-grid__header-column-weekday tribe-common-h8 tribe-common-h--alt">
				<?php echo esc_html( $day[ 'weekday' ] ); ?>
			</span>
			<span class="tribe-events-pro-week-grid__header-column-daynum tribe-common-h4">
				<?php if ( ! empty( $day['found_events'] ) ) : ?>
					<a
						class="tribe-events-pro-week-grid__header-column-daynum-link"
						href="<?php echo esc_url( $day['day_url'] ); ?>"
					>
						<?php echo esc_html( $day[ 'daynum' ] ); ?>
					</a>
				<?php else : ?>
					<?php echo esc_html( $day[ 'daynum' ] ); ?>
				<?php endif; ?>
			</span>
		</time>
	</h3>
</div>
