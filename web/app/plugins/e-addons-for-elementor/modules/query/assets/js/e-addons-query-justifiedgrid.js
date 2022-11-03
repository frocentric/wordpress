jQuery(window).on('elementor/frontend/init', () => {

    class WidgetQueryJustifiedGridHandlerClass extends elementorModules.frontend.handlers.Base {
        getDefaultSettings() {
            // e-add-posts-container e-add-posts e-add-skin-grid e-add-skin-grid-masonry
            return {
                selectors: {
                    container: '.e-add-posts-container',
                    containerGrid: '.e-add-posts-container.e-add-skin-justifiedgrid',

                    containerWrapper: '.e-add-wrapper-justifiedgrid',
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
                $containerGrid: this.$element.find(selectors.containerGrid),
                $containerWrapper: this.$element.find(selectors.containerWrapper),

                $items: this.$element.find(selectors.items),

                //$isMasonryEnabled = false
                $justifiedObject: null,
                $animationReveal: null
            };
        }

        bindEvents() {
            this.skinPrefix = this.$element.data('widget_type').split('.').pop() + '_';

            let scope = this.elements.$scope,
                    id_scope = this.elements.$id_scope,
                    elementSettings = this.getElementSettings(),
                    justified_rowHeight = elementSettings[this.skinPrefix + 'justified_rowHeight'] ? Number(elementSettings[this.skinPrefix + 'justified_rowHeight']['size']) : 270,
                    justified_maxRowHeight = elementSettings[this.skinPrefix + 'justified_maxRowHeight'] ? Number(elementSettings[this.skinPrefix + 'justified_maxRowHeight']) : false,
                    justified_maxRowsCount = elementSettings[this.skinPrefix + 'justified_maxRowsCount'] ? Number(elementSettings[this.skinPrefix + 'justified_maxRowsCount']) : 0,
                    justified_margin = elementSettings[this.skinPrefix + 'justified_margin'] ? Number(elementSettings[this.skinPrefix + 'justified_margin']) : 0,
                    //justified_border = elementSettings[this.skinPrefix + 'justified_border'] ? Number(elementSettings[this.skinPrefix + 'justified_border']['size']) : -1,
                    justified_lastRow = elementSettings[this.skinPrefix + 'justified_lastRow'] || 'justify';

            //alert(elementSettings[this.skinPrefix + 'justified_rowHeight']['size']);

            // -------------------------------------------
            if (elementSettings['scrollreveal_effect_type']) {
                var isLive = elementSettings['scrollreveal_live'] ? false : true;
                this.elements.$animationReveal = new eadd_animationReveal(this.elements.$container, isLive);
            }


            /*
            http://miromannino.github.io/Justified-Gallery/options-and-events/
            Options:
            - rowHeight                 120
            - maxRowHeight              false
            - maxRowsCount              0
            - sizeRangeSuffixes         {}
            - thumbnailPath             undefined
            - lastRow                   'nojustify' [justify, hide, center, right]
            - captions                  true
            - margins                   1
            - border                    -1
            - rtl                       false ...
            - cssAnimation              true
            - imagesAnimationDuration   500
            */

            if(!justified_maxRowHeight){
                justified_maxRowHeight = false;
            }
            this.elements.$containerWrapper.justifiedGallery({
                rowHeight: justified_rowHeight, //270
                maxRowsCount: justified_maxRowsCount,
                
                //la max non mi convince .. devo capire il meccanismo!
                maxRowHeight: justified_maxRowHeight,

                lastRow: justified_lastRow, //'justify' 'nojustify' 'left' 'justify' 'nojustify' 'left' 'center''right''hide'
                margins: justified_margin, //0
                
                //non lo trovo utile
                //border: justified_border,
                
                captions: false,
                cssAnimation: false,
                selector: '.e-add-item-justifiedgrid',
                imgSelector: '> .e-add-post-block > .e-add-image-area > .e-add-item > .e-add-post-image > .e-add-img > img, > .e-add-post-block > .e-add-image-area > .e-add-item > .e-add-post-image > .e-add-img  > a > img'
            }).on('jg.complete', function (e) {
                //console.log(e);
            });
            ;

        }
        /*
        onElementChange(propertyName) {
            //console.log(propertyName);
            let elementSettings = this.getElementSettings();

           
             if (this.skinPrefix+'grid_type' === propertyName) {
             if(  elementSettings[propertyName] != 'masonry' && this.elements.$masonryObject ){
             this.elements.$masonryObject.removeMasonry();
             }
             }

             if ( this.skinPrefix+'columns_grid' === propertyName ||
             this.skinPrefix+'row_gap' === propertyName && this.elements.$masonryObject ) {
             if(this.elements.$masonryObject)
             this.elements.$masonryObject.layoutMasonry();
             }
        }
        */
    }

    const Widget_EADD_Query_justifiedgrid_Handler = ($element) => {

        elementorFrontend.elementsHandler.addHandler(WidgetQueryJustifiedGridHandlerClass, {
            $element,
        });
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-posts.justifiedgrid', Widget_EADD_Query_justifiedgrid_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-users.justifiedgrid', Widget_EADD_Query_justifiedgrid_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-terms.justifiedgrid', Widget_EADD_Query_justifiedgrid_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-itemslist.justifiedgrid', Widget_EADD_Query_justifiedgrid_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-media.justifiedgrid', Widget_EADD_Query_justifiedgrid_Handler);
});