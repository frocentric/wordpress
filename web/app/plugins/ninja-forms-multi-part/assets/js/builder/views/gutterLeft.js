/**
 * Main content left gutter
 * 
 * @package Ninja Forms builder
 * @subpackage App
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function() {
	var view = Marionette.ItemView.extend({
		tagName: 'div',
		template: '#nf-tmpl-mp-gutter-left',

		events: {
			'click .fa': 'clickPrevious'
		},

		initialize: function() {
			this.collection = nfRadio.channel( 'mp' ).request( 'get:collection' );
			this.listenTo( this.collection, 'change:part', this.render );
			this.listenTo( this.collection, 'sort', this.render );
			this.listenTo( this.collection, 'remove', this.render );
		},

		onRender: function() {
			var that = this;
			jQuery( this.el ).find( '.fa' ).droppable( {
				// Activate by pointer
				tolerance: 'pointer',
				// Class added when we're dragging over
				hoverClass: 'mp-circle-over',
				activeClass: 'mp-circle-active',
				// Which elements do we want to accept?
				accept: '.nf-field-type-draggable, .nf-field-wrap, .nf-stage',

				/**
				 * When we drag over this droppable, trigger a radio event.
				 * 
				 * @since  3.0
				 * @param  object 	e  event
				 * @param  object 	ui jQuery UI element
				 * @return void
				 */
				over: function( e, ui ) {
					nfRadio.channel( 'mp' ).trigger( 'over:gutter', ui, that.collection );
				},

				/**
				 * When we drag out of this droppable, trigger a radio event.
				 * 
				 * @since  3.0
				 * @param  object 	e  event
				 * @param  object 	ui jQuery UI element
				 * @return void
				 */
				out: function( e, ui ) {
					nfRadio.channel( 'mp' ).trigger( 'out:gutter', ui, that.collection );
				},

				/**
				 * When we drop on this droppable, trigger a radio event.
				 * 
				 * @since  3.0
				 * @param  object 	e  event
				 * @param  object 	ui jQuery UI element
				 * @return void
				 */
				drop: function( e, ui ) {
					nfRadio.channel( 'mp' ).trigger( 'drop:leftGutter', ui, that.collection );
				}
			} );
		},

		clickPrevious: function( e ) {
			nfRadio.channel( 'mp' ).trigger( 'click:previous', e );
		},

		templateHelpers: function() {
			var that = this;
			return {
				hasPrevious: function() {
					return that.collection.hasPrevious();
				}
			}
		},

		changePart: function( context ) {
			context.collection.previous();
		}
	});

	return view;
} );