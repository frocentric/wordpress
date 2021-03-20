<?php
// Don't load directly
defined( 'WPINC' ) or die;

/**
 * Organizer Fields Template
 *
 * This is used to edit the details of individual organizers (phone, email, etc).
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/organizer-fields.php
 *
 * @link https://evnt.is/1ao4 Help article for Community Events & Tickets template files.
 *
 * @since  2.1
 * @since 4.8.2 Updated template link.
 *
 * @version 4.8.2
 */

$organizer_label_singular = tribe_get_organizer_label_singular();

// If posting back, then use $POST values
if ( ! $_POST ) {
	$organizer_name    = esc_attr( tribe_get_organizer() );
	$organizer_phone   = esc_attr( tribe_get_organizer_phone() );
	$organizer_website = esc_url( tribe_get_organizer_website_url() );
	$organizer_email   = esc_attr( tribe_get_organizer_email() );
} else {
	$organizer_name    = isset( $_POST['organizer']['Organizer'] ) ? esc_attr( $_POST['organizer']['Organizer'] ) : '';
	$organizer_phone   = isset( $_POST['organizer']['Phone'] ) ? esc_attr( $_POST['organizer']['Phone'] ) : '';
	$organizer_website = isset( $_POST['organizer']['Website'] ) ? esc_attr( $_POST['organizer']['Website'] ) : '';
	$organizer_email   = isset( $_POST['organizer']['Email'] ) ? esc_attr( $_POST['organizer']['Email'] ) : '';
}
if ( ! isset( $event ) ) {
	$event = null;
}
?>

<!-- Organizer -->
<div class="tribe-events-community-details eventForm bubble" id="event_organizer">

	<div class="tribe-community-event-info">
		<div class="tribe_sectionheader">
			<h4>
				<label class="<?php echo tribe_community_events_field_has_error( 'organizer' ) ? 'error' : ''; ?>"><?php
						printf( __( '%s Details', 'tribe-events-community' ), $organizer_label_singular );
						echo tribe_community_required_field_marker( 'organizer' );
						?>
				</label>
			</h4>
		</div>

		<?php tribe_community_events_organizer_select_menu( $event ); ?>

		<?php if ( ! tribe_community_events_is_organizer_edit_screen() ) { ?>
			<div class="organizer">
				<label for="OrganizerOrganizer">
					<?php printf( __( '%s Name', 'tribe-events-community' ), $organizer_label_singular ); ?>:
				</label>
				<input type="text" id="OrganizerOrganizer" class="linked-post-name" name="organizer[Organizer]" size="25"  value="<?php echo esc_attr( $organizer_name ); ?>" />
			</div><!-- .organizer -->
		<?php } ?>

		<div class="organizer">
			<label for="OrganizerPhone">
				<?php esc_html_e( 'Phone', 'tribe-events-community' ); ?>:
			</label>
			<input type="text" id="OrganizerPhone" name="organizer[Phone]" size="25" value="<?php echo esc_attr( $organizer_phone ); ?>" />
		</div><!-- .organizer -->

		<div class="organizer">
			<label for="OrganizerWebsite"><?php esc_html_e( 'Website', 'tribe-events-community' ); ?>:</label>
			<input type="text" id="OrganizerWebsite" name="organizer[Website]" size="25" value="<?php echo esc_attr( $organizer_website ); ?>" />
		</div><!-- .organizer -->

		<div class="organizer">
			<label for="OrganizerEmail"><?php esc_html_e( 'Email', 'tribe-events-community' ); ?>:</label>
			<input type="text" id="OrganizerEmail" name="organizer[Email]" size="25" value="<?php echo esc_attr( $organizer_email ); ?>" />
		</div><!-- .organizer -->

	</div><!-- #event_organizer -->

</div>