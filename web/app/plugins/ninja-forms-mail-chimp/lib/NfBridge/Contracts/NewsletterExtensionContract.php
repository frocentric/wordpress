<?php

namespace NFMailchimp\EmailCRM\NfBridge\Contracts;

/**
 * Extend the NF Action for NF Newsletters
 */
interface NewsletterExtensionContract
{

	/**
	 * Get a collection of lists
	 * @param bool $cacheOkay
	 * @return array
	 */
	public function getLists(bool $cacheOkay): array;

	/**
	 * Get Merge Vars for a given list id
	 * @param string $listId
	 * @return array
	 */
	public function getMergeVars(string $listId): array;

	/**
	 * Get Interest Categories for a given list id
	 * @param string $listId
	 * @return array
	 */
	public function getListInterestCategories($listId): array;

	/**
	 * Get Interests for a given list id and interest category id
	 * @param string $listId
	 * @param string $interestCategoryId
	 */
	public function getInterests(string $listId, string $interestCategoryId): array;
}
