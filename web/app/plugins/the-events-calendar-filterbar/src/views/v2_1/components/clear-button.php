<?php
/**
 * View: Clear Button
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-filterbar/v2_1/components/clear-button.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @var array<string> $classes Additional classes to add to the button.
 *
 * @version 5.0.0
 *
 */
$button_classes = [ 'tribe-filter-bar-c-clear-button', 'tribe-common-c-btn-border', 'tribe-common-c-btn-border--alt' ];

if ( ! empty( $classes ) ) {
	$button_classes = array_merge( $button_classes, $classes );
}
?>
<button
	<?php tribe_classes( $button_classes ); ?>
	type="reset"
	data-js="tribe-filter-bar-c-clear-button"
>
	<?php $this->template( 'components/icons/reset', [ 'classes' => [ 'tribe-filter-bar-c-clear-button__reset-icon' ] ] ); ?>
	<span class="tribe-filter-bar-c-clear-button__text">
		<?php esc_html_e( 'Clear', 'tribe-events-filter-view' ); ?>
	</span>
</button>
