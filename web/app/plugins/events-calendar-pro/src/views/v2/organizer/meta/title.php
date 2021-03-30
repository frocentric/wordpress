<?php
/**
 * View: Organizer meta title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/organizer/meta/title.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 * @var WP_Post $organizer The organizer post object.
 *
 */

?>
<h2 class="tribe-events-pro-organizer__meta-title tribe-common-h3">
	<?php echo tribe_get_organizer( $organizer->ID ); ?>
</h2>
