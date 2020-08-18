/**
 * Stores part setting information.
 *
 * @package Ninja Forms builder
 * @subpackage App - Edit Settings Drawer
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function( SettingGroupCollection ) {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			/*
			 * Instantiate our setting group collection
			 */
			this.setupCollection();

			// Respond to requests for our part setting group collection
			nfRadio.channel( 'mp' ).reply( 'get:settingGroupCollection', this.getCollection, this );
		},

		setupCollection: function() {
			var settingGroupCollection = nfRadio.channel( 'app' ).request( 'get:settingGroupCollectionDefinition' );
			this.collection = new settingGroupCollection([
				{
					id: 'primary',
					label: '',
					display: true,
					priority: 100,
					settings: [
						{
							name: 'title',
							type: 'textbox',
							label: 'Part Title',
							width: 'full',
						},
						{
							name: 'mp_remove',
							type: 'html',
							width: 'one-half',
							value: '<a href="#" class="nf-remove-part nf-button secondary extra">Remove Part</a>'
						}
					]
				},
			] );
			// only allow part duplication if Layouts & Styles exist
			var formContentLoadFilters = nfRadio.channel( 'formContent'  ).request( 'get:loadFilters' );
			if( 'undefined' != typeof formContentLoadFilters[4] ) {
				 var colSettings  = this.collection.models[0].get( 'settings' );
					 colSettings.push(
					{
						name: 'mp_duplicate',
						type: 'html',
						width: 'one-half',
						value: '<a href="#" class="nf-duplicate-part nf-button secondary extra">Duplicate Part</a>'
					}
				)
			}
		},

		/**
		 * Return our setting group collection.
		 *
		 * @since  3.0
		 * @return backbone.collection
		 */
		getCollection: function() {
			return this.collection;
		}

	});

	return controller;
} );
