/**
 * Top drawer part view
 * 
 * @package Ninja Forms builder
 * @subpackage App
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function() {
	var view = Marionette.ItemView.extend({
		tagName: 'li',
		template: '#nf-tmpl-mp-drawer-item',

		initialize: function( options ) {
			this.collectionView = options.collectionView;
			this.listenTo( this.model, 'change:title', this.updatedTitle );
			this.listenTo( this.model.collection, 'change:part', this.maybeChangeActive );
		},

		updatedTitle: function() {
			this.render();
			this.collectionView.setULWidth( this.collectionView.el );
		},

		maybeChangeActive: function() {
			jQuery( this.el ).removeClass( 'active' );
			if ( this.model == this.model.collection.getElement() ) {
				jQuery( this.el ).addClass( 'active' );
			}
		},

		attributes: function() {
			return {
				id: this.model.get( 'key' )
			}
		},

		onShow: function() {
			var that = this;
			jQuery( this.el ).droppable( {
				activeClass: 'mp-drag-active',
				hoverClass: 'mp-drag-hover',
				accept: '.nf-field-type-draggable, .nf-field-wrap, .nf-stage',
				tolerance: 'pointer',

				over: function( e, ui ) {
					nfRadio.channel( 'mp' ).trigger( 'over:part', e, ui, that.model, that );
				},

				out: function( e, ui ) {
					nfRadio.channel( 'mp' ).trigger( 'out:part', e, ui, that.model, that );
				},

				drop: function( e, ui ) {
					nfRadio.channel( 'mp' ).trigger( 'drop:part', e, ui, that.model, that );
				}
			} );

			this.maybeChangeActive();
		},

		events: {
			'click': 'click',
		},

		click: function( e ) {
			nfRadio.channel( 'mp' ).trigger( 'click:part', e, this.model );
		},

		templateHelpers: function() {
			var that = this;
			return {
				getIndex: function() {
					return that.model.collection.indexOf( that.model ) + 1;
				}
			}
		}
	});

	return view;
} );