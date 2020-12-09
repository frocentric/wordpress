<div
	class="recurrence-row custom-recurrence-weeks tribe-buttonset"
	data-multiple
	role="group"
	aria-label="<?php esc_html_e( 'Event Series Recurrence Day of Week', 'tribe-events-calendar-pro' ); ?>"
>
	<span class="tribe-field-inline-text first-label-in-line"><?php echo esc_html_x( 'On', 'Begins the line indicating what day of the week the event will occur on', 'tribe-events-calendar-pro' ); ?></span>
	<a class="tribe-button-field {{tribe_if_in '7' custom.week.day 'tribe-active'}}" title="<?php esc_attr_e( 'Sunday', 'tribe-events-calendar-pro' ); ?>">
		<input
			class="tribe-button-input tribe-hidden"
			type="checkbox"
			name="recurrence[<?php echo esc_attr( $rule_type ); ?>][][custom][week][day][]"
			data-field="custom-week-day"
			value="7"
			{{tribe_if_in '7' custom.week.day 'checked'}}
		/>
		<?php echo esc_html( tribe_wp_locale_weekday( 'Sunday', 'initial' ) ); ?>
	</a>
	<a class="tribe-button-field {{tribe_if_in '1' custom.week.day 'tribe-active'}}" title="<?php esc_attr_e( 'Monday', 'tribe-events-calendar-pro' ); ?>">
		<input
			class="tribe-button-input tribe-hidden"
			type="checkbox"
			name="recurrence[<?php echo esc_attr( $rule_type ); ?>][][custom][week][day][]"
			data-field="custom-week-day"
			value="1"
			{{tribe_if_in '1' custom.week.day 'checked'}}
		/>
		<?php echo esc_html( tribe_wp_locale_weekday( 'Monday', 'initial' ) ); ?>
	</a>
	<a class="tribe-button-field {{tribe_if_in '2' custom.week.day 'tribe-active'}}" title="<?php esc_attr_e( 'Tuesday', 'tribe-events-calendar-pro' ); ?>">
		<input
			class="tribe-button-input tribe-hidden"
			type="checkbox"
			name="recurrence[<?php echo esc_attr( $rule_type ); ?>][][custom][week][day][]"
			data-field="custom-week-day"
			value="2"
			{{tribe_if_in '2' custom.week.day 'checked'}}
		/>
		<?php echo esc_html( tribe_wp_locale_weekday( 'Tuesday', 'initial' ) ); ?>
	</a>
	<a class="tribe-button-field {{tribe_if_in '3' custom.week.day 'tribe-active'}}" title="<?php esc_attr_e( 'Wednesday', 'tribe-events-calendar-pro' ); ?>">
		<input
			class="tribe-button-input tribe-hidden"
			type="checkbox"
			name="recurrence[<?php echo esc_attr( $rule_type ); ?>][][custom][week][day][]"
			data-field="custom-week-day"
			value="3"
			{{tribe_if_in '3' custom.week.day 'checked'}}
		/>
		<?php echo esc_html( tribe_wp_locale_weekday( 'Wednesday', 'initial' ) ); ?>
	</a>
	<a class="tribe-button-field {{tribe_if_in '4' custom.week.day 'tribe-active'}}" title="<?php esc_attr_e( 'Thursday', 'tribe-events-calendar-pro' ); ?>">
		<input
			class="tribe-button-input tribe-hidden"
			type="checkbox"
			name="recurrence[<?php echo esc_attr( $rule_type ); ?>][][custom][week][day][]"
			data-field="custom-week-day"
			value="4"
			{{tribe_if_in '4' custom.week.day 'checked'}}
		/>
		<?php echo esc_html( tribe_wp_locale_weekday( 'Thursday', 'initial' ) ); ?>
	</a>
	<a class="tribe-button-field {{tribe_if_in '5' custom.week.day 'tribe-active'}}" title="<?php esc_attr_e( 'Friday', 'tribe-events-calendar-pro' ); ?>">
		<input
			class="tribe-button-input tribe-hidden"
			type="checkbox"
			name="recurrence[<?php echo esc_attr( $rule_type ); ?>][][custom][week][day][]"
			data-field="custom-week-day"
			value="5"
			{{tribe_if_in '5' custom.week.day 'checked'}}
		/>
		<?php echo esc_html( tribe_wp_locale_weekday( 'Friday', 'initial' ) ); ?>
	</a>
	<a class="tribe-button-field {{tribe_if_in '6' custom.week.day 'tribe-active'}}" title="<?php esc_attr_e( 'Saturday', 'tribe-events-calendar-pro' ); ?>">
		<input
			class="tribe-button-input tribe-hidden"
			type="checkbox"
			name="recurrence[<?php echo esc_attr( $rule_type ); ?>][][custom][week][day][]"
			data-field="custom-week-day"
			value="6"
			{{tribe_if_in '6' custom.week.day 'checked'}}
		/>
		<?php echo esc_html( tribe_wp_locale_weekday( 'Saturday', 'initial' ) ); ?>
	</a>
</div>
