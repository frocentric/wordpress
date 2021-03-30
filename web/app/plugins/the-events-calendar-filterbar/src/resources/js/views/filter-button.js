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
 * Configures Filter Button Object in the Global Tribe variable.
 *
 * @since 5.0.0
 *
 * @type   {PlainObject}
 */
tribe.filterBar.filterButton = {};

/**
 * Initializes in a Strict env the code that manages the filter Button.
 *
 * @since 5.0.0
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} obj tribe.filterBar.filterButton
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
		actionDone: '[data-js="tribe-filter-bar__action-done"]',
		filterBarVertical: '.tribe-events--filter-bar-vertical',
		filterBar: '[data-js~="tribe-filter-bar"]',
		filterButton: '[data-js~="tribe-events-filter-button"]',
		filterButtonActive: '.tribe-events-c-events-bar__filter-button--active',
		filtersSliderContainer: '[data-js="tribe-filter-bar-filters-slider-container"]',
		select2ChoiceRemove: '.select2-selection__choice__remove',
	};

	/**
	 * Handler for resize event on vertical filter bar.
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

		// Set filter bar state on resize to desktop.
		if ( ! isMobile && ! state.filterButtonDesktopInitialized ) {
			// Open vertical filter bar on resize to desktop.
			if ( $container.is( obj.selectors.filterBarVertical ) ) {
				tribe.filterBar.filterBarState.openFilterBar( $container );
			}

			state.filterButtonDesktopInitialized = true;
			$filterBar.data( 'tribeEventsState', state );

		// Close filter bar on resize to mobile.
		} else if ( isMobile && state.filterButtonDesktopInitialized ) {
			tribe.filterBar.filterBarState.closeFilterBar( $container );
			state.filterButtonDesktopInitialized = false;
			$filterBar.data( 'tribeEventsState', state );
		}
	};

	/**
	 * Handler for document click event.
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

		// Return early if not mobile.
		if ( ! isMobile ) {
			return;
		}

		var $target = $( event.target );
		var isParentFilterBar = Boolean( $target.closest( obj.selectors.filterBar ).length );
		var isParentFilterButton = Boolean( $target.closest( obj.selectors.filterButton ).length );
		var isParentSelect2ChoiceRemove = Boolean( $target.closest( obj.selectors.select2ChoiceRemove ).length );

		if ( ! isParentFilterBar && ! isParentFilterButton && ! isParentSelect2ChoiceRemove ) {
			tribe.filterBar.filterBarState.closeFilterBar( $container );
		}
	};

	/**
	 * Handler for action done button click event.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object of click event.
	 *
	 * @return {void}
	 */
	obj.handleActionDoneClick = function( event ) {
		tribe.filterBar.filterBarState.closeFilterBar( event.data.container );
	};

	/**
	 * Handler for filter button click event.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object of click event.
	 *
	 * @return {void}
	 */
	obj.handleFilterButtonClick = function( event ) {
		var $container = event.data.container;
		var $filterButton = event.data.target;
		var isOpen = $filterButton.is( obj.selectors.filterButtonActive );

		if ( isOpen ) {
			tribe.filterBar.filterBarState.closeFilterBar( $container );
		} else {
			tribe.filterBar.filterBarState.openFilterBar( $container );
		}

		const data = { container: $container };
		tribe.filterBar.filterBarSlider.handleResize( { data: data } );
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
		var $filterButton = $container.find( obj.selectors.filterButton );
		var $actionDone = $container.find( obj.selectors.actionDone );
		$filterButton.off( 'click', obj.handleFilterButtonClick );
		$actionDone.off( 'click', obj.handleActionDoneClick );
		$document.off( 'click', obj.handleClick );
		$container.off( 'resize.tribeEvents', obj.handleResize );
	};

	/**
	 * Bind events for filter button functionality.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.bindEvents = function( $container ) {
		var $filterButton = $container.find( obj.selectors.filterButton );
		var $actionDone = $container.find( obj.selectors.actionDone );
		$filterButton.on(
			'click',
			{ target: $filterButton, actionDone: $actionDone, container: $container },
			obj.handleFilterButtonClick,
		);
		$actionDone.on(
			'click',
			{ target: $actionDone, filterButton: $filterButton, container: $container },
			obj.handleActionDoneClick,
		);
		$document.on( 'click', { container: $container }, obj.handleClick );
		$container.on( 'resize.tribeEvents', { container: $container }, obj.handleResize );
	};

	/**
	 * Initializes filter button state.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.initState = function( $container ) {
		var $filterBar = $container.find( obj.selectors.filterBar );
		var containerState = $container.data( 'tribeEventsState' );
		var isMobile = containerState.isMobile;

		var state = {
			filterButtonDesktopInitialized: ! isMobile,
		};

		$filterBar.data( 'tribeEventsState', state );
	};

	/**
	 * Deinitialize filter button JS.
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
	 * Initialize filter button JS.
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
	 * Handles the initialization of filter button when Document is ready.
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
} )( jQuery, tribe.filterBar.filterButton );
