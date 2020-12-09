<?php
// Don't load directly
defined( 'WPINC' ) or die;

/**
 * My Events Column for Category Display
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/columns/category.php
 *
 * @since  4.5
 * @version 4.5
 */

echo Tribe__Events__Admin_List::custom_columns( 'events-cats', $event->ID );