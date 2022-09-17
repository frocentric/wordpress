<?php

namespace NFMailchimp\EmailCRM\Mailchimp\Contracts;

use NFMailchimp\EmailCRM\Mailchimp\Entities\Lists;

/**
 * Interface that classes responsible for getting collection of Mailchimp lists via remote API MUST use.
 */
interface GetsListsFromApi
{

	/**
	 * Request collection of lists from remote API
	 *
	 * @return Lists
	 * @throws \Exception
	 */
	public function requestLists(): Lists;
}
