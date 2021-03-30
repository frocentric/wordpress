<?php
/**
 * View: Week View - Multiday Events Row Header Toggle Button
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/grid-body/multiday-events-row-header/multiday-events-row-header-toggle.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.2.0
 *
 * @var string $multiday_toggle_controls A space-separated list of entries for the `aria-controls` attribute.
 */

?>
<button
	class="tribe-events-pro-week-grid__multiday-toggle-button"
	aria-controls="<?php echo esc_attr( $multiday_toggle_controls ) ?>"
	aria-expanded="false"
	aria-selected="false"
	data-js="tribe-events-pro-week-multiday-toggle-button"
>
	<span class="tribe-common-a11y-visual-hide">
		<?php
		echo esc_html(
			sprintf(
				/* translators: %s: events (plural) */
				__( 'Toggle multiday %s', 'tribe-events-calendar-pro' ),
				tribe_get_event_label_plural_lowercase()
			)
		);
		?>
	</span>
	<?php $this->template( 'components/icons/caret-down', [ 'classes' => [ 'tribe-events-pro-week-grid__multiday-toggle-button-icon-svg' ] ] ); ?>
</button>
