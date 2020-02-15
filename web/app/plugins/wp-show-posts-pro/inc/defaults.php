<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'wpsp_pro_defaults' ) ) {
	add_filter( 'wpsp_defaults', 'wpsp_pro_defaults' );
	/**
	 * Set all of our pro defaults
	 * @since 0.5
	 */
	function wpsp_pro_defaults( $defaults ) {
		$defaults['wpsp_image_lightbox'] = false;
		$defaults['wpsp_image_gallery'] = false;
		$defaults['wpsp_image_overlay_color_static'] = '';
		$defaults['wpsp_image_overlay_color'] = '';
		$defaults['wpsp_image_overlay_icon'] = '';
		$defaults['wpsp_ajax_pagination'] = false;
		$defaults['wpsp_masonry'] = false;
		$defaults['wpsp_social_sharing'] = false;
		$defaults['wpsp_social_sharing_alignment'] = 'right';
		$defaults['wpsp_twitter']	= false;
		$defaults['wpsp_twitter_color'] = '#737373';
		$defaults['wpsp_twitter_color_hover'] = '#0084b4';
		$defaults['wpsp_facebook'] = false;
		$defaults['wpsp_facebook_color'] = '#737373';
		$defaults['wpsp_facebook_color_hover'] = '#2d4372';
		$defaults['wpsp_pinterest'] = false;
		$defaults['wpsp_pinterest_color'] = '#737373';
		$defaults['wpsp_pinterest_color_hover'] = '#bd081c';
		$defaults['wpsp_love'] = false;
		$defaults['wpsp_love_color'] = '#737373';
		$defaults['wpsp_love_color_hover'] = '#ff6863';
		$defaults['wpsp_featured_post'] = false;
		$defaults['wpsp_image_hover_effect'] = '';
		$defaults['wpsp_border'] = '';
		$defaults['wpsp_border_hover'] = '';
		$defaults['wpsp_filter'] = false;
		$defaults['wpsp_background'] = '';
		$defaults['wpsp_background_hover'] = '';
		$defaults['wpsp_title_font_size'] = '';
		$defaults['wpsp_title_color'] = '';
		$defaults['wpsp_title_color_hover'] = '';
		$defaults['wpsp_meta_color'] = '';
		$defaults['wpsp_meta_color_hover'] = '';
		$defaults['wpsp_text'] = '';
		$defaults['wpsp_link'] = '';
		$defaults['wpsp_link_hover'] = '';
		$defaults['wpsp_padding'] = '';
		$defaults['wpsp_read_more_background_color'] = '';
		$defaults['wpsp_read_more_background_color_hover'] = '#222222';
		$defaults['wpsp_read_more_text_color'] = '#222222';
		$defaults['wpsp_read_more_text_color_hover'] = '#FFFFFF';
		$defaults['wpsp_read_more_border_color'] = '#222222';
		$defaults['wpsp_read_more_border_color_hover'] = '#222222';
		$defaults['wpsp_carousel'] = false;
		$defaults['wpsp_carousel_slides'] = 3;
		$defaults['wpsp_carousel_slides_to_scroll'] = 3;
		$defaults['wpsp_section_classes'] = '';
		$defaults['wpsp_cards'] = 'none';

		return apply_filters( 'wpsp_pro_defaults', $defaults );
	}
}
