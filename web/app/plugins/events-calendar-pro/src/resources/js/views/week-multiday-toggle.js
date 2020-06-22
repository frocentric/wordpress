/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since 4.7.7
 *
 * @type   {PlainObject}
 */
tribe.events = tribe.events || {};
tribe.events.views = tribe.events.views || {};

/**
 * Configures Week Multiday Toggle Object in the Global Tribe variable
 *
 * @since 4.7.7
 *
 * @type  {PlainObject}
 */
tribe.events.views.weekMultidayToggle = {};

/**
 * Initializes in a Strict env the code that manages the Event Views
 *
 * @since 4.7.7
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
	 * @since 4.7.7
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		weekMultidayRow: '[data-js="tribe-events-pro-week-multiday-events-row"]',
		weekMultidayToggleButton: '[data-js="tribe-events-pro-week-multiday-toggle-button"]',
		weekMultidayToggleButtonOpenClass: '.tribe-events-pro-week-grid__multiday-toggle-button--open',
		weekMultidayMoreButtonWrapper: '[data-js="tribe-events-pro-week-multiday-more-events-wrapper"]',
		weekMultidayMoreButton: '[data-js="tribe-events-pro-week-multiday-more-events"]',
		tribeCommonA11yHiddenClass: '.tribe-common-a11y-hidden',
	};

	/**
	 * Toggles the week multiday overflow events
	 *
	 * @since 4.7.7
	 *
	 * @param {Event} event event object of click event
	 *
	 * @return {void}
	 */
	obj.toggleMultidayEvents = function( event ) {
		var $toggleButton = event.data.toggleButton;
		var togglesAndContainers = event.data.togglesAndContainers;

		if ( 'true' === $toggleButton.attr( 'aria-expanded' ) ) {
			/**
			 * Define empty jQuery object for content as toggle button
			 * is not associated with a specific content
			 */
			tribe.events.views.accordion.closeAccordion( $toggleButton, $( '' ) );
			$toggleButton.removeClass( obj.selectors.weekMultidayToggleButtonOpenClass.className() );
		} else {
			/**
			 * Define empty jQuery object for content as toggle button
			 * is not associated with a specific content
			 */
			tribe.events.views.accordion.openAccordion( $toggleButton, $( '' ) );
			$toggleButton.addClass( obj.selectors.weekMultidayToggleButtonOpenClass.className() );
		}

		togglesAndContainers.forEach( function( item ) {
			var $headerWrapper = item.headerWrapper;
			var $header = item.header;
			var $content = item.content;

			if ( 'true' === $header.attr( 'aria-expanded' ) ) {
				tribe.events.views.accordion.closeAccordion( $header, $content );
				$headerWrapper.removeClass( obj.selectors.tribeCommonA11yHiddenClass.className() );
			} else {
				tribe.events.views.accordion.openAccordion( $header, $content );
				$headerWrapper.addClass( obj.selectors.tribeCommonA11yHiddenClass.className() );
			}
		} );
	};

	/**
	 * Create an assosiative array of objects containing
	 * header wrapper, header, and content for the accordions.
	 *
	 * @since 4.7.7
	 *
	 * @param {jQuery} $multidayRow jQuery object of the row.
	 * @param {array} containerIds Array containing a list of the toggle buttoons.
	 *
	 * @return {array}
	 */
	obj.getTogglesAndContainers = function( $multidayRow, containerIds ) {
		var togglesAndContainers = [];

		containerIds.forEach( function( toggleContent ) {
			var $toggleContent = $multidayRow.find( '#' + toggleContent );
			var $moreButtonWrapper = $toggleContent.siblings( obj.selectors.weekMultidayMoreButtonWrapper );
			var $moreButton = $moreButtonWrapper.find( obj.selectors.weekMultidayMoreButton )

			togglesAndContainers.push( {
				headerWrapper: $moreButtonWrapper,
				header: $moreButton,
				content: $toggleContent,
			} );
		} );

		return togglesAndContainers;
	}

	/**
	 * Initialize toggle
	 *
	 * @since 4.7.7
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.initToggle = function( $container ) {
		var $multidayRow = $container.find( obj.selectors.weekMultidayRow );
		var $toggleButton = $multidayRow.find( obj.selectors.weekMultidayToggleButton );
		var containerIds = $toggleButton.attr( 'aria-controls' ).split( ' ' );
		var togglesAndContainers = obj.getTogglesAndContainers( $multidayRow, containerIds );

		$toggleButton.on( 'click', {
			toggleButton: $toggleButton,
			togglesAndContainers: togglesAndContainers,
		}, obj.toggleMultidayEvents );

		togglesAndContainers.forEach( function( item ) {
			var $moreButton = item.header;
			$moreButton.on( 'click', {
				toggleButton: $toggleButton,
				togglesAndContainers: togglesAndContainers,
			}, obj.toggleMultidayEvents );
		} );
	};

	/**
	 * Deinitialize toggle
	 *
	 * @since 4.7.7
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.deinitToggle = function( $container ) {
		var $multidayRow = $container.find( obj.selectors.weekMultidayRow );

		$multidayRow
			.find( obj.selectors.weekMultidayToggleButton )
			.off( 'click', obj.toggleMultidayEvents );

		$multidayRow
			.find( obj.selectors.weekMultidayMoreButton )
			.each( function( index, moreButton ) {
				$( moreButton ).off( 'click', obj.toggleMultidayEvents );
		} );
	};

	/**
	 * Deinitialize week multiday toggle JS.
	 *
	 * @since  4.7.7
	 *
	 * @param  {Event}       event    event object for 'afterSetup.tribeEvents' event
	 * @param  {jqXHR}       jqXHR    Request object
	 * @param  {PlainObject} settings Settings that this request was made with
	 *
	 * @return {void}
	 */
	obj.deinit = function( event, jqXHR, settings ) {
		var $container = event.data.container;
		obj.deinitToggle( $container );
		$container.off( 'beforeAjaxSuccess.tribeEvents', obj.deinit );
	};


	/**
	 * Initialize week multiday toggle JS.
	 *
	 * @since 4.7.7
	 *
	 * @param {Event}   event      JS event triggered.
	 * @param {integer} index      jQuery.each index param from 'afterSetup.tribeEvents' event.
	 * @param {jQuery}  $container jQuery object of view container.
	 * @param {object}  data       data object passed from 'afterSetup.tribeEvents' event.
	 *
	 * @return {void}
	 */
	obj.init = function( event, index, $container, data ) {
		var $toggleButton = $container.find( obj.selectors.weekMultidayToggleButton );

		if ( ! $toggleButton.length ) {
			return;
		}

		obj.initToggle( $container );
		$container.on( 'beforeAjaxSuccess.tribeEvents', { container: $container }, obj.deinit );
	};

	/**
	 * Handles the initialization of the week grid scroller when Document is ready
	 *
	 * @since 4.7.7
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		$document.on( 'afterSetup.tribeEvents', tribe.events.views.manager.selectors.container, obj.init );
	};

	// Configure on document ready
	$document.ready( obj.ready );
} )( jQuery, tribe.events.views.weekMultidayToggle );
