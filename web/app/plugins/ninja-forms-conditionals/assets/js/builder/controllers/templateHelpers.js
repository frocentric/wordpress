/**
 * Adds template helpers for the fields conditional logic setting type
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			this.listenTo( nfRadio.channel( 'conditions' ), 'init:model', this.addTemplateHelpers );
			this.listenTo( nfRadio.channel( 'conditions' ), 'init:thenModel', this.addTemplateHelpers );
			this.listenTo( nfRadio.channel( 'conditions' ), 'init:whenModel', this.addTemplateHelpers );
			this.listenTo( nfRadio.channel( 'conditions' ), 'init:elseModel', this.addTemplateHelpers );
			
		},

		addTemplateHelpers: function( model ) {
			model.set( 'renderKeySelect', this.renderKeySelect );
			model.set( 'renderComparators', this.renderComparators );
			model.set( 'renderTriggers', this.renderTriggers );
			model.set( 'renderWhenValue', this.renderWhenValue );
			model.set( 'renderItemValue', this.renderItemValue );
		},

		renderKeySelect: function( currentValue, modelType ) {
			
			var groups = []

			var fieldCollection = nfRadio.channel( 'fields' ).request( 'get:collection' );
			var fieldOptions = _.chain( fieldCollection.models )
				.filter( function( field ) { return ! nfRadio.channel( 'conditions-key-select-field-' + field.get( 'type' ) ).request( 'hide', modelType ) || false; })
				.filter( function( field )  {

					// filter out these fields for the when condition
					var notForWhen = [ 'submit', 'hr', 'html', 'save', 'file-upload', 'password', 'passwordconfirm', 'product' ];
					
					if( field.get( 'key' ) === currentValue ) {
						notForWhen = notForWhen.splice( notForWhen.indexOf( field.get( 'type' ), 1) );
					}

					if( notForWhen.includes( field.get( 'type' ) ) && 'when' === modelType ) {
						return false;
					}

					return true;
				})
				.map( function( field ) {
                    var label = field.get( 'label' )
					if( 'undefined' !== typeof field.get( 'admin_label' ) && 0 < field.get( 'admin_label' ).length ){
                    	label = field.get( 'admin_label' );
					}
					return { key: field.get( 'key' ), label: label }; }
				)
				.sortBy( function( field ){
					return field.label.toLowerCase();
				} )
				.value();
				
			groups.push( { label: 'Fields', type: 'field', options: fieldOptions } );
			
			var calcCollection = nfRadio.channel( 'settings' ).request( 'get:setting', 'calculations' );

			/*
			 * If we are working on a 'when' model and we have calculations, add them to our select options.
			 */
			if ( 'when' == modelType && 0 < calcCollection.length ) {
				var calcOptions = calcCollection.map( function( calc ) {
					return { key: calc.get( 'name' ), label: calc.get( 'name' ) };
				} );

				groups.push( { label: 'Calculations', type: 'calc', options: calcOptions } );
			}

			/*
			 * Pass our groups through any 'when/then' group filters we have.
			 */
			var filters = nfRadio.channel( 'conditions' ).request( 'get:groupFilters' );
			_.each( filters, function( filter ) {
				groups = filter( groups, modelType );
			} );

			/*
			 * Use a template to get our field select
			 */
			var template = Backbone.Radio.channel( 'app' ).request( 'get:template', '#tmpl-nf-cl-key-select' );

			var tmp = template( { groups: groups, currentValue: currentValue } );
			return tmp;
		},

		renderComparators: function( type, key, currentComparator ) {
			var defaultComparators = {
				equal: {
					label: nfcli18n.templateHelperEquals,
					value: 'equal'
				},

				notequal: {
					label: nfcli18n.templateHelperDoesNotEqual,
					value: 'notequal'
				},

				contains: {
					label: nfcli18n.templateHelperContains,
					value: 'contains'
				},

				notcontains: {
					label: nfcli18n.templateHelperDoesNotContain,
					value: 'notcontains'
				},

				greater: {
					label: nfcli18n.templateHelperGreaterThan,
					value: 'greater'
				},

				less: {
					label: nfcli18n.templateHelperLessThan,
					value: 'less'
				}
			};

			if ( key ) {
				/*
				 * This could be a field or a calculation key. If it's a calc key, get the calc model.
				 */
				if ( 'calc' == type ) {
					var comparators = _.omit( defaultComparators, 'contains', 'notcontains' );
					_.extend( comparators, {
						lessequal: {
							label: nfcli18n.templateHelperLessThanOrEqual,
							value: 'lessequal'
						},

						greaterequal: {
							label: nfcli18n.templateHelperGreaterThanOrEqual,
							value: 'greaterequal'
						}
					} );
				} else {
					/*
					 * Send out a radio request for an html value on a channel based upon the field type.
					 *
					 * Get our field by key
					 * Get our field type model
					 *
					 * Send out a message on the type channel
					 * If we don't get a response, send a message out on the parent type channel
					 */
					var fieldModel = nfRadio.channel( 'fields' ).request( 'get:field', key );

					if( fieldModel ) {
						var comparators = nfRadio.channel('conditions-' + fieldModel.get('type')).request('get:comparators', defaultComparators, currentComparator );
						if (!comparators) {
							var typeModel = nfRadio.channel('fields').request('get:type', fieldModel.get('type'));
							comparators = nfRadio.channel('conditions-' + typeModel.get('parentType')).request('get:comparators', defaultComparators, currentComparator ) || defaultComparators;
						}
					} else {
						var comparators = defaultComparators;
					}
				}
			} else {
				var comparators = defaultComparators;
			}

			/*
			 * Use a template to get our comparator select
			 */
			var template = Backbone.Radio.channel( 'app' ).request( 'get:template', '#tmpl-nf-cl-comparators' );
			return template( { comparators: comparators, currentComparator: currentComparator } );
		},

		renderTriggers: function( type, key, currentTrigger, value ) {
			var defaultTriggers = {
				show_field: {
					label: nfcli18n.templateHelperShowField,
					value: 'show_field'
				},

				hide_field: {
					label: nfcli18n.templateHelperHideField,
					value: 'hide_field'
				},

				change_value: {
					label: nfcli18n.templateHelperChangeValue,
					value: 'change_value'
				},

				set_required: {
					label: nfcli18n.templateHelperSetRequired,
					value: 'set_required'
				},

				unset_required: {
					label: nfcli18n.templateHelperUnsetRequired,
					value: 'unset_required'
				}
			};

			if ( key && 'field' == type ) {
				/*
				 * Send out a radio request for an html value on a channel based upon the field type.
				 *
				 * Get our field by key
				 * Get our field type model
				 *
				 * Send out a message on the type channel
				 * If we don't get a response, send a message out on the parent type channel
				 */
				var fieldModel = nfRadio.channel( 'fields' ).request( 'get:field', key );

				if( 'undefined' != typeof fieldModel ) {
					var typeModel = nfRadio.channel('fields').request('get:type', fieldModel.get('type'));

					var triggers = nfRadio.channel('conditions-' + fieldModel.get('type')).request('get:triggers', defaultTriggers);
					if (!triggers) {
						triggers = nfRadio.channel('conditions-' + typeModel.get('parentType')).request('get:triggers', defaultTriggers) || defaultTriggers;
					}
				} else {
					var triggers = nfRadio.channel( 'conditions-' + type ).request( 'get:triggers', defaultTriggers ) || defaultTriggers;
				}
			} else {
				var triggers = nfRadio.channel( 'conditions-' + type ).request( 'get:triggers', defaultTriggers ) || defaultTriggers;
			}


			/*
			 * Use a template to get our comparator select
			 */
			var template = Backbone.Radio.channel( 'app' ).request( 'get:template', '#tmpl-nf-cl-triggers' );
			return template( { triggers: triggers, currentTrigger: currentTrigger } );
		},

		renderWhenValue: function( type, key, comparator, value ) {
			/*
			 * Use a template to get our value
			 */
			var template = Backbone.Radio.channel( 'app' ).request( 'get:template', '#tmpl-nf-cl-value-default' );
			var defaultHTML = template( { value: value } );

			/*
			 * If we have a key and it's not a calc, get our field type based HTML.
			 */
			if ( key && 'calc' != type ) {
				/*
				 * Send out a radio request for an html value on a channel based upon the field type.
				 *
				 * Get our field by key
				 * Get our field type model
				 *
				 * Send out a message on the type channel
				 * If we don't get a response, send a message out on the parent type channel
				 */
				var fieldModel = nfRadio.channel( 'fields' ).request( 'get:field', key );

				if( fieldModel ) {
					var html = nfRadio.channel('conditions-' + fieldModel.get('type')).request('get:valueInput', key, comparator, value);
					if (!html) {
						var typeModel = nfRadio.channel('fields').request('get:type', fieldModel.get('type'));
						html = nfRadio.channel('conditions-' + typeModel.get('parentType')).request('get:valueInput', key, comparator, value) || defaultHTML;
					}
				} else {
					html = defaultHTML;
				}
			} else {
				var html = defaultHTML;
			}
			
			return html;
		},

		renderItemValue: function( key, trigger, value ) {
			/*
			 * Use a template to get our value
			 *
			 * TODO: This should be much more dynamic.
			 * At the moment, we manually check to see if we are doing a "change_value" or similar trigger.
			 */
			if ( trigger != 'change_value'
				&& trigger != 'select_option'
				&& trigger != 'deselect_option'
				&& trigger != 'show_option'
				&& trigger != 'hide_option' 
			) {
				return '';
			}

			var template = Backbone.Radio.channel( 'app' ).request( 'get:template', '#tmpl-nf-cl-value-default' );
			var defaultHTML = template( { value: value } );

			if ( key ) {
				/*
				 * Send out a radio request for an html value on a channel based upon the field type.
				 *
				 * Get our field by key
				 * Get our field type model
				 *
				 * Send out a message on the type channel
				 * If we don't get a response, send a message out on the parent type channel
				 */
				var fieldModel = nfRadio.channel( 'fields' ).request( 'get:field', key );

				if( 'undefined' != typeof fieldModel ) {
					var typeModel = nfRadio.channel('fields').request('get:type', fieldModel.get('type'));
					var html = nfRadio.channel('conditions-' + fieldModel.get('type')).request('get:valueInput', key, trigger, value);
					if (!html) {
						html = nfRadio.channel('conditions-' + typeModel.get('parentType')).request('get:valueInput', key, trigger, value) || defaultHTML;
					}
				}
			} else {
				var html = defaultHTML;
			}

			return html;
		}
	});

	return controller;
} );
