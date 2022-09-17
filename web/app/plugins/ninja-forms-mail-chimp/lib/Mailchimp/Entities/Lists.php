<?php


namespace NFMailchimp\EmailCRM\Mailchimp\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;

/**
 * Describes a collection of Mailchimp lists
 */
class Lists extends MailChimpEntity
{

	/**
	 * @var SingleList[]
	 */
	protected $lists;

	public function __construct()
	{
		$this->lists = [];
	}

	public static function fromArray(array $items): SimpleEntity
	{
		$obj = new static();
		foreach ($items as $list) {
			if (! is_array($list)) {
				if (is_a($list, SingleList::class)) {
					$obj->addList($list);
					continue;
				} else {
					$list = (array)$list;
				}
			}

			$obj->addList(SingleList::fromArray($list));
		}
		return$obj;
	}

	public function toUiFieldConfig(): array
	{
		$fieldConfig = [
			'fieldType' => 'select',
			'required' => true,
			'fieldId' => 'mc-select-field',
			'options' => [],
			'label' => 'Choose List'
		];
		$fieldConfig['options'][] = [
			'value' => null,
			'label' => ' --- '
		];

		if (is_array($lists = $this->getLists())) {
			/** @var SingleList $list */
			foreach ($lists as $list) {
				$fieldConfig[ 'options' ][] = [
					'value' => $list->getListId(),
					'label' => $list->getName(),
				];
			}
		}
		return [$fieldConfig];
	}

	/**
	 * Add a list to collection
	 *
	 * @param SingleList $list
	 *
	 * @return Lists
	 */
	public function addList(SingleList $list): Lists
	{
		$this->lists[$list->getListId()] = $list;
		return $this;
	}

	/**
	 * Get a list from collection
	 *
	 * @param string $listId
	 *
	 * @return SingleList
	 * @throws Exception
	 */
	public function getList(string  $listId): SingleList
	{
		if (! isset($this->lists[$listId])) {
			throw new Exception();
		}
		return $this->lists[$listId];
	}

	/**
	 * Get all lists in collection
	 *
	 * @return SingleList[]
	 */
	public function getLists(): array
	{
		return $this->lists;
	}
}
