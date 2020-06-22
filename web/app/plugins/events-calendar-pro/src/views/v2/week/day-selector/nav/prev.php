<?php
/**
 * View: Week View - Previous Button
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/day-selector/nav/prev.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @version 5.0.1
 *
 */
?>
<li class="tribe-events-pro-week-day-selector__nav-list-item tribe-events-pro-week-day-selector__nav-list-item--prev">
	<a
		href="<?php echo esc_url( $link ); ?>"
		rel="prev"
		class="tribe-events-pro-week-day-selector__prev"
		data-js="tribe-events-view-link"
		aria-label="<?php esc_attr_e( 'Previous week', 'tribe-events-calendar-pro' ); ?>"
		title="<?php esc_attr_e( 'Previous week', 'tribe-events-calendar-pro' ); ?>"
	>
		<span class="tribe-common-a11y-visual-hide">
			<?php esc_html_e( 'Previous week', 'tribe-events-calendar-pro' ); ?>
		</span>
	</a>
</li>
