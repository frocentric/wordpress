<?php

return apply_filters( 'ninja_forms_multi_part_advanced_settings', array(

    'multi_part' => array(
        'mp_validate'       => array(
            'name'          => 'mp_validate',
            'type'          => 'toggle',
            'label'         => __( 'Validate each part', 'ninja-forms-multi-part' ),
            'width'         => 'full',
            'group'         => 'primary',
        ),

        'mp_breadcrumb'       => array(
            'name'          => 'mp_breadcrumb',
            'type'          => 'toggle',
            'label'         => __( 'Show Breadcrumbs', 'ninja-forms-multi-part' ),
            'width'         => 'full',
            'group'         => 'primary',
            'value'			=> 1,
        ),

        'mp_progress_bar'       => array(
            'name'          => 'mp_progress_bar',
            'type'          => 'toggle',
            'label'         => __( 'Show Progress Bar ', 'ninja-forms-multi-part' ),
            'width'         => 'full',
            'group'         => 'primary',
            'value'         => 1,
        ),

        'mp_display_titles'       => array(
            'name'          => 'mp_display_titles',
            'type'          => 'toggle',
            'label'         => __( 'Show Part Titles ', 'ninja-forms-multi-part' ),
            'width'         => 'full',
            'group'         => 'primary',
            'value'         => 0,
        ),

        'mp_prev_label'       => array(
            'name'          => 'mp_prev_label',
            'type'          => 'textbox',
            'label'         => __( 'Previous Button Label ', 'ninja-forms-multi-part' ),
            'width'         => 'full',
            'group'         => 'primary',
            'placeholder'   => __( 'Previous', 'ninja-forms-multi-part' ),
        ),

        'mp_next_label'       => array(
            'name'          => 'mp_next_label',
            'type'          => 'textbox',
            'label'         => __( 'Next Button Label ', 'ninja-forms-multi-part' ),
            'width'         => 'full',
            'group'         => 'primary',
            'placeholder'   => __( 'Next', 'ninja-forms-multi-part' ),
        ),

    )
) );
