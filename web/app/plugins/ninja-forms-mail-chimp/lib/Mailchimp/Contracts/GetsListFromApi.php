<?php


namespace NFMailchimp\EmailCRM\Mailchimp\Contracts;

use NFMailchimp\EmailCRM\Mailchimp\Entities\SingleList;

/**
 * Interface that classes responsible for getting a Mailchimp list via remote API MUST use.
 */
interface GetsListFromApi
{

	/**
	 * Request list details from remote API
	 *
	 * @param string $listId
	 * @return SingleList
	 * @throws \Exception
	 */
	public function requestList(string $listId) : SingleList;
}
