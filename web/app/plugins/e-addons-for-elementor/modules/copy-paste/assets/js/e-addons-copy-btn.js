jQuery(window).on('elementor/frontend/init', () => {

    class CopyButtonHandlerClass extends elementorModules.frontend.handlers.Base {

        getDefaultSettings() {
            return {
                selectors: {
                    button: 'button',
                    code: '.e-codemirror',
                },
            };
        }

        getDefaultElements() {
            const selectors = this.getSettings('selectors');
            return {
                $scope: this.$element,
                $button: this.$element.find(selectors.button),
                $code: this.$element.find(selectors.code),
                $id_scope: this.$element.attr('data-id')
            };
        }

        initCopy() {
            let id_scope = this.elements.$id_scope,
                    elementSettings = this.getElementSettings();

            this.elements.$button.each(function () {
                if (jQuery(this).data('copy-target')) {
                    jQuery(this).on('mousedown', function() {
                        let target = jQuery(jQuery(this).data('copy-target'));
                        if (target.length) {
                            let txt = target.text().trim();
                            if (['input', 'textarea', 'select'].includes(target.prop("tagName"))) {
                                txt = target.val();
                            }
                            jQuery(jQuery(this).data('clipboard-target')).val(txt);
                        }
                    });
                }
                let clipboard = new ClipboardJS('#' + jQuery(this).attr('id'));
                clipboard.on('success', function (e) {
                    e.clearSelection();
                    let animation = jQuery(e.trigger).data('animation');
                    if (animation) {
                        jQuery(e.trigger).addClass('animated').addClass(animation);
                        setTimeout(function () {
                            jQuery(e.trigger).removeClass('animated').removeClass(animation);
                        }, 2000);
                    }
                    return false;
                });
                clipboard.on('error', function (e) {
                    e.clearSelection();
                });
            });
        }

        bindEvents() {
            let id_scope = this.elements.$id_scope,
                    elementSettings = this.getElementSettings();

            this.initCopy();
        }
    }

    const CopyButtonHandlerFront = ($element) => {
        elementorFrontend.elementsHandler.addHandler(CopyButtonHandlerClass, {
            $element,
        });
    };
    elementorFrontend.hooks.addAction('frontend/element_ready/copy-button.default', CopyButtonHandlerFront);
});