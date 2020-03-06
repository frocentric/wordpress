define( [ 'models/whenCollection' ], function( WhenCollection ) {
	var model = Backbone.Model.extend( {
		initialize: function( options ) {
			/*
			 * Our "when" statement will be like:
			 * When field1 == value
			 * AND field2 == value
			 *
			 * We need to create a collection out of this when statement, with each row as a model.
			 */
			this.set( 'when', new WhenCollection( this.get( 'when' ), { condition: this } ) );
			/*
			 * When we update any of our "when" models' status, check to see if we should send a message.
			 */
			this.get( 'when' ).on( 'change:status', this.checkWhen, this );
			/*
			 * Check our initial status;
			 */
			this.checkWhen();
		},

		checkWhen: function() {
			/*
			 * If we have any OR connectors, then any status being true should trigger pass.
			 * Otherwise, we need every status to be true.
			 */
			var statusResults = this.get( 'when' ).pluck( 'status' );

			var connectors = this.get( 'when' ).pluck( 'connector' );
			var allAND = _.every( _.values( connectors ), function( v ) { return v == 'AND' }, this );
			if ( allAND ) {
				var status = _.every( _.values( statusResults ), function(v) { return v; }, this );
			} else {
				var status = _.some( _.values( statusResults ), function(v) { return v; }, this );
			}

			if ( status ) {
			   	/*
			 	 * Send out a request for each of our "then" statements.
			 	 */
				_.each( this.get( 'then' ), function( then, index ) {
					nfRadio.channel( 'condition:trigger' ).request( then.trigger, this, then );
				}, this );			 
			} else {
				/*
				 * Send out a request for each of our "else" statements.
				 */
				_.each( this.get( 'else' ), function( elseData, index ) {
					nfRadio.channel( 'condition:trigger' ).request( elseData.trigger, this, elseData );
				}, this );
			}
		}
	} );
	
	return model;
} );