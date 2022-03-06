<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_edit_profile_settings', array(

    /*
    |--------------------------------------------------------------------------
    | Username
    |--------------------------------------------------------------------------
    */

    'username_nickname' => array(
        'name'          => 'username_nickname',
        'type'          => 'field-select',
        'label'         => __( 'Username Nickname', 'ninja-forms-user-management' ),
        'width'         => 'full',
        'group'         => 'primary',
        'field_types'   => array(
            'textbox',
            'email'
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Email
    |--------------------------------------------------------------------------
    */

    'email' => array(
        'name'          => 'email',
        'type'          => 'field-select',
        'label'         => __( 'Email', 'ninja-forms-user-management' ),
        'width'         => 'full',
        'group'         => 'primary',
        'field_types'   => array(
            'textbox',
            'email'
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | First Name
    |--------------------------------------------------------------------------
    */

    'first_name' => array(
        'name'          => 'first_name',
        'type'          => 'field-select',
        'label'         => __( 'First Name', 'ninja-forms-user-management' ),
        'width'         => 'full',
        'group'         => 'primary',
        'field_types'   => array(
            'textbox',
            'firstname'
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Last Name
    |--------------------------------------------------------------------------
    */

    'last_name' => array(
        'name'          => 'last_name',
        'type'          => 'field-select',
        'label'         => __( 'Last Name', 'ninja-forms-user-management' ),
        'width'         => 'full',
        'group'         => 'primary',
        'field_types'   => array(
            'textbox',
            'lastname'
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | URL
    |--------------------------------------------------------------------------
    */

    'url' => array(
        'name'          => 'url',
        'type'          => 'field-select',
        'label'         => __( 'URL', 'ninja-forms-user-management' ),
        'width'         => 'full',
        'group'         => 'primary',
        'field_types'   => array(
            'textbox'
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Password
    |--------------------------------------------------------------------------
    */

    'password' => array(
        'name'          => 'password',
        'type'          => 'field-select',
        'label'         => __( 'Password', 'ninja-forms-user-management'),
        'width'         => 'full',
        'group'         => 'primary',
        'field_types'   => array(
            'password'
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Role
    |--------------------------------------------------------------------------
    */

    'role' => array(
        'name'          => 'role',
        'type'          => 'select',
        'label'         => __( 'Role', 'ninja-forms-user-management'),
        'width'         => 'full',
        'value'         => '',
        'group'         => 'primary',
        'options'       => array(
            array( 'label' => '--', 'value' => '', ),
        ),
    ),


    /*
    |--------------------------------------------------------------------------
    | Custom Meta
    |--------------------------------------------------------------------------
    */

    'custom_meta' => array(
        'name'      => 'custom_meta',
        'type'      => 'option-repeater',
        'label'     => __( 'Custom Meta', 'ninja-forms-user-management' ) . ' <a href="#" class="nf-add-new">' .
                        __( 'Add New', 'ninja-forms-user-management' ) . '</a>',
        'width'     => 'full',
        'group'     => 'advanced',
        'tmpl_row'  => 'tmpl-nf-user-registration-custom-meta-repeater-row',
        'value'     => array(),
        'columns'   => array(
            'key' => array(
                'header'    => __( 'Meta Key', 'ninja-forms-user-management' ),
                'default'   => '',
                'options'   => array()
            ),
            'value' => array(
                'header'        => __( 'Field Key', 'ninja-forms-user-management' ),
                'default'       => '',
                'field_types'   => array(
                    'textbox',
                ),
            ),
        ),
    ),

) );
