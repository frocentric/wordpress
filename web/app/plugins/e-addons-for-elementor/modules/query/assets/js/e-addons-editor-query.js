jQuery(document).ready(function () {
    // spinner in ITEMS
    jQuery(document).on('mousedown','.elementor-control-section_items:not(.elementor-open)',function(e){
        jQuery('.elementor-control-section_items .elementor-section-title').append('<span class="elementor-control-spinner e-add-control-spinner"><i class="fa fa-spinner fa-spin"></i></span>');
    });
    jQuery(document).find('.elementor-control-section_items.elementor-open .elementor-section-title .elementor-control-spinner').remove();
    
    // spinner in CUSTOM META FIELDS
    jQuery(document).on('mousedown','.elementor-control-section_custommeta:not(.elementor-open)',function(e){
        jQuery('.elementor-control-section_custommeta .elementor-section-title').append('<span class="elementor-control-spinner e-add-control-spinner"><i class="fa fa-spinner fa-spin"></i></span>');
    });
    jQuery(document).find('.elementor-control-section_custommeta.elementor-open .elementor-section-title .elementor-control-spinner').remove();
});