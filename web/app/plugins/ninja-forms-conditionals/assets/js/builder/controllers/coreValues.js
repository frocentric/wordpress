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
		}


	});

	return controller;
} );
