<?php
/**
 * Map View Google Map Container
 * This file outputs the Google Map container.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/pro/map/gmap-container.php
 *
 * @package TribeEventsCalendar
 * @version 4.4.28
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
} ?>

<div id="tribe-geo-map-wrapper" aria-hidden="true">
	<div id="tribe-geo-loading"></div><!-- #tribe-geo-loading -->
	<div id="tribe-geo-map"></div><!-- #tribe-geo-map -->
</div><!-- #tribe-geo-map-wrapper -->
