<?php
/**
 * Default Events Template placeholder:
 * used to display community events content within the default events template itself.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/default-placeholder.php
 *
 * @package Tribe__Events__Community__Main
 * @since  3.2
 * @author Modern Tribe Inc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

while ( have_posts() ) {
	the_post();
	the_content();
}