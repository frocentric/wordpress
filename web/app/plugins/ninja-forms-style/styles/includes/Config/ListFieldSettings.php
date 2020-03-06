<?php

return array(

    'list_item_row_styles' => array(
        'name' => 'list_item_row_styles',
        'type' => 'fieldset',
        'label' => __( 'List Item Row Styles', 'ninja-forms-layout-styles' ),
        'width' => 'full',
        'selector' => '.nf-form-content .nf-field-container #nf-field-{ID}-wrap .nf-field-element li',
    ),

    'list_item_label_styles' => array(
        'name' => 'list_item_label_styles',
        'type' => 'fieldset',
        'label' => __( 'List Item Label Styles', 'ninja-forms-layout-styles' ),
        'width' => 'full',
        'selector' => '.nf-form-content .nf-field-container #nf-field-{ID}-wrap .nf-field-element li label',
    ),

    'list_item_element_styles' => array(
        'name' => 'list_item_element_styles',
        'type' => 'fieldset',
        'label' => __( 'List Item Element Styles', 'ninja-forms-layout-styles' ),
        'width' => 'full',
        'selector' => '.nf-form-content .nf-field-container #nf-field-{ID}-wrap .nf-field-element li .nf-element',
    ),

);