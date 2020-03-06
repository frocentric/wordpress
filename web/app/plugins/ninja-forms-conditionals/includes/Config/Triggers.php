<?php

return apply_filters( 'ninja_forms_conditional_logic_triggers', array(

    /*
    |--------------------------------------------------------------------------
    | Hide Field
    |--------------------------------------------------------------------------
    */

    'hide_field' => array(
        'key'      => 'hide_field',
        'label'    => __( 'Hide Field', 'ninja-forms-conditional-logic' ),
        'instance' => new NF_ConditionalLogic_Triggers_HideField()
    ),

    /*
    |--------------------------------------------------------------------------
    | Show Field
    |--------------------------------------------------------------------------
    */

    'show_field' => array(
        'key'      => 'show_field',
        'label'    => __( 'Show Field', 'ninja-forms-conditional-logic' ),
        'instance' => new NF_ConditionalLogic_Triggers_ShowField()
    ),

    /*
    |--------------------------------------------------------------------------
    | Change Value
    |--------------------------------------------------------------------------
    */

    'change_value' => array(
        'key'      => 'change_value',
        'label'    => __( 'Change Value', 'ninja-forms-conditional-logic' ),
        'instance' => ''
    ),

));
