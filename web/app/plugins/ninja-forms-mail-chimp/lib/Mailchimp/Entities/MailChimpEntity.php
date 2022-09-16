<?php


namespace NFMailchimp\EmailCRM\Mailchimp\Entities;

use calderawp\calderaforms\pro\settings\form;
use NFMailchimp\EmailCRM\Shared\SimpleEntity;

/**
 * Class MailChimpEntity
 *
 * Base class for describing Mailchimp entities
 */
abstract class MailChimpEntity extends SimpleEntity
{
	/**
	 * @var string
	 */
	protected $listId;

	/**
	 * @return string
	 */
	public function getListId(): string
	{
		return $this->listId;
	}

	/**
	 * @param string $listId
	 *
	 * @return MailChimpEntity
	 */
	public function setListId(string $listId): MailChimpEntity
	{
		$this->listId = $listId;
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public static function fromArray(array $items) : SimpleEntity
	{
		$obj = new static();
		foreach ($items as $property => $value) {
			if (null !== $value) {
				$obj = $obj->__set($property, $value);
			}
		}
		if (isset($items[ 'list_id' ])) {
			$obj->setListId($items[ 'list_id' ]);
		}
		return $obj;
	}

	/** @inheritDoc */
	public function toArray(): array
	{
		$items =  parent::toArray();
		$items = $this->recursiveArrayCast($items);
		return  $items;
	}

	/**
	 * @param array $items
	 * @return array
	 */
	protected function recursiveArrayCast(array $items): array
	{
		foreach ($items as $key => $item) {
			if (is_object($item) && method_exists($item, 'toArray')) {
				$items[$key] = $item->toArray();
			} elseif (is_array($item)) {
				$items[$key] = $this->recursiveArrayCast($item);
			}
		}
		return $items;
	}
}
