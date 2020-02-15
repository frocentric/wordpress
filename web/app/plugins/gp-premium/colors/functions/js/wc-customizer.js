/**
 * WooCommerce link color
 */
generate_colors_live_update( 'wc_product_title_color', '.woocommerce ul.products li.product .woocommerce-LoopProduct-link', 'color', '', 'link_color' );
generate_colors_live_update( 'wc_product_title_color_hover', '.woocommerce ul.products li.product .woocommerce-LoopProduct-link:hover', 'color', '', 'link_color_hover' );

/**
 * WooCommerce primary button
 */
var wc_button = '.woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button, button, \
	html input[type="button"], \
	input[type="reset"], \
	input[type="submit"],\
	.button,\
	.button:visited';
generate_colors_live_update( 'form_button_background_color', wc_button, 'background-color' );
generate_colors_live_update( 'form_button_text_color', wc_button, 'color' );

/**
 * WooCommerce primary button hover
 */
var wc_button_hover = '.woocommerce #respond input#submit:hover, .woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce input.button:hover,button:hover, \
	html input[type="button"]:hover, \
	input[type="reset"]:hover, \
	input[type="submit"]:hover,\
	.button:hover,\
	button:focus, \
	html input[type="button"]:focus, \
	input[type="reset"]:focus, \
	input[type="submit"]:focus,\
	.button:focus';
generate_colors_live_update( 'form_button_background_color_hover', wc_button_hover, 'background-color' );
generate_colors_live_update( 'form_button_text_color_hover', wc_button_hover, 'color' );

/**
 * WooCommerce alt button
 */
var wc_alt_button = '.woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt';
generate_colors_live_update( 'wc_alt_button_background', wc_alt_button, 'background-color' );
generate_colors_live_update( 'wc_alt_button_text', wc_alt_button, 'color' );

/**
 * WooCommerce alt button hover
 */
var wc_alt_button_hover = '.woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover';
generate_colors_live_update( 'wc_alt_button_background_hover', wc_alt_button_hover, 'background-color' );
generate_colors_live_update( 'wc_alt_button_text_hover', wc_alt_button_hover, 'color' );

/**
 * WooCommerce star ratings
 */
var wc_stars = '.woocommerce .star-rating span:before, .woocommerce .star-rating:before';
generate_colors_live_update( 'wc_rating_stars', wc_stars, 'color' );

/**
 * WooCommerce sale sticker
 */
var wc_sale_sticker = '.woocommerce span.onsale';
generate_colors_live_update( 'wc_sale_sticker_background', wc_sale_sticker, 'background-color' );
generate_colors_live_update( 'wc_sale_sticker_text', wc_sale_sticker, 'color' );

/**
 * WooCommerce price
 */
var wc_price = '.woocommerce ul.products li.product .price, .woocommerce div.product p.price';
generate_colors_live_update( 'wc_price_color', wc_price, 'color' );

/**
 * WooCommerce product tab text
 */
var wc_product_tab = '.woocommerce div.product .woocommerce-tabs ul.tabs li a';
generate_colors_live_update( 'wc_product_tab', wc_product_tab, 'color' );

/**
 * WooCommerce product tab text highlight/active
 */
var wc_product_tab_active = '.woocommerce div.product .woocommerce-tabs ul.tabs li a:hover, .woocommerce div.product .woocommerce-tabs ul.tabs li.active a';
generate_colors_live_update( 'wc_product_tab_highlight', wc_product_tab_active, 'color' );

/**
 * WooCommerce success message
 */
var wc_success_message = '.woocommerce-message';
generate_colors_live_update( 'wc_success_message_background', wc_success_message, 'background-color' );
generate_colors_live_update( 'wc_success_message_text', wc_success_message + ', div.woocommerce-message a.button, div.woocommerce-message a.button:focus, div.woocommerce-message a.button:hover, div.woocommerce-message a, div.woocommerce-message a:focus, div.woocommerce-message a:hover', 'color' );

/**
 * WooCommerce info message
 */
var wc_info_message = '.woocommerce-info';
generate_colors_live_update( 'wc_info_message_background', wc_info_message, 'background-color' );
generate_colors_live_update( 'wc_info_message_text', wc_info_message + ', div.woocommerce-info a.button, div.woocommerce-info a.button:focus, div.woocommerce-info a.button:hover, div.woocommerce-info a, div.woocommerce-info a:focus, div.woocommerce-info a:hover', 'color' );

/**
 * WooCommerce error message
 */
var wc_error_message = '.woocommerce-error';
generate_colors_live_update( 'wc_error_message_background', wc_error_message, 'background-color' );
generate_colors_live_update( 'wc_error_message_text', wc_error_message + ', div.woocommerce-error a.button, div.woocommerce-error a.button:focus, div.woocommerce-error a.button:hover, div.woocommerce-error a, div.woocommerce-error a:focus, div.woocommerce-error a:hover', 'color' );

/**
 * Menu Mini Cart
 */
generate_colors_live_update( 'wc_mini_cart_background_color', '#wc-mini-cart', 'background-color' );
generate_colors_live_update( 'wc_mini_cart_text_color', '#wc-mini-cart,#wc-mini-cart a:not(.button), #wc-mini-cart a.remove', 'color' );

generate_colors_live_update( 'wc_mini_cart_button_background', '#wc-mini-cart .button.checkout', 'background-color' );
generate_colors_live_update( 'wc_mini_cart_button_text', '#wc-mini-cart .button.checkout', 'color' );

generate_colors_live_update( 'wc_mini_cart_button_background_hover', '#wc-mini-cart .button.checkout:hover, #wc-mini-cart .button.checkout:focus, #wc-mini-cart .button.checkout:active', 'background-color' );
generate_colors_live_update( 'wc_mini_cart_button_text_hover', '#wc-mini-cart .button.checkout:hover, #wc-mini-cart .button.checkout:focus, #wc-mini-cart .button.checkout:active', 'color' );

/**
 * Sticky panel cart button
 */
 generate_colors_live_update( 'wc_panel_cart_background_color', '.add-to-cart-panel', 'background-color' );
 generate_colors_live_update( 'wc_panel_cart_text_color', '.add-to-cart-panel, .add-to-cart-panel a:not(.button)', 'color' );

 generate_colors_live_update( 'wc_panel_cart_button_background', '#wc-sticky-cart-panel .button', 'background-color' );
 generate_colors_live_update( 'wc_panel_cart_button_text', '#wc-sticky-cart-panel .button', 'color' );

 generate_colors_live_update( 'wc_panel_cart_button_background_hover', '#wc-sticky-cart-panel .button:hover, #wc-sticky-cart-panel .button:focus, #wc-sticky-cart-panel .button:active', 'background-color' );
 generate_colors_live_update( 'wc_panel_cart_button_text_hover', '#wc-sticky-cart-panel .button:hover, #wc-sticky-cart-panel .button:focus, #wc-sticky-cart-panel .button:active', 'color' );

/**
 * Price slider bar
 */
generate_colors_live_update( 'wc_price_slider_background_color', '.woocommerce .widget_price_filter .price_slider_wrapper .ui-widget-content', 'background-color' );
generate_colors_live_update( 'wc_price_slider_bar_color', '.woocommerce .widget_price_filter .ui-slider .ui-slider-range, .woocommerce .widget_price_filter .ui-slider .ui-slider-handle', 'background-color' );

// Archive product description text
wp.customize( 'generate_settings[text_color]', function( value ) {
	value.bind( function( newval ) {
		if ( ! wp.customize.value('generate_settings[content_text_color]')() ) {
			if ( jQuery( 'style#wc_desc_color' ).length ) {
				jQuery( 'style#wc_desc_color' ).html( '.woocommerce-product-details__short-description{color:' + newval + ';}' );
			} else {
				jQuery( 'head' ).append( '<style id="wc_desc_color">.woocommerce-product-details__short-description{color:' + newval + ';}</style>' );
				setTimeout(function() {
					jQuery( 'style#wc_desc_color' ).not( ':last' ).remove();
				}, 1000);
			}
		}
	} );
} );

wp.customize( 'generate_settings[content_text_color]', function( value ) {
	value.bind( function( newval ) {
		if ( '' == newval ) {
			newval = wp.customize.value('generate_settings[text_color]')();
		}
		if ( jQuery( 'style#wc_desc_color' ).length ) {
			jQuery( 'style#wc_desc_color' ).html( '.woocommerce-product-details__short-description{color:' + newval + ';}' );
		} else {
			jQuery( 'head' ).append( '<style id="wc_desc_color">.woocommerce-product-details__short-description{color:' + newval + ';}</style>' );
			setTimeout(function() {
				jQuery( 'style#wc_desc_color' ).not( ':last' ).remove();
			}, 1000);
		}
	} );
} );
