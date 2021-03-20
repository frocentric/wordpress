<?php
/**
 * Default Events Template placeholder:
 * used to display community events content within the default events template itself.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/default-placeholder.php
 *
 * @link https://evnt.is/1ao4 Help article for Community Events & Tickets template files.
 *
 * @since  3.2
 * @since 4.8.2 Updated template link.
 *
 * @version 4.8.2
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

while ( have_posts() ) {
	the_post();
	the_content();
}