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
 * Configures Filter Checkboxes Object in the Global Tribe variable.
 *
 * @since 5.0.0
 *
 * @type   {PlainObject}
 */
tribe.filterBar.filterCheckboxes = {};

/**
 * Initializes in a Strict env the code that manages the filter checkboxes.
 *
 * @since 5.0.0
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} obj tribe.filterBar.filterCheckboxes
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
		checkbox: '[data-js="tribe-filter-bar-c-checkbox-input"]',
	};

	/**
	 * Handle checkbox change event.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object of change event.
	 *
	 * @return {void}
	 */
	obj.handleCheckboxChange = function( event ) {
		var key = event.target.name;
		var value = event.target.value;

		if ( ! key || ! value ) {
			return;
		}

		var location = event.target.checked
			? tribe.filterBar.filters.addKeyValueToQuery( window.location, key, value )
			: tribe.filterBar.filters.removeKeyValueFromQuery( window.location, key, value );

		tribe.filterBar.filters.submitRequest( event.data.container, location.href );
	};

	/**
	 * Unbind events for filter checkboxes functionality.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.unbindEvents = function( $container ) {
		$container.find( obj.selectors.checkbox ).off();
	};

	/**
	 * Bind events for filter checkboxes functionality.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.bindEvents = function( $container ) {
		$container
			.find( obj.selectors.checkbox )
			.each( function( index, checkbox ) {
				$( checkbox ).on( 'change', { container: $container }, obj.handleCheckboxChange );
			} );
	};

	/**
	 * Deinitialize filter checkboxes JS.
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
	 * Initialize filter checkboxes JS.
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
	 * Handles the initialization of filter checkboxes when Document is ready.
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
} )( jQuery, tribe.filterBar.filterCheckboxes );
