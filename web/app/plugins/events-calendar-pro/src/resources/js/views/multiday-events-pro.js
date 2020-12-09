/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since 4.7.8
 *
 * @type   {PlainObject}
 */
tribe.events = tribe.events || {};
tribe.events.views = tribe.events.views || {};

/**
 * Configures Multiday Events Pro Object in the Global Tribe variable
 *
 * @since 4.7.8
 *
 * @type  {PlainObject}
 */
tribe.events.views.multidayEventsPro = {};

/**
 * Initializes in a Strict env the code that manages the Event Views
 *
 * @since 4.7.8
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} obj tribe.events.views.manager
 *
 * @return {void}
 */
( function( $, obj ) {
	'use strict';
	var $document = $( document );

	/**
	 * Selector prefixes used for creating selectors
	 *
	 * @since 4.7.8
	 *
	 * @type {PlainObject}
	 */
	obj.selectorPrefixes = {
		week: '.tribe-events-pro-week-grid__',
	};

	/**
	 * Handles after multiday events init allowed views event.
	 *
	 * @since 4.7.8
	 *
	 * @param {Event}   event      event object for 'afterMultidayEventsInitAllowedViews.tribeEvents' event
	 * @param {jQuery}  $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.handleAfterMultidayEventsInitAllowedViews = function( event, $container ) {
		var allowedViews = $container.data( 'tribeEventsMultidayEventsAllowedViews' );
		allowedViews.push( 'week' );
		$container.data( 'tribeEventsMultidayEventsAllowedViews', allowedViews );
		tribe.events.views.multidayEvents.selectorPrefixes.week = obj.selectorPrefixes.week;
	};

	/**
	 * Handles the initialization of multiday events when Document is ready
	 *
	 * @since 4.7.8
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		$document.on( 'afterMultidayEventsInitAllowedViews.tribeEvents', tribe.events.views.manager.selectors.container, obj.handleAfterMultidayEventsInitAllowedViews );
	};

	// Configure on document ready
	$document.ready( obj.ready );
} )( jQuery, tribe.events.views.multidayEventsPro );
