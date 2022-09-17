/**
 * Returns the type of input value we'd like to use.
 * This covers all the core field types.
 *
 * Add-ons can copy this code structure in order to get custom "values" for conditions.
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			nfRadio.channel( 'conditions-checkbox' ).reply( 'get:valueInput', this.getCheckboxValue );
			nfRadio.channel( 'conditions-list' ).reply( 'get:valueInput', this.getListValue );
			nfRadio.channel( 'conditions-listcountry' ).reply( 'get:valueInput', this.getListCountryValue );
			nfRadio.channel( 'conditions-date' ).reply( 'get:valueInput', this.getDateValue );
		},

		getCheckboxValue: function( key, trigger, value ) {
			/*
			 * Checks our values ensures they've been converted to strings and
			 * sets the value.
			 */
			if( 1 == value && value.length > 1 ) {
				value = 'checked';
			} else if( 0 == value && value.length > 1 ) {
                value = 'unchecked';
            } else if( 0 == value.length ){
				value = '';
			}

			var template = Backbone.Radio.channel( 'app' ).request( 'get:template', '#tmpl-nf-cl-value-checkbox' );
			return template( { value: value } );
		},

		getListValue: function( key, trigger, value ) {
			var fieldModel = nfRadio.channel( 'fields' ).request( 'get:field', key );
			var options = fieldModel.get( 'options' );
			var template = Backbone.Radio.channel( 'app' ).request( 'get:template', '#tmpl-nf-cl-value-list' );
			return template( { options: options, value: value } );
		},

		getListCountryValue: function( key, trigger, value ) {
			var fieldModel = nfRadio.channel( 'fields' ).request( 'get:field', key );
			var options = fieldModel.get( 'options' );
			var template = Backbone.Radio.channel( 'app' ).request( 'get:template', '#tmpl-nf-cl-value-list' );

			options.reset();
			_.each( nfListCountries, function( value, label ) {
				options.add( { label: label, value: value } );
			});

			return template( { options: options, value: value } );
		},

		getDateValue: function( key, trigger, value ) {
			let fieldModel = nfRadio.channel( 'fields' ).request( 'get:field', key );
			let dateMode = fieldModel.get( 'date_mode' );

			if ( 'undefined' == typeof dateMode ) {
				dateMode = 'date_only';
			}

			let timestamp = value * 1000;
			let dateObject = new Date( timestamp );
			dateObject = new Date( dateObject.getUTCFullYear(), dateObject.getUTCMonth(), dateObject.getUTCDate(), dateObject.getUTCHours(), dateObject.getUTCMinutes() );

			let selectedHour = dateObject.getHours();
			let selectedMinute = dateObject.getMinutes(); 

			let hourSelect = '<select class="extra" data-type="hour">';
			for (var i = 0; i < 24; i++) {
				let formattedOption = i;
				let selected = '';
				if ( i < 10 ) {
					formattedOption = '0' + formattedOption;
				}

				if ( selectedHour == formattedOption ) {
					selected = 'selected="selected"';
				}
				
				hourSelect += '<option value="' + formattedOption + '" ' + selected + '>' + formattedOption + '</option>';
			}
			hourSelect += '</select>';

			let minuteSelect = '<select class="extra" data-type="minute">';
			for (var i = 0; i < 60; i++) {
				let formattedOption = i;
				let selected = '';
				if ( i < 10 ) {
					formattedOption = '0' + formattedOption;
				}

				if ( selectedMinute == formattedOption ) {
					selected = 'selected="selected"';
				}
				
				minuteSelect += '<option value="' + formattedOption + '" ' + selected + '>' + formattedOption + '</option>';
			}
			minuteSelect += '</select>';

			let date = moment( dateObject.toUTCString() ).format( 'YYYY-MM-DD' );
			if ( '1970-01-01' == date ) {
				date = '';
			}

			let template = Backbone.Radio.channel( 'app' ).request( 'get:template', '#tmpl-nf-cl-value-date-' + dateMode );
			return template( { value: value, date: date, hourSelect: hourSelect, minuteSelect: minuteSelect  } );
		},


	});

	return controller;
} );
