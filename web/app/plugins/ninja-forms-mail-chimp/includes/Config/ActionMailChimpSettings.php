<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'nf_mail_chimp_action_mail_chimp_settings', array(

    /*
    |--------------------------------------------------------------------------
    | Double Opt-In
    |--------------------------------------------------------------------------
    */

    'double_opt_in' => array(
        'name' => 'double_opt_in',
        'type' => 'toggle',
        'label' => __( 'Require subscribers to confirm their subscription', 'ninja-forms-mail-chimp' ),
        'group' => 'advanced',
        'width' => 'full',
	    'value' => 0
    ),

));
