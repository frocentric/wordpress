<?php


namespace NFMailchimp\EmailCRM\Mailchimp\ApiRequests;

use NFMailchimp\EmailCRM\Mailchimp\MailchimpApi;
use NFMailchimp\EmailCRM\Mailchimp\Contracts\GetsMergeFieldsFromApi;
use NFMailchimp\EmailCRM\Mailchimp\Entities\MergeVars;
use NFMailchimp\EmailCRM\Mailchimp\Entities\SingleList;

/**
 * Get Merge vars for a list
 */
class GetMergeFields implements GetsMergeFieldsFromApi
{

	/** @var MailchimpApi */
	protected $mailchimpApi;

	/** @var SingleList */
	protected $list;

	/**
	 * @param NfMailchimpLists $mailchimpApi
	 */
	public function __construct(MailchimpApi $mailchimpApi)
	{
		$this->mailchimpApi = $mailchimpApi;
	}

	/**
	 * Request merge vars from remote API
	 *
	 * @param string $listId
	 * @return MergeVars
	 */
	public function requestMergeFields(string $listId): MergeVars
	{
		try {
			$response = $this->mailchimpApi->getMergeFields($listId, ['count' => 500]);
			$mergeVars = MergeVars::fromArray((array)$response->merge_fields);

			return $mergeVars;
		} catch (\Exception $e) {
			error_log(self::class . '::' . __FUNCTION__).' - ' . $e->getMessage();
			return new MergeVars();
		}
	}
}
