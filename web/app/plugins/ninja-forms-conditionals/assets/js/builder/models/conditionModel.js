/**
 * Conditon Model
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [ 'models/whenCollection', 'models/thenCollection', 'models/elseCollection' ], function( WhenCollection, ThenCollection, ElseCollection ) {
	var model = Backbone.Model.extend( {
		defaults: {
			collapsed: false,
			process: 1,
			connector: 'all',
			when: [ {} ],
			then: [ {} ],
			else: []
		},

		initialize: function() {
			this.set( 'when', new WhenCollection( this.get( 'when' ), { conditionModel: this } ) );
			this.set( 'then', new ThenCollection( this.get( 'then' ), { conditionModel: this } ) );
			this.set( 'else', new ElseCollection( this.get( 'else' ), { conditionModel: this } ) );

			nfRadio.channel( 'conditions' ).trigger( 'init:model', this );
		}
	} );
	
	return model;
} );