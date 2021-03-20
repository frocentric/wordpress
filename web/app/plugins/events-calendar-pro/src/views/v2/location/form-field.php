<?php
/**
 * View: Form Location Field
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/location/form-field.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.2.0
 */
?>
<div
	class="tribe-common-form-control-text tribe-events-c-search__input-control tribe-events-c-search__input-control--location"
	data-js="tribe-events-events-bar-input-control"
>
	<label class="tribe-common-form-control-text__label" for="tribe-events-events-bar-location">
		<?php printf( esc_html__( 'Enter Location. Search for %s by Location.', 'tribe-events-calendar-pro' ), tribe_get_event_label_plural() ); ?>
	</label>
	<input
		class="tribe-common-form-control-text__input tribe-events-c-search__input tribe-events-c-search__input--icon"
		data-js="tribe-events-events-bar-input-control-input"
		type="text"
		id="tribe-events-events-bar-location"
		name="tribe-events-views[tribe-bar-location]"
		value="<?php echo esc_attr( tribe_events_template_var( [ 'bar', 'location' ], '' ) ); ?>"
		placeholder="<?php esc_attr_e( 'In a location', 'tribe-events-calendar-pro' ); ?>"
		aria-label="<?php printf( esc_attr__( 'Enter Location. Search for %s by Location.', 'tribe-events-calendar-pro' ), tribe_get_event_label_plural() ); ?>"
	/>
	<?php $this->template( 'components/icons/location', [ 'classes' => [ 'tribe-events-c-search__input-control-icon-svg' ] ] ); ?>
</div>
