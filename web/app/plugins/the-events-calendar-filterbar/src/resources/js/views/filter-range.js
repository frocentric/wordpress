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
 * Configures Filter Range Object in the Global Tribe variable.
 *
 * @since 5.0.0
 *
 * @type   {PlainObject}
 */
tribe.filterBar.filterRange = {};

/**
 * Initializes in a Strict env the code that manages the filter range.
 *
 * @since 5.0.0
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} _   Underscore.js
 * @param  {PlainObject} obj tribe.filterBar.filterRange
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
		rangeInput: '.tribe-filter-bar-c-range__input',
		rangeLabel: '.tribe-filter-bar-c-range__label',
		rangeSlider: '[data-js="tribe-filter-bar-c-range-slider"]',
	};

	/**
	 * Handle range slidechange event.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event}       event event object of change event.
	 * @param  {PlainObject} ui    object containing properties of the UI.
	 *
	 * @return {void}
	 */
	obj.handleRangeSlideChange = function( event, ui ) {
		var $rangeSlider = event.data.target;
		var $rangeInput = $rangeSlider.siblings( obj.selectors.rangeInput );
		var key = $rangeInput.attr( 'name' );

		// Return early if no key.
		if ( ! key ) {
			return;
		}

		var min = Number( $rangeSlider.data( 'min' ) );
		var max = Number( $rangeSlider.data( 'max' ) );

		// Return early if min or max are not numbers.
		if ( Number.isNaN( min ) || Number.isNaN( max ) ) {
			return;
		}

		var location = tribe.filterBar.filters.removeKeyValueFromQuery( window.location, key, true );

		// If range slider low is greater than min or high is less than max, add range to query.
		if ( ui.values[ 0 ] > min || ui.values[ 1 ] < max ) {
			var value = ui.values.join( '-' );
			location = tribe.filterBar.filters.addKeyValueToQuery( location, key, value );
		}

		tribe.filterBar.filters.submitRequest( event.data.container, location.href );
	};

	/**
	 * Handle range slide event.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event}       event event object of change event.
	 * @param  {PlainObject} ui    object containing properties of the UI.
	 *
	 * @return {void}
	 */
	obj.handleRangeSlide = function( event, ui ) {
		var $rangeSlider = event.data.target;
		var $rangeLabel = $rangeSlider.siblings( obj.selectors.rangeLabel );
		var templateString = tribe_events_filter_bar_js_config.events.reverse_currency_position
			? tribe_events_filter_bar_js_config.l10n.cost_range_currency_symbol_after
			: tribe_events_filter_bar_js_config.l10n.cost_range_currency_symbol_before;

		var template = _.template( templateString );
		$rangeLabel.text( template( {
			currency_symbol: tribe_events_filter_bar_js_config.events.currency_symbol,
			cost_low: ui.values[ 0 ],
			cost_high: ui.values[ 1 ],
		} ) );
	};

	/**
	 * Unbind events for filter range functionality.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.unbindEvents = function( $container ) {
		$container.find( obj.selectors.rangeSlider ).off();
	};

	/**
	 * Bind events for filter range functionality.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.bindEvents = function( $container ) {
		$container
			.find( obj.selectors.rangeSlider )
			.each( function( index, rangeSlider ) {
				var $rangeSlider = $( rangeSlider );
				$rangeSlider
					.on( 'slide', { target: $rangeSlider, container: $container }, obj.handleRangeSlide )
					.on( 'slidechange', { target: $rangeSlider, container: $container }, obj.handleRangeSlideChange );
			} );
	};

	/**
	 * Initializes filter range slider.
	 *
	 * @param  {jQuery}  $rangeSlider jQuery object of range slider.
	 *
	 * @return {void}
	 */
	obj.initRangeSlider = function( $rangeSlider ) {
		var $rangeInput = $rangeSlider.siblings( obj.selectors.rangeInput );
		var min = $rangeSlider.data( 'min' );
		var max = $rangeSlider.data( 'max' );
		var low, high;
		var value = $rangeInput.attr( 'value' );

		if ( ! value ) {
			low = min;
			high = max;
		} else {
			[ low, high ] = value.split( '-' );
		}

		$rangeSlider.slider( {
			range: true,
			min: min,
			max: max,
			values: [ low, high ],
		} );
	};

	/**
	 * Deinitializes filter range sliders.
	 *
	 * @param  {jQuery}  $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.deinitRangeSliders = function( $container ) {
		$container
			.find( obj.selectors.rangeSlider )
			.slider( 'destroy' );
	};

	/**
	 * Initializes filter range sliders.
	 *
	 * @param  {jQuery}  $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.initRangeSliders = function( $container ) {
		$container
			.find( obj.selectors.rangeSlider )
			.each( function( index, rangeSlider ) {
				obj.initRangeSlider( $( rangeSlider ) );
			} );
	};

	/**
	 * Deinitialize filter range JS.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object for 'beforeAjaxSuccess.tribeEvents' event.
	 *
	 * @return {void}
	 */
	obj.deinit = function( event ) {
		var $container = event.data.container;
		obj.deinitRangeSliders( $container );
		obj.unbindEvents( $container );
		$container.off( 'beforeAjaxSuccess.tribeEvents', obj.deinit );
	};

	/**
	 * Initialize filter range JS.
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
		obj.initRangeSliders( $container );
		obj.bindEvents( $container );
		$container.on( 'beforeAjaxSuccess.tribeEvents', { container: $container }, obj.deinit );
	};

	/**
	 * Handles the initialization of filter range when Document is ready.
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
} )( jQuery, _, tribe.filterBar.filterRange );
