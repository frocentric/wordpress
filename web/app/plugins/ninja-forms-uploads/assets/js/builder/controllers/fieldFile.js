var fileUploadsFieldController = Marionette.Object.extend( {
    initialize: function() {
        Backbone.Radio.channel( 'conditions-key-select-field-file_upload' ).reply( 'hide', function( modelType ){ if ( 'when' == modelType ) { return true; } else { return false; } } );
        Backbone.Radio.channel( 'conditions-file_upload' ).reply( 'get:triggers', this.getTriggers );
    },

    getTriggers: function() {
    	return {
			show_field: {
				label: nfcli18n.templateHelperShowField,
				value: 'show_field'
			},

			hide_field: {
				label: nfcli18n.templateHelperHideField,
				value: 'hide_field'
			}
		};
    }
});

jQuery( document ).ready( function( $ ) {
    new fileUploadsFieldController();
});