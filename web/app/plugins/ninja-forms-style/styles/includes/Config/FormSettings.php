<?php

return array(

    'container_styles' => array(
        'name' => 'container_styles',
        'type' => 'fieldset',
        'label' => __( 'Container Styles', 'ninja-forms-layout-styles' ),
        'width' => 'full',
        'selector' => '#nf-form-{ID}-cont'
    ),

    'title_styles' => array(
        'name' => 'title_styles',
        'type' => 'fieldset',
        'label' => __( 'Title Styles', 'ninja-forms-layout-styles' ),
        'width' => 'full',
        'selector' => '#nf-form-{ID}-cont .nf-form-title h3'
    ),

    'row_styles' => array(
        'name' => 'row_styles',
        'type' => 'fieldset',
        'label' => __( 'Row Styles', 'ninja-forms-layout-styles' ),
        'width' => 'full',
        'selector' => '#nf-form-{ID}-cont .nf-row'
    ),

    'row-odd_styles' => array(
        'name' => 'row-odd_styles',
        'type' => 'fieldset',
        'label' => __( 'Odd Row Styles', 'ninja-forms-layout-styles' ),
        'width' => 'full',
        'selector' => '#nf-form-{ID}-cont .nf-row:nth-child(odd)'
    ),

    'success-msg_styles' => array(
        'name' => 'success-msg_styles',
        'type' => 'fieldset',
        'label' => __( 'Success Response Message Styles', 'ninja-forms-layout-styles' ),
        'width' => 'full',
        'selector' => '#nf-form-{ID}-cont .nf-response-msg'
    ),

    'error_msg_styles' => array(
        'name' => 'error_msg_styles',
        'type' => 'fieldset',
        'label' => __( 'Error Response Message Styles', 'ninja-forms-layout-styles' ),
        'width' => 'full',
        'selector' => '#nf-form-{ID}-cont .nf-error-field-errors'
    ),

);