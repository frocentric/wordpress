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
 * Configures Filter Toggle Object in the Global Tribe variable.
 *
 * @since 5.0.0
 *
 * @type   {PlainObject}
 */
tribe.filterBar.filterToggle = {};

/**
 * Initializes in a Strict env the code that manages the filter toggle.
 *
 * @since 5.0.0
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} obj tribe.filterBar.filterToggle
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
		filterBarHorizontal: '.tribe-events--filter-bar-horizontal',
		filterBar: '.tribe-filter-bar',
		filter: '.tribe-filter-bar-c-filter',
		filterOpen: '.tribe-filter-bar-c-filter--open',
		filterContainer: '.tribe-filter-bar-c-filter__container',
		filtersSliderContainer: '.tribe-filter-bar__filters-slider-container',
		filterToggle: '[data-js~="tribe-filter-bar-c-filter-toggle"]',
		filterClose: '[data-js~="tribe-filter-bar-c-filter-close"]',
		pillFilterToggle: '[data-js~="tribe-filter-bar-filters-slide-pill"]',
	};

	/**
	 * Closes all filters.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $filterBar jQuery object of filter bar.
	 * @param  {jQuery} $filter    jQuery object of filter to be closed.
	 *
	 * @return {void}
	 */
	obj.closeAllFilters = function( $filterBar ) {
		$filterBar.find( obj.selectors.filter ).each( function( index, filter ) {
			obj.closeFilter( $filterBar, $( filter ) );
		} );
	};

	/**
	 * Opens given filter.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $filterBar jQuery object of filter bar.
	 * @param  {jQuery} $filter    jQuery object of filter to be closed.
	 *
	 * @return {void}
	 */
	obj.openFilter = function( $filterBar, $filter ) {
		var $filterContainer = $filter.find( obj.selectors.filterContainer );
		var filterContainerId = $filterContainer.attr( 'id' );

		// Return early if no filter container ID.
		if ( ! filterContainerId ) {
			return;
		}

		var $filterToggle = $filter.find( obj.selectors.filterToggle );
		var $filterPillToggle = $filterBar.find( obj.selectors.pillFilterToggle + '[aria-controls="' + filterContainerId + '"]' );

		$filter.addClass( obj.selectors.filterOpen.className() );
		tribe.events.views.accordion.openAccordion( $filterToggle, $filterContainer );

		if ( $filterPillToggle.length ) {
			tribe.events.views.accordion.openAccordion( $filterPillToggle, $filterContainer );
		}
	};

	/**
	 * Closes given filter.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $filterBar jQuery object of filter bar.
	 * @param  {jQuery} $filter    jQuery object of filter to be closed.
	 *
	 * @return {void}
	 */
	obj.closeFilter = function( $filterBar, $filter ) {
		var $filterContainer = $filter.find( obj.selectors.filterContainer );
		var filterContainerId = $filterContainer.attr( 'id' );

		// Return early if no filter container ID.
		if ( ! filterContainerId ) {
			return;
		}

		var $filterToggle = $filter.find( obj.selectors.filterToggle );
		var $filterPillToggle = $filterBar.find( obj.selectors.pillFilterToggle + '[aria-controls="' + filterContainerId + '"]' );

		$filter.removeClass( obj.selectors.filterOpen.className() );
		tribe.events.views.accordion.closeAccordion( $filterToggle, $filterContainer );

		if ( $filterPillToggle.length ) {
			tribe.events.views.accordion.closeAccordion( $filterPillToggle, $filterContainer );
		}
	};

	/**
	 * Handler for toggle click event.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object of click event.
	 *
	 * @return {void}
	 */
	obj.handleToggleClick = function( event ) {
		var $filter = event.data.target.closest( obj.selectors.filter );
		$filter.toggleClass( obj.selectors.filterOpen.className() );
	};

	/**
	 * Handler for close click event.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object of click event.
	 *
	 * @return {void}
	 */
	obj.handleCloseClick = function( event ) {
		var $filter = event.data.target.closest( obj.selectors.filter );
		var $filterBar = $filter.closest( obj.selectors.filterBar );
		obj.closeFilter( $filterBar, $filter );
	};

	/**
	 * Handler for pill toggle click event.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object of click event.
	 *
	 * @return {void}
	 */
	obj.handlePillToggleClick = function( event ) {
		var $pillToggle = event.data.target;
		var filterContainerId = $pillToggle.attr( 'aria-controls' );

		// Return early if no filter container ID.
		if ( ! filterContainerId ) {
			return;
		}

		var $filterBar = $pillToggle.closest( obj.selectors.filterBar );
		var $filterContainer = $filterBar.find( '#' + filterContainerId );
		var $filter = $filterContainer.closest( obj.selectors.filter );
		var shouldOpen = ! $filter.is( obj.selectors.filterOpen );

		obj.closeAllFilters( $filterBar );

		if ( shouldOpen ) {
			obj.openFilter( $filterBar, $filter );
		}
	};

	/**
	 * Handler for resize event on horizontal filter bar.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object of click event.
	 *
	 * @return {void}
	 */
	obj.handleResize = function( event ) {
		var $container = event.data.container;
		var $filterBar = $container.find( obj.selectors.filterBar );
		var state = $filterBar.data( 'tribeEventsState' );
		var containerState = $container.data( 'tribeEventsState' );
		var isMobile = containerState.isMobile;

		// Close all filters on resize to desktop.
		if ( ! isMobile && ! state.filterToggleDesktopInitialized ) {
			obj.closeAllFilters( $filterBar );

			state.filterToggleDesktopInitialized = true;
			$filterBar.data( 'tribeEventsState', state );

		// Reset `filterToggleDesktopInitialized` state on resize to mobile.
		} else if ( isMobile && state.filterToggleDesktopInitialized ) {
			state.filterToggleDesktopInitialized = false;
			$filterBar.data( 'tribeEventsState', state );
		}
	};

	/**
	 * Handler for click event on document on horizontal filter bar.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object of click event.
	 *
	 * @return {void}
	 */
	obj.handleClick = function( event ) {
		var $container = event.data.container;
		var containerState = $container.data( 'tribeEventsState' );
		var isMobile = containerState.isMobile;

		// Bail early if mobile.
		if ( isMobile ) {
			return;
		}

		var $target = $( event.target );
		var isParentFilter = Boolean( $target.closest( obj.selectors.filter ).length );
		var isParentFiltersSlidePill = Boolean( $target.closest( obj.selectors.filtersSliderContainer ).length );

		if ( ! isParentFilter && ! isParentFiltersSlidePill ) {
			var $filterBar = $container.find( obj.selectors.filterBar );
			obj.closeAllFilters( $filterBar );
		}
	};

	/**
	 * Unbind events for filter buttons.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.unbindFilterEvents = function( $container ) {
		var $filterToggles = $container.find( obj.selectors.filterToggle );
		var $filterCloses = $container.find( obj.selectors.filterClose );
		var $pillFilterToggles = $container.find( obj.selectors.pillFilterToggle );

		$filterToggles.off();
		$filterCloses.off();
		$pillFilterToggles.off();
	};

	/**
	 * Bind events for filter buttons.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.bindFilterEvents = function( $container ) {
		var $filterToggles = $container.find( obj.selectors.filterToggle );
		var $filterCloses = $container.find( obj.selectors.filterClose );
		var $pillFilterToggles = $container.find( obj.selectors.pillFilterToggle );

		// Bind click events for filter toggles.
		$filterToggles.each( function( index, toggle ) {
			var $toggle = $( toggle );
			$toggle.on( 'click', { target: $toggle, container: $container }, obj.handleToggleClick );
		} );

		// Bind click events for pill filter toggles.
		$pillFilterToggles.each( function( index, toggle ) {
			var $toggle = $( toggle );
			$toggle.on( 'click', { target: $toggle, container: $container }, obj.handlePillToggleClick );
		} );

		// Bind click events for filter close.
		$filterCloses.each( function( index, close ) {
			var $close = $( close );
			$close.on( 'click', { target: $close, container: $container }, obj.handleCloseClick );
		} );
	};

	/**
	 * Unbind events for filter toggles functionality.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.unbindEvents = function( $container ) {
		obj.unbindFilterEvents( $container );

		// Unbind resize event on horizontal filter bar.
		if ( $container.is( obj.selectors.filterBarHorizontal ) ) {
			$container.off( 'resize.tribeEvents', obj.handleResize );
			$document.off( 'click', obj.handleClick );
		}
	};

	/**
	 * Bind events for filter toggles functionality.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.bindEvents = function( $container ) {
		obj.bindFilterEvents( $container );

		// Bind resize event on horizontal filter bar.
		if ( $container.is( obj.selectors.filterBarHorizontal ) ) {
			$container.on( 'resize.tribeEvents', { container: $container }, obj.handleResize );
			$document.on( 'click', { container: $container }, obj.handleClick );
		}
	};

	/**
	 * Initializes filter bar toggle state.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.initState = function( $container ) {
		// Return early if filter bar is not horizontal.
		if ( ! $container.is( obj.selectors.filterBarHorizontal ) ) {
			return;
		}

		var $filterBar = $container.find( obj.selectors.filterBar );
		var containerState = $container.data( 'tribeEventsState' );
		var isMobile = containerState.isMobile;

		var state = {
			filterToggleDesktopInitialized: ! isMobile,
		};

		$filterBar.data( 'tribeEventsState', state );
	};

	/**
	 * Deinitialize filter toggle JS.
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
	 * Initialize filter toggles JS.
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
		obj.initState( $container );
		obj.bindEvents( $container );
		$container.on( 'beforeAjaxSuccess.tribeEvents', { container: $container }, obj.deinit );
	};

	/**
	 * Handles the initialization of filter toggles when Document is ready.
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
} )( jQuery, tribe.filterBar.filterToggle );
