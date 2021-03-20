/* global tribe, tribe_events_filter_bar_js_config */
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
 * Configures Filter Bar State Object in the Global Tribe variable.
 *
 * @since 5.0.0
 *
 * @type   {PlainObject}
 */
tribe.filterBar.filterBarState = {};

/**
 * Initializes in a Strict env the code that manages the filter bar state.
 *
 * @since 5.0.0
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} obj tribe.filterBar.filterBarState
 *
 * @return {void}
 */
( function( $, obj ) {
	'use strict';

	/**
	 * Selectors used for configuration and setup.
	 *
	 * @since 5.0.0
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		actionDone: '[data-js="tribe-filter-bar__action-done"]',
		container: '[data-js="tribe-events-view"]', // @todo [BTRIA-621]: @paulmskim remove when proper hooks are in place.
		dataScript: '[data-js="tribe-events-view-data"]', // @todo [BTRIA-621]: @paulmskim remove when proper hooks are in place.
		filterBar: '[data-js~="tribe-filter-bar"]',
		filterBarOpen: '.tribe-filter-bar--open',
		filterButton: '[data-js~="tribe-events-filter-button"]',
		filterButtonActive: '.tribe-events-c-events-bar__filter-button--active',
		filterButtonText: '.tribe-events-c-events-bar__filter-button-text',
	};

	/**
	 * Open filter bar.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.openFilterBar = function( $container ) {
		var $filterButton = $container.find( obj.selectors.filterButton );
		var $filterButtonText = $filterButton.find( obj.selectors.filterButtonText );
		var $actionDone = $container.find( obj.selectors.actionDone );
		var $filterBar = $container.find( obj.selectors.filterBar );

		$filterButton.addClass( obj.selectors.filterButtonActive.className() );
		$filterButtonText.text( tribe_events_filter_bar_js_config.l10n.hide_filters );
		$filterBar.addClass( obj.selectors.filterBarOpen.className() );

		tribe.events.views.accordion.setOpenAccordionA11yAttrs( $filterButton, $filterBar );
		tribe.events.views.accordion.setOpenAccordionA11yAttrs( $actionDone, $filterBar );
	};

	/**
	 * Close filter bar.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.closeFilterBar = function( $container ) {
		var $filterButton = $container.find( obj.selectors.filterButton );
		var $filterButtonText = $filterButton.find( obj.selectors.filterButtonText );
		var $actionDone = $container.find( obj.selectors.actionDone );
		var $filterBar = $container.find( obj.selectors.filterBar );

		$filterButton.removeClass( obj.selectors.filterButtonActive.className() );
		$filterButtonText.text( tribe_events_filter_bar_js_config.l10n.show_filters );
		$filterBar.removeClass( obj.selectors.filterBarOpen.className() );

		tribe.events.views.accordion.setCloseAccordionA11yAttrs( $filterButton, $filterBar );
		tribe.events.views.accordion.setCloseAccordionA11yAttrs( $actionDone, $filterBar );
	};

	/**
	 * Setup filter bar.
	 *
	 * @since  5.0.2
	 *
	 * @param  {HTMLElement} container HTML element of the container containing the script tag calling setup
	 *
	 * @return {void}
	 */
	obj.setup = function( container ) {
		// @todo [BTRIA-621]: @paulmskim all of this is temporary, this will hook into the breakpoints.js in TEC when released with TEC containing the fixes.
		var $container = $( container );

		if ( ! $container.is( obj.selectors.container ) ) {
			return;
		}

		var $filterBar = $container.find( obj.selectors.filterBar );

		// Return early if mobile initial state control is false.
		if ( ! $filterBar.data( 'mobileInitialStateControl' ) ) {
			return;
		}

		var $data = $container.find( obj.selectors.dataScript );
		var data = {};

		// If we have data element set it up.
		if ( $data.length ) {
			data = JSON.parse( $.trim( $data.text() ) );
		}

		var breakpoints = Object.keys( data.breakpoints );

		breakpoints.forEach( function( breakpoint ) {
			if ( 'medium' === breakpoint && $container.outerWidth() < data.breakpoints[ breakpoint ] ) {
				obj.closeFilterBar( $container );
			}
		} );
	};
} )( jQuery, tribe.filterBar.filterBarState );
