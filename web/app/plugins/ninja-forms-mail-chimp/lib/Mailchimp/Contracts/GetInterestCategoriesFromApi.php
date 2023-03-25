<?php

namespace NFMailchimp\EmailCRM\Mailchimp\Contracts;

use NFMailchimp\EmailCRM\Mailchimp\Entities\InterestCategories;

/**
 * Interface that classes responsible for getting collection of Mailchimp Interest Categories via remote API MUST use.
 */
interface GetInterestCategoriesFromApi
{

	/**
	 * Request collection of interest categories from remote API
	 *
	 * @param string $listId
	 * @return InterestCategories
	 * @throws \Exception
	 */

	public function requestInterestCategories(string $listId): InterestCategories;
}
