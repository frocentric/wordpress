define( [], function() {
	var model = Backbone.Model.extend( {
		initialize: function( models, options ) {
			/*
			 * If our key or comparator is empty, don't do anything else.
			 */
			if ( ! this.get( 'key' ) || ! this.get( 'comparator' ) ) return;

			/*
			 * Our key could be a field or a calc.
			 * We need to setup a listener on either the field or calc model for changes.
			 */
			if ( 'calc' == this.get( 'type' ) ) { // We have a calculation key
				/*
				 * Get our calc model
				 */
				var calcModel = nfRadio.channel( 'form-' + this.collection.options.condition.collection.formModel.get( 'id' ) ).request( 'get:calc', this.get( 'key' ) );
				/*
				 * When we update our calculation, update our compare
				 */
				this.listenTo( calcModel, 'change:value', this.updateCalcCompare );
				/*
				 * Update our compare status.
				 */
				this.updateCalcCompare( calcModel );
			} else { // We have a field key
				// Get our field model
				var fieldModel = nfRadio.channel( 'form-' + options.condition.collection.formModel.get( 'id' ) ).request( 'get:fieldByKey', this.get( 'key' ) );

				if( 'undefined' == typeof fieldModel ) return;

				// When we change the value of our field, update our compare status.
				fieldModel.on( 'change:value', this.updateFieldCompare, this );
				// When we keyup in our field, maybe update our compare status.
				this.listenTo( nfRadio.channel( 'field-' + fieldModel.get( 'id' ) ), 'keyup:field', this.maybeupdateFieldCompare );
				// Update our compare status.
				this.updateFieldCompare( fieldModel );

				/*
				 * TODO: This should be moved to the show_field/hide_field file because it is specific to showing and hiding.
				 * Create a radio message here so that the specific JS file can hook into whenModel init.
				 */
				fieldModel.on( 'change:visible', this.updateFieldCompare, this );
			}
		},

		updateCalcCompare: function( calcModel ) {
			this.updateCompare( calcModel.get( 'value' ) );
		},

		maybeupdateFieldCompare: function( el, fieldModel, keyCode ) {
			if( 'checkbox' == fieldModel.get( 'type' ) ){
                var fieldValue = ( 'checked' == jQuery( el ).attr( 'checked' ) ) ? 1 : 0;
            } else if( 'listcheckbox' == fieldModel.get( 'type' ) ) {
				// This field isn't a single element, so we need to reference the fieldModel, instead of the DOM.
                var fieldValue = fieldModel.get( 'value' ).join();
            } else {
				var fieldValue = jQuery( el ).val();
			}


			this.updateFieldCompare( fieldModel, null, fieldValue );
		},

		updateCompare: function( value ) {
			var this_val = this.get( 'value' );

			// if this is a calcModel then let's convert to number for comparison
			if ( 'calc' === this.get( 'type' ) ) {
				this_val = Number( this_val );
				value = Number( value );
			}
			// Check to see if the value of the field model value COMPARATOR the value of our when condition is true.
			var status = this.compareValues[ this.get( 'comparator' ) ]( value, this_val );
			this.set( 'status', status );
		},

		updateFieldCompare: function( fieldModel, val, fieldValue ) {
			if ( _.isEmpty( fieldValue ) ) {
				fieldValue = fieldModel.get( 'value' );
			}

			// Change the value of checkboxes to match the new convention.
			if( 'checkbox' == fieldModel.get( 'type' ) ) {
				if( 0 == fieldValue ) {
					fieldValue = 'unchecked';
				} else {
					fieldValue = 'checked';
				}
			}
			this.updateCompare( fieldValue );

			/*
			 * TODO: This should be moved to the show_field/hide_field file because it is specific to showing and hiding.
			 */
			if ( ! fieldModel.get( 'visible' ) ) {
				this.set( 'status', false );
			}			
		},

		compareValues: {
			'equal': function( a, b ) {
				return a == b;
			},
			'notequal': function( a, b ) {
				return a != b;
			},
			'contains': function( a, b ) {
				if ( jQuery.isArray( a ) ) {
					/*
					 * If a is an array, then we're searching for an index.
					 */
					return a.indexOf( b ) >= 0;
				} else {
					/*
					 * If a is a string, then we're searching for a string position.
					 *
					 * If our b value has quotes in it, we want to find that exact word or phrase.
					 */
					if ( b.indexOf( '"' ) >= 0 ) {
						b = b.replace( /['"]+/g, '' );
						return new RegExp("\\b" + b + "\\b").test( a );
					}
					return a.toLowerCase().indexOf( b.toLowerCase() ) >= 0; 				
				}
			},
			'notcontains': function( a, b ) {
				return ! this.contains( a, b );
			},
			'greater': function( a, b ) {
				/*
				 * In 2.9.x, you could use the greater and less like string count.
				 * i.e. if textbox > (empty string) do something.
				 * This recreates that ability.
				 */
				if ( jQuery.isNumeric( b ) ) {
					return parseFloat( a ) > parseFloat( b );
				} else if ( 'string' == typeof a ) {
					return 0 < a.length;
				}
				
			},
			'less': function( a, b ) {
				/*
				 * In 2.9.x, you could use the greater and less like string count.
				 * i.e. if textbox > (empty string) do something.
				 * This recreates that ability.
				 */
				if ( jQuery.isNumeric( b ) ) {
					return parseFloat( a ) < parseFloat( b );
				} else if ( 'string' == typeof a ) {
					return 0 >= a.length;
				}
		
			},
			'greaterequal': function( a, b ) {
				return parseFloat( a ) > parseFloat( b ) || parseFloat( a ) == parseFloat( b );
			},
			'lessequal': function( a, b ) {
				return parseFloat( a ) < parseFloat( b ) || parseFloat( a ) == parseFloat( b );
			}
		} 
	} );
	
	return model;
} );