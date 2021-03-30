<?php
/**
 * View: Dropdown Component
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-filterbar/v2_1/components/dropdown.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @var string       $value   Value for the dropdown.
 * @var string       $id      ID of the dropdown.
 * @var string       $name    Name attribute for the dropdown.
 * @var array<array> $options Options for the dropdown.
 *
 * @version 5.0.0
 *
 */

$classes = [ 'tribe-filter-bar-c-dropdown' ];
if ( ! empty( $value ) ) {
	$classes[] = 'tribe-filter-bar-c-dropdown--has-selection';
}
?>
<div <?php tribe_classes( $classes ); ?>>
	<input
		class="tribe-filter-bar-c-dropdown__input"
		id="<?php echo esc_attr( $id ); ?>"
		data-js="tribe-filter-bar-c-dropdown-input"
		name="<?php echo esc_attr( $name ); ?>"
		type="hidden"
		value="<?php echo esc_attr( $value ); ?>"
		data-allow-html
		data-dropdown-css-width="false"
		data-options="<?php echo esc_attr( wp_json_encode( $options ) ); ?>"
		data-attach-container
		placeholder="<?php esc_attr_e( 'Select', 'tribe-events-filter-view' ); ?>"
		style="width: 100%;" <?php /* This is required for selectWoo styling to prevent select box overflow */ ?>
	/>
</div>
