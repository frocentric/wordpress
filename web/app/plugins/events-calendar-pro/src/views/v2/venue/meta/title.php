<?php
/**
 * View: Venue meta title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/venue/meta/title.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 * @var WP_Post $venue The venue post object.
 *
 */

?>
<h2 class="tribe-events-pro-venue__meta-title tribe-common-h3">
	<?php echo wp_kses_post( $venue->post_title ); ?>
</h2>
