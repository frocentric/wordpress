<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_register_user_settings', array(

    'password_reset' => array(
        'id'        => 'password_reset',
        'tag'       => '{user_management:password_reset}',
        'label'     => __( 'Password Reset', 'ninja-forms-user-management' ),
        'callback'  => 'get_password_reset',
    ),

    'logout' => array(
        'id'        => 'logout',
        'tag'       => '{user_management:logout}',
        'label'     => __( 'Logout Link', 'ninja-forms-user-management' ),
        'callback'  => 'logout_link',
    ),
));