<?php
// Don't load directly
defined( 'WPINC' ) or die;

/**
 * Event Submission Form Metabox For Recurrence
 * This is used to add a metabox to the event submission form to allow for choosing or
 * creating recurrences of user submitted events.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/recurrence.php
 *
 * @link https://evnt.is/1ao4 Help article for Community Events & Tickets template files.
 *
 * @since  2.1
 * @since 4.8.2 Updated template link.
 *
 * @version 4.8.2
 *
 */

global $post;
$post_id = isset( $post->ID ) ? $post->ID : null;
?>
<div class="recurrence">
	<?php Tribe__Events__Pro__Recurrence__Meta::loadRecurrenceData( $post_id ); ?>
</div>