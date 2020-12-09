/**
 * @file This file contains all photo view specific javascript.
 * This file should load after all vendors and core events javascript.
 * @version 3.0
 */

(function( window, document, $, td, te, tf, ts, tt, config, dbug ) {

	/*
	 * $    = jQuery
	 * td   = tribe_ev.data
	 * te   = tribe_ev.events
	 * tf   = tribe_ev.fn
	 * ts   = tribe_ev.state
	 * tt   = tribe_ev.tests
	 * dbug = tribe_debug
	 */

	// @ifdef DEBUG
	if ( dbug ) {
		if ( !$().isotope ) {
			tec_debug.warn( 'TEC Debug: vendor isotope was not loaded before its dependent file tribe-photo-view.js' );
		}
	}
	// @endif

	$( document ).ready( function() {

		var tribe_is_paged = tf.get_url_param( 'tribe_paged' ),
			tribe_display = tf.get_url_param( 'tribe_event_display' ),
			$container = $( '#tribe-events-photo-events' ),
			container_width = 0,
			resize_timer;

		ts.view = 'photo';

		if ( tribe_is_paged ) {
			ts.paged = tribe_is_paged;
		}

		if ( tribe_display == 'past' ) {
			ts.view = 'past';
		}

		/**
		 * @function tribe_show_loader
		 * @desc Show photo view loading mask if set.
		 */

		function tribe_show_loader() {
			$( '.photo-loader' ).show();
			$( '#tribe-events-photo-events' ).addClass( "photo-hidden" );
		}

		/**
		 * @function tribe_hide_loader
		 * @desc Hide photo view loading mask if set.
		 */

		function tribe_hide_loader() {
			$( '.photo-loader' ).hide();
			$( '#tribe-events-photo-events' ).removeClass( "photo-hidden" ).animate( {"opacity": "1"}, {duration: 600} );
		}

		/**
		 * @function tribe_setup_isotope
		 * @desc tribe_setup_isotope applies isotope layout to the events list, on initial load and on ajax.
		 * @param {jQuery} $container The photo view container.
		 */
		function tribe_setup_isotope( $container ) {
			if ( $().isotope ) {

				$container.isotope({
					itemSelector: '.tribe-events-photo-event',
					percentPosition: true,
					masonry: {
						columnWidth: '.tribe-events-photo-grid-sizer',
						gutter: '.tribe-events-photo-gutter-sizer'
					}
				});

				$container.imagesLoaded().progress( function() {
					$container.isotope( 'layout' );
				});

			} else {
				$( '#tribe-events-photo-events' ).removeClass( "photo-hidden" ).css( "opacity", "1" );
			}
		}

		tribe_setup_isotope( $container );

		if ( tt.pushstate && !tt.map_view() ) {

			var params = 'action=tribe_photo&tribe_paged=' + ts.paged;

			if ( td.params.length ) {
				params = params + '&' + td.params;
			}

			if ( ts.category ) {
				params = params + '&tribe_event_category=' + ts.category;
			}

			if ( tf.is_featured() ) {
				params = params + '&featured=1';
			}

			var isShortcode = $( document.getElementById( 'tribe-events' ) ).is( '.tribe-events-shortcode' );

			if ( ! isShortcode || false !== config.update_urls.shortcode.photo ) {
				history.replaceState( {
					'tribe_params': params,
					'tribe_url_params': td.params
				}, '', location.href );
			}

			$( window ).on( 'popstate', function( event ) {

				var state = event.originalEvent.state;

				if ( state ) {
					ts.do_string = false;
					ts.pushstate = false;
					ts.popping = true;
					ts.params = state.tribe_params;
					ts.url_params = state.tribe_url_params;
					tf.pre_ajax( function() {
						tribe_events_photo_ajax_post();
					} );

					tf.set_form( ts.params );
				}
			} );
		}

		$( '#tribe-events' ).on( 'click', 'li.tribe-events-nav-next a',function( e ) {
			e.preventDefault();
			if ( ts.ajax_running ) {
				return;
			}
			if ( ts.view == 'past' ) {
				if ( ts.paged == '1' ) {
					ts.view = 'photo';
				}
				else {
					ts.paged--;
				}
			}
			else {
				ts.paged++;
			}
			ts.popping = false;
			tf.pre_ajax( function() {
				tribe_events_photo_ajax_post();
			} );
		} ).on( 'click', 'li.tribe-events-nav-previous a', function( e ) {
			e.preventDefault();
			if ( ts.ajax_running ) {
				return;
			}
			if ( ts.view == 'photo' ) {
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
			tf.pre_ajax( function() {
				tribe_events_photo_ajax_post();
			} );
		} );

		/**
		 * @function tribe_events_bar_photoajax_actions
		 * @desc On events bar submit, this function collects the current state of the bar and sends it to the photo view ajax handler.
		 * @param {event} e The event object.
		 */
		function tribe_events_bar_photoajax_actions( e ) {
			if ( tribe_events_bar_action != 'change_view' ) {
				e.preventDefault();
				if ( ts.ajax_running ) {
					return;
				}
				ts.paged = 1;
				ts.view = 'photo';
				ts.popping = false;
				tf.pre_ajax( function() {
					tribe_events_photo_ajax_post();
				} );
			}
		}

		if ( tt.no_bar() || tt.live_ajax() && tt.pushstate ) {
			$( '#tribe-bar-date' ).on( 'changeDate', function( e ) {
				if ( !tt.reset_on() ) {
					tribe_events_bar_photoajax_actions( e )
				}
			} );
		}

		$( te ).on( 'updating-recurrence.tribe', function() {
			ts.popping = false;
		} );

		$( '#tribe-bar-form' ).on( 'submit', function( e ) {
			if ( tribe_events_bar_action != 'change_view' ) {
				tribe_events_bar_photoajax_actions( e )
			}
		} );

		tf.snap( '#tribe-events-content-wrapper', '#tribe-events-content-wrapper', '#tribe-events-footer .tribe-events-nav-previous a, #tribe-events-footer .tribe-events-nav-next a' );

		$( te ).on( 'run-ajax.tribe', function() {
			tribe_events_photo_ajax_post();
		} );

		/**
		 * @function tribe_events_photo_ajax_post
		 * @desc The ajax handler for photo view.
		 * Fires the custom event 'tribe_ev_serializeBar' at start, then 'tribe_ev_collectParams' to gather any additional parameters before actually launching the ajax post request.
		 * As post begins 'tribe_ev_ajaxStart' and 'tribe_ev_photoView_AjaxStart' are fired, and then 'tribe_ev_ajaxSuccess' and 'tribe_ev_photoView_ajaxSuccess' are fired on success.
		 * Various functions in the events plugins hook into these events. They are triggered on the tribe_ev.events object.
		 */
		function tribe_events_photo_ajax_post() {

			if ( !ts.popping ) {

				ts.ajax_running = true;
				if ( ts.filter_cats ) {
					td.cur_url = $( '#tribe-events-header' ).data( 'baseurl' );
				}

				var tribe_hash_string = $( '#tribe-events-list-hash' ).val();

				ts.params = {
					action             : 'tribe_photo',
					tribe_paged        : ts.paged,
					tribe_event_display: ts.view,
					featured           : tf.is_featured()
				};

				ts.url_params = {
					action             : 'tribe_photo',
					tribe_paged        : ts.paged,
					tribe_event_display: ts.view
				};

				if ( td.default_permalinks ) {
					if( !ts.url_params.hasOwnProperty( 'post_type' ) ){
						ts.url_params['post_type'] = config.events_post_type;
					}
					if( !ts.url_params.hasOwnProperty( 'eventDisplay' ) ){
						ts.url_params['eventDisplay'] = ts.view;
					}
				}

				if ( tribe_hash_string.length ) {
					ts.params['hash'] = tribe_hash_string;
				}

				if ( ts.category ) {
					ts.params['tribe_event_category'] = ts.category;
				}

				/**
				 * DEPRECATED: tribe_ev_serializeBar has been deprecated in 4.0. Use serialize-bar.tribe instead
				 */
				$( te ).trigger( 'tribe_ev_serializeBar' );
				$( te ).trigger( 'serialize-bar.tribe' );

				if ( tf.invalid_date_in_params( ts.params ) ) {
					ts.ajax_running = false;
					return;
				}

				$( '#tribe-events-header' ).tribe_spin();
				tribe_show_loader();

				ts.params = $.param( ts.params );
				ts.url_params = $.param( ts.url_params );

				/**
				 * DEPRECATED: tribe_ev_collectParams has been deprecated in 4.0. Use collect-params.tribe instead
				 */
				$( te ).trigger( 'tribe_ev_collectParams' );
				$( te ).trigger( 'collect-params.tribe' );

				ts.pushstate = false;
				ts.do_string = true;
			}

			if ( tt.pushstate && !ts.filter_cats ) {

				// @ifdef DEBUG
				dbug && tec_debug.time( 'Photo View Ajax Timer' );
				// @endif

				/**
				 * DEPRECATED: tribe_ev_ajaxStart and tribe_ev_photoView_AjaxStart have been deprecated in 4.0. Use ajax-start.tribe and photo-view-ajax-start.tribe instead
				 */
				$( te ).trigger( 'tribe_ev_ajaxStart' ).trigger( 'tribe_ev_photoView_AjaxStart' );
				$( te ).trigger( 'ajax-start.tribe' ).trigger( 'photo-view-ajax-start.tribe' );

				$.post(
					TribePhoto.ajaxurl,
					ts.params,
					function( response ) {

						ts.initial_load = false;
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

							$( '#tribe-events-list-hash' ).val( response.hash );

							var $the_content = $.parseHTML( response.html );

							$( '#tribe-events-content' ).replaceWith( $the_content );
							$( '#tribe-events-content' ).prev( '#tribe-events-list-hash' ).remove();
							$( '.tribe-events-promo' ).next( '.tribe-events-promo' ).remove();

							//If no events are returned, then hide Header
							if ( response.max_pages == 0 ) {
								$( '#tribe-events-header' ).hide();
							}

							ts.page_title = $( '#tribe-events-header' ).data( 'title' );
							ts.view_title = $( '#tribe-events-header' ).data( 'viewtitle' );
							if ( ts.page_title ) {
								document.title = ts.page_title;
							}
							$( '.tribe-events-page-title' ).html( ts.view_title );

							var isShortcode = $( document.getElementById( 'tribe-events' ) ).is( '.tribe-events-shortcode' );
							var shouldUpdateHistory = ! isShortcode || false !== config.update_urls.shortcode.photo;

							if ( ts.do_string && shouldUpdateHistory ) {
								history.pushState( {
									"tribe_params"    : ts.params,
									"tribe_url_params": ts.url_params
								}, ts.page_title, td.cur_url + '?' + ts.url_params );
							}

							if ( ts.pushstate && shouldUpdateHistory ) {
								history.pushState( {
									"tribe_params"    : ts.params,
									"tribe_url_params": ts.url_params
								}, ts.page_title, td.cur_url );
							}

							tribe_setup_isotope( $( '#tribe-events-photo-events' ) );

							/**
							 * DEPRECATED: tribe_ev_ajaxSuccess and tribe_ev_photoView_AjaxSuccess have been deprecated in 4.0. Use ajax-success.tribe and photo-view-ajax-success.tribe instead
							 */
							$( te ).trigger( 'tribe_ev_ajaxSuccess' ).trigger( 'tribe_ev_photoView_AjaxSuccess' );
							$( te ).trigger( 'ajax-success.tribe' ).trigger( 'photo-view-ajax-success.tribe' );

							// @ifdef DEBUG
							dbug && tec_debug.timeEnd( 'Photo View Ajax Timer' );
							// @endif

						}
					}
				);
			}
			else {

				if ( ts.url_params.length ) {
					window.location = td.cur_url + '?' + ts.url_params;
				}
				else {
					window.location = td.cur_url;
				}
			}
		}

		// @ifdef DEBUG
		dbug && tec_debug.info( 'TEC Debug: tribe-events-photo-view.js successfully loaded' );
		ts.view && dbug && tec_debug.timeEnd( 'Tribe JS Init Timer' );
		// @endif

	} );

})( window, document, jQuery, tribe_ev.data, tribe_ev.events, tribe_ev.fn, tribe_ev.state, tribe_ev.tests, tribe_js_config, tribe_debug );
