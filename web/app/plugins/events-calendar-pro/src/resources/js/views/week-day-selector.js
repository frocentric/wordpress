/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since 4.7.6
 *
 * @type   {PlainObject}
 */
tribe.events = tribe.events || {};
tribe.events.views = tribe.events.views || {};

/**
 * Configures Week Day Selector Object in the Global Tribe variable
 *
 * @since 4.7.6
 *
 * @type  {PlainObject}
 */
tribe.events.views.weekDaySelector = {};

/**
 * Initializes in a Strict env the code that manages the Event Views
 *
 * @since 4.7.6
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} obj tribe.events.views.manager
 *
 * @return {void}
 */
( function( $, obj ) {
	'use strict';
	var $document = $( document );

	/**
	 * Selectors used for configuration and setup
	 *
	 * @since 4.7.6
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		daySelectorDay: '[data-js="tribe-events-pro-week-day-selector-day"]',
		daySelectorDayActiveClass: '.tribe-events-pro-week-day-selector__day--active',
		mobileEventsDayActiveClass: '.tribe-events-pro-week-mobile-events__day--active',
	};

	/**
	 * Opens day with mobile events
	 *
	 * @since 4.7.6
	 *
	 * @param {jQuery} $header jQuery object of day selector day button
	 * @param {jQuery} $content jQuery object of mobile events container
	 *
	 * @return {void}
	 */
	obj.openDay = function( $header, $content ) {
		// only perform accordion actions if $header has aria-controls attribute.
		var contentId = $header.attr( 'aria-controls' );
		if ( contentId ) {
			tribe.events.views.accordion.openAccordion( $header, $content );
		}

		$header.addClass( obj.selectors.daySelectorDayActiveClass.className() );
	};

	/**
	 * Closes day with mobile events
	 *
	 * @since 4.7.6
	 *
	 * @param {jQuery} $header jQuery object of day selector day button
	 * @param {jQuery} $content jQuery object of mobile events container
	 *
	 * @return {void}
	 */
	obj.closeDay = function( $header, $content ) {
		// only perform accordion actions if $header has aria-controls attribute.
		var contentId = $header.attr( 'aria-controls' );
		if ( contentId ) {
			tribe.events.views.accordion.closeAccordion( $header, $content );
		}

		$header.removeClass( obj.selectors.daySelectorDayActiveClass.className() );
		$content.removeClass( obj.selectors.mobileEventsDayActiveClass.className() );
	};

	/**
	 * Closes all days
	 *
	 * @since 4.7.6
	 *
	 * @param {jQuery} $container jQuery object of view container
	 *
	 * @return {void}
	 */
	obj.closeAllDays = function( $container ) {
		$container
			.find( obj.selectors.daySelectorDay )
			.each( function( index, header ) {
				var $header = $( header );
				var contentId = $header.attr( 'aria-controls' );

				/**
				 * Define empty jQuery object in the case contentId is false or undefined
				 * so that we don't get selectors like #false or #undefined.
				 * Also only perform accordion actions if header has aria-controls attribute.
				 */
				var $content = $( '' );
				if ( contentId ) {
					$content = $container.find( '#' + contentId );
					tribe.events.views.accordion.closeAccordion( $header, $content );
				}

				obj.closeDay( $header, $content );
			} );
	}

	/**
	 * Handle click event on day button
	 *
	 * @since 4.7.6
	 *
	 * @param {Event} event event object of click event
	 *
	 * @return {void}
	 */
	obj.handleClick = function( event ) {
		var $container = event.data.container;
		var $header = $( event.data.target );
		var contentId = $header.attr( 'aria-controls' );

		/**
		 * Define empty jQuery object in the case contentId is false or undefined
		 * so that we don't get selectors like #false or #undefined.
		 */
		var $content = $( '' );
		if ( contentId ) {
			$content = $container.find( '#' + contentId );
		}

		obj.closeAllDays( $container );
		obj.openDay( $header, $content );
	};

	/**
	 * Deinitialize day selector
	 *
	 * @since 4.7.6
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.deinitDaySelector = function( $container ) {
		var $daySelector = $container.find( obj.selectors.daySelector );
		$daySelector
			.find( obj.selectors.daySelectorDay )
			.each( function( index, day ) {
				$( day ).off( 'click', obj.handleClick );
			} );
	};

	/**
	 * Initialize day selector
	 *
	 * @since 4.7.6
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.initDaySelector = function( $container ) {
		$container
			.find( obj.selectors.daySelectorDay )
			.each( function( index, day ) {
				$( day ).on( 'click', {
					target: day,
					container: $container,
				}, obj.handleClick );
			} );
	};

	/**
	 * Deinitialize week day selector.
	 *
	 * @since 4.7.6
	 *
	 * @param  {Event}       event    event object for 'afterSetup.tribeEvents' event
	 * @param  {jqXHR}       jqXHR    Request object
	 * @param  {PlainObject} settings Settings that this request was made with
	 *
	 * @return {void}
	 */
	obj.deinit = function( event, jqXHR, settings ) {
		var $container = event.data.container;
		obj.deinitDaySelector( $container );
		$container.off( 'beforeAjaxSuccess.tribeEvents', obj.deinit );
	};

	/**
	 * Initialize week day selector.
	 *
	 * @since 4.7.8
	 *
	 * @param {Event}   event      JS event triggered.
	 * @param {integer} index      jQuery.each index param from 'afterSetup.tribeEvents' event.
	 * @param {jQuery}  $container jQuery object of view container.
	 * @param {object}  data       data object passed from 'afterSetup.tribeEvents' event.
	 *
	 * @return {void}
	 */
	obj.init = function( event, index, $container, data ) {
		if ( 'week' !== data.slug ) {
			return;
		}

		obj.initDaySelector( $container );
		$container.on( 'beforeAjaxSuccess.tribeEvents', { container: $container }, obj.deinit );
	};

	/**
	 * Handles the initialization of the week day selector when Document is ready
	 *
	 * @since 4.7.6
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		$document.on( 'afterSetup.tribeEvents', tribe.events.views.manager.selectors.container, obj.init );
	};

	// Configure on document ready
	$document.ready( obj.ready );
} )( jQuery, tribe.events.views.weekDaySelector );
