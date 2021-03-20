<?php
/**
 * View: Actions
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-filterbar/v2_1/filter-bar/actions.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @var string $breakpoint_pointer String we use as pointer to the current view we are setting up with breakpoints.
 * @var string $filterbar_state    Default state of the filter bar, `open` or `closed`.
 *
 * @version 5.0.0
 */

$aria_expanded = 'closed' === $filterbar_state ? 'false' : 'true';
?>
<div class="tribe-filter-bar__actions">
	<button
		class="tribe-filter-bar__action-done tribe-common-c-btn-border tribe-common-c-btn-border--secondary"
		data-js="tribe-filter-bar__action-done"
		type="button"
		aria-controls="tribe-filter-bar--<?php echo esc_attr( $breakpoint_pointer ); ?>"
		aria-expanded="<?php echo esc_attr( $aria_expanded ); ?>"
	>
		<?php esc_html_e( 'Done', 'tribe-events-filter-view' ); ?>
	</button>
	<?php $this->template( 'components/clear-button', [ 'classes' => [ 'tribe-filter-bar__action-clear' ] ] ); ?>
</div>
