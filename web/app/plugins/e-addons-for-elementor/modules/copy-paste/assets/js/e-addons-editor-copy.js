/*
 * e-addons COPY/PASTE from Clipboard
 * e-addons.com
 */

(function ($) {
    elementor.hooks.addFilter('elements/widget/contextMenuGroups', function (groups, widget) {
        return eAddPasteAction(groups, widget);
    });
    elementor.hooks.addFilter('elements/column/contextMenuGroups', function (groups, column) {
        return eAddPasteAction(groups, column);
    });
    elementor.hooks.addFilter('elements/section/contextMenuGroups', function (groups, section) {
        return eAddPasteAction(groups, section);
    });
})(jQuery);

// add context menu item to add-section
//jQuery(window).on('load', function () {
jQuery(document).ready(function () {
    elementor.on('preview:loaded', function () {
        //console.log('preview:loaded');
        eAddPasteAll();
    });
});

function eAddPasteAll() {
    setTimeout(function () {
        if (!jQuery("#elementor-loading:visible").length) {
            //console.log('no loading');
            var preview_iframe = jQuery("iframe#elementor-preview-iframe").contents();
            //console.log(preview_iframe.find('#elementor-add-new-section').length);
            //preview_iframe.one('contextmenu', '#elementor-add-new-section', function(){
            preview_iframe.find('#elementor-add-new-section, .elementor-add-section').one('contextmenu', function () {
                //console.log('contextmenu');
                if (!jQuery('.elementor-context-menu-list__group-paste .elementor-context-menu-list__item-e_paste').length) {
                    jQuery('.elementor-context-menu-list__group-paste .elementor-context-menu-list__item-paste').after(
                            '<div class="elementor-context-menu-list__item elementor-context-menu-list__item-e_paste"><div class="elementor-context-menu-list__item__icon"></div><div class="elementor-context-menu-list__item__title">Paste from Clipboard</div></div>'
                            );
                    //console.log('add paste all');
                    jQuery(document).on('click', '.elementor-context-menu-list__group-paste .elementor-context-menu-list__item-e_paste', function () {
                        //console.log('e paste start - add section');
                        ePasteFromClipboard(false, this);
                    });
                }
            });

            // add Element ID copy
            setInterval(function () {
                jQuery('.elementor-context-menu').each(function () {
                    if (!jQuery(this).find('.elementor-context-menu-element__icon').length) {
                        let copy = jQuery(this).find('.elementor-context-menu-list__item.elementor-context-menu-list__item-copy .elementor-context-menu-list__item__title');
                        if (copy.length) {
                            copy.append('<abbr title="Copy Element ID to Clipboard" class="elementor-context-menu-element__icon elementor-context-menu-element_id__icon e-copy-id"><i class="e-copy-id eadd-copy-id"></i></abbr>');
                        } else {
                            copy = jQuery(this).find('.elementor-context-menu-list__item.elementor-context-menu-list__item-copy_all_content .elementor-context-menu-list__item__title');
                        }                        
                        copy.append('<abbr title="Download Element as Json Template" class="elementor-context-menu-element__icon elementor-context-menu-element_download__icon e-download"><i class="e-download eadd-download"></i></abbr>');
                        let paste = jQuery(this).find('.elementor-context-menu-list__item.elementor-context-menu-list__item-paste .elementor-context-menu-list__item__title');
                        paste.append('<abbr title="Paste from Clipboard" class="elementor-context-menu-element__icon elementor-context-menu-element_paste__icon e-paste"><i class="e-paste eadd-paste-clipboard"></i></abbr>');
                        let paste_style = jQuery(this).find('.elementor-context-menu-list__item.elementor-context-menu-list__item-pasteStyle .elementor-context-menu-list__item__title');
                        paste_style.append('<abbr title="Paste Style from Clipboard" class="elementor-context-menu-element__icon elementor-context-menu-element_paste_style__icon e-paste-style"><i class="e-paste-style eadd-paste-style"></i></abbr>');
                        jQuery(this).find('.elementor-context-menu-element__icon').hide().fadeIn();
                    }
                });
            }, 1000);

        } else {
            //console.log('loading');
            eAddPasteAll();
        }
    }, 1000);
}

jQuery(window).on('load', function () {
    // PASTE
    jQuery(document).on('mousedown', '.elementor-context-menu-list__item-paste', function (e) {
        console.log(e);
        if (jQuery(e.target).hasClass('e-paste')) {
            elementorCommon.storage.set('clipboard', ''); // prevent default Past action
            //elementorCommon.storage.set('transfer', '');
            jQuery(this).siblings(".elementor-context-menu-list__item-e_paste").trigger('click');
            return false;
        }
    });
    // PASTE STYLE
    jQuery(document).on('mousedown', '.elementor-context-menu-list__item-pasteStyle', function (e) {
        if (jQuery(e.target).hasClass('e-paste-style')) {
            elementorCommon.storage.set('clipboard', ''); // prevent default Past action
            //elementorCommon.storage.set('transfer', '');
            jQuery(this).siblings(".elementor-context-menu-list__item-e_paste_style").trigger('click');
            return false;
        }
    });
    // COPY
    jQuery(document).on('click', '.elementor-context-menu-list__item-copy, .elementor-context-menu-list__item-copy_all_content', function (e) {

        //console.log('e copy start');
        var transferData = elementorCommon.storage.get('clipboard');
        if (!transferData) {
            transferData = elementorCommon.storage.get('transfer');
        }
        transferData = eRemoveHtmlCache(transferData);
        var jTransferData = JSON.stringify(transferData);

        //console.log(jQuery(e.target));
        //console.log(transferData);
        
        if (jQuery(e.target).hasClass('e-copy-id')) {
            // copy Element ID
            jTransferData = transferData[0]['id'];
        }
        
        if (jQuery(e.target).hasClass('e-download')) {
            // download
            let filename = transferData[0]['elType'];
            if (filename == 'widget') {
                filename += '_' + transferData[0]['widgetType'];
            }
            jTransferData = '{"version":"1.0.1","title":"' + filename + '","type":"page","content":' + jTransferData + '}';
            filename += '_' + transferData[0]['id'] + '.json';
            //alert(filename);
            var element = document.createElement('a');
            element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(jTransferData));
            element.setAttribute('download', filename);
            element.style.display = 'none';
            document.body.appendChild(element);
            element.click();
            document.body.removeChild(element);
            return;
        }
        //console.log(transferData);

        if (navigator.clipboard) {
            navigator.clipboard.writeText(jTransferData)
                    .then(() => {
                        // Success!
                        //console.log('e copied');
                    })
                    .catch(err => {
                        console.log('Something went wrong', err);
                    });
        } else {
            // fallback
            eAddPasteFallback(jTransferData);
            var clipboard = new ClipboardJS('#e_copy_paste__btn');

            clipboard.on('success', function (e) {
                console.info('Action:', e.action);
                console.info('Text:', e.text);
                console.info('Trigger:', e.trigger);
                e.clearSelection();
            });
            clipboard.on('error', function (e) {
                console.error('Action:', e.action);
                console.error('Trigger:', e.trigger);
            });

            jQuery('#e_copy_paste__btn').trigger('click');

            jQuery('#e_copy_paste').remove();
            // Success!
            //console.log('e copied fallback');
        }
    });

});

function eAddPasteAction(groups, element) {
    //console.log('add paste action');
    var transferGroup = _.findWhere(groups, {name: 'clipboard'});
    if (!transferGroup) {
        transferGroup = _.findWhere(groups, {name: 'transfer'});
    }
    if (!transferGroup) {
        return groups;
    }
    jQuery.each(groups, function (index, value) {
        if (value.name == 'transfer' || value.name == 'clipboard' || value.name == 'paste') {
            //console.log(value.name);
            groups[index].actions.push(
                    {
                        name: 'e_paste',
                        title: 'Paste from Clipboard',
                        callback: function () {
                            //console.log('Paste from Clipboard');
                            pasteAction = _.findWhere(transferGroup.actions, {name: 'paste'});
                            return ePasteFromClipboard(pasteAction);
                        }
                    },
                    {
                        name: 'e_paste_style',
                        title: 'Paste Style from Clipboard',
                        callback: function () {
                            //console.log('Paste Style from Clipboard');
                            pasteStyleAction = _.findWhere(transferGroup.actions, {name: 'pasteStyle'});
                            return ePasteFromClipboard(pasteStyleAction);
                        }
                    }
            );
        }
    });

    return groups;
}

// PASTE
function ePasteFromClipboard(pasteAction, pasteBtn) {

    let cid = false;
    if (pasteBtn) {
        cid = jQuery(pasteBtn).closest('.elementor-context-menu').attr('data-model-cid');
    }
    if (!cid || cid == 'undefined') {
        cid = jQuery('.elementor-context-menu:visible').attr('data-model-cid');
    }
    //console.log(cid);
    //console.log('e paste start');
    if (eCanJsPaste()) {
        navigator.clipboard.readText()
                .then(text => {
                    // `text` contains the text read from the clipboard
                    ePasteAction(text, pasteAction, pasteBtn, cid);
                })
                .catch(err => {
                    // maybe user didn't grant access to read from clipboard
                    console.log('Something went wrong', err);
                });
    } else {
        jQuery(pasteBtn).closest('.elementor-context-menu').hide()
        eAddPasteFallback('', 'paste', cid, pasteAction, pasteBtn); // create an empty textarea
        jQuery('#e_copy_paste__textarea').select();
        document.execCommand("paste");
        var text = jQuery('#e_copy_paste__textarea').val(); // retrieve the pasted text
        if (text) {
            jQuery('#e_copy_paste__btn').trigger('click');
        }
    }
    return true;
}

function eAddPasteFallback(value = '', action = 'copy', cid, pasteAction, pasteBtn) {
    //console.log(pasteAction);
    if (jQuery('#e_copy_paste').length) {
        jQuery('#e_copy_paste').remove();
    }
    jQuery('#elementor-preview-responsive-wrapper').append('<div id="e_copy_paste" class="elementor-context-menu" data-model-cid="' + cid + '"></div>');
    jQuery('#e_copy_paste').append('<p>Sorry, direct Paste is <b>not supported</b> by your browser or your <b>clipboard is empty</b>, to continue <b>MANUALLY Paste</b> content in the below Textarea and <b>click PASTE</b></p>');
    jQuery('#e_copy_paste').append('<textarea id="e_copy_paste__textarea" placeholder="Paste HERE">' + value + '</textarea>');
    jQuery('#e_copy_paste').append('<label id="e_copy_paste__file_label" for="e_copy_paste__file">or choose an Elementor Template JSON file:</label><input type="file" id="e_copy_paste__file">');
    jQuery('#e_copy_paste').append('<button id="e_copy_paste__btn" data-clipboard-action="' + action + '" data-clipboard-target="#e_copy_paste__textarea"><span class="icon pull-right ml-1"></span> ' + (pasteAction ? pasteAction.title : 'Paste') + '</button>');
    jQuery('#e_copy_paste').append('<a id="e_copy_paste__close" href="#"><i class="eicon-close"></i></a>');
    if (action == 'paste') {
        jQuery('#e_copy_paste__textarea').trigger('click').focus();
        jQuery('#e_copy_paste').attr('data-model-cid', cid);
        jQuery('#e_copy_paste__btn').on('click', function () {
            var text = jQuery('#e_copy_paste__textarea').val();
            ePasteAction(text, pasteAction, pasteBtn, jQuery('#e_copy_paste').attr('data-model-cid'));
            jQuery('#e_copy_paste').remove();
        });
        jQuery('#e_copy_paste__file').on('change', function () {
            const fileList = this.files;
            //console.log(fileList);
            eReadTemplate(fileList[0]);
        });
    }
    jQuery('#e_copy_paste__close').on('click', function () {
        jQuery('#e_copy_paste').remove();
    });
}

function ePasteAction(text, pasteAction, pasteBtn, cid) {

    var isJson = true;
    try {
        JSON.parse(text);
    } catch (e) {
        isJson = false;
    }

    if (isJson) {
        var clipboardData = JSON.parse(text);
        //console.log(clipboardData);
        if (clipboardData.content) {
            clipboardData = clipboardData.content;
        }
        clipboardData = eGenerateUniqueID(clipboardData);
        //if (transferData.elements.length) {
        elementorCommon.storage.set('clipboard', clipboardData); // >= 2.8
        elementorCommon.storage.set('transfer', clipboardData); // <= 2.7

        //console.log('e pasted');
        if (pasteAction) {
            //console.log(pasteAction);
            if (!pasteAction.callback()) {
                // not working on PasteStyle action...so fallback
                //console.log('paste enabled'); console.log(pasteAction.isEnabled());
                if (cid && cid != 'undefined') {
                    var pasteBtnSelector = '.elementor-context-menu[data-model-cid=' + cid + '] .elementor-context-menu-list__item-' + pasteAction.name;
                    var iFrameDOM = jQuery("iframe#elementor-preview-iframe").contents();
                    iFrameDOM.find('.elementor-element[data-model-cid=' + cid + ']').trigger('contextmenu');
                    //pasteAction.callback()
                    jQuery('.elementor-context-menu[data-model-cid=' + cid + ']').hide();
                    setTimeout(function () {
                        //console.log(pasteBtnSelector);
                        jQuery(pasteBtnSelector).trigger('click');
                    }, 100);

                }
                //return new Commands.PasteStyle().run();
                //$e.run('document/elements/paste-style', {});
            }
        } else {
            jQuery(pasteBtn).prev().trigger('click');
        }
        //}
        jQuery('#e_copy_paste').remove();
    } else {
        alert('Invalid JSON Element in Clipboard:\r\n------------------\r\n' + text);
        eAddPasteFallback('', 'paste', cid, pasteAction, pasteBtn);
    }
}

function eRemoveHtmlCache(elements) {
    elements.forEach(function (item, index) {
        elements[index].htmlCache = null;
        if (item.elements.length > 0) {
            elements[index].elements = eRemoveHtmlCache(item.elements);
        }
    });
    return elements;
}

function eGenerateUniqueID(elements) {
    elements.forEach(function (item, index) {
        elements[index].id = elementor.helpers.getUniqueID();
        if (item.elements.length > 0) {
            elements[index].elements = eGenerateUniqueID(item.elements);
        }
    });
    return elements;
}

function eCanJsPaste() {
    return navigator.clipboard && typeof navigator.clipboard.readText === "function" && (location.protocol == 'https:' || location.hostname == 'localhost' || location.hostname == '127.0.0.1');
}

function eReadTemplate(file) {
    // Check if the file is a json.
    if (file.type && file.type.indexOf('json') === -1) {
        //console.log(file.type);
        alert('Sorry, you not select a valid exported Elementor Template JSON file.');
        return;
    }

    const reader = new FileReader();
    reader.addEventListener('load', (event) => {
        //console.log(event);
        let tmp = event.target.result.split(';base64,');
        let base64 = tmp.pop();
        let fileContent = atob(base64);
        jQuery('#e_copy_paste__textarea').val(fileContent);
    });
    reader.readAsDataURL(file);
}