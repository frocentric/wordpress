(function ($, window, document) {
    var StickUp = function(elem, opts) {
        var lastScrollTop = 0,
        scroll = 0,
        scrollDir = '',
        scrollDistance = 0,
        active = false,
        bottom = false,
        hold = false,
        disabled = false,
        landscape = false,
        portrait = false,
        stickyHeight = 0,
        outerHeight = 0,
		currentOuterHeight = 0,
        viewportHeight = 0,
        scrollBottom = 0,
        elementOffset = 0,
        elementOffsetBottom = 0,
        $element = $(),
		$body = $( 'body' ),
		stickyDelay = 0,
        topMargin = 0,
        offset = 0,
        $placeholder = $( elem ).clone().css({
			'visibility': 'hidden',
			'display': 'none'
		}).attr({
			id: 'sticky-placeholder',
			'aria-hidden': true,
			itemtype: null,
			itemscope: null,
		}),
        $parent = $(),
        stickpoints = {
            top:0,
            bottom:0,
            custom:[]
        },
        left,

        //defaults
        options = {
            scrollHide: false,
            lazyHeight: 0,
            topMargin: "auto",
            keepInWrapper: false,
            wrapperSelector: '',
            zIndex: 10000,
			namespaceClass: "stuckElement",
			fixedClass: "isStuck",
            disableOn:function(){
                return true;
            },
			transition: "none"
        },

        getTopMargin = function() {
			var wpAdminBar = ( jQuery( '#wpadminbar' ).length > 0 && jQuery( '#wpadminbar' ).css( 'position' ) == 'fixed' ) ? jQuery( '#wpadminbar' ).outerHeight() : 0;
            if (options.topMargin === 'auto') {
                return parseInt( wpAdminBar + $element.css( 'marginTop' ) );
            } else {
                if ( isNaN( options.topMargin ) && options.topMargin.search( "px" ) > 0 ) {
                    return parseInt( wpAdminBar + options.topMargin.replace( "px", "" ) );
                } else if ( ! isNaN( parseInt( options.topMargin ) ) ) {
                    return parseInt( wpAdminBar + options.topMargin );
                } else {
                    void 0;
                    return 0;
                }
            }
        },

        unStick = function() {
            void 0;
			$placeholder.remove();

            $element.removeClass(options.fixedClass)
            .css({
                'max-width': '',
                'margin-top': '',
                'margin-left': '',
                'margin-right': '',
                'position': '',
                'top': '',
                'left': '',
                'right': '',
				'bottom': '',
				'width': '',
				'opacity': '',
				'height': '',
				'overflow': '',
				'-webkit-transform': '',
				'-ms-transform': '',
				'transform': '',
				'-webkit-transition': '',
				'-ms-transition': '',
				'transition': '',
				'visibility': '',
				'z-index': '',
            })
			.removeClass( 'sticky-navigation-transition' )
			.removeClass( 'navigation-transition' )
			.removeClass( 'sticky-nav-scrolling-up' );

			if ( 'sticky-navigation' === $element.attr( 'id' ) ) {
				$element.attr( 'id', 'site-navigation' );
			}

            active = false;
			bottom = false;
			hold = false;

			$element.trigger( 'stickUp:unStick' );
        },

		holdIt = function( forceBottom ) {
			void 0;

			$( $placeholder ).insertAfter( $element ).show().addClass( options.fixedClass );

			var offsetParent = $placeholder.offsetParent();

			if ( forceBottom ) {
				$element.css({
					position: "absolute"
				});

				var topOffset =
					($parent.offset().top + $parent.outerHeight()) //bottom of container
					- offsetParent.offset().top - currentOuterHeight //parent-position - elementHeight
					- parseInt( $parent.css( "paddingBottom" ) );
			}
			void 0;
			void 0;
			$element.css({
				position: "absolute",
				marginTop: topMargin,
				bottom:"",
				left:$placeholder.position().left,
				top: forceBottom? topOffset : $element.offset().top - offsetParent.offset().top - topMargin
			});

			$element.trigger( 'stickUp:holdIt' );
		},

        stickIt = function() {
            void 0;
            active = true;

			if ( options.zIndex ) {
                $element.css( 'z-index', options.zIndex );
			}

			if ( 'fade' == options.transition ) {
				$element.hide();
			}

			if ( 'slide' == options.transition || options.scrollHide ) {
				$element.css({
					'height': '0',
					'overflow': 'hidden',
					'visibility': 'hidden'
				});
			}

			$( $placeholder ).insertAfter( $element ).show().addClass( options.fixedClass );

			if ( $( '.gen-sidebar-nav' ).length ) {
				$placeholder.css( 'height', $element.outerHeight() );
			}

			if ( 'left' == $element.css( 'float' ) || 'right' == $element.css( 'float' ) ) {
				$placeholder.css( 'float', $element.css( 'float' ) );
				$placeholder.attr( 'style', $placeholder.attr( 'style' ) + 'width:auto !important;' );
			}

			if ( 'slide' == options.transition && 'block' == $placeholder.css( 'display' ) ) {
				$element.css({
					'-webkit-transform': 'translateY(-100%)',
					'-ms-transform': 'translateY(-100%)',
					'transform': 'translateY(-100%)',
					'-webkit-transition': 'transform 300ms ease-in-out',
					'-ms-transition': 'transform 300ms ease-in-out',
					'transition': 'transform 300ms ease-in-out'
				});
			}

            $element.addClass(options.fixedClass);

			if ( 'site-navigation' === $element.attr( 'id' ) ) {
				$element.attr( 'id', 'sticky-navigation' );
			}

            var topDistance = -offset;

            $element.css({
                'margin-top': topMargin,
                'position': 'fixed',
                'top': topDistance + 'px',
                'left': '',
                'right': '',
                'bottom': '',
            });

			$element.trigger( 'stickUp:stickIt' );

			if ( 'fade' == options.transition ) {
				$element.fadeIn( 300 );
			}

			if ( 'slide' == options.transition ) {
				$element.one("webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend", function(event) {
					$element.css({
						'visibility': '',
						'height': '',
						'overflow': '',
					});

					setTimeout( function() {
						$element.css({
							'-webkit-transform': 'translateY(0)',
						    '-ms-transform': 'translateY(0)',
						    'transform': 'translateY(0)',
						});
					}, 10 );
				});
			}

			if ( options.scrollHide ) {
				$element.css({
					'height': '',
					'overflow': '',
					'visibility': ''
				});
			}
        },

		syncWidth = function() {
			if ( $placeholder && $placeholder.width() !== $element.outerWidth() ) {
				$element.outerWidth( $placeholder.outerWidth() );
			}
        },

        stickUpScrollHandlerFn = function(event) {
            if ( ! options.disableOn() ) {
                if( !disabled ) {
                    void 0;
                    unStick();
                    disabled = true;
                }
                return;
            } else if ( disabled ) {
                disabled = false;
            }

            if ( options.keepInWrapper && ! $parent.is( ':visible' ) ) {
                return;
            }

            scroll = $( event.target ).scrollTop();
            scrollDir = ( scroll >= lastScrollTop ) ? 'down' : 'up';
            scrollDistance = Math.abs( lastScrollTop - scroll );
            viewportHeight = $( window ).outerHeight();
            scrollBottom = scroll + viewportHeight;
            lastScrollTop = scroll;
            elementOffset = $element.offset().top;
            stickyHeight = parseInt( $element.outerHeight() + topMargin ) + parseInt( $element.css( 'marginBottom' ) );

			if ( ! active && ! hold && ! bottom ) {
				outerHeight = parseInt( $element.outerHeight( true ) );

				if ( ! bottom && ! hold ) {
					stickpoints.top = parseInt($element.offset().top);
				} else {
					stickpoints.top = parseInt($placeholder.offset().top);
					left = parseInt($element.offset().left)+5;
				}
			}

			currentOuterHeight = parseInt( $element.outerHeight() ) + parseInt( $element.css( 'margin-bottom' ) ) + topMargin;

            if ( options.keepInWrapper ) {
                stickpoints.bottom = $parent.offset().top + $parent.outerHeight() - parseInt( $parent.css( 'paddingBottom' ) );
            } else {
                stickpoints.bottom = $( document ).outerHeight();
			}

            elementOffsetBottom = $element.offset().top + currentOuterHeight;

			// Google like reappearance on upward scroll
			if ( options.scrollHide ) {
				offset = stickyHeight + options.lazyHeight; //negative offset for initial hiding
			} else {
				offset = options.lazyHeight;
			}

			if ( 'none' !== options.transition ) {
				stickyDelay = $element.outerHeight() * 2;
			}

			// Update top margin on scroll
			topMargin = ( options.topMargin !== null ) ? getTopMargin() : 0;

			// If our top margin changes (#wpadminbar), update our margin
			if ( active && topMargin !== $element.css( 'margin-top' ) ) {
				$element.css( 'margin-top', topMargin );
			}

			if ( ! active && ! bottom && scroll >= stickpoints.top - topMargin + offset + stickyDelay
			|| bottom && hold && scroll <= elementOffset - topMargin + offset ) {
				void 0;
				stickIt();
				active = true;
				bottom = false;
				hold = false;
			}

			if ( active && ! bottom && scroll >= stickpoints.top - topMargin + offset + ( $element.outerHeight() / 2 ) ) {
				$placeholder.addClass( 'sticky-navigation-transition' );
				$element.addClass( 'sticky-navigation-transition' );
			}

			if ( options.keepInWrapper
			&& parseInt(elementOffsetBottom - topMargin ) !== parseInt( stickpoints.bottom )
			&& scroll >= stickpoints.bottom - currentOuterHeight + offset ) {
				void 0;
				holdIt( true );
				active = false;
				bottom = true;
				hold = true;
			}

			//Calculate lazyHeight and autoHide
			if ( active ) {
				var topValue = parseInt( $element.css( 'top' ) );
				if ( scrollDir === 'up' && topValue !== 0 ) {
					var newTopValue = scrollDistance > -topValue ? 0 : topValue + scrollDistance;
					$element.css( 'top', newTopValue + 'px' );
					$element.addClass( 'sticky-nav-scrolling-up' );
				} else if ( scrollDir === "down" && topValue > -offset ) {
					var newTopValue = scrollDistance > offset + topValue ? -offset : topValue - scrollDistance;
					$element.css( 'top', newTopValue + 'px' );
					$element.removeClass( 'sticky-nav-scrolling-up' );
				}
			}

            //UNSTICK
            if ( ( active || hold || bottom ) && scroll <= stickpoints.top - topMargin ) {
                void 0;
                unStick();
            }

			//RESPONSIVE baby ;-)
			if ( active || hold || bottom ) {
				syncWidth();
			}

        },

        stickUpResponsiveHandlerFn = function( event ){
            if ( hold ) {
                holdIt();
                bottom = false;
            }

            void 0;
            stickUpScrollHandlerFn( event );
        };

        //init
        var initialize = function( elem, opts ) {
            $element = $( elem );

            // adding a class to users div
            $element.addClass( options.namespaceClass );

            //getting options
            if ( opts ) {
                $.extend( true, options, opts );
            }

            topMargin = ( options.topMargin !== null ) ? getTopMargin() : 0;

            if ( options.lazyHeight ) {
                topMargin = topMargin + options.lazyHeight;
			}

            if ( options.keepInWrapper ) {
                if ( options.wrapperSelector !== '' ) {
                    $parent = $element.closest( options.wrapperSelector );
				}

                //if no Wrapper available use offsetParent
                if ( ! $parent.length ) {
                    $parent = $element.parent();
				}
            } else {
                $parent = $body;
            }

            $( window ).on( 'scroll.stickUp', stickUpScrollHandlerFn );
            $( window ).on( 'resize.stickUp', stickUpResponsiveHandlerFn );

            //initial round ;-)
            stickUpScrollHandlerFn( {target: document} );

        };

        initialize.call( this, elem, opts );

		$( elem ).on( 'stickUp:detach', function( opts ) {
			void 0;
			$element = $(this);
			$element.removeClass(options.namespaceClass);
			$placeholder.remove();
			$element.removeClass(options.fixedClass)
			.css({
				maxWidth:"",
				marginTop: "",
				marginLeft:"",
				marginRight:"",
				position: "",
				top: "",
				left: "",
				right: "",
				bottom: "",
				width:""
			});
			active = false;
			bottom = false;
			hold = false;
			disabled = true;
			$( window ).off( 'scroll.stickUp', stickUpScrollHandlerFn );
			$( window ).off( 'resize.stickUp', stickUpResponsiveHandlerFn );
		})
    };

    $.fn.stickUp = function( options ) {
        return this.each(function() {
          new StickUp( this, options );
        });
    };

}(jQuery, window, document));

(function ( $ ) {
	$.fn.GenerateSimpleSticky = function( options ) {
		var settings = $.extend({
			menu: $( this ),
			parent: false,
			scrollHide: false,
			offsetElement: '#wpadminbar',
			disableOn: function() {
				return true;
			},
			transition: "none"
		}, options );

		var body = $( 'body' ),
			parent = null,
			offset = null,
			autoHide = false;

		if ( settings.parent ) {
			parent = settings.parent;
		} else {
			parent = settings.menu.parent();
		}

		if (
			settings.menu.parents( '.site-header' ).length > 0 ||
			settings.menu.parents( '.generate-page-header' ).length > 0 ||
			settings.menu.parents( '.header-wrap' ).length > 0 ||
			settings.menu.parents( '.site-wrapper' ).length > 0
		) {
			parent = body;
		}

		if ( ( body.hasClass( 'nav-right-sidebar' ) || body.hasClass( 'nav-left-sidebar' ) ) && ! $( '.menu-toggle' ).is( ':visible' ) ) {
			settings.menu = $( '.gen-sidebar-nav' );
			parent = $( '.site-content' );

			if ( settings.menu.children().hasClass( 'auto-hide-sticky' ) ) {
				autoHide = true;
			}
		}

		offset = ( jQuery( settings.offsetElement ).length > 0 && jQuery( settings.offsetElement ).css( 'position' ) == 'fixed' ) ? jQuery( settings.offsetElement ).outerHeight() : 0;

		var stickyOptions = {
			scrollHide: settings.scrollHide,
			keepInWrapper: true,
			wrapperSelector: parent,
			fixedClass: 'is_stuck navigation-stick navigation-clone',
			topMargin: 0,
			disableOn: settings.disableOn,
			transition: settings.transition
		};

		settings.menu.stickUp( stickyOptions );
	}
}( jQuery ));

function generateStickyDebounce(func, wait, immediate) {
	var timeout;
	return function() {
		var context = this, args = arguments;
		var later = function() {
			timeout = null;
			if (!immediate) func.apply(context, args);
		};
		var callNow = immediate && !timeout;
		clearTimeout(timeout);
		timeout = setTimeout(later, wait);
		if (callNow) func.apply(context, args);
	};
};

jQuery( function( $ ) {
	var resizeEvent = 'onorientationchange' in window ? 'orientationchange' : 'resize',
		body = $( 'body' ),
		transition = 'none';

	if ( body.hasClass( 'sticky-enabled' ) ) {

		var navigation = $( '#site-navigation' );

		var navigationDisableOn = function() {
			var body = $( 'body' ),
				mobileHeader = $( '#mobile-header' ),
				mobile = $( '.menu-toggle' );

			if ( body.hasClass( 'desktop-sticky-menu' ) && mobile.is( ':visible' ) ) {
				return false;
			}

			if ( body.hasClass( 'mobile-sticky-menu' ) && ! mobile.is( ':visible' ) ) {
				return false;
			}

			if ( body.hasClass( 'mobile-header' ) && mobileHeader.is( ':visible' ) ) {
				return false;
			}

			return true;
		}

		if ( body.hasClass( 'sticky-menu-fade' ) ) {
			transition = 'fade';
		}

		if ( body.hasClass( 'sticky-menu-slide' ) ) {
			transition = 'slide';
		}

		var autoHide = ( navigation.hasClass( 'auto-hide-sticky' ) ) ? true : false;

		var options = {
			transition: transition,
			scrollHide: autoHide,
			disableOn: navigationDisableOn
		};

		$( navigation ).GenerateSimpleSticky( options );

		body.on( 'generate_navigation_location_updated', function() {
			navigation.trigger( 'stickUp:detach' );
			setTimeout(function() {
				$( navigation ).GenerateSimpleSticky( options );
			}, 250);
		});
	}

	if ( body.hasClass( 'mobile-header' ) && body.hasClass( 'mobile-header-sticky' ) ) {

		var mobileHeader = $( '#mobile-header' );

		mobileHeader.GenerateSimpleSticky({
			scrollHide: ( mobileHeader.data( 'auto-hide-sticky' ) || '' === mobileHeader.data( 'auto-hide-sticky' ) ) ? true : false,
			disableOn: function() {
				if ( ! mobileHeader.is( ':visible' ) ) {
					return false;
				}
				return true;
			}
		});
	}

	var sidebarNavClone = $( '.sidebar-nav-mobile' ),
		sidebarNav = $( '.gen-sidebar-nav' ),
		windowWidth = $( window ).width();

	var checkSidebarNav = generateStickyDebounce( function() {
		if ( windowWidth !== $( window ).width() ) {
			if ( sidebarNavClone.is( ':visible' ) ) {
				sidebarNav.trigger( 'stickUp:detach' );
				sidebarNavClone.trigger( 'stickUp:detach' );
				sidebarNavClone.GenerateSimpleSticky( options );
			}

			if ( sidebarNav.is( ':visible' ) ) {
				sidebarNavClone.trigger( 'stickUp:detach' );
				sidebarNav.trigger( 'stickUp:detach' );
				sidebarNav.GenerateSimpleSticky( options );
			}
		}
	}, 250);

	if ( $( 'body' ).hasClass( 'sticky-enabled' ) && sidebarNavClone.length ) {
		window.addEventListener( 'resize', checkSidebarNav );
		window.addEventListener( 'orientationchange', checkSidebarNav );
	}

	// Weak workaround for iOS bug when using a focused form in a sticky element.
	if ( navigator.userAgent.match( /(iPod|iPhone|iPad)/ ) ) {
		if ( body.hasClass( 'sticky-enabled' ) || ( body.hasClass( 'mobile-header' ) && body.hasClass( 'mobile-header-sticky' ) ) ) {
			$( document ).on( 'focus', '.navigation-stick .search-field', function() {
				$( 'html, body' ).animate({ scrollTop: 0 }, 'fast' );
		  	} );
		}
	}

	var closeMobileMenu = function( item ) {
		var _this = item,
			url = _this.getAttribute( 'href' );

		if ( _this.pathname === window.location.pathname && '#' !== url ) {
			if ( _this.closest( 'nav' ).classList.contains( 'toggled' ) ) {
				_this.closest( 'nav' ).classList.remove( 'toggled' );

				if ( $( 'html' ).hasClass( 'mobile-menu-open' ) ) {
					$( 'html' ).removeClass( 'mobile-menu-open' );
				}
			}
		}
	};

	if ( body.hasClass( 'both-sticky-menu' ) || body.hasClass( 'mobile-sticky-menu' ) ) {
		$( '.main-navigation:not(#mobile-header) ul a[href*=\\#]' ).on( 'click', function( event ) {
			closeMobileMenu( this );
		} );
	}

	if ( body.hasClass( 'mobile-header-sticky' ) ) {
		$( '#mobile-header ul a[href*=\\#]' ).on( 'click', function( event ) {
			closeMobileMenu( this );
		} );
	}
});
