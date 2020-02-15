jQuery(document).ready(function($) {
	
	jQuery('#hook-dropdown').on('change', function() {
		var id = jQuery(this).children(":selected").attr("id");
		jQuery('#gp_hooks_settings .form-table tr').hide();
		jQuery('#gp_hooks_settings .form-table tr').eq(id).show();
		Cookies.set('remember_hook', $('#hook-dropdown option:selected').attr('id'), { expires: 90, path: '/'});
		
		if ( jQuery('#hook-dropdown').val() == 'all' ) {
			$('#gp_hooks_settings .form-table tr').show();
			Cookies.set('remember_hook', 'none', { expires: 90, path: '/'});
		}
		
	});

	//checks if the cookie has been set
	if( Cookies.get('remember_hook') === null || Cookies.get('remember_hook') === "" || Cookies.get('remember_hook') === "null" || Cookies.get('remember_hook') === "none" || Cookies.get('remember_hook') === undefined )
	{	
		$('#gp_hooks_settings .form-table tr').show();
		Cookies.set('remember_hook', 'none', { expires: 90, path: '/'});
	} else {
		$('#hook-dropdown option[id="' + Cookies.get('remember_hook') + '"]').attr('selected', 'selected');
		$('#gp_hooks_settings .form-table tr').hide();
		$('#gp_hooks_settings .form-table tr').eq(Cookies.get('remember_hook')).show();
	}
	
	var top = $('.sticky-scroll-box').offset().top;
	$(window).scroll(function (event) {
		var y = $(this).scrollTop();
		if (y >= top)
			$('.sticky-scroll-box').addClass('fixed');
		else
			$('.sticky-scroll-box').removeClass('fixed');
			$('.sticky-scroll-box').width($('.sticky-scroll-box').parent().width());
	});

});

