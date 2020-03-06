<?php

return apply_filters( 'ninja_forms_styles_setting_groups', array(

    /*
    |--------------------------------------------------------------------------
    | Form Styles
    |--------------------------------------------------------------------------
    */

    'form_settings' => array(
        'name' => 'form_settings',
        'label' => __( 'Form Styles', 'ninja-forms-layout-styles' ),
        'sections' => array(
            'container' => array(
                'name' => 'container',
                'label' => __( 'Container Styles', 'ninja-forms-layout-styles' ),
                'selector' => '.nf-form-cont'
            ),
            'title' => array(
                'name' => 'title',
                'label' => __( 'Title Styles', 'ninja-forms-layout-styles' ),
                'selector' => '.nf-form-title h3'
            ),
            'required-message' => array(
                'name' => 'required-message',
                'label' => __( 'Required Message Styles', 'ninja-forms-layout-styles' ),
                'selector' => '.nf-form-fields-required'
            ),
            'row' => array(
                'name' => 'row',
                'label' => __( 'Row Styles', 'ninja-forms-layout-styles' ),
                'selector' => '.nf-row'
            ),
            'row-odd' => array(
                'name' => 'row-odd',
                'label' => __( 'Odd Row Styles', 'ninja-forms-layout-styles' ),
                'selector' => '.nf-row:nth-child(odd)'
            ),
            'success-msg' => array(
                'name' => 'success-msg',
                'label' => __( 'Success Response Message Styles', 'ninja-forms-layout-styles' ),
                'selector' => '.nf-response-msg'
            ),
        )
    ),

    /*
    |--------------------------------------------------------------------------
    | Field Styles
    |--------------------------------------------------------------------------
    */

    'field_settings' => array(
        'name' => 'field_settings',
        'label' => __( 'Default Field Styles', 'ninja-forms-layout-styles' ),
        'sections' => array(
            'wrap' => array(
                'name' => 'wrap',
                'label' => __( 'Wrap Styles', 'ninja-forms-layout-styles' ),
                'selector' => '.nf-form-content .nf-field-container .field-wrap'
            ),
            'label' => array(
                'name' => 'label',
                'label' => __( 'Label Styles', 'ninja-forms-layout-styles' ),
                'selector' => '.nf-form-content .nf-field-label label'
            ),
            'field' => array(
                'name' => 'field',
                'label' => __( 'Element Styles', 'ninja-forms-layout-styles' ),
                'selector' => '.nf-form-content .nf-field-element .ninja-forms-field:not(select)'
            ),
        )
    ),

    /*
    |--------------------------------------------------------------------------
    | Field Type Styles
    |--------------------------------------------------------------------------
    */

    'field_type' => array(
        'name' => 'field_type',
        'label' => __( 'Field Type Styles', 'ninja-forms-layout-styles' ),
        'sections' => array(),
        'selector' => '.nf-form-content .nf-field-container.{field-type}-container'
    ),

    /*
    |--------------------------------------------------------------------------
    | Error Styles
    |--------------------------------------------------------------------------
    */

    'error_settings' => array(
        'name' => 'error_settings',
        'label' => __( 'Error Styles', 'ninja-forms-layout-styles' ),
        'sections' => array(
            'error_msg' => array(
                'name' => 'error_msg',
                'label' => __( 'Main Error Message Wrap Styles', 'ninja-forms-layout-styles' ),
                'selector' => '.nf-form-errors .nf-error-msg'
            ),
            'field_error_wrap' => array(
                'name' => 'field_error_wrap',
                'label' => __( 'Error Field Wrap Styles', 'ninja-forms-layout-styles' ),
                'selector' => '.nf-form-content .nf-error-wrap'
            ),
            'field_error_label' => array(
                'name' => 'field_error_label',
                'label' => __( 'Error Label Styles', 'ninja-forms-layout-styles' ),
                'selector' => '.nf-error .nf-field-label label'
            ),
            'field_error_element' => array(
                'name' => 'field_error_element',
                'label' => __( 'Error Element Styles', 'ninja-forms-layout-styles' ),
                'selector' => '.nf-error .nf-field-element .nf-element'
            ),
            'field_error_msg' => array(
                'name' => 'field_error_msg',
                'label' => __( 'Error Message Styles', 'ninja-forms-layout-styles' ),
                'selector' => '.nf-form-content .nf-error-wrap .nf-error-msg'
            ),
        )
    ),

    /*
    |--------------------------------------------------------------------------
    | DatePicker Styles
    |--------------------------------------------------------------------------
    */

    'datepicker_settings' => array(
        'name' => 'datepicker_settings',
        'label' => __( 'DatePicker Styles', 'ninja-forms-layout-styles' ),
        'sections' => array(
            'datepicker_container' => array(
                'name' => 'datepicker_container',
                'label' => __( 'DatePicker Container', 'ninja-forms-layout-styles' ),
                'selector' => 'html body .pika-single'
            ),
            'datepicker_header' => array(
                'name' => 'datepicker_header',
                'label' => __( 'DatePicker Header', 'ninja-forms-layout-styles' ),
                'selector' => 'html body .pika-title'
            ),
            'datepicker_week' => array(
                'name' => 'datepicker_week',
                'label' => __( 'DatePicker Week Days', 'ninja-forms-layout-styles' ),
                'selector' => 'html body .pika-single th'
            ),
            'datepicker_days' => array(
                'name' => 'datepicker_days',
                'label' => __( 'DatePicker Days', 'ninja-forms-layout-styles' ),
                'selector' => 'html body .pika-button'
            ),
            'datepicker_days_hover' => array(
                'name' => 'datepicker_days_hover',
                'label' => __( 'DatePicker Day Hover', 'ninja-forms-layout-styles' ),
                'selector' => 'html body .pika-single .pika-button:hover'
            ),
            'datepicker_today' => array(
                'name' => 'datepicker_today',
                'label' => __( 'DatePicker Today', 'ninja-forms-layout-styles' ),
                'selector' => 'html body .is-today .pika-button'
            ),
            'datepicker_selected' => array(
                'name' => 'datepicker_selected',
                'label' => __( 'DatePicker Selected', 'ninja-forms-layout-styles' ),
                'selector' => 'html body .is-selected .pika-button'
            ),
            'datepicker_prev' => array(
                'name' => 'datepicker_prev',
                'label' => __( 'DatePicker Previous Link', 'ninja-forms-layout-styles' ),
                'selector' => 'html body .pika-prev'
            ),
            'datepicker_next' => array(
                'name' => 'datepicker_next',
                'label' => __( 'DatePicker Next Link', 'ninja-forms-layout-styles' ),
                'selector' => 'html body .pika-next'
            ),
        )
    ),

    /*
    |--------------------------------------------------------------------------
    | MultiPart Styles
    |--------------------------------------------------------------------------
    */
    
    'multipart_settings' => array(
        'name' => 'multipart_settings',
        'label' => __( 'MultiPart Styles', 'ninja-forms-layout-styles' ),
        'sections' => array(
            'breadcrumb_container_styles' => array(
                'name' => 'breadcrumb_container_styles',
                'type' => 'fieldset',
                'label' => __( 'Breadcrumb Container Styles', 'ninja-forms-layout-styles' ),
                'width' => 'full',
                'selector' => '.nf-form-content .nf-mp-header .nf-breadcrumbs',
            ),

            'breadcrumb_buttons_styles' => array(
                'name' => 'breadcrumb_buttons_styles',
                'type' => 'fieldset',
                'label' => __( 'Breadcrumb Buttons Styles', 'ninja-forms-layout-styles' ),
                'width' => 'full',
                'selector' => '.nf-breadcrumbs li a.nf-breadcrumb',
            ),

            'breadcrumb_button_hover_styles' => array(
                'name' => 'breadcrumb_button_hover_styles',
                'type' => 'fieldset',
                'label' => __( 'Breadcrumb Button Hover Styles', 'ninja-forms-layout-styles' ),
                'width' => 'full',
                'selector' => '.nf-breadcrumbs li a.nf-breadcrumb:hover',
            ),

            'breadcrumb_active_button_styles' => array(
                'name' => 'breadcrumb_active_button_styles',
                'type' => 'fieldset',
                'label' => __( 'Breadcrumb Active Button Styles', 'ninja-forms-layout-styles' ),
                'width' => 'full',
                'selector' => '.nf-breadcrumbs .active > .nf-breadcrumb',
            ),

            'progress_bar_container_styles' => array(
                'name' => 'progress_bar_container_styles',
                'type' => 'fieldset',
                'label' => __( 'Progress Bar Container Styles', 'ninja-forms-layout-styles' ),
                'width' => 'full',
                'selector' => '.nf-mp-header .nf-progress-container',
            ),

            'progress_bar_fill_styles' => array(
                'name' => 'progress_bar_fill_styles',
                'type' => 'fieldset',
                'label' => __( 'Progress Bar Fill Styles', 'ninja-forms-layout-styles' ),
                'width' => 'full',
                'selector' => '.nf-mp-header .nf-progress',
            ),

            'part_titles_styles' => array(
                'name' => 'part_titles_styles',
                'type' => 'fieldset',
                'label' => __( 'Part Titles Styles', 'ninja-forms-layout-styles' ),
                'width' => 'full',
                'selector' => '.nf-mp-header h3',
            ),

            'navigation_container_styles' => array(
                'name' => 'navigation_container_styles',
                'type' => 'fieldset',
                'label' => __( 'Navigation Container Styles', 'ninja-forms-layout-styles' ),
                'width' => 'full',
                'selector' => '.nf-mp-footer .nf-next-previous',
            ),

            'previous_button_styles' => array(
                'name' => 'previous_button_styles',
                'type' => 'fieldset',
                'label' => __( 'Previous Button Styles', 'ninja-forms-layout-styles' ),
                'width' => 'full',
                'selector' => '.nf-previous-item > .nf-previous',
            ),

            'next_button_styles' => array(
                'name' => 'next_button_styles',
                'type' => 'fieldset',
                'label' => __( 'Next Button Styles', 'ninja-forms-layout-styles' ),
                'width' => 'full',
                'selector' => '.nf-next-item .nf-next',
            ),

            'navigation_hover_styles' => array(
                'name' => 'navigation_hover_styles',
                'type' => 'fieldset',
                'label' => __( 'Navigation Hover Styles', 'ninja-forms-layout-styles' ),
                'width' => 'full',
                'selector' => '.nf-next-previous input:hover',
            ),
        )
    ),

));
