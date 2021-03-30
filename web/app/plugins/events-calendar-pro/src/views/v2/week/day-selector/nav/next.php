<?php
/**
 * View: Week View - Next Button
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/day-selector/nav/next.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.2.0
 *
 */
?>
<li class="tribe-events-pro-week-day-selector__nav-list-item tribe-events-pro-week-day-selector__nav-list-item--next">
	<a
		href="<?php echo esc_url( $link ); ?>"
		rel="next"
		class="tribe-events-pro-week-day-selector__next"
		data-js="tribe-events-view-link"
		aria-label="<?php esc_attr_e( 'Next week', 'tribe-events-calendar-pro' ); ?>"
		title="<?php esc_attr_e( 'Next week', 'tribe-events-calendar-pro' ); ?>"
	>
		<span class="tribe-common-a11y-visual-hide">
			<?php esc_html_e( 'Next week', 'tribe-events-calendar-pro' ); ?>
		</span>
		<?php $this->template( 'components/icons/caret-right', [ 'classes' => [ 'tribe-events-pro-week-day-selector__next-icon-svg' ] ] ); ?>
	</a>
</li>
