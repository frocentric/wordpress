(function ($) {
    jQuery(window).on('elementor/frontend/init', function () {

        jQuery('.elementor-button-copy-wrapper').each(function () {
            let prev = jQuery(this).prev(); //closest('.elementor-element'); //;
            //console.log(prev.data('element_type'));
            switch (prev.data('element_type')) {
                case 'widget':
                    //jQuery(this).appendTo(prev.children('.elementor-widget-container'));
                    break;
                case 'column':
                    jQuery(this).appendTo(prev.children('.elementor-widget-wrap'));
                    break;
                case 'section':
                    let row = prev.children('.elementor-container').children('.elementor-row');
                    if (row.length) {
                        jQuery(this).appendTo(row);
                    } else {
                        jQuery(this).appendTo(prev.children('.elementor-container'));
                    }
                    jQuery(this).addClass('elementor-column').addClass('elementor-col-100');
                    prev.children('.elementor-container').css('flex-wrap', 'wrap');
                    break;
            }            
            jQuery(this).removeClass('elementor-hidden').addClass('e-block');
            
            let btn = jQuery(this).find('.elementor-download-button');
            //console.log(btn);
            if (btn.length) {
                let element = jQuery(this).closest('.elementor-element');
                let settings = jQuery(btn.data('clipboard-target')).val();
                
                let filename = element.data('element_type');
                if (filename == 'widget') {
                    filename += '_' + element.data('widget_type');
                }
                settings = '{"version":"1.0.1","title":"'+filename+'","type":"page","content":'+settings+'}';
                filename += '_' + element.data('id') + '.json';
                
                btn.attr('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(settings));
                btn.attr('download', filename);
            }
            
            btn = jQuery(this).find('.elementor-copy-button');
            if (btn.length) {
                let clipboard = new ClipboardJS('#' + btn.attr('id'));
                clipboard.on('success', function (e) {
                    e.clearSelection();
                    jQuery(e.trigger).addClass('animated').addClass('jello');
                    setTimeout(function () {
                        jQuery(e.trigger).removeClass('animated').removeClass('jello');
                    }, 3000);
                    return false;
                });
                clipboard.on('error', function (e) {
                    e.clearSelection();
                });
            }
        });

    });
})(jQuery, window);