jQuery(function () {
	var pageNum = parseInt(jQuery('.wpsp-load-more a').attr('data-page')) + 1;
	var max = parseInt(jQuery('.wpsp-load-more a').attr('data-pages'));
	if (pageNum > max) jQuery('.wpsp-load-more').remove();
	var nextLink = jQuery('.wpsp-load-more a').attr('data-link');
	jQuery('.wpsp-load-more a').click(function () {
		loading = false;
		if (pageNum <= max && !loading) {
			loading = true;
			jQuery(this).html(ajaxpagination.loading);
			jQuery.get(nextLink, function (data) {
				pageNum++;
				if (nextLink.indexOf("paged=") > 0) nextLink = nextLink.replace(/paged=[0-9]*/, 'paged=' + pageNum);
				else nextLink = nextLink.replace(/\/page\/[0-9]*/, '/page/' + pageNum);
				var items = Array();
				var $newItems = jQuery('.wp-show-posts-single', data);
				console.log($newItems);
				$newItems.imagesLoaded(function () {
					if ( jQuery('.wp-show-posts-masonry')[0] ) {
						jQuery('.wp-show-posts-masonry').append($newItems).masonry('appended', $newItems );
						jQuery(window).resize();
					} else {
						//jQuery('.wp-show-posts').append($newItems).fadeIn(999);
						$newItems.hide().appendTo( '.wp-show-posts' ).fadeIn(500);
					}

					setTimeout(function () {
						loading = false;
						jQuery('.wpsp-load-more a').html(ajaxpagination.more);
						if ( jQuery('.wp-show-posts-masonry')[0] ) {
							jQuery('.wp-show-posts-masonry').masonry('reloadItems');
						}
						jQuery(window).resize();
						if (pageNum > max) jQuery('.wpsp-load-more').fadeOut(500).remove();
					}, 500);
					
					if ( 'object' === typeof _gaq ) {
						_gaq.push( [ '_trackPageview', nextLink ] );
					}
					if ( 'function' === typeof ga ) {
						ga( 'send', 'pageview', nextLink );
					}
					
					jQuery('body').trigger('wpsp_items_loaded');
					
				});
			});
		} else {}
		return false;
	});
});

