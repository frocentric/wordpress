<?php

namespace NFMailchimp\NinjaForms\Mailchimp\Handlers;

/**
 * Add Mailchimp's version of standard NF opt in field
 *
 * Carryover from NF 3.0
 */
class MailchimpOptIn extends \NF_Abstracts_FieldOptIn
{
	protected $_name = 'mailchimp-optin';

	protected $_section = 'common';

	protected $_type = 'mailchimp-optin';

	protected $_templates = 'checkbox';

	/**
	 * Construct Mailchimp's version of a NF Opt In field
	 */
	public function __construct()
	{
		parent::__construct();

		$this->_nicename = __('Mailchimp Opt-In', 'ninja-forms');
	}
}
