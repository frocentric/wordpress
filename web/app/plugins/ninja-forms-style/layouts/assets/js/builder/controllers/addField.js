define( [], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			var fieldCollection = nfRadio.channel( 'fields' ).request( 'get:collection' );
			fieldCollection.on( 'add', this.maybeAddRow, this );

			this.listenTo( nfRadio.channel( 'drawer-addField' ), 'startDrag:type', this.startDragging );
			this.listenTo( nfRadio.channel( 'drawer-addField' ), 'stopDrag:type', this.stopDragging );

			this.listenTo( nfRadio.channel( 'drawer-addField' ), 'startDrag:fieldStaging', this.startDragging );
			this.listenTo( nfRadio.channel( 'drawer-addField' ), 'stopDrag:fieldStaging', this.stopDragging );
		},

		maybeAddRow: function( model ) {
            if ( ! model.get( 'cellcid' ) ) {
				var order = ( ! model.get( 'order' ) || 999 == model.get( 'order' ) ) ? null : model.get( 'order' );
				nfRadio.channel( 'layouts' ).request( 'add:row', null, { order: order, field: model } );
			}
		},

		startDragging: function( ui ) {
			jQuery( '.layouts-row' ).addClass( 'dragging' );
			jQuery( '#nf-builder' ).addClass( 'layouts-dragging' );
		},

		stopDragging: function( ui ) {
			jQuery( '.layouts-row' ).removeClass( 'dragging' );
			jQuery( '#nf-builder' ).removeClass( 'layouts-dragging' );
		}

	});

	return controller;
} );
