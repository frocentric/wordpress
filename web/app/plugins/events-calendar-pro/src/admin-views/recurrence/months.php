<div class="recurrence-row custom-recurrence-months">
	<span class="tribe-field-inline-text first-label-in-line"><?php esc_html_e( 'On', 'tribe-events-calendar-pro' ); ?></span>
	<select
		name="recurrence[<?php echo esc_attr( $rule_type ); ?>][][custom][month][same-day]"
		id="<?php echo esc_attr( $rule_prefix ); ?>_rule_--_month_same_day"
		class="tribe-dropdown tribe-same-day-select"
		data-hide-search
		data-field="month-same-day"
	>
		{{#tribe_recurrence_select custom.month.[same-day]}}
			<option value="yes"><?php esc_html_e( 'the same day:', 'tribe-events-calendar-pro' ); ?></option>
			<option value="no"><?php esc_html_e( 'a different day:', 'tribe-events-calendar-pro' ); ?></option>
		{{/tribe_recurrence_select}}
	</select>
	<span
		class="tribe-field-inline-text recurrence-same-day-text tribe-dependent"
		data-depends="#<?php echo esc_attr( $rule_prefix ); ?>_rule_--_month_same_day"
		data-condition="yes"
	></span>
	<span
		class="tribe-field-inline-text tribe-dependent"
		data-depends="#<?php echo esc_attr( $rule_prefix ); ?>_rule_--_month_same_day"
		data-condition="no"
	>
		<span
			class="tribe-field-inline-text tribe-dependent"
			data-depends="#<?php echo esc_attr( $rule_prefix ); ?>_rule_--_month_number"
			data-condition-is-numeric
		>
			<?php echo esc_html_x( 'day', 'Qualifying the "different day". Example: "day" in "day 12 of the month"', 'tribe-events-calendar-pro' ); ?>
		</span>
		<span
			class="tribe-field-inline-text tribe-dependent"
			data-depends="#<?php echo esc_attr( $rule_prefix ); ?>_rule_--_month_number"
			data-condition-is-not-numeric
		>
			<?php echo esc_html_x( 'the', 'Qualifying the "different day". Example: "the" in "the first Friday"', 'tribe-events-calendar-pro' ); ?>
		</span>
	</span>
	<span
		class="tribe-dependent recurrence-month-on-the"
		data-depends="#<?php echo esc_attr( $rule_prefix ); ?>_rule_--_month_same_day"
		data-condition="no"
	>
		<select
			name="recurrence[<?php echo esc_attr( $rule_type ); ?>][][custom][month][number]"
			id="<?php echo esc_attr( $rule_prefix ); ?>_rule_--_month_number"
			class="tribe-dropdown"
			data-field="custom-month-number"
			data-hide-search
			data-prevent-clear
		>
			{{#tribe_recurrence_select custom.month.number}}
				<optgroup label="<?php esc_attr_e( 'Use pattern:', 'tribe-events-calendar-pro' ); ?>">
					<option value="First"><?php esc_html_e( 'first', 'tribe-events-calendar-pro' ); ?></option>
					<option value="Second"><?php esc_html_e( 'second', 'tribe-events-calendar-pro' ); ?></option>
					<option value="Third"><?php esc_html_e( 'third', 'tribe-events-calendar-pro' ); ?></option>
					<option value="Fourth"><?php esc_html_e( 'fourth', 'tribe-events-calendar-pro' ); ?></option>
					<option value="Fifth"><?php esc_html_e( 'fifth', 'tribe-events-calendar-pro' ); ?></option>
					<option value="Last"><?php esc_html_e( 'last', 'tribe-events-calendar-pro' ); ?></option>
				</optgroup>
				<optgroup label="<?php esc_attr_e( 'Use date:', 'tribe-events-calendar-pro' ); ?>">
					<?php for ( $i = 1; $i <= 31; $i ++ ): ?>
						<option value="<?php echo $i ?>"><?php echo $i; ?></option>
					<?php endfor; ?>
				</optgroup>
			{{/tribe_recurrence_select}}
		</select>
		<span
			class="tribe-dependent"
			data-depends="#<?php echo esc_attr( $rule_prefix ); ?>_rule_--_month_number"
			data-condition-is-not-numeric
		>
			<select
				name="recurrence[<?php echo esc_attr( $rule_type ); ?>][][custom][month][day]"
				class="tribe-dropdown"
				data-field="custom-month-day"
				data-hide-search
				data-prevent-clear
			>
				{{#tribe_recurrence_select custom.month.day}}
					<option value="1"><?php esc_html_e( 'Monday', 'tribe-events-calendar-pro' ); ?></option>
					<option value="2"><?php esc_html_e( 'Tuesday', 'tribe-events-calendar-pro' ); ?></option>
					<option value="3"><?php esc_html_e( 'Wednesday', 'tribe-events-calendar-pro' ); ?></option>
					<option value="4"><?php esc_html_e( 'Thursday', 'tribe-events-calendar-pro' ); ?></option>
					<option value="5"><?php esc_html_e( 'Friday', 'tribe-events-calendar-pro' ); ?></option>
					<option value="6"><?php esc_html_e( 'Saturday', 'tribe-events-calendar-pro' ); ?></option>
					<option value="7"><?php esc_html_e( 'Sunday', 'tribe-events-calendar-pro' ); ?></option>
					<option value="-">--</option>
					<option value="8"><?php esc_html_e( 'day', 'tribe-events-calendar-pro' ); ?></option>
				{{/tribe_recurrence_select}}
			</select>
		</span>
		<span
			class="tribe-dependent tribe-field-inline-text"
			data-depends="#<?php echo esc_attr( $rule_prefix ); ?>_rule_--_month_number"
			data-condition-is-numeric
		>
			<?php echo esc_html_x( 'of the month', 'As in: day 12 of the month', 'tribe-events-calendar-pro' ); ?>
		</span>
	</span>
</div>
