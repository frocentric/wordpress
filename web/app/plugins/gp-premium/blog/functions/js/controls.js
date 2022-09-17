jQuery( function( $ ) {
	// Featured image controls
	var featuredImageArchiveControls = [
		'generate_blog_settings-post_image',
		'generate_blog_settings-post_image_padding',
		'generate_blog_settings-post_image_position',
		'generate_blog_settings-post_image_alignment',
		'generate_blog_settings-post_image_size',
		'generate_blog_settings-post_image_width',
		'generate_blog_settings-post_image_height',
		'generate_regenerate_images_notice',
	];

	$.each( featuredImageArchiveControls, function( index, value ) {
		$( '#customize-control-' + value ).attr( 'data-control-section', 'featured-image-archives' );
	} );

	var featuredImageSingleControls = [
		'generate_blog_settings-single_post_image',
		'generate_blog_settings-single_post_image_padding',
		'generate_blog_settings-single_post_image_position',
		'generate_blog_settings-single_post_image_alignment',
		'generate_blog_settings-single_post_image_size',
		'generate_blog_settings-single_post_image_width',
		'generate_blog_settings-single_post_image_height',
		'generate_regenerate_single_post_images_notice',
	];

	$.each( featuredImageSingleControls, function( index, value ) {
		$( '#customize-control-' + value ).attr( 'data-control-section', 'featured-image-single' ).css( {
			visibility: 'hidden',
			height: '0',
			width: '0',
			margin: '0',
			overflow: 'hidden',
		} );
	} );

	var featuredImagePageControls = [
		'generate_blog_settings-page_post_image',
		'generate_blog_settings-page_post_image_padding',
		'generate_blog_settings-page_post_image_position',
		'generate_blog_settings-page_post_image_alignment',
		'generate_blog_settings-page_post_image_size',
		'generate_blog_settings-page_post_image_width',
		'generate_blog_settings-page_post_image_height',
		'generate_regenerate_page_images_notice',
	];

	$.each( featuredImagePageControls, function( index, value ) {
		$( '#customize-control-' + value ).attr( 'data-control-section', 'featured-image-page' ).css( {
			visibility: 'hidden',
			height: '0',
			width: '0',
			margin: '0',
			overflow: 'hidden',
		} );
	} );

	// Post meta controls
	var postMetaArchiveControls = [
		'generate_settings-post_content',
		'generate_blog_settings-excerpt_length',
		'generate_blog_settings-read_more',
		'generate_blog_settings-read_more_button',
		'generate_blog_settings-date',
		'generate_blog_settings-author',
		'generate_blog_settings-categories',
		'generate_blog_settings-tags',
		'generate_blog_settings-comments',
		'generate_blog_settings-infinite_scroll',
		'generate_blog_settings-infinite_scroll_button',
		'blog_masonry_load_more_control',
		'blog_masonry_loading_control',
	];

	$.each( postMetaArchiveControls, function( index, value ) {
		$( '#customize-control-' + value ).attr( 'data-control-section', 'post-meta-archives' );
	} );

	var postMetaSingleControls = [
		'generate_blog_settings-single_date',
		'generate_blog_settings-single_author',
		'generate_blog_settings-single_categories',
		'generate_blog_settings-single_tags',
		'generate_blog_settings-single_post_navigation',
	];

	$.each( postMetaSingleControls, function( index, value ) {
		$( '#customize-control-' + value ).attr( 'data-control-section', 'post-meta-single' ).css( {
			visibility: 'hidden',
			height: '0',
			width: '0',
			margin: '0',
			overflow: 'hidden',
		} );
	} );
} );
