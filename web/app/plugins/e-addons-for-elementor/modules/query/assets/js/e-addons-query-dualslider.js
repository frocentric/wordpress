jQuery(window).on('elementor/frontend/init', () => {

class WidgetQueryDualSliderHandlerClass extends elementorModules.frontend.handlers.Base {

    getDefaultSettings() {
        // e-add-posts-container e-add-posts e-add-skin-grid e-add-skin-grid-masonry
        return {
            selectors: {
                container: '.e-add-posts-container',
                containerCarousel: '.e-add-posts-container.e-add-skin-dualslider',
                thumbsCarousel: '.e-add-dualslider-gallery-thumbs',
                dualsliderCarousel: '.e-add-skin-dualslider',
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

            $thumbsCarousel: this.$element.find(selectors.thumbsCarousel),
            $dualsliderCarousel: this.$element.find(selectors.dualsliderCarousel),

            $containerWrapper: this.$element.find(selectors.containerWrapper),

            $items: this.$element.find(selectors.items),

            $animationReveal: null,
            $galleryThumbs: null
        };
    }

    bindEvents() {
        this.skinPrefix = this.$element.attr('data-widget_type').split('.').pop() + '_';
        this.elementSettings = this.getElementSettings();
        this.countItems = this.elements.$items.length;
        this.isThumbsCarouselEnabled = false;
        
        //@p questo è nuovo
        this.adapteHeight();
        
        this.postId = this.elements.$scope.find('.e-add-dualslider-controls').attr('data-post-id');
        
        let scope = this.elements.$scope,
            id_scope = this.elements.$id_scope,
            widgetType = this.getWidgetType(),
            galleryThumbs = null;

        //@p per sicurezza se esiste già un'istanza la distruggo! ..
        if(galleryThumbs) galleryThumbs.destroy();
       
        this.elements.$thumbsCarousel.imagesLoaded().always( () => {
            if (this.elements.$galleryThumbs) {
                this.elements.$galleryThumbs.update();
            }
        });

        if(this.countItems > 1){
            galleryThumbs = new Swiper( this.elements.$thumbsCarousel[0], this.thumbCarouselOptions( id_scope ) );
            this.elements.$galleryThumbs = galleryThumbs;
            this.elements.$scope.data('thumbscarousel', galleryThumbs);
        }else{
            this.elements.$thumbsCarousel.remove();
            this.elements.$scope.find('.e-add-dualslider-controls').remove();
            
        }
        //@p lancio il carosello principale per il sync
        elementorFrontend.hooks.doAction('frontend/element_ready/'+widgetType+'.carousel', scope);

        //da fare.... l'evento di resizing per gestire l'altezza delle tumbnail in caso di left-right
        this.initEvents();

    }
    initEvents(){
		// on risize adapting blocks and elements
		window.addEventListener("resize", (event) => {
			this.adapteHeight();
		});

	}
    onElementChange(propertyName){
        this.elementSettings = this.getElementSettings();

        if (    this.skinPrefix+'ratio_image' === propertyName ||
                this.skinPrefix+'dualslider_distribution_vertical' === propertyName ||
                this.skinPrefix+'dualslider_height_container' === propertyName
            ) {
            this.elements.$galleryThumbs.update();
            this.adapteHeight();
        }
        if(this.skinPrefix+'dualslider_image_height' === propertyName){
            this.elements.$galleryThumbs.update();
        }
    }
    adapteHeight(){
        if(this.elementSettings[this.skinPrefix+'dualslider_style'] == 'row-reverse' || this.elementSettings[this.skinPrefix+'dualslider_style'] == 'row'){
            this.elements.$thumbsCarousel.height(this.elements.$dualsliderCarousel.height());
        }
    }
    thumbCarouselOptions( id_scope ){
        var self;
        let slidesPerView = Number(this.elementSettings[this.skinPrefix+'thumbnails_slidesPerView']),
            elementorBreakpoints = elementorFrontend.config.breakpoints,
            directionThumbSlider = 'horizontal',
            loopdual = true;

        if(this.elementSettings[this.skinPrefix+'dualslider_style'] == 'row-reverse' || this.elementSettings[this.skinPrefix+'dualslider_style'] == 'row'){
            directionThumbSlider = 'vertical';
        }else if(this.elementSettings[this.skinPrefix+'dualslider_style'] == 'column-reverse' || this.elementSettings[this.skinPrefix+'dualslider_style'] == 'column'){
            directionThumbSlider = 'horizontal';
        }
        let navigationThumbnail;
        if(this.countItems == 1){
            loopdual = false;
            navigationThumbnail = false;
        }else{
            loopdual = Boolean(this.elementSettings[this.skinPrefix+'dualsliderloop']);
            navigationThumbnail = {
                nextEl: '.dual-next-' + id_scope + '-' + this.postId,
                prevEl: '.dual-prev-' + id_scope + '-' + this.postId,
            };
        }
        var eaddSwiperOptions = {
            spaceBetween: Number(this.elementSettings[this.skinPrefix+'dualslider_gap']) || 0,
            slidesPerView: slidesPerView || 'auto',
            autoHeight: false,
            watchOverflow: true,
            direction:  directionThumbSlider,
            watchSlidesProgress: true,
            navigation: navigationThumbnail,
            loop: loopdual,
            on: {
                init: function () {
                    this.isThumbsCarouselEnabled = true;
                },

            }
        };
        
        var responsivePoints = eaddSwiperOptions.breakpoints = {};
        responsivePoints[elementorBreakpoints.lg] = {
            slidesPerView: Number(this.elementSettings[this.skinPrefix+'thumbnails_slidesPerView']) || 'auto',
            slidesPerGroup: Number(this.elementSettings[this.skinPrefix + 'thumbnails_slidesPerGroup']) || 1,
            spaceBetween: Number(this.elementSettings[this.skinPrefix+'dualslider_gap']) || 0,
        };
        responsivePoints[elementorBreakpoints.md] = {
            slidesPerView: Number(this.elementSettings[this.skinPrefix+'thumbnails_slidesPerView_tablet']) || Number(this.elementSettings[this.skinPrefix+'thumbnails_slidesPerView']) || 'auto',
            slidesPerGroup: Number(this.elementSettings[this.skinPrefix + 'thumbnails_slidesPerGroup_tablet']) || Number(this.elementSettings[this.skinPrefix + 'thumbnails_slidesPerGroup']) || 1,
            spaceBetween: Number(this.elementSettings[this.skinPrefix+'dualslider_gap_tablet']) || Number(this.elementSettings[this.skinPrefix+'dualslider_gap']) || 0,
        };
        responsivePoints[elementorBreakpoints.xs] = {
            slidesPerView: Number(this.elementSettings[this.skinPrefix+'thumbnails_slidesPerView_mobile']) || Number(this.elementSettings[this.skinPrefix+'thumbnails_slidesPerView_tablet']) || Number(this.elementSettings[this.skinPrefix+'thumbnails_slidesPerView']) || 'auto',
            slidesPerGroup: Number(this.elementSettings[this.skinPrefix + 'thumbnails_slidesPerGroup_mobile']) || Number(this.elementSettings[this.skinPrefix + 'thumbnails_slidesPerGroup_tablet']) || Number(this.elementSettings[this.skinPrefix + 'thumbnails_slidesPerGroup']) || 1,
            spaceBetween: Number(this.elementSettings[this.skinPrefix+'dualslider_gap_mobile']) || Number(this.elementSettings[this.skinPrefix+'dualslider_gap_tablet']) || Number(elementSettings[self.skinPrefix+'spaceBetween']) || 0,
        };
        eaddSwiperOptions = jQuery.extend(eaddSwiperOptions, responsivePoints);

        return eaddSwiperOptions;
    }
}

    const Widget_EADD_Query_dualslider_Handler = ($element) => {

        elementorFrontend.elementsHandler.addHandler(WidgetQueryDualSliderHandlerClass, {
            $element,
        });

    };

    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-posts.dualslider', Widget_EADD_Query_dualslider_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-users.dualslider', Widget_EADD_Query_dualslider_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-terms.dualslider', Widget_EADD_Query_dualslider_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-itemslist.dualslider', Widget_EADD_Query_dualslider_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-media.dualslider', Widget_EADD_Query_dualslider_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-repeater.dualslider', Widget_EADD_Query_dualslider_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-products.dualslider', Widget_EADD_Query_dualslider_Handler);
    // comments (todo)
    // products (todo)  
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-rest-api.dualslider', Widget_EADD_Query_dualslider_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-db.dualslider', Widget_EADD_Query_dualslider_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-data-listing.dualslider', Widget_EADD_Query_dualslider_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-xml.dualslider', Widget_EADD_Query_dualslider_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-spreadsheet.dualslider', Widget_EADD_Query_dualslider_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-rss.dualslider', Widget_EADD_Query_dualslider_Handler);
});