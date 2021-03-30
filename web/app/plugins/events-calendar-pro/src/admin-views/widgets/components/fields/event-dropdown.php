<?php
/**
 * Admin View: Widget Event Dropdown Input Component
 *
 * Administration Views cannot be overwritten by your theme.
 *
 * See more documentation about our views templating system.
 *
 * @link    http://m.tri.be/1aiy
 *
 * @since 5.2.0 Introduced.
 * @since 5.3.0 Moved to `components/fields`.
 *
 * @version 5.3.0
 *
 * @var string     $label       Label for the event dropdown input.
 * @var string     $id          ID of the event dropdown input.
 * @var string     $name        Name attribute for the event dropdown input.
 * @var string     $disabled    The list of chosen items for select2 to disable.
 * @var string|int $selected    The selected option id.
 * @var string     $placeholder The input placeholder.
 */
$selected       = $this->context['event'];
$selected_value = empty( $selected ) ? -1 : $selected;
$selected_text  = empty( $selected ) ? $placeholder : get_the_title( $selected );
?>
<div
	class="tribe-widget-form-control tribe-widget-form-control--multiselect"
>
	<label
		class="tribe-widget-form-control__label"
		for="<?php echo esc_attr( $id ); ?>"
	>
		<?php echo esc_html( $label ); ?>
	</label>
	<select
		id="<?php echo esc_attr( $id ); ?>"
		name="<?php echo esc_attr( $name ); ?>"
		class="widefat tribe-widget-form-control__input calendar-widget-event-filter tribe-widget-select2"
		placeholder="<?php echo esc_attr( $placeholder ); ?>"
		data-selected="<?php echo esc_attr( $selected ); ?>"
		data-source="events"
		data-prevent-clear
	>
		<option selected="selected" value="<?php echo esc_attr( $selected_value ); ?>">
			<?php echo esc_html( $selected_text ); ?>
		</option>
	</select>
</div>
