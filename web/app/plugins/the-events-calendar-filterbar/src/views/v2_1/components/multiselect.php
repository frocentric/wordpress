<?php
/**
 * View: Multiselect Component
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-filterbar/v2_1/components/multiselect.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @var string       $value   Value for the multiselect.
 * @var string       $id      ID of the multiselect.
 * @var string       $name    Name attribute for the multiselect.
 * @var array<array> $options Options for the multiselect.
 *
 * @version 5.0.0
 *
 */
$classes = [ 'tribe-filter-bar-c-multiselect' ];
if ( ! empty( $value ) ) {
	$classes[] = 'tribe-filter-bar-c-multiselect--has-selection';
}
?>
<div <?php tribe_classes( $classes ); ?>>
	<input
		class="tribe-filter-bar-c-multiselect__input"
		data-js="tribe-filter-bar-c-multiselect-input"
		id="<?php echo esc_attr( $id ); ?>"
		name="<?php echo esc_attr( $name ); ?>"
		type="hidden"
		value="<?php echo esc_attr( $value ); ?>"
		data-allow-html
		data-dropdown-css-width="false"
		data-options="<?php echo esc_attr( wp_json_encode( $options ) ); ?>"
		data-attach-container
		multiple
		style="width: 100%;" <?php /* This is required for selectWoo styling to prevent select box overflow */ ?>
	/>
</div>
