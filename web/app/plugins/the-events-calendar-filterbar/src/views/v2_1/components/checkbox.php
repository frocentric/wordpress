<?php
/**
 * View: Checkbox Component
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-filterbar/v2_1/components/checkbox.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @var string  $label   Label for the checkbox.
 * @var string  $value   Value for the checkbox.
 * @var string  $id      ID of the checkbox.
 * @var string  $name    Name attribute for the checkbox.
 * @var boolean $checked Whether the checkbox is checked or not.
 *
 * @version 5.0.0
 *
 */
?>
<div
	class="tribe-filter-bar-c-checkbox tribe-common-form-control-checkbox"
	data-js="tribe-filter-bar-c-checkbox"
>
	<input
		class="tribe-common-form-control-checkbox__input"
		id="<?php echo esc_attr( $id ); ?>"
		name="<?php echo esc_attr( $name ); ?>"
		type="checkbox"
		value="<?php echo esc_attr( $value ); ?>"
		<?php checked( $checked ); ?>
		data-js="tribe-filter-bar-c-checkbox-input"
	/>
	<label
		class="tribe-common-form-control-checkbox__label"
		for="<?php echo esc_attr( $id ); ?>"
	>
		<?php echo esc_html( $label ); ?>
	</label>
</div>
