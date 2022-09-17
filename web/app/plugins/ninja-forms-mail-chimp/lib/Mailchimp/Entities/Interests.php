<?php

namespace NFMailchimp\EmailCRM\Mailchimp\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;
use NFMailchimp\EmailCRM\Mailchimp\Entities\Interest;

/**
 * Describes a collection of Mailchimp Interests within an Interest Category
 */
class Interests extends MailChimpEntity
{

	/**
	 * @var Interest[]
	 */
	protected $interests;

	public function __construct()
	{
		$this->interests = [];
	}

	public static function fromArray(array $items): SimpleEntity
	{
		$obj = new static();
		// Entity structure else response structure
		if (isset($items['interests'])) {
			$array = $items['interests'];
		} else {
			$array = $items;
		}
		foreach ($array as $list) {
			if (!is_array($list)) {
				if (is_a($list, Interest::class)) {
					$obj->addInterest($list);
					continue;
				} else {
					$list = (array) $list;
				}
			}

			$interestEntity = Interest::fromArray($list);

			$obj->addInterest($interestEntity);
		}

		// Entity structure contains listId
		if (isset($items['listId'])) {
			$obj->setListId($items['listId']);
		}

		return$obj;
	}

	/**
	 * Add an Interest to collection
	 *
	 * @param Interest $interest
	 * @return \NFMailchimp\EmailCRM\Mailchimp\Entities\Interests
	 */
	public function addInterest(Interest $interest): Interests
	{
		$this->interests[$interest->getId()] = $interest;
		return $this;
	}

	/**
	 * Append collection of interests to existing collection
	 * @param \NFMailchimp\EmailCRM\Mailchimp\Entities\Interests $interests
	 * @return \NFMailchimp\EmailCRM\Mailchimp\Entities\Interests
	 */
	public function appendInterests(Interests $interests): Interests
	{

		$newInterests = $interests->getInterests();

		foreach ($newInterests as $interest) {
			$this->addInterest($interest);
		}

		return $this;
	}

	/**
	 * Get an Interest from collection
	 *
	 * @param string $interestId
	 *
	 * @return Interest
	 * @throws Exception
	 */
	public function getInterest(string $interestId): Interest
	{
		if (!isset($this->interests[$interestId])) {
			throw new Exception();
		}
		return $this->interests[$interestId];
	}

	/**
	 * Get all Interests in collection
	 *
	 * @return Interest[]
	 */
	public function getInterests(): array
	{
		return $this->interests;
	}

	/**
	 * Returns boolean if segment exists
	 * @param string $id
	 * @return bool
	 */
	public function hasInterest($id): bool
	{
		return isset($this->getInterests()[$id]);
	}
}
