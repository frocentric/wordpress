<?php
$rule_type              = 'rules';
$rule_prefix            = 'recurrence';
$start_hour_options     = Tribe__View_Helpers::getHourOptions( null, true );
$start_minute_options   = Tribe__View_Helpers::getMinuteOptions( null, true );
$start_meridian_options = Tribe__View_Helpers::getMeridianOptions( null, true );

$interval_options = array();
for ( $i = 1; $i <= 12; $i++ ) {
	$interval_options[] = array( 'id' => $i, 'text' => $i );
}
$delete_this_button = esc_html__( 'Delete', 'tribe-events-calendar-pro' );
$label = __( 'Event Series:', 'tribe-events-calendar-pro' );
?>

<?php
/**
 * Hook before recurring event meta fields
 *
 * @since 4.4.15
 *
 */
do_action( 'tribe_events_pro_recurrence_before_metabox' );
?>
<tr>
	<td>
		<div id="tribe-row-delete-dialog">
			<p class="question rule-question"><?php esc_html_e( 'Are you sure you want to delete these events?', 'tribe-events-calendar-pro' ) ?></p>
			<p class="question exclusion-question"><?php esc_html_e( 'Are you sure you want to delete this exclusion?', 'tribe-events-calendar-pro' ) ?></p>
		</div>
	</td>
</tr>

<tr class="recurrence-row tribe-datetime-block">
	<td class="recurrence-rules-header">
		<?php if ( function_exists( 'tribe_community_events_field_label' ) ) : ?>
			<?php tribe_community_events_field_label( 'EventSeries', $label ); ?>
		<?php else: ?>
			<label><?php echo esc_html( $label ); ?></label>
		<?php endif; ?>
	</td>
	<td>
		<div id="tribe-recurrence-staging"></div>
		<script type="text/x-handlebars-template" id="tmpl-tribe-recurrence">
			<div class="tribe-event-recurrence tribe-event-recurrence-rule">
				<a class="dashicons dashicons-trash tribe-delete-this" href="#"><span class="screen-reader-text"><?php esc_html_e( 'Delete This', 'tribe-events-calendar-pro' ); ?></span></a>
				<button class="tribe-confirm-delete-this tribe-delete-this button-primary button-red">
					<?php echo $delete_this_button ?>
				</button>
				<input type="hidden" name="is_recurring[]" data-field="is_recurring" value="{{#if is_recurring}}true{{else}}false{{/if}}"/>

				<div data-input="#recurrence_rule_--_type" class="tribe-buttonset">
					<input
						type="hidden"
						id="recurrence_rule_--_type"
						name="recurrence[rules][][type]"
						class="tribe-recurrence-rule-type tribe-button-input"
						data-field="type"
						data-single="<?php esc_attr_e( 'event', 'tribe-events-calendar-pro' ) ?>"
						data-plural="<?php esc_attr_e( 'events', 'tribe-events-calendar-pro' ) ?>"
						value="{{ custom.type }}"
					>
					<a data-value="Daily" class="tribe-button-field" href="#" data-singular="<?php esc_attr_e( 'day', 'tribe-events-calendar-pro' ); ?>"><?php esc_html_e( 'Daily', 'tribe-events-calendar-pro' ); ?></a>
					<a data-value="Weekly" class="tribe-button-field" href="#"><?php esc_html_e( 'Weekly', 'tribe-events-calendar-pro' ); ?></a>
					<a data-value="Monthly" class="tribe-button-field" href="#"><?php esc_html_e( 'Monthly', 'tribe-events-calendar-pro' ); ?></a>
					<a data-value="Yearly" class="tribe-button-field" href="#"><?php esc_html_e( 'Yearly', 'tribe-events-calendar-pro' ); ?></a>
					<a data-value="Date" class="tribe-button-field" href="#"><?php esc_html_e( 'Once', 'tribe-events-calendar-pro' ); ?></a>
				</div>

				<span class="tribe-dependent" data-depends="#recurrence_rule_--_type" data-condition-not="Yearly">
					<span class="tribe-dependent recurrence-custom-container" data-depends="#recurrence_rule_--_type" data-condition="Date">
						<span class="tribe-field-inline-text"><?php esc_html_e( 'On', 'tribe-events-calendar-pro' ); ?></span>
						<input
							autocomplete="off"
							type="text"
							class="tribe-datepicker"
							name="recurrence[rules][][custom][date][date]"
							id="recurrence_rule_--_date"
							data-field="custom-date-date"
							value="{{ custom.date.date }}"
						/>
					</span>

					<span class="tribe-dependent tribe-recurrence-type" data-depends="#recurrence_rule_--_type" data-condition-not="Date">
						<span class="tribe-field-inline-text"><?php esc_html_e( 'Every', 'tribe-events-calendar-pro' ); ?></span>
						<select
							id="recurrence_rule_--_interval"
							name="recurrence[rules][][custom][interval]"
							class="tribe-dropdown tribe-recurrence-rule-interval"
							data-options="<?php echo esc_attr( json_encode( $interval_options ) ); ?>"
							data-freeform
							data-int
							data-tags="true"
							data-field="custom-interval"
							style="display:inline-block;"
						>
							{{#if custom.interval}}
								<option value="{{custom.interval}}">{{custom.interval}}</option>
							{{else}}
								<option value="1">1</option>
							{{/if}}
						</select>
						<span class="tribe-field-inline-text tribe-dependent" data-depends="#recurrence_rule_--_type" data-condition="Daily">
							<span class="tribe-dependent" data-depends="#recurrence_rule_--_interval" data-condition="1">
								<?php esc_html_e( 'day', 'tribe-events-calendar-pro' ); ?>
							</span>
							<span class="tribe-dependent" data-depends="#recurrence_rule_--_interval" data-condition-not="1">
								<?php esc_html_e( 'days', 'tribe-events-calendar-pro' ); ?>
							</span>
						</span>
						<span class="tribe-field-inline-text tribe-dependent" data-depends="#recurrence_rule_--_type" data-condition="Weekly">
							<span class="tribe-dependent" data-depends="#recurrence_rule_--_interval" data-condition="1">
								<?php esc_html_e( 'week', 'tribe-events-calendar-pro' ); ?>
							</span>
							<span class="tribe-dependent" data-depends="#recurrence_rule_--_interval" data-condition-not="1">
								<?php esc_html_e( 'weeks', 'tribe-events-calendar-pro' ); ?>
							</span>
						</span>
						<span class="tribe-field-inline-text tribe-dependent" data-depends="#recurrence_rule_--_type" data-condition="Monthly">
							<span class="tribe-dependent" data-depends="#recurrence_rule_--_interval" data-condition="1">
								<?php esc_html_e( 'month', 'tribe-events-calendar-pro' ); ?>
							</span>
							<span class="tribe-dependent" data-depends="#recurrence_rule_--_interval" data-condition-not="1">
								<?php esc_html_e( 'months', 'tribe-events-calendar-pro' ); ?>
							</span>
						</span>
					</span>
				</span>
				<div class="tribe-recurrence-details">
					<div class="tribe-dependent" data-depends="#recurrence_rule_--_type" data-condition="Weekly">
						<?php
						/**
						 * Filters the week template for the recurrence UI
						 *
						 * @param $template
						 * @param $rule_type Type of recurrence: rules or exclusions
						 */
						include apply_filters( 'tribe_pro_recurrence_template_weeks', Tribe__Events__Pro__Main::instance()->pluginPath . '/src/admin-views/recurrence/weeks.php', $rule_type );
						?>
					</div>
					<div class="tribe-dependent" data-depends="#recurrence_rule_--_type" data-condition="Monthly">
						<?php
						/**
						 * Filters the month template for the recurrence UI
						 *
						 * @param $template
						 * @param $rule_type Type of recurrence: rules or exclusions
						 */
						include apply_filters( 'tribe_pro_recurrence_template_months', Tribe__Events__Pro__Main::instance()->pluginPath . '/src/admin-views/recurrence/months.php', $rule_type );
						?>
					</div>
					<div class="tribe-dependent" data-depends="#recurrence_rule_--_type" data-condition="Yearly">
						<?php
						/**
						 * Filters the year template for the recurrence UI
						 *
						 * @param $template
						 * @param $rule_type Type of recurrence: rules or exclusions
						 */
						include apply_filters( 'tribe_pro_recurrence_template_years', Tribe__Events__Pro__Main::instance()->pluginPath . '/src/admin-views/recurrence/years.php', $rule_type );
						?>
					</div>
					<div class="recurrence-row tribe-dependent" data-depends="#recurrence_rule_--_type" data-condition-not-empty>
						<span class="tribe-field-inline-text first-label-in-line">
							<?php echo esc_html_x( 'At', 'Begins the line indicating when a recurrence time starts' ,'tribe-events-calendar-pro' ); ?>
						</span>
						<select
							name="recurrence[rules][][custom][same-time]"
							id="recurrence_rule_--_same_time"
							class="tribe-dropdown tribe-same-time-select"
							data-hide-search
							data-field="same-time"
						>
							{{#tribe_recurrence_select custom.[same-time]}}
								<option value="yes"><?php esc_html_e( 'the same time:', 'tribe-events-calendar-pro' ); ?></option>
								<option value="no"><?php esc_html_e( 'a different time:', 'tribe-events-calendar-pro' ); ?></option>
							{{/tribe_recurrence_select}}
						</select>
						<span
							class="tribe-field-inline-text recurrence-same-time-text tribe-dependent"
							data-depends="#recurrence_rule_--_same_time"
							data-condition="yes"
						></span>
						<span class="tribe-dependent" data-depends="#recurrence_rule_--_same_time" data-condition="no">
							<?php
							/**
							 * Filters the time template for the recurrence UI
							 *
							 * @param $template
							 * @param $rule_type Type of recurrence: rules or exclusions
							 */
							include apply_filters( 'tribe_pro_recurrence_template_time', Tribe__Events__Pro__Main::instance()->pluginPath . '/src/admin-views/recurrence/time.php', $rule_type );
							?>
						</span>
					</div>
					<div
						class="tribe-dependent recurrence-row recurrence-end"
						data-depends="#recurrence_rule_--_type"
						data-condition-not-empty
						data-condition-not="Date"
						data-condition-relation="and"
					>
						<span class="recurrence-end-range">
							<span class="tribe-field-inline-text first-label-in-line"><?php esc_html_e( 'Series ends', 'tribe-events-calendar-pro' ); ?></span>
							<select
								name="recurrence[rules][][end-type]"
								id="recurrence_rule_--_end_type"
								class="tribe-dropdown"
								data-hide-search
								data-field="end-type"
							>
								{{#tribe_recurrence_select this.[end-type]}}
									<option value="On"><?php esc_html_e( 'on', 'tribe-events-calendar-pro' ); ?></option>
									<option value="After"><?php esc_html_e( 'after', 'tribe-events-calendar-pro' ); ?></option>
									<option value="Never"><?php esc_html_e( 'never', 'tribe-events-calendar-pro' ); ?></option>
								{{/tribe_recurrence_select}}
							</select>
						</span>
						<span
							class="tribe-dependent recurrence-end-container"
							data-depends="#recurrence_rule_--_end_type"
							data-condition="On"
						>
							<input
								autocomplete="off"
								data-placeholder="<?php echo esc_attr( Tribe__Date_Utils::date_only( date( Tribe__Date_Utils::DBDATEFORMAT ) ) ); ?>"
								type="text"
								class="tribe-datepicker recurrence_end tribe-no-end-date-update tribe-field-end_date"
								name="recurrence[rules][][end]"
								data-field="end"
								value="{{end}}"
								aria-label="<?php esc_html_e( 'Series ends on this date', 'tribe-events-calendar-pro' ); ?>"
							>
						</span>
						<span
							class="rec-count tribe-dependent"
							data-depends="#recurrence_rule_--_end_type"
							data-condition="After"
						>
							<input
								autocomplete="off"
								type="text"
								name="recurrence[rules][][end-count]"
								data-field="end-count"
								class="recurrence_end_count"
								value="{{this.[end-count]}}"
							>
							<span class='occurence-count-text tribe-field-inline-text'><?php _ex( 'events', 'occurence count text', 'tribe-events-calendar-pro' ) ?></span>
						</span>
						<span class="rec-error rec-end-error">
							<?php esc_html_e( 'You must select a recurrence end date', 'tribe-events-calendar-pro' ); ?>
						</span>
					</div>
					<input type="hidden" name="recurrence[rules][][custom][type-text]" data-field="custom-type-text" value="{{custom.[type-text]}}" />
					<input type="hidden" name="recurrence[rules][][occurrence-count-text]" data-field="occurrence-count-text" value="<?php esc_attr_e( _x( 'events', 'occurence count text', 'tribe-events-calendar-pro' ) ) ?>" />
				</div>
				<div class="tribe-event-recurrence-description"></div>
				<div class="tribe-dependent tribe-recurrence-details-option" data-depends="#recurrence_rule_--_type" data-condition-not-empty>
					<span class="tribe-handle" title="Click to toggle">
						<span class="show"><?php esc_html_e( 'Show Details', 'tribe-events-calendar-pro' ); ?></span>
						<span class="hide"><?php esc_html_e( 'Hide Details', 'tribe-events-calendar-pro' ); ?></span>
					</span>
				</div>
			</div>

		</script>

		<button id="tribe-add-recurrence" class="tribe-add-recurrence button tribe-button tribe-button-secondary">
			<span class="has-no-recurrence">
				<?php esc_html_e( 'Schedule multiple events', 'tribe-events-calendar-pro' ); ?>
			</span>
			<span class="has-recurrence">
				<?php esc_html_e( 'Add more events', 'tribe-events-calendar-pro' ); ?>
			</span>
		</button>

		<?php
		// check input if recurring, new recurring events will check box with jQuery
		$event_id     = get_the_ID();
		$is_recurring = '';

		if ( ! empty( $event_id ) ) {
			$is_recurring = tribe_is_recurring_event( $event_id );
		}

		?>

		<?php
		// This should only show in the admin, not on the front-end (e.g. the Community Events submission form).
		if ( ! tribe_is_frontend() ) : ?>
		<label for="tribe-recurrence-active" class="tribe-recurrence-active-label">
			<?php esc_html_e( 'Recurring Events Active', 'tribe-events-calendar-pro' ); ?>
		</label>
		<?php endif; ?>

		<input
			id="tribe-recurrence-active"
			type="checkbox"
			class="tribe-recurrence-active tribe-dependency <?php echo ! $is_recurring ? 'inactive' : ''; ?>"
			value="1"
			<?php checked( $is_recurring ); ?>
		/>
	</td>
</tr>

<?php
// switch the rule type and the rule prefix to have the templates print the correct id attribute
$rule_type   = 'exclusions';
$rule_prefix = 'exclusion';
?>

<tr class="recurrence-row tribe-recurrence-exclusion-row tribe-datetime-block">
	<td class="recurrence-exclusions-header"><?php esc_html_e( 'Event will not occur:', 'tribe-events-calendar-pro' ); ?></td>
	<td>
		<div id="tribe-exclusion-staging"></div>
		<script type="text/x-handlebars-template" id="tmpl-tribe-exclusion">
			<div class="tribe-event-exclusion tribe-event-recurrence-exclusion">
				<a class="dashicons dashicons-trash tribe-delete-this" href="#"></a>
				<button class="tribe-delete-this tribe-confirm-delete-this button-primary button-red">
					<?php echo $delete_this_button ?>
				</button>

				<div data-input="#exclusion_rule_--_type" class="tribe-buttonset">
					<input
						type="hidden"
						id="exclusion_rule_--_type"
						name="recurrence[exclusions][][type]"
						class="tribe-recurrence-rule-type tribe-button-input"
						data-field="type"
						data-single="<?php esc_attr_e( 'event', 'tribe-events-calendar-pro' ) ?>"
						data-plural="<?php esc_attr_e( 'events', 'tribe-events-calendar-pro' ) ?>"
						value="{{ custom.type }}"
					>
					<a data-value="Daily" class="tribe-button-field" href="#" data-singular="<?php esc_attr_e( 'day', 'tribe-events-calendar-pro' ); ?>"><?php esc_html_e( 'Daily', 'tribe-events-calendar-pro' ); ?></a>
					<a data-value="Weekly" class="tribe-button-field" href="#"><?php esc_html_e( 'Weekly', 'tribe-events-calendar-pro' ); ?></a>
					<a data-value="Monthly" class="tribe-button-field" href="#"><?php esc_html_e( 'Monthly', 'tribe-events-calendar-pro' ); ?></a>
					<a data-value="Yearly" class="tribe-button-field" href="#"><?php esc_html_e( 'Yearly', 'tribe-events-calendar-pro' ); ?></a>
					<a data-value="Date" class="tribe-button-field" href="#"><?php esc_html_e( 'Once', 'tribe-events-calendar-pro' ); ?></a>
				</div>

				<div class="tribe-dependent" data-depends="#exclusion_rule_--_type" data-condition-not="Yearly">
					<span class="tribe-dependent recurrence-custom-container" data-depends="#exclusion_rule_--_type" data-condition="Date">
						<span class="tribe-field-inline-text"><?php esc_html_e( 'On', 'tribe-events-calendar-pro' ); ?></span>
						<input
							autocomplete="off"
							type="text"
							class="tribe-datepicker"
							name="recurrence[exclusions][][custom][date][date]"
							id="exclusion_rule_--_date"
							data-field="custom-date"
							value="{{ custom.date.date }}"
						/>
					</span>
					<span class="tribe-dependent tribe-recurrence-type" data-depends="#exclusion_rule_--_type" data-condition-not="Date">
						<span class="tribe-field-inline-text"><?php esc_html_e( 'Every', 'tribe-events-calendar-pro' ); ?></span>
						<select
							type="text"
							id="exclusion_rule_--_interval"
							name="recurrence[exclusions][][custom][interval]"
							class="tribe-dropdown tribe-recurrence-rule-interval"
							data-options="<?php echo esc_attr( json_encode( $interval_options ) ); ?>"
							data-freeform
							data-int
							data-field="custom-interval"
						>
							{{#tribe_recurrence_select custom.interval}}
								<option value="1"><?php esc_html_e( '1', 'tribe-events-calendar-pro' ); ?></option>
								<option value="2"><?php esc_html_e( '2', 'tribe-events-calendar-pro' ); ?></option>
								<option value="3"><?php esc_html_e( '3', 'tribe-events-calendar-pro' ); ?></option>
								<option value="4"><?php esc_html_e( '4', 'tribe-events-calendar-pro' ); ?></option>
								<option value="5"><?php esc_html_e( '5', 'tribe-events-calendar-pro' ); ?></option>
								<option value="6"><?php esc_html_e( '6', 'tribe-events-calendar-pro' ); ?></option>
								<option value="7"><?php esc_html_e( '7', 'tribe-events-calendar-pro' ); ?></option>
								<option value="8"><?php esc_html_e( '8', 'tribe-events-calendar-pro' ); ?></option>
								<option value="9"><?php esc_html_e( '9', 'tribe-events-calendar-pro' ); ?></option>
								<option value="10"><?php esc_html_e( '10', 'tribe-events-calendar-pro' ); ?></option>
							{{/tribe_recurrence_select}}
						</select>
						<span class="tribe-field-inline-text tribe-dependent" data-depends="#exclusion_rule_--_type" data-condition="Daily">
							<span class="tribe-dependent" data-depends="#exclusion_rule_--_interval" data-condition="1">
								<?php esc_html_e( 'day', 'tribe-events-calendar-pro' ); ?>
							</span>
							<span class="tribe-dependent" data-depends="#exclusion_rule_--_interval" data-condition-not="1">
								<?php esc_html_e( 'days', 'tribe-events-calendar-pro' ); ?>
							</span>
						</span>
						<span class="tribe-field-inline-text tribe-dependent" data-depends="#exclusion_rule_--_type" data-condition="Weekly">
							<span class="tribe-dependent" data-depends="#exclusion_rule_--_interval" data-condition="1">
								<?php esc_html_e( 'week', 'tribe-events-calendar-pro' ); ?>
							</span>
							<span class="tribe-dependent" data-depends="#exclusion_rule_--_interval" data-condition-not="1">
								<?php esc_html_e( 'weeks', 'tribe-events-calendar-pro' ); ?>
							</span>
						</span>
						<span class="tribe-field-inline-text tribe-dependent" data-depends="#exclusion_rule_--_type" data-condition="Monthly">
							<span class="tribe-dependent" data-depends="#exclusion_rule_--_interval" data-condition="1">
								<?php esc_html_e( 'month', 'tribe-events-calendar-pro' ); ?>
							</span>
							<span class="tribe-dependent" data-depends="#exclusion_rule_--_interval" data-condition-not="1">
								<?php esc_html_e( 'months', 'tribe-events-calendar-pro' ); ?>
							</span>
						</span>
					</span>
				</div>
				<div class="tribe-recurrence-details">
					<div class="tribe-dependent weekly" data-depends="#exclusion_rule_--_type" data-condition="Weekly">
						<?php
						/**
						 * Filters the week template for the recurrence UI
						 *
						 * @param $template
						 * @param $rule_type Type of recurrence: exclusions or exclusions
						 */
						include apply_filters( 'tribe_pro_recurrence_template_weeks', Tribe__Events__Pro__Main::instance()->pluginPath . '/src/admin-views/recurrence/weeks.php', $rule_type );
						?>
					</div>
					<div class="tribe-dependent monthly" data-depends="#exclusion_rule_--_type" data-condition="Monthly">
						<?php
						/**
						 * Filters the month template for the recurrence UI
						 *
						 * @param $template
						 * @param $rule_type Type of recurrence: exclusions or exclusions
						 */
						include apply_filters( 'tribe_pro_recurrence_template_months', Tribe__Events__Pro__Main::instance()->pluginPath . '/src/admin-views/recurrence/months.php', $rule_type );
						?>
					</div>
					<div class="tribe-dependent" data-depends="#exclusion_rule_--_type" data-condition="Yearly">
						<?php
						/**
						 * Filters the year template for the recurrence UI
						 *
						 * @param $template
						 * @param $rule_type Type of recurrence: exclusions or exclusions
						 */
						include apply_filters( 'tribe_pro_recurrence_template_years', Tribe__Events__Pro__Main::instance()->pluginPath . '/src/admin-views/recurrence/years.php', $rule_type );
						?>
					</div>

					<input type="hidden" name="recurrence[exclusions][][custom][same-time]" data-field="same-time" value="yes">

					<div class="tribe-dependent recurrence-row recurrence-end" data-depends="#exclusion_rule_--_type" data-condition-not-empty>
						<span class="tribe-dependent" data-depends="#exclusion_rule_--_type" data-condition-not="Date">
							<span class="recurrence-end-range">
								<span class="tribe-field-inline-text">
									<?php esc_html_e( 'Series ends', 'tribe-events-calendar-pro' ); ?>
								</span>
								<select
									name="recurrence[exclusions][][end-type]"
									id="exclusion_rule_--_end_type"
									class="tribe-dropdown"
									data-hide-search
									data-field="end-type"
								>
									{{#tribe_recurrence_select this.[end-type]}}
										<option value="On"><?php esc_html_e( 'on', 'tribe-events-calendar-pro' ); ?></option>
										<option value="After"><?php esc_html_e( 'after', 'tribe-events-calendar-pro' ); ?></option>
										<option value="Never"><?php esc_html_e( 'never', 'tribe-events-calendar-pro' ); ?></option>
									{{/tribe_recurrence_select}}
								</select>
							</span>
							<span
								class="tribe-dependent recurrence-end-container"
								data-depends="#exclusion_rule_--_end_type"
								data-condition="On"
							>
								<input
									autocomplete="off"
									data-placeholder="<?php echo esc_attr( Tribe__Date_Utils::date_only( date( Tribe__Date_Utils::DBDATEFORMAT ) ) ); ?>"
									type="text"
									class="tribe-datepicker recurrence_end tribe-no-end-date-update tribe-field-end_date"
									name="recurrence[exclusions][][end]"
									data-field="end"
									value="{{end}}"
								>
							</span>
							<span
								class="rec-count tribe-dependent"
								data-depends="#exclusion_rule_--_end_type"
								data-condition="After"
							>
								<input
									autocomplete="off"
									type="text"
									name="recurrence[exclusions][][end-count]"
									data-field="end-count"
									class="recurrence_end_count"
									value="{{this.[end-count]}}"
								>
								<span class='occurence-count-text tribe-field-inline-text'><?php _ex( 'events', 'occurence count text', 'tribe-events-calendar-pro' ) ?></span>
							</span>
							<span class="rec-error rec-end-error">
								<?php esc_html_e( 'You must select a recurrence end date', 'tribe-events-calendar-pro' ); ?>
							</span>
						</span>
					</div>
					<div class="recurrence-rows">
						<div class="recurrence-row custom-recurrence-frequency">
							<input type="hidden" name="recurrence[exclusions][][custom][type-text]" data-field="custom-type-text" value="{{custom.[type-text]}}" />
							<input type="hidden" name="recurrence[exclusions][][occurrence-count-text]" data-field="occurrence-count-text" value="<?php esc_attr_e( _x( 'events', 'occurence count text', 'tribe-events-calendar-pro' ) ) ?>" />
							<span class="rec-error rec-days-error"><?php esc_html_e( 'Frequency of recurring event must be a number', 'tribe-events-calendar-pro' ); ?></span>
						</div>
					</div>
				</div>
				<div class="tribe-event-recurrence-description"></div>
				<div
					class="tribe-dependent tribe-recurrence-details-option"
					data-depends="#exclusion_rule_--_type"
					data-condition-relation="and"
					data-condition-not-empty
					data-condition-not="Date"
				>
					<span class="tribe-handle" title="Click to toggle">
						<span class="show"><?php esc_html_e( 'Show Details', 'tribe-events-calendar-pro' ); ?></span>
						<span class="hide"><?php esc_html_e( 'Hide Details', 'tribe-events-calendar-pro' ); ?></span>
					</span>
				</div>
			</div>
		</script>

		<button id="tribe-add-exclusion" class="button"><?php esc_html_e( 'Add Exclusion', 'tribe-events-calendar-pro' ); ?></button>
	</td>
</tr>

<tr class="recurrence-row tribe-recurrence-description">
	<td class="recurrence-description-header"><?php esc_html_e( 'Recurrence Description:', 'tribe-events-calendar-pro' ); ?></td>
	<td>
		<label class="screen-reader-text" for="recurrence-description"><?php esc_html_e( 'Recurrence Description', 'tribe-events-calendar-pro' ); ?></label>
		<input id="recurrence-description" type="text" name="recurrence[description]" value="<?php echo esc_attr( empty( $recurrence['description'] ) ? '' : $recurrence['description'] ); ?>"/>
		<div class="tribe-event-recurrence-description">
			<?php esc_html_e( 'Use this field if you want to override the auto-generated descriptions of event recurrence', 'tribe-events-calendar-pro' ); ?>
		</div>
	</td>
</tr>
<?php
/**
 * Hook after recurring event meta fields
 *
 * @since 4.4.15
 *
 */
do_action( 'tribe_events_pro_recurrence_after_metabox' );
