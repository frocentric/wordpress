<?php

return apply_filters( 'ninja_forms_conditional_logic_action_settings', array(

    // TODO: Register new action settings.

    'conditions' => array(
        'name' => 'conditions',
        'type' => 'action_conditions',
        'group' => 'conditional_logic',
        'label' => __( 'Conditions', 'ninja-forms-conditional-logic' ),
        'placeholder' => '',
        'width' => 'full',
        'value' => '',
    ),

));
