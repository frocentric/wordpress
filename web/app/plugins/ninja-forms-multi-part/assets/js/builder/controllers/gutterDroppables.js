/**
 * Listen for drag events on our arrows.
 * 
 * @package Ninja Forms Multi-Part
 * @subpackage Fields
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define(	[],	function () {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			this.listenTo( nfRadio.channel( 'mp' ), 'over:gutter', this.over );
			this.listenTo( nfRadio.channel( 'mp' ), 'out:gutter', this.out );
			this.listenTo( nfRadio.channel( 'mp' ), 'drop:rightGutter', this.dropRight );
			this.listenTo( nfRadio.channel( 'mp' ), 'drop:leftGutter', this.dropLeft );
		},

		over: function( ui, partCollection ) {
			/*
			 * Remove any other draggable placeholders.
			 */
			jQuery( '#nf-main' ).find( '.nf-fields-sortable-placeholder' ).addClass( 'nf-sortable-removed' ).removeClass( 'nf-fields-sortable-placeholder' );

			// Trigger Ninja Forms default handler for being over a field sortable.
			ui.item = ui.draggable;
			nfRadio.channel( 'app' ).request( 'over:fieldsSortable', ui );
			
			/*
			 * If we hover over our droppable for more than x seconds, change the part.
			 */
			// setTimeout( this.changePart, 1500, ui, partCollection );
		},

		out: function( ui, partCollection ) {
			/*
			 * Re-add any draggable placeholders that we removed.
			 */
			jQuery( '#nf-main' ).find( '.nf-sortable-removed' ).addClass( 'nf-fields-sortable-placeholder' );
			
			// Trigger Ninja Forms default handler for being out of a field sortable.
			ui.item = ui.draggable;
			nfRadio.channel( 'app' ).request( 'out:fieldsSortable', ui );

			/*
			 * If we hover over our droppable for more than x seconds, change the part.
			 */
			// clearTimeout( this.changePart );
		},

		drop: function( ui, partCollection, dir ) {
			ui.draggable.dropping = true;
			ui.item = ui.draggable;
			nfRadio.channel( 'app' ).request( 'out:fieldsSortable', ui );
			nfRadio.channel( 'fields' ).request( 'sort:fields', null, null, false );

			/*
			 * If we hover over our droppable for more than x seconds, change the part.
			 */
			// clearTimeout( this.changePart );				
		},

		dropLeft: function( ui, partCollection ) {
			this.drop( ui, partCollection, 'left' );
			/*
			 * Check to see if we have a previous part.
			 */
			if ( ! partCollection.hasPrevious() ) return;
			/*
			 * If we're dealing with a field that already exists on our form, handle moving it.
			 */
			if ( jQuery( ui.draggable ).hasClass( 'nf-field-wrap' ) ) {
				var fieldModel = nfRadio.channel( 'fields' ).request( 'get:field', jQuery( ui.draggable ).data( 'id' ) );
				/*
				 * Add the dragged field to the previous part.
				 */
				var oldOrder = fieldModel.get( 'order' );

				partCollection.getFormContentData().trigger( 'remove:field', fieldModel );
				var previousPart = partCollection.at( partCollection.indexOf( partCollection.getElement() ) - 1 );
				previousPart.get( 'formContentData' ).trigger( 'append:field', fieldModel );
				
				/*
				 * Register our part change to the change manager.
				 */
				// Set our 'clean' status to false so that we get a notice to publish changes
				nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
				// Update our preview
				nfRadio.channel( 'app' ).request( 'update:db' );

				// Add our field addition to our change log.
				var label = {
					object: 'Field',
					label: fieldModel.get( 'label' ),
					change: 'Changed Part',
					dashicon: 'image-flip-horizontal'
				};

				var data = {
					oldPart: partCollection.getElement(),
					newPart: previousPart,
					fieldModel: fieldModel,
					oldOrder: oldOrder
				};

				var newChange = nfRadio.channel( 'changes' ).request( 'register:change', 'fieldChangePart', previousPart, null, label, data );

			} else if ( jQuery( ui.draggable ).hasClass( 'nf-field-type-draggable' ) ) {
				var type = jQuery( ui.draggable ).data( 'id' );
				var fieldModel = this.addField( type, partCollection );
				/*
				 * We have a previous part. Add the new field to it.
				 */
				partCollection.at( partCollection.indexOf( partCollection.getElement() ) - 1 ).get( 'formContentData' ).trigger( 'append:field', fieldModel );
			} else {
				/*
				 * We are dropping a stage
				 */
				
				// Make sure that our staged fields are sorted properly.	
				nfRadio.channel( 'fields' ).request( 'sort:staging' );
				// Grab our staged fields.
				var stagedFields = nfRadio.channel( 'fields' ).request( 'get:staging' );

				_.each( stagedFields.models, function( field, index ) {
					// Add our field.
					
					var fieldModel = this.addField( field.get( 'slug' ), partCollection );
					partCollection.at( partCollection.indexOf( partCollection.getElement() ) - 1 ).get( 'formContentData' ).trigger( 'append:field', fieldModel );
				}, this );

				// Clear our staging
				nfRadio.channel( 'fields' ).request( 'clear:staging' );
			}
		},

		dropRight: function( ui, partCollection ) {
			this.drop( ui, partCollection );
			/*
			 * If we're dealing with a field that already exists on our form, handle moving it.
			 */
			if ( jQuery( ui.draggable ).hasClass( 'nf-field-wrap' ) ) {
				var fieldModel = nfRadio.channel( 'fields' ).request( 'get:field', jQuery( ui.draggable ).data( 'id' ) );
				/*
				 * Check to see if we have a next part.
				 */
				if ( partCollection.hasNext() ) {
					/*
					 * Add the dragged field to the next part.
					 */
					var oldOrder = fieldModel.get( 'order' );

					partCollection.getFormContentData().trigger( 'remove:field', fieldModel );
					var nextPart = partCollection.at( partCollection.indexOf( partCollection.getElement() ) + 1 );
					nextPart.get( 'formContentData' ).trigger( 'append:field', fieldModel );
				
					/*
					 * Register our part change to the change manager.
					 */
					// Set our 'clean' status to false so that we get a notice to publish changes
					nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
					// Update our preview
					nfRadio.channel( 'app' ).request( 'update:db' );

					// Add our field addition to our change log.
					var label = {
						object: 'Field',
						label: fieldModel.get( 'label' ),
						change: 'Changed Part',
						dashicon: 'image-flip-horizontal'
					};

					var data = {
						oldPart: partCollection.getElement(),
						newPart: nextPart,
						fieldModel: fieldModel,
						oldOrder: oldOrder
					};

					var newChange = nfRadio.channel( 'changes' ).request( 'register:change', 'fieldChangePart', nextPart, null, label, data );

				} else {
					var oldPart = partCollection.getElement();
					/*
					 * Add the dragged field to a new part.
					 */
					partCollection.getFormContentData().trigger( 'remove:field', fieldModel );
					var newPart = partCollection.append( { formContentData: [ fieldModel.get( 'key' ) ] } );
					partCollection.setElement( newPart );

					/*
					 * Register our new part to the change manager.
					 */
					// Set our 'clean' status to false so that we get a notice to publish changes
					nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
					// Update our preview
					nfRadio.channel( 'app' ).request( 'update:db' );

					// Add our field addition to our change log.
					var label = {
						object: 'Part',
						label: newPart.get( 'title' ),
						change: 'Added',
						dashicon: 'plus-alt'
					};

					var data = {
						collection: newPart.collection,
						oldPart: oldPart,
						fieldModel: fieldModel
					};

					var newChange = nfRadio.channel( 'changes' ).request( 'register:change', 'addPart', newPart, null, label, data );

				}
			} else if ( jQuery( ui.draggable ).hasClass( 'nf-field-type-draggable' ) ) {
				var type = jQuery( ui.draggable ).data( 'id' );
				var fieldModel = this.addField( type, partCollection );
				if ( partCollection.hasNext() ) {
					/*
					 * We have a next part. Add the new field to it.
					 */
					partCollection.at( partCollection.indexOf( partCollection.getElement() ) + 1 ).get( 'formContentData' ).trigger( 'append:field', fieldModel );
					return false;
				} else {
					/*
					 * We don't have a next part, so add a new one, then add this field to it.
					 */
					var newPart = partCollection.append( { formContentData: [ fieldModel.get( 'key' ) ] } );
					partCollection.setElement( newPart );

					/*
					 * Register our new part to the change manager.
					 */
					// Set our 'clean' status to false so that we get a notice to publish changes
					nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
					// Update our preview
					nfRadio.channel( 'app' ).request( 'update:db' );

					// Add our field addition to our change log.
					var label = {
						object: 'Part',
						label: newPart.get( 'title' ),
						change: 'Added',
						dashicon: 'plus-alt'
					};

					var data = {
						collection: newPart.collection,

					};

					var newChange = nfRadio.channel( 'changes' ).request( 'register:change', 'addPart', newPart, null, label, data );

					return newPart;
				}
			} else {
				// Make sure that our staged fields are sorted properly.	
				nfRadio.channel( 'fields' ).request( 'sort:staging' );
				// Grab our staged fields.
				var stagedFields = nfRadio.channel( 'fields' ).request( 'get:staging' );
				
				var keys = [];
				_.each( stagedFields.models, function( field, index ) {
					// Add our field.
					var fieldModel = this.addField( field.get( 'slug' ), partCollection );
					if ( partCollection.hasNext() ) {
						partCollection.at( partCollection.indexOf( partCollection.getElement() ) + 1 ).get( 'formContentData' ).trigger( 'append:field', fieldModel );
					} else {
						keys.push( fieldModel.get( 'key' ) );
					}
					
				}, this );

				if ( ! partCollection.hasNext() ) {
					/*
					 * Add each of our fields to our next part
					 */
					var newPart = partCollection.append( { formContentData: keys } );
					partCollection.setElement( newPart );

					/*
					 * Register our new part to the change manager.
					 */
					// Set our 'clean' status to false so that we get a notice to publish changes
					nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
					// Update our preview
					nfRadio.channel( 'app' ).request( 'update:db' );

					// Add our field addition to our change log.
					var label = {
						object: 'Part',
						label: newPart.get( 'title' ),
						change: 'Added',
						dashicon: 'plus-alt'
					};

					var data = {
						collection: newPart.collection
					};

					var newChange = nfRadio.channel( 'changes' ).request( 'register:change', 'addPart', newPart, null, label, data );				
				}

				// Clear our staging
				nfRadio.channel( 'fields' ).request( 'clear:staging' );
			}
		},

		addField: function( type, collection ) {
			var fieldType = nfRadio.channel( 'fields' ).request( 'get:type', type ); 
			// Add our field
			var fieldModel = nfRadio.channel( 'fields' ).request( 'add', {
				label: fieldType.get( 'nicename' ),
				type: type
			} );

			collection.getFormContentData().trigger( 'remove:field', fieldModel );
			return fieldModel;
		},

		changePart: function( ui, partCollection ) {
			partCollection.next();
			jQuery( ui.helper ).draggable();
		}

	});

	return controller;
} );