(function( $, td, te, tf, ts, tt, dbug ) {

	/*
	 * $    = jQuery
	 * td   = tribe_ev.data
	 * te   = tribe_ev.events
	 * tf   = tribe_ev.fn
	 * ts   = tribe_ev.state
	 * tt   = tribe_ev.tests
	 * dbug = tribe_debug
	 */

	$.extend( tribe_ev.fn, {
		set_form: function( params ) {

			var has_sliders = false;
			var $body       = $( 'body' );
			var $form       = $( document.getElementById( 'tribe_events_filters_form' ) );
			var $bar_form   = $( document.getElementById( 'tribe-bar-form' ) );

			$body.addClass( 'tribe-reset-on' );

			if ( $form.length ) {

				$form.tribe_clear_form();

				if ( $form.find( '.ui-slider' ).length ) {

					has_sliders = true;

					$( '#tribe_events_filters_form .ui-slider' ).each( function() {
						var s_id     = $( this ).attr( 'id' );
						var $this    = $( '#' + s_id );
						var $input   = $this.prev();
						var $display = $input.prev();
						var settings = $this.slider( "option" );

						$this.slider( 'values', 0, settings.min );
						$this.slider( 'values', 1, settings.max );
						$display.text( settings.min + " - " + settings.max );
						$input.val( '' );
					} );
				}
			}

			if ( $bar_form.length ) {
				$bar_form.tribe_clear_form();
			}

			params = tf.parse_string( params );

			$.each( params, function( key, value ) {
				if ( key !== 'action' ) {
					var name = decodeURIComponent( key ),
						$target;

					if ( 1 === value.length ) {
						if ( Array.isArray( value ) ) {
							value = value[0];
						}
						value = decodeURIComponent( value.replace( /\+/g, '%20' ) );

						if ( $( '[name="' + name + '"]' ).is( 'input[type="text"], input[type="hidden"]' ) ) {
							$( '[name="' + name + '"]' ).val( value );
						} else if ( $( '[name="' + name + '"][value="' + value + '"]' ).is( ':checkbox, :radio' ) ) {
							$( '[name="' + name + '"][value="' + value + '"]' ).prop( "checked", true );
						} else if ( $( '[name="' + name + '"]' ).is( 'select' ) ) {
							$( 'select[name="' + name + '"] option[value="' + value + '"]' ).attr( 'selected', true );
						}
					} else {
						for ( var i = 0; i < value.length; i++ ) {
							$target = $( '[name="' + name + '"][value="' + value[i] + '"]' );
							if ( $target.is( ':checkbox, :radio' ) ) {
								$target.prop( "checked", true );
							} else {
								$( 'select[name="' + name + '"] option[value="' + value[i] + '"]' ).attr( 'selected', true );
							}
						}
					}
				}
			} );

			if ( has_sliders ) {
				$( '#tribe_events_filters_form .ui-slider' ).each( function() {
					var s_id   = $( this ).attr( 'id' );
					var $this  = $( '#' + s_id );
					var $input = $this.prev();
					var range  = $input.val().split( '-' );

					if ( range[0] !== '' ) {
						var $display = $input.prev();

						$this.slider( "values", 0, range[0] );
						$this.slider( "values", 1, range[1] );
						$display.text( range[0] + " - " + range[1] );
						$this.slider( 'refresh' );
					}
				} );
			}

			$body.removeClass( 'tribe-reset-on' );
		},

		/**
		 * Update Display of the Current Selected Filter for a Field
		 *
		 * @since 4.5
		 *
		 * @param filter
		 * @param type
		 */
		update_current_filter: function( filter, type ) {

			var $filter = $( filter );

			$filter.closest( '.tribe_events_filter_item' ).removeClass( 'active' );

			if ( 'slider' === type ) {

				var val = $filter.slider( "values" );

				if ( val ) {
					var first = tribe_filter.currency_symbol + val[0];
					var last  = tribe_filter.currency_symbol + val[1];

					if ( tribe_filter.reverse_position ) {
						first = val[0] + tribe_filter.currency_symbol;
						last  = val[1] + tribe_filter.currency_symbol;
					}

					$filter.closest( '.tribe_events_filter_item' ).addClass( 'active' ).find( '.tribe-filter-status' ).text( first + ' - ' + last );
				}

				return;
			}

			$filter.closest( '.tribe_events_filter_item' ).find( '.tribe-filter-status' ).text( '' );

			var value                 = $filter.serialize();
			var text                  = '';
			var additional_selections = '';

			type = $filter.attr( 'type' );

			if ( $filter.data( 'select2' ) ) {

				var data = $filter.select2( 'data' );

				if ( ! data ) {
					return;
				}

				if ( data.text ) {
					$filter.closest( '.tribe_events_filter_item' ).find( '.tribe-filter-status' ).text( data.text.trim() );
				} else if ( Array.isArray( data ) ) {
					text = [];
					$( data ).each( function () {
						text.push( this.text );
					} );

					if ( 0 < text.length ) {
						additional_selections = '';
						if ( 1 < text.length ) {
							additional_selections = ' <span class="tribe-events-filter-count">+' + ( text.length - 1 ) + '</span>';
						}
						$filter.closest( '.tribe_events_filter_item' ).addClass( 'active' ).find( '.tribe-filter-status' ).text( text[0].trim()  ).append( additional_selections );
					}

				}


			} else if ( 'checkbox' === type ) {

				value = [];
				text  = [];

				$filter.closest( '.tribe_events_filter_item' ).find( "input:checked" ).each( function () {
					value.push( $( this ).val() );
					if ( 'tribe_featuredevent[]' === $( this ).attr( 'name' )  ) {
						text.push( tribe_filter.featured_active_filter );
					} else {
						text.push( $( 'label[for="' + this.id + '"] span' ).text() );
					}
				} );

				if ( text ) {
					additional_selections = '';
					if ( 1 < text.length ) {
						additional_selections = ' <span class="tribe-events-filter-count">+' + ( text.length - 1 ) + '</span>';
					}

					if ( 0 < text.length ) {
						$filter.closest( '.tribe_events_filter_item' ).addClass( 'active' ).find( '.tribe-filter-status' ).text( text[0] ).append( additional_selections );
					}

				}

			}
		},

		/**
		 * Toggle Display of Child Event Category Fields
		 *
		 * @since 4.5
		 *
		 * @param parent_id
		 * @param parent_closed
		 */
		toggle_child_fields: function ( parent_id, parent_closed ) {

			$( '.child-' + parent_id ).each( function () {
				if ( parent_closed ) {
					$( this ).slideUp();
				} else {
					$( this ).slideToggle();
				}

				var parent = tf.get_parent_cat_id( this, false );

				if ( parent_closed && parent ) {
					$( this ).toggleClass( 'closed' );
					tf.toggle_child_fields( parent, parent_closed );
				}

			} );

		},

		/**
		 * Update Category Child Term Checkboxes
		 *
		 * @since 4.5
		 *
		 * @param filter
		 */
		category_update: function ( filter ) {

			var $filter = $( filter );
			var type    = $filter.attr( 'type' );
			var parent  = '';

			if ( 'tribe_eventcategory[]' === $filter.attr( 'name' ) && 'checkbox' === type ) {

				parent = tf.get_parent_cat_id( filter, 'closest' );

				if ( parent ) {
					$filter.closest( 'li' ).removeClass( 'closed' );
					tf.toggle_child_cat_fields( parent, $filter.prop( 'checked' ) );
				}

			}
		},

		/**
		 * Toggle Child Category Field Display and Term Checks based on Parent
		 *
		 * @since 4.5
		 *
		 * @param parent_id
		 * @param is_checked
		 */
		toggle_child_cat_fields: function ( parent_id, is_checked ) {

			$( '.child-' + parent_id ).each( function () {

				if ( is_checked ) {
					$( this ).slideDown();
					$( this ).find( 'input' ).prop('checked', true);
				} else {
					$( this ).find( 'input' ).prop('checked', false);
				}

				var parent = tf.get_parent_cat_id( this, false );

				if ( parent ) {
					$( this ).removeClass( 'closed' );
					tf.toggle_child_cat_fields( parent, is_checked );
				}

			} );

		},

		/**
		 * Get the parent category id from the class
		 *
		 * @since 4.5
		 *
		 * @param filter
		 * @param is_parent
		 * @returns {string}
		 */
		get_parent_cat_id: function ( filter, type ) {

			var parent = '';

			if ( 'closest' === type ) {
				parent = $.grep( $( filter ).closest( 'li' ).attr( 'class' ).split( " " ), function ( class_name ) {
					return class_name.indexOf( 'parent-' ) === 0;
				} ).join();
			} else if ( 'parent' === type ) {
				parent = $.grep( $( filter ).parent().attr( 'class' ).split( " " ), function ( class_name ) {
					return class_name.indexOf( 'parent-' ) === 0;
				} ).join();
			} else {
				parent = $.grep( $( filter ).attr( 'class' ).split( " " ), function ( class_name ) {
					return class_name.indexOf( 'parent-' ) === 0;
				} ).join();
			}

			parent = $.grep( parent.split( "-" ), function ( id ) {
				return id > 0;
			} ).join();

			return parent;

		},

		/**
		 * Open and Close of Tribe Filter Sections
		 *
		 * @since ?
		 *
		 * @param event
		 */
		filter_section_toggle: function ( e ) {
			var $horizontal   = $( '.tribe-events-filters-horizontal' );
			var $tribe_events = $( document.getElementById( 'tribe-events' ) );
			var hover_filters = ( $tribe_events.length && $tribe_events.tribe_has_attr( 'data-hover-filters' ) && $tribe_events.data( 'hover-filters' ) === 1 ) ? true : false;

			if ( $horizontal.length && hover_filters ) {
				return;
			}

			var $parent = $( e.target ).closest( '.tribe_events_filter_item' );
			var filterId = $parent.attr( 'id' );

			if ( $horizontal.length ) {
				e.stopPropagation();
				$( '.tribe_events_filter_item' ).not( $parent ).addClass( 'closed' );
			}

			if ( $parent.hasClass( 'closed' ) ) {
				$parent.removeClass( 'closed' );
				tf.a11y_filter_toggle( e, open );
				if ( tribe_storage ) {
					tribe_storage.setItem( filterId, 'open' );
				}
			}
			else {
				$parent.addClass( 'closed' );
				tf.a11y_filter_toggle( e, closed );
				if ( tribe_storage ) {
					tribe_storage.setItem( filterId, 'closed' );
				}
			}
		},

		/**
		 * Change a11y items on filter open/close
		 *
		 * @since ?
		 *
		 * @param event
		 * @param state
		 */
		a11y_filter_toggle: function ( e, state ) {
			var $targetItem = $( e.currentTarget ) || $( e );

			var aria_expanded = ( open === state ) ? 'true' : 'false';

			$targetItem.attr( "aria-expanded", aria_expanded );
		}

	} );

	$( function() {
		// We should not run on singles. Unless we enable it for shortcodes in the future.
		if ( $( 'body' ).hasClass( 'single' ) ) {
			return;
		}

		$( '.tribe_events_filter_item' ).filter( ':last' ).addClass( 'tribe_last_child' );

		var $form         = $( document.getElementById( 'tribe_events_filters_form' ) );
		var $horizontal   = $( '.tribe-events-filters-horizontal' );
		var $tribe_events = $( document.getElementById( 'tribe-events' ) );
		var $body         = $( 'body' );
		var hover_filters = ( $tribe_events.length && $tribe_events.tribe_has_attr( 'data-hover-filters' ) && $tribe_events.data( 'hover-filters' ) === 1 ) ? true : false;
		var $event_cat    = $( document.getElementById( 'tribe_events_filter_item_eventcategory' ) );

		if ( $( '#tribe_events_filter_item_eventcategory' ).length && ts.category ) {
			ts.filter_cats = true;
		}

		function close_filters( force ) {
			if ( force || (ts.ajax_trigger === 'filters' && td.v_width < td.mobile_break) ) {
				$body
					.addClass( 'tribe-filters-closed' )
					.removeClass( 'tribe-filters-open' );

				if ( tribe_storage ) {
					tribe_storage.setItem( 'tribe_events_filters_wrapper', 'closed' );
				}

				ts.ajax_trigger = '';
			}
		}

		function open_filters() {
			$body
				.removeClass( 'tribe-filters-closed' )
				.removeClass( 'tribe-ajax-success' )
				.addClass( 'tribe-filters-open' );

			if ( tribe_storage ) {
				tribe_storage.setItem( 'tribe_events_filters_wrapper', 'open' );
			}
		}

		function toggle_filters() {
			if ( $body.is( '.tribe-filters-closed' ) ) {
				open_filters();
			}
			else {
				close_filters( true );
			}
		}

		function reset_select2() {

			$form.find( '.tribe-events-filter-select2' ).each( function() {
				var $el = $( this );
				$el.find( 'input.tribe-dropdown' ).val( null ).trigger( 'change' );
			} );

		}

		/**
		 * Remove the active filter text and reset
		 *
		 * @since 4.5
		 *
		 */
		function reset_active_filters() {

			$form.find( '.tribe_events_filter_item.active' ).each( function() {
				$( this ).removeClass( 'active' );
			} );

			$form.find( '.tribe-filter-status' ).each( function() {
				$( this ).text( '' );
			} );

		}

		if ( tribe_storage ) {

			var fb_state = tribe_storage.getItem( 'tribe_events_filters_wrapper' );

			if ( fb_state == null && $body.is( '.tribe-filters-closed' ) ) {
				fb_state = 'closed';
			}
			if ( fb_state && fb_state == 'closed' ) {
				close_filters( true );
			}
			else if ( fb_state && fb_state == 'open' ) {
				open_filters();
			}

			$( '.tribe_events_filter_item' ).each( function() {

				var $this  = $( this );
				var filterId   = $this.attr( 'id' );
				var filterStorageID = tribe_storage.getItem( filterId );

				if ( filterStorageID && filterStorageID == 'closed' ) {
					$this.addClass( 'closed' );
					tf.a11y_filter_toggle( $this.find( 'button' ), closed );
				}
			} );
		}

		$( document.getElementById( 'tribe_events_filters_wrapper' ) )
			.on( 'click', '.tribe-js-filters-reset', function( e ) {

				e.preventDefault();
				$body.addClass( 'tribe-reset-on' );

				$form.tribe_clear_form();

				if ( $form.find( '.ui-slider' ).length ) {
					$( '#tribe_events_filters_form .ui-slider' ).each( function() {

						var s_id     = $( this ).attr( 'id' );
						var $this    = $( '#' + s_id );
						var $input   = $this.prev();
						var $display = $input.prev();
						var settings = $this.slider( "option" );

						$this.slider( 'values', 0, settings.min );
						$this.slider( 'values', 1, settings.max );
						$display.text( settings.min + " - " + settings.max );
						$input.val( '' );
					} );
				}
				if ( $horizontal.length ) {
					$( '.tribe_events_filter_item' ).addClass( 'closed' );
				}

				reset_select2();
				reset_active_filters();

				$form.submit();
				$body.removeClass( 'tribe-reset-on' );
			} )
			.on( 'click', '.tribe-js-filters-toggle', function( e ) {
				e.preventDefault();
				toggle_filters();
			} );

		$body
			.on( 'click', function() {
				if ( $horizontal.length && !hover_filters ) {
					$( '.tribe_events_filter_item' ).addClass( 'closed' );
				}
			} )
			.on( 'click', '.tribe-events-filter-group, .select2-search__field', function( e ) {
				if ( $horizontal.length && !hover_filters ) {
					e.stopPropagation();
				}
			} );

		/**
		 *
		 * Allow users to switch to hover for horizontal filters. See example of usage in functions.php:
		 *
		 *   add_action( 'tribe_events_view_data_attributes', 'hover_filters' );
		 function hover_filters( $attributes ) {
                $attributes['hover-filters'] = 1;
                return $attributes;
             }
		 */

		if ( hover_filters ) {

			$form
				.on( 'mouseover', '.tribe_events_filter_item', function() {
					if ( $horizontal.length ) {
						$( this ).removeClass( 'closed' );
					}
				} )
				.on( 'mouseout', '.tribe_events_filter_item', function() {
					if ( $horizontal.length ) {
						$( '.tribe_events_filter_item' ).addClass( 'closed' );
					}
				} );
		}

		// Toggle show/hide on Filter Section by click
		$form
			.on( 'click', '.tribe-events-filters-group-heading', tf.filter_section_toggle );

		// Toggle show/hide on Filter Section by enter button
		$( 'form .tribe-events-filters-group-heading' )
			.keypress( function( e ) {
				if ( 13 === e.which ) {
					e.preventDefault();
					tf.filter_section_toggle( e );
				}
			});

		// Force-hides the filters when viewport is under mobile breakpoint (default: 767px)
		function mobile_close_filters() {
			if ( td.v_width < td.mobile_break ) {
				close_filters( true );
			}
		}

		mobile_close_filters();

		$( te )
			.on( 'ajax-success.tribe', function() {
				close_filters( false );
			} )
			.on( 'resize-complete.tribe', mobile_close_filters );


		function run_view_specific_changes() {
			if ( ts.view === 'past' || ts.view === 'list' || ts.view === 'photo' || ts.view === 'map' ) {
				ts.paged = 1;
				if ( ts.view === 'past' || ts.view === 'list' ) {
					if ( ts.filter_cats ) {
						td.cur_url = $( document.getElementById( 'tribe-events-header' ) ).attr( 'data-baseurl' );
					}
				}
			}
			else if ( ts.view === 'month' ) {
				ts.date = $( document.getElementById( 'tribe-events-header' ) ).attr( 'data-date' );
				if ( ts.filter_cats ) {
					td.cur_url = $( document.getElementById( 'tribe-events-header' ) ).attr( 'data-baseurl' );
				}
				else {
					td.cur_url = tf.url_path( document.URL );
				}

			}
			else if ( ts.view === 'week' || ts.view === 'day' ) {
				ts.date = $( document.getElementById( 'tribe-events-header' ) ).attr( 'data-date' );
			}
		}


		$form.on( 'submit', function( e ) {
			if ( tribe_events_bar_action !== 'change_view' ) {
				e.preventDefault();
				ts.popping = false;
				run_view_specific_changes();
				tf.pre_ajax( function() {
					/**
					 * DEPRECATED: tribe_ev_runAjax has been deprecated in 4.0. Use run-ajax.tribe instead
					 */
					$( te ).trigger( 'tribe_ev_runAjax' );
					$( te ).trigger( 'run-ajax.tribe' );
					ts.ajax_trigger = 'filters';
				} );
			}
		} );

		if ( tt.live_ajax() && tt.pushstate ) {

			$form.find( 'input[type="submit"]' ).remove();

			function run_filtered_ajax() {
				tf.disable_inputs( '#tribe_events_filters_form', 'input, select' );
				ts.popping = false;
				run_view_specific_changes();
				if ( ts.view === 'map' ) {
					if ( tt.pushstate ) {
						tf.pre_ajax( function() {
							/**
							 * DEPRECATED: tribe_ev_runAjax has been deprecated in 4.0. Use run-ajax.tribe instead
							 */
							$( te ).trigger( 'tribe_ev_runAjax' );
							$( te ).trigger( 'run-ajax.tribe' );
							ts.ajax_trigger = 'filters';
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
				else {
					tf.pre_ajax( function() {
						/**
						 * DEPRECATED: tribe_ev_runAjax has been deprecated in 4.0. Use run-ajax.tribe instead
						 */
						$( te ).trigger( 'tribe_ev_runAjax' );
						$( te ).trigger( 'run-ajax.tribe' );
						ts.ajax_trigger = 'filters';
					} );
				}

			}

			if ( $( document.getElementById( 'tribe_events_filter_item_eventcategory' ) ).length && ts.category ) {
				$( '#tribe_events_filter_item_eventcategory input, #tribe_events_filter_item_eventcategory select' ).on( "change", function() {
					tf.setup_ajax_timer( function() {
						run_filtered_ajax();
					} );
				} );
			}
		}

		$form
			.on( 'slidechange', '.ui-slider', function() {

				tf.update_current_filter( this, 'slider' );

				if ( tt.live_ajax() ) {
					tf.setup_ajax_timer( function() {
						run_filtered_ajax();
					} );
				}
			} )
			.on( 'change', 'input, select', function() {

				tf.category_update( this );

				tf.update_current_filter( this );

				if ( ts.filter_cats && $( this ).parents( '.tribe_events_filter_item' ).attr( 'id' ) === 'tribe_events_filter_item_eventcategory' ) {
					return;
				}

				if ( tt.live_ajax() ) {
					tf.setup_ajax_timer(function () {
						run_filtered_ajax();
					} );
				}
			} );

		$( te ).on( 'tribe_ev_collectParams', function() {
			var tribe_filter_params = tf.serialize( '#tribe_events_filters_form', 'input, select' );

			if ( tribe_filter_params.length ) {
				ts.filters = true;
				ts.params = ts.params + '&' + tribe_filter_params;
				if ( ts.view !== 'map' ) {
					if ( ts.url_params.length ) {
						ts.url_params = ts.url_params + '&' + tribe_filter_params;
					}
					else {
						ts.url_params = tribe_filter_params;
					}
				}
			}
			else {
				ts.filters = false;
			}
		} );

		$( te ).on( 'collect-params.tribe', function() {
			if ( ts.filter_cats ) {
				$( '#tribe_events_filter_item_eventcategory option:selected, #tribe_events_filter_item_eventcategory input:checked' ).remove();
			}

			var cv_filter_params = tf.serialize( '#tribe_events_filters_form', 'input, select' );
			// Make sure we always split in a string if not we create an empty array.
			var url_params = ts.url_params
				&& typeof ts.url_params === 'string'
				? ts.url_params.split( '&' )
				: [];
			// Remove duplicates values already present on the url_params
			var filter_params = cv_filter_params
				.split( '&' )
				.filter(function( param ) {
					// Filter only values that are not present on url_params
					return url_params.indexOf( param ) === -1;
				})
				.join( '&' );

			if ( ts.url_params.length && filter_params.length ) {
				ts.url_params = ts.url_params + '&' + filter_params;
			}
			else if ( filter_params.length ) {
				ts.url_params = filter_params;
			}
		} );

		if ( $event_cat.length && ts.category ) {
			$body.addClass( 'tribe-reset-on' );
			$event_cat.find( 'option[data-slug="' + ts.category + '"]' ).attr( 'selected', true );
			$event_cat.find( 'input[data-slug="' + ts.category + '"]' ).attr( "checked", "checked" );
			$body.removeClass( 'tribe-reset-on' );
		}

		// on load if there are any event categories checked slide open all terms
		if ( $event_cat.length ) {
			var terms = [];
			$event_cat.find( 'input:checked' ).each( function () {
				terms.push( $( this ).val() );
			} );
			if ( terms.length ) {
				$event_cat.find( "li" ).each( function () {
					$( this ).removeClass('closed').slideDown();
				} );
			}
		}

		/**
		 * On click of Toggle Field SlideToggle Child Fields
		 *
		 * @since 4.5
		 *
		 */
		$( document ).on( 'click', '.tribe-toggle-child', function ( e ) {

			e.preventDefault();

			var parent = tf.get_parent_cat_id( this, 'parent' );

			$( this ).parent().toggleClass( 'closed' );
			tf.toggle_child_fields( parent, $( this ).parent().hasClass( 'closed' ) );
		} );

	} );

})( jQuery, tribe_ev.data, tribe_ev.events, tribe_ev.fn, tribe_ev.state, tribe_ev.tests, tribe_debug );
