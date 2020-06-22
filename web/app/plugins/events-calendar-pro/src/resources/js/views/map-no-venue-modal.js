/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since 4.7.9
 *
 * @type  {PlainObject}
 */
tribe.events = tribe.events || {};
tribe.events.views = tribe.events.views || {};

/**
 * Configures Map No Venue Modal Object in the Global Tribe variable
 *
 * @since 4.7.9
 *
 * @type  {PlainObject}
 */
tribe.events.views.mapNoVenueModal = {};

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
	 * Selectors used for configuration and setup
	 *
	 * @since 4.7.9
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		noVenueModal: '[data-js="tribe-events-pro-map-no-venue-modal"]',
		noVenueModalClose: '[data-js="tribe-events-pro-map-no-venue-modal-close"]',
		noVenueModalLink: '[data-js="tribe-events-pro-map-no-venue-modal-link"]',
		tribeCommonA11yHiddenClass: '.tribe-common-a11y-hidden',
	};

	/**
	 * Open no venue modal.
	 *
	 * @since 4.7.9
	 *
	 * @param {Event} event event object for `openNoVenueModal.tribeEvents` event
	 *
	 * @return {void}
	 */
	obj.openNoVenueModal = function( event ) {
		var $modal = event.data.modal;

		$modal.removeClass( obj.selectors.tribeCommonA11yHiddenClass.className() );
	};

	/**
	 * Close no venue modal.
	 *
	 * @since 4.7.9
	 *
	 * @param {Event} event event object for `closeNoVenueModal.tribeEvents` or `click` event
	 *
	 * @return {void}
	 */
	obj.closeNoVenueModal = function( event ) {
		var $modal = event.data.modal;

		$modal.addClass( obj.selectors.tribeCommonA11yHiddenClass.className() );
	};

	/**
	 * Set no venue modal link.
	 *
	 * @since 4.7.9
	 *
	 * @param {Event}  event event object for `setNoVenueModalLink.tribeEvents` event
	 * @param {string} link  URL of link to be set
	 *
	 * @return {void}
	 */
	obj.setNoVenueModalLink = function( event, link ) {
		var $modal = event.data.modal;

		$modal
			.find( obj.selectors.noVenueModalLink )
			.attr( 'href', link );
	}

	/**
	 * Unbind events for map no venue modal.
	 *
	 * @since 4.7.9
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.unbindEvents = function( $container ) {
		$container
			.off( 'openNoVenueModal.tribeEvents', obj.openNoVenueModal )
			.off( 'closeNoVenueModal.tribeEvents', obj.closeNoVenueModal )
			.off( 'setNoVenueModalLink.tribeEvents', obj.setNoVenueModalLink )
			.find( obj.selectors.noVenueModalClose )
			.off( 'click', obj.closeNoVenueModal );
	};

	/**
	 * Bind events for map no venue modal.
	 *
	 * @since 4.7.9
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.bindEvents = function( $container ) {
		var $modal = $container.find( obj.selectors.noVenueModal );

		$container
			.on( 'openNoVenueModal.tribeEvents', { container: $container, modal: $modal }, obj.openNoVenueModal )
			.on( 'closeNoVenueModal.tribeEvents', { container: $container, modal: $modal }, obj.closeNoVenueModal )
			.on( 'setNoVenueModalLink.tribeEvents', { container: $container, modal: $modal }, obj.setNoVenueModalLink )
			.find( obj.selectors.noVenueModalClose )
			.on( 'click', { container: $container, modal: $modal }, obj.closeNoVenueModal );
	};

	/**
	 * Deinitialize map no venue modal.
	 *
	 * @since 4.7.9
	 *
	 * @param  {Event}       event    event object for 'afterSetup.tribeEvents' event
	 * @param  {jqXHR}       jqXHR    Request object
	 * @param  {PlainObject} settings Settings that this request was made with
	 *
	 * @return {void}
	 */
	obj.deinit = function( event, jqXHR, settings ) {
		var $container = event.data.container;
		obj.unbindEvents( $container );
		$container.off( 'beforeAjaxSuccess.tribeEvents', obj.deinit );
	};

	/**
	 * Initialize map no venue modal.
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
		if ( 'map' !== data.slug ) {
			return;
		}

		obj.bindEvents( $container );
		$container.on( 'beforeAjaxSuccess.tribeEvents', { container: $container }, obj.deinit );
	};

	/**
	 * Handles the initialization of the map no venue modal when Document is ready
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
} )( jQuery, tribe.events.views.mapNoVenueModal );
