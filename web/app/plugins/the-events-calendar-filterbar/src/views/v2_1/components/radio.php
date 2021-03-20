<?php
/**
 * View: Radio Component
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-filterbar/v2_1/components/radio.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @var string  $label   Label for the radio.
 * @var string  $value   Value for the radio.
 * @var string  $id      ID of the radio.
 * @var string  $name    Name attribute for the radio.
 * @var boolean $checked Whether the radio is checked or not.
 *
 * @version 5.0.0
 *
 */
?>
<div
	class="tribe-filter-bar-c-radio tribe-common-form-control-radio"
	data-js="tribe-filter-bar-c-radio"
>
	<input
		class="tribe-common-form-control-radio__input"
		id="<?php echo esc_attr( $id ); ?>"
		name="<?php echo esc_attr( $name ); ?>"
		type="radio"
		value="<?php echo esc_attr( $value ); ?>"
		<?php checked( $checked ); ?>
		data-js="tribe-filter-bar-c-radio-input"
	/>
	<label
		class="tribe-common-form-control-radio__label"
		for="<?php echo esc_attr( $id ); ?>"
	>
		<?php echo esc_html( $label ); ?>
	</label>
</div>
