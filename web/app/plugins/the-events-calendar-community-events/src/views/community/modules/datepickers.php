<?php
// Don't load directly.
defined( 'WPINC' ) or die;

/**
 * Event Submission Form Metabox For Datepickers
 * This is used to add a metabox to the event submission form to allow for choosing the
 * event time and day.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/datepickers.php
 *
 * @link https://evnt.is/1ao4 Help article for Community Events & Tickets template files.
 *
 * @since  3.1
 * @since  4.7.1 Now using new tribe_community_events_field_classes function to set up classes for the input.
 * @since 4.8.2 Updated template link.
 *
 * @version 4.8.2
 */

// Our possible post.
$has_post = get_post();

// Administration Metabox Instance.
$metabox = tribe( 'tec.admin.event-meta-box' );

// We are using this to mimic variables from the Administration.
extract( $metabox->get_extract_vars( $has_post ) );

$tz_choice = Tribe__Events__Timezones::get_event_timezone_string();

if ( $has_post && 0 !== get_the_ID() && 'auto-draft' !== get_post_status( $has_post ) ) {
	$start_date = tribe_community_events_get_start_date();
	$end_date   = tribe_community_events_get_end_date();
} else {
	$start_date    = ! empty( $_POST['EventStartDate'] ) ? $_POST['EventStartDate'] : tribe_community_events_get_start_date();
	$end_date      = ! empty( $_POST['EventEndDate'] ) ? $_POST['EventEndDate'] : tribe_community_events_get_end_date();
	$start_time    = ! empty( $_POST['EventStartTime'] ) ? $_POST['EventStartTime'] : $start_timepicker_default;
	$end_time      = ! empty( $_POST['EventEndTime'] ) ? $_POST['EventEndTime'] : $end_timepicker_default;
	$isEventAllDay = ! empty( $_POST['EventAllDay'] ) && tribe_is_truthy( $_POST['EventAllDay'] ) ? 'checked="checked"' : $isEventAllDay;
	$tz_choice     = ! empty( $_POST['EventTimezone'] ) ? $_POST['EventTimezone'] : $tz_choice;
}

$events_label_singular           = tribe_get_event_label_singular();
$events_label_plural             = tribe_get_event_label_plural();
$events_label_singular_lowercase = tribe_get_event_label_singular_lowercase();
$events_label_plural_lowercase   = tribe_get_event_label_plural_lowercase();

?>

<div class="tribe-section tribe-section-datetime event-datepickers event-time eventForm">
	<div class="tribe-section-header">
		<h3><?php printf( __( '%s Time &amp; Date', 'tribe-events-community' ), $events_label_singular ); ?></h3>
	</div>

	<?php
	/**
	 * Allow developers to hook and add content to the beginning of this section
	 */
	do_action( 'tribe_events_community_section_before_datetime' );
	?>

	<table class="tribe-section-content tribe-datetime-block">
		<colgroup>
			<col class="tribe-colgroup tribe-colgroup-label">
			<col class="tribe-colgroup tribe-colgroup-field">
		</colgroup>

		<tr id="recurrence-changed-row">
			<td colspan="2">
				<?php printf( __( 'You have changed the recurrence rules of this %1$s. Saving the %1$s will update all future %2$s.  If you did not mean to change all %2$s, then please refresh the page.', 'tribe-events-community' ), $events_label_singular_lowercase, $events_label_plural_lowercase ); ?>
			</td>
		</tr>

		<tr class="tribe-section-content-row">
			<td class="tribe-section-content-label">
				<?php tribe_community_events_field_label( 'EventStartDate', __( 'Start/End:', 'tribe-events-community' ) ); ?>
			</td>
			<td class="tribe-section-content-field">
				<!-- Start Date -->
				<label class="screen-reader-text" for="EventStartDate">
					<?php esc_html_e( 'Event Start Date', 'tribe-events-community' ); ?>
				</label>
				<input
					id="EventStartDate"
					autocomplete="off"
					type="text"
					class="<?php tribe_community_events_field_classes( 'EventStartDate', [ 'tribe-datepicker', 'tribe-field-start_date' ] ); ?>"
					name="EventStartDate"
					value="<?php echo esc_attr( $start_date ) ?>"
				/>
				<span class="helper-text hide-if-js"><?php esc_html_e( 'YYYY-MM-DD', 'tribe-events-community' ) ?></span>

				<!-- Start Time -->
				<label class="screen-reader-text" for="EventStartTime">
					<?php esc_html_e( 'Event Start Time', 'tribe-events-community' ); ?>
				</label>
				<input
					id="EventStartTime"
					autocomplete="off"
					type="text"
					class="<?php tribe_community_events_field_classes( 'EventStartTime', [ 'tribe-timepicker', 'tribe-field-start_time' ] ); ?>"
					name="EventStartTime"
					<?php echo Tribe__View_Helpers::is_24hr_format() ? 'data-format="H:i"' : '' ?>
					data-step="<?php echo esc_attr( $start_timepicker_step ); ?>"
					data-round="<?php echo esc_attr( $timepicker_round ); ?>"
					data-disable-touch-keyboard="true"
					value="<?php echo esc_attr( $metabox->is_auto_draft() ? $start_time : $EventStartTime ) ?>"
				/>
				<span class="helper-text hide-if-js"><?php esc_html_e( 'HH:MM', 'tribe-events-community' ) ?></span>
				<span class="tribe-datetime-separator"> <?php echo esc_html_x( 'to', 'Start Date Time "to" End Date Time', 'tribe-events-community' ); ?> </span>

				<!-- End Time -->
				<label class="screen-reader-text" for="EventEndTime">
					<?php esc_html_e( 'Event End Time', 'tribe-events-community' ); ?>
				</label>
				<input
					id="EventEndTime"
					name="EventEndTime"
					autocomplete="off"
					type="text"
					class="<?php tribe_community_events_field_classes( 'EventEndTime', [ 'tribe-timepicker', 'tribe-field-end_time' ] ); ?>"
					<?php echo Tribe__View_Helpers::is_24hr_format() ? 'data-format="H:i"' : '' ?>
					data-step="<?php echo esc_attr( $end_timepicker_step ); ?>"
					data-round="<?php echo esc_attr( $timepicker_round ); ?>"
					data-disable-touch-keyboard="true"
					value="<?php echo esc_attr( $metabox->is_auto_draft() ? $end_time : $EventEndTime ); ?>"
				/>
				<span class="helper-text hide-if-js"><?php esc_html_e( 'HH:MM', 'tribe-events-community' ); ?></span>

				<!-- End Date -->
				<label class="screen-reader-text" for="EventEndDate">
					<?php esc_html_e( 'Event End Date', 'tribe-events-community' ); ?>
				</label>
				<input
					id="EventEndDate"
					name="EventEndDate"
					autocomplete="off"
					type="text"
					class="<?php tribe_community_events_field_classes( 'EventEndDate', [ 'tribe-datepicker', 'tribe-field-end_date' ] ); ?>"
					value="<?php echo esc_attr( $end_date ); ?>"
				/>
				<span class="helper-text hide-if-js"><?php esc_html_e( 'YYYY-MM-DD', 'tribe-events-community' ); ?></span>

				<?php if ( class_exists( 'Tribe__Events__Timezones' ) && ! tribe_community_events_single_geo_mode() ) : ?>

					<!-- Timezone -->
					<select
						id="event-timezone"
						aria-label="<?php esc_html_e( 'Timezone', 'tribe-events-community' ); ?>"
						name="EventTimezone"
						class="<?php tribe_community_events_field_classes( 'EventTimezone', [ 'tribe-field-timezone', 'tribe-dropdown', 'hide-if-js' ] ); ?>"
						data-timezone-label="<?php esc_attr_e( 'Timezone:', 'tribe-events-community' ) ?>"
						data-timezone-value="<?php echo esc_attr( $tz_choice ); ?>"
					>
						<?php
						// Use the Tribe-specific wrapper function (the output of which is filterable) if available.
						if ( function_exists( 'tribe_events_timezone_choice' ) ) {
							echo tribe_events_timezone_choice( $tz_choice );
						} else {
							echo wp_timezone_choice( $tz_choice );
						}
						?>
					</select>

				<?php endif ?>
			</td>
		</tr>

		<tr class="tribe-section-content-row">
			<td class="tribe-section-content-label"></td>
			<td class="tribe-section-content-field">
				<input
					id="allDayCheckbox"
					type="checkbox"
					name="EventAllDay"
					class="<?php tribe_community_events_field_classes( 'EventAllDay', [] ); ?>"
					value="yes" <?php echo esc_html( $isEventAllDay ); ?>
				/>
				<label for="allDayCheckbox"><?php esc_html_e( 'All Day Event', 'tribe-events-community' ); ?></label>
			</td>
		</tr>

		<tr class="tribe-section-content-row event-dynamic-helper">
			<td class="tribe-section-content-label"></td>
			<td class="tribe-section-content-field">
				<div class="event-dynamic-helper-text"></div>
			</td>
		</tr>

		<?php do_action( 'tribe_events_date_display', null, true ); ?>
	</table>

	<?php
	/**
	 * Allow developers to hook and add content to the end of this section
	 */
	do_action( 'tribe_events_community_section_after_datetime' );
	?>
</div>
