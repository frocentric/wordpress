jQuery(window).on('load', function () {
    var ControlBaseDataView = elementor.modules.controls.BaseData;
    var uiSelectorView = ControlBaseDataView.extend({
        ui: function ui() {
            var ui = ControlBaseDataView.prototype.ui.apply(this, arguments);
            ui.inputs = '[type="radio"]';
            return ui;
        },
        events: function events() {
            return _.extend(ControlBaseDataView.prototype.events.apply(this, arguments), {
                'mousedown label': 'onMouseDownLabel',
                'click @ui.inputs': 'onClickInput',
                'change @ui.inputs': 'onBaseInputChange'
            });
        },
        applySavedValue: function applySavedValue() {
            let currentValue = this.getControlValue();
            if (currentValue) {
                this.ui.inputs.filter('[value="' + currentValue + '"]').prop('checked', true);
            } else {
                this.ui.inputs.filter(':checked').prop('checked', false);
            }
        },
        onMouseDownLabel: function onMouseDownLabel(event) {
            let $clickedLabel = this.$(event.currentTarget),
                    $selectedInput = this.$('#' + $clickedLabel.attr('for'));
            $selectedInput.data('checked', $selectedInput.prop('checked'));
        },
        onClickInput: function onClickInput(event) {
            if (!this.model.get('toggle')) {
                return;
            }
            let currentValue = this.getControlValue();
            let $selectedInput = this.$(event.currentTarget);
            console.log($selectedInput);
            console.log(currentValue);
            if ((currentValue && currentValue == $selectedInput.val())
                    || (currentValue == 'none' == $selectedInput.val())) {
                if ($selectedInput.prop('checked')) {
                    $selectedInput.prop('checked', false);
                    //$selectedInput.trigger('change');
                    this.container.settings.set(this.model.get('name'), '');
                }
            }
        }
    });

    elementor.addControlView('ui_selector', uiSelectorView);

});