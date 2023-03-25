<?php

namespace NFMailchimp\EmailCRM\Mailchimp\Contracts;

use NFMailchimp\EmailCRM\Mailchimp\Entities\Segments;

/**
 * Interface that classes responsible for getting collection of Mailchimp Segments via remote API MUST use.
 */
interface GetSegmentsFromApi
{

	/**
	 * Request collection of Segments from remote API
	 *
	 * @param string $listId
	 * @return Segments
	 */
	public function requestSegments(string $listId): Segments;
}
