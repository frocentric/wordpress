<?php

namespace NFMailchimp\EmailCRM\Mailchimp\ApiRequests;

use NFMailchimp\EmailCRM\Mailchimp\MailchimpApi;
use NFMailchimp\EmailCRM\Mailchimp\Contracts\GetsListsFromApi;
use NFMailchimp\EmailCRM\Mailchimp\Entities\Lists;

/**
 * Retrieves Mailchimp lists structured as Lists entity
 */
class GetLists implements GetsListsFromApi
{

	/**
	 * @var MailchimpApi
	 */
	protected $mailchimpApi;

	/**
	 * @param MailchimpApi $mailchimpApi
	 */
	public function __construct(MailchimpApi $mailchimpApi)
	{
		$this->mailchimpApi = $mailchimpApi;
	}

	/**
	 * Request collection of lists from remote API
	 *
	 * If exception is returned, return empty Lists
	 *
	 * @return Lists
	 */
	public function requestLists(): Lists
	{
		try {

			$response = $this->mailchimpApi->getLists(['count' => 500]);
			return Lists::fromArray((array) $response->lists);
		} catch (\Exception $e) {
			
			error_log(self::class . '::' . __FUNCTION__).' - ' . $e->getMessage();
			return new Lists();
		}
	}
}
