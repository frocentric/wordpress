/**
 * Community Events JavaScript
 * Linked Posts toggle
 * Event image field function
 * Events list functions
 * Invoke dative datepicker on mobile
 */
var tribe_community_events = tribe_community_events || {};

(function ( window, $, obj ) {
	'use strict';
	/**
	 * jQuery Object of the window
	 *
	 * @type {jQuery}
	 */
	obj.$window = $( window );

	/**
	 * jQuery object of the Datepicker inputs
	 *
	 * @type {jQuery}
	 */
	obj.$datepickers = $();

	/**
	 * jQuery Object of the Timepicker inputs
	 *
	 * @type {jQuery}
	 */
	obj.$timepickers = $();

	/**
	 * Some oft-cited elements in the submission and edit-event form.
	 *
	 * @since 4.5.15
	 *
	 * @type {object}
	 */
	obj.els = {
		uploadArea      : document.querySelector( '.tribe-image-upload-area' ),
		uploadFile      : document.getElementById( 'uploadFile' ),
		eventImage      : document.getElementById( 'EventImage' ),
		detachThumbnail : document.getElementById( 'tribe-events-community-detach-thumbnail' )
	};

	/**
	 * Formats a Date object into string yyyy-MM-dd
	 *
	 * @since  4.5
	 *
	 * @param  {Date}   date  Date to be formated
	 * @return {string}
	 */
	obj.date_to_ymd = function ( date ) {
		var d = date.getDate();
		var m = date.getMonth() + 1;
		var y = date.getFullYear();

		return y + '-' + ( m <= 9 ? '0' + m : m ) + '-' + ( d <= 9 ? '0' + d : d );
	}

	/**
	 * Hides the Linked Posts toggle until there is more than one post visible
	 *
	 * @since  4.5
	 *
	 * @return {void}
	 */
	obj.init_linked_posts_handle = function () {

		var handle  = $( '.move-linked-post-group' );
		var trigger = $( '.saved-linked-post' );

		handle.addClass( 'hidden' );

		trigger.on( 'change', function () {
			$( trigger ).closest( handle ).removeClass( 'hidden' );
			$( this ).find( handle ).removeClass( 'hidden' );
		} )
	};

	/**
	 * Change input type from Text to Date when viewport width is
	 * 568px or less in order to invoke the native datepicker
	 *
	 * Add `disableTouchKeyboard` option to the timepicker to prevent
	 * the native keyboard from overlaying timepicker dropdown
	 *
	 * @since  4.5
	 *
	 * @return {void}
	 */
	obj.init_mobile_datetime_input = function () {
		obj.$datepickers = $( '#EventStartDate, #EventEndDate' );
		obj.$timepickers = $( '#EventStartTime, #EventEndTime' );

		obj.$window.on( {
			'resize.tribe': function () {
				var viewport_width = obj.$window.width();

				if ( viewport_width <= 568 ) {
					obj.$datepickers.on( 'change.tribe', obj.on_change_datepicker ).trigger( 'change' );

					// Look for a Tribe fork of jQuery Timepicker to prevent conflicts; load the default if not found.
					if ( 'undefined' !== typeof $.fn.tribeTimepicker ) {
						obj.$timepickers.tribeTimepicker( 'option', 'disableTouchKeyboard', true );
					} else {
						// @deprecated 4.5.6
						obj.$timepickers.timepicker( 'option', 'disableTouchKeyboard', true );
					}
				} else {
					obj.$datepickers.prop( 'type', 'text' );
					obj.$datepickers.off( 'change.tribe' );

					// Look for a Tribe fork of jQuery Timepicker to prevent conflicts; load the default if not found.
					if ( 'undefined' !== typeof $.fn.tribeTimepicker ) {
						obj.$timepickers.tribeTimepicker( 'option', 'disableTouchKeyboard', false );
					} else {
						// @deprecated 4.5.6
						obj.$timepickers.timepicker( 'option', 'disableTouchKeyboard', false );
					}
				}
			},

			'load': function () {
				obj.$window.trigger( 'resize.tribe' );
			}
		} );
	};

	/**
	 * Applied to on `change.tribe` for mobile screens only
	 * Gets turned off when screen is bigger then
	 *
	 * @since  4.5
	 *
	 * @return {void}
	 */
	obj.on_change_datepicker = function() {
		var $field     = $( this );
		var $other     = obj.$datepickers.not( $field );
		var type       = 'start';
		var instance   = $( this ).data( 'datepicker' );

		var date       = $.datepicker.parseDate( instance.settings.dateFormat || $.datepicker._defaults.dateFormat, $field.val(), instance.settings );
		var other_date = $.datepicker.parseDate( instance.settings.dateFormat || $.datepicker._defaults.dateFormat, $other.val(), instance.settings );

		if ( 'EventEndDate' === $field.attr( 'name' ) || 'EventEndDate' === $field.attr( 'id' ) ) {
			type = 'end';
		}

		if ( 'start' === type ) {
			$other.attr( 'min', obj.date_to_ymd( date ) );
			if ( other_date < date ) {
				$other.val( obj.date_to_ymd( date ) );
			}
		} else {
			if ( other_date > date ) {
				$other.val( obj.date_to_ymd( date ) );
			}
		}
	};

	/**
	 * Makes sure we are dealing with the Columns displayed with some sort of caching for user options
	 *
	 * @since  4.5
	 *
	 * @return {void}
	 */
	obj.init_local_storage = function () {
		if ( ! window.localStorage ) {
			return;
		}

		$( '.tribe-toggle-column' ).on( 'change', function() {
			var $button = $( this );
			var id      = $button.attr( 'id' );
			var stored  = window.localStorage.getItem( id );

			if ( $button.is( ':checked' ) ) {
				window.localStorage.removeItem( id );
			} else {
				window.localStorage.setItem( id, '1' );
			}
		} ).each( function() {
			var $button = $( this );
			var id      = $button.attr( 'id' );
			var stored  = window.localStorage.getItem( id );

			if ( stored ) {
				$button.prop( 'checked', false ).trigger( 'change' );
			}
		} );
	};

	/**
	 * Event Image Field Function
	 * Adds the value of the filename from the actual input into a disabled placeholder input
	 * this is all done in the name of pretty forms and accessibility
	 *
	 * @since  4.5
	 *
	 * @return {void}
	 */
	obj.init_image_input = function () {

		var $uploadArea = $( obj.els.uploadArea );

		$( obj.els.eventImage ).on( 'change', function() {

			var fileName = this.value;
			var clean    = fileName.split( '\\' ).pop();

			obj.els.uploadArea.classList.add( 'uploaded' );

			obj.els.detachThumbnail.setAttribute( 'value', false );

			obj.els.uploadFile.setAttribute( 'value', clean );
			obj.els.uploadFile.setAttribute( 'size', clean.length );
		} );

		// Remove image on submission form.
		$uploadArea.on( 'click', '.tribe-remove-upload a', function ( e ) {
			e.preventDefault();
			obj.removeEventImage();
		} );

		// Remove image on edit form for already-submitted events, *and* unhook attached thumbnail.
		$uploadArea.on( 'click', '.submitdelete', function( e ) {
			e.preventDefault();
			obj.removeEventImage();
			obj.detachThumbnail();
		} );
	};

	/**
	 * Clears the event image from the submission form.
	 *
	 * @since 4.5.15
	 *
	 * @return {void}
	 */
	obj.removeEventImage = function() {
		obj.els.uploadArea.classList.remove( 'uploaded' );
		obj.els.uploadArea.classList.remove( 'has-image' );

		obj.els.uploadFile.setAttribute( 'value', '' );
		obj.els.uploadFile.value = '';
		obj.els.eventImage.setAttribute( 'value', '' );
		obj.els.eventImage.value = '';
	};

	/**
	 * Goes a step beyond clearing the event image from the submission form and sets up the form for
	 * unattaching the image as the event's post thumbnail.
	 *
	 * @since 4.5.15
	 *
	 * @return {void}
	 */
	obj.detachThumbnail = function() {
		obj.els.detachThumbnail.setAttribute( 'value', true );
	};

	/**
	 * Configure the My Events page Columns displayed menu toggle
	 *
	 * @since  4.5
	 *
	 * @return {void}
	 */
	obj.init_list_columns_menu = function () {
		var $display_options = $( '.table-menu' );

		$( '.table-menu-btn' ).click( function () {
			var clickedItem = $( this );
			clickedItem
				.toggleClass( 'menu-open' )
				.parent()
				.find( '.table-menu' )
				.toggleClass( 'table-menu-hidden' );
			return false;
		} );

		// assign click-away-to-close event
		$( document ).click( function ( e ) {
			if ( !$( e.target ).is( $display_options ) ) {
				if ( !$( e.target ).is( $( $display_options ).find( '*' ) ) ) {
					$( $display_options ).addClass( 'table-menu-hidden' );
				}
			}
		} );
	};

	/**
	 * Remove the no-js class from the Community Events container.
	 *
	 * @since  4.5
	 *
	 * @return {void}
	 */
	obj.remove_no_js_class = function() {
		$( document.getElementById( 'tribe-events' ) ).removeClass( 'tribe-no-js' ).addClass( 'tribe-js' );
	};

	/**
	 * Reuses the same logic used by TEC in the admin environment to prevent
	 * situations such as an event end time earlier than the event start time
	 * being set.
	 *
	 */
	obj.datetime_selectors = function() {
		if ( 'object' === typeof tribe_dynamic_helper_text ) {
			tribe_dynamic_helper_text.event_date_change();
		}
	};

	/**
	 * Checks if the specified submission form field is empty.
	 *
	 * @since 4.5.14
	 *
	 * @param {string} The Community Events submission form field name to check if empty.
	 * @param {array} The $.fn.serializeArray() array of the submission form.
	 * @return {boolean} True if the field by the given name exists and is an empty string.
	 */
	obj.is_form_field_empty = function( field_name, form_state ) {

		for ( var i = 0; i < form_state.length; i++ ) {

			// For the visual editor, we need to check tinyMCE
			// We do it first so other fields will "fall through"
			// We don't supply a name, so it defaults to the same as the supplied ID
			if ( 'undefined' !== typeof tinyMCE && tinyMCE.get( field_name ) !== null ) {
				return tinyMCE.get( field_name ).getContent() === "";
			}

			if ( field_name == form_state[ i ].name ) {
				return '' === form_state[ i ].value;
			}
		}

		return false;
	}

	/**
	 * Allow for addition of notices via JS, e.g. when form submission is prevented.
	 *
	 * @since 4.5.14
	 *
	 * @param {array} fields The name of fields
	 * @return {void}
	 */
	obj.add_js_form_errors = function( fields ) {

		var $form_wrapper = $( '#tribe-community-events' );

		// Prevent stacking/duplication of errors.
		$form_wrapper.find( '.tribe-community-js-notice' ).remove();

		var $notice = $( '<div />', {
			'class' : 'tribe-community-notice tribe-community-js-notice tribe-community-notice-error'
		} );

		$notice.insertBefore( $form_wrapper.find( 'form' ) );

		for ( var i = 0; i < fields.length; i++ ) {

			if ( undefined === tribe_submit_form_i18n.errors[ fields[i] ] ) {
				continue;
			}

			var noticeText = tribe_submit_form_i18n.errors[ fields[i] ];
			$( '<p />', { text: noticeText } ).appendTo( $notice );
		}
	}

	/**
	 * If a linked post is being created, but Event Title or Event Description are empty, prevent submission.
	 *
	 * @since 4.5.14
	 *
	 * @return {boolean} False if title or description are empty while a new linked post is being created.
	 */
	obj.disallow_submission_while_creating_linked_posts = function() {

		$( '#tribe-community-events form' ).on( 'submit', function( e ) {
			var $form        = $( this );
			var form_state   = $form.serializeArray();
			var error_fields = [];

			$form.find( '.required' ).each( function( index ) {

				if ( 'checkbox' === $( this ).attr( 'type' ) ) {
					if ( ! $( this ).prop( 'checked' ) ) {
						error_fields.push( $( this ).attr( 'name' ) );
						return true;
					}
				}

				if ( '' === $( this ).val() ) {
					error_fields.push( $( this ).attr( 'name' ) );
				}
			});

			if ( error_fields.length ) {
				obj.add_js_form_errors( error_fields );

				$( 'html, body' ).animate( {
					scrollTop: $form.closest( '#tribe-community-events' ).offset().top,
				}, 300 );

				return false;
			}

			return true;
		} );
	}

	obj.delete_post = function() {
		$( '.delete.wp-admin.events-cal .submitdelete' ).on( 'click', function(e) {
			e.preventDefault();
			if ( ! $( e.target ).data( 'event_id' ) ) {
				return false;
			}

			if ( ! confirm( "Are you sure you want to delete this event? This cannot be undone!" ) ) {
				return false;
			}

			var target = e.target;
			// do the ajax thing
			var event_id = target.dataset.event_id;
			var nonce = target.dataset.nonce;
			var $parent_row = $( target.closest( 'tr' ) );

			$.ajax({
				url: TEC.ajaxurl,
				method: 'GET',
				data: {
					action: 'tribe_events_community_delete_post',
					nonce: nonce,
					id: event_id
				},
				success: function( response ) {
					if ( true === response.success ) {
						$parent_row.fadeOut( function(){
							$parent_row.remove();
						});
					}
				},
				complete: function( response ){
					var response_data = $.parseJSON( response.responseText );
					alert( response_data.data );
				}
			});

			// don't follow link!
			return false;
		} );
	}

	// Configure all function to run when Doc Ready
	$( document )
		.ready( obj.init_mobile_datetime_input )
		.ready( obj.init_image_input )
		.ready( obj.init_list_columns_menu )
		.ready( obj.init_local_storage )
		.ready( obj.init_linked_posts_handle )
		.ready( obj.remove_no_js_class )
		.ready( obj.remove_no_js_class )
		.ready( obj.setup_dropdowns )
		.ready( obj.datetime_selectors )
		.ready( obj.disallow_submission_while_creating_linked_posts )
		.ready( obj.delete_post );

})( window, jQuery, tribe_community_events );
