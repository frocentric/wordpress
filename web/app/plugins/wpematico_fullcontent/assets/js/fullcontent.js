jQuery(document).ready(function($){

	var fullcontent_message = fullcontent_object.fullcontent_message;
	$('#fullcontent-box div.inside p label input[type=checkbox]').click(function(){
		if($(this).hasClass('disabled')==true){
			$(this).attr('checked',false);
			alert(fullcontent_message);
		}
	});
	$('#campaign_type').change(function(){
		campaign_type = $(this).val();
		activade_desactivade_checkfc(campaign_type);
	});

	function activade_desactivade_checkfc(campaign_type){
		if(campaign_type=='youtube')
		{	
			$('#fullcontent-box').find('div.inside p label input[type=checkbox]').addClass('disabled').attr('checked',false);
		}else{
			$('#fullcontent-box').find('div.inside p label input[type=checkbox]').removeClass('disabled');
		}
	}
	campaign_type = $('#campaign_type').val();
	activade_desactivade_checkfc(campaign_type);

});