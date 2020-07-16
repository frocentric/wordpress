/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since 4.7.8
 *
 * @type {PlainObject}
 */
tribe.events = tribe.events || {};
tribe.events.views = tribe.events.views || {};

/**
 * Configures Views Tooltip Pro Object in the Global Tribe variable
 *
 * @since 4.7.8
 *
 * @type {PlainObject}
 */
tribe.events.views.tooltipPro = {};

/**
 * Initializes in a Strict env the code that manages the Event Views
 *
 * @since  4.7.8
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} obj tribe.events.views.tooltip
 *
 * @return {void}
 */
( function( $, obj ) {
	'use strict';
	var $document = $( document );

	/**
	 * Selectors used for configuration and setup
	 *
	 * @since 4.7.8
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		tribeEventsProClass: '.tribe-events-pro',
	};

	/**
	 * Handles after tooltip init theme event.
	 *
	 * @since 4.7.8
	 *
	 * @param {Event}  event      event object for 'afterTooltipInitTheme.tribeEvents' event
	 * @param {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.handleAfterTooltipInitTheme = function( event, $container ) {
		var theme = $container.data( 'tribeEventsTooltipTheme' );
		theme.push( obj.selectors.tribeEventsProClass.className() );
		$container.data( 'tribeEventsTooltipTheme', theme );
	};

	/**
	 * Handles the initialization of the scripts when Document is ready
	 *
	 * @since  4.7.8
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		$document.on( 'afterTooltipInitTheme.tribeEvents', tribe.events.views.manager.selectors.container, obj.handleAfterTooltipInitTheme );
	};

	// Configure on document ready
	$document.ready( obj.ready );
} )( jQuery, tribe.events.views.tooltipPro );
