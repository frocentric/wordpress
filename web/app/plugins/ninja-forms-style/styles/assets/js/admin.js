jQuery( document ).ready( function( $ ){

    /*
     * Initialize Color Picker Options
     */
    $( '.js-ninja-forms-styles-color-field' ).wpColorPicker();

    /*
     * Initialize CodeMirror
     */
    $( 'textarea.setting-advanced' ).each( function( index, textarea ){
        ninjaFormsStyles.initCodeMirror( textarea );
    });

    /*
     * Initialize Metaboxes
     *
     * Note: Run AFTER initializing CodeMirror
     */
    postboxes.add_postbox_toggles(pagenow);
    $( '.postbox' ).each( function() {
        $( this ).addClass( 'closed' );
    });

    /*
     * Field Type Selector
     */
    $( '#ninja-forms-styles-field-type-selector' ).change( function(){
        window.location.href = window.location.href + '&field_type=' + $( this ).val();
    });

    /*
     * Toggle Advanced CSS
     */
    var advancedCSS = $( '.row-ninja-forms--display, .row-ninja-forms--float, .row-ninja-forms--advanced' );
    advancedCSS.hide();
    $( '#advanced_css' ).change( function(){
        var isChecked = $( this ).prop( 'checked' );
        return ( isChecked ) ? advancedCSS.show() : advancedCSS.hide();
    });

});

var ninjaFormsStyles = {

    initCodeMirror: function( textarea ) {
        CodeMirror.fromTextArea( textarea, {
            lineNumbers: true,
        } );
    },

};
