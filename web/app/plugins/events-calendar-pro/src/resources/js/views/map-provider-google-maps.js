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
 * Configures Map Provider Google Maps Object in the Global Tribe variable
 *
 * @since 4.7.7
 *
 * @type  {PlainObject}
 */
tribe.events.views.mapProviderGoogleMaps = {};

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
		map: '[data-js="tribe-events-pro-map-map"]',
		googleMapsDefault: '[data-js="tribe-events-pro-map-google-maps-default"]',
		googleMapsPremium: '[data-js="tribe-events-pro-map-google-maps-premium"]',
		eventCardWrapper: '[data-js="tribe-events-pro-map-event-card-wrapper"]',
		eventTooltipTemplate: '[data-js="tribe-events-pro-map-event-tooltip-template"]',
		eventTooltipSlider: '[data-js="tribe-events-pro-map-event-tooltip-slider"]',
		eventTooltipSlide: '[data-js="tribe-events-pro-map-event-tooltip-slide"]',
		eventTooltipPrevButton: '[data-js="tribe-events-pro-map-event-tooltip-prev-button"]',
		eventTooltipNextButton: '[data-js="tribe-events-pro-map-event-tooltip-next-button"]',
		eventTooltipButtonDisabledClass: '.tribe-events-pro-map__event-tooltip-navigation-button--disabled',
		eventActionLinkDetails: '[data-js="tribe-events-pro-map-event-actions-link-details"]',
		tribeCommonA11yHiddenClass: '.tribe-common-a11y-hidden',
	};

	/**
	 * Global Google Maps state
	 *
	 * @since 4.7.7
	 *
	 * @type {PlainObject}
	 */
	obj.state = {
		mapsScriptLoaded: 'undefined' !== typeof window.google && 'undefined' !== typeof window.google.maps,
		zoom: 10,
	};

	/**
	 * Curry function to handle tooltip slide change.
	 * Used to pass in `$container` and `state`.
	 *
	 * @since 4.7.8
	 *
	 * @param {jQuery}      $container jQuery object of view container.
	 * @param {PlainObject} state      state of Google Maps premium.
	 *
	 * @return {function}
	 */
	obj.handleTooltipSlideChange = function( $container, state ) {
		/**
		 * Handle tooltip slider slide change.
		 *
		 * @since 4.7.8
		 *
		 * @return {void}
		 */
		return function() {
			var eventId = $( state.slider.slides[ state.slider.activeIndex ] ).attr( 'data-event-id' );
			var mapEventsSelectors = tribe.events.views.mapEvents.selectors;
			var activeEventCardWrapperSelector = '[data-event-id="' + eventId + '"]';

			var $buttons = $container.find( mapEventsSelectors.eventCardButton );
			var $eventCardWrapper = $container.find( mapEventsSelectors.eventCardWrapper + activeEventCardWrapperSelector );
			var $button = $eventCardWrapper.find( mapEventsSelectors.eventCardButton );

			tribe.events.views.mapEvents.deselectAllEvents( $buttons );
			tribe.events.views.mapEvents.selectEvent( $button );
			if ( ! tribe.events.views.mapEventsScroller.isWithinScrollView( $container, $eventCardWrapper ) ) {
				tribe.events.views.mapEventsScroller.scrollTo( $container, $eventCardWrapper );
			}
		};
	};

	/**
	 * Get event object from premium map state
	 *
	 * @since 4.7.7
	 *
	 * @param {PlainObject} state   state of Google Maps premium.
	 * @param {string}      eventId id of the event.
	 *
	 * @return {PlainObject|boolean}
	 */
	obj.getEventFromState = function( state, eventId ) {
		var eventObjects = state.events.filter( function( event, index ) {
			return event.eventId == eventId;
		} );

		if ( eventObjects.length ) {
			return eventObjects[0];
		}

		return false;
	};

	/**
	 * Deinitialize tooltip slider
	 *
	 * @since 4.7.8
	 *
	 * @param {Swiper} slider object of swiper.
	 *
	 * @return {void}
	 */
	obj.deinitTooltipSlider = function( slider ) {
		if ( slider && ! slider.destroyed ) {
			slider.off( 'slideChange' );
			slider.destroy();
		}
	};

	/**
	 * Initialize tooltip slider
	 *
	 * @since 4.7.8
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.initTooltipSlider = function( $container ) {
		var $googleMapsPremium = $container.find( obj.selectors.googleMapsPremium );
		var $tooltipSlider = $googleMapsPremium.find( obj.selectors.eventTooltipSlider );

		if ( $tooltipSlider.length ) {
			var state = $googleMapsPremium.data( 'tribeEventsState' );
			var activeEventTooltipSlideSelector = '[data-event-id="' + state.activeEventId + '"]';
			var $initialSlide = $tooltipSlider.find( obj.selectors.eventTooltipSlide + activeEventTooltipSlideSelector );

			state.slider = new Swiper( $tooltipSlider[0], {
				initialSlide: $initialSlide.attr( 'data-slide-index' ),
				speed: 0,
				resistanceRatio: 0,
				allowTouchMove: false,
				navigation: {
					prevEl: $tooltipSlider.find( obj.selectors.eventTooltipPrevButton )[0],
					nextEl: $tooltipSlider.find( obj.selectors.eventTooltipNextButton )[0],
					disabledClass: obj.selectors.eventTooltipButtonDisabledClass.className(),
				},
			} );
			state.slider.on( 'slideChange', obj.handleTooltipSlideChange( $container, state ) );
		}
	};

	/**
	 * Clean up slider and close tooltip
	 *
	 * @since 4.7.8
	 *
	 * @param {PlainObject} state state of Google Maps premium.
	 *
	 * @return {void}
	 */
	obj.closeTooltip = function( state ) {
		obj.deinitTooltipSlider( state.slider );
		state.tooltip.close();
	};

	/**
	 * Set tooltip content and open
	 *
	 * @since 4.7.8
	 *
	 * @param {InfoWindow} tooltip  object of info window.
	 * @param {string}     template template of tooltip content.
	 * @param {Map}        map      object of map.
	 * @param {Marker}     marker   object of marker.
	 *
	 * @return {void}
	 */
	obj.openTooltip = function( tooltip, template, map, marker ) {
		tooltip.setContent( template );
		tooltip.open( map, marker );
	};

	/**
	 * Handle event click.
	 *
	 * @since 4.7.7
	 *
	 * @param {Event}  event      JS event triggered.
	 * @param {jQuery} $container jQuery object of view container.
	 * @param {jQuery} $button    jQuery object of event card button.
	 *
	 * @return {void}
	 */
	obj.handleEventClick = function( event, $container, $button ) {
		var isPremium = $container.find( obj.selectors.map ).data( 'tribeEventsState' ).isPremium;

		if ( ! isPremium ) {
			// set google maps default iframe src
			var $googleMapsDefault = $container.find( obj.selectors.googleMapsDefault );
			var $eventCardWrapper = $button.closest( obj.selectors.eventCardWrapper );
			var currentSrc = $googleMapsDefault.attr( 'src' );
			var src = $eventCardWrapper.attr( 'data-src' );
			$container.trigger( 'closeNoVenueModal.tribeEvents' );

			// If data-src exists for iframe (event has venue)
			if ( src && currentSrc !== src ) {
				$googleMapsDefault.attr( 'src', src );
			} else if ( ! src ) {
				// If data-src does not exist for iframe (event does not have venue)
				var detailsLink = $eventCardWrapper.find( obj.selectors.eventActionLinkDetails ).attr( 'href' );

				$container.trigger( 'openNoVenueModal.tribeEvents' );
				$container.trigger( 'setNoVenueModalLink.tribeEvents', [ detailsLink ] );
			}
		} else {
			var $googleMapsPremium = $container.find( obj.selectors.googleMapsPremium );
			var state = $googleMapsPremium.data( 'tribeEventsState' );
			var $eventCardWrapper = $button.closest( obj.selectors.eventCardWrapper );
			var eventId = $eventCardWrapper.attr( 'data-event-id' );
			var eventObject = obj.getEventFromState( state, eventId );

			// Close tooltip and no venue modal
			obj.closeTooltip( state );
			$container.trigger( 'closeNoVenueModal.tribeEvents' );

			// If event object exists (event has venue)
			if ( eventObject ) {
				// Open selected event tooltip
				var $tooltipTemplate = $eventCardWrapper.find( obj.selectors.eventTooltipTemplate );
				obj.openTooltip( state.tooltip, $tooltipTemplate[0].textContent, state.map, eventObject.marker );

				// set active event id
				state.activeEventId = eventId;
				$googleMapsPremium.data( 'tribeEventsState', state );

				// move map center
				state.map.panTo( eventObject.marker.getPosition() );
			} else {
				// If event object does not exist (event does not have venue)
				var detailsLink = $eventCardWrapper.find( obj.selectors.eventActionLinkDetails ).attr( 'href' );

				$container.trigger( 'openNoVenueModal.tribeEvents' );
				$container.trigger( 'setNoVenueModalLink.tribeEvents', [ detailsLink ] );
			}
		}
	};

	/**
	 * Curry function to handle marker click.
	 * Used to pass in `$container` and `marker`.
	 *
	 * @since 4.7.7
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 * @param {Marker} marker     instance of marker object.
	 *
	 * @return {function}
	 */
	obj.handleMarkerClick = function( $container, marker ) {
		/**
		 * Handle marker click.
		 *
		 * @since 4.7.7
		 *
		 * @param {Event} event event object.
		 *
		 * @return {void}
		 */
		return function( event ) {
			var $googleMapsPremium = $container.find( obj.selectors.googleMapsPremium );
			var state = $googleMapsPremium.data( 'tribeEventsState' );
			var eventIds = marker.get( 'eventIds' );
			var position = marker.getPosition();

			var mapEventsSelectors = tribe.events.views.mapEvents.selectors;
			var activeEventCardWrapperSelector = '[data-event-id="' + eventIds[0] + '"]';

			var $buttons = $container.find( mapEventsSelectors.eventCardButton );
			var $eventCardWrapper = $container.find( mapEventsSelectors.eventCardWrapper + activeEventCardWrapperSelector );
			var $button = $eventCardWrapper.find( mapEventsSelectors.eventCardButton );

			// deselect all events and select active event
			tribe.events.views.mapEvents.deselectAllEvents( $buttons );
			tribe.events.views.mapEvents.selectEvent( $button );
			if ( ! tribe.events.views.mapEventsScroller.isWithinScrollView( $container, $eventCardWrapper ) ) {
				tribe.events.views.mapEventsScroller.scrollTo( $container, $eventCardWrapper );
			}

			// close previous tooltip and open selected event tooltip
			var $tooltipTemplate = $eventCardWrapper.find( obj.selectors.eventTooltipTemplate );
			obj.closeTooltip( state );
			obj.openTooltip( state.tooltip, $tooltipTemplate[0].textContent, state.map, marker );

			// set active event id
			state.activeEventId = eventIds[0];
			$googleMapsPremium.data( 'tribeEventsState', state );

			// move map center
			state.map.panTo( position );
		};
	};

	/**
	 * Curry function to handle map click.
	 * Used to pass in `$container` and `map`.
	 *
	 * @since 4.7.8
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 * @param {Marker} map        instance of map object.
	 *
	 * @return {function}
	 */
	obj.handleMapClick = function( $container, map ) {
		/**
		 * Handle map click.
		 *
		 * @since 4.7.8
		 *
		 * @param {Event} event event object.
		 *
		 * @return {void}
		 */
		return function( event ) {
			var $googleMapsPremium = $container.find( obj.selectors.googleMapsPremium );
			var state = $googleMapsPremium.data( 'tribeEventsState' );

			// close tooltip
			obj.closeTooltip( state );

			// set active event id to null
			state.activeEventId = null;
			$googleMapsPremium.data( 'tribeEventsState', state );

			// deselect all event buttons
			var $buttons = $container.find( tribe.events.views.mapEvents.selectors.eventCardButton );
			tribe.events.views.mapEvents.deselectAllEvents( $buttons );
		};
	};

	/**
	 * Curry function to handle tooltip close click event.
	 * Used to pass in `$container`.
	 *
	 * @since 4.7.8
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 *
	 * @return {function}
	 */
	obj.handleTooltipCloseClick = function( $container ) {
		/**
		 * Handle tooltip close click event.
		 *
		 * @since 4.7.8
		 *
		 * @return {void}
		 */
		return function() {
			var $googleMapsPremium = $container.find( obj.selectors.googleMapsPremium );
			var state = $googleMapsPremium.data( 'tribeEventsState' );

			// set active event id to null
			state.activeEventId = null;
			$googleMapsPremium.data( 'tribeEventsState', state );

			// deinit tooltip slider
			obj.deinitTooltipSlider( state.slider );

			// deselect all event buttons
			var $buttons = $container.find( tribe.events.views.mapEvents.selectors.eventCardButton );
			tribe.events.views.mapEvents.deselectAllEvents( $buttons );
		};
	};

	/**
	 * Curry function to handle tooltip dom ready event.
	 * Used to pass in `$container`.
	 *
	 * @since 4.7.8
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 *
	 * @return {function}
	 */
	obj.handleTooltipDomReady = function( $container ) {
		/**
		 * Handle tooltip dom ready event.
		 *
		 * @since 4.7.8
		 *
		 * @return {void}
		 */
		return function() {
			obj.initTooltipSlider( $container );
		};
	};

	/**
	 * Unsets map markers
	 *
	 * @since 4.7.7
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.unsetMarkers = function( $container ) {
		var $googleMapsPremium = $container.find( obj.selectors.googleMapsPremium );
		var state = $googleMapsPremium.data( 'tribeEventsState' );

		state.markers.forEach( function( marker, index ) {
			google.maps.event.clearInstanceListeners( marker );
			marker.setMap( null );
		} );

		state.markers = [];
		state.events = [];

		$googleMapsPremium.data( 'tribeEventsState', state );
	};

	/**
	 * Sets markers on the map
	 *
	 * @since 4.7.7
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 * @param {object} data       data object passed from 'afterSetup.tribeEvents' event.
	 *
	 * @return {void}
	 */
	obj.setMarkers = function( $container, data ) {
		var $googleMapsPremium = $container.find( obj.selectors.googleMapsPremium );
		var state = $googleMapsPremium.data( 'tribeEventsState' );
		var bounds = new google.maps.LatLngBounds();

		// init markers from data structure
		$.each( data.events_by_venue, function( venueId, venue ) {
			if ( ! venue.geolocation ) {
				return;
			}

			// create marker
			var marker = new google.maps.Marker( {
				position: new google.maps.LatLng( venue.geolocation.latitude, venue.geolocation.longitude ),
				map: state.map,
				eventIds: venue.event_ids,
				icon: data.map_provider.map_pin_url,
			} );

			// add click listener for marker
			marker.addListener( 'click', obj.handleMarkerClick( $container, marker ) );

			// extend bounds based on marker position
			bounds.extend( marker.getPosition() );

			// push marker to state
			state.markers.push( marker );

			// push event object to state for each event id
			venue.event_ids.forEach( function( eventId, eventIdIndex ) {
				state.events.push( {
					eventId: eventId,
					marker: marker,
					index: eventIdIndex,
				} );
			} );
		} );


		if ( 1 === state.markers.length ) {
			state.map.setCenter( state.markers[0].getPosition() );
			state.map.setZoom( obj.state.zoom );
		} else {
			state.map.fitBounds( bounds );
			google.maps.event.addListenerOnce( state.map, 'idle', function() {
				if ( state.map.getZoom() > obj.state.zoom ) {
					state.map.setZoom( obj.state.zoom );
				}
			} );
		}

		// save state to Google Maps premium
		$googleMapsPremium.data( 'tribeEventsState', state );
	};

	/**
	 * Initializes map state
	 *
	 * @since 4.7.7
	 *
	 * @param {jQuery} $googleMapsPremium jQuery object of Google Maps premium.
	 *
	 * @return {void}
	 */
	obj.initMapState = function( $googleMapsPremium ) {
		var state = {
			map: null,
			tooltip: null,
			slider: null,
			activeEventId: null,
			events: [],
			markers: [],
		};

		$googleMapsPremium.data( 'tribeEventsState', state );
	};

	/**
	 * Denitialize tooltip
	 *
	 * @since 4.7.8
	 *
	 * @param {PlainObject} state state of Google Maps premium.
	 *
	 * @return {void}
	 */
	obj.deinitTooltip = function( state ) {
		google.maps.event.clearInstanceListeners( state.tooltip );
	};

	/**
	 * Initialize tooltip
	 *
	 * @since 4.7.8
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.initTooltip = function( $container ) {
		var state = $container.find( obj.selectors.googleMapsPremium ).data( 'tribeEventsState' );
		state.tooltip.addListener( 'closeclick', obj.handleTooltipCloseClick( $container ) );
		state.tooltip.addListener( 'domready', obj.handleTooltipDomReady( $container ) );
	};

	/**
	 * Creates a new tooltip
	 *
	 * @since 4.7.8
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.createTooltip = function( $container ) {
		var $googleMapsPremium = $container.find( obj.selectors.googleMapsPremium );
		var state = $googleMapsPremium.data( 'tribeEventsState' );

		state.tooltip = new google.maps.InfoWindow();
		state.tooltip.addListener( 'closeclick', obj.handleTooltipCloseClick( $container ) );
		state.tooltip.addListener( 'domready', obj.handleTooltipDomReady( $container ) );

		$googleMapsPremium.data( 'tribeEventsState', state );
	};

	/**
	 * Creates a new map
	 *
	 * @since 4.7.7
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.createNewMap = function( $container ) {
		var $googleMapsPremium = $container.find( obj.selectors.googleMapsPremium );
		var state = $googleMapsPremium.data( 'tribeEventsState' );

		state.map = new google.maps.Map( $googleMapsPremium[0], {
			zoom: obj.state.zoom,
			center: new google.maps.LatLng( 0, 0 ),
		} );
		state.map.addListener( 'click', obj.handleMapClick( $container, state.map ) );

		$googleMapsPremium.data( 'tribeEventsState', state );
	};

	/**
	 * Caches the map and moves it outside the container
	 *
	 * @since 4.7.7
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.cacheMap = function( $container ) {
		$container
			.find( obj.selectors.googleMapsPremium )
			.addClass( obj.selectors.tribeCommonA11yHiddenClass.className() )
			.insertAfter( $container );
	};

	/**
	 * Gets cached map and moved it into the container
	 *
	 * @since 4.7.7
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.getCachedMap = function( $container ) {
		var $googleMapsPremium = $container
			.siblings( obj.selectors.googleMapsPremium )
			.removeClass( obj.selectors.tribeCommonA11yHiddenClass.className() );

		$container
			.find( obj.selectors.googleMapsPremium )
			.replaceWith( $googleMapsPremium );
	};

	/**
	 * Checks whether the map is cached or not
	 *
	 * @since 4.7.7
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 *
	 * @return {boolean}
	 */
	obj.isMapCached = function( $container ) {
		return 0 !== $container.siblings( obj.selectors.googleMapsPremium ).length;
	};

	/**
	 * Deinitializes the Google Maps premium map
	 *
	 * @since 4.7.7
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.deinitMap = function( $container ) {
		// find Google Maps premium
		var $googleMapsPremium = $container.find( obj.selectors.googleMapsPremium );

		if ( $googleMapsPremium.length && 'undefined' !== typeof google ) {
			var state = $googleMapsPremium.data( 'tribeEventsState' );

			// unset markers, deinit slider and tooltip
			obj.unsetMarkers( $container );
			obj.closeTooltip( state );
			obj.deinitTooltip( state );

			// set active event id to null
			state.activeEventId = null;
			$googleMapsPremium.data( 'tribeEventsState', state );

			// cache map
			obj.cacheMap( $container );
		}
	};

	/**
	 * Initializes the Google Maps premium map
	 *
	 * @since 4.7.7
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 * @param {object} data       data object passed from 'afterSetup.tribeEvents' event.
	 *
	 * @return {void}
	 */
	obj.initMap = function( $container, data ) {
		// find Google Maps premium
		var $googleMapsPremium = $container.find( obj.selectors.googleMapsPremium );

		if ( $googleMapsPremium.length && 'undefined' !== typeof google ) {
			// check if map exists
			if ( obj.isMapCached( $container ) ) {
				// get cached map
				obj.getCachedMap( $container );
				obj.initTooltip( $container );
			} else {
				// init map state, create tooltip and new map
				obj.initMapState( $googleMapsPremium );
				obj.createTooltip( $container );
				obj.createNewMap( $container );
			}

			// set markers
			obj.setMarkers( $container, data );
		}
	};

	/**
	 * Curry function to handle ajax success of loading Google Maps script.
	 * Used to pass in `$container` and `data`.
	 *
	 * @since 4.7.7
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 * @param {object} data       data object passed from 'afterSetup.tribeEvents' event.
	 *
	 * @return {function}
	 */
	obj.handleMapsScriptLoadedSuccess = function( $container, data ) {
		/**
		 * Handle ajax success of loading Google Maps script.
		 *
		 * @since 4.7.7
		 *
		 * @param  {*}      script     Script data
		 * @param  {string} textStatus Status message
		 * @param  {jqXHR}  jqXHR      Request object
		 *
		 * @return {void}
		 */
		return function( script, textStatus, jqXHR ) {
			obj.state.mapsScriptLoaded = true;
			obj.initMap( $container, data );
			$container.on( 'afterMapEventClick.tribeEvents', obj.handleEventClick );
			$container.on( 'mapDeinit.tribeEvents', { container: $container }, obj.deinit );
		};
	};

	/**
	 * Sets whether map view is premium or not and returns it
	 *
	 * @since 4.7.7
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 * @param {object} data       data object passed from 'afterSetup.tribeEvents' event.
	 *
	 * @return {boolean}
	 */
	obj.setIsPremium = function( $container, data ) {
		var state = {
			isPremium: data.map_provider.is_premium,
		};

		$container.find( obj.selectors.map ).data( 'tribeEventsState', state );

		return state.isPremium;
	};

	/**
	 * Deinitialize map events.
	 *
	 * @since 4.7.7
	 *
	 * @param  {Event}       event    event object for 'afterSetup.tribeEvents' event
	 * @param  {jqXHR}       jqXHR    Request object
	 * @param  {PlainObject} settings Settings that this request was made with
	 *
	 * @return {void}
	 */
	obj.deinit = function( event, jqXHR, settings ) {
		var $container = event.data.container;
		obj.deinitMap( $container );
		$container.off( 'afterMapEventClick.tribeEvents', obj.handleEventClick );
		$container.off( 'mapDeinit.tribeEvents', obj.deinit );
	};

	/**
	 * Initialize map events.
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
		if ( 'map' !== data.slug ) {
			return;
		}

		// set zoom level
		obj.state.zoom = data.map_provider.zoom;
		var isPremium = obj.setIsPremium( $container, data );

		// If premium maps
		if ( isPremium ) {

			// If the maps script is not loaded, fetch map script and init on success
			if ( ! obj.state.mapsScriptLoaded ) {
				var url = data.map_provider.javascript_url + '?key=' + data.map_provider.api_key;

				$.ajax( {
					url: url,
					dataType: 'script',
					success: obj.handleMapsScriptLoadedSuccess( $container, data ),
				} );
			} else {
				// If the maps script is loaded, init
				obj.initMap( $container, data );
				$container.on( 'afterMapEventClick.tribeEvents', obj.handleEventClick );
				$container.on( 'mapDeinit.tribeEvents', { container: $container }, obj.deinit );
			}
		} else {
			// If not premium maps, init
			$container.on( 'afterMapEventClick.tribeEvents', obj.handleEventClick );
			$container.on( 'mapDeinit.tribeEvents', { container: $container }, obj.deinit );
		}
	};

	/**
	 * Handles the initialization of the week grid scroller when Document is ready
	 *
	 * @since 4.7.7
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		$document.on( 'mapInit.tribeEvents', tribe.events.views.manager.selectors.container, obj.init );
	};

	// Configure on document ready
	$document.ready( obj.ready );
} )( jQuery, tribe.events.views.mapProviderGoogleMaps );
