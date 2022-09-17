<?php


namespace NFMailchimp\EmailCRM\Mailchimp\Entities\Responses;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;

class SubscriptionSuccessful extends SimpleEntity
{

	/**
	 * @var string
	 */
	protected $status;

	/**
	 * @var string
	 */
	protected $id;
}
