/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since 4.7.9
 *
 * @type   {PlainObject}
 */
tribe.events = tribe.events || {};
tribe.events.views = tribe.events.views || {};

/**
 * Configures Week Multiday Toggle Object in the Global Tribe variable
 *
 * @since 4.7.9
 *
 * @type  {PlainObject}
 */
tribe.events.views.weekEventLink = {};

/**
 * Initializes in a Strict env the code that manages the Event Views
 *
 * @since 4.7.9
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
	 * Config used for link hover
	 *
	 * @since 4.7.9
	 *
	 * @type {PlainObject}
	 */
	obj.config = {
		delayHoverIn: 600,
	};

	/**
	 * Selectors used for configuration and setup
	 *
	 * @since 4.7.9
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		weekEventLink: '[data-js~="tribe-events-pro-week-grid-event-link"]',
		weekEventLinkHoverClass: '.tribe-events-pro-week-grid__event-link--hover',
		weekEventLinkIntendedClass: '.tribe-events-pro-week-grid__event-link--intended',
	};

	/**
	 * Add intended class to event link after timeout
	 *
	 * @since 4.7.9
	 *
	 * @param {jQuery} $link jQuery object of link
	 *
	 * @return {void}
	 */
	obj.addIntendedClass = function( $link ) {
		setTimeout( function() {
			// if link is not focused or hovered
			if (
				! $link.is( ':focus' ) &&
				! $link.hasClass( obj.selectors.weekEventLinkHoverClass.className() )
			) {
				return;
			}

			$link.addClass( obj.selectors.weekEventLinkIntendedClass.className() );
		}, obj.config.delayHoverIn );
	};

	/**
	 * Remove intended class from event link
	 *
	 * @since 4.7.9
	 *
	 * @param {jQuery} $link jQuery object of link
	 *
	 * @return {void}
	 */
	obj.removeIntendedClass = function( $link ) {
		// if link is focused or hovered
		if (
			$link.is( ':focus' ) ||
			$link.hasClass( obj.selectors.weekEventLinkHoverClass.className() )
		) {
			return;
		}

		$link.removeClass( obj.selectors.weekEventLinkIntendedClass.className() );
	};

	/**
	 * Handle mouse enter event
	 *
	 * @since 4.7.9
	 *
	 * @param {Event} event Event object
	 *
	 * @return {void}
	 */
	obj.handleMouseEnter = function( event ) {
		var $link = event.data.target;
		$link.addClass( obj.selectors.weekEventLinkHoverClass.className() );
		obj.addIntendedClass( $link );
	};

	/**
	 * Handle mouse leave event
	 *
	 * @since 4.7.9
	 *
	 * @param {Event} event Event object
	 *
	 * @return {void}
	 */
	obj.handleMouseLeave = function( event ) {
		var $link = event.data.target;
		$link.removeClass( obj.selectors.weekEventLinkHoverClass.className() );
		obj.removeIntendedClass( $link );
	};

	/**
	 * Handle focus event
	 *
	 * @since 4.7.9
	 *
	 * @param {Event} event Event object
	 *
	 * @return {void}
	 */
	obj.handleFocus = function( event ) {
		var $link = event.data.target;
		obj.addIntendedClass( $link );
	};

	/**
	 * Handle blur event
	 *
	 * @since 4.7.9
	 *
	 * @param {Event} event Event object
	 *
	 * @return {void}
	 */
	obj.handleBlur = function( event ) {
		var $link = event.data.target;
		obj.removeIntendedClass( $link );
	};

	/**
	 * Deinitialize week event link
	 *
	 * @since 4.7.9
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.deinitEventLink = function( $container ) {
		$container
			.find( obj.selectors.weekEventLink )
			.each( function( index, link ) {
				$( link ).off();
			} );
	};


	/**
	 * Initialize week event link
	 *
	 * @since 4.7.9
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.initEventLink = function( $container ) {
		$container
			.find( obj.selectors.weekEventLink )
			.each( function( index, link ) {
				var $link = $( link );
				$link
					.on( 'mouseenter touchstart', { target: $link }, obj.handleMouseEnter )
					.on( 'mouseleave touchstart', { target: $link }, obj.handleMouseLeave )
					.on( 'focus', { target: $link }, obj.handleFocus )
					.on( 'blur', { target: $link }, obj.handleBlur );
			} );
	};

	/**
	 * Deinitialize week event link JS.
	 *
	 * @since  4.7.9
	 *
	 * @param  {Event}       event    event object for 'afterSetup.tribeEvents' event
	 * @param  {jqXHR}       jqXHR    Request object
	 * @param  {PlainObject} settings Settings that this request was made with
	 *
	 * @return {void}
	 */
	obj.deinit = function( event, jqXHR, settings ) {
		var $container = event.data.container;
		obj.deinitEventLink( $container );
		$container.off( 'beforeAjaxSuccess.tribeEvents', obj.deinit );
	};


	/**
	 * Initialize week event link JS.
	 *
	 * @since 4.7.9
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

		obj.initEventLink( $container );
		$container.on( 'beforeAjaxSuccess.tribeEvents', { container: $container }, obj.deinit );
	};

	/**
	 * Handles the initialization of the week grid scroller when Document is ready
	 *
	 * @since 4.7.9
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		$document.on( 'afterSetup.tribeEvents', tribe.events.views.manager.selectors.container, obj.init );
	};

	// Configure on document ready
	$document.ready( obj.ready );
} )( jQuery, tribe.events.views.weekEventLink );
