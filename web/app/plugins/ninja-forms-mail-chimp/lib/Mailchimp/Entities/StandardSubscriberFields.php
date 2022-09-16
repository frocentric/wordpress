<?php

namespace NFMailchimp\EmailCRM\Mailchimp\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;
use NFMailchimp\EmailCRM\Mailchimp\Entities\StandardSubscriberField;

/**
 * Standard fields used in all Subscribers
 *
 */
class StandardSubscriberFields extends SimpleEntity
{

	/**
	 * @var StandardSubscriberField[]
	 */
	protected $standardSubscriberFields;

	public function __construct()
	{
		$this->standardSubscriberFields = [];
	}

	public static function fromArray(array $items): SimpleEntity
	{
		$obj = new static();
		// Entity structure else response structure
		if (isset($items['standardSubscriberFields'])) {
			$array = $items['standardSubscriberFields'];
		} else {
			$array = $items;
		}

		foreach ($array as $list) {
			if (!is_array($list)) {
				if (is_a($list, StandardSubscriberField::class)) {
					$obj->addStandardSubscriberField($list);
					continue;
				} else {
					$list = (array) $list;
				}
			}

			$obj->addStandardSubscriberField(StandardSubscriberField::fromArray($list));
		}

		return $obj;
	}

	/**
	 * Add a StandardSubscriberField to collection
	 *
	 * @param StandardSubscriberField $field
	 *
	 * @return StandardSubscriberFields
	 */
	public function addStandardSubscriberField(StandardSubscriberField $field): StandardSubscriberFields
	{
		$this->standardSubscriberFields[$field->getName()] = $field;
		return $this;
	}

	/**
	 * Get a StandardSubscriberField from collection
	 *
	 * @param string $name
	 *
	 * @return StandardSubscriberField
	 * @throws Exception
	 */
	public function getStandardSubscriberField(string $name): StandardSubscriberField
	{
		if (!isset($this->standardSubscriberFields[$name])) {
			throw new Exception();
		}
		return $this->standardSubscriberFields[$name];
	}

	/**
	 * Get all StandardSubscriberFields in collection
	 *
	 * @return StandardSubscriberFields[]
	 */
	public function getStandardSubscriberFields(): array
	{
		return $this->standardSubscriberFields;
	}
}
