/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since  5.4.0
 *
 * @type   {PlainObject}
 */
tribe.events = tribe.events || {};
tribe.events.pro = tribe.events.pro || {};
tribe.events.pro.widgets = tribe.events.pro.widgets || {};

/**
 * Configures Views Object in the Global Tribe variable
 *
 * @since  5.4.0
 *
 * @type   {PlainObject}
 */
tribe.events.pro.widgets.countdown = {};

/**
 * Initializes in a Strict env the code that manages the Event Views
 *
 * @since  5.4.0
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} obj tribe.events.pro.widgets.countdown
 *
 * @return {void}
 */
( function( $, obj ) {
	'use strict';
	var $document = $( document );

	/**
	 * Selectors used for configuration and setup
	 *
	 * @since 5.4.0
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		countdownWidget: '.tribe-events-view--widget-countdown',
		time: '[data-js="tribe-events-widget-countdown-time"]',
		days: '[data-js="tribe-events-widget-countdown-days"]',
		hours: '[data-js="tribe-events-widget-countdown-hours"]',
		minutes: '[data-js="tribe-events-widget-countdown-minutes"]',
		seconds: '[data-js="tribe-events-widget-countdown-seconds"]',
		complete: '[data-js="tribe-events-widget-countdown-complete"]',
		hidden: '.tribe-common-a11y-hidden',
	};

	/**
	 * Constants used for time.
	 *
	 * @since 5.4.0
	 *
	 * @type {PlainObject}
	 */
	obj.constants = {
		MINUTE_IN_SECONDS: 60,
		HOUR_IN_SECONDS: 60 * 60,
		DAY_IN_SECONDS: 60 * 60 * 24,
	};

	/**
	 * Adds zero padding.
	 *
	 * @since 5.4.0
	 *
	 * @param {string} number Time unit string
	 *
	 * @return {string}
	 */
	obj.zeroPad = function( number ) {
		var paddedNumber = parseInt( number, 10 );
		if ( paddedNumber < 10 ) {
			return '0' + paddedNumber;
		}
		return paddedNumber;
	};

	/**
	 * Decrements a given timer object and renders it to the appropriate div.
	 *
	 * @since 5.4.0
	 *
	 * @param {jQuery} $container jQuery object of widget container
	 * @param {Timer}  timer      Instance of Timer
	 *
	 * @return {void}
	 */
	obj.updateTimer = function( $container, timer ) {
		// Return early if timer is finished.
		if ( timer.seconds <= 0 ) {
			return;
		}

		timer.seconds -= 1;

		if ( timer.seconds > 0 ) {
			timer.elements.days.text( obj.zeroPad( ( timer.seconds ) / obj.constants.DAY_IN_SECONDS ) );
			timer.elements.hours.text( obj.zeroPad( ( timer.seconds % obj.constants.DAY_IN_SECONDS ) / obj.constants.HOUR_IN_SECONDS ) );
			timer.elements.minutes.text( obj.zeroPad( ( timer.seconds % obj.constants.HOUR_IN_SECONDS ) / obj.constants.MINUTE_IN_SECONDS ) );
			timer.elements.seconds.length && timer.elements.seconds.text( obj.zeroPad( timer.seconds % obj.constants.MINUTE_IN_SECONDS ) );
		} else {
			timer.elements.time.addClass( obj.selectors.hidden.className() );
			timer.elements.complete.removeClass( obj.selectors.hidden.className() );

			var timerId = $container.data( 'tribeEventsCountdownWidgetTimerId' );
			clearInterval( timerId );
		}
	};

	/**
	 * Set up the timer for the countdown widget.
	 *
	 * @since 5.4.0
	 *
	 * @param {jQuery} $container jQuery object of widget container
	 * @param {Timer}  timer      Instance of Timer
	 *
	 * @return {void}
	 */
	obj.setupTimer = function( $container, timer ) {
		var timerId = setInterval( obj.updateTimer, 1000, $container, timer );
		$container.data( 'tribeEventsCountdownWidgetTimerId', timerId );
	};

	/**
	 * Creates a new timer object.
	 *
	 * @since 5.4.0
	 *
	 * @param {number} seconds 	Seconds remaining
	 * @param {Object} elements Object containing references to the timer's DOM elements
	 *
	 * @return {void}
	 */
	obj.Timer = function( seconds, elements ) {
		this.seconds = seconds;
		this.elements = elements;
	};

	/**
	 * Setup the widget for views management
	 *
	 * @since 5.4.0
	 *
	 * @param  {jQuery}  $container jQuery object of widget container
	 *
	 * @return {void}
	 */
	obj.setup = function( $container ) {
		$container.find( obj.selectors.time )
		var seconds = parseInt( $container.find( obj.selectors.time ).data( 'seconds' ), 10 );

		// Return early if already complete.
		if ( seconds <= 0 ) {
			return;
		}

		var elements = {
			time: $container.find( obj.selectors.time ),
			days: $container.find( obj.selectors.days ),
			hours: $container.find( obj.selectors.hours ),
			minutes: $container.find( obj.selectors.minutes ),
			seconds: $container.find( obj.selectors.seconds ),
			complete: $container.find( obj.selectors.complete ),
		};

		// Create Timer object and push it to the timers collection
		var timer = new obj.Timer( seconds, elements );
		obj.setupTimer( $container, timer );
	};

	/**
	 * Initialize countdown widget JS
	 *
	 * @since  5.4.0
	 *
	 * @param  {Event}   event      event object for 'afterSetup.tribeEvents' event
	 * @param  {integer} index      jQuery.each index param from 'afterSetup.tribeEvents' event
	 * @param  {jQuery}  $container jQuery object of view container
	 * @param  {object}  data       data object passed from 'afterSetup.tribeEvents' event
	 *
	 * @return {void}
	 */
	obj.init = function( event, index, $container, data ) {
		// Return early if not countdown widget.
		if ( ! $container.is( obj.selectors.countdownWidget ) ) {
			return;
		}
		obj.setup( $container );
	};

	/**
	 * Handles the initialization of the manager when Document is ready.
	 *
	 * @since  5.4.0
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		$document.on( 'afterSetup.tribeEvents', tribe.events.views.manager.selectors.container, obj.init );
	};

	// Configure on document ready.
	$( obj.ready );
} )( jQuery, tribe.events.pro.widgets.countdown );
