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
 * Configures Filter Clear Object in the Global Tribe variable.
 *
 * @since 5.0.0
 *
 * @type   {PlainObject}
 */
tribe.filterBar.filterClear = {};

/**
 * Initializes in a Strict env the code that manages the filter clear.
 *
 * @since 5.0.0
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} obj tribe.filterBar.filterClear
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
		clearButton: '[data-js="tribe-filter-bar-c-clear-button"]',
		selectedFilter: '[data-js="tribe-filter-bar__selected-filter"]',
	};

	/**
	 * Handler for clear button click event.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object of click event.
	 *
	 * @return {void}
	 */
	obj.handleClearClick = function( event ) {
		var $container = event.data.container;
		var location = $container
			.find( obj.selectors.selectedFilter )
			.toArray()
			.reduce( function( loc, filter ) {
				var name = $( filter ).data( 'filter-name' );

				// Return early if name doesn't exist.
				if ( ! name ) {
					return loc;
				}

				return tribe.filterBar.filters.removeKeyValueFromQuery( loc, name, true );
			}, window.location );

		tribe.filterBar.filters.submitRequest( $container, location.href );
	};

	/**
	 * Unbind events for filter clear functionality.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.unbindEvents = function( $container ) {
		$container.find( obj.selectors.clearButton ).off();
	};

	/**
	 * Bind events for filter clear functionality.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.bindEvents = function( $container ) {
		$container
			.find( obj.selectors.clearButton )
			.each( function( index, clearButton ) {
				var $clearButton = $( clearButton );
				$clearButton.on( 'click', { target: $clearButton, container: $container }, obj.handleClearClick );
			} );
	};

	/**
	 * Deinitialize filter clear JS.
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
	 * Initialize filter clear JS.
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
	 * Handles the initialization of filter clear when Document is ready.
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
} )( jQuery, tribe.filterBar.filterClear );
