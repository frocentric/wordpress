<?php
/**
 * Admin View: Widget Venue Dropdown Input Component
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/widgets/components/venue-dropdown.php
 *
 * See more documentation about our views templating system.
 *
 * @link    https://evnt.is/1aiy
 *
 * @version 5.3.0
 *
 * @var string     $label       Label for the venue dropdown input.
 * @var string     $id          ID of the venue dropdown input.
 * @var string     $name        Name attribute for the venue dropdown input.
 * @var string     $disabled    The list of chosen items for select2 to disable.
 * @var string|int $selected    The selected option id.
 * @var string     $placeholder The input placeholder.
 */
// Check for the Venue ID here and if none use the the default.
// It is done here to prevent overwriting a user selected venue.
if ( ! empty( $this->context['venue_ID'] ) ) {
	$selected = $this->context['venue_ID'];
}
$selected_value = empty( $selected ) ? - 1 : $selected;
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
			class="widefat tribe-widget-form-control__input calendar-widget-venue-filter tribe-widget-select2"
			placeholder="<?php echo esc_attr( $placeholder ); ?>"
			data-selected="<?php echo esc_attr( $selected ); ?>"
			data-source="venues"
			data-prevent-clear
	>
		<option selected="selected" value="<?php echo esc_attr( $selected_value ); ?>">
			<?php echo esc_html( $selected_text ); ?>
		</option>
	</select>
</div>
