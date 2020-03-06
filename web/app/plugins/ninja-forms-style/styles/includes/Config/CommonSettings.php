<?php

return apply_filters( 'ninja_forms_styles_common_settings', array(

    /*
    |--------------------------------------------------------------------------
    | Background Color
    |--------------------------------------------------------------------------
    */

    'background-color' => array(
        'name' => 'background-color',
        'type' => 'color',
        'label' => __( 'Background Color', 'ninja-forms-layout-styles' ),
        'width' => 'full',
        'value' => null,
    ),

    /*
    |--------------------------------------------------------------------------
    | Border Width
    |--------------------------------------------------------------------------
    */

    'border' => array(
        'name' => 'border',
        'type' => 'textbox',
        'label' => __( 'Border Width', 'ninja-forms-layout-styles' ),
        'width' => 'one-half',
        'value' => null,
    ),

    /*
    |--------------------------------------------------------------------------
    | Border Style
    |--------------------------------------------------------------------------
    */

    'border-style' => array(
        'name' => 'border-style',
        'type' => 'select',
        'label' => __( 'Border Style', 'ninja-forms-layout-styles' ),
        'width' => 'one-half',
        'value' => null,
        'options' => array(
            array(
                'label' => '- ' . __( 'None', 'ninja-forms-layout-styles' ),
                'value' => ''
            ),
            array(
                'label' =>  __( 'Solid', 'ninja-forms-layout-styles' ),
                'value' => 'solid'
            ),
            array(
                'label' =>  __( 'Dashed', 'ninja-forms-layout-styles' ),
                'value' => 'dashed'
            ),
            array(
                'label' =>  __( 'Dotted', 'ninja-forms-layout-styles' ),
                'value' => 'dotted'
            ),
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Border Color
    |--------------------------------------------------------------------------
    */

    'border-color' => array(
        'name' => 'border-color',
        'type' => 'color',
        'label' => __( 'Border Color', 'ninja-forms-layout-styles' ),
        'width' => 'one-half',
        'value' => null,
    ),

    /*
    |--------------------------------------------------------------------------
    | Text Color
    |--------------------------------------------------------------------------
    */

    'color' => array(
        'name' => 'color',
        'type' => 'color',
        'label' => __( 'Text Color', 'ninja-forms-layout-styles' ),
        'width' => 'one-half',
        'value' => null,
    ),

    /*
    |--------------------------------------------------------------------------
    | Height
    |--------------------------------------------------------------------------
    */

    'height' => array(
        'name' => 'height',
        'type' => 'textbox',
        'label' => __( 'Height', 'ninja-forms-layout-styles' ),
        'width' => 'one-half',
        'value' => null,
    ),

    /*
    |--------------------------------------------------------------------------
    | Width
    |--------------------------------------------------------------------------
    */

    'width' => array(
        'name' => 'width',
        'type' => 'textbox',
        'label' => __( 'Width', 'ninja-forms-layout-styles' ),
        'width' => 'one-half',
        'value' => null,
    ),

    /*
    |--------------------------------------------------------------------------
    | Font Size
    |--------------------------------------------------------------------------
    */

    'font-size' => array(
        'name' => 'font-size',
        'type' => 'textbox',
        'label' => __( 'Font Size', 'ninja-forms-layout-styles' ),
        'width' => 'one-half',
        'value' => null,
    ),

    /*
    |--------------------------------------------------------------------------
    | Margin
    |--------------------------------------------------------------------------
    */

    'margin' => array(
        'name' => 'margin',
        'type' => 'textbox',
        'label' => __( 'Margin', 'ninja-forms-layout-styles' ),
        'width' => 'one-half',
        'value' => null,
    ),

    /*
    |--------------------------------------------------------------------------
    | Padding
    |--------------------------------------------------------------------------
    */

    'padding' => array(
        'name' => 'padding',
        'type' => 'textbox',
        'label' => __( 'Padding', 'ninja-forms-layout-styles' ),
        'width' => 'one-half',
        'value' => null,
    ),

    /*
    |--------------------------------------------------------------------------
    | Display
    |--------------------------------------------------------------------------
    */

    'display' => array(
        'name' => 'display',
        'type' => 'select',
        'label' => __( 'Display', 'ninja-forms-layout-styles' ),
        'width' => 'one-half',
        'value' => null,
        'options' => array(
            array(
                'label' => '- ' . __( 'Default', 'ninja-forms-layout-styles' ),
                'value' => ''
            ),
            array(
                'label' =>  __( 'Block', 'ninja-forms-layout-styles' ),
                'value' => 'block'
            ),
            array(
                'label' =>  __( 'Inline', 'ninja-forms-layout-styles' ),
                'value' => 'inline'
            ),
            array(
                'label' =>  __( 'Inline Block', 'ninja-forms-layout-styles' ),
                'value' => 'inline-block'
            ),
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Float
    |--------------------------------------------------------------------------
    */

    'float' => array(
        'name' => 'float',
        'type' => 'textbox',
        'label' => __( 'Float', 'ninja-forms-layout-styles' ),
        'width' => 'one-half',
        'value' => null,
    ),

    /*
    |--------------------------------------------------------------------------
    | Advanced
    |--------------------------------------------------------------------------
    */

    'show_advanced_css' => array(
        'name' => 'show_advanced_css',
        'type' => 'toggle',
        'label' => __( 'Show Advanced CSS Properties', 'ninja-forms-layout-styles' ),
        'width' => 'full',
        'value' => 0
    ),

    'advanced' => array(
        'name' => 'advanced',
        'type' => 'textarea',
        'label' => __( 'Advanced CSS', 'ninja-forms-layout-styles' ),
        'value' => null,
        'width' => 'full',
        'deps' => array(
            'show_advanced_css' => 1
        )
    ),

));