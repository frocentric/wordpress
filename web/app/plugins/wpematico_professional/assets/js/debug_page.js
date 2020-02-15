jQuery(document).ready(function($){
	jQuery('#include_cammpaigns').change(function(e) {
		if (jQuery('#include_cammpaigns').is(":checked")) {
			jQuery('#debug_page_include_campaigns_div').show();
		} else {
			jQuery('#debug_page_include_campaigns_div').hide();
		}
	});
});