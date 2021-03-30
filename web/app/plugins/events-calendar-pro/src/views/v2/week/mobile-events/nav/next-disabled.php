<?php
/**
 * View: Week View Nav Disabled Next Button
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/mobile-events/nav/next-disabled.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.2.0
 *
 */
?>
<li class="tribe-events-c-nav__list-item tribe-events-c-nav__list-item--next">
	<button
		class="tribe-events-c-nav__next tribe-common-b2"
		aria-label="<?php esc_attr_e( 'Next week', 'tribe-events-calendar-pro' ); ?>"
		title="<?php esc_attr_e( 'Next week', 'tribe-events-calendar-pro' ); ?>"
		disabled
	>
		<?php esc_html_e( 'Next', 'tribe-events-calendar-pro' ); ?>
		<?php $this->template( 'components/icons/caret-right', [ 'classes' => [ 'tribe-events-c-nav__next-icon-svg' ] ] ); ?>
	</button>
</li>
