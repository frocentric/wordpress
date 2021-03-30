/* global tribe, tribe_dropdowns */
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
 * Configures Filter Dropdowns Object in the Global Tribe variable.
 *
 * @since 5.0.0
 *
 * @type   {PlainObject}
 */
tribe.filterBar.filterDropdowns = {};

/**
 * Initializes in a Strict env the code that manages the filter dropdowns.
 *
 * @since 5.0.0
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} obj tribe.filterBar.filterDropdowns
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
		dropdownInput: '[data-js="tribe-filter-bar-c-dropdown-input"]',
	};

	/**
	 * Handle dropdown change event.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object of change event.
	 *
	 * @return {void}
	 */
	obj.handleDropdownChange = function( event ) {
		var key = event.data.target.attr( 'name' );

		// Return early if name attribute is not set.
		if ( ! key ) {
			return;
		}

		// Return early if an ajax request is already happening.
		if ( tribe.events.views.manager.currentAjaxRequest ) {
			return;
		}

		var location = tribe.filterBar.filters.removeKeyValueFromQuery( window.location, key, true );
		var value = event.data.target.attr( 'value' );

		if ( value ) {
			location = tribe.filterBar.filters.addKeyValueToQuery( location, key, value );
		}

		tribe.filterBar.filters.submitRequest( event.data.container, location.href );
	};

	/**
	 * Handler for template selection.
	 *
	 * @since  5.0.0
	 *
	 * @param  {PlainObject} state State of selected item.
	 *
	 * @return {jQuery}
	 */
	obj.handleTemplateSelection = function( state ) {
		var $newEl = $( '<span class="select2-selection__choice__text"></span>' );
		$newEl.text( state.text );
		return $newEl;
	};

	/**
	 * Initializes filter dropdown.
	 *
	 * @param  {jQuery}  $dropdownInput jQuery object of dropdown input.
	 * @param  {jQuery}  $container     jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.initDropdown = function( $dropdownInput, $container ) {
		tribe_dropdowns.dropdown( $dropdownInput, {
			templateSelection: obj.handleTemplateSelection,
		} );
		$dropdownInput
			.on( 'change', { target: $dropdownInput, container: $container }, obj.handleDropdownChange )
			.data( 'select2' ).$container.addClass( 'select2-container--open' );
		$dropdownInput.data( 'select2' ).trigger( 'query', {} );
	};

	/**
	 * Deinitializes filter dropdowns.
	 *
	 * @param  {jQuery}  $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.deinitDropdowns = function( $container ) {
		$container
			.find( obj.selectors.dropdownInput )
			.each( function( index, dropdownInput ) {
				$( dropdownInput )
					.off()
					.select2( 'destroy' );
			} );
	};

	/**
	 * Initializes filter dropdowns.
	 *
	 * @param  {jQuery}  $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.initDropdowns = function( $container ) {
		$container
			.find( obj.selectors.dropdownInput )
			.each( function( index, dropdownInput ) {
				obj.initDropdown( $( dropdownInput ), $container );
			} );
	};

	/**
	 * Deinitialize filter dropdowns JS.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object for 'beforeAjaxSuccess.tribeEvents' event.
	 *
	 * @return {void}
	 */
	obj.deinit = function( event ) {
		var $container = event.data.container;
		obj.deinitDropdowns( $container );
		$container.off( 'beforeAjaxSuccess.tribeEvents', obj.deinit );
	};

	/**
	 * Initialize filter dropdowns JS.
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
		obj.initDropdowns( $container );
		$container.on( 'beforeAjaxSuccess.tribeEvents', { container: $container }, obj.deinit );
	};

	/**
	 * Handles the initialization of filter dropdowns when Document is ready.
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
} )( jQuery, tribe.filterBar.filterDropdowns );
