<?php
/**
 * View: Filter Button Component
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-filterbar/v2_1/components/events-bar/filter-button.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @var string $breakpoint_pointer String we use as pointer to the current view we are setting up with breakpoints.
 * @var string $filterbar_state    Current state of the entire Filter Bar, either `open` or `closed`.
 *
 * @version 5.0.0
 *
 */

$button_classes = [ 'tribe-events-c-events-bar__filter-button' ];

if ( empty( $filterbar_state ) || 'closed' === $filterbar_state ) {
	$button_text   = __( 'Show filters', 'tribe-events-filter-view' );
	$aria_expanded = 'false';
} else {
	$button_classes[] = 'tribe-events-c-events-bar__filter-button--active';
	$button_text      = __( 'Hide filters', 'tribe-events-filter-view' );
	$aria_expanded    = 'true';
}
?>
<div class="tribe-events-c-events-bar__filter-button-container">
	<button
		<?php tribe_classes( $button_classes ); ?>
		aria-controls="tribe-filter-bar--<?php echo esc_attr( $breakpoint_pointer ); ?>"
		aria-expanded="<?php echo esc_attr( $aria_expanded ); ?>"
		data-js="tribe-events-filter-button"
	>
		<?php $this->template( 'components/icons/filter', [ 'classes' => [ 'tribe-events-c-events-bar__filter-button-icon' ] ] ); ?>
		<span class="tribe-events-c-events-bar__filter-button-text tribe-common-b2 tribe-common-a11y-visual-hide">
			<?php echo esc_html( $button_text ); ?>
		</span>
	</button>
</div>
