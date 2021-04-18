jQuery(document).ready(function(jQuery) {
		
	/* * * Begin File Upload JS * * */

	jQuery(".ninja-forms-change-file-upload").click(function(e){
		e.preventDefault();
		var file_upload_id = this.id.replace('ninja_forms_change_file_upload_', '');
		jQuery("#ninja_forms_file_upload_" + file_upload_id).toggle();
	});


	jQuery(".ninja-forms-delete-file-upload").click(function(e){
		e.preventDefault();
		//var answer = confirm( ninja_forms_uploads_settings.delete );
		//if(answer){
			var file_upload_li = this.id.replace('_delete', '' );
			file_upload_li += "_li";
			jQuery("#" + file_upload_li).fadeOut('fast', function(){
				jQuery("#" + file_upload_li).remove();
			});
						
		//}
	});

	jQuery( document ).on( 'submitResponse.uploads', function( e, response ) {
		var success = response.success;

		var form_settings = response.form_settings;
		var hide_complete = form_settings.hide_complete;
		var clear_complete = form_settings.clear_complete;
		if ( success != false && clear_complete == 1 ) {
			if( jQuery.isFunction( jQuery.fn.MultiFile ) ) {
				jQuery('input:file.multi').MultiFile('reset');
			}
		}
	});

	/* * * End File Upload JS * * */
});