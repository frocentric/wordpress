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
 * Configures Filter Multiselects Object in the Global Tribe variable.
 *
 * @since 5.0.0
 *
 * @type   {PlainObject}
 */
tribe.filterBar.filterMultiselects = {};

/**
 * Initializes in a Strict env the code that manages the filter multiselects.
 *
 * @since 5.0.0
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} _   Underscore.js
 * @param  {PlainObject} obj tribe.filterBar.filterMultiselects
 *
 * @return {void}
 */
( function( $, _, obj ) {
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
		multiselectInput: '[data-js="tribe-filter-bar-c-multiselect-input"]',
	};

	/**
	 * Handle multiselect change event.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object of change event.
	 *
	 * @return {void}
	 */
	obj.handleMultiselectChange = function( event ) {
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
			var uniqueValues = _.uniq( value.split( ',' ) );
			var filteredValues = _.filter( uniqueValues, _.identity );
			filteredValues.forEach( function( filteredValue ) {
				location = tribe.filterBar.filters.addKeyValueToQuery( location, key, filteredValue );
			} );
		}

		tribe.filterBar.filters.submitRequest( event.data.container, location.href );
	};

	/**
	 * Debounce the multiselect change handler.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object of change event.
	 *
	 * @return {void}
	 */
	obj.debouncedHandleMultiselectChange = _.debounce( obj.handleMultiselectChange, 50 );

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
	 * Initializes filter multiselect.
	 *
	 * @param  {jQuery}  $multiselectInput jQuery object of multiselect input.
	 * @param  {jQuery}  $container        jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.initMultiselect = function( $multiselectInput, $container ) {
		tribe_dropdowns.dropdown( $multiselectInput, {
			templateSelection: obj.handleTemplateSelection,
		} );
		$multiselectInput
			.on( 'change', { target: $multiselectInput, container: $container }, obj.debouncedHandleMultiselectChange )
			.data( 'select2' ).$container.addClass( 'select2-container--open' );
		$multiselectInput.data( 'select2' ).trigger( 'query', {} );
	};

	/**
	 * Deinitializes filter multiselects.
	 *
	 * @param  {jQuery}  $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.deinitMultiselects = function( $container ) {
		$container
			.find( obj.selectors.multiselectInput )
			.each( function( index, multiselectInput ) {
				$( multiselectInput )
					.off()
					.select2( 'destroy' );
			} );
	};

	/**
	 * Initializes filter multiselects.
	 *
	 * @param  {jQuery}  $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.initMultiselects = function( $container ) {
		$container
			.find( obj.selectors.multiselectInput )
			.each( function( index, multiselectInput ) {
				obj.initMultiselect( $( multiselectInput ), $container );
			} );
	};

	/**
	 * Handler for `keydown` event. Prevent `keydown` event from bubbling up.
	 *
	 * @todo: @paulmskim this will be removed once a more permanent solution can be put in place.
	 *
	 * @since  5.0.2
	 *
	 * @param  {Event} event event object for 'keydown' event.
	 *
	 * @return {void}
	 */
	obj.handleKeyDown = function( event ) {
		event.stopPropagation();
	};

	/**
	 * Handler for `focusin` event.
	 *
	 * @todo: @paulmskim this will be removed once a more permanent solution can be put in place.
	 *
	 * @since  5.0.2
	 *
	 * @param  {Event} event event object for 'focusin' event.
	 *
	 * @return {void}
	 */
	obj.handleFocusIn = function( event ) {
		$( event.target ).on( 'keydown', obj.handleKeyDown );
	};

	/**
	 * Handler for `focusout` event.
	 *
	 * @todo: @paulmskim this will be removed once a more permanent solution can be put in place.
	 *
	 * @since  5.0.2
	 *
	 * @param  {Event} event event object for 'focusout' event.
	 *
	 * @return {void}
	 */
	obj.handleFocusOut = function( event ) {
		$( event.target ).off( 'keydown', obj.handleKeyDown );
	};

	/**
	 * Deinitialize filter multiselects JS.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object for 'beforeAjaxSuccess.tribeEvents' event.
	 *
	 * @return {void}
	 */
	obj.deinit = function( event ) {
		var $container = event.data.container;
		obj.deinitMultiselects( $container );
		$container.off( 'beforeAjaxSuccess.tribeEvents', obj.deinit );
	};

	/**
	 * Initialize filter multiselects JS.
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
		obj.initMultiselects( $container );
		$container.on( 'beforeAjaxSuccess.tribeEvents', { container: $container }, obj.deinit );

		/**
		 * @todo: @paulmskim this will be removed once a more permanent solution can be put in place.
		 */
		$container
			.find( '.tribe-events-c-search__input' )
			.on( 'focusin', { container: $container }, obj.handleFocusIn )
			.on( 'focusout', { container: $container }, obj.handleFocusOut );
	};

	/**
	 * Handles the initialization of filter multiselects when Document is ready.
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
} )( jQuery, _, tribe.filterBar.filterMultiselects );
