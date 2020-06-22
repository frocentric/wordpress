<?php
/**
 * Mini Calendar Widget Grid Template
 * This file loads the mini calendar widget grid
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/pro/widgets/mini-calendar/grid.php
 *
 * @package TribeEventsCalendar
 *
 * @version 4.4.32
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
} ?>

<?php
$days_of_week = tribe_events_get_days_of_week( 'min' );
$week         = 0;
$mini_cal_widget_id = Tribe__Events__Utils__Id_Generator::generate_id( 'tribe-mini-calendar-month', 'tribe-mini-calendar-month' );

?>
<div class="tribe-mini-calendar-grid-wrapper">
	<table class="tribe-mini-calendar" <?php tribe_events_the_mini_calendar_header_attributes() ?>>
		<?php do_action( 'tribe_events_mini_cal_before_header' ); ?>
		<thead class="tribe-mini-calendar-nav">
		<tr>
			<td colspan="7">
				<div>
					<?php tribe_events_the_mini_calendar_prev_link() ?>
					<span id="<?php echo esc_attr( $mini_cal_widget_id ) ?>"><?php tribe_events_the_mini_calendar_title() ?></span>
					<?php tribe_events_the_mini_calendar_next_link() ?>
					<img id="ajax-loading-mini" src="<?php echo esc_url( tribe_events_resource_url( 'images/tribe-loading.gif' ) ); ?>" alt="loading..." />
				</div>
			</td>
		</tr>
		<?php do_action( 'tribe_events_mini_cal_after_header' ); ?>
		<?php do_action( 'tribe_events_mini_cal_before_the_grid' ); ?>
		<tr>
			<?php foreach ( $days_of_week as $day ) : ?>
				<th class="tribe-mini-calendar-dayofweek"><?php echo $day ?></th>
			<?php endforeach; ?>

		</tr>
		</thead>

		<tbody>

		<tr>
			<?php while ( tribe_events_have_month_days() ) :
			tribe_events_the_month_day(); ?>
			<?php if ( $week != tribe_events_get_current_week() ) :
			$week ++; ?>
		</tr>
		<tr>
			<?php endif; ?>
			<td class="<?php tribe_events_the_month_day_classes() ?>">
				<?php tribe_get_template_part( 'pro/widgets/mini-calendar/single-day' ) ?>
			</td>
			<?php endwhile; ?>
		</tr>
		</tbody>
		<?php do_action( 'tribe_events_mini_cal_after_the_grid' ); ?>
	</table>
</div> <!-- .tribe-mini-calendar-grid-wrapper -->
