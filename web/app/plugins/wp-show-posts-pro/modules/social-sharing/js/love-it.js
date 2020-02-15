(function( $ ) {
	'use strict';
	$(document).on('click', '.wpsp-li-button', function( e ) {
		e.preventDefault();
		var button = $(this);
		var post_id = button.attr('data-post-id');
		var security = button.attr('data-nonce');
		var allbuttons;
		allbuttons = $('.wpsp-li-button-'+post_id);
		if (post_id !== '') {
			$.ajax({
				type: 'POST',
				url: wpspLoveIt.ajaxurl,
				data : {
					action : 'wpsp_pro_process_simple_like',
					post_id : post_id,
					nonce : security,
				},	
				success: function(response){
					var icon = response.icon;
					var count = response.count;
					allbuttons.html(icon+count);
					if(response.status === 'unliked') {
						var like_text = wpspLoveIt.like;
						allbuttons.prop('title', like_text);
						allbuttons.removeClass('wpsp-liked');
					} else {
						var unlike_text = wpspLoveIt.unlike;
						allbuttons.prop('title', unlike_text);
						allbuttons.addClass('wpsp-liked');
					}				
				}
			});
			
		}
		return false;
	});
})( jQuery );