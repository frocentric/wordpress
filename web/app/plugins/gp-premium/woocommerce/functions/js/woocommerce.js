jQuery( document ).ready( function( $ ) {
	var throttle = function(fn, threshhold, scope) {
		threshhold || (threshhold = 250);
		var last,
			deferTimer;

		return function () {
			var context = scope || this;

			var now = +new Date,
				args = arguments;

			if (last && now < last + threshhold) {
				// hold on to it
				clearTimeout(deferTimer);
				deferTimer = setTimeout(function () {
					last = now;
					fn.apply(context, args);
				}, threshhold);
			} else {
				last = now;
				fn.apply(context, args);
			}
		};
	};

	$( '.wc-has-gallery .wc-product-image' ).hover(
		function() {
			$( this ).find( '.secondary-image' ).css( 'opacity','1' );
		}, function() {
			$( this ).find( '.secondary-image' ).css( 'opacity','0' );
		}
	);

	$( 'body' ).on( 'added_to_cart', function() {
		if ( ! $( '.wc-menu-item' ).hasClass( 'has-items' ) ) {
			$( '.wc-menu-item' ).addClass( 'has-items' );
		}

		if ( ! $( '.wc-mobile-cart-items' ).hasClass( 'has-items' ) ) {
			$( '.wc-mobile-cart-items' ).addClass( 'has-items' );
		}
	} );

	$( 'body' ).on( 'removed_from_cart', function() {
		var numberOfItems = $( '.number-of-items' );

		if ( numberOfItems.length ) {
			if ( numberOfItems.hasClass( 'no-items' ) ) {
				$( '.wc-menu-item' ).removeClass( 'has-items' );
				$( '.wc-mobile-cart-items' ).removeClass( 'has-items' );
			}
		}
	} );

	if ( generateWooCommerce.addToCartPanel ) {
		$( document.body ).on( "added_to_cart", function() {
			var adminBar = $( '#wpadminbar' ),
				stickyNav = $( '.navigation-stick' ),
				top = 0;

			if ( adminBar.length ) {
				top = adminBar.outerHeight();
			}

			if ( stickyNav.length && '0px' === stickyNav.css( 'top' ) ) {
				top = top + stickyNav.outerHeight();
			}

			$( '.add-to-cart-panel' ).addClass( 'item-added' ).css( {
				'-webkit-transform': 'translateY(' + top + 'px)',
				'-ms-transform': 'translateY(' + top + 'px)',
				'transform': 'translateY(' + top + 'px)'
			} );
		} );

		$( '.add-to-cart-panel .continue-shopping' ).on( 'click', function( e ) {
			e.preventDefault();

			$( '.add-to-cart-panel' ).removeClass( 'item-added' ).css( {
				'-webkit-transform': 'translateY(-100%)',
				'-ms-transform': 'translateY(-100%)',
				'transform': 'translateY(-100%)'
			} );
		} );

		$( window ).on( 'scroll', throttle( function() {
			var panel = $( '.add-to-cart-panel' );

			if ( panel.hasClass( 'item-added' ) ) {
				panel.removeClass( 'item-added' ).css( {
					'-webkit-transform': 'translateY(-100%)',
					'-ms-transform': 'translateY(-100%)',
					'transform': 'translateY(-100%)'
				} );
			}
		}, 250 ) );
	}

	if ( generateWooCommerce.stickyAddToCart ) {
		var lastScroll = 0;
		$( window ).on( 'scroll', throttle( function() {
			var adminBar = $( '#wpadminbar' ),
				stickyNav = $( '.navigation-stick' ),
				top = 0,
				scrollTop = $( window ).scrollTop(),
				panel = $( '.add-to-cart-panel' ),
				panelPosition = panel.offset().top + panel.outerHeight(),
				button = $( '.single_add_to_cart_button' ),
				buttonTop = button.offset().top,
				buttonHeight = button.outerHeight(),
				footerTop = $( '.site-footer' ).offset().top;

			if ( adminBar.length ) {
				top = adminBar.outerHeight();
			}

			if ( stickyNav.length ) {
				if ( stickyNav.hasClass( 'auto-hide-sticky' ) ) {
					if ( scrollTop < lastScroll && '0px' === stickyNav.css( 'top' ) ) {
						top = top + stickyNav.outerHeight();
					} else {
						top = top;
					}

					lastScroll = scrollTop;
				} else {
					top = top + stickyNav.outerHeight();
				}
			}

			if ( scrollTop > ( buttonTop + buttonHeight ) && panelPosition < footerTop ) {
				panel.addClass( 'show-sticky-add-to-cart' ).css( {
					'-webkit-transform': 'translateY(' + top + 'px)',
					'-ms-transform': 'translateY(' + top + 'px)',
					'transform': 'translateY(' + top + 'px)'
				} );
		    } else {
				panel.removeClass( 'show-sticky-add-to-cart' ).css( {
					'-webkit-transform': '',
					'-ms-transform': '',
					'transform': ''
				} );
			}
		}, 250 ) );

		$( '.go-to-variables' ).on( 'click', function( e ) {
			e.preventDefault();

			var offset = 0,
				stickyNav = $( '.navigation-stick' ),
				adminBar = $( '#wpadminbar' );

			if ( stickyNav.length ) {
				offset = stickyNav.outerHeight();
			}

			if ( adminBar.length ) {
				offset = offset + adminBar.outerHeight();
			}

			$( 'html, body' ).animate({
				scrollTop: $( '.variations' ).offset().top - offset
			}, 250 );
		} );
	}

	$( document ).on( 'ready', function() {
		"use strict";

		if ( generateWooCommerce.quantityButtons ) {
			generateQuantityButtons();
		}
	} );

	$( document ).ajaxComplete( function() {
		"use strict";

		if ( generateWooCommerce.quantityButtons ) {
			generateQuantityButtons();
		}
	} );

	function generateQuantityButtons( quantitySelector ) {
		var quantityBoxes,
			cart = $( '.woocommerce div.product form.cart' );

		if ( cart.closest( '.elementor-add-to-cart' ).length ) {
			$( '.elementor.product' ).removeClass( 'do-quantity-buttons' );
			return;
		}

		if ( ! quantitySelector ) {
			quantitySelector = '.qty';
		}

		quantityBoxes = $( 'div.quantity:not(.buttons-added), td.quantity:not(.buttons-added)' ).find( quantitySelector );

		if ( quantityBoxes && 'date' !== quantityBoxes.prop( 'type' ) && 'hidden' !== quantityBoxes.prop( 'type' ) ) {

			// Add plus and minus icons
			quantityBoxes.parent().addClass( 'buttons-added' ).prepend('<a href="javascript:void(0)" class="minus">-</a>');
	        quantityBoxes.after('<a href="javascript:void(0)" class="plus">+</a>');

			// Target quantity inputs on product pages
			$( 'input' + quantitySelector + ':not(.product-quantity input' + quantitySelector + ')' ).each( function() {
				var min = parseFloat( $( this ).attr( 'min' ) );

				if ( min && min > 0 && parseFloat( $( this ).val() ) < min ) {
					$( this ).val( min );
				}
			});

			// Quantity input
			if ( $( 'body' ).hasClass( 'single-product' ) && ! cart.hasClass( 'grouped_form' ) ) {
				var quantityInput = $( '.woocommerce form input[type=number].qty' );
				quantityInput.on( 'keyup', function() {
					var qty_val = $( this ).val();
					quantityInput.val( qty_val );
				});
			}

			$( '.plus, .minus' ).unbind( 'click' );

			$( '.plus, .minus' ).on( 'click', function() {

				// Quantity
				var quantityBox;

				// If floating bar is enabled
				if ( $( 'body' ).hasClass( 'single-product' ) && ! cart.hasClass( 'grouped_form' ) && ! cart.hasClass( 'cart_group' ) ) {
					quantityBox = $( '.plus, .minus' ).closest( '.quantity' ).find( quantitySelector );
				} else {
					quantityBox = $( this ).closest( '.quantity' ).find( quantitySelector );
				}

				// Get values
				var currentQuantity = parseFloat( quantityBox.val() ),
				    maxQuantity     = parseFloat( quantityBox.attr( 'max' ) ),
				    minQuantity     = parseFloat( quantityBox.attr( 'min' ) ),
				    step            = quantityBox.attr( 'step' );

				// Fallback default values
				if ( ! currentQuantity || '' === currentQuantity  || 'NaN' === currentQuantity ) {
					currentQuantity = 0;
				}

				if ( '' === maxQuantity || 'NaN' === maxQuantity ) {
					maxQuantity = '';
				}

				if ( '' === minQuantity || 'NaN' === minQuantity ) {
					minQuantity = 0;
				}

				if ( 'any' === step || '' === step  || undefined === step || 'NaN' === parseFloat( step )  ) {
					step = 1;
				}

				// Change the value
				if ( $( this ).is( '.plus' ) ) {

					if ( maxQuantity && ( maxQuantity == currentQuantity || currentQuantity > maxQuantity ) ) {
						quantityBox.val( maxQuantity );
					} else {
						quantityBox.val( currentQuantity + parseFloat( step ) );
					}

				} else {

					if ( minQuantity && ( minQuantity == currentQuantity || currentQuantity < minQuantity ) ) {
						quantityBox.val( minQuantity );
					} else if ( currentQuantity > 0 ) {
						quantityBox.val( currentQuantity - parseFloat( step ) );
					}

				}

				// Trigger change event
				quantityBox.trigger( 'change' );

			} );
		}
	}
});
