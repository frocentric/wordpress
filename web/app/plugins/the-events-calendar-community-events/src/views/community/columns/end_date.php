<?php
// Don't load directly
defined( 'WPINC' ) or die;

/**
 * My Events Column for End Date Display
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/columns/end_date.php
 *
 * @link https://evnt.is/1ao4 Help article for Community Events & Tickets template files.
 *
 * @since 4.5
 * @since 4.8.2 Updated template link.
 *
 * @version 4.8.2
 */

echo esc_html( tribe_get_end_date( $event->ID ) );