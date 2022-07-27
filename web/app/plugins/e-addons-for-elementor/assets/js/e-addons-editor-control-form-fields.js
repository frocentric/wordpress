var e_model_cid = e_model_cid || false;
jQuery(window).on('load', function () {
    elementor.hooks.addAction('panel/open_editor/section', function (panel, model, view) {
        e_model_cid = model.cid;
    });
    elementor.hooks.addAction('panel/open_editor/column', function (panel, model, view) {
        e_model_cid = model.cid;
    });
    elementor.hooks.addAction('panel/open_editor/widget', function (panel, model, view) {
        e_model_cid = model.cid;
    });
    var formFieldsItemView = elementor.modules.controls.BaseData.extend({
        onReady: function () {
            let selectedElement = (typeof elementor.getCurrentElement === 'function') ? elementor.getCurrentElement() : false;
            let cid = e_model_cid; //jQuery('.elementor-navigator__item.elementor-editing').parent().data('model-cid');
            if (selectedElement) {
                cid = selectedElement.model.cid;
            }
            if (elementorFrontend.config.elements.data[cid]) {
                let settings = elementorFrontend.config.elements.data[cid].attributes;
                let fields = settings['form_fields'];
                // single field
                let select = this.$el.find('select');
                let data_setting = select.data('setting');
                //console.log(data_setting);
                let select_value = settings[data_setting];
                //console.log(select_value);
                //var self = this;
                let options = '';
                if (!select.prop('multiple')) {
                    options = '<option value="">No field</option>';
                }
                if (fields) {
                    jQuery(fields.models).each(function (index, element) {
                        if (element.attributes.custom_id) {
                            let field_label = '[' + element.attributes.custom_id + '] (' + element.attributes.field_type + ')';
                            if (element.attributes.field_label) {
                                if (element.attributes.field_label.length > 20) {
                                    field_label = element.attributes.field_label.substr(0, 20) + '… ' + field_label;
                                } else {
                                    field_label = element.attributes.field_label + ' ' + field_label;
                                }
                            }
                            let selected = '';
                            //console.log(element);
                            /*if (select_value) {
                                if (select.prop('multiple')) {
                                    if (select_value.includes(element.attributes.custom_id)) {
                                        selected = ' selected';
                                    }
                                } else {
                                    if (element.attributes.custom_id == select_value) {
                                        selected = ' selected';
                                    }
                                }
                            }*/
                            let field_type_filter = true;
                            if (select.data('field_type')) {
                                let field_type = element.attributes.field_type;
                                //console.log(field_type);
                                switch(select.data('field_type')) {
                                    case 'attachment':
                                        if (!['media','upload','signature'].includes(field_type)) {
                                            field_type_filter = false;
                                        }
                                        break;
                                    default:
                                        if (select.data('field_type') != field_type) {
                                            field_type_filter = false;
                                        }
                                }
                            }
                            
                            if (field_type_filter) {
                                options += '<option value="' + element.attributes.custom_id + '"'+selected+'>' + field_label + '</option>';
                            }
                        }
                    });
                }
                setTimeout(() => {
                    if (this.options.container && this.options.container.type == 'repeater') {
                        // in repeater
                        let index = this._parent._index;
                        let repeter = this.options.container.model.attributes.name;
                        if (settings[repeter] && settings[repeter].models[index]) {
                            select_value = settings[repeter].models[index].attributes[data_setting];
                        }
                    }
                    let is_select2 = false;

                    if (select.hasClass("select2-hidden-accessible")) {
                        select.select2('destroy');
                        is_select2 = true;
                    }
                    select.html(options);
                    /*if (custom_id_input) {
                     // remove itself
                     select.find("option[value='" + custom_id_input.val() + "']").remove();
                     }*/
                    select.val(select_value);
                    if (is_select2 || select.hasClass('elementor-select2')) { //select.prop('multiple')) {
                        //console.log('select2');
                        select.select2();
                    }
                }, 100);
            }
        },
    });
    elementor.addControlView('form_fields', formFieldsItemView);
});