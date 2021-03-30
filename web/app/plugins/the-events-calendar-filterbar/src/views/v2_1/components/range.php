<?php
/**
 * View: Range Component
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-filterbar/v2_1/components/range.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @var string  $label Label for the range.
 * @var string  $value Value for the range.
 * @var string  $id    ID of the range.
 * @var string  $name  Name attribute for the range.
 * @var string  $min   Min value for the range.
 * @var string  $max   Max value for the range.
 *
 * @version 5.0.0
 *
 */
?>
<div
	class="tribe-filter-bar-c-range"
>
	<label
		class="tribe-filter-bar-c-range__label"
		for="<?php echo esc_attr( $id ); ?>"
	>
		<?php echo esc_html( $label ); ?>
	</label>
	<input
		class="tribe-filter-bar-c-range__input"
		id="<?php echo esc_attr( $id ); ?>"
		name="<?php echo esc_attr( $name ); ?>"
		type="hidden"
		value="<?php echo esc_attr( $value ); ?>"
	/>
	<div
		class="tribe-filter-bar-c-range__slider"
		data-js="tribe-filter-bar-c-range-slider"
		data-min="<?php echo esc_attr( $min ); ?>"
		data-max="<?php echo esc_attr( $max ); ?>"
	>
	</div>
</div>
