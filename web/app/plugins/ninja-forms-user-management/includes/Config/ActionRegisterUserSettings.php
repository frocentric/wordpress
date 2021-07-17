<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_register_user_settings', array(

    /*
    |--------------------------------------------------------------------------
    | Username
    |--------------------------------------------------------------------------
    */

    'username' => array(
        'name'          => 'username',
        'type'          => 'field-select',
        'label'         => __( 'Username', 'ninja-forms-user-management' ),
        'width'         => 'full',
        'group'         => 'primary',
        'field_types'   => array(
            'textbox',
            'email',
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
        'deps'          => array(
            'register_user_email' => 0,
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
        'value'         => 'subscriber',
        'group'         => 'primary',
        'options'       => array(),
    ),

    /*
    |--------------------------------------------------------------------------
    | Login User Upon Registration
    |--------------------------------------------------------------------------
    */

    'login_user_upon_registration' => array(
        'name'  => 'login_user_upon_registration',
        'type'  => 'toggle',
        'label' => __( 'Login user upon successful registration.', 'ninja-forms-user-management'),
        'width' => 'full',
        'group' => 'advanced',
        'value' => 1,
    ),

    'refresh_upon_registration' => array(
        'name'  => 'refresh_upon_registration',
        'type'  => 'toggle',
        'label' => __( 'Refresh the page upon registration.', 'ninja-forms-user-management'),
        'width' => 'full',
        'help'  => __( 'This setting will not show success messages that are on your form.',
                        'ninja-forms-user-management' ),
        'group' => 'advanced',
        'value' => 1,
        'deps' => array(
            'login_user_upon_registration' => 1,
        )
    ),

    /*
    |--------------------------------------------------------------------------
    | Register User Email
    |--------------------------------------------------------------------------
    */

    'register_user_email' => array(
        'name'  => 'register_user_email',
        'type'  => 'toggle',
        'label' => __( 'EMAIL CREDENTIALS TO NEWLY CREATED USERS? (GENERATES RANDOM PASSWORD)?',
                        'ninja-forms-user-management'),
        'width' => 'full',
        'group' => 'advanced',
        'value' => 0,
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
                'options' => array()
            ),
            'value' => array(
                'header'    => __( 'Meta Value', 'ninja-forms-user-management' ),
                'default'   => '',
            ),
        ),
    ),
) );
