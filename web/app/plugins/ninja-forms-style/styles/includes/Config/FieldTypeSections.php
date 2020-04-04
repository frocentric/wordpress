<?php

return apply_filters( 'ninja_forms_styles_field_type_section', array(
    
    'wrap' => array(
        'name' => 'wrap',
        'label' => __( 'Wrap', 'ninja-forms-layout-styles' ),
        'selector' => '.field-wrap',
        'except' => array(
            'hr',
            'html',
            'password',
            'passwordconfirm',
        )
    ),
    
    'label' => array(
        'name' => 'label',
        'label' => __( 'Label', 'ninja-forms-layout-styles' ),
        'selector' => '.nf-field-label label',
        'except' => array(
            'hr',
            'html',
            'password',
            'passwordconfirm',
        )
    ),
    
    'field' => array(
        'name' => 'field',
        'label' => __( 'Element', 'ninja-forms-layout-styles' ),
        'selector' => '.nf-field-element .ninja-forms-field',
        'except' => array(
            'hr',
            'html',
            'starrating',
            'password',
            'passwordconfirm',
            'checkbox',
            'listcheckbox',
            'listradio',
            'terms',
            'mailchimp-optin',
            'recaptcha',
        )
    ),

    /*
    |--------------------------------------------------------------------------
    | Button Specific Sections
    |--------------------------------------------------------------------------
    */

    'submit-hover' => array(
        'name' => 'submit-hover',
        'label' => __( 'Element Hover', 'ninja-forms-layout-styles' ),
        'selector' => '.nf-field-element .ninja-forms-field:hover',
        'only' => array(
            'submit'
        )
    ),

    /*
    |--------------------------------------------------------------------------
    | List Specific Sections
    |--------------------------------------------------------------------------
    */

    'list-item-row' => array(
        'name' => 'list-item-row',
        'label' => __( 'List Item Row', 'ninja-forms-layout-styles' ),
        'selector' => 'li',
        'only' => array(
            'listcheckbox',
            'listradio',
            'terms'
        )
    ),

    'list-item-label' => array(
        'name' => 'list-item-label',
        'label' => __( 'List Item Label', 'ninja-forms-layout-styles' ),
        'selector' => 'li label',
        'only' => array(
            'listcheckbox',
            'listradio',
            'terms'
        )
    ),

    // Removed these because I'm fairly certian they're not being applied anyway.
    /*'list-item-element' => array(
        'name' => 'list-item-element',
        'label' => __( 'List Item Element', 'ninja-forms-layout-styles' ),
        'selector' => 'li input',
        'only' => array(
            'listcheckbox',
            'listradio',
            'terms'
        )
    ),*/

    /*
    |--------------------------------------------------------------------------
    | Star Rating Specific Sections
    |--------------------------------------------------------------------------
    */

    'rating-item' => array(
        'name' => 'rating-item',
        'label' => __( 'Item', 'ninja-forms-layout-styles' ),
        'selector' => '.starrating-wrap .star',
        'only' => array(
            'starrating',
        )
    ),

    'rating-item-hover' => array(
        'name' => 'rating-item-hover',
        'label' => __( 'Item Hover', 'ninja-forms-layout-styles' ),
        'selector' => '.starrating-wrap .tmp_fs',
        'only' => array(
            'starrating',
        )
    ),

    'rating-item-selected' => array(
        'name' => 'rating-item-selected',
        'label' => __( 'Item Selected', 'ninja-forms-layout-styles' ),
        'selector' => '.starrating-wrap .fullStar',
        'only' => array(
            'starrating',
        )
    ),

    // Not currently using these...
    /*'rating-cancel' => array(
        'name' => 'rating-cancel',
        'label' => __( 'Cancel', 'ninja-forms-layout-styles' ),
        'selector' => '',
        'only' => array(
            'starrating',
        )
    ),

    'rating-cancel-hover' => array(
        'name' => 'rating-cancel-hover',
        'label' => __( 'Cancel Hover', 'ninja-forms-layout-styles' ),
        'selector' => '',
        'only' => array(
            'starrating',
        )
    ),*/

    /*
    |--------------------------------------------------------------------------
    | Product Specific Sections
    |--------------------------------------------------------------------------
     */

    'product-quantity' => array(
        'name' => 'product-quantity',
        'label' => __( 'Quantity', 'ninja-forms-layout-styles' ),
        'selector' => '.nf-product-field .nf-product-quantity .nf-element',
        'only' => array(
            'product',
        )
    ),
    
    /*
    |--------------------------------------------------------------------------
    | Password Specific Sections
    |--------------------------------------------------------------------------
    */

    'password-wrap' => array(
        'name' => 'password-wrap',
        'label' => __( 'Wrap', 'ninja-forms-layout-styles' ),
        'selector' => '.password-wrap',
        'only' => array(
            'password',
        )
    ),

    'password-label' => array(
        'name' => 'password-label',
        'label' => __( 'Label', 'ninja-forms-layout-styles' ),
        'selector' => '.password-wrap .nf-field-label label',
        'only' => array(
            'password',
        )
    ),

    'password-element' => array(
        'name' => 'password-element',
        'label' => __( 'Field', 'ninja-forms-layout-styles' ),
        'selector' => '.password-wrap .nf-field-element .nf-element',
        'only' => array(
            'password',
        )
    ),

    'confirm-wrap' => array(
        'name' => 'confirm-wrap',
        'label' => __( 'Wrap', 'ninja-forms-layout-styles' ),
        'selector' => '.passwordconfirm-wrap',
        'only' => array(
            'passwordconfirm',
        )
    ),

    'confirm-label' => array(
        'name' => 'confirm-label',
        'label' => __( 'Label', 'ninja-forms-layout-styles' ),
        'selector' => '.passwordconfirm-wrap .nf-field-label label',
        'only' => array(
            'passwordconfirm',
        )
    ),

    'confirm-element' => array(
        'name' => 'confirm-element',
        'label' => __( 'Field', 'ninja-forms-layout-styles' ),
        'selector' => '.passwordconfirm-wrap .nf-field-element .nf-element',
        'only' => array(
            'passwordconfirm',
        )
    ),

    // Not currently using these...
    /*'strength-indicator' => array(
        'name' => 'strength-indicator',
        'label' => __( 'Strength Indicator', 'ninja-forms-layout-styles' ),
        'selector' => '',
        'only' => array(
            'password',
        )
    ),

    'strength-indicator-short' => array(
        'name' => 'strength-indicator-short',
        'label' => __( 'Strength Indicator - Very Weak', 'ninja-forms-layout-styles' ),
        'selector' => '',
        'only' => array(
            'password',
        )
    ),

    'strength-indicator-bad' => array(
        'name' => 'strength-indicator-bad',
        'label' => __( 'Strength Indicator - Weak', 'ninja-forms-layout-styles' ),
        'selector' => '',
        'only' => array(
            'password',
        )
    ),

    'strength-indicator-good' => array(
        'name' => 'strength-indicator-good',
        'label' => __( 'Strength Indicator - Medium', 'ninja-forms-layout-styles' ),
        'selector' => '',
        'only' => array(
            'password',
        )
    ),

    'strength-indicator-strong' => array(
        'name' => 'strength-indicator-strong',
        'label' => __( 'Strength Indicator - Strong', 'ninja-forms-layout-styles' ),
        'selector' => '',
        'only' => array(
            'password',
        )
    ),

    'strength-indicator-hint' => array(
        'name' => 'strength-indicator-hint',
        'label' => __( 'Strength Indicator - Hint', 'ninja-forms-layout-styles' ),
        'selector' => '',
        'only' => array(
            'password',
        )
    ),*/

    /*
    |--------------------------------------------------------------------------
    | HR/Divider Specific Sections
    |--------------------------------------------------------------------------
    */

    'hr-element' => array(
        'name' => 'hr-element',
        'label' => __( 'Element', 'ninja-forms-layout-styles' ),
        'selector' => '.nf-field-element .ninja-forms-field',
        'only' => array(
            'hr'
        )
    ),

    /*
    |--------------------------------------------------------------------------
    | HTML/Desc/Text Specific Sections
    |--------------------------------------------------------------------------
    */

    'html_element' => array(
        'name' => 'html_element',
        'label' => __( 'Element', 'ninja-forms-layout-styles' ),
        'selector' => '.html-wrap .nf-field-element',
        'only' => array(
            'html'
        )
    ),

));