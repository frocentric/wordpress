<?php
// Don't load directly
defined( 'WPINC' ) or die;

/**
 * Event Submission Form Metabox For Venues
 * This is used to add a metabox to the event submission form to allow for choosing or
 * creating a venue for user submitted events.
 *
 * This is ALSO used in the Venue edit view. Be careful to test changes in both places.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/venue.php
 *
 * @link https://evnt.is/1ao4 Help article for Community Events & Tickets template files.
 *
 * @since 2.1
 * @since 4.8.2 Updated template link.
 *
 * @version 4.8.2
 */

// If the user cannot create new venues *and* if there are no venues
// to select from then there's no point in generating this UI
if ( ! tribe( 'community.main' )->event_form()->should_show_linked_posts_module( Tribe__Events__Venue::POSTTYPE ) ) {
    return;
}

// We need the variables here otherwise it will throw notices
$venue_label_singular = tribe_get_venue_label_singular();

if ( ! isset( $event ) ) {
	$event = null;
}
?>

<div id="event_tribe_venue" class="tribe-section tribe-section-venue eventForm <?php echo tribe_community_events_single_geo_mode() ? 'tribe-single-geo-mode' : ''; ?>">
	<div class="tribe-section-header">
		<h3 class="<?php echo tribe_community_events_field_has_error( 'organizer' ) ? 'error' : ''; ?>">
			<?php
			printf( esc_html__( '%s Details', 'tribe-events-community' ), $venue_label_singular );
			echo tribe_community_required_field_marker( 'venue' );
			?>
		</h3>
	</div>

	<?php
	/**
	 * Allow developers to hook and add content to the beginning of this section
	 */
	do_action( 'tribe_events_community_section_before_venue' );
	?>

	<table class="tribe-section-content">
		<colgroup>
			<col class="tribe-colgroup tribe-colgroup-label">
			<col class="tribe-colgroup tribe-colgroup-field">
		</colgroup>

		<?php
		tribe_community_events_venue_select_menu( $event );

		// The venue meta box will render everything within a <tbody>
		$metabox = new Tribe__Events__Linked_Posts__Chooser_Meta_Box( $event, Tribe__Events__Venue::POSTTYPE );
		$metabox->render();
		?>
	</table>

	<?php
	/**
	 * Allow developers to hook and add content to the end of this section
	 */
	do_action( 'tribe_events_community_section_after_venue' );
	?>
</div>