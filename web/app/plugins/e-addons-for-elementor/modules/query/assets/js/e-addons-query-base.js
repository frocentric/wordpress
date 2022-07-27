;
class eadd_animationReveal {
    constructor($target, $live) {
        this.target = $target;
        let waypointRevOptions = {
            offset: '100%',
            triggerOnce: $live
        };
        this.items = $target.find('.e-add-post-item');
        elementorFrontend.waypoint(this.items, this.runAnim, waypointRevOptions);
    }
    runAnim(dir) {
        var el = jQuery(this);
        var el_i = jQuery(this).index();

        if (dir == 'down') {
            setTimeout(function () {
                el.addClass('animate');
            }, 100 * el_i);
            // play
        } else if (dir == 'up') {
            el.removeClass('animate');
            // stop
        }
    }
    upgradeItems() {
        this.items = $target.find('.e-add-post-item');
    }
}
class eadd_masonry {

    constructor($target, $id_scope) {
        this.target = $target;

        this.masonryGrid = $target.masonry({
            // options
            itemSelector: '.e-add-post-item-' + $id_scope,
            transitionDuration: 0
        });
        this.isMasonryEnabled = true;

        $target.data('masonry');
        this.masonryGrid.masonry('layout');

        // layout Masonry after each image loads
        this.masonryGrid.imagesLoaded().progress(() => {
            this.masonryGrid.masonry('layout');
        });
    }
    layoutMasonry() {
        this.masonryGrid.masonry('layout');
    }
    instanceMasonry() {
        return $target.data('masonry');
    }
    removeMasonry() {
        this.masonryGrid.masonry('destroy');
        this.isMasonryEnabled = false;

    }
}

jQuery(window).on('elementor/frontend/init', () => {
    class ElementQueryBaseHandlerClass extends elementorModules.frontend.handlers.Base {
        getDefaultSettings() {
            return {
                selectors: {
                    //xxxx: '.e-add-rellax',
                    images: '.e-add-item_image, .e-add-item_imageoricon',
                    //img: '.e-add-img img',
                    container: '.e-add-posts-container',
                    containerWrapper: '.e-add-posts-wrapper',
                    items: '.e-add-post-item',
                    hovereffects: '.e-add-post-block.e-add-hover-effects',
                    infiniteScroll: '.e-add-infiniteScroll'
                },
            };
        }

        getDefaultElements() {
            const selectors = this.getSettings('selectors');
            return {
                $scope: this.$element,
                $widgetType: this.$element.data('widget_type').split('.'),
                $id_scope: this.getID(), //this.$element.attr('data-id')
                $images: this.$element.find(selectors.images),
                $container: this.$element.find(selectors.container),
                $containerWrapper: this.$element.find(selectors.containerWrapper),
                $items: this.$element.find(selectors.items),
                $hovereffects: this.$element.find(selectors.hovereffects),
                $animationReveal: null
            };
        }

        bindEvents() {
            this.skinPrefix = this.$element.attr('data-widget_type').split('.').pop() + '_';
            this.elementSettings = this.getElementSettings();

            let id_scope = this.elements.$id_scope,
                    scope = this.elements.$scope,
                    widgetType = this.getWidgetType();

            if (widgetType.startsWith('e-query-') || widgetType == 'e-data-listing') {
                const fitV = function () {
                    // ---------------------------------------------
                    // FitVids per scalare in percentuale video e mappa
                    if (jQuery(".e-add-oembed").length) {
                        jQuery(".e-add-oembed").fitVids();
                    }
                }
                // ---------------------------------------------
                // FIT IMAGES RATIO ........
                // 3 -
                const fitImage = ($post) => {
                    let $imageParent = $post.find('.e-add-img'),
                            $image = $imageParent.find('img'),
                            image = $image[0];

                    if (!image) {
                        return;
                    }

                    var imageParentRatio = $imageParent.outerHeight() / $imageParent.outerWidth(),
                            imageRatio = image.naturalHeight / image.naturalWidth;

                    $imageParent.toggleClass('e-add-fit-img', imageRatio < imageParentRatio);
                };
                // 2 -
                const toggleRatio = () => {
                    var itemRatio = getComputedStyle(this.elements.$scope[0], ':after').content;
                    this.elements.$container.toggleClass('e-add-is_ratio', !!itemRatio.match(/\d/));
                }
                // 1 -
                const fitImages = () => {
                    toggleRatio(); // <-- 2

                    scope.find('.e-add-item_image,.e-add-item_imageoricon').each(function () {
                        var _this = jQuery(this),
                                $post = _this.find('.e-add-post-image'),
                                $itemId = _this.data('item-id'),
                                $image = $post.find('.e-add-img img');

                        fitImage($post); // <-- 3
                        $image.on('load', function () {
                            fitImage($post); // <-- 3

                        });
                    });
                };
                //Run on load..
                fitImages();
                fitV();


                // ---------------------------------------------
                // infiniteScroll load paginations
                const activeInfiniteScroll = () => {
                    let infscr_options = {
                        path: '.e-add-infinite-scroll-paginator__next-' + id_scope,
                        append: '.e-add-post-item-' + id_scope,
                        hideNav: '.e-add-infinite-scroll-paginator',
                        status: '.e-add-page-load-status-' + id_scope,
                        history: false,
                        outlayer: this.elements.$containerWrapper.data('masonry'),
                        checkLastPage: true
                    };
                    if (this.elementSettings.infiniteScroll_trigger == 'button') {
                        infscr_options['button'] = '.e-add-view-more-button-' + id_scope;
                        infscr_options['scrollThreshold'] = false;
                    }
                    if (this.elementSettings.infiniteScroll_enable_history) {
                        //infscr_options['history'] = 'push';
                        infscr_options['history'] = 'replace';
                    }
                    if (this.elementSettings.infiniteScroll_prefill) {
                        infscr_options['prefill'] = true;
                    }

                    let name = this.$element.data('widget_type').split('.').shift();
                    let classe = '.' + name + '_' + id_scope + '__items_length';
                    //console.log(classe);

                    this.elements.$containerWrapper.infiniteScroll(infscr_options);
                    this.elements.$containerWrapper.on('append.infiniteScroll', (event, body, path, items, response) => {
                        if (this.elementSettings[this.skinPrefix + 'scrollreveal_effect_type']) {
                            var isLive = this.elementSettings[this.skinPrefix + 'scrollreveal_live'] ? false : true;
                            this.elements.$animationReveal = new eadd_animationReveal(this.elements.$container, isLive);
                            fitImages();
                            fitV();
                        }

                        //console.log(`Appended ${items.length} items on ${path}`);
                        if (jQuery(classe).length) {
                            let lenght = this.elements.$containerWrapper.find(infscr_options['append']).length;
                            jQuery(classe).text(lenght);
                        }

                        // trigger ready for each widgets in custom Template
                        jQuery.each(items, function (index, value) {
                            jQuery(value).find('.elementor-widget').each(function () {
                                let $scope = jQuery(this);
                                elementorFrontend.elementsHandler.runReadyTrigger($scope);
                            });
                        });

                    });

                    if (this.elementSettings.infiniteScroll_prefill && this.elementSettings.infiniteScroll_trigger == 'button') {
                        //let promise = this.elements.$containerWrapper.infiniteScroll('loadNextPage');
                        var $containerWrapper = this.elements.$containerWrapper;
                        $containerWrapper.on('load.infiniteScroll', function (event, body, path, response) {
                            $containerWrapper.find(infscr_options['append']).addClass('old-items');
                        });
                        $containerWrapper.on('append.infiniteScroll', function (event, body, path, items, response) {
                            $containerWrapper.find(infscr_options['append']).not('.old-items').addClass('elementor-hidden');
                            if (jQuery(classe).length) {
                                let lenght = $containerWrapper.find(infscr_options['append'] + ':not(.elementor-hidden)').length;
                                jQuery(classe).text(lenght);
                            }
                        });
                        jQuery(infscr_options['button']).on('click', function () {
                            $containerWrapper.find(infscr_options['append'] + '.elementor-hidden').removeClass('elementor-hidden');
                            if (jQuery(classe).length) {
                                let lenght = $containerWrapper.find(infscr_options['append']).length;
                                jQuery(classe).text(lenght);
                            }
                        });
                    }

                };
                // infinite-scroll
                if (this.elementSettings.infiniteScroll_enable) {
                    setTimeout(function () {
                        activeInfiniteScroll();
                    }, 200);
                }
                // ---------------------------------------------
                // HOVER EFFECTS ........
                var blocks_hoverEffects = this.elements.$hovereffects;
                if (blocks_hoverEffects.length) {
                    //
                    blocks_hoverEffects.each(function (i, el) {
                        jQuery(el).on("mouseenter touchstart", function () {
                            jQuery(this).find('.e-add-hover-effect-content').removeClass('e-add-close').addClass('e-add-open');
                        });
                        jQuery(el).on("mouseleave touchend", function () {
                            jQuery(this).find('.e-add-hover-effect-content').removeClass('e-add-open').addClass('e-add-close');
                        });
                    });
                }

                //
                //
                // ---------------------------------------------
                // Funzione di callback eseguita quando avvengono le mutazioni
                var eAddns_MutationObserverCallback = function (mutationsList, observer) {
                    for (var mutation of mutationsList) {
                        if (mutation.type == 'childList') {
                            console.log('A child node has been added or removed.');
                        } else if (mutation.type == 'attributes') {
                            var attribute_of_target = getComputedStyle(mutation.target, ':after').content

                            if (attribute_of_target && attribute_of_target != 'none') {
                                fitImages(); // <-- 1
                            }
                            if (attribute_of_target == 'none') {
                                toggleRatio(); // <-- 2
                            }
                        }
                    }
                };

                observe_eAddns_element(this.elements.$scope[0], eAddns_MutationObserverCallback);


                // ajax pagination
                if (this.elementSettings.pagination_ajax) {
                    if (!scope.hasClass('e-pagination-ajax')) {
                        let self = this;
                        jQuery(document).on('click', '.elementor-element-' + id_scope + ' nav.elementor-pagination a', function () {
                            scope.children().animate({opacity: "0.45"}, 500);
                            let href = jQuery(this).attr('href');
                            jQuery.get(href, function (data, status) {
                                let element = jQuery(data).find(' .elementor-element-' + id_scope);
                                scope.html(element.html());
                                elementorFrontend.elementsHandler.runReadyTrigger(scope);
                                
                                if (self.elementSettings.pagination_ajax_url) {
                                    //window.location.href = href;
                                    window.history.pushState("", "", href);
                                    //console.log(href);
                                }

                                if (self.elementSettings.pagination_ajax_top) {
                                    //console.log(scope.offset().top);
                                    if (scope.offset().top) {
                                        jQuery('html, body').animate({
                                            scrollTop: scope.offset().top
                                        }, 500);
                                    }
                                }
                            });
                            return false;
                        });
                        scope.addClass('e-pagination-ajax');
                    }
                }

            }
        }

        onElementChange(propertyName) {
            this.elementSettings = this.getElementSettings();

            if ('_skin' === propertyName) {
                this.skinPrefix = this.$element.data('widget_type').split('.').pop() + '_';
            }
        }
    }

    const queryBaseHandlerFront = ($element) => {
        elementorFrontend.elementsHandler.addHandler(ElementQueryBaseHandlerClass, {
            $element,
        });
    };
    elementorFrontend.hooks.addAction('frontend/element_ready/widget', queryBaseHandlerFront);

});