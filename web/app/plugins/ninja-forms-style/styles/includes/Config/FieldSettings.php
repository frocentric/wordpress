<?php

return array(

    'wrap_styles' => array(
        'name' => 'wrap_styles',
        'type' => 'fieldset',
        'label' => __( 'Wrap Styles', 'ninja-forms-layout-styles' ),
        'width' => 'full',
        'selector' => '.nf-form-content .nf-field-container #nf-field-{ID}-wrap'
    ),

    'label_styles' => array(
        'name' => 'label_styles',
        'type' => 'fieldset',
        'label' => __( 'Label Styles', 'ninja-forms-layout-styles' ),
        'width' => 'full',
        'selector' => '.nf-form-content .nf-field-container #nf-field-{ID}-wrap .nf-field-label label'
    ),

    'element_styles' => array(
        'name' => 'element_styles',
        'type' => 'fieldset',
        'label' => __( 'Element Styles', 'ninja-forms-layout-styles' ),
        'width' => 'full',
        'selector' => '.nf-form-content .nf-field-container #nf-field-{ID}-wrap .nf-field-element .ninja-forms-field'
    ),

);
