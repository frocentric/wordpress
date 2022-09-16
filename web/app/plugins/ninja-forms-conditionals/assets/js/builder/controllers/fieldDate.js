/**
 * Listens for changes in the "extra" settings in "when" settings.
 * We use this for the date field to update the "value" to a timestamp when we change a date value setting.
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			this.listenTo( nfRadio.channel( 'conditions' ), 'change:extra', this.maybeUpdateSetting );
		},

		maybeUpdateSetting: function( e, dataModel ) {
			let dateString = '';
			// Get our date
			let date = jQuery( e.target ).parent().parent().find( "[data-type='date']" ).val();
			if ( 'undefined' == typeof date ) {
				date = '1970-01-02';
			}
			dateString += date + 'T';

			// Get our hour
			let hour = jQuery( e.target ).parent().parent().find( "[data-type='hour']" ).val();
			if ( 'undefined' == typeof hour ) {
				hour = '00';
			}
			dateString += hour + ':';

			// Get our minute
			let minute = jQuery( e.target ).parent().parent().find( "[data-type='minute']" ).val();
			if ( 'undefined' == typeof minute ) {
				minute = '00';
			}
			dateString += minute + 'Z';

			// Build a timestamp
			let dateObject = new Date( dateString );
			let timestamp = Math.floor( dateObject.getTime() / 1000 );

			// Update our value with the timestamp
			dataModel.set( 'value', timestamp );
			nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
		},

	});

	return controller;
} );