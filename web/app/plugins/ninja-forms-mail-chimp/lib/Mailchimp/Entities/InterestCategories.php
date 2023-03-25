<?php

namespace NFMailchimp\EmailCRM\Mailchimp\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;
use NFMailchimp\EmailCRM\Mailchimp\Entities\InterestCategory;

/**
 * Describes a collection of Mailchimp Interest Categories
 */
class InterestCategories extends MailChimpEntity
{

	/**
	 * @var InterestCategory[]
	 */
	protected $interestCategories;

	public function __construct()
	{
		$this->interestCategories = [];
	}

	/** @inheritDoc */
	public static function fromArray(array $items): SimpleEntity
	{
		$obj = new static();

		// Entity structure else response structure
		if (isset($items['interestCategories'])) {
			$array = $items['interestCategories'];
		} else {
			$array = $items;
		}
		
		foreach ($array as $list) {
			if (!is_array($list)) {
				if (is_a($list, InterestCategory::class)) {
					$obj->addInterestCategory($list);
					continue;
				} else {
					$list = (array) $list;
				}
			}

			$obj->addInterestCategory(InterestCategory::fromArray($list));
		}
		
		// Entity structure contains listId
		if (isset($items['listId'])) {
			$obj->setListId($items['listId']) ;
		}
		
		return$obj;
	}

	/**
	 * Add an Interest Category to collection
	 *
	 * @param InterestCategory $interestCategory
	 *
	 * @return InterestCategories
	 */
	public function addInterestCategory(InterestCategory $interestCategory): InterestCategories
	{
		$this->interestCategories[$interestCategory->getId()] = $interestCategory;
		return $this;
	}

	/**
	 * Get an Interest Category from collection
	 *
	 * @param string $interestCategoryId
	 *
	 * @return InterestCategory
	 * @throws Exception
	 */
	public function getInterestCategory(string $interestCategoryId): InterestCategory
	{
		if (!isset($this->interestCategories[$interestCategoryId])) {
			throw new Exception();
		}
		return $this->interestCategories[$interestCategoryId];
	}

	/**
	 * Get all Interest Categories in collection
	 *
	 * @return InterestCategory[]
	 */
	public function getInterestCategories(): array
	{
		return $this->interestCategories;
	}
}
