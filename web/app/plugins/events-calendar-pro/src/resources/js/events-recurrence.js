var tribe_events_pro_admin = tribe_events_pro_admin || {};

tribe_events_pro_admin.recurrence = {
	recurrence_count: 0,
	exclusion_count: 0,
	event: {}
};

( function( $, my ) {
	'use strict';

	var $document = $( document );

	my.init = function() {
		this.init_recurrence();
	};

	/**
	 * initialize the recurrence behaviors and UI
	 */
	my.init_recurrence = function() {
		this.$recurrence_staging = $( '#tribe-recurrence-staging' );
		this.recurrence_tmpl     = document.getElementById( 'tmpl-tribe-recurrence' );

		if ( ! this.recurrence_tmpl ) {
			return;
		}

		this.recurrence_template = Handlebars.compile( this.recurrence_tmpl.innerHTML );
		this.$add_recurrence     = $( '#tribe-add-recurrence' );
		this.$recurrence_rules   = $( '.tribe-event-recurrence-rule' );
		this.$recurrence_row     = $( '.recurrence-row.tribe-datetime-block' ).addClass( 'tribe-recurrence-exclusion-row--idle' );

		this.$exclusion_staging  = $( '#tribe-exclusion-staging' );
		this.exclusion_tmpl      = document.getElementById( 'tmpl-tribe-exclusion' );

		this.exclusion_template  = Handlebars.compile( this.exclusion_tmpl.innerHTML );
		this.$add_exclusion      = $( '#tribe-add-exclusion' );
		this.$exclusion_rules    = $( '.tribe-event-recurrence-exclusion' );

		this.recurrence_errors = {
			days: [],
			end : [],
		};

		this.date_format = tribe_datepicker_opts.dateFormat.toUpperCase();
		this.date_format = this.date_format.replace( 'YY', 'YYYY' );
		this.populate_recurrence( tribe_events_pro_recurrence_data );

		window.Handlebars.registerHelper( {
			tribe_recurrence_select: function( value, options ) {
				var $el = $( '<select />' ).html( options.fn( this ) );

				// if a value is passed in, get rid of the defaults
				if ( value ) {
					// Since we are not dealing with DOM elements we want HTML, we need to use $.attr instead of $.prop.
					$el.find( 'option:selected' ).attr( 'selected', false );
				}

				if ( ! $.isArray( value ) ) {
					value = [ value ];
				}

				value.forEach( function( currentValue ) {
					var $option = $el.find( '[value="' + currentValue + '"]' );

					// Since we are not dealing with DOM elements we want HTML, we need to use $.attr instead of $.prop.
					$option.attr( 'selected', 'selected' );
				} );

				return $el.html();
			},

			tribe_if_in: function( value, collection, text ) {
				if ( typeof collection === 'undefined' ) {
					collection = [];
				}

				if ( typeof text === 'undefined' ) {
					text = '';
				}

				return -1 !== $.inArray( value, collection ) ? text : '';
			},

			tribe_if_not_in: function( value, collection, text ) {
				if ( typeof collection === 'undefined' ) {
					collection = [];
				}

				if ( typeof text === 'undefined' ) {
					text = '';
				}

				return -1 === $.inArray( value, collection ) ? text : '';
			},

			tribe_checked_if_is: function( value, goal ) {
				return value === goal ? 'checked' : '';
			},
			tribe_checked_if_is_not: function( value, goal ) {
				return value !== goal ? 'checked' : '';
			},
			tribe_checked_if_in: function( value, collection ) {
				return -1 !== $.inArray( value, collection ) ? 'checked' : '';
			}
		} );
	};

	my.attach_behaviors = function() {
		$( '.eventForm' )
			.on( 'submit', '.wp-admin.events-cal #post', this.event.submit_validation )
			.on( 'change', '[data-field="type"]', this.event.recurrence_type_changed )
			.on( 'change', '[data-field="end-type"]', this.event.recurrence_end_type_changed )
			.on( 'change', '[data-field="custom-month-number"]', this.event.recurrence_custom_month_changed )
			.on( 'change', '[data-field="same-time"]', this.event.same_time_changed )
			.on( 'change', '[data-field="custom-week-day"]', this.event.weekdays_changed )
			.on( 'change', '.recurrence_end_count', this.event.recurrence_end_count_changed )
			.on( 'change', '.recurrence-row, .custom-recurrence-row', this.event.recurrence_row_changed )
			.on( 'change', '#EventStartDate, #EventEndDate, #allDayCheckbox, #EventStartTime, #EventEndTime', this.event.datepicker_updated )
			.on( 'change', '#EventStartTime, #EventEndTime, #allDayCheckbox', this.setup_same_time )
			.on( 'change', '#EventStartDate', this.setup_same_day )
			.on( 'click', '#tribe-add-recurrence', this.event.add_recurrence )
			.on( 'click', '#tribe-add-exclusion', this.event.add_exclusion )
			.on( 'click', '.tribe-event-recurrence .tribe-handle, .tribe-event-exclusion .tribe-handle', this.event.toggle_rule )
			.on( 'click', '.tribe-event-recurrence .tribe-confirm-delete-this, .tribe-event-exclusion .tribe-confirm-delete-this', this.event.delete_rule );

		// bind to document so we only trigger the delete dialog if a click event propagates up to the document. The global
		// close dialog click event stops propagation at the body tag
		$( document ).on( 'click', '.tribe-event-recurrence .tribe-delete-this, .tribe-event-exclusion .tribe-delete-this', this.event.delete_rule );

		// If recurrence changes on a recurring event, then show warning
		if ( $( 'input[data-field="is_recurring"][value="true"]' ).length ) {
			$( '.eventForm' ).on( 'change', '.recurrence-row input, .custom-recurrence-row input, .recurrence-row select, .custom-recurrence-row select', this.event.recurrence_changed );
			$( '.eventForm' ).on( 'recurrenceEndChanged', '[data-field="end"]', this.event.recurrence_changed );
		}

		$( '[data-field="end"]' ).datepicker( 'option', 'onSelect', this.event.datepicker_end_date_changed );
		$( '.recurrence_end, #EventStartDate, #EventEndDate' ).datepicker( 'option', 'onClose', this.event.datepicker_updated );

		this.set_recurrence_end_min_date();
		// It's important to trigger the Buttonset after setup of a Recurrence
		this.init_buttonset();

	}

	my.init_dropdowns = function() {
		$( '.recurrence-row .tribe-dropdown' )
			.not( '.tribe-dropdown-created' )
			.tribe_dropdowns();
	};

	my.init_buttonset = function() {
		$( tribe_buttonset.selector.input ).trigger( 'change.tribe_buttonset' );
	};

	/**
	 * adds a recurrence rule to the list of available rules
	 */
	my.add_recurrence = function( data ) {
		var is_new = 'undefined' === typeof data;

		if ( 'undefined' !== typeof data && 'undefined' !== typeof data.end && data.end ) {
			data.end = moment( data.end ).format( this.date_format );
		}

		// Ensure the custom date - if set - is in the expected format
		// @todo replace this with a common helper for retrieving deeply nested values once available
		try {
			if ( data.custom.date.date ) {
				data.custom.date.date = moment( data.custom.date.date ).format( this.date_format );
			}
		} catch ( e ) {}

		this.$recurrence_staging.append( this.recurrence_template( data ) );

		var $rule = this.$recurrence_staging.find( '.tribe-event-recurrence' );

		if ( is_new ) {
			$rule.addClass( 'recurrence-new-rule' );
		}

		// replace all of the recurrence[] instances with recurrence[x] where x is a number
		$rule.find( '[name*="recurrence[rules][]"],[id*="recurrence_rule_--"],[data-input*="recurrence_rule_--"],[data-depends*="#recurrence_rule_--"]' ).each( function() {
			var $field = $( this );

			if ( $field.attr( 'name' ) ) {
				$field.attr( 'name', $field.attr( 'name' ).replace( /recurrence\[rules\]\[\]/, 'recurrence[rules][' + my.recurrence_count + ']' ) );
			}

			if ( $field.attr( 'id' ) ) {
				$field.attr( 'id', $field.attr( 'id' ).replace( /recurrence_rule_--/, 'recurrence_rule_' + my.recurrence_count ) );
			}

			if ( $field.attr( 'data-input' ) ) {
				$field
					.attr( 'data-input', $field.attr( 'data-input' ).replace( /recurrence_rule_--/, 'recurrence_rule_' + my.recurrence_count ) )
					.data( 'input', $field.attr( 'data-input' ) );
			}

			if ( $field.attr( 'data-depends' ) ) {
				$field.attr( 'data-depends', $field.attr( 'data-depends' ).replace( /#recurrence_rule_--/, '#recurrence_rule_' + my.recurrence_count ) );
			}
		} );

		if ( ! data ) {
			$rule.find( '.tribe-same-time-checkbox' ).prop( 'checked', true );
		}

		$rule.find( '.tribe-datepicker' ).datepicker( tribe_datepicker_opts );
		$rule.insertBefore( this.$recurrence_staging );
		this.set_recurrence_end_min_date();

		this.set_recurrence_data_attributes( $rule );
		this.maybe_relocate_end_date( $rule );
		this.adjust_rule_helper_text( $rule );
		this.update_rule_recurrence_text( $rule );

		// re-initialize recurrence rules
		this.$recurrence_rules = this.$recurrence_rules.add( $rule );
		this.recurrence_count++;

		this.check_for_useful_rule();
		this.setup_intervals( $rule );
		this.setup_same_time();
		this.setup_same_day();

		this.setup_yearly_select( $rule );
		this.setup_weekly_select( $rule );

		this.init_dropdowns();

		if ( 'undefined' === typeof data ) {
			this.toggle_rule( $rule );
		}

		// check active recurrence input to use for dependencies
		$( '#tribe-recurrence-active.inactive' ).trigger( 'click' ).prop( 'checked', true ).removeClass( 'inactive' );
	};

	my.setup_weekly_select = function ( $rule ) {
		// default the weekly rule to pre-check the day of the week for the current event
		var start_date = $( document.getElementById( 'EventStartDate' ) ).val(),
			default_day_of_week = moment( start_date, this.date_format ).isoWeekday(),
			$days = $rule.find( '[data-field="custom-week-day"]' );

		// If any are selected bail
		if ( $days.filter( ':checked' ).length > 0 ) {
			return;
		}

		$rule.find( '[data-field="custom-week-day"][value="' + default_day_of_week + '"]' ).each(function ()  {
			$( this ).parent().trigger( 'click' );
		});
	};

	/**
	 * Sets up the checkboxes for months-in-a-year
	 *
	 * @param $rule Recurrence rule
	 */
	my.setup_yearly_select = function( $rule ) {
		var $start_date = $( document.getElementById( 'EventStartDate' ) );
		var format = tribe_dynamic_help_text.datepicker_format;

		format = format.replace( 'm', 'MM' ).replace( 'd', 'DD' ).replace( 'Y', 'YYYY' );

		var start_month = moment( $start_date.val(), format ).format( 'M' );
		var $select     = $rule.find( '[data-field="custom-year-month"]' );

		// When you already have content we bail
		if ( $select.val() ) {
			return;
		}

		// Change the Value
		$select.val( start_month );
	};

	/**
	 * Sets up the interval select box
	 *
	 * @param $rule Recurrence rule
	 */
	my.setup_intervals = function( $rule ) {
		var type = $rule.find( '[data-field="type"]' ).val();
		var $interval = $rule.find( '.tribe-recurrence-rule-interval' );
		var interval = $interval.get(0);

		if ( interval.created ) {
			return;
		}

		var i = 1;
		var num = 6;
		var autocomplete_options = [];

		if ( 'Daily' === type ) {
			num = 6;
		} else if ( 'Weekly' === type ) {
			num = 6;
		} else if ( 'Monthly' === type ) {
			num = 12;
		} else if ( 'Yearly' === type ) {
			num = 3;
		}

		for ( i = 1; i <= num; i++ ) {
			autocomplete_options.push( { id: i, text: '' + i } );
		}

		$interval
			.select2()
			.select2( 'destroy' )
			.removeClass( 'select2-offscreen' )
			.data( 'options', autocomplete_options );

		interval.created = true;
	};

	/**
	 * sets up the section that indicates if the recurrence rule uses the same day as the main event or not
	 */
	my.setup_same_day = function() {
		$( '.tribe-event-recurrence, .tribe-event-exclusion' ).each( function() {
			var $rule = $( this );
			var $same_day_text = $rule.find( '.recurrence-same-day-text' );

			var $start_date = $( document.getElementById( 'EventStartDate' ) );
			var format      = my.convert_date_format_php_to_moment( tribe_dynamic_help_text.datepicker_format );
			var start_day   = moment( $start_date.val(), format ).format( 'D' );

			$same_day_text.html( tribe_events_pro_recurrence_strings.recurrence['same-day-month-' + start_day] );
		} );
	};

	/**
	 * sets up the section that indicates if the recurrence rule uses the same time as the main event or not
	 */
	my.setup_same_time = function() {
		$( '.tribe-event-recurrence, .tribe-event-exclusion' ).each( function() {
			var $rule = $( this );
			var $same_time_text = $rule.find( '.recurrence-same-time-text' );
			var same_time_text = '';

			if ( $( '#allDayCheckbox:checked' ).length ) {
				same_time_text = tribe_events_pro_recurrence_strings.recurrence['same-time-text-same-all-day' ];
			} else {
				var $start_time = $( document.getElementById( 'EventStartTime' ) );
				var $end_time   = $( document.getElementById( 'EventEndTime' ) );
				same_time_text  = tribe_events_pro_recurrence_strings.recurrence['same-time-text-same' ].replace( '%1$s', $start_time.val() ).replace( '%2$s', $end_time.val() );
			}

			$same_time_text.html( same_time_text );

			var $timepickers = $rule.find( '.tribe-timepicker:not(.ui-timepicker-input)' );
			tribe_timepickers.setup_timepickers( $timepickers );
		} );
	};

	/**
	 * Optionally relocates the end date. "Date" recurrence rules need the end
	 * date near the top of the form. Otherwise, it should be at the bottom.
	 *
	 * @param $rule
	 */
	my.maybe_relocate_end_date = function( $rule ) {
		var $custom_container = $rule.find( '.recurrence-custom-container' );
		var $end_container = $rule.find( '.recurrence-end-container' );
		var $end = $rule.find( '.recurrence_end' );
		var type = $rule.find( '[data-field="type"]' ).data( 'value' );

		if ( 'Date' === type ) {
			$custom_container.append( $end );
		} else {
			$end_container.append( $end );
		}
	};

	/**
	 * checks for a useful rule and adds a class to the containing eventtable
	 */
	my.check_for_useful_rule = function() {
		var $rule = this.$recurrence_rules.filter( ':first' );
		var rule_set = false;

		this.$recurrence_rules.find( '[data-field="type"]' ).each( function() {
			var $el = $( this );
			if ( 'None' !== $el.data( 'value' ) ) {
				rule_set = true;
			}
		} );

		if ( rule_set ) {
			$rule.closest( 'table' ).addClass( 'tribe-has-recurrence-rule' );
		} else {
			$rule.closest( 'table' ).removeClass( 'tribe-has-recurrence-rule' );
		}
	};

	/**
	 * adds an exclusion rule to the list of available rules
	 */
	my.add_exclusion = function( data ) {
		var is_new = 'undefined' === typeof data;

		if ( 'undefined' !== typeof data && 'undefined' !== typeof data.end && data.end ) {
			var date_format = tribe_datepicker_opts.dateFormat.toUpperCase().replace( 'YY', 'YYYY' );
			data.end = moment( data.end ).format( date_format );
		}

		this.$exclusion_staging.append( this.exclusion_template( data ) );

		var $rule = this.$exclusion_staging.find( '.tribe-event-exclusion' );

		if ( is_new ) {
			$rule.addClass( 'exclusion-new-rule' );
		}

		// replace all of the exclusion[] instances with exclusion[x] where x is a number
		$rule.find( '[name*="recurrence[exclusions][]"],[id*="exclusion_rule_--"],[data-input*="exclusion_rule_--"],[data-depends*="#exclusion_rule_--"]' ).each( function() {
			var $field = $( this );

			if ( $field.attr( 'name' ) ) {
				$field.attr( 'name', $field.attr( 'name' ).replace( /recurrence\[exclusions\]\[\]/, 'recurrence[exclusions][' + my.exclusion_count + ']' ) );
			}

			if ( $field.attr( 'id' ) ) {
				$field.attr( 'id', $field.attr( 'id' ).replace( /exclusion_rule_--/, 'exclusion_rule_' + my.exclusion_count ) );
			}

			if ( $field.attr( 'data-input' ) ) {
				$field
					.attr( 'data-input', $field.attr( 'data-input' ).replace( /exclusion_rule_--/, 'exclusion_rule_' + my.exclusion_count ) )
					.data( 'input', $field.attr( 'data-input' ) );
			}

			if ( $field.attr( 'data-depends' ) ) {
				$field.attr( 'data-depends', $field.attr( 'data-depends' ).replace( /#exclusion_rule_--/, '#exclusion_rule_' + my.exclusion_count ) );
			}
		} );

		if ( ! data ) {
			$rule.find( '.tribe-same-time-checkbox' ).prop( 'checked', true );
		}

		$rule.find( '.tribe-datepicker' ).datepicker( tribe_datepicker_opts );
		$rule.insertBefore( this.$exclusion_staging );
		this.set_recurrence_end_min_date();

		this.set_recurrence_data_attributes( $rule );
		this.maybe_relocate_end_date( $rule );
		this.adjust_rule_helper_text( $rule );
		this.update_rule_recurrence_text( $rule );

		// re-initialize exclusion rules
		this.$exclusion_rules = this.$exclusion_rules.add( $rule );
		this.exclusion_count++;

		this.check_for_useful_rule();
		this.setup_intervals( $rule );
		this.setup_same_time();
		this.setup_same_day();

		this.setup_yearly_select( $rule );
		this.setup_weekly_select( $rule );

		this.init_dropdowns();

		if ( 'undefined' === typeof data ) {
			this.toggle_rule( $rule );
		}
	};

	/**
	 * populate recurrence UI based on recurrence data
	 */
	my.populate_recurrence = function( data ) {
		var i = 0;
		my.operations_completed = 0;
		my.total_operations = 0;

		// if there aren't any rules defined, don't bother trying to populate recurrence until
		// a rule is added
		if ( 'undefined' === typeof data.rules || ! data.rules.length ) {
			my.populate_completed();
			return;
		}

		my.total_operations = 1;
		my.async_operation( function( item, next ) {
			my.add_recurrence( item );
			next();
		}, data.rules, function() {
			my.operations_completed += 1;
			my.populate_completed()
		});

		if ( 'undefined' !== typeof data.exclusions && data.exclusions.length ) {
			my.total_operations += 1;
			my.async_operation( function( item, next ) {
				my.add_exclusion( item );
				next();
			}, data.exclusions, function() {
				my.operations_completed += 1;
				my.populate_completed();
			} );
		}
	};

	/**
	 * Function executed once all the exclusion has been added into the UI thred.
	 *
	 * @since 4.4.23
	 */
	my.populate_completed = function() {
		if ( my.operations_completed === my.total_operations ) {
			setTimeout( function() {
				my.attach_behaviors();
				$document.trigger( 'setup.dependency' );
				my.$recurrence_row.removeClass( 'tribe-recurrence-exclusion-row--idle' );
			} );
		}
	}

	/**
	 * Executes a function out of the UI thred to avoid block on loading.
	 *
	 * @param {Function} callback Function executed on each iteration
	 * @param {Array} collection Collection of items from where iterate
	 * @param {Function} done Function called  when all the iterations has been completed.
	 * @param {int} index The index of the current iteration.
	 *
	 * @since 4.4.23
	 */
	my.async_operation = function( callback, collection, done, index ) {
		var i = index || 0;
		if ( i >= collection.length ) {
			setTimeout( done );
			return;
		} else {
			setTimeout( function() {
				callback( collection[i], function() {
					my.async_operation( callback, collection, done, i + 1 );
				} );
			} );
		}
	};

	/**
	 * Adjust the Custom frequency helper text
	 */
	my.adjust_rule_helper_text = function( $rule ) {
		var $custom_type_option = $rule.find( '[data-field="custom-type"] option:selected' );
		$rule.find( '.recurrence-interval-type' ).text( $custom_type_option.data( 'plural' ) );
		$rule.find( '[data-field="custom-type-text"]' ).val( $custom_type_option.data( 'plural' ) );
	};

	/**
	 * checks the current state of fields and sets appropriate data attributes for them
	 * on the recurrence rule
	 */
	my.set_recurrence_data_attributes = function( $rules ) {
		var $rules_to_set = $rules || this.$recurrence_rules;

		$rules_to_set.each( function() {
			var $rule = $( this );
			var $field = null;
			var fields = [
				'is_recurring',
				'type',
				'end-type',
				'custom-type',
				'custom-month-number'
			];

			for ( var i in fields ) {
				$field = $rule.find( '[data-field="' + fields[ i ] + '"]' );

				if ( 'custom-month-number' === fields[ i ] ) {
					$rule.attr( 'data-recurrence-' + fields[ i ], isNaN( $field.val() ) ? 'string' : 'numeric' );
				} else if ( 'custom-month-number' === fields[ i ] ) {
					$rule.attr( 'data-recurrence-' + fields[ i ], $field.is( ':checked' ) ? 'yes' : 'no' );
				} else {
					$rule.attr( 'data-recurrence-' + fields[ i ], $field.val() );
				}
			}

			var $custom_type = $rule.find( '[data-field="custom-type"]' );
			var type = null;

			switch ( $custom_type.val() ) {
				case 'Weekly':
					type = 'week';
					break;
				case 'Monthly':
					type = 'month';
					break;
				case 'Yearly':
					type = 'year';
					break;
				case 'Daily':
				default:
					type = 'day';
					break;
			}

			var $same_time = $rule.find( '[data-field="custom-' + type + '-same-time"]' );
			$rule.attr( 'data-recurrence-same-time', $same_time.filter( ':checked' ).length ? 'yes' : 'no' );
		} );
	};

	/**
	 * Sets the min date for recurrence rules
	 */
	my.set_recurrence_end_min_date = function() {
		var start = $( '#EventStartDate' ).val();
		if ( '' === start ) {
			return;
		}

		$( '.recurrence_end' ).attr( 'placeholder', start ).datepicker( 'option', 'minDate', start );
	};

	/**
	 * returns whether or not the recurrence rules have valid recurrence days
	 */
	my.has_valid_recurrence_days = function() {
		var valid = true;

		this.$recurrence_rules.each( function( index ) {
			if ( ! my.has_valid_recurrence_days_for_rule( $( this ) ) ) {
				valid = false;
				my.recurrence_errors.days.push( index );
			}
		} );

		return valid;
	};

	/**
	 * returns whether or not a specific recurrence rule has valid recurrence days
	 */
	my.has_valid_recurrence_days_for_rule = function( $recurrence_rule ) {
		var $interval = $recurrence_rule.find( '[data-field="custom-interval"]' );
		var $recurrence_type = $recurrence_rule.find( '[data-field="type"]' );

		if ( $interval.val() !== parseInt( $interval.val(), 10 ) && 'Custom' === $recurrence_type.val() ) {
			return false;
		}

		return true;
	};

	/**
	 * returns whether or not the recurrence rules have valid recurrence ends
	 */
	my.has_valid_recurrence_ends = function() {
		var valid = true;

		this.$recurrence_rules.each( function( index ) {
			if ( ! my.has_valid_recurrence_end_for_rule( $( this ) ) ) {
				valid = false;
				my.recurrence_errors.end.push( index );
			}
		} );

		return valid;
	};

	/**
	 * returns whether or not a specific recurrence rule has valid recurrence days
	 */
	my.has_valid_recurrence_end_for_rule = function( $recurrence_rule ) {
		var $end = $recurrence_rule.find( '[data-field="end"]' );
		var $end_type = $recurrence_rule.find( '[data-field="end-type"]' );
		var $recurrence_type = $recurrence_rule.find( '[data-field="type"]' );

		if ( 'None' !== $recurrence_type.val() && 'On' === $end_type.val() ) {
			return $end.val() && ! $end.hasClass( 'placeholder' );
		}

		return true;
	};

	/**
	 * resets the post submission button state
	 */
	my.reset_submit_button = function() {
		$( '#publishing-action .button-primary-disabled' ).removeClass( 'button-primary-disabled' );
		$( '#publishing-action .spinner' ).hide();
	};

	/**
	 * validates the recurrence data
	 */
	my.validate_recurrence = function() {
		var valid = true;

		if ( ! this.has_valid_recurrence_days() ) {
			valid = false;

			alert( $( '.rec-days-error:first' ).text() );

			$( '.rec-days-error' ).each( function( index ) {
				if ( $.inArray( index, my.recurrence_errors.days ) ) {
					$( this ).show();
				}
			} );
		}

		if ( ! this.has_valid_recurrence_ends() ) {
			valid = false;

			alert( $( '.rec-end-error:first' ).text() );

			$( '.rec-end-error' ).each( function( index ) {
				if ( $.inArray( index, my.recurrence_errors.end ) ) {
					$( this ).show();
				}
			} );
		}

		return valid;
	};

	/**
	 * Toggles a recurrence rule open/closed
	 */
	my.toggle_rule = function( $rule, state ) {
		if ( 'undefined' !== state && state ) {
			if ( 'open' === state ) {
				$rule.addClass( 'tribe-open' );
			} else {
				$rule.removeClass( 'tribe-open' );
			}
		} else {
			$rule.toggleClass( 'tribe-open' );
		}
	};

	/**
	 * Updates recurrence text
	 */
	my.update_recurrence_text = function() {
		this.$recurrence_rules.each( function() {
			my.update_rule_recurrence_text( $( this ) );
		} );

		this.$exclusion_rules.each( function() {
			my.update_rule_recurrence_text( $( this ) );
		} );
	};

	/**
	 * Converts a PHP date format to Moment JS date format
	 */
	my.convert_date_format_php_to_moment = function( format ) {
		// this format conversion is pretty fragile, but the best we can do at the moment
		return format
			.replace( 'S', 'o' )
			.replace( 'j', 'D' )
			.replace( 'F', 'MMMM' )
			.replace( 'Y', 'YYYY' )
			.replace( 'm', 'MM' )
			.replace( 'd', 'DD' );
	};

	my.update_rule_recurrence_text = function( $rule ) {
		var type      = $rule.find( '[data-field="type"]' ).val();
		var end_type  = $rule.find( '[data-field="end-type"] option:selected' ).val();
		var end_count = $rule.find( '[data-field="end-count"]' ).val();
		var same_time = 'yes' === $rule.find( '[data-field="same-time"]' ).val();
		var interval  = $rule.find( '[data-field="custom-interval"]' ).val();
		var allday    = $( document.getElementById( 'allDayCheckbox' ) ).prop( 'checked' );

		if ( ! end_count ) {
			end_count = 0;
		}

		type     = type.toLowerCase().replace( ' ', '-' );
		end_type = end_type.toLowerCase().replace( ' ', '-' );

		if ( 'none' === type || '' === type ) {
			$rule.find( '.tribe-event-recurrence-description' ).html( '' );
			return;
		}

		var $event_form = $rule.closest( '.eventForm' );

		var date_format = this.date_format + ' hh:mm a';

		var $start_date = $( document.getElementById( 'EventStartDate' ) );
		var start_date  = $start_date.val();

		var $start_time = $( document.getElementById( 'EventStartTime' ) );
		var start_time  = $start_time.val().toUpperCase();
		start_date     += ' ' + start_time;

		var $end_date   = $( document.getElementById( 'EventEndDate' ) );
		var end_date    = $end_date.val();
		var $selected_end_meridian = $event_form.find( '[name="EventEndMeridian"] option:selected' );

		var $end_time   = $( document.getElementById( 'EventEndTime' ) );
		var end_time    = $end_time.val().toUpperCase();
		end_date       += ' ' + end_time;

		var start_moment  = moment( start_date, date_format );
		var end_moment    = moment( end_date, date_format );
		var single_moment = start_moment;

		// The specific start time for this rule depends on whether or not it takes
		// place at the same time as the parent event
		var instance_start_time = same_time ? start_time : $rule.find( '.tribe-field-start_time' ).val();

		var days_of_week = null;
		var month_names  = null;
		var month_day_description = tribe_events_pro_recurrence_strings.date.day_placeholder;
		var day_number   = start_moment.format( 'D' );

		var key = type;

		// @todo: double check the end of day cut off setting
		if ( allday && ! start_moment.isSame( end_moment, 'day' ) ) {
			key += '-multi';
		} else if ( allday && same_time ) {
			key += '-allday';
		}

		if ( 'weekly' === type ) {
			var weekdays = [];
			$rule.find( '[data-field="custom-week-day"]:checked' ).each( function() {
				weekdays.push( tribe_events_pro_recurrence_strings.date.weekdays[ parseInt( $( this ).val(), 10 ) - 1 ] );
			} );

			if ( 0 === weekdays.length ) {
				days_of_week = tribe_events_pro_recurrence_strings.date.day_placeholder;
			} else if ( 2 === weekdays.length ) {
				days_of_week = weekdays.join( ' ' + tribe_events_pro_recurrence_strings.date.collection_joiner + ' ' );
			} else {
				days_of_week = weekdays.join( ', ' );
				days_of_week = days_of_week.replace( /(.*),/, '$1, ' + tribe_events_pro_recurrence_strings.date.collection_joiner );
			}
		} else if ( 'monthly' === type ) {
			var same_day     = $rule.find( '[data-field="month-same-day"] option:selected' ).val();
			var month_number = $rule.find( '[data-field="custom-month-number"] option:selected' ).val();
			var month_day    = $rule.find( '[data-field="custom-month-day"] option:selected' ).val();

			if ( 'yes' === same_day || ! isNaN( month_number ) ) {
				month_number = day_number;
				key += '-numeric';
			} else {
				day_number = month_number;

				month_day_description = tribe_events_pro_recurrence_strings.date[ month_number.toLowerCase() + '_x' ];
				month_day_description = month_day_description.replace( '%1$s', tribe_events_pro_recurrence_strings.date.weekdays[ parseInt( month_day, 10 ) - 1 ] );
			}
		} else if ( 'yearly' === type ) {
			var same_day     = $rule.find( '[data-field="year-same-day"] option:selected' ).val();
			var month_number = $rule.find( '[data-field="custom-year-month-number"]' ).val();
			var month_day    = $rule.find( '[data-field="custom-year-month-day"]' ).val();

			var months       = [];
			var month_values = $rule.find( '[data-field="custom-year-month"]' ).val();

			if ( month_values ) {
				if ( _.isString( month_values ) ) {
					month_values = month_values.split(',');
				}

				$.each( month_values, function ( i, month ) {
					months.push( tribe_events_pro_recurrence_strings.date.months[ parseInt( month, 10 ) - 1 ] );
				} );
			}

			// build a string of month names
			if ( 0 === months.length ) {
				month_names = tribe_events_pro_recurrence_strings.date.month_placeholder;
			} else if ( 2 === months.length ) {
				month_names = months.join( ' ' + tribe_events_pro_recurrence_strings.date.collection_joiner + ' ' );
			} else {
				month_names = months.join( ', ' );
				month_names = month_names.replace( /(.*),/, '$1, ' + tribe_events_pro_recurrence_strings.date.collection_joiner );
			}

			if ( 'yes' === same_day || ! isNaN( month_number ) ) {
				month_number = day_number;
				key += '-numeric';
			}  else {
				day_number = month_number;

				month_day_description = tribe_events_pro_recurrence_strings.date[ month_number.toLowerCase() + '_x' ];
				month_day_description = month_day_description.replace( '%1$s', tribe_events_pro_recurrence_strings.date.weekdays[ parseInt( month_day, 10 ) - 1 ] );
			}

		} else if ( 'date' === type ) {
			var single_date = $rule.find( 'input.tribe-datepicker' ).val();

			// If the date for this rule has not yet been set, clean the description
			if ( ! single_date ) {
				$rule.find( '.tribe-event-recurrence-description' ).html( '' );
				return;
			}

			var single_moment = moment( single_date, date_format );
		}

		// For single date rules, 'after' or 'never' will not be required
		if ( 'date' === type ) {
			key += '-on';
		} else if ( 'after' === end_type ) {
			key += '-after';
		} else if ( 'never' === end_type ) {
			key += '-never';
		} else {
			key += '-on';
		}

		// If an allday event is not taking place at the same time as the initial event indicate this
		if ( allday && ! same_time ) {
			key += '-at';
		}

		// "*-at" descriptions aren't defined or required in all cases, so fallback to the closest
		// alternative when necessary (ie, if 'date-on-at' isn't available, use 'date-on' instead)
		if (
			'undefined' === typeof tribe_events_pro_recurrence_strings.recurrence[ key ]
			&& key.endsWith( '-at' )
		) {
			key = key.replace( /-at$/, '' );
		}

		var text = tribe_events_pro_recurrence_strings.recurrence[ key ];

		if ( 'undefined' === typeof text ) {
			$rule.find( '.tribe-event-recurrence-description' ).html( '' );
			return;
		}

		var end = $rule.find( '[data-field="end"]' ).val();
		if ( ! end ) {
			end = $rule.find( '[data-field="end"]' ).attr( 'placeholder' );
		}

		var series_end_moment = moment( end, date_format );
		var display_format = this.convert_date_format_php_to_moment( tribe_dynamic_help_text.date_with_year );

		text = text.replace( '[count]', end_count );
		text = text.replace( '[day_number]', day_number );
		text = text.replace( '[days_of_week]', days_of_week );
		text = text.replace( '[month_day_description]', month_day_description );
		text = text.replace( '[month_names]', month_names );
		text = text.replace( '[month_number]', month_number );
		text = text.replace( '[interval]', interval );
		text = text.replace( '[series_end_date]', series_end_moment.format( display_format ) );
		text = text.replace( '[start_date]', start_moment.format( display_format ) );
		text = text.replace( '[start_time]', instance_start_time );
		text = text.replace( '[single_date]', single_moment.format( display_format ) );

		// English-only simplification
		text = text.replace( '1 day(s)', tribe_events_pro_recurrence_strings.date.time_spans.day );
		text = text.replace( '1 week(s)', tribe_events_pro_recurrence_strings.date.time_spans.week );
		text = text.replace( '1 month(s)', tribe_events_pro_recurrence_strings.date.time_spans.month );
		text = text.replace( '1 year(s)', tribe_events_pro_recurrence_strings.date.time_spans.year );
		text = text.replace( 'day(s)', tribe_events_pro_recurrence_strings.date.time_spans.days );
		text = text.replace( 'week(s)', tribe_events_pro_recurrence_strings.date.time_spans.weeks );
		text = text.replace( 'month(s)', tribe_events_pro_recurrence_strings.date.time_spans.months );
		text = text.replace( 'year(s)', tribe_events_pro_recurrence_strings.date.time_spans.years );

		$rule.find( '.tribe-event-recurrence-description' ).html( text );
	};

	my.event.add_recurrence = function( e ) {
		e.preventDefault();
		my.add_recurrence();
	};

	my.event.add_exclusion = function( e ) {
		e.preventDefault();
		my.add_exclusion();
	};

	/**
	 * Handles when a recurrence type changes
	 */
	my.event.recurrence_type_changed = function() {
		var $el   = $( this );
		var $rule = $el.closest( '.tribe-event-recurrence, .tribe-event-exclusion' );

		var val = $el.find( 'option:selected' ).val();

		var $count_text = $rule.find( '.occurence-count-text' );
		var end_count   = parseInt( $rule.find( '.recurrence_end_count' ).val(), 10 );
		var type_text   = $el.data( 'plural' );

		// clean the input from other characters and set the int value
		$rule.find( '.recurrence_end_count' ).val( end_count );

		// prevent the end_count to be empty or a non positive int
		if ( isNaN( end_count ) || ( 0 >= end_count ) ) {
			$rule.find( '.recurrence_end_count' ).val( 1 );
			end_count = 1;
		}

		if ( 1 === end_count ) {
			type_text = $el.data( 'single' );
		}

		$count_text.text( type_text );
		$rule.find( '[data-field="occurrence-count-text"]' ).val( $count_text.text() );

		my.set_recurrence_data_attributes( $rule );
		my.maybe_relocate_end_date( $rule );
		my.check_for_useful_rule();

		my.setup_intervals( $rule );
		my.setup_yearly_select( $rule );
		my.setup_weekly_select( $rule );

		my.init_dropdowns();

		var $container = $el.closest( '.tribe-buttonset' ).parent();
		$container.find( '.tribe-dependent:not(.tribe-dependency)' ).dependency();

		my.toggle_rule( $rule, 'open' );
	};

	/**
	 * Handles when a recurrence end type changes
	 */
	my.event.recurrence_end_type_changed = function() {
		var $el   = $( this );
		var $rule = $el.closest( '.tribe-event-recurrence' );

		my.set_recurrence_data_attributes( $rule );
	};

	/**
	 * When a recurrence row changes, make sure the recurrence changed row is displayed
	 */
	my.event.recurrence_changed = function() {
		var $el   = $( this );
		var $rule = $el.closest( '.tribe-event-recurrence, .tribe-event-exclusion' );
		$rule.attr( 'data-recurrence-changed', 'yes' );
		my.toggle_rule( $rule, 'open' );
	};

	/**
	 * Handles when the recurrence end count changes
	 */
	my.event.recurrence_end_count_changed = function() {
		var $el   = $( this );
		var $rule = $el.closest( '.tribe-event-recurrence, .tribe-event-exclusion' );

		$rule.find( '[data-field="type"]' ).change();
	};

	/**
	 * Handles the changing of custom month numbers
	 */
	my.event.recurrence_custom_month_changed = function() {
		var $el   = $( this );
		var $rule = $el.closest( '.tribe-event-recurrence' );

		my.set_recurrence_data_attributes( $rule );
	};

	/**
	 * validates the recurrence data before submission occurs
	 */
	my.event.submit_validation = function() {
		if ( ! tribe_events_pro_admin.validate_recurrence() ) {
			e.preventDefault();
			e.stopPropagation();
			tribe_events_pro_admin.reset_submit_button();
			return false;
		}
	};

	my.event.datepicker_updated = function() {
		tribe_events_pro_admin.recurrence.update_recurrence_text();
		tribe_events_pro_admin.recurrence.set_recurrence_end_min_date();
	};

	my.event.datepicker_end_date_changed = function() {
		$( this ).removeClass( 'placeholder' );

		$( this ).trigger( 'recurrence-end-changed.tribe' );
	};

	my.event.recurrence_row_changed = function() {
		var $row = $( '.recurrence-pattern-description-row' );
		if ( ! $row.is( ':visible' ) ) {
			$row.show();
		}
		tribe_events_pro_admin.recurrence.update_recurrence_text();
	};

	/**
	 * handles when the "Same Time" select box is toggled
	 */
	my.event.same_time_changed = function() {
		var $el   = $( this );
		var $rule = $el.closest( '.tribe-event-recurrence, .tribe-event-exclusion' );

		my.set_recurrence_data_attributes( $rule );
	};

	/**
	 * Handles when Week Days changed
	 */
	my.event.weekdays_changed = function() {
		var $el   = $( this );
		var $rule = $el.closest( '.tribe-event-recurrence, .tribe-event-exclusion' );

		my.set_recurrence_data_attributes( $rule );
		my.update_rule_recurrence_text( $rule );
	};

	/**
	 * Toggles a rule open/closed
	 */
	my.event.toggle_rule = function() {
		var $el = $( this );

		my.toggle_rule( $el.closest( '.tribe-event-recurrence, .tribe-event-exclusion' ) );
	};

	my.event.delete_rule = function ( event ) {
		event.preventDefault();

		var $this       = $( this ),
			$rule       = $this.closest( '.tribe-event-recurrence, .tribe-event-exclusion' ),
			delete_text = tribe_events_pro_recurrence_strings.recurrence['delete-confirm'],
			cancel_text = tribe_events_pro_recurrence_strings.recurrence['delete-cancel'],
			buttons = {
				'delete': {
					text: delete_text,
					click: function () {
						$( this ).dialog( 'close' );
						$rule.fadeOut( 500, function () {
							$rule.remove();
							tribe_events_pro_admin.recurrence.update_recurrence_text();
						} );
					}
				},
				'cancel': {
					text: cancel_text,
					click: function () {
						my.event.close_delete_confirmation_dialog( $( this ) );
					}
				}
			},
			$dialog = $( '#tribe-row-delete-dialog' );

		if ( $rule.is( '.tribe-event-recurrence' ) ) {
			$dialog.addClass( 'rule-dialog' ).removeClass( 'exclusion-dialog' );
		} else {
			$dialog.addClass( 'exclusion-dialog' ).removeClass( 'rule-dialog' );
		}

		// if there's a dialog that is already open, close it before opening a new one
		if ( $( '.delete-dialog-open' ).length ) {
			my.event.close_delete_confirmation_dialog( $dialog );
		}

		$dialog.dialog( {
			dialogClass   : 'tribe-row-delete-dialog',
			closeOnEscape : true,
			resizable     : false,
			height        : $rule[0].offsetHeight,
			width         : $rule[0].offsetWidth,
			buttons       : buttons,
			position      : { my: 'center', at: 'center', of: $rule },
			open: function () {
				var $dialog  = $( '.tribe-row-delete-dialog' );
				var $buttons = $dialog.find( '.ui-dialog-buttonset button' );
				var $content = $dialog.find( '.ui-dialog-content' );

				$rule.fadeTo( 100, 0.1 );

				// make sure the content area is sized appropriately so we don't take up too much space
				$content.css( {
					'height'    : 'auto',
					'min-height': '1.5rem'
				} );

				// set our own button classes to replace the default ones
				$( $buttons[0] ).removeClass().addClass( 'button button-red' );
				$( $buttons[1] ).removeClass().addClass( 'button button-secondary' );

				// track the a delete dialog has been opened
				$rule.addClass( 'delete-dialog-open' );

				// make sure the dialogs close when clicked off of the dialog or if the window resizes
				$( 'body' ).on( 'click', my.event.close_dialogs );
				$( window ).on( 'resize', my.event.close_dialogs );
			},
			close: function() {
				// remove our tracking class
				$rule.removeClass( 'delete-dialog-open' );

				// fade the rule back to 100% opacity
				$rule.fadeTo( 100, 1 );

				// remove the click/resize events that cause dialogs to close
				$( 'body' ).off( 'click', my.event.close_dialogs );
				$( window ).off( 'resize', my.event.close_dialogs );
			}
		} );
	};

	my.event.close_delete_confirmation_dialog = function ( $dialog ) {
		if ( $dialog && $dialog.dialog( 'isOpen' ) ) {
			$dialog.dialog( 'close' );
		}
	};

	my.event.close_dialogs = function ( ev ) {
		var $dialog = $( '#tribe-row-delete-dialog' );
		if (
			$dialog.length
			&& $dialog.dialog( 'isOpen' )
			&& ! $( ev.target ).is( '.ui-dialog, a' )
			&& ! $( ev.target ).closest( '.ui-dialog' ).length
		) {
			ev.preventDefault();
			ev.stopPropagation();

			my.event.close_delete_confirmation_dialog( $dialog );
		}
	};

	$( function() {
		my.init();
	} );
} )( jQuery, tribe_events_pro_admin.recurrence );
