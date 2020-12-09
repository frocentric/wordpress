/**
 * @file This file contains all week view specific javascript.
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

	$( document ).ready( function() {

		var $body            = $( 'body' ),
			$tribedate       = $( '#tribe-bar-date' ),
			$tribe_container = $( '#tribe-events' ),
			$tribe_bar       = $( '#tribe-events-bar' ),
			$tribe_header    = $( '#tribe-events-header' ),
			start_day        = 0,
			date_mod         = false,
			$first_event     = $( '.column.tribe-week-grid-hours div:first-child' );

		var base_url = '/';

		if ( 'undefined' !== typeof config.events_base ) {
			base_url = $( '#tribe-events-header' ).data( 'baseurl' );
		}

		if ( td.default_permalinks ) {
			base_url = base_url.split( '?' )[0];
		}

		if ( ! Array.prototype.indexOf ) {

			Array.prototype.indexOf = function( elt /*, from*/ ) {
				var len = this.length >>> 0;

				var from = Number( arguments[1] ) || 0;
				from = (from < 0)
					? Math.ceil( from )
					: Math.floor( from );
				if ( from < 0 ) {
					from += len;
				}

				for ( ; from < len; from++ ) {
					if ( from in this &&
						this[from] === elt ) {
						return from;
					}
				}
				return -1;
			};
		}

		if ( $tribe_header.length ) {
			start_day = $tribe_header.data( 'startofweek' );
		}

		$tribe_bar.addClass( 'tribe-has-datepicker' );

		var initial_date = $tribe_header.data( 'date' );

		if ( ts.datepicker_format !== '0' ) {
			initial_date = tribeDateFormat( initial_date, "tribeQuery" );
		}

		ts.date = initial_date;

		var days_to_disable = [0, 1, 2, 3, 4, 5, 6],
			index = days_to_disable.indexOf( start_day );

		if ( index > -1 ) {
			days_to_disable.splice( index, 1 );
		}

		// begin display date formatting
		var date_format = 'yyyy-mm-dd';

		if ( '0' !== ts.datepicker_format ) {

			// we are not using the default query date format, lets grab it from the data array
			date_format = td.datepicker_formats.main[ts.datepicker_format];

			var url_date = tf.get_url_param( 'tribe-bar-date' );

			// if url date is set and datepicker format is different from query format
			// we need to fix the input value to emulate that before kicking in the datepicker
			if ( url_date ) {
				$tribedate.val( tribeDateFormat( url_date, ts.datepicker_format ) );
			}
		}

		td.datepicker_opts = {
			format             : date_format,
			weekStart          : start_day,
			daysOfWeekDisabled : days_to_disable,
			autoclose          : true
		};

		// Set up some specific strings for datepicker i18n.
		tribe_ev.fn.ensure_datepicker_i18n();

		$tribedate
			.bootstrapDatepicker( td.datepicker_opts )
			.on( 'changeDate', function( e ) {
				if ( ts.updating_picker ) {
					return;
				}
				let maskKey = ts.datepicker_format.toString();

				ts.date = tribeUtils.formatDateWithMoment( e.date, "tribeQuery", maskKey );
				date_mod = true;
				if ( tt.no_bar() || tt.live_ajax() && tt.pushstate ) {
					if ( ! tt.reset_on() ) {
						tribe_events_bar_weekajax_actions( e, ts.date );
					}
				}

			} );

		function tribe_go_to_earliest_event() {

			$( '.tribe-week-grid-wrapper.tribe-scroller' ).nanoScroller( {
				paneClass          : 'scroller-pane',
				sliderClass        : 'scroller-slider',
				contentClass       : 'scroller-content',
				iOSNativeScrolling : true,
				alwaysVisible      : false,
				scrollTo           : $first_event
			} );

		}

		function tribe_add_right_class() {

			var $cols = ( $( '.tribe-week-grid-wrapper .tribe-grid-body .column' ).length > 6 ) ?
				$( '.tribe-grid-body .column:eq(5), .tribe-grid-body .column:eq(6), .tribe-grid-body .column:eq(7)' ) :
				$( '.tribe-grid-body .column:eq(4), .tribe-grid-body .column:eq(5)' );

			$cols.addClass( 'tribe-events-right' );
		}

		function tribe_set_allday_placeholder_height() {
			$( '.tribe-event-placeholder' ).each( function() {
				var pid  = $( this ).attr( 'data-event-id' );
				var hght = parseInt( $( '#tribe-events-event-' + pid ).outerHeight() );
				$( this ).height( hght );
			} );
		}

		function tribe_set_allday_spanning_events_width() {

			var $ad    = $( '.tribe-grid-allday' );
			var $ad_e  = $ad.find( '.vevent' );
			var ad_c_w = parseInt( $( '.tribe-grid-content-wrap .column' ).width() ) - 8;

			for ( var i = 1; i < 8; i++ ) {
				if ( $ad_e.hasClass( 'tribe-dayspan' + i ) ) {
					$ad.find( '.tribe-dayspan' + i ).children( 'div' ).css( 'width', ad_c_w * i + ((i * 2 - 2) * 4 + (i - 1)) + 'px' );
				}
			}

		}

		function tribe_find_overlapped_events( $week_events ) {

			$week_events.each( function() {

				var $this     = $( this );
				var $target   = $this.next();

				var css_left  = { 'left' : '0', 'width': '65%' };
				var css_right = { 'right': '0', 'width': '65%' };

				if ( $target.length ) {

					var tAxis   = $target.offset();
					var t_x     = [tAxis.left, tAxis.left + $target.outerWidth()];
					var t_y     = [tAxis.top, tAxis.top + $target.outerHeight()];
					var thisPos = $this.offset();
					var i_x     = [thisPos.left, thisPos.left + $this.outerWidth()];
					var i_y     = [thisPos.top, thisPos.top + $this.outerHeight()];

					if (
						t_x[0] < i_x[1]
						&& t_x[1] > i_x[0]
						&& t_y[0] < i_y[1]
						&& t_y[1] > i_y[0]
					) {

						if ( $this.is( '.overlap-right' ) ) {
							$target.css( css_left ).addClass( 'overlap-left' );
						}
						else if ( $this.is( '.overlap-left' ) ) {
							$target.css( css_right ).addClass( 'overlap-right' );
						}
						else {
							$this.css( css_left );
							$target.css( css_right ).addClass( 'overlap-right' );
						}
					}
				}
			} );
		}

		// count the columns and set their percentage width to fill the container before display

		function tribe_set_column_widths() {

			var $columns = $( '.tribe-grid-body .tribe-events-mobile-day.column' ),
				count    = $columns.length,
				width    = 100 / count;

			$columns.css( 'width', width + '%' );
			$( '.tribe-grid-header .tribe-grid-content-wrap .column' ).css( 'width', width + '%' );
			$( '.tribe-grid-allday .tribe-grid-content-wrap .column' ).css( 'width', width + '%' );

		}

		function tribe_display_week_view() {

			var $week_events = $( ".tribe-grid-body .tribe-grid-content-wrap .column > div[id*='tribe-events-event-']" );
			var grid_height  = $( ".tribe-week-grid-inner-wrap" ).height();
			var offset_top   = 5000;

			$week_events.each( function() {

				// iterate through each event in the main grid and set their length plus position in time.
				var $this        = $( this ),
					$event_link  = $this.find( 'a' ),
					event_hour   = $this.attr( 'data-hour' ),
					event_length = $this.attr( 'data-duration' ),
					event_min    = $this.attr( 'data-min' );

				// $event_target is our grid block with the same data-hour value as our event.
				var $event_target = $( '.tribe-week-grid-block[data-hour="' + event_hour + '"]' );

				// find it's offset from top of main grid container
				var event_position_top = 0;

				if ( $event_target.get(0) ) {
					event_position_top = $event_target.offset().top - $event_target.parent().offset().top - $event_target.parent().scrollTop();
				}

				// add the events minutes to the offset (relies on grid block being 60px, 1px per minute, nice)
				event_position_top = parseInt( Math.round( event_position_top ) ) + parseInt( event_min );

				// test if we've exceeded space because this event runs into next day
				var free_space = parseInt( grid_height ) - parseInt( event_length ) - parseInt( event_position_top );

				if ( 0 > free_space ) {
					event_length = event_length + free_space - 14;
				}

				// set length and position from top for our event and show it.
				// Also set length for the event anchor so the entire event is clickable.
				// Also ensure event title are always visible
				var link_height,
					title_height = ( $event_link.css( 'height', 'auto' ).height() ) + 5;

				link_height = ( title_height > event_length ) ? ( title_height ) : ( event_length - 16 );

				var	event_height = link_height + 16,
					link_setup   = { 'height': link_height + 'px' };

				if ( event_position_top < offset_top ) {
					offset_top   = event_position_top;
					$first_event = $this;
				}

				$this
					.css( {
						'height' : event_height + 'px',
						'top'    : event_position_top + 'px'
					} );

				$event_link
					.css( link_setup )
					.parent()
					.css( link_setup );
			} );

			if ( !$week_events.length ) {
				$first_event = $( '.column.tribe-week-grid-hours div:first-child' );
			}

			tribe_go_to_earliest_event();

			tribe_set_column_widths();

			// Fade our events in upon js load
			$( "div[id^='tribe-events-event-']" )
				.css( { 'visibility': 'visible', 'opacity': '0' } )
				.delay( 500 )
				.animate( { "opacity": "1" }, { duration: 250 } );

			// deal with our overlaps
			tribe_find_overlapped_events( $week_events );

			// set the height of the header columns to the height of the tallest
			tribe_ev.fn.equal_height( $( ".tribe-grid-header .tribe-grid-content-wrap .column" ) );

			// set the height of the allday columns to the height of the tallest
			tribe_ev.fn.equal_height( $( ".tribe-grid-allday .column" ) );

			// set the height of the other columns for week days to be as tall as the main container
			setTimeout( function() {

				var week_day_height = $( ".tribe-grid-body" ).height();

				$( ".tribe-grid-body .tribe-grid-content-wrap .column" ).height( week_day_height );

			}, 250 );
		}

		function tribe_mobile_load_events( date ) {

			var $target = $( '.tribe-mobile-day[data-day="' + date + '"]' );
			var $events = $( '.column[title="' + date + '"] .tribe-week-event' );

			if ( $events.length ) {
				$events.each( function() {

					var $this = $( this );

					if ( $this.tribe_has_attr( 'data-tribejson' ) ) {

						var data = $this.data( 'tribejson' );

						$target.append( tribe_tmpl( 'tribe_tmpl_week_mobile', data ) );
					}

				} );
			}
		}

		function tribe_mobile_setup_day( date, day_attr ) {

			var $container  = $( '#tribe-mobile-container' );
			var $target_day = $( '.tribe-mobile-day[data-day="' + date + '"]' );

			if ( $target_day.length ) {
				$target_day.show();
			} else {
				$container.append( '<div class="tribe-mobile-day" data-day="' + date + '"></div>' );
				tribe_mobile_load_events( date );
			}

			if ( ! $target_day.length ) {
				$target_day = $( '.tribe-mobile-day[data-day="' + date + '"]' );
			}

			if ( ! $target_day.find( 'h5' ).length && $target_day.find( '.tribe-events-mobile' ).length ) {
				$target_day.prepend( '<h5 class="tribe-mobile-day-date">' + day_attr + '</h5>' );
			}
		}

		function tribe_mobile_week_setup( $tribe_grid ) {

			var $mobile_days = $( '.tribe-events-mobile-day' );

			if ( ! $( '#tribe-mobile-container' ).length ) {
				$( '<div id="tribe-mobile-container" />' ).insertAfter( $tribe_grid );
			}

			$tribe_grid.hide();

			$mobile_days.each( function() {
				var $this         = $( this );
				var day_date      = $this.attr( 'title' );
				var $grid_day_col = $( '.tribe-grid-content-wrap .column[title="' + day_date + '"]' );
				var day_attr      = $grid_day_col.find( 'span' ).attr( 'data-full-date' );

				tribe_mobile_setup_day( day_date, day_attr );
			});
		}

		function tribe_week_view_init() {
			var $tribe_grid = $( '#tribe-events-content > .tribe-events-grid' );

			if ( $body.is( '.tribe-mobile' ) ) {
				tribe_mobile_week_setup( $tribe_grid );
			} else {
				$tribe_grid.show();

				tribe_set_allday_placeholder_height();
				tribe_set_allday_spanning_events_width();
				tribe_add_right_class();
				tribe_display_week_view();
			}
		}

		tribe_week_view_init();

		$( te ).on( 'resize-complete.tribe', function() {
			tribe_week_view_init();
		} );

		if ( tt.pushstate && ! tt.map_view() ) {

			var params = 'action=tribe_week&eventDate=' + ts.date;

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

			if ( ! isShortcode || false !== config.update_urls.shortcode.week ) {
				history.replaceState({
					'tribe_params': params,
					'tribe_url_params': td.params
				}, '', location.href);
			}

			$( window ).on( 'popstate', function( event ) {

				var state = event.originalEvent.state;

				if ( state ) {
					ts.do_string  = false;
					ts.pushstate  = false;
					ts.popping    = true;
					ts.params     = state.tribe_params;
					ts.url_params = state.tribe_url_params;
					tf.pre_ajax( function() {
						tribe_events_week_ajax_post();
					} );

					tf.set_form( ts.params );
				}
			} );
		}

		$tribe_container
			.on( 'click', '.tribe-events-nav-previous, .tribe-events-nav-next', function( e ) {
				e.preventDefault();

				if ( ts.ajax_running ) {
					return;
				}

				var $this  = $( this ).find( 'a' );
				ts.popping = false;
				ts.date    = $this.attr( 'data-week' );

				// Update the baseurl
				tf.update_base_url( $this.attr( 'href' ) );

				tf.update_picker( ts.date );

				tf.pre_ajax( function() {
					tribe_events_week_ajax_post();
				} );
			} );

		/**
		 * @function tribe_events_bar_weekajax_actions
		 * @desc On events bar submit, this function collects the current state of the bar and sends it to the week view ajax handler.
		 * @param {event} e The event object.
		 * @param {string} date Date passed by datepicker.
		 */

		function tribe_events_bar_weekajax_actions( e, date ) {
			if ( 'change_view' != tribe_events_bar_action ) {
				e.preventDefault();
				if ( ts.ajax_running ) {
					return;
				}

				var $tdate = $( '#tribe-bar-date' );

				ts.popping = false;

				if ( date ) {

					ts.date    = date;
					td.cur_url = base_url + ts.date + '/';

				} else if ( $tdate.length && '' !== $tdate.val() ) {
					let maskKey = ts.datepicker_format.toString();

					if ( '0' !== ts.datepicker_format ) {
						ts.date = tribeUtils.formatDateWithMoment( $tdate.bootstrapDatepicker( 'getDate' ), "tribeQuery", maskKey );
					} else {
						ts.date = $tdate.val();
					}

					td.cur_url = base_url + ts.date + '/';

				}
				else if ( date_mod ) {

					td.cur_url = base_url + ts.date + '/';

				} else {

					ts.date = td.cur_date;
					td.cur_url = base_url + td.cur_date + '/';

				}

				tf.pre_ajax( function() {
					tribe_events_week_ajax_post();
				} );
			}
		}

		$( 'form#tribe-bar-form' ).on( 'submit', function( e ) {
			tribe_events_bar_weekajax_actions( e, null );
		} );

		tf.snap( '#tribe-events-content', 'body', '#tribe-events-footer .tribe-events-nav-previous, #tribe-events-footer .tribe-events-nav-next' );

		$( te ).on( 'run-ajax.tribe', function() {
			tribe_events_week_ajax_post();
		} );

		/**
		 * @function tribe_events_week_ajax_post
		 * @desc The ajax handler for week view.
		 * Fires the custom event 'tribe_ev_serializeBar' at start, then 'tribe_ev_collectParams' to gather any additional parameters before actually launching the ajax post request.
		 * As post begins 'tribe_ev_ajaxStart' and 'tribe_ev_weekView_AjaxStart' are fired, and then 'tribe_ev_ajaxSuccess' and 'tribe_ev_weekView_ajaxSuccess' are fired on success.
		 * Various functions in the events plugins hook into these events. They are triggered on the tribe_ev.events object.
		 */

		function tribe_events_week_ajax_post() {

			if ( tf.invalid_date( ts.date ) ) {
				return;
			}

			var $tribe_header = $( '#tribe-events-header' );

			$( '.tribe-events-grid' ).tribe_spin();
			ts.pushcount    = 0;
			ts.ajax_running = true;

			if ( ! ts.popping ) {

				if ( ts.filter_cats ) {
					td.cur_url = base_url;
				}

				ts.params = {
					action    : 'tribe_week',
					eventDate : ts.date,
					tribe_event_display: ts.view,
					featured  : tf.is_featured()
				};

				ts.url_params = {};

				if ( td.default_permalinks ) {
					if ( ! ts.url_params.hasOwnProperty( 'eventDate' ) ) {
						ts.url_params['eventDate'] = ts.date;
					}
					if ( ! ts.url_params.hasOwnProperty( 'post_type' ) ) {
						ts.url_params['post_type'] = config.events_post_type;
					}
					if ( ! ts.url_params.hasOwnProperty( 'eventDisplay' ) ) {
						ts.url_params['eventDisplay'] = ts.view;
					}
				}

				// add shortcode display value
				if ( ! ts.url_params.hasOwnProperty( 'tribe_event_display' ) ) {
					ts.url_params['tribe_event_display'] = ts.view;
				}

				if ( ts.category ) {
					ts.params['tribe_event_category'] = ts.category;
				}

				/**
				 * DEPRECATED: tribe_ev_serializeBar has been deprecated in 4.0. Use serialize-bar.tribe instead
				 */
				$( te ).trigger( 'tribe_ev_serializeBar' );
				$( te ).trigger( 'serialize-bar.tribe' );

				ts.params     = $.param( ts.params );
				ts.url_params = $.param( ts.url_params );

				/**
				 * DEPRECATED: tribe_ev_collectParams has been deprecated in 4.0. Use collect-params.tribe instead
				 */
				$( te ).trigger( 'tribe_ev_collectParams' );
				$( te ).trigger( 'collect-params.tribe' );

				ts.pushstate = true;
				ts.do_string = false;

				if ( 0 < ts.pushcount || ts.filters || td.default_permalinks ) {
					ts.pushstate = false;
					ts.do_string = true;
				}

			}

			var appended = false;
			if ( tt.pushstate ) {

				// @ifdef DEBUG
				dbug && tec_debug.time( 'Week View Ajax Timer' );
				// @endif

				/**
				 * DEPRECATED: tribe_ev_ajaxStart and tribe_ev_weekView_AjaxStart have been deprecated in 4.0. Use ajax-start.tribe and week-view-ajax-start.tribe instead
				 */
				$( te ).trigger( 'tribe_ev_ajaxStart' ).trigger( 'tribe_ev_weekView_AjaxStart' );
				$( te ).trigger( 'ajax-start.tribe' ).trigger( 'week-view-ajax-start.tribe' );

				$.post(
					TribeWeek.ajaxurl,
					ts.params,
					function( response ) {

						ts.initial_load = false;
						tf.enable_inputs( '#tribe_events_filters_form', 'input, select' );

						// Bail if it's not successful
						if ( ! response.success ) {
							return;
						}

						ts.ajax_running = false;

						td.ajax_response = {
							'total_count' : '',
							'view'        : response.view,
							'max_pages'   : '',
							'tribe_paged' : '',
							'timestamp'   : new Date().getTime()
						};

						// @TODO: We need to D.R.Y. this assignment and the following if statement about shortcodes/do_string
						// Ensure that the base URL is, in fact, the URL we want
						td.cur_url = tf.get_base_url();

						var $the_content = $.parseHTML( response.html );

						$( '#tribe-events-content.tribe-events-week-grid' ).replaceWith( $the_content );

						tribe_week_view_init();

						$( "div[id*='tribe-events-event-']" ).hide().fadeIn( 'fast' );

						ts.page_title  = $( '#tribe-events-header' ).data( 'title' );
						ts.view_title  = $( '#tribe-events-header' ).data( 'viewtitle' );
						if ( ts.page_title ) {
							document.title = ts.page_title;
						}

						$( '.tribe-events-page-title' ).html( ts.view_title );

						// we only want to add query args for Shortcodes and ugly URL sites
						if (
								$( '#tribe-events.tribe-events-shortcode' ).length
								|| ts.do_string
						) {
							if ( -1 !== td.cur_url.indexOf( '?' ) ) {
								td.cur_url = td.cur_url.split( '?' )[0];
							}

							td.cur_url = td.cur_url + '?' + ts.url_params;
							appended = true;
						}

						var isShortcode = $( document.getElementById( 'tribe-events' ) ).is( '.tribe-events-shortcode' );
						var shouldUpdateHistory = ! isShortcode || false !== config.update_urls.shortcode.week;

						if ( ts.do_string && shouldUpdateHistory ) {
							history.pushState( {
								'tribe_url_params' : ts.url_params,
								'tribe_params'     : ts.params
							}, ts.page_title, appended ? td.cur_url : td.cur_url + '?' + ts.url_params );
						}

						if ( ts.pushstate && shouldUpdateHistory ) {
							history.pushState( {
								'tribe_url_params' : ts.url_params,
								'tribe_params'     : ts.params
							}, ts.page_title, appended ? td.cur_url : td.cur_url + '?' + ts.url_params );
						}

						/**
						 * DEPRECATED: tribe_ev_ajaxSuccess and tribe_ev_weekView_AjaxSuccess have been deprecated in 4.0. Use ajax-success.tribe and week-view-ajax-success.tribe instead
						 */
						$( te ).trigger( 'tribe_ev_ajaxSuccess' ).trigger( 'tribe_ev_weekView_AjaxSuccess' );
						$( te ).trigger( 'ajax-success.tribe' ).trigger( 'week-view-ajax-success.tribe' );

						// @ifdef DEBUG
						dbug && tec_debug.timeEnd( 'Week View Ajax Timer' );
						// @endif
					}
				);

			}
			else {
				if ( ts.url_params.length ) {
					window.location = appended ? td.cur_url : td.cur_url + '?' + ts.url_params;
				}
				else {
					window.location = td.cur_url;
				}
			}
		}

		// Prevent double-tap to open link to single event
		$( '.tribe-week-event a.url' ).on( 'click touchend', function( e ) {
			var el   = $( this );
			var link = el.attr( 'href' );
			window.location = link;
		} );

		// @ifdef DEBUG
		dbug && tec_debug.info( 'TEC Debug: tribe-events-week.js successfully loaded' );
		ts.view && dbug && tec_debug.timeEnd( 'Tribe JS Init Timer' );
		// @endif
	} );

})( window, document, jQuery, tribe_ev.data, tribe_ev.events, tribe_ev.fn, tribe_ev.state, tribe_ev.tests, tribe_js_config, tribe_debug );
