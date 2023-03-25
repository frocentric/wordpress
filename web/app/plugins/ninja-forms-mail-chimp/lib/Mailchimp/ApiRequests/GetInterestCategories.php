<?php

namespace NFMailchimp\EmailCRM\Mailchimp\ApiRequests;

use NFMailchimp\EmailCRM\Mailchimp\MailchimpApi;
use NFMailchimp\EmailCRM\Mailchimp\Contracts\GetInterestCategoriesFromApi;

use NFMailchimp\EmailCRM\Mailchimp\Entities\InterestCategories;

/**
 * Gets a collection of interest categories from remote API
 */
class GetInterestCategories implements GetInterestCategoriesFromApi
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

	/** @inheritDoc */
	public function requestInterestCategories(string $listId): InterestCategories
	{
		try {
			$r = $this->mailchimpApi->getInterestCategories($listId, ['count' => 500]);
			$interestCategories = InterestCategories::fromArray((array) $r->categories);

			$interestCategories->setListId($listId);

			return $interestCategories;
		} catch (\Exception $e) {
			error_log(self::class . '::' . __FUNCTION__).' - ' . $e->getMessage();
			return new InterestCategories();
		}
	}
}
