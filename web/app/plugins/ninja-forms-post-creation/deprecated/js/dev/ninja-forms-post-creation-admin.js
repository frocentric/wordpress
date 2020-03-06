jQuery(document).ready(function($) {

	/* * * Begin Front End Posting JS * * */
	$(document).on( 'change', '.ninja-forms-post-tax', function(){
		if(this.checked){
			jQuery("#post_tax_" + this.value).show();
		}else{
			jQuery("#post_tax_" + this.value).find(":checkbox").prop("checked", false);
			jQuery("#post_tax_" + this.value).hide();
		}
	});

	$(document).on( 'change', '.ninja-forms-post-type', function(){
		var post_type = this.value;
		var form_id = $("#_form_id").val();
		$("#post_tax_div").prop("innerHTML", "");
		$("#post_tax_loader").show();

		$.post(ajaxurl, { form_id: form_id, post_type: post_type, action:"ninja_forms_post_return_tax"}, function(response){
			$("#post_tax_loader").hide();
			$("#post_tax_div").prop("innerHTML", response);
		});
	});

	$("#ninja_forms_toggle_post_content").click(function(event){
		event.preventDefault();
		$("#post_content_div").toggle();
	});

	$(document).on( 'change', '.ninja-forms-post-meta-value', function(){
		var custom_meta = this.id.replace("_post_meta_value", "_custom_post_meta_value");
		if(this.value == "custom"){
			$("#" + custom_meta).val('');
			$("#" + custom_meta).show();
			$("#" + custom_meta).focus();
		}else{
			$("#" + custom_meta).hide();
			$("#" + custom_meta).val(this.value);
		}
	});

	/* * * End Front End Posting JS * * */
});