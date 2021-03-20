<?php
// Don't load directly
defined( 'WPINC' ) or die;

/**
 * My Events Column for Category Display
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/columns/category.php
 *
 * @link https://evnt.is/1ao4 Help article for Community Events & Tickets template files.
 *
 * @since  4.5
 * @since 4.8.2 Updated template link.
 *
 * @version 4.8.2
 */

echo Tribe__Events__Admin_List::custom_columns( 'events-cats', $event->ID );