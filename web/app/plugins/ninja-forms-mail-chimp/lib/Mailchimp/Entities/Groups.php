<?php


namespace NFMailchimp\EmailCRM\Mailchimp\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;

/**
 * Describes a collection of interest groups.
 */
class Groups extends MailChimpEntity
{

	/**
	 * @var Group[]
	 */
	protected $groups;

	/**
	 * @var Categories[]
	 */
	protected $categories;

	public function __construct()
	{
		$this->groups = [];
		$this->categories = [];
	}

	/** @inheritdoc */
	public static function fromArray(array $items): SimpleEntity
	{
		$obj = new static();
		$handleGroup = function ($obj, $group) {
			if (!is_array($group)) {
				if (is_a($group, Group::class)) {
					$obj->addGroup($group);
					return $obj;
				} else {
					$group = (array)$group;
				}
			}
			$obj->addGroup(Group::fromArray($group));
			return $obj;
		};

		if (isset($items['groups'])) {
			foreach ($items['groups'] as $group) {
				$obj = $handleGroup($obj, $group);
			}
		} else {
			foreach ($items as $group) {
				$obj = $handleGroup($obj, $group);
			}
		}

		if (isset($items['categories'])) {
			foreach ($items['categories'] as $groupId => $category) {
				$obj->addCategoriesForGroup($groupId, $category);
			}
		}
		return $obj;
	}

	/**
	 * Add a group to collection
	 *
	 * @param Group $group
	 *
	 * @return $this
	 */
	public function addGroup(Group $group)
	{
		if (!is_array($this->groups)) {
			$this->groups = $this->getGroups();
		}
		$this->groups[$group->getId()] = $group;
		return $this;
	}

	/**
	 * @param string $id
	 * @return bool
	 */
	public function removeGroup(string $id): bool
	{
		$groups = $this->getGroups();
		if (isset($groups[$id])) {
			unset($groups[$id]);
			$this->groups = $groups;
			return true;
		}
		return false;
	}

	/**
	 * Get a group from collection
	 *
	 * @param string $id
	 *
	 * @return Group
	 * @throws Exception
	 */
	public function getGroup(string $id): Group
	{
		if (!$this->hasGroup($id)) {
			throw new Exception();
		}
		return $this->getGroups()[$id];
	}

	/**
	 * @param string $id
	 * @return bool
	 */
	public function hasGroup(string $id): bool
	{
		return isset($this->getGroups()[$id]);
	}

	public function getGroups(): array
	{
		return is_array($this->groups) ? $this->groups : [];
	}

	/**
	 * @param string $categoryId
	 *
	 * @return mixed|Categories
	 * @throws Exception
	 */
	public function getCategoriesForGroup(string $categoryId)
	{
		if (!$this->hasCategoriesForGroup($categoryId)) {
			throw new Exception();
		}
		return $this->categories[$categoryId];
	}

	/**
	 * @param string $categoryId
	 *
	 * @return bool
	 */
	public function hasCategoriesForGroup(string $categoryId): bool
	{
		return isset($this->categories[$categoryId]);
	}

	/**
	 * @param string $categoryId
	 * @param Categories $categories
	 *
	 * @return Groups
	 */
	public function addCategoriesForGroup(string $categoryId, Categories $categories): Groups
	{
		$this->categories[$categoryId] = $categories;
		return $this;
	}
}
