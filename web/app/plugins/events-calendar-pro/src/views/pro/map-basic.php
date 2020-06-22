<?php
/**
 * Map View Template
 * The wrapper template for map view.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/pro/map.php
 *
 * @package TribeEventsCalendar
 * @version 4.4.33
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
} ?>

<?php do_action( 'tribe_events_before_template' ); ?>

<!-- Title Bar -->
<?php tribe_get_template_part( 'pro/map/title-bar' ); ?>

<!-- Google Map Container -->
<?php

$embed_url = tribe_events_get_map_view_basic_embed_url();

if ( $embed_url ) {
    tribe_get_template_part( 'modules/map-basic', null, array(
        'width'     => '100%',
        'height'    => '440px',
        'embed_url' => $embed_url,
    ) );
}

?>

<!-- Tribe Bar -->
<?php tribe_get_template_part( 'modules/bar' ); ?>

<!-- Main Events Content -->
<?php tribe_get_template_part( 'pro/map/content' ) ?>

<div class="tribe-clear"></div>

<?php
do_action( 'tribe_events_after_template' );
