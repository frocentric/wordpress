/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since 4.7.7
 *
 * @type   {PlainObject}
 */
tribe.events = tribe.events || {};
tribe.events.views = tribe.events.views || {};

/**
 * Configures Map Events Object in the Global Tribe variable
 *
 * @since 4.7.7
 *
 * @type  {PlainObject}
 */
tribe.events.views.mapEvents = {};

/**
 * Initializes in a Strict env the code that manages the Event Views
 *
 * @since 4.7.7
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
	 * Selectors used for configuration and setup
	 *
	 * @since 4.7.7
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		eventCardButton: '[data-js="tribe-events-pro-map-event-card-button"]',
		eventCardWrapper: '[data-js="tribe-events-pro-map-event-card-wrapper"]',
		eventCardWrapperActiveClass: '.tribe-events-pro-map__event-card-wrapper--active',
	};

	/**
	 * Deselect events by setting aria attribute and active class
	 *
	 * @since 4.7.7
	 *
	 * @param {jQuery} $buttons jQuery object of all event buttons.
	 *
	 * @return {void}
	 */
	obj.deselectAllEvents = function( $buttons ) {
		$buttons.each( function( index, button ) {
			obj.deselectEvent( $( button ) );
		} );
	};

	/**
	 * Deselect event by setting aria attribute and active class
	 *
	 * @since 4.7.7
	 *
	 * @param {jQuery} $button jQuery object of event button.
	 *
	 * @return {void}
	 */
	obj.deselectEvent = function( $button ) {
		$button
			.attr( 'aria-selected', 'false' )
			.closest( obj.selectors.eventCardWrapper )
			.removeClass( obj.selectors.eventCardWrapperActiveClass.className() );

		var contentId = $button.attr( 'aria-controls' );
		if ( contentId ) {
			var $content = $button.closest( obj.selectors.eventCardWrapper ).find( '#' + contentId );
			tribe.events.views.accordion.closeAccordion( $button, $content );
		}
	};

	/**
	 * Select event by setting aria attribute and active class
	 *
	 * @since 4.7.7
	 *
	 * @param {jQuery} $button jQuery object of event button.
	 *
	 * @return {void}
	 */
	obj.selectEvent = function( $button ) {
		$button
			.attr( 'aria-selected', 'true' )
			.closest( obj.selectors.eventCardWrapper )
			.addClass( obj.selectors.eventCardWrapperActiveClass.className() );

		var contentId = $button.attr( 'aria-controls' );
		if ( contentId ) {
			var $content = $button.closest( obj.selectors.eventCardWrapper ).find( '#' + contentId );
			tribe.events.views.accordion.openAccordion( $button, $content );
		}
	};

	/**
	 * Handle event card button click
	 *
	 * @since 4.7.7
	 *
	 * @param {Event} event event object for 'afterSetup.tribeEvents' event
	 *
	 * @return {void}
	 */
	obj.handleEventClick = function( event ) {
		var $container = event.data.container;
		var $button = event.data.target;
		var $buttons = event.data.buttons;

		$container.trigger( 'beforeMapEventClick.tribeEvents', [ $container, $button ] );

		obj.deselectAllEvents( $buttons );
		obj.selectEvent( $button );

		$container.trigger( 'afterMapEventClick.tribeEvents', [ $container, $button ] );
	};

	/**
	 * Trigger events for unbind events
	 *
	 * @since 4.7.7
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.unbindEvents = function( $container ) {
		$container.trigger( 'beforeMapUnbindEvents.tribeEvents', [ $container ] );

		$container
			.find( obj.selectors.eventCardButton )
			.each( function( buttonIndex, button ) {
				$( button ).off( 'click', obj.handleEventClick );
			} );

		$container.trigger( 'afterMapUnbindEvents.tribeEvents', [ $container ] );
	};

	/**
	 * Trigger events for bind events
	 *
	 * @since 4.7.7
	 *
	 * @param {integer} index      jQuery.each index param from 'afterSetup.tribeEvents' event.
	 * @param {jQuery}  $container jQuery object of view container.
	 * @param {object}  data       data object passed from 'afterSetup.tribeEvents' event.
	 *
	 * @return {void}
	 */
	obj.bindEvents = function( index, $container, data ) {
		$container.trigger( 'beforeMapBindEvents.tribeEvents', [ index, $container, data ] );

		var $buttons = $container.find( obj.selectors.eventCardButton );
		$buttons.each( function( buttonIndex, button ) {
			var $button = $( button );
			var eventData = {
				target: $button,
				buttons: $buttons,
				container: $container,
			};

			$button.on( 'click', eventData, obj.handleEventClick );
		} );

		$container.trigger( 'afterMapBindEvents.tribeEvents', [ index, $container, data ] );
	};

	/**
	 * Trigger events for deinitialize map
	 *
	 * @since 4.7.7
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.deinitMap = function( $container ) {
		$container.trigger( 'beforeMapDeinit.tribeEvents', [ $container ] );
		$container.trigger( 'mapDeinit.tribeEvents', [ $container ] );
		$container.trigger( 'afterMapDeinit.tribeEvents', [ $container ] );
	}

	/**
	 * Trigger events for initialize map
	 *
	 * @since 4.7.7
	 *
	 * @param {integer} index      jQuery.each index param from 'afterSetup.tribeEvents' event.
	 * @param {jQuery}  $container jQuery object of view container.
	 * @param {object}  data       data object passed from 'afterSetup.tribeEvents' event.
	 *
	 * @return {void}
	 */
	obj.initMap = function( index, $container, data ) {
		$container.trigger( 'beforeMapInit.tribeEvents', [ index, $container, data ] );
		$container.trigger( 'mapInit.tribeEvents', [ index, $container, data ] );
		$container.trigger( 'afterMapInit.tribeEvents', [ index, $container, data ] );
	};

	/**
	 * Deinitialize map events.
	 *
	 * @since 4.7.7
	 *
	 * @param  {Event}       event    event object for 'afterSetup.tribeEvents' event
	 * @param  {jqXHR}       jqXHR    Request object
	 * @param  {PlainObject} settings Settings that this request was made with
	 *
	 * @return {void}
	 */
	obj.deinit = function( event, jqXHR, settings ) {
		var $container = event.data.container;
		obj.deinitMap( $container );
		obj.unbindEvents( $container );
		$container.off( 'beforeAjaxSuccess.tribeEvents', obj.deinit );
	};

	/**
	 * Initialize map events.
	 *
	 * @since 4.7.7
	 *
	 * @param {Event}   event      JS event triggered.
	 * @param {integer} index      jQuery.each index param from 'afterSetup.tribeEvents' event.
	 * @param {jQuery}  $container jQuery object of view container.
	 * @param {object}  data       data object passed from 'afterSetup.tribeEvents' event.
	 *
	 * @return {void}
	 */
	obj.init = function( event, index, $container, data ) {
		if ( 'map' !== data.slug ) {
			return;
		}

		obj.initMap( index, $container, data );
		obj.bindEvents( index, $container, data );
		$container.on( 'beforeAjaxSuccess.tribeEvents', { container: $container }, obj.deinit );
	};

	/**
	 * Handles the initialization of the map events when Document is ready
	 *
	 * @since 4.7.7
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		$document.on( 'afterSetup.tribeEvents', tribe.events.views.manager.selectors.container, obj.init );
	};

	// Configure on document ready
	$document.ready( obj.ready );
} )( jQuery, tribe.events.views.mapEvents );
