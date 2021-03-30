/* global tribe */
/* eslint-disable no-var, strict */

/**
 * Makes sure we have all the required levels on the Tribe Object.
 *
 * @since 5.0.0
 *
 * @type   {PlainObject}
 */
tribe.filterBar = tribe.filterBar || {};

/**
 * Configures Filter Radios Object in the Global Tribe variable.
 *
 * @since 5.0.0
 *
 * @type   {PlainObject}
 */
tribe.filterBar.filterRadios = {};

/**
 * Initializes in a Strict env the code that manages the filter radios.
 *
 * @since 5.0.0
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} obj tribe.filterBar.filterRadios
 *
 * @return {void}
 */
( function( $, obj ) {
	'use strict';
	var $document = $( document );

	/**
	 * Selectors used for configuration and setup.
	 *
	 * @since 5.0.0
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		radio: '[data-js="tribe-filter-bar-c-radio-input"]',
	};

	/**
	 * Handle radio change event.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object of change event.
	 *
	 * @return {void}
	 */
	obj.handleRadioChange = function( event ) {
		var key = event.target.name;
		var value = event.target.value;

		if ( ! key || ! value ) {
			return;
		}

		var modifiedLocation = tribe.filterBar.filters.removeKeyValueFromQuery( window.location, key, true );
		var newLocation = tribe.filterBar.filters.addKeyValueToQuery( modifiedLocation, key, value );

		tribe.filterBar.filters.submitRequest( event.data.container, newLocation.href );
	};

	/**
	 * Unbind events for filter radios functionality.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.unbindEvents = function( $container ) {
		$container.find( obj.selectors.radio ).off();
	};

	/**
	 * Bind events for filter radios functionality.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.bindEvents = function( $container ) {
		$container
			.find( obj.selectors.radio )
			.each( function( index, radio ) {
				$( radio ).on( 'change', { container: $container }, obj.handleRadioChange );
			} );
	};

	/**
	 * Deinitialize filter radios JS.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object for 'beforeAjaxSuccess.tribeEvents' event.
	 *
	 * @return {void}
	 */
	obj.deinit = function( event ) {
		var $container = event.data.container;
		obj.unbindEvents( $container );
		$container.off( 'beforeAjaxSuccess.tribeEvents', obj.deinit );
	};

	/**
	 * Initialize filter radios JS.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event}   event      event object for 'afterSetup.tribeEvents' event.
	 * @param  {integer} index      jQuery.each index param from 'afterSetup.tribeEvents' event.
	 * @param  {jQuery}  $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.init = function( event, index, $container ) {
		obj.bindEvents( $container );
		$container.on( 'beforeAjaxSuccess.tribeEvents', { container: $container }, obj.deinit );
	};

	/**
	 * Handles the initialization of filter radios when Document is ready.
	 *
	 * @since 5.0.0
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		$document.on( 'afterSetup.tribeEvents', tribe.events.views.manager.selectors.container, obj.init );
	};

	// Configure on document ready.
	$( obj.ready );
} )( jQuery, tribe.filterBar.filterRadios );
