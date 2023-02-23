<?php


namespace NFMailchimp\EmailCRM\Mailchimp\Contracts;

use NFMailchimp\EmailCRM\Mailchimp\Entities\MergeVars;

/**
 * Contract for classes that get merge fields from remote API
 */
interface GetsMergeFieldsFromApi
{

	/**
	 * Request merge vars from remote API
	 *
	 * @return MergeVars
	 * @throws \Exception
	 */
	public function requestMergeFields(string $listId): MergeVars;
}
