<?php
/**
 * View: Week View Nav Next Button
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/mobile-events/nav/next.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @var string $link The URL to the next page, if any, or an empty string.
 *
 * @version 5.2.0
 *
 */
?>
<li class="tribe-events-c-nav__list-item tribe-events-c-nav__list-item--next">
	<a
		href="<?php echo esc_url( $link ); ?>"
		rel="next"
		class="tribe-events-c-nav__next tribe-common-b2"
		data-js="tribe-events-view-link"
		aria-label="<?php esc_attr_e( 'Next week', 'tribe-events-calendar-pro' ); ?>"
		title="<?php esc_attr_e( 'Next week', 'tribe-events-calendar-pro' ); ?>"
	>
		<?php esc_html_e( 'Next', 'tribe-events-calendar-pro' ); ?>
		<?php $this->template( 'components/icons/caret-right', [ 'classes' => [ 'tribe-events-c-nav__next-icon-svg' ] ] ); ?>
	</a>
</li>
