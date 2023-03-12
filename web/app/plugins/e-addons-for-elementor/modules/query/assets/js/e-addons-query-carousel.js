jQuery(window).on('elementor/frontend/init', () => {
    class WidgetQueryCarouselHandlerClass extends elementorModules.frontend.handlers.Base {
        getDefaultSettings() {
            // e-add-posts-container e-add-posts e-add-skin-grid e-add-skin-grid-masonry
            return {
                selectors: {
                    container: '.e-add-posts-container',
                    containerCarousel: '.e-add-posts-container.e-add-skin-carousel',
                    containerWrapper: '.e-add-posts-wrapper',
                    items: '.e-add-post-item',
                },
            };
        }
        getDefaultElements() {
            const selectors = this.getSettings('selectors');
            return {
                $scope: this.$element,
                $id_scope: this.getID(),
                $container: this.$element.find(selectors.container),
                $containerCarousel: this.$element.find(selectors.containerCarousel),
                $containerWrapper: this.$element.find(selectors.containerWrapper),
                $items: this.$element.find(selectors.items),
                $animationReveal: null,
                $eaddPostsSwiper: null
            };
        }
        bindEvents() {
            this.skinPrefix = this.$element.attr('data-widget_type').split('.').pop() + '_';
            this.isCarouselEnabled = false;
            this.elementSettings = this.getElementSettings();
            this.postId = this.elements.$scope.find('.e-add-carousel-controls').data('post-id');
            let self = this;
            let scope = this.elements.$scope,
                    id_scope = this.elements.$id_scope,
                    widgetType = this.getWidgetType(),
                    eaddPostsSwiper = null;

            if (this.elementSettings.carousel_show_hidden_slides) {
                scope.closest('.elementor-top-section').addClass('e-overflow-hidden');
            }

            //@p per sicurezza se esiste già un'istanza la distruggo! ..
            if (eaddPostsSwiper)
                eaddPostsSwiper.destroy();

            
            //----------------------------------------------
			//https://stackoverflow.com/questions/17165096/custom-events-in-class/17165730
			this.events = new function() {
				var _triggers = {};
			  
				this.on = function(event,callback) {
					if(!_triggers[event])
						_triggers[event] = [];
					_triggers[event].push( callback );
				  }
			  
				this.triggerHandler = function(event,params) {
                    
					if( _triggers[event] ) {
						for( const i in _triggers[event] )
							_triggers[event][i](params);
					}
				}
			};
			
			this.elements.$containerCarousel.data('EcarouselEvent',this.events);
			//----------------------------------------------
            //init
            //@p ho passato da 1 a 0 perché non generare proprio il carosello è un problema in certi cassi, eventialmente lo RIVALLUTIAMO.
            if (this.elements.$items.length > 0) {
                
                setTimeout(() => {
                    //COSTRUCTOR
                    eaddPostsSwiper = new Swiper(this.elements.$containerCarousel[0], this.carouselOptions(id_scope, this.elementSettings));
                    //console.log(eaddPostsSwiper);
                    this.elements.$eaddPostsSwiper = eaddPostsSwiper;

                    if (this.elementSettings[this.skinPrefix + 'useAutoplay'] && this.elementSettings[this.skinPrefix + 'autoplayPauseOnHover']) {
                        scope.on('mouseenter', function (e) {
                            //console.log('stop autoplay');
                            eaddPostsSwiper.autoplay.stop();
                        });
                        scope.on('mouseleave', function (e) {
                            //console.log('start autoplay');
                            eaddPostsSwiper.autoplay.run();
                        });
                    }
                    // if (this.skinPrefix == 'dualslider_') {
                    //     eaddPostsSwiper.controller.control = this.elements.$scope.data('thumbscarousel');
                    //     this.elements.$scope.data('thumbscarousel').controller.control = eaddPostsSwiper;
                    // }

                }, 10);

            } else {
                //@p se il risultato della query ha solo 1 elemento non produco nulla
                scope.find('.e-add-container-navigation').remove();
            }

            //@p memorizzo listanza del carosello in data per sincronizzarla con dualslider o altro
            this.elements.$scope.data('eaddPostsSwiper', eaddPostsSwiper);
            //console.log(this.elements.$scope.data('eaddPostsSwiper'));
        }

        onElementChange(propertyName) {
            //@p importante
            if (this.skinPrefix + 'ratio_image' === propertyName ||
                    this.skinPrefix + 'dualslider_distribution_vertical' === propertyName ||
                    this.skinPrefix + 'dualslider_height_container' === propertyName ||
                    this.skinPrefix + 'height_container' === propertyName
                    ) {
                this.elements.$eaddPostsSwiper.update();

                //console.log(propertyName);
            }
        }

        carouselOptions(id_scope, settings) {
            //@p qui vado a restituire l'oggettoo per configurare loo swiper ;-)
            var self = this;
            //let isLoop = (settings[this.skinPrefix + 'slidesPerView'] >= this.elements.$items.length) ? false : Boolean(settings[this.skinPrefix + 'loop'])
            let isLoop = Boolean(settings[this.skinPrefix + 'loop']);
            /*if (settings[this.skinPrefix + 'loopInsufficientSlides']) {
                if (settings[this.skinPrefix + 'slidesPerView'] >= this.elements.$items.length) {
                    isLoop = false;
                }
            }*/
            var eaddSwiperOptions = {

                direction: String(settings[this.skinPrefix + 'direction_slider']) || 'horizontal',
                //observer: true,
                //observeParents: true,

                speed: Number(settings[this.skinPrefix + 'speed_slider']) || 300,
                autoHeight: Boolean(settings[this.skinPrefix + 'autoHeight']),

                //@p visualizzazioni
                slidesPerView: Number(settings[this.skinPrefix + 'slidesPerView']) || 'auto',
                slidesPerGroup: Number(settings[this.skinPrefix + 'slidesPerGroup']) || 1,
                slidesPerColumn: Number(settings[this.skinPrefix + 'slidesColumn']) || 1,
                
                //@p spaziature
                spaceBetween: Number(settings[this.skinPrefix + 'spaceBetween']) || 0, // 30,
                slidesOffsetBefore: Number(settings[this.skinPrefix + 'slidesOffsetBefore']) || 0,
                slidesOffsetAfter: Number(settings[this.skinPrefix + 'slidesOffsetAfter']) || 0,

                //@p opzioni varie
                slidesPerColumnFill: String(settings[this.skinPrefix + 'slidesPerColumnFill']) || 'row',
                centerInsufficientSlides: Boolean(settings[this.skinPrefix + 'centerInsufficientSlides']),
                watchOverflow: Boolean(settings[this.skinPrefix + 'watchOverflow']),
                centeredSlides: Boolean(settings[this.skinPrefix + 'centeredSlides']),
                centeredSlidesBounds: Boolean(settings[this.skinPrefix + 'centeredSlidesBounds']),
                grabCursor: Boolean(settings[this.skinPrefix + 'grabCursor']),
                watchSlidesVisibility: Boolean(settings[this.skinPrefix + 'watchSlidesVisibility']),
                //alert(eaddSwiperOptions.slidesPerView+' '+this.elements.$items.length)
                loop: isLoop,
                
                //runCallbacksOnInit: true, //@p interessante!..da capire
                //@p per la navigazione con arrows
                navigation: {
                    nextEl: '.next-' + self.getID() + '-' + this.postId,
                    prevEl: '.prev-' + self.getID() + '-' + this.postId,
                },
                //@p i pallini e i simboli
                pagination: {
                    el: '.pagination-' + self.getID() + '-' + this.postId,
                    clickable: true,
                    type: String(settings[this.skinPrefix + 'pagination_type']) || 'bullets',
                    dynamicBullets: Boolean(settings[this.skinPrefix + 'dynamicBullets']),
                    progressbarOpposite: Boolean(settings[this.skinPrefix + 'progressbarOpposite']),
                    renderBullet: (index, className) => {
                        var indexLabel = !Boolean(settings[this.skinPrefix + 'dynamicBullets']) && Boolean(settings[this.skinPrefix + 'bullets_numbers']) ? '<span class="swiper-pagination-bullet-title">' + (index + 1) + '</span>' : '';

                        return '<span class="' + className + '">' + indexLabel + '</span>';
                    },
                    renderFraction: (currentClass, totalClass) => {
                        let fractionSeparator = settings[this.skinPrefix + 'fraction_separator'] || '/';
                        return '<span class="' + currentClass + '"></span>' +
                                '<span class="separator">' + fractionSeparator + '</span>' +
                                '<span class="' + totalClass + '"></span>';
                    },
                    renderProgressbar: (progressbarFillClass) => {
                        return '<span class="' + progressbarFillClass + '"></span>';
                    },
                    /*renderCustom: (swiper, current, total) => {
                     },*/
                },

                //@p gli effetti di scorrimento... 
                // ..... considerare di aggiungere i nuovi cards e creative .......
                effect: settings[this.skinPrefix + 'effects'] || 'slide',
                //@p i parametri degli effetti
                cubeEffect: this.carousel_eff_cube(settings),
                coverflowEffect: this.carousel_eff_coverflow(settings),
                flipEffect: this.carousel_eff_flip(settings),
                fadeEffect: this.carousel_eff_fade(settings),

                //@p scrollbar ..migliorabile
                scrollbar: {
                    el: '.elementor-element-'+id_scope+' .swiper-scrollbar',
                    hide: Boolean(settings[this.skinPrefix + 'scrollbar_hide']),
                    draggable: Boolean(settings[this.skinPrefix + 'scrollbar_draggable']),
                    snapOnRelease: true,
                },

                allowTouchMove: Boolean(settings[this.skinPrefix + 'allowTouchMove']),
                keyboard: {
                    enabled: Boolean(settings[this.skinPrefix + 'keyboardControl']),
                },
                on: {
                    init: function() {
                        this.isCarouselEnabled = true;
                        jQuery('body').attr('data-carousel-' + self.getID(), this.realIndex);
                    },
                    slideChange: function(e){
                        jQuery('body').attr('data-carousel-' + self.getID(), this.realIndex);

                        //l'evento di ritorno....
					    self.events.triggerHandler('carouselChange',this.realIndex);
                    },
                    transitionStart: function(e){
                    //console.log(this);
                        
                    },
                    transitionEnd: function(e){
                        //l'evento di ritorno....
					    self.events.triggerHandler('carouselChangeEnd',this.realIndex);
                    }
                }
            };

            if (settings[this.skinPrefix + 'mousewheelControl']) { 
                eaddSwiperOptions.mousewheel = {
                    //releaseOnEdges: true,
                    forceToAxis: Boolean(settings[this.skinPrefix + 'mousewheelControl_forceToAxis']),
                    invert: Boolean(settings[this.skinPrefix + 'mousewheelControl_invert']),
                }
            }
            //@p opzione per il metodo free
            if (settings[this.skinPrefix + 'freeMode']) {  
                //for swiper 7/8
                eaddSwiperOptions.freeMode = {
                    enabled: Boolean(settings[this.skinPrefix + 'freeMode']) || false,
                    sticky: Boolean(settings[this.skinPrefix + 'freeModeSticky']) || false,
                    minimumVelocity: Number(settings[this.skinPrefix + 'freeModeMinimumVelocity']) || 0.02,
                    momentum: Boolean(settings[this.skinPrefix + 'freeModeMomentum']) || true,
                    momentumBounce: Boolean(settings[this.skinPrefix + 'freeModeMomentumBounce']) || true,
                    momentumBounceRatio: Number(settings[this.skinPrefix + 'freeModeMomentumBounceRatio']) || 1,
                    momentumRatio: Number(settings[this.skinPrefix + 'freeModeMomentumRatio']) || 1,
                    momentumVelocityRatio: Number(settings[this.skinPrefix + 'freeModeMomentumVelocityRatio']) || 1
                }
                // compatibility with swier 6
                eaddSwiperOptions.freeMode = Boolean(settings[this.skinPrefix + 'freeMode']) || false;
                eaddSwiperOptions.freeModeSticky = Boolean(settings[this.skinPrefix + 'freeModeSticky']) || false;
                eaddSwiperOptions.freeModeMinimumVelocity = Number(settings[this.skinPrefix + 'freeModeMinimumVelocity']) || 0.02;
                // optionals momentum "free-mode"
                if(settings[this.skinPrefix + 'freeModeMomentum']){
                    eaddSwiperOptions.freeModeMomentum = Boolean(settings[this.skinPrefix + 'freeModeMomentum']) || true;
                    if(settings[this.skinPrefix + 'freeModeMomentumBounce']) eaddSwiperOptions.freeModeMomentumBounce = Boolean(settings[this.skinPrefix + 'freeModeMomentumBounce']) || false;
                    eaddSwiperOptions.freeModeMomentumBounceRatio = Number(settings[this.skinPrefix + 'freeModeMomentumBounceRatio']) || 1;
                    eaddSwiperOptions.freeModeMomentumRatio = Number(settings[this.skinPrefix + 'freeModeMomentumRatio']) || 1;
                    eaddSwiperOptions.freeModeMomentumVelocityRatio = Number(settings[this.skinPrefix + 'freeModeMomentumVelocityRatio']) || 1;
                }
            }else{
                eaddSwiperOptions.freeMode = false;
            }
            
            //@lo switcher enabele auto per slidesPerView
            if (settings[this.skinPrefix + 'slidesPerView_auto']) {
                eaddSwiperOptions.slidesPerView = 'auto';
            }

            //alert(eaddSwiperOptions.slidesPerView+' - '+eaddSwiperOptions.centeredSlides);
            //@p l'autoplay
            if (settings[this.skinPrefix + 'useAutoplay']) {
                eaddSwiperOptions = jQuery.extend(eaddSwiperOptions, {autoplay: true});
                var autoplayDelay = Number(settings[this.skinPrefix + 'autoplay']);
                if (!autoplayDelay) {
                    autoplayDelay = 3000;
                } else {
                    autoplayDelay = Number(settings[this.skinPrefix + 'autoplay']);
                }
                eaddSwiperOptions = jQuery.extend(eaddSwiperOptions, {
                    autoplay: {
                        delay: autoplayDelay,
                        reverseDirection: Boolean(settings[this.skinPrefix + 'reverseDirection']),
                        disableOnInteraction: Boolean(settings[this.skinPrefix + 'autoplayDisableOnInteraction']),
                        stopOnLastSlide: Boolean(settings[this.skinPrefix + 'autoplayStopOnLast'])
                    }
                });
            }

            //@p il responsive per i valori minimi:
            var elementorBreakpoints = elementorFrontend.config.breakpoints;
            var responsivePoints = eaddSwiperOptions.breakpoints = {};
            responsivePoints[elementorBreakpoints.lg] = {
                slidesPerView: Number(settings[this.skinPrefix + 'slidesPerView']) || 'auto',
                slidesPerGroup: Number(settings[this.skinPrefix + 'slidesPerGroup']) || 1,
                spaceBetween: Number(settings[this.skinPrefix + 'spaceBetween']) || 0,
                slidesPerColumn: Number(settings[this.skinPrefix + 'slidesColumn']) || 1,
                spaceBetween: Number(settings[this.skinPrefix + 'spaceBetween']) || 0,
                slidesOffsetBefore: Number(settings[this.skinPrefix + 'slidesOffsetBefore']) || 0,
                slidesOffsetAfter: Number(settings[this.skinPrefix + 'slidesOffsetAfter']) || 0,
            };
            responsivePoints[elementorBreakpoints.md] = {
                slidesPerView: Number(settings[this.skinPrefix + 'slidesPerView_tablet']) || Number(settings[this.skinPrefix + 'slidesPerView']) || 'auto',
                slidesPerGroup: Number(settings[this.skinPrefix + 'slidesPerGroup_tablet']) || Number(settings[this.skinPrefix + 'slidesPerGroup']) || 1,
                spaceBetween: Number(settings[this.skinPrefix + 'spaceBetween_tablet']) || Number(settings[this.skinPrefix + 'spaceBetween']) || 0,
                slidesPerColumn: Number(settings[this.skinPrefix + 'slidesColumn_tablet']) || Number(settings[this.skinPrefix + 'slidesColumn']) || 1,
                spaceBetween: Number(settings[this.skinPrefix + 'spaceBetween_tablet']) || 0,
                slidesOffsetBefore: Number(settings[this.skinPrefix + 'slidesOffsetBefore_tablet']) || 0,
                slidesOffsetAfter: Number(settings[this.skinPrefix + 'slidesOffsetAfter_tablet']) || 0,
            };
            responsivePoints[elementorBreakpoints.xs] = {
                slidesPerView: Number(settings[this.skinPrefix + 'slidesPerView_mobile']) || Number(settings[this.skinPrefix + 'slidesPerView_tablet']) || Number(settings[this.skinPrefix + 'slidesPerView']) || 'auto',
                slidesPerGroup: Number(settings[this.skinPrefix + 'slidesPerGroup_mobile']) || Number(settings[this.skinPrefix + 'slidesPerGroup_tablet']) || Number(settings[this.skinPrefix + 'slidesPerGroup']) || 1,
                spaceBetween: Number(settings[this.skinPrefix + 'spaceBetween_mobile']) || Number(settings[this.skinPrefix + 'spaceBetween_tablet']) || Number(settings[this.skinPrefix + 'spaceBetween']) || 0,
                slidesPerColumn: Number(settings[this.skinPrefix + 'slidesColumn_mobile']) || Number(settings[this.skinPrefix + 'slidesColumn_tablet']) || Number(settings[this.skinPrefix + 'slidesColumn']) || 1,
                spaceBetween: Number(settings[this.skinPrefix + 'spaceBetween_mobile']) || 0,
                slidesOffsetBefore: Number(settings[this.skinPrefix + 'slidesOffsetBefore_mobile']) || 0,
                slidesOffsetAfter: Number(settings[this.skinPrefix + 'slidesOffsetAfter_mobile']) || 0,
            };
            eaddSwiperOptions = jQuery.extend(eaddSwiperOptions, responsivePoints);

            //@p per il sync con dualslider
            if (this.skinPrefix == 'dualslider_') {
                eaddSwiperOptions = jQuery.extend(eaddSwiperOptions, {thumbs: {
                        swiper: this.elements.$scope.data('thumbscarousel'),
                        multipleActiveThumbs: false,
                        
                    }});
            }
            //console.log(eaddSwiperOptions);
            return eaddSwiperOptions;
        }
        carousel_eff_cube(settings) {
            return {
                shadow: Boolean(settings[this.skinPrefix + 'cube_shadow']),
                slideShadows: Boolean(settings[this.skinPrefix + 'slideShadows']),
                //shadowOffset: 20,
                //shadowScale: 0.94,
            }
        }
        carousel_eff_coverflow(settings) {
            return {
                rotate: 50,
                stretch: Number(settings[this.skinPrefix + 'coverflow_stretch']) || 0,
                depth: 100,
                modifier: Number(settings[this.skinPrefix + 'coverflow_modifier']) || 1,
                slideShadows: Boolean(settings[this.skinPrefix + 'slideShadows']),
            }
        }
        carousel_eff_flip(settings) {
            return {
                rotate: 30,
                slideShadows: Boolean(settings[this.skinPrefix + 'slideShadows']),
                limitRotation: true,
            }
        }
        carousel_eff_fade(settings) {
            return {
                crossFade: Boolean(settings[this.skinPrefix + 'crossFade'])
            }
        }
    }

    const Widget_EADD_Query_carousel_Handler = ($element) => {
        elementorFrontend.elementsHandler.addHandler(WidgetQueryCarouselHandlerClass, {
            $element,
        });
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-posts.carousel', Widget_EADD_Query_carousel_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-users.carousel', Widget_EADD_Query_carousel_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-terms.carousel', Widget_EADD_Query_carousel_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-itemslist.carousel', Widget_EADD_Query_carousel_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-media.carousel', Widget_EADD_Query_carousel_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-repeater.carousel', Widget_EADD_Query_carousel_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-products.carousel', Widget_EADD_Query_carousel_Handler);
    // comments (todo)
    // products (todo)    
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-rest-api.carousel', Widget_EADD_Query_carousel_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-db.carousel', Widget_EADD_Query_carousel_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-data-listing.carousel', Widget_EADD_Query_carousel_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-xml.carousel', Widget_EADD_Query_carousel_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-spreadsheet.carousel', Widget_EADD_Query_carousel_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-rss.carousel', Widget_EADD_Query_carousel_Handler);

});