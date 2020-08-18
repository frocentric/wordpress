/**
 * Handles events for the part items in our bottom drawer.
 * 
 * @package Ninja Forms Multi-Part
 * @subpackage Fields
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define(	[],	function () {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			this.listenTo( nfRadio.channel( 'mp' ), 'over:part', this.over );
			this.listenTo( nfRadio.channel( 'mp' ), 'out:part', this.out );
			this.listenTo( nfRadio.channel( 'mp' ), 'drop:part', this.drop );
		},

		over: function( e, ui, partModel, partView ) {
			/*
			 * Remove any other draggable placeholders.
			 */
			jQuery( '#nf-main' ).find( '.nf-fields-sortable-placeholder' ).addClass( 'nf-sortable-removed' ).removeClass( 'nf-fields-sortable-placeholder' );

			// Trigger Ninja Forms default handler for being over a field sortable.
			ui.item = ui.draggable;

			if ( jQuery( ui.draggable ).hasClass( 'nf-field-type-draggable' ) || jQuery( ui.draggable ).hasClass( 'nf-stage' ) ) {
				nfRadio.channel( 'app' ).request( 'over:fieldsSortable', ui );
			} else {
				jQuery( ui.helper ).css( { 'width': '300px', 'height': '50px', 'opacity': '0.7' } );
			}
		},

		out: function( e, ui, partModel, partView ) {
			/*
			 * Re-add any draggable placeholders that we removed.
			 */
			jQuery( '#nf-main' ).find( '.nf-sortable-removed' ).addClass( 'nf-fields-sortable-placeholder' );

			// Trigger Ninja Forms default handler for being out of a field sortable.
			ui.item = ui.draggable;
			if ( jQuery( ui.draggable ).hasClass( 'nf-field-type-draggable' ) || jQuery( ui.draggable ).hasClass( 'nf-stage' ) ) {
				nfRadio.channel( 'app' ).request( 'out:fieldsSortable', ui );
			} else {
				// Get our sortable element.
				var sortableEl = nfRadio.channel( 'fields' ).request( 'get:sortableEl' );
				// Get our fieldwidth.
				var fieldWidth = jQuery( sortableEl ).width();
				var fieldHeight = jQuery( sortableEl ).height();

				jQuery( ui.helper ).css( { 'width': fieldWidth, 'height': '', 'opacity': '' } );
			}
		},

		drop: function( e, ui, partModel, partView ) {
			ui.draggable.dropping = true;
			// Trigger Ninja Forms default handler for being out of a field sortable.
			ui.item = ui.draggable;
			nfRadio.channel( 'app' ).request( 'out:fieldsSortable', ui );

			jQuery( ui.draggable ).effect( 'transfer', { to: jQuery( partView.el ) }, 500 );

			if ( jQuery( ui.draggable ).hasClass( 'nf-field-wrap' ) ) { // Dropping a field that already exists
				this.dropField( e, ui, partModel, partView );
			} else if ( jQuery( ui.draggable ).hasClass( 'nf-field-type-draggable' ) ) { // Dropping a new field
				this.dropNewField( e, ui, partModel, partView );
			} else if ( jQuery( ui.draggable ).hasClass( 'nf-stage' ) ) { // Dropping the staging area
				this.dropStaging( e, ui, partModel, partView );
			}
		},

		dropField: function( e, ui, partModel, partView ) {
			/*
			 * If we are dropping a field that exists on our form already:
			 * Remove it from the current part.
			 * Add it to the new part.
			 */
			nfRadio.channel( 'fields' ).request( 'sort:fields', null, null, false );
			nfRadio.channel( 'app' ).request( 'out:fieldsSortable', ui );
			var fieldModel = nfRadio.channel( 'fields' ).request( 'get:field', jQuery( ui.draggable ).data( 'id' ) );
			var oldOrder = fieldModel.get( 'order' );
			var oldPart = partModel.collection.getElement();
			var newPart = partModel;

			/*
			 * Add the dragged field to the previous part.
			 */
			partModel.collection.getFormContentData().trigger( 'remove:field', fieldModel );
			partModel.get( 'formContentData' ).trigger( 'append:field', fieldModel );

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
				oldPart: oldPart,
				newPart: newPart,
				fieldModel: fieldModel,
				oldOrder: oldOrder
			};

			var newChange = nfRadio.channel( 'changes' ).request( 'register:change', 'fieldChangePart', partModel, null, label, data );
		},

		dropNewField: function( e, ui, partModel, partView ) {
			var type = jQuery( ui.draggable ).data( 'id' );
			var fieldModel = this.addField( type, partModel.collection );
			/*
			 * We have a previous part. Add the new field to it.
			 */
			partModel.get( 'formContentData' ).trigger( 'append:field', fieldModel );
		},

		dropStaging: function( e, ui, partModel, partView ) {
			/*
			 * We are dropping a stage
			 */
			
			// Make sure that our staged fields are sorted properly.	
			nfRadio.channel( 'fields' ).request( 'sort:staging' );
			// Grab our staged fields.
			var stagedFields = nfRadio.channel( 'fields' ).request( 'get:staging' );

			_.each( stagedFields.models, function( field, index ) {
				// Add our field.
				var fieldModel = this.addField( field.get( 'slug' ), partModel.collection );
				partModel.get( 'formContentData' ).trigger( 'append:field', fieldModel );
			}, this );

			// Clear our staging
			nfRadio.channel( 'fields' ).request( 'clear:staging' );
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
		}


	});

	return controller;
} );