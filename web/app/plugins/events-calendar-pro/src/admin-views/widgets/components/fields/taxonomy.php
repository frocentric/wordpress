<?php
/**
 * Admin View: Widget Taxonomy Input Component
 *
 * Administration Views cannot be overwritten by your theme.
 *
 * See more documentation about our views templating system.
 *
 * @link    https://evnt.is/1aiy
 *
 * @since 5.2.0 Introduced.
 * @since 5.3.0 Moved to `components/fields`.
 *
 * @version 5.3.0
 *
 * @var string $label       Label for the taxonomy input.
 * @var string $id          ID of the taxonomy input.
 * @var string $name        Name attribute for the taxonomy input.
 * @var string $disabled    The list of chosen items for select2 to disable.
 * @var string $placeholder The input placeholder.
 */
use \Tribe\Events\Pro\Views\V2\Widgets\Taxonomy_Filter;

// Makes sure we dont have any notices from disabled not existing.
if ( empty( $disabled ) ) {
	$disabled = tribe( Taxonomy_Filter::class )->get_disabled_terms_on_widget( $widget_obj );
}

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
		class="widefat tribe-widget-form-control__input calendar-widget-add-filter tribe-widget-select2"
		placeholder="<?php echo esc_attr( $placeholder ); ?>"
		data-disabled="<?php echo esc_attr( $disabled ); ?>"
		data-source="terms"
		data-hide-search
		data-prevent-clear
	>
		<option selected="selected" value="-1"><?php esc_html_e( 'Select a Taxonomy Term', 'tribe-events-calendar-pro' ); ?></option>
	</select>
</div>
