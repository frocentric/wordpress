jQuery(window).load(function () {  
    
    var has_php = false;    
    elementor.hooks.addAction( 'panel/open_editor/widget/e-pure-php', function( panel, model, view ) {
        if (!has_php) {
            has_php = setInterval(function () {
                var preview = jQuery("iframe#elementor-preview-iframe").contents();
                if (preview.find("div.elementor-widget-e-pure-php").length) {
                    if (preview.find("div.elementor-widget-e-pure-php.elementor-loading").length) {
                        // disable
                        jQuery('#elementor-panel-saver-button-publish, #elementor-panel-saver-button-save-options, #elementor-panel-saver-menu-save-draft').addClass('elementor-saver-disabled').prop('disabled', true);
                        jQuery('#elementor-panel-saver-button-publish').addClass('e-addons-elementor-saver-disabled');
                        jQuery('.elementor-control-custom_php_error .e-addons-php-error').slideDown();
                        //console.log('errore');
                    } else {
                        // enable
                        if (jQuery('#elementor-panel-saver-button-publish').hasClass('e-addons-elementor-saver-disabled')) {
                            jQuery('#elementor-panel-saver-button-publish, #elementor-panel-saver-button-save-options, #elementor-panel-saver-menu-save-draft').removeClass('elementor-saver-disabled').removeClass('elementor-disabled').prop('disabled', false).removeProp('disabled');
                            jQuery('#elementor-panel-saver-button-publish').removeClass('e-addons-elementor-saver-disabled');
                        }
                        jQuery('.elementor-control-custom_php_error .e-addons-php-error').slideUp();
                    }
                }
            }, 1000);
        }
    } );
    
});
//console.log('E-PURE PHP');