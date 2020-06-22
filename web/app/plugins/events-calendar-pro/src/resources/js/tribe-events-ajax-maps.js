/**
 * @file This file contains all map view specific javascript.
 * This file should load after all vendors and core events javascript.
 * @version 3.0
 */

(function( window, document, $, td, te, tf, tg, ts, tt, config, dbug ) {

	/*
	 * $    = jQuery
	 * td   = tribe_ev.data
	 * te   = tribe_ev.events
	 * tf   = tribe_ev.fn
	 * tg   = tribe_ev.geoloc
	 * ts   = tribe_ev.state
	 * tt   = tribe_ev.tests
	 * dbug = tribe_debug
	 */

	// If we do not have valid geolocation data (ie, the geocenter values are nulls) then let's
	// gracefully exit before attempting to interact with the Google Maps API
	if ( null === TribeEventsPro.geocenter.max_lat ) {
		return;
	}

	$.extend( tribe_ev.fn, {

		/**
		 * @function tribe_ev.fn.map_add_marker
		 * @desc tribe_ev.fn.map_add_marker adds event markers to the map on geoloc view.
		 * @param {String} lat Marker latitude.
		 * @param {String} lng Marker longitude.
		 * @param {String} title Marker event title.
		 * @param {String} address Marker event address.
		 * @param {String} link Marker event permalink.
		 */

		map_add_marker: function( lat, lng, title, address, link ) {
			if ( 'undefined' == typeof google ) {
				return;
			}

			var myLatlng = new google.maps.LatLng( lat, lng );

			var marker = {
					position: myLatlng,
					map     : tg.map,
					title   : title
				};

			// If we have a Map Pin set, we use it
			if ( 'undefined' !== GeoLoc.pin_url && GeoLoc.pin_url ) {
				marker.icon = GeoLoc.pin_url;
			}

			// Overwrite with an actual object
			marker = new google.maps.Marker( marker );

			var infoWindow = new google.maps.InfoWindow();

			var content_title = title;
			if ( link ) {
				content_title = $( '<div/>' ).append( $( "<a/>" ).attr( 'href', link ).text( title ) ).html();
			}

			var content = TribeEventsPro.map_tooltip_event + content_title;

			if ( address ) {
				content = content + "<br/>" + TribeEventsPro.map_tooltip_address + address;
			}

			infoWindow.setContent( content );

			google.maps.event.addListener( marker, 'click', function( event ) {
				infoWindow.open( tg.map, marker );
			} );

			tg.markers.push( marker );

			if ( tg.refine ) {
				marker.setVisible( false );
			}
			tg.bounds.extend( myLatlng );
		}
	} );

	try {
		tg.geocoder = new google.maps.Geocoder();
		tg.bounds = new google.maps.LatLngBounds();
	} catch( e ) {};

	$( document ).ready( function() {

		/**
		 * @function tribe_test_location
		 * @desc tribe_test_location clears the lat and lng values in event bar if needed. Also hides or shows the geofence filter if present.
		 */
		var $tribeBar = $( '#tribe-bar-geoloc' );
		var $fence = $( '#tribe_events_filter_item_geofence' );
		var $latlng = $( '#tribe-bar-geoloc-lat, #tribe-bar-geoloc-lng' );

		function tribe_test_location() {

			if ( $tribeBar.length ) {
				if ( $tribeBar.val() ) {
					$fence.show();
				}
				else {
					$fence.hide();
					if ( $latlng.length ) {
						$latlng.val( '' );
					}
				}
			}
		}

		/**
		 * Listen for either a key DOWN or UP to see if we have a value on the location field if the field is not empty
		 * we display the $fence field which is the field with the distance dropdown so is available as son as there's
		 * something to be displayed.
		 *
		 * @since 4.4.22
		 */
		$( '#tribe-bar-geoloc' ).on( 'keydown keyup', function( e ) {
			$fence.toggle( this.value.trim() !== '' );
		} );


		tribe_test_location();

		var $tribe_container = $( '#tribe-events' );
		var $geo_bar_input   = $( '#tribe-bar-geoloc' );
		var $geo_options     = $( '#tribe-geo-options' );
		var invalid_date     = false;

		var mapEl = document.getElementById( 'tribe-geo-map' );

		if ( mapEl && 'undefined' !== typeof google ) {

			var options = {
				zoom     : 5,
				center   : new google.maps.LatLng( TribeEventsPro.geocenter.max_lat, TribeEventsPro.geocenter.max_lng ),
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};

			tg.map = new google.maps.Map( mapEl, options );
			tg.bounds = new google.maps.LatLngBounds();

			/**
			 * Trigger a new event when the Map is created in order to allow Users option to customize the map by listening
			 * to the correct event and having an instance of the Map variable avialable to modify if required.
			 *
			 * @param {Object} map An instance of the Google Map.
			 * @param {Element} el The DOM Element where the map is attached.
			 * @param {Object} options The initial set of options for the map.
			 * @param {Object} bounds An instance with the bounds of the Map.
			 *
			 * @since 4.4.22
			 */
			$( 'body' ).trigger( 'map-created.tribe', [ tg.map, mapEl, options, tg.bounds ] );

			var minLatlng = new google.maps.LatLng( TribeEventsPro.geocenter.min_lat, TribeEventsPro.geocenter.min_lng );
			tg.bounds.extend( minLatlng );

			var maxLatlng = new google.maps.LatLng( TribeEventsPro.geocenter.max_lat, TribeEventsPro.geocenter.max_lng );
			tg.bounds.extend( maxLatlng );
		}
		if ( $().placeholder ) {
			$( '#tribe-geo-location' ).placeholder();
		}

		if ( tt.map_view() ) {

			var tribe_is_paged = tf.get_url_param( 'tribe_paged' ),
				tribe_display = tf.get_url_param( 'tribe_event_display' );

			if ( tribe_is_paged ) {
				ts.paged = tribe_is_paged;
			}

			ts.view = 'map';

			if ( tribe_display == 'past' ) {
				ts.view = 'past';
			}

			tf.tooltips();
		}

		if ( tt.map_view() && td.params ) {

			var tp = td.params;
			if ( tf.in_params( tp, "tribe_geosearch" ) >= 0 ) {
			}
			else {
				tp += '&action=tribe_geosearch';
			}
			if ( tf.in_params( tp, "tribe_paged" ) >= 0 ) {
			}
			else {
				tp += '&tribe_paged=1';
			}

			ts.params = tp;

			ts.do_string = false;
			ts.pushstate = false;
			ts.popping = true;
			tf.pre_ajax( function() {
				tribe_map_processOption();
			} );
		}
		else if ( tt.map_view() ) {

			ts.do_string = false;
			ts.pushstate = false;
			ts.popping = false;
			ts.initial_load = true;
			tf.pre_ajax( function() {
				tribe_map_processOption();
			} );
		}

		if ( tt.pushstate && tt.map_view() ) {

			var isShortcode = $( document.getElementById( 'tribe-events' ) ).is( '.tribe-events-shortcode' );

			if ( ! isShortcode || false !== config.update_urls.shortcode.map ) {
				history.replaceState( {
					'tribe_paged': ts.paged,
					'tribe_params': ts.params
				}, '', location.href );
			}

			$( window ).on( 'popstate', function( event ) {

				var state = event.originalEvent.state;

				if ( state ) {
					ts.do_string = false;
					ts.pushstate = false;
					ts.popping = true;
					ts.params = state.tribe_params;
					ts.paged = state.tribe_paged;
					tf.pre_ajax( function() {
						tribe_map_processOption();
					} );

					tf.set_form( ts.params );
				}
			} );
		}

		if ( tt.map_view() ) {

			$tribe_container.on( 'click', '.tribe-geo-option-link', function( e ) {
				e.preventDefault();
				e.stopPropagation();
				var $this = $( this );

				$( '.tribe-geo-option-link' ).removeClass( 'tribe-option-loaded' );
				$this.addClass( 'tribe-option-loaded' );

				$geo_bar_input.val( $this.text() );

				$( '#tribe-bar-geoloc-lat' ).val( tg.geocodes[$this.data( 'index' )].geometry.location.lat() );
				$( '#tribe-bar-geoloc-lng' ).val( tg.geocodes[$this.data( 'index' )].geometry.location.lng() );

				ts.do_string = true;
				ts.pushstate = false;
				ts.popping = false;

				if ( tt.pushstate ) {
					tf.pre_ajax( function() {
						tribe_map_processOption();
						$geo_options.hide();
					} );
				}
				else {
					tf.pre_ajax( function() {
						/**
						 * DEPRECATED: tribe_ev_reloadOldBrowser has been deprecated in 4.0. Use reload-old-browser.tribe instead
						 */
						$( te ).trigger( 'tribe_ev_reloadOldBrowser' );
						$( te ).trigger( 'reload-old-browser.tribe' );
					} );
				}

			} );

			$( document ).on( 'click', function() {
				$geo_options.hide();
			} );

			tf.snap( '#tribe-events-content-wrapper', '#tribe-events-content-wrapper', '#tribe-events-footer .tribe-events-nav-previous a, #tribe-events-footer .tribe-events-nav-next a' );

		}

		/**
		 * @function tribe_generate_map_params
		 * @desc tribe_generate_map_params generates query parameters for the map view ajax call.
		 */

		function tribe_generate_map_params() {
			ts.ajax_running = true;
			ts.params = {
				action             : 'tribe_geosearch',
				tribe_paged        : ts.paged,
				tribe_event_display: ts.view,
				featured           : tf.is_featured()
			};

			if ( ts.category ) {
				ts.params.tribe_event_category = ts.category;
			}

			if ( td.default_permalinks ) {
				if( !ts.params.hasOwnProperty( 'post_type' ) ){
					ts.params['post_type'] = config.events_post_type;
				}
				if( !ts.params.hasOwnProperty( 'eventDisplay' ) ){
					ts.params['eventDisplay'] = 'map';
				}
			}

			/**
			 * DEPRECATED: tribe_ev_serializeBar has been deprecated in 4.0. Use serialize-bar.tribe instead
			 */
			$( te ).trigger( 'tribe_ev_serializeBar' );
			$( te ).trigger( 'serialize-bar.tribe' );

			if ( tf.invalid_date_in_params( ts.params ) ) {
				ts.ajax_running = false;
				invalid_date = true;
				return;
			}
			else {
				invalid_date = false;
			}

			ts.params = $.param( ts.params );

			/**
			 * DEPRECATED: tribe_ev_collectParams has been deprecated in 4.0. Use collect-params.tribe instead
			 */
			$( te ).trigger( 'tribe_ev_collectParams' );
			$( te ).trigger( 'collect-params.tribe' );

		}

		$( te ).on( 'reload-old-browser.tribe', function() {
			tribe_generate_map_params();
			window.location = td.cur_url + '?' + ts.params;
		} );

		/**
		 * @function tribe_map_processOption
		 * @desc tribe_map_processOption is the main ajax event query for map view.
		 */

		function tribe_map_processOption() {

			if ( !ts.popping ) {
				tribe_generate_map_params();
				ts.pushstate = false;
				if ( !ts.initial_load ) {
					ts.do_string = true;
				}
			}

			if ( invalid_date ) {
				return;
			}

			$( '#tribe-events-content .tribe-events-loop' ).tribe_spin();
			deleteMarkers();

			$.post( GeoLoc.ajaxurl, ts.params, function( response ) {

				/**
				 * DEPRECATED: tribe_ev_ajaxStart and tribe_ev_mapView_AjaxStart have been deprecated in 4.0. Use ajax-start.tribe and map-view-ajax-start.tribe instead
				 */
				$( te ).trigger( 'tribe_ev_ajaxStart' ).trigger( 'tribe_ev_mapView_AjaxStart' );
				$( te ).trigger( 'ajax-start.tribe' ).trigger( 'map-view-ajax-start.tribe' );

				tf.enable_inputs( '#tribe_events_filters_form', 'input, select' );

				if ( response.success ) {

					ts.ajax_running = false;

					td.ajax_response = {
						'total_count': parseInt( response.total_count ),
						'view'       : response.view,
						'max_pages'  : response.max_pages,
						'tribe_paged': response.tribe_paged,
						'timestamp'  : new Date().getTime()
					};

					ts.initial_load = false;

					var $the_content = $.parseHTML( response.html );

					$( '#tribe-events-content' ).replaceWith( $the_content );

					//If no events are returned, then hide Header
					if ( response.total_count == 0 ) {
						$( '#tribe-events-header' ).hide();
					}

					$.each( response.markers, function( i, e ) {
						tf.map_add_marker( e.lat, e.lng, e.title, e.address, e.link );
					} );

					if ( tt.pushstate ) {

						ts.page_title = $( '#tribe-events-header' ).data( 'title' );
						ts.view_title = $( '#tribe-events-header' ).data( 'viewtitle' );

						if ( ts.page_title ) {
							document.title = ts.page_title;
						}

						$( '.tribe-events-page-title' ).html( ts.view_title );

						var isShortcode = $( document.getElementById( 'tribe-events' ) ).is( '.tribe-events-shortcode' );
						var shouldUpdateHistory = ! isShortcode || false !== config.update_urls.shortcode.map;

						if ( ts.do_string && shouldUpdateHistory ) {
							// strip the baseurl from the push state URL
							var params = ts.params.replace( /&?baseurl=[^&]*/i, '' );

							history.pushState( {
								"tribe_paged" : ts.paged,
								"tribe_params": ts.params
							}, ts.page_title, td.cur_url + '?' + params );
						}

						if ( ts.pushstate && shouldUpdateHistory ) {
							history.pushState( {
								"tribe_paged" : ts.paged,
								"tribe_params": ts.params
							}, ts.page_title, td.cur_url );
						}

					}

					/**
					 * DEPRECATED: tribe_ev_ajaxSuccess and tribe_ev_mapView_AjaxSuccess have been deprecated in 4.0. Use ajax-success.tribe and map-view-ajax-success.tribe instead
					 */
					$( te ).trigger( 'tribe_ev_ajaxSuccess' ).trigger( 'tribe_ev_mapView_AjaxSuccess' );
					$( te ).trigger( 'ajax-success.tribe' ).trigger( 'map-view-ajax-success.tribe' );

					if ( response.markers.length > 0 ) {
						centerMap();
					}
				}
			} );

		}

		if ( tt.map_view() ) {

			var center;

			$( "#tribe-geo-map-wrapper" ).resize( function() {
				center = tg.map.getCenter();
				google.maps.event.trigger( tg.map, "resize" );
				tg.map.setCenter( center );
			} );

			$( '#tribe-events' ).on( 'click', 'li.tribe-events-nav-next a',function( e ) {
				e.preventDefault();
				if ( ts.ajax_running ) {
					return;
				}
				if ( ts.view === 'past' ) {
					if ( ts.paged == '1' ) {
						ts.view = 'map';
					}
					else {
						ts.paged--;
					}
				}
				else {
					ts.paged++;
				}
				ts.popping = false;
				if ( tt.pushstate ) {
					tf.pre_ajax( function() {
						tribe_map_processOption();
					} );
				}
				else {
					tf.pre_ajax( function() {
						/**
						 * DEPRECATED: tribe_ev_reloadOldBrowser has been deprecated in 4.0. Use reload-old-browser.tribe instead
						 */
						$( te ).trigger( 'tribe_ev_reloadOldBrowser' );
						$( te ).trigger( 'reload-old-browser.tribe' );
					} );
				}
			} ).on( 'click', 'li.tribe-events-nav-previous a', function( e ) {
				e.preventDefault();
				if ( ts.ajax_running ) {
					return;
				}
				if ( ts.view === 'map' ) {
					if ( ts.paged == '1' ) {
						ts.view = 'past';
					}
					else {
						ts.paged--;
					}
				}
				else {
					ts.paged++;
				}
				ts.popping = false;
				if ( tt.pushstate ) {
					tf.pre_ajax( function() {
						tribe_map_processOption( null );
					} );
				}
				else {
					tf.pre_ajax( function() {
						/**
						 * DEPRECATED: tribe_ev_reloadOldBrowser has been deprecated in 4.0. Use reload-old-browser.tribe instead
						 */
						$( te ).trigger( 'tribe_ev_reloadOldBrowser' );
						$( te ).trigger( 'reload-old-browser.tribe' );
					} );
				}
			} );

		}

		/**
		 * @function tribe_events_bar_mapajax_actions
		 * @desc On events bar submit, this function collects the current state of the bar and sends it to the map view ajax handler.
		 * @param {event} e The event object.
		 */

		function tribe_events_bar_mapajax_actions( e ) {
			if ( tribe_events_bar_action != 'change_view' ) {
				e.preventDefault();
				if ( ts.ajax_running ) {
					return;
				}
				ts.paged = 1;
				ts.view = 'map';
				ts.popping = false;
				if ( tt.pushstate ) {
					tf.pre_ajax( function() {
						tribe_map_processOption( null );
					} );
				}
				else {
					tf.pre_ajax( function() {
						/**
						 * DEPRECATED: tribe_ev_reloadOldBrowser has been deprecated in 4.0. Use reload-old-browser.tribe instead
						 */
						$( te ).trigger( 'tribe_ev_reloadOldBrowser' );
						$( te ).trigger( 'reload-old-browser.tribe' );
					} );
				}

			}
		}

		if ( (GeoLoc.map_view && $( 'form#tribe-bar-form' ).length && tt.live_ajax() && tt.pushstate) || (GeoLoc.map_view && tt.no_bar()) ) {
			$( '#tribe-events-bar' ).on( 'changeDate', '#tribe-bar-date', function( e ) {
				tribe_events_bar_mapajax_actions( e );
			} );
		}

		if ( GeoLoc.map_view ) {
			$( te ).on( 'run-ajax.tribe', function() {
				tribe_map_processOption();
			} );
		}

		/**
		 * @function deleteMarkers
		 * @desc Clears markers from the active map.
		 */

		function deleteMarkers() {
			if ( tg.markers && 'undefined' !== typeof google ) {
				for ( i in tg.markers ) {
					tg.markers[i].setMap( null );
				}
				tg.markers.length = 0;
				tg.bounds = new google.maps.LatLngBounds();
			}
		}

		/**
		 * @function centerMap
		 * @desc Centers the active map.
		 */

		function centerMap() {

			if ( 'undefined' == typeof google ) {
				return;
			}

			tg.map.fitBounds( tg.bounds );

			if ( tg.map.getZoom() > 13 ) {
				tg.map.setZoom( 13 );
			}
		}

		function spin_start() {
			$( '#tribe-events-footer, #tribe-events-header' ).find( '.tribe-events-ajax-loading' ).show();
		}

		function spin_end() {
			$( '#tribe-events-footer, #tribe-events-header' ).find( '.tribe-events-ajax-loading' ).hide();
		}

		if ( tt.map_view() ) {

			$( 'form#tribe-bar-form' ).on( 'submit', function() {
				if ( tribe_events_bar_action != 'change_view' ) {
					ts.paged = 1;
					spin_start();

					// hide pagination on submit
					$( '.tribe-events-sub-nav' ).remove();

					var val = $( '#tribe-bar-geoloc' ).val();

					if ( val !== '' ) {

						ts.do_string = true;
						ts.pushstate = false;
						ts.popping = false;

						deleteMarkers();
						$( "#tribe-geo-results" ).empty();
						$( "#tribe-geo-options" ).hide();
						$( "#tribe-geo-options #tribe-geo-links" ).empty();

						tf.process_geocoding( val, function( results ) {
							tg.geocodes = results;

							spin_end();

							var lat = results[0].geometry.location.lat();
							var lng = results[0].geometry.location.lng();

							if ( lat ) {
								$( '#tribe-bar-geoloc-lat' ).val( lat );
							}

							if ( lng ) {
								$( '#tribe-bar-geoloc-lng' ).val( lng );
							}

							if ( tg.geocodes.length > 1 ) {
								tf.print_geo_options();
								tribe_test_location();
								centerMap();


							}
							else {
								if ( tt.pushstate ) {
									tribe_test_location();
									tribe_map_processOption( tg.geocodes[0] );
								}
								else {
									/**
									 * DEPRECATED: tribe_ev_reloadOldBrowser has been deprecated in 4.0. Use reload-old-browser.tribe instead
									 */
									$( te ).trigger( 'tribe_ev_reloadOldBrowser' );
									$( te ).trigger( 'reload-old-browser.tribe' );
								}
							}

						} );

						return false;
					}

					if ( val === '' ) {
						$( '#tribe-bar-geoloc-lat' ).val( '' );
						$( '#tribe-bar-geoloc-lng' ).val( '' );
						$( "#tribe-geo-options" ).hide();
						//We can show the map even if we don't get a geo query
						if ( tt.pushstate ) {
							ts.do_string = true;
							ts.pushstate = false;
							ts.popping = false;
							tribe_test_location();
							tribe_map_processOption();
						}
						else {
							/**
							 * DEPRECATED: tribe_ev_reloadOldBrowser has been deprecated in 4.0. Use reload-old-browser.tribe instead
							 */
							$( te ).trigger( 'tribe_ev_reloadOldBrowser' );
							$( te ).trigger( 'reload-old-browser.tribe' );
						}
						spin_end();
						return false;

					}
					return true;
				}
			} );
			// @ifdef DEBUG
			ts.view && dbug && tec_debug.timeEnd( 'Tribe JS Init Timer' );
			// @endif
		}

		// @ifdef DEBUG
		dbug && tec_debug.info( 'TEC Debug: tribe-events-ajax-maps.js successfully loaded' );
		// @endif

	} );

})( window, document, jQuery, tribe_ev.data, tribe_ev.events, tribe_ev.fn, tribe_ev.geoloc, tribe_ev.state, tribe_ev.tests, tribe_js_config, tribe_debug );
