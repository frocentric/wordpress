jQuery( function( $ ) {
	var debounce = function( callback, wait ) {
		var timeout;

		return function() {
			clearTimeout( timeout );

			timeout = setTimeout( function() {
				timeout = undefined;
				callback.call();
			}, wait );
		};
	};

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
		$( document.body ).on( 'added_to_cart', function() {
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
				transform: 'translateY(' + top + 'px)',
			} );
		} );

		$( '.add-to-cart-panel .continue-shopping' ).on( 'click', function( e ) {
			e.preventDefault();

			$( '.add-to-cart-panel' ).removeClass( 'item-added' ).css( {
				'-webkit-transform': 'translateY(-100%)',
				'-ms-transform': 'translateY(-100%)',
				transform: 'translateY(-100%)',
			} );
		} );

		$( window ).on( 'scroll', debounce( function() {
			var panel = $( '.add-to-cart-panel' );

			if ( panel.hasClass( 'item-added' ) ) {
				panel.removeClass( 'item-added' ).css( {
					'-webkit-transform': 'translateY(-100%)',
					'-ms-transform': 'translateY(-100%)',
					transform: 'translateY(-100%)',
				} );
			}
		}, 250 ) );
	}

	if ( generateWooCommerce.stickyAddToCart ) {
		var lastScroll = 0;
		var scrollDownTimeout = 300;

		$( window ).on( 'scroll', debounce( function() {
			var adminBar = $( '#wpadminbar' ),
				stickyNav = $( '.navigation-stick' ),
				stuckElement = $( '.stuckElement' ),
				top = 0,
				scrollTop = $( window ).scrollTop(),
				panel = $( '.add-to-cart-panel' ),
				panelPosition = panel.offset().top + panel.outerHeight(),
				button = $( '.single_add_to_cart_button' ),
				buttonTop = button.offset().top,
				buttonHeight = button.outerHeight(),
				footerTop = $( '.site-footer' ).offset().top;

			if ( stuckElement.length === 0 ) {
				scrollDownTimeout = 0;
			}

			if ( scrollTop > ( buttonTop + buttonHeight ) && panelPosition < footerTop ) {
				setTimeout( function() {
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

					panel.addClass( 'show-sticky-add-to-cart' ).css( {
						'-webkit-transform': 'translateY(' + top + 'px)',
						'-ms-transform': 'translateY(' + top + 'px)',
						transform: 'translateY(' + top + 'px)',
					} );
				}, scrollDownTimeout );
			} else {
				panel.removeClass( 'show-sticky-add-to-cart' ).css( {
					'-webkit-transform': '',
					'-ms-transform': '',
					transform: '',
				} );
			}
		}, 50 ) );

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

			$( 'html, body' ).animate( {
				scrollTop: $( '.variations' ).offset().top - offset,
			}, 250 );
		} );
	}

	$( function() {
		'use strict';

		if ( generateWooCommerce.quantityButtons ) {
			generateQuantityButtons();
		}
	} );

	$( document ).ajaxComplete( function() {
		'use strict';

		if ( generateWooCommerce.quantityButtons ) {
			generateQuantityButtons();
		}
	} );

	function generateQuantityButtons() {
		// Check if we have an overwrite hook for this function
		try {
			return generateWooCommerce.hooks.generateQuantityButtons();
		} catch ( e ) {
			// No hook in place, carry on
		}

		// Grab the FIRST available cart form on the page
		var cart = $( '.woocommerce div.product form.cart' ).first();

		// Check if we see elementor style classes
		if ( cart.closest( '.elementor-add-to-cart' ).length ) {
			// Found classes, remove them and finish here
			$( '.elementor.product' ).removeClass( 'do-quantity-buttons' );
			return;
		}

		// Grab all the quantity boxes that need dynamic buttons adding
		var quantityBoxes;

		try {
			// Is there a hook available?
			quantityBoxes = generateWooCommerce.selectors.generateQuantityButtons.quantityBoxes;
		} catch ( e ) {
			// Use the default plugin selector functionality
			quantityBoxes = $( '.cart div.quantity:not(.buttons-added), .cart td.quantity:not(.buttons-added)' ).find( '.qty' );
		}

		// Test the elements have length and greater than 0
		// Try, catch here to provide basic error checking on hooked data
		try {
			// Nothing found... stop here
			if ( quantityBoxes.length === 0 ) {
				return false;
			}
		} catch ( e ) {
			return false;
		}

		// Allow the each loop callback to be completely overwritten
		var quantityBoxesCallback;

		try {
			// Try assign a hooked callback
			quantityBoxesCallback = generateWooCommerce.callbacks.generateQuantityButtons.quantityBoxes;
		} catch ( e ) {
			// Use the default callback handler
			quantityBoxesCallback = function( key, value ) {
				var box = $( value );

				// Check allowed types
				if ( [ 'date', 'hidden' ].indexOf( box.prop( 'type' ) ) !== -1 ) {
					return;
				}

				// Add plus and minus icons
				box.parent().addClass( 'buttons-added' ).prepend( '<a href="javascript:void(0)" class="minus">-</a>' );
				box.after( '<a href="javascript:void(0)" class="plus">+</a>' );

				// Enforce min value on the input
				var min = parseFloat( $( this ).attr( 'min' ) );

				if ( min && min > 0 && parseFloat( $( this ).val() ) < min ) {
					$( this ).val( min );
				}

				// Add event handlers to plus and minus (within this scope)
				box.parent().find( '.plus, .minus' ).on( 'click', function() {
					// Get values
					var currentQuantity = parseFloat( box.val() ),
						maxQuantity = parseFloat( box.attr( 'max' ) ),
						minQuantity = parseFloat( box.attr( 'min' ) ),
						step = box.attr( 'step' );

					// Fallback default values
					if ( ! currentQuantity || '' === currentQuantity || 'NaN' === currentQuantity ) {
						currentQuantity = 0;
					}

					if ( '' === maxQuantity || 'NaN' === maxQuantity ) {
						maxQuantity = '';
					}

					if ( '' === minQuantity || 'NaN' === minQuantity ) {
						minQuantity = 0;
					}

					if ( 'any' === step || '' === step || undefined === step || 'NaN' === parseFloat( step ) ) {
						step = 1;
					}

					if ( $( this ).is( '.plus' ) ) {
						if ( maxQuantity && ( maxQuantity === currentQuantity || currentQuantity > maxQuantity ) ) {
							box.val( maxQuantity );
						} else {
							box.val( currentQuantity + parseFloat( step ) );
						}
					} else if ( minQuantity && ( minQuantity === currentQuantity || currentQuantity < minQuantity ) ) {
						box.val( minQuantity );
					} else if ( currentQuantity > 0 ) {
						box.val( currentQuantity - parseFloat( step ) );
					}

					// Trigger change event
					box.trigger( 'change' );
				} );
			};
		}

		$.each( quantityBoxes, quantityBoxesCallback );
	}
} );
