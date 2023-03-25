<?php


namespace NFMailchimp\EmailCRM\Mailchimp\Contracts;

use NFMailchimp\EmailCRM\Mailchimp\Entities\Groups;

interface GetsGroupsFromApi
{
	/**
	 * Request interest groups from the
	 *
	 * @param string $listId
	 * @return Groups
	 * @throws \Exception
	 */
	public function requestGroups(string $listId) : Groups;
}
