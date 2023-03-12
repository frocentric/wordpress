<?php

namespace NFMailchimp\EmailCRM\Mailchimp\Contracts;

use NFMailchimp\EmailCRM\Mailchimp\Entities\Interests;

/**
 * Interface that classes responsible for getting collection of Mailchimp Interests via remote API MUST use.
 */
interface GetInterestsFromApi
{

	/**
	 * Request collection of interests from remote API
	 *
	 * @param string $listId Id for the list (audience)
	 * @param string $interestCategoryId Id for the interest category
	 * @return Interests
	 * @throws \Exception
	 */
	public function requestInterests(string $listId, string $interestCategoryId): Interests;
}
