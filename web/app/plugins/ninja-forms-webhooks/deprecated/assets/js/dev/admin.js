_.templateSettings = {
  evaluate    : /<#([\s\S]+?)#>/g,
  interpolate : /<#=([\s\S]+?)#>/g
};

jQuery( document ).ready( function( $ ) {
	// Backbone View for all our args
	var ArgsView = Backbone.View.extend( {
		el: $( '#nf_key_fields' ), // attaches `this.el` to an existing element.
		
		// Get our view up and running.
		initialize: function() {
			_.bindAll(this, 'render'); // fixes loss of context for 'this' within methods
			this.render();
		},

		render: function() {
			_.each( nf_wh_args, function( args, object_id ) {
				var singleview = new SingleArgsView();
				singleview.render( args, object_id );
			} );
			
		},

		events: {
			'click .add'		: 'addArgs',
			'click .add-all'	: 'addAll',
			'click .remove'		: 'removeArgs',
			'click .remove-all' : 'removeAll'
		},

		addArgs: function( e ) {
			e.preventDefault();
			this.renderArgs( {}, 'new' );
		},

		renderArgs: function( args, object_id, tokens ) {
			var singleview = new SingleArgsView();
			var view = singleview.render( args, object_id, tokens );
		},

		removeArgs: function( e ) {
	    	e.preventDefault();
	    	targetEl = $( e.target ).parent().parent().parent();
	    	$( targetEl ).remove();
		},

		addAll: function( e ) {
			e.preventDefault();
			var that = this;
			_.each( nf_notifications.process_fields, function( field ) {
				var tokens = [{ value: 'field_' + field.field_id, label: field.label + ' ID - ' + field.field_id }];
				that.renderArgs( { key: 'field_' + field.field_id, field: 'field_' + field.field_id }, 'new', tokens );
			} );
		},

		removeAll: function( e ) {
			e.preventDefault();
			$( '#nf_wh_args' ).html('');
		}

	} );

	// Backbone View for our single args row
	var SingleArgsView = Backbone.View.extend( {
		el: $( '#nf_wh_args' ),

		// Get our view up and running.
		initialize: function() {
			_.bindAll(this, 'render'); // fixes loss of context for 'this' within methods
		},

		render: function( args, object_id, tokens ) {
			var tmp = _.template( $( '#tmpl-nf-wh-args' ).html() )( { args: args, object_id: object_id } );
			var el = $( '<div>' ).html( tmp );
			this.item = $( el ).find( '.single-wh-args' );
			this.tokenize = $( this.item ).find( '.nf-tokenize' );

			$( this.el ).append( this.item );

			if ( 'undefined' == typeof tokens ) {
				if ( 'undefined' == typeof nf_notifications.tokens[ 'wh_args_' + object_id ] || '' == nf_notifications.tokens[ 'wh_args_' + object_id ] ) {
					tokens = '';
				} else {
					tokens = nf_notifications.tokens[ 'wh_args_' + object_id ];
				}
			}

			$( this.tokenize ).tokenfield({
				autocomplete: {
					source: nf_notifications.search_fields[ 'all' ],
					delay: 100,
				},
				tokens: tokens,
				delimiter: [ '`' ],
				showAutocompleteOnFocus: true,
				beautify: false,
				limit: 0,
				createTokensOnBlur: true
			});

			return this;
		}

	} );

	var argsView = new ArgsView();

	$( document ).on( 'change', '#wh-json-encode', function() {
		if ( this.checked ) {
			$( '#wh-json-use-arg-tr' ).show();
		} else {
			$( '#wh-json-use-arg-tr' ).hide();
		}
		$( '#wh-json-use-arg' ).change();
	} );

	$( document ).on( 'change', '#wh-json-use-arg', function() {
		if ( this.checked && $( '#wh-json-encode' ).attr( 'checked' ) ) {
			$( '#wh-json-arg-tr' ).attr( 'style', '' );
		} else {
			$( '#wh-json-arg-tr' ).hide();
		}
	} );

	$( document ).on( 'change', '#wh-remote-method', function() {
		if ( 'get' == this.value ) {
			$( '#wh-json-use-arg' ).attr( 'checked', true ).attr( 'disabled', true );
			$( '#wh-json-use-arg' ).change();
		} else {
			$( '#wh-json-use-arg' ).attr( 'disabled', false );
		}
	} );
} );