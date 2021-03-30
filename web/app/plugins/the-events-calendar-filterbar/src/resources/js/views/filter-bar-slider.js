/* global tribe, jQuery, Swiper */
/* eslint-disable no-new, no-var, strict */

/**
 * Makes sure we have all the required levels on the Tribe Object.
 *
 * @since 5.0.0
 *
 * @type   {PlainObject}
 */
tribe.filterBar = tribe.filterBar || {};

/**
 * Configures Filter Bar Slider Object in the Global Tribe variable.
 *
 * @since 5.0.0
 *
 * @type   {PlainObject}
 */
tribe.filterBar.filterBarSlider = {};

/**
 * Initializes in a Strict env the code that manages the filter bar slider.
 *
 * @since 5.0.0
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} obj tribe.filterBar.filterBarSlider
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
		filtersSliderContainer: '[data-js="tribe-filter-bar-filters-slider-container"]',
		filtersSliderNav: '.tribe-filter-bar__filters-slider-nav',
		filtersSliderNavOverflowStart: '.tribe-filter-bar__filters-slider-nav--overflow-start',
		filtersSliderNavOverflowEnd: '.tribe-filter-bar__filters-slider-nav--overflow-end',
		filtersSliderNavButtonPrev: '[data-js~="tribe-filter-bar-filters-slider-nav-button-prev"]',
		filtersSliderNavButtonNext: '[data-js~="tribe-filter-bar-filters-slider-nav-button-next"]',
		filtersContainer: '[data-js="tribe-filter-bar-filters-container"]',
	};

	/**
	 * Handler for slider translate event.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.handleSliderOverflow = function( $container ) {
		var $filterBar = $container.find( obj.selectors.filterBar );
		var $filtersSliderContainer = $filterBar.find( obj.selectors.filtersSliderContainer );
		var $filtersSliderNav = $filtersSliderContainer.find( obj.selectors.filtersSliderNav );

		$filtersSliderNav
			.removeClass( obj.selectors.filtersSliderNavOverflowStart.className() )
			.removeClass( obj.selectors.filtersSliderNavOverflowEnd.className() );

		var swiper = $filtersSliderContainer[ 0 ].swiper;
		if ( ! swiper.isBeginning ) {
			$filtersSliderNav.addClass( obj.selectors.filtersSliderNavOverflowStart.className() );
		}
		if ( ! swiper.isEnd ) {
			$filtersSliderNav.addClass( obj.selectors.filtersSliderNavOverflowEnd.className() );
		}
	};

	/**
	 * Handler for slider translate event.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 * @param  {Number} translate  Value of slider translation.
	 *
	 * @return {void}
	 */
	obj.handleSliderTranslate = function( $container, translate ) {
		var $filterBar = $container.find( obj.selectors.filterBar );
		tribe.filterBar.filterToggle.closeAllFilters( $filterBar );

		var $filtersContainer = $filterBar.find( obj.selectors.filtersContainer );
		$filtersContainer.css( { transform: 'translateX(' + translate + 'px)' } );
	};

	/**
	 * Deinitializes filter bar slider.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.deinitSlider = function( $container ) {
		var $filtersSliderContainer = $container.find( obj.selectors.filtersSliderContainer );

		// If slider container doesn't exist, return early.
		if ( ! $filtersSliderContainer.length ) {
			return;
		}

		// If slider does not exist, return early.
		if ( ! $filtersSliderContainer[ 0 ].swiper ) {
			return;
		}

		// Deinitialize slider.
		var $filtersContainer = $container.find( obj.selectors.filtersContainer );
		$filtersSliderContainer[ 0 ].swiper.destroy();
		$filtersContainer.css( { transform: '' } );

		// Remove classes for overflow.
		$filtersSliderContainer
			.find( obj.selectors.filtersSliderNav )
			.removeClass( obj.selectors.filtersSliderNavOverflowStart.className() )
			.removeClass( obj.selectors.filtersSliderNavOverflowEnd.className() );
	};

	/**
	 * Initializes filter bar slider.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.initSlider = function( $container ) {
		var $filtersSliderContainer = $container.find( obj.selectors.filtersSliderContainer );

		// If slider container doesn't exist, return early.
		if ( ! $filtersSliderContainer.length ) {
			return;
		}

		// If slider exists, update and return early.
		if ( $filtersSliderContainer[ 0 ].swiper ) {
			$filtersSliderContainer[ 0 ].swiper.update();
			return;
		}

		// Initalize slider.
		new Swiper( $filtersSliderContainer[ 0 ], {
			slidesPerView: 'auto',
			resistanceRatio: 0,
			freeMode: true,
			freeModeMomentumBounce: false,
			navigation: {
				prevEl: $filtersSliderContainer.find( obj.selectors.filtersSliderNavButtonPrev )[ 0 ],
				nextEl: $filtersSliderContainer.find( obj.selectors.filtersSliderNavButtonNext )[ 0 ],
			},
			on: {
				init: function() {
					obj.handleSliderOverflow( $container );
				},
				setTranslate: function( translate ) {
					obj.handleSliderTranslate( $container, translate );
				},
				fromEdge: function() {
					obj.handleSliderOverflow( $container );
				},
				toEdge: function() {
					obj.handleSliderOverflow( $container );
				},
			},
		} );
	};

	/**
	 * Handler for resize event.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object of click event.
	 *
	 * @return {void}
	 */
	obj.handleResize = function( event ) {
		var $container = event.data.container;
		var containerState = $container.data( 'tribeEventsState' );
		var isMobile = containerState.isMobile;

		if ( isMobile ) {
			obj.deinitSlider( $container );
		} else {
			obj.initSlider( $container );
		}
	};

	/**
	 * Unbind events for filter bar slider functionality.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.unbindEvents = function( $container ) {
		$container.off( 'resize.tribeEvents', obj.handleResize );
	};

	/**
	 * Bind events for filter bar slider functionality.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.bindEvents = function( $container ) {
		$container.on( 'resize.tribeEvents', { container: $container }, obj.handleResize );
	};

	/**
	 * Deinitialize filter bar slider JS.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object for 'beforeAjaxSuccess.tribeEvents' event.
	 *
	 * @return {void}
	 */
	obj.deinit = function( event ) {
		var $container = event.data.container;
		obj.deinitSlider( $container );
		obj.unbindEvents( $container );
		$container.off( 'beforeAjaxSuccess.tribeEvents', obj.deinit );
	};

	/**
	 * Initialize filter bar slider JS.
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
		// Return early if filter bar is not horizontal.
		if ( ! $container.is( obj.selectors.filterBarHorizontal ) ) {
			return;
		}

		obj.handleResize( { data: { container: $container } } );
		obj.bindEvents( $container );
		$container.on( 'beforeAjaxSuccess.tribeEvents', { container: $container }, obj.deinit );
	};

	/**
	 * Handles the initialization of filter bar slider when Document is ready.
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
} )( jQuery, tribe.filterBar.filterBarSlider );
