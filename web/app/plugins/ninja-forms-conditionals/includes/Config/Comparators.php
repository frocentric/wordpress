<?php

return apply_filters( 'ninja_forms_conditional_logic_comparators', array(

    /*
    |--------------------------------------------------------------------------
    | Equals / Does Not Equal
    |--------------------------------------------------------------------------
    */

    'equal' => array(
        'key'      => 'equal',
        'label'    => __( 'Equals', 'ninja-forms-conditional-logic' ),
        'instance' => new NF_ConditionalLogic_Comparators_Equal()
    ),

    'notequal' => array(
        'key'      => 'notequal',
        'label'    => __( 'Does Not Equal', 'ninja-forms-conditional-logic' ),
        'instance' => new NF_ConditionalLogic_Comparators_NotEqual()
    ),

    /*
    |--------------------------------------------------------------------------
    | Contains / Does Not Contain
    |--------------------------------------------------------------------------
    */

    'contains' => array(
        'key'      => 'contains',
        'label'    => __( 'Contains', 'ninja-forms-conditional-logic' ),
        'instance' => new NF_ConditionalLogic_Comparators_Contains()
    ),

    'notcontains' => array(
        'key'      => 'notcontains',
        'label'    => __( 'Does Not Contain', 'ninja-forms-conditional-logic' ),
        'instance' => new NF_ConditionalLogic_Comparators_NotContains()
    ),

    /*
    |--------------------------------------------------------------------------
    | Greater Than / Less Than
    |--------------------------------------------------------------------------
    */

    'greater' => array(
        'key'      => 'greater',
        'label'    => __( 'Greater Than', 'ninja-forms-conditional-logic' ),
        'instance' => new NF_ConditionalLogic_Comparators_Greater()
    ),

    'greaterequal' => array(
        'key'      => 'greaterequal',
        'label'    => __( 'Greater Than Or Equal', 'ninja-forms-conditional-logic' ),
        'instance' => new NF_ConditionalLogic_Comparators_GreaterEqual()
    ),

    'less' => array(
        'key'      => 'less',
        'label'    => __( 'Less Than', 'ninja-forms-conditional-logic' ),
        'instance' => new NF_ConditionalLogic_Comparators_Less()
    ),

    'lessequal' => array(
        'key'      => 'lessequal',
        'label'    => __( 'Less Than Or Equal', 'ninja-forms-conditional-logic' ),
        'instance' => new NF_ConditionalLogic_Comparators_LessEqual()
    ),

));
