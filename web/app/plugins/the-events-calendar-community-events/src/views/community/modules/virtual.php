<?php
// Don't load directly

defined( 'WPINC' ) or die;

/**
 * Event Submission Form meta box For Virtual Events.
 *
 * This is used to add a meta box to the event submission form to allow for
 * setting up a Virtual Event.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/virtual.php
 *
 * @link https://evnt.is/1ao4 Help article for Community Events & Tickets template files.
 *
 * @since  4.8.0
 * @since 4.8.2 Updated template link.
 *
 * @version 4.8.2
 */

use Tribe\Events\Virtual\Metabox;

// Check if the necessary class is available.
if ( ! class_exists( '\Tribe\Events\Virtual\Metabox' ) ) {
	return;
}

if ( ! isset( $event ) ) {
	$event = Tribe__Events__Main::postIdHelper();
}

/**
 * @var Metabox $virtual_meta_box
 */
$virtual_meta_box = tribe( Metabox::class );
?>

<div id="event_tribe_virtual" class="tribe-section tribe-section-virtual">
	<div class="tribe-section-header">
		<h3 class="<?php echo tribe_community_events_field_has_error( 'virtual' ) ? 'error' : ''; ?>">
			<?php
			echo $virtual_meta_box->get_title();
			echo tribe_community_required_field_marker( 'virtual' );
			?>
		</h3>
	</div>

	<?php
	/**
	 * Allow developers to hook and add content to the beginning of this section.
	 *
	 * @since4.8.0
	 */
	do_action( 'tribe_events_community_section_before_virtual' );

	// The virtual meta box will render everything within a table.
	$template_arguments = [
		// Don't show the header row.
		'block_editor_compatibility' => true,
	];

	$virtual_meta_box->print_template( $event, $template_arguments );

	/**
	 * Allow developers to hook and add content to the end of this section.
	 *
	 * @since4.8.0
	 */
	do_action( 'tribe_events_community_section_after_virtual' );
	?>
</div>
