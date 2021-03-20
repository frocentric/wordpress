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
 * Configures Filter Remove Object in the Global Tribe variable.
 *
 * @since 5.0.0
 *
 * @type   {PlainObject}
 */
tribe.filterBar.filterRemove = {};

/**
 * Initializes in a Strict env the code that manages the filter remove.
 *
 * @since 5.0.0
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} obj tribe.filterBar.filterRemove
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
		removeButton: '[data-js="tribe-filter-bar-c-pill__remove-button"]',
		pillFilterName: '[data-filter-name]',
	};

	/**
	 * Handler for remove button click event.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object of click event.
	 *
	 * @return {void}
	 */
	obj.handleRemoveClick = function( event ) {
		var $removeButton = event.data.target;
		var $container = event.data.container;
		var $pill = $removeButton.closest( '[data-filter-name]' );

		// Return early if pill with data-filter-name attribute is not found.
		if ( ! $pill.length ) {
			return;
		}

		var name = $pill.data( 'filter-name' );

		// Return early if name doesn't exist.
		if ( ! name ) {
			return;
		}

		var location = tribe.filterBar.filters.removeKeyValueFromQuery( window.location, name, true );

		tribe.filterBar.filters.submitRequest( $container, location.href );
	};

	/**
	 * Unbind events for filter remove functionality.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.unbindEvents = function( $container ) {
		$container.find( obj.selectors.removeButton ).off();
	};

	/**
	 * Bind events for filter remove functionality.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.bindEvents = function( $container ) {
		$container
			.find( obj.selectors.removeButton )
			.each( function( index, removeButton ) {
				var $removeButton = $( removeButton );
				$removeButton.on( 'click', { target: $removeButton, container: $container }, obj.handleRemoveClick );
			} );
	};

	/**
	 * Deinitialize filter remove JS.
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
	 * Initialize filter remove JS.
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
	 * Handles the initialization of filter remove when Document is ready.
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
} )( jQuery, tribe.filterBar.filterRemove );
