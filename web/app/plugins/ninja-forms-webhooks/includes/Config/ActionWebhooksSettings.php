<?php if ( ! defined( 'ABSPATH' ) ) exit;

return array(

    /*
    |--------------------------------------------------------------------------
    | Remote Url
    |--------------------------------------------------------------------------
    */

    'wh-remote-url' => array(
        'name' => 'wh-remote-url',
        'type' => 'textbox',
        'group' => 'primary',
        'label' => __( 'Remote Url', 'ninja-forms-webhooks' ),
        'width' => 'one-half',
        ),

    /*
    |--------------------------------------------------------------------------
    | Remote Method
    |--------------------------------------------------------------------------
    */

    'wh-remote-method' => array(
        'name' => 'wh-remote-method',
        'type' => 'select',
        'group' => 'primary',
        'value' => 'get',
        'label' => __( 'Remote Method', 'ninja-forms-webhooks' ),
        'options' => array(
            array(
                'label' => __( 'Get', 'ninja-forms-webhooks' ),
                'value' => 'get',
            ),
            array(
                'label' => __( 'Post', 'ninja-forms-webhooks' ),
                'value' => 'post'
            ),
        ),
        'width' => 'one-half',
    ),

    /*
    |--------------------------------------------------------------------------
    | Args Option Rep
    |--------------------------------------------------------------------------
    */

        'wh-args' => array(
            'name' => 'wh-args',
            'type' => 'option-repeater',
            'label' => __( 'Args', 'ninja-forms-webhooks' ) . ' <a href="#" class="nf-add-new">' . __( 'Add New', 'ninja-forms-webhooks' )  . '</a>',
            'width' => 'full',
            'group' => 'primary',
            'tmpl_row' => 'tmpl-nf-webhooks-args-repeater-row',
            'value' => array(),
            'columns'   =>array(
                'key' => array(
                    'header' => __( 'Key', 'ninja-forms-webhooks' ),
                    'default' => '',
                    ),
                'value' => array(
                    'header' => __( 'Value', 'ninja-forms-webhooks' ),
                    'default' => '',
                ),
            ),
        ),

    /*
    |--------------------------------------------------------------------------
    | Encode as JSON string
    |--------------------------------------------------------------------------
    */

    'wh-encode-json' => array(
        'name' => 'wh-encode-json',
        'type' => 'toggle',
        'label' => __( 'Encode Args as a JSON String', 'ninja-forms-webhooks' ),
        'width' => 'one-half',
        'group' => 'advanced',
        'help' => __( 'For Example: { firstname:"John", lastname:"Doe" }', 'ninja-forms-webhooks' ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Debug Mode
    |--------------------------------------------------------------------------
    */

    'wh-debug-mode' => array(
        'name' => 'wh-debug-mode',
        'type' => 'toggle',
        'label' => __( 'Run in Debug Mode', 'ninja-forms-webhooks' ),
        'width' => 'one-half',
        'group' => 'advanced',
        'help' => __( 'This will terminate the submission before completion and show the data sent to the remote server and the response received.', 'ninja-forms-webhooks' ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Send JSON String as a Single Variable
    |--------------------------------------------------------------------------
    */

    'wh-json-use-arg' => array(
        'name' => 'wh-json-use-arg',
        'type' => 'toggle',
        'label' => __( 'Send JSON String as a Single Variable', 'ninja-forms-webhooks' ),
        'width' => 'one-half',
        'group' => 'advanced',
        'help'  => __( 'data => { firstname:"John", lastname:"Doe" }', 'ninja-forms-webhooks' ),
        'deps' => array(
            'wh-encode-json' => 1,
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | JSON Variable Name
    |--------------------------------------------------------------------------
    */

    'wh-json-arg' => array(
        'name' => 'wh-json-arg',
        'type' => 'textbox',
        'label' => __( 'JSON Variable Name', 'ninja-forms-webhooks' ),
        'width' => 'one-half',
        'group' => 'advanced',
        'help' => __( 'All the args in the table above will be encoded as a JSON string and passed with this variable name.', 'ninja-forms-webhooks' ),
        'deps' => array(
            'wh-json-use-arg' => 1,
            'wh-encode-json' => 1,
        ),
    ),

);