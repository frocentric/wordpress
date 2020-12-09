/**
 * Makes sure we have all the required levels on the Tribe Object.
 *
 * @since 4.7.9
 *
 * @type {PlainObject}
 */
tribe.events = tribe.events || {};
tribe.events.views = tribe.events.views || {};

/**
 * Configures Views Toggle Recurrence Object in the Global Tribe variable.
 *
 * @since 4.7.9
 *
 * @type {PlainObject}
 */
tribe.events.views.toggleRecurrence = {};

/**
 * Initializes in a Strict env for managing the Toggle Recurrence Input.
 *
 * @since  4.7.9
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} obj tribe.events.views.toggleRecurrence
 *
 * @return {void}
 */
( function( $, obj ) {
	'use strict';
	var $document = $( document );

	/**
	 * Selectors used for configuration and setup.
	 *
	 * @since 4.7.9
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		toggleInput: '[data-js="tribe-events-pro-top-bar-toggle-recurrence"]',
	};

	/**
	 * Handles after toggle recurrence change of input.
	 *
	 * @since 4.7.9
	 *
	 * @param {Event}  event      event object for 'change.tribeEvents' event
	 *
	 * @return {void}
	 */
	obj.handleChangeInput = function( event ) {
		var is_checked = $( event.target ).is( ':checked' );
		var $container = tribe.events.views.manager.getContainer( event.target );

		var data = {
			view_data: {
				hide_subsequent_recurrences: is_checked ? true : null,
			},
			_wpnonce: $container.data( 'view-rest-nonce' ),
		};

		tribe.events.views.manager.request( data, $container );
	};

	/**
	 * Deinitialize toggle recurrence JS.
	 *
	 * @since 5.0.0
	 *
	 * @param  {Event}       event    event object for 'afterSetup.tribeEvents' event
	 * @param  {jqXHR}       jqXHR    Request object
	 * @param  {PlainObject} settings Settings that this request was made with
	 *
	 * @return {void}
	 */
	obj.deinit = function( event, jqXHR, settings ) {
		var $container = event.data.container;
		$container
			.off( 'beforeAjaxSuccess.tribeEvents', obj.deinit )
			.find( obj.selectors.toggleInput )
			.off( 'change.tribeEvents', obj.handleChangeInput );
	};

	/**
	 * Handles after toggle recurrence init theme event.
	 *
	 * @since  4.7.9
	 *
	 * @param  {Event}   event      event object for 'afterSetup.tribeEvents' event
	 * @param  {integer} index      jQuery.each index param from 'afterSetup.tribeEvents' event
	 * @param  {jQuery}  $container jQuery object of view container
	 * @param  {object}  data       data object passed from 'afterSetup.tribeEvents' event
	 *
	 * @return {void}
	 */
	obj.init = function( event, index, $container, data ) {
		$container
			.on( 'beforeAjaxSuccess.tribeEvents', { container: $container }, obj.deinit )
			.find( obj.selectors.toggleInput )
			.on( 'change.tribeEvents', obj.handleChangeInput );
	};

	/**
	 * Handles the initialization of the scripts when Document is ready.
	 *
	 * @since  4.7.9
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		$document.on( 'afterSetup.tribeEvents', tribe.events.views.manager.selectors.container, obj.init );
	};

	// Configure on document ready
	$document.ready( obj.ready );
} )( jQuery, tribe.events.views.toggleRecurrence );
