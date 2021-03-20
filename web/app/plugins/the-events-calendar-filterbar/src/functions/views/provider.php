<?php
/**
 * Checks whether V1 of the Filter Bar Views is enabled or not.
 *
 * In order the function will check the `TRIBE_EVENTS_FILTERBAR_V1_VIEWS` constant,
 * the `TRIBE_EVENTS_FILTERBAR_V1_VIEWS` environment variable.
 *
 * @since 5.0.0
 *
 * @return bool Whether V1 of the Views are enabled or not.
 */
function tribe_events_filterbar_views_v1_is_enabled() {

	return (bool) defined( 'TRIBE_EVENTS_FILTERBAR_V1_VIEWS' ) && TRIBE_EVENTS_FILTERBAR_V1_VIEWS;
}