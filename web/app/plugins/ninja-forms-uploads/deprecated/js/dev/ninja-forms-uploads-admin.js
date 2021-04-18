jQuery(document).ready(function($) {
/* * * Begin File Upload Settings JS * * */
	$("#ninja_forms_reset_base_upload_dir").click(function(){
		var default_base_dir = $("#ninja_forms_default_base_upload_dir").val();
		var default_base_url = $("#ninja_forms_default_base_upload_url").val();
		$("#base_upload_dir").val(default_base_dir);
		$("#base_upload_url").val(default_base_url);
	});

	$(".ninja-forms-delete-upload").click(function(e){
		e.preventDefault();
		var answer = confirm("Delete this file?");
		if(answer){
			var upload_id = this.id.replace('delete_upload_', '');
			$.post(ajaxurl, { upload_id: upload_id, action:"ninja_forms_delete_upload"}, function(response){
				//alert(response);
				$("#ninja_forms_upload_" + upload_id + "_tr").css("background-color", "#FF0000").fadeOut("slow", function(){
					$(this).remove();
					if($("#ninja_forms_uploads_tbody tr").length == 0){
						var html = "<tr id='ninja_forms_files_empty' style=''><td colspan='7'>No files found</td></tr>";
						$("#ninja_forms_uploads_tbody").append(html);
					}
				});
			});
		}
	});

	$(".ninja-forms-change-file-upload").click(function(e){
		e.preventDefault();
		var file_upload_id = this.id.replace('ninja_forms_change_file_upload_', '');
		$("#ninja_forms_file_upload_" + file_upload_id).toggle();
	});

	$(document).on( 'click', '.ninja-forms-rename-help', function(event){
		event.preventDefault();
		if( !$("#tab-panel-upload_help").is(":visible") ){
			$("#tab-link-upload_help").find("a").click();
			$("#contextual-help-link").click().focus();	
		}
	});

	/* * * End File Upload Settings JS * * */
});