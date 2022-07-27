
jQuery(window).on('elementor/frontend/init', () => {
    class WidgetWpmlSelectorHandlerClass extends elementorModules.frontend.handlers.Base {

        getDefaultSettings() {
            const target = '#e-add-language-switcher-' + this.getID();
            return {
                selectors: {
                    container: target,
                    select: 'select.e-add-language-switcher-select',
                    dropdown: '.e-add-language-switcher-type-dropdown'
                },
            };
        }

        getDefaultElements() {
            const selectors = this.getSettings('selectors');
            return {
                $scope: this.$element,
                $container: this.$element.find(selectors.container),
                $select: this.$element.find(selectors.select),
                $dropdown: this.$element.find(selectors.dropdown),
            };
        }
        bindEvents() {

            let scope = this.elements.$scope,
                    select = this.elements.$select,
                    dropdown = this.elements.$dropdown,
                    elementSettings = this.getElementSettings(),
                    id_scope = this.getID();

            if (elementSettings.wpmllangselector_type == 'dropdown') {

                let heightItem = dropdown.find('.e-add-lang-ul').innerHeight();
                //
                /*dropdown.find('.e-add-lang-li').each( (i,el) => {
                 heightItem += Number(jQuery(el).height());
                 });
                 dropdown.attr('data-dd-height',heightItem);*/

                if (dropdown.length)
                    dropdown.on('click', '.e-add-active-lang:not(.e-add-active-lang-open)', (e) => {
                        jQuery(e.currentTarget).addClass('e-add-active-lang-open');
                        dropdown.find('.e-add-lang-panel').height(heightItem);
                    });
                dropdown.on('click', '.e-add-active-lang.e-add-active-lang-open', (e) => {
                    jQuery(e.currentTarget).removeClass('e-add-active-lang-open');
                    dropdown.find('.e-add-lang-panel').height(0);
                })
            }

        }
    }


    const wpmlselectorHandler = ($element) => {
        elementorFrontend.elementsHandler.addHandler(WidgetWpmlSelectorHandlerClass, {
            $element,
        });
        //console.log($element);
    };
    elementorFrontend.hooks.addAction('frontend/element_ready/e-wpml-select.default', wpmlselectorHandler);

    //console.log(elementorFrontend);
});