<?php

namespace NFMailchimp\EmailCRM\Shared\Entities;

use NFMailchimp\EmailCRM\Shared\Entities\Option;
use NFMailchimp\EmailCRM\Shared\SimpleEntity;

/**
 * Collection of option entities
 *
 */
class Options extends SimpleEntity
{

	/**
	 *
	 * @var Option[]
	 */
	protected $options = [];

	/**
	 * Add Option to the collection
	 * @param Option $option
	 * @return \NFMailchimp\EmailCRM\NfBridge\Entities\options
	 */
	public function addOption(Option $option): options
	{
		$this->options[] = $option;

		return $this;
	}

	/**
	 * Return option collection as array
	 * @return array
	 */
	public function toArray(): array
	{

		$toArray = [];

		foreach ($this->options as $option) {
			$toArray[] = $option->toArray();
		}

		return $toArray;
	}

	/**
	 * Convert array into Options
	 * @param array $items
	 * @return SimpleEntity
	 */
	public static function fromArray(array $items): SimpleEntity
	{
		$obj = new static();
		foreach ($items as $option) {
			if (is_a($option, Option::class)) {
				$obj->addOption($option);
			} elseif (is_array($option)) {
				$obj->addOption(Option::fromArray($option));
			}
		}
		return $obj;
	}
}
