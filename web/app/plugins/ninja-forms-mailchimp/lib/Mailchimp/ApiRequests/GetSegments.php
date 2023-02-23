<?php

namespace NFMailchimp\EmailCRM\Mailchimp\ApiRequests;

use NFMailchimp\EmailCRM\Mailchimp\MailchimpApi;
use NFMailchimp\EmailCRM\Mailchimp\Contracts\GetSegmentsFromApi;
use NFMailchimp\EmailCRM\Mailchimp\Entities\Segments;

/**
 * Gets a collection of interest categories from remote API
 */
class GetSegments implements GetSegmentsFromApi
{

	/** @var MailchimpApi */
	protected $mailchimpApi;

	/**
	 * @param MailchimpApi $mailchimpApi
	 */
	public function __construct(MailchimpApi $mailchimpApi)
	{
		$this->mailchimpApi = $mailchimpApi;
	}

	/**
	 * @param string $listId
	 * @return Segments
	 * @throws \Exception
	 */
	public function requestSegments(string $listId): Segments
	{
		try {
			$response = $this->mailchimpApi->getSegments($listId, ['count' => 500]);
			$segments = Segments::fromArray((array)$response->segments);
			$segments->setListId($listId);
			return $segments;
		} catch (\Exception $e) {
			error_log(self::class . '::' . __FUNCTION__).' - ' . $e->getMessage();
			return new Segments();
		}
	}
}
