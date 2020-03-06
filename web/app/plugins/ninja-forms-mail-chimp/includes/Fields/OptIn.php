<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Field_OptIn
 */
class NF_MailChimp_Fields_OptIn extends NF_Abstracts_FieldOptIn
{
    protected $_name = 'mailchimp-optin';

    protected $_section = 'common';

    protected $_type = 'mailchimp-optin';

    protected $_templates = 'checkbox';

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = __( 'Mail Chimp OptIn', 'ninja-forms' );
    }
}