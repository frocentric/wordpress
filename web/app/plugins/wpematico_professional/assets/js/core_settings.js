jQuery(document).ready(function($){

	/* Audios Settings JS Events */
	jQuery('#customupload_audios').click(function() {
		if ( true == jQuery('#customupload_audios').is(':checked')) {
			jQuery('#contanier_audio_ren').fadeIn();
		} else {
			jQuery('#contanier_audio_ren').fadeOut();
		}
	});

	jQuery('#audio_cache').click(function() {
		if ( true == jQuery('#audio_cache').is(':checked') && true == jQuery('#customupload_audios').is(':checked')) {
			jQuery('#contanier_audio_ren').fadeIn();
		} else {
			jQuery('#contanier_audio_ren').fadeOut();
		}
		if ( true == jQuery('#audio_cache').is(':checked')) {
			jQuery('#audio_upload_ranges_div').fadeIn();
		} else {
			jQuery('#audio_upload_ranges_div').fadeOut();
		}
	});

	jQuery('#audio_upload_ranges').click(function() {
		if ( true == jQuery('#audio_upload_ranges').is(':checked')) {
			jQuery('#audio_upload_range_mb_div').fadeIn();
		} else {
			jQuery('#audio_upload_range_mb_div').fadeOut();
		}
	});

	jQuery('#rss_audio').click(function() {
		if ( true == jQuery('#rss_audio').is(':checked')) {
			jQuery('.rss_audio_opt').fadeIn();
		} else {
			jQuery('.rss_audio_opt').fadeOut();
		}
	});

	jQuery('#enable_audio_rename').click(function() {
		if ( true == jQuery('#enable_audio_rename').is(':checked')) {
			jQuery('#no_audio_ren').fadeIn();
		} else {
			jQuery('#no_audio_ren').fadeOut();
		}
	});

	/* Videos Settings JS Events */
	jQuery('#customupload_videos').click(function() {
		if ( true == jQuery('#customupload_videos').is(':checked')) {
			jQuery('#contanier_video_ren').fadeIn();
		} else {
			jQuery('#contanier_video_ren').fadeOut();
		}
	});

	jQuery('#video_cache').click(function() {
		if ( true == jQuery('#video_cache').is(':checked') && true == jQuery('#customupload_videos').is(':checked')) {
			jQuery('#contanier_video_ren').fadeIn();
		} else {
			jQuery('#contanier_video_ren').fadeOut();
		}
		if ( true == jQuery('#video_cache').is(':checked')) {
			jQuery('#video_upload_ranges_div').fadeIn();
		} else {
			jQuery('#video_upload_ranges_div').fadeOut();
		}
	});

	jQuery('#video_upload_ranges').click(function() {
		if ( true == jQuery('#video_upload_ranges').is(':checked')) {
			jQuery('#video_upload_range_mb_div').fadeIn();
		} else {
			jQuery('#video_upload_range_mb_div').fadeOut();
		}
	});
	jQuery('#rss_video').click(function() {
		if ( true == jQuery('#rss_video').is(':checked')) {
			jQuery('.rss_video_opt').fadeIn();
		} else {
			jQuery('.rss_video_opt').fadeOut();
		}
	});

	jQuery('#enable_video_rename').click(function() {
		if ( true == jQuery('#enable_video_rename').is(':checked')) {
			jQuery('#no_video_ren').fadeIn();
		} else {
			jQuery('#no_video_ren').fadeOut();
		}
	});


});
