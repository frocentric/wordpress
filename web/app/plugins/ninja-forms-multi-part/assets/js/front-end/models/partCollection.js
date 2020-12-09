define( [ 'models/partModel' ], function( PartModel ) {
	var collection = Backbone.Collection.extend( {
		model: PartModel,
		currentElement: false,

		initialize: function( models, options ){
			this.formModel = options.formModel;
		},
		
		getElement: function() {
			/*
			 * If we haven't set an element yet, set it to the first one.
			 */
			if ( ! this.currentElement ) {
				this.setElement( this.at( 0 ), true );
			}
			return this.currentElement;
		},

		setElement: function( model, silent ) {
			silent = silent || false;

			/*
			 * If we have part errors and aren't updating silently, check for part errors.
			 */
			if ( ! silent ) {
				if ( this.partErrors() ) return;
			}
			
			this.currentElement = model;
			if ( ! silent ) {
				this.trigger( 'change:part', this );
				nfRadio.channel( 'nfMP' ).trigger( 'change:part', this );
			} 
		},
		  
		// check for errors on Next click if we have validate parts on
		setNextElement: function( model, silent ) {
			silent = silent || false;

			/*
			 * If we have part errors and aren't updating silently, check for part errors.
			 */
			if ( ! silent ) {
				if ( this.partErrors() ) return;
			}
			
			this.currentElement = model;
			if ( ! silent ) {
				this.trigger( 'change:part', this );
				nfRadio.channel( 'nfMP' ).trigger( 'change:part', this );
			} 
		},

		// We don't want to stop user from moving back in the form if there are errors
		setPreviousElement: function( model, silent ) {
			silent = silent || false;
			
			this.currentElement = model;
			if ( ! silent ) {
				this.trigger( 'change:part', this );
				nfRadio.channel( 'nfMP' ).trigger( 'change:part', this );
			}
		},
		
		next: function (){
			/*
			 * If this isn't the last visible part, move forward.
			 */
			if ( this.getVisibleParts().length - 1 != this.getVisibleParts().indexOf( this.getElement() ) ) {
				this.setNextElement( this.getVisibleParts()[ this.getVisibleParts().indexOf( this.getElement() ) + 1 ] );
			}
			
			return this;
		},

		previous: function() {
			/*
			 * If this isn't the first visible part, move backward.
			 */
			if ( 0 != this.getVisibleParts().indexOf( this.getElement() ) ) {
				this.setPreviousElement( this.getVisibleParts()[ this.getVisibleParts().indexOf( this.getElement() ) - 1 ] );	
			}
			
			return this;
		},

		partErrors: function() {
			if ( 'undefined' == typeof this.formModel.get( 'settings' ).mp_validate || 0 == this.formModel.get( 'settings' ).mp_validate ) return false;
			/*
			 * Check to see if our parts have any errors.
			 */
			this.currentElement.validateFields();
			return this.currentElement.get( 'errors' );
		},

		validateFields: function() {
			/*
			 * call validateFields on each visible part
			 */
			_.each( this.getVisibleParts(), function( partModel ) { partModel.validateFields(); } );
		},

		getVisibleParts: function() {
			return this.where( { visible: true } );
		}
	} );

	return collection;
} );