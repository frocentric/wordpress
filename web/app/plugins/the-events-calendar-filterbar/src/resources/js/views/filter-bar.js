/* global tribe, tribe_dropdowns */
/* eslint-disable no-var, strict */

/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since 4.9.0
 *
 * @type   {PlainObject}
 */
tribe.events = tribe.events || {};
tribe.events.views = tribe.events.views || {};

/**
 * Configures Events Bar Object in the Global Tribe variable
 *
 * @since 4.9.0
 *
 * @type   {PlainObject}
 */
tribe.events.views.filterBar = {};

/**
 * Initializes in a Strict env the code that manages the Event Views
 *
 * @since 4.9.0
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} _   Underscore.js
 * @param  {PlainObject} obj tribe.events.views.filterBar
 *
 * @return {void}
 */
( function( $, _, obj ) {
	'use strict';
	var $document = $( document );
	var $body = null;

	/**
	 * Selectors used for configuration and setup
	 *
	 * @since 4.9.0
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		tribeFiltersOpen: '.tribe-filters-open',
		tribeFiltersClosed: '.tribe-filters-closed',
		tribeAjaxSuccess: '.tribe-ajax-success',
		filterBar: '#tribe_events_filters_wrapper',
		filterBarHorizontalClass: '.tribe-events-filters-horizontal',
		filtersForm: '#tribe_events_filters_form',
		filterItem: '.tribe_events_filter_item',
		filterGroupHeading: '.tribe-events-filters-group-heading',
		filterGroup: '.tribe-events-filter-group',
		filterSelect2: '.tribe-events-filter-select2',
		filterItemClosedClass: '.closed',
		filterItemActiveClass: '.active',
		filterItemCategory: '#tribe_events_filter_item_eventcategory',
		filterStatus: '.tribe-filter-status',
		uiSlider: '.ui-slider',
		filtersToggle: '.tribe-js-filters-toggle',
		filtersReset: '.tribe-js-filters-reset',
		filterLive: '.tribe-filter-live',
		visualHide: '.tribe-common-a11y-visual-hide',
		searchForm: '.tribe-events-c-events-bar__search-form',
		selectWoo: '.select2-container--open.tribe-dropdown',
	};

	/**
	 * State for filter bar
	 *
	 * @since 4.9.0
	 *
	 * @type {PlainObject}
	 */
	obj.state = {
		shouldSubmit: false,
		isLiveRefresh: false,
		mobileInitialized: false,
	};

	/**
	 * Determine whether filters are horizontal or not.
	 *
	 * @since 4.9.3
	 *
	 * @param {jQuery} $filterBar jQuery object of the filter bar.
	 *
	 * @return {bool}
	 */
	obj.areFiltersHorizontal = function( $filterBar ) {
		return $filterBar.hasClass( obj.selectors.filterBarHorizontalClass.className() );
	};

	/**
	 * Determine whether filters are open or not.
	 *
	 * @since 4.9.3
	 *
	 * @return {bool}
	 */
	obj.areFiltersOpen = function() {
		return $body.hasClass( obj.selectors.tribeFiltersOpen.className() );
	};

	/**
	 * Open filters
	 *
	 * @since 4.9.0
	 *
	 * @return {void}
	 */
	obj.openFilters = function() {
		$body
			.removeClass( obj.selectors.tribeFiltersClosed.className() )
			.removeClass( obj.selectors.tribeAjaxSuccess.className() )
			.addClass( obj.selectors.tribeFiltersOpen.className() );
	};

	/**
	 * Close filters
	 *
	 * @since 4.9.0
	 *
	 * @return {void}
	 */
	obj.closeFilters = function() {
		$body
			.addClass( obj.selectors.tribeFiltersClosed.className() )
			.removeClass( obj.selectors.tribeFiltersOpen.className() );
	};

	/**
	 * Toggle filters
	 *
	 * @since 4.9.0
	 *
	 * @return {void}
	 */
	obj.toggleFilters = function() {
		if ( $body.is( obj.selectors.tribeFiltersClosed ) ) {
			obj.openFilters();
		} else {
			obj.closeFilters();
		}
	};

	/**
	 * Clear filters
	 *
	 * @since 4.9.0
	 *
	 * @param {jQuery} $filterBar jQuery object of filter bar
	 *
	 * @return {void}
	 */
	obj.clearFilters = function( $filterBar ) {
		$filterBar
			.find( 'input, select' )
			.each( function( index, filter ) {
				var type = filter.type;
				var tag = filter.tagName.toLowerCase();

				if ( type === 'text' || type === 'password' || tag === 'textarea' ) {
					this.value = '';
				} else if ( type === 'checkbox' || type === 'radio' ) {
					this.checked = false;
				} else if ( tag === 'select' ) {
					this.selectedIndex = 0;
				}
			} );
	};

	/**
	 * Reset UI slider
	 *
	 * @since 4.9.0
	 *
	 * @param {jQuery} $filterBar jQuery object of filter bar
	 *
	 * @return {void}
	 */
	obj.resetUiSlider = function( $filterBar ) {
		$filterBar
			.find( obj.selectors.uiSlider )
			.each( function( index, slider ) {
				var $slider = $( slider );
				var $input = $slider.prev();
				var $display = $input.prev();
				var settings = $slider.slider( 'option' );

				$slider.slider( 'values', 0, settings.min );
				$slider.slider( 'values', 1, settings.max );
				$display.text( settings.min + ' - ' + settings.max );
				$input.val( '' );
			} );
	};

	/**
	 * Reset filters
	 *
	 * @since 4.9.0
	 *
	 * @param {Event} event event object for 'click' event
	 *
	 * @return {void}
	 */
	obj.resetFilters = function( event ) {
		$body.addClass( 'tribe-reset-on' );

		var $filterBar = event.data.filterBar;
		var $form = $filterBar.find( obj.selectors.filtersForm );

		obj.clearFilters( $filterBar );
		obj.resetUiSlider( $filterBar );
		obj.resetSelect2( $filterBar );
		obj.resetActiveFilters( $filterBar );

		if ( obj.areFiltersHorizontal( $filterBar ) ) {
			$filterBar
				.find( obj.selectors.filterItem )
				.addClass( obj.selectors.filterItemClosedClass.className() );
		}

		obj.state.shouldSubmit = false;
		$form.submit();

		$body.removeClass( 'tribe-reset-on' );
	};

	/**
	 * Submit filter bar form
	 *
	 * @since 4.9.0
	 *
	 * @param {jQuery} $form jQuery object of form
	 *
	 * @return {void}
	 */
	obj.submitForm = function( $form ) {
		var $container = $form.closest( tribe.events.views.manager.selectors.container );
		var nonce = $container.data( 'view-rest-nonce' );
		var formData = Qs.parse( $form.serialize() );

		// Flag to Backend that it can ignore the URL params.
		formData.form_submit = true;

		var data = {
			url: window.location.href,
			view_data: formData,
			_wpnonce: nonce,
		};

		tribe.events.views.manager.request( data, $container );
	};

	/**
	 * Debounced form submit
	 *
	 * @since 4.9.0
	 *
	 * @param {jQuery} $form jQuery object of form
	 *
	 * @return {void}
	 */
	obj.debouncedSubmitForm = _.debounce( function( $form ) {
		if ( ! obj.state.shouldSubmit ) {
			return;
		}

		$form.submit();
	}, 500 );

	/**
	 * Handle slide change event
	 *
	 * @since 4.9.0
	 *
	 * @param {Event} event event object for `slidechange` event
	 *
	 * @return {void}
	 */
	obj.handleSlideChange = function( event ) {
		if ( ! obj.state.isLiveRefresh ) {
			return;
		}

		var $form = event.data.target.closest( obj.selectors.filtersForm );
		obj.state.shouldSubmit = true;
		obj.debouncedSubmitForm( $form );
	};

	/**
	 * Handle filter change
	 *
	 * @since 4.9.0
	 *
	 * @param {Event} event event object for `change` event
	 *
	 * @return {void}
	 */
	obj.handleFilterChange = function( event ) {
		if ( ! obj.state.isLiveRefresh ) {
			return;
		}

		var $form = event.data.target.closest( obj.selectors.filtersForm );
		obj.state.shouldSubmit = true;
		obj.debouncedSubmitForm( $form );
	};

	/**
	 * Reset select2 filter
	 *
	 * @since 4.9.0
	 *
	 * @param {jQuery} $filterBar jQuery object of filter bar
	 *
	 * @return {void}
	 */
	obj.resetSelect2 = function( $filterBar ) {
		$filterBar
			.find( obj.selectors.filterSelect2 )
			.each( function( index, filter ) {
				$( filter )
					.find( 'input.tribe-dropdown' )
					.val( null )
					.trigger( 'change' );
			} );
	};

	/**
	 * Reset active filters
	 *
	 * @since 4.9.0
	 *
	 * @param {jQuery} $filterBar jQuery object of filter bar
	 *
	 * @return {void}
	 */
	obj.resetActiveFilters = function( $filterBar ) {
		$filterBar
			.find( obj.selectors.filterItem + obj.selectors.filterItemActiveClass )
			.each( function( index, filterItem ) {
				$( filterItem ).removeClass( obj.selectors.filterItemActiveClass.className() );
			} );
		$filterBar
			.find( obj.selectors.filterStatus )
			.each( function( index, status ) {
				$( status ).text( '' );
			} );
	};

	/**
	 * Opens filter group
	 *
	 * @since 4.9.0
	 *
	 * @param {Event} event event object for 'click' event
	 *
	 * @return {function}
	 */
	obj.openFilterGroup = function( event ) {
		var $filterItem = event.data.target;
		$filterItem
			.removeClass( obj.selectors.filterItemClosedClass.className() )
			.find( obj.selectors.filterGroupHeading )
			.attr( 'aria-expanded', 'false' );
	};

	/**
	 * Closes filter group
	 *
	 * @since 4.9.0
	 *
	 * @param {Event} event event object for 'click' event
	 *
	 * @return {void}
	 */
	obj.closeFilterGroup = function( event ) {
		var $filterItem = event.data.target;
		$filterItem
			.addClass( obj.selectors.filterItemClosedClass.className() )
			.find( obj.selectors.filterGroupHeading )
			.attr( 'aria-expanded', 'true' );
	};

	/**
	 * Closes all filter groups
	 *
	 * @since 4.9.0
	 *
	 * @param {Event} event event object for 'click' event
	 *
	 * @return {void}
	 */
	obj.closeAllFilterGroups = function( event ) {
		event.data.filterBar
			.find( obj.selectors.filterItem )
			.each( function( index, filterItem ) {
				var eventObj = { data: { target: $( filterItem ) } };
				obj.closeFilterGroup( eventObj );
			} );
	};

	/**
	 * Toggles filter group
	 *
	 * @since 4.9.0
	 *
	 * @param {Event} event event object for 'click' event
	 *
	 * @return {void}
	 */
	obj.toggleFilterGroup = function( event ) {
		event.stopPropagation();
		var $filterBar = event.data.filterBar;
		var $filterItem = event.data.target.closest( obj.selectors.filterItem );

		if ( obj.areFiltersHorizontal( $filterBar ) ) {
			$filterBar
				.find( obj.selectors.filterItem )
				.not( $filterItem )
				.each( function( index, filterItem ) {
					var eventObj = { data: { target: $( filterItem ) } };
					obj.closeFilterGroup( eventObj );
				} );
		}

		var eventObj = { data: { target: $filterItem } };
		if ( $filterItem.hasClass( obj.selectors.filterItemClosedClass.className() ) ) {
			obj.openFilterGroup( eventObj );
		} else {
			obj.closeFilterGroup( eventObj );
		}
	};

	/**
	 * Handles `click` event on document
	 *
	 * @since 4.9.0
	 *
	 * @param {Event} event event object for `click` event
	 *
	 * @return {void}
	 */
	obj.handleDocumentClick = function( event ) {
		var $target = $( event.target );

		if (
			$target.is( obj.selectors.filterGroup ) ||
				$target.closest( obj.selectors.filterGroup ).length ||
				$( obj.selectors.selectWoo ).length
		) {
			return;
		}

		var eventObj = { data: { filterBar: event.data.filterBar } };
		obj.closeAllFilterGroups( eventObj );
	};

	/**
	 * Handles 'resize.tribeEvents' event
	 *
	 * @since 4.9.0
	 *
	 * @param {Event} event event object for 'resize.tribeEvents' event
	 *
	 * @return {void}
	 */
	obj.handleResize = function( event ) {
		var $container = event.data.container;
		var containerState = $container.data( 'tribeEventsState' );
		var isMobile = containerState.isMobile;

		if ( ! isMobile && obj.state.mobileInitialized ) {
			obj.state.mobileInitialized = false;
			var $filterBar = $container.find( obj.selectors.filterBar );

			if ( ! obj.areFiltersOpen() && ! obj.areFiltersHorizontal( $filterBar ) ) {
				obj.openFilters();
			}
		} else if ( isMobile && ! obj.state.mobileInitialized ) {
			obj.state.mobileInitialized = true;
			obj.closeFilters();
		}
	};

	/**
	 * Handles `beforeOnSubmit.tribeEvents` event
	 *
	 * @since 4.9.0
	 *
	 * @param {Event} event         event object for `beforeOnSubmit.tribeEvents` event
	 * @param {Event} onSubmitEvent event object for `submit.tribeEvents` event
	 *
	 * @return {void}
	 */
	obj.handleBeforeOnSubmit = function( event, onSubmitEvent ) {
		var $container = $( event.target );
		var $form = $( onSubmitEvent.target );

		// only run if form is search form
		if ( ! $form.is( obj.selectors.searchForm ) ) {
			return;
		}

		$container
			.find( obj.selectors.filterItem )
			.each( function( filterItemIndex, filter ) {
				var $filter = $( filter );

				// adjust field name to have same form as search form
				$filter
					.find( '[name]' )
					.each( function( index, field ) {
						var $field = $( field );
						var name = $field.attr( 'name' );
						var adjustedName;

						if ( name.slice( -2 ) === '[]' ) {
							adjustedName = 'tribe-events-views[' + name.slice( 0, name.length - 2 ) + '][]';
						} else {
							adjustedName = 'tribe-events-views[' + name + ']';
						}

						$field.attr( 'name', adjustedName );
					} );

				// move filter to search form
				$filter
					.addClass( obj.selectors.visualHide.className() )
					.appendTo( $form );
			} );
	};

	/**
	 * Handles `afterOnSubmit.tribeEvents` event
	 *
	 * @since 4.9.0
	 *
	 * @param {Event} event         event object for `afterOnSubmit.tribeEvents` event
	 * @param {Event} onSubmitEvent event object for `submit.tribeEvents` event
	 *
	 * @return {void}
	 */
	obj.handleAfterOnSubmit = function( event, onSubmitEvent ) {
		var $container = $( event.target );
		var $form = $( onSubmitEvent.target );

		// only run if form is search form
		if ( ! $form.is( obj.selectors.searchForm ) ) {
			return;
		}

		var $filtersForm = $container.find( obj.selectors.filtersForm );

		$.fn.reverse = Array.prototype.reverse;

		$form
			.find( obj.selectors.filterItem )
			.reverse()
			.each( function( filterItemIndex, filter ) {
				var $filter = $( filter );

				// adjust field name back to original
				$filter
					.find( '[name]' )
					.each( function( index, field ) {
						var $field = $( field );
						var name = $field.attr( 'name' );
						var adjustedName;

						if ( name.slice( -2 ) === '[]' ) {
							adjustedName = name.slice( 19, name.length - 3 ) + '[]';
						} else {
							adjustedName = name.slice( 19, name.length - 1 );
						}

						$field.attr( 'name', adjustedName );
					} );

				// move filter back to filters form
				$filter
					.removeClass( obj.selectors.visualHide.className() )
					.prependTo( $filtersForm );
			} );

		delete $.fn.reverse;
	};

	/**
	 * Handles form `submit` event
	 *
	 * @since 4.9.0
	 *
	 * @param {Event} event event object for `submit` event
	 *
	 * @return {void}
	 */
	obj.handleFormSubmit = function( event ) {
		event.preventDefault();
		obj.submitForm( event.data.form );
	};

	/**
	 * Unbind events for filter bar
	 *
	 * @since 4.9.0
	 *
	 * @param  {jQuery}  $container jQuery object of view container
	 *
	 * @return {void}
	 */
	obj.unbindEvents = function( $container ) {
		var $filterBar = $container.find( obj.selectors.filterBar );
		var $form = $filterBar.find( obj.selectors.filtersForm );
		var $filterGroupHeading = $filterBar.find( obj.selectors.filterGroupHeading );
		var $filtersToggle = $filterBar.find( obj.selectors.filtersToggle );
		var $filtersReset = $filterBar.find( obj.selectors.filtersReset );
		var $uiSlider = $filterBar.find( obj.selectors.uiSlider );
		var $inputSelect = $filterBar.find( 'input, select' );

		$document.off( 'click', obj.handleDocumentClick );
		$container
			.off( 'resize.tribeEvents', obj.handleResize )
			.off( 'beforeOnSubmit.tribeEvents', obj.handleBeforeOnSubmit )
			.off( 'afterOnSubmit.tribeEvents', obj.handleAfterOnSubmit );
		$filterGroupHeading.each( function( index, groupHeading ) {
			$( groupHeading ).off( 'click', obj.toggleFilterGroup );
		} );
		$filtersToggle.off( 'click', obj.toggleFilters );
		$filtersReset.off( 'click', obj.resetFilters );
		$uiSlider.each( function( index, slider ) {
			$( slider ).off( 'slidechange', obj.handleSlideChange );
		} );
		$inputSelect.each( function( index, filter ) {
			$( filter ).off( 'change', obj.handleFilterChange );
		} );
		$form.off( 'submit', obj.handleFormSubmit );
	};

	/**
	 * Bind events for filter bar
	 *
	 * @since 4.9.0
	 *
	 * @param  {jQuery}  $container jQuery object of view container
	 *
	 * @return {void}
	 */
	obj.bindEvents = function( $container ) {
		var $filterBar = $container.find( obj.selectors.filterBar );
		var $form = $filterBar.find( obj.selectors.filtersForm );
		var $filterGroupHeading = $filterBar.find( obj.selectors.filterGroupHeading );
		var $filtersToggle = $filterBar.find( obj.selectors.filtersToggle );
		var $filtersReset = $filterBar.find( obj.selectors.filtersReset );
		var $uiSlider = $filterBar.find( obj.selectors.uiSlider );
		var $inputSelect = $filterBar.find( 'input, select' );

		$document.on( 'click', { filterBar: $filterBar }, obj.handleDocumentClick );
		$container
			.on( 'resize.tribeEvents', { container: $container }, obj.handleResize )
			.on( 'beforeOnSubmit.tribeEvents', obj.handleBeforeOnSubmit )
			.on( 'afterOnSubmit.tribeEvents', obj.handleAfterOnSubmit );
		$filterGroupHeading.each( function( index, groupHeading ) {
			var $groupHeading = $( groupHeading );
			$groupHeading.on(
				'click',
				{ target: $groupHeading, filterBar: $filterBar },
				obj.toggleFilterGroup,
			);
		} );
		$filtersToggle.on( 'click', obj.toggleFilters );
		$filtersReset.on( 'click', { filterBar: $filterBar }, obj.resetFilters );
		$uiSlider.each( function( index, slider ) {
			var $slider = $( slider );
			$slider.on( 'slidechange', { target: $slider }, obj.handleSlideChange );
		} );
		$inputSelect.each( function( index, filter ) {
			var $filter = $( filter );
			$filter.on( 'change', { target: $filter }, obj.handleFilterChange );
		} );
		$form.on( 'submit', { form: $form }, obj.handleFormSubmit );
	};

	/**
	 * Initialize filters
	 *
	 * @since 4.9.0
	 *
	 * @param  {jQuery}  $container jQuery object of view container
	 *
	 * @return {void}
	 */
	obj.initFilters = function( $container ) {
		var $filterBar = $container.find( obj.selectors.filterBar );
		var $dropdowns = $filterBar
			.find( tribe_dropdowns.selector.dropdown )
			.not( tribe_dropdowns.selector.created );

		var eventObj = { data: { filterBar: $filterBar } };
		obj.closeAllFilterGroups( eventObj );

		obj.state.isLiveRefresh = $body.is( obj.selectors.filterLive );

		// Initialize dropdowns
		$dropdowns.tribe_dropdowns();

		var containerState = $container.data( 'tribeEventsState' );
		var isMobile = containerState.isMobile;

		if ( ! isMobile && ! obj.areFiltersHorizontal( $filterBar ) ) {
			obj.openFilters();
		}

		obj.state.mobileInitialized = isMobile;
	};

	/**
	 * Deinitialize filter bar JS
	 *
	 * @since 4.9.0
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

		var $dropdowns = $container.find( tribe_dropdowns.selector.dropdown );

		$dropdowns.each( function( index, dropdown ) {
			var $dropdown = $( dropdown );
			if ( ! $dropdown.is( 'input, select' ) ) {
				return;
			}

			$dropdown.data( 'select2' ).destroy();
		} );

		$container.off( 'beforeAjaxSuccess.tribeEvents', obj.deinit );
	};

	/**
	 * Initialize filter bar JS
	 *
	 * @since  4.9.0
	 *
	 * @param  {Event}   event      event object for 'afterSetup.tribeEvents' event
	 * @param  {integer} index      jQuery.each index param from 'afterSetup.tribeEvents' event
	 * @param  {jQuery}  $container jQuery object of view container
	 * @param  {object}  data       data object passed from 'afterSetup.tribeEvents' event
	 *
	 * @return {void}
	 */
	obj.init = function( event, index, $container, data ) {
		var $filterBar = $container.find( obj.selectors.filterBar );

		if ( ! $filterBar.length ) {
			return;
		}

		obj.bindEvents( $container );
		obj.initFilters( $container );

		$container.on( 'beforeAjaxSuccess.tribeEvents', { container: $container }, obj.deinit );
	};

	/**
	 * Handles the initialization of filter bar when Document is ready
	 *
	 * @since 4.9.0
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		$body = $( 'body' );
		$document.on(
			'afterSetup.tribeEvents',
			tribe.events.views.manager.selectors.container,
			obj.init,
		);
	};

	// Configure on document ready
	$( obj.ready );
} )( jQuery, _, tribe.events.views.filterBar );
