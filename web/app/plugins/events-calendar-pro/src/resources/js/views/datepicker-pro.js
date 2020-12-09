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
 * Configures Map Events Scroller Object in the Global Tribe variable
 *
 * @since 4.7.9
 *
 * @type  {PlainObject}
 */
tribe.events.views.datepickerPro = {};

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
		datepickerDays: '.datepicker-days',
		datepickerDaysRow: '.datepicker-days tbody tr',
		datepickerDay: '.day',
		datepickerDayNotDisabled: '.day:not(.disabled)',
		activeClass: '.active',
		disabledClass: '.disabled',
		focusedClass: '.focused',
		hoveredClass: '.hovered',
		currentClass: '.current',
	};

	/**
	 * Toggle hover class
	 *
	 * @since 4.7.9
	 *
	 * @param {Event} event event object for 'mouseenter' and 'mouseleave' events
	 *
	 * @return {void}
	 */
	obj.toggleHoverClass = function( event ) {
		event.data.row.toggleClass( obj.selectors.hoveredClass.className() );
	};

	/**
	 * Handle disabled day click event
	 *
	 * @since 4.7.9
	 *
	 * @param {Event} event event object for 'click' event
	 *
	 * @return {void}
	 */
	obj.handleDisabledDayClick = function( event ) {
		event.data.row.find( obj.selectors.datepickerDayNotDisabled ).click();
	};

	/**
	 * Bind datepicker row events
	 *
	 * @since 4.7.9
	 *
	 * @param {Event} event event object for 'show' event
	 *
	 * @return {void}
	 */
	obj.bindRowEvents = function( event ) {
		var $datepickerDays = event.data.container.find( obj.selectors.datepickerDays );
		var config = { attributes: true, childList: true, subtree: true };

		var $container = event.data.container;
		var $rows = $container.find( obj.selectors.datepickerDaysRow );

		// for each row, add mouseenter and mouseleave event listeners to toggle hover class
		$rows.each( function( index, row ) {
			var $row = $( row );
			$row
				.off( 'mouseenter mouseleave', obj.toggleHoverClass )
				.on( 'mouseenter mouseleave', { row: $row }, obj.toggleHoverClass )
				.find( obj.selectors.datepickerDay )
				.each( function( index, day ) {
					var $day = $( day );

					// if day has disabled class, allow clicking day to select first day of the week
					if ( $day.hasClass( obj.selectors.disabledClass.className() ) ) {
						$day
							.off( 'click', obj.handleDisabledDayClick )
							.on( 'click', { row: $row }, obj.handleDisabledDayClick );
					}

					// if day has focused class, add focused class to row
					if ( $day.hasClass( obj.selectors.focusedClass.className() ) ) {
						$row.addClass( obj.selectors.focusedClass.className() );
					}

					// if day has active class, add active class to row
					if ( $day.hasClass( obj.selectors.activeClass.className() ) ) {
						$row.addClass( obj.selectors.activeClass.className() );
					}

					// if day has current class, add current class to row
					if ( $day.hasClass( obj.selectors.currentClass.className() ) ) {
						$row.addClass( obj.selectors.currentClass.className() );
					}
				} );
		} );

		event.data.observer.observe( $datepickerDays[ 0 ], config );
	};

	/**
	 * PRO tasks to run after deinitializing datepicker
	 *
	 * @since  4.7.9
	 *
	 * @param  {Event}       event    event object for 'afterDatepickerDeinit.tribeEvents' event
	 * @param  {jqXHR}       jqXHR    Request object
	 * @param  {PlainObject} settings Settings that this request was made with
	 *
	 * @return {void}
	 */
	obj.afterDeinit = function( event, jqXHR, settings ) {
		var $container = event.data.container;
		$container
			.off( 'afterDatepickerDeinit.tribeEvents', obj.afterDeinit )
			.off( 'handleMutationMonthChange.tribeEvents', obj.bindRowEvents )
			.find( obj.selectors.input )
			.off( 'show', obj.bindRowEvents );
	};

	/**
	 * PRO tasks to run before initializing datepicker
	 *
	 * @since  4.7.9
	 *
	 * @param  {Event}   event      event object for 'beforeDatepickerInit.tribeEvents' event
	 * @param  {integer} index      jQuery.each index param from 'beforeDatepickerInit.tribeEvents' event
	 * @param  {jQuery}  $container jQuery object of view container
	 * @param  {object}  data       data object passed from 'beforeDatepickerInit.tribeEvents' event
	 *
	 * @return {void}
	 */
	obj.beforeInit = function( event, index, $container, data ) {
		var daysOfWeekDisabled = [];

		if ( 'week' === data.slug ) {
			[ 0, 1, 2, 3, 4, 5, 6 ].forEach( function( value, index ) {
				if ( data.start_of_week == value ) {
					return;
				}

				daysOfWeekDisabled.push( value );
			} );
		}

		tribe.events.views.datepicker.options.daysOfWeekDisabled = daysOfWeekDisabled;
	};

	/**
	 * PRO tasks to run after initializing datepicker
	 *
	 * @since  4.7.9
	 *
	 * @param  {Event}   event      event object for 'afterDatepickerInit.tribeEvents' event
	 * @param  {integer} index      jQuery.each index param from 'afterDatepickerInit.tribeEvents' event
	 * @param  {jQuery}  $container jQuery object of view container
	 * @param  {object}  data       data object passed from 'afterDatepickerInit.tribeEvents' event
	 *
	 * @return {void}
	 */
	obj.afterInit = function( event, index, $container, data ) {
		if ( 'week' !== data.slug ) {
			return;
		}

		$container
			.on( 'afterDatepickerDeinit.tribeEvents', { container: $container, viewSlug: data.slug }, obj.afterDeinit )
			.on( 'handleMutationMonthChange.tribeEvents', { container: $container, observer: tribe.events.views.datepicker.observer }, obj.bindRowEvents )
			.find( tribe.events.views.datepicker.selectors.input )
			.on( 'show', { container: $container, observer: tribe.events.views.datepicker.observer }, obj.bindRowEvents );
	};

	/**
	 * Handles the initialization of the Datepicker when datepicker init events are triggered.
	 *
	 * @since 4.7.9
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		$document.on( 'beforeDatepickerInit.tribeEvents', tribe.events.views.manager.selectors.container, obj.beforeInit );
		$document.on( 'afterDatepickerInit.tribeEvents', tribe.events.views.manager.selectors.container, obj.afterInit );
	};

	// Configure on document ready
	$document.ready( obj.ready );
} )( jQuery, tribe.events.views.datepickerPro );
