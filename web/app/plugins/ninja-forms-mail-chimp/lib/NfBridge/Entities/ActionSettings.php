<?php

namespace NFMailchimp\EmailCRM\NfBridge\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;
use NFMailchimp\EmailCRM\NfBridge\Entities\ActionSetting;
use NFMailchimp\EmailCRM\Shared\Contracts\FormActionFieldCollection;
use NFMailchimp\EmailCRM\NfBridge\Exception;

/**
 * Describes a collection of NF Bridge Action Settings
 */
class ActionSettings extends SimpleEntity implements FormActionFieldCollection
{

	/**
	 * @var ActionSetting[]
	 */
	protected $actionSettings;

	public function __construct()
	{
		$this->actionSettings = [];
	}

	/** @inheritDoc */
	public static function fromArray(array $items): SimpleEntity
	{

		$obj = new static();

		// Entity structure else response structure
		if (isset($items['actionSettings'])) {
			$array = $items['actionSettings'];
		} else {
			$array = $items;
		}

		foreach ($array as $list) {
			if (!is_array($list)) {
				if (is_a($list, ActionSetting::class)) {
					$obj->addActionSetting($list);
					continue;
				} else {
					$list = (array) $list;
				}
			}

			$obj->addActionSetting(ActionSetting::fromArray($list));
		}

		return$obj;
	}

	/**
	 * Add an Action Setting to collection
	 *
	 * @param ActionSetting $actionSetting
	 *
	 * @return ActionSettings
	 */
	public function addActionSetting(ActionSetting $actionSetting): ActionSettings
	{
				 
		$this->actionSettings[$actionSetting->getName()] = $actionSetting;
		return $this;
	}

	/**
	 * Get an Action Setting from collection
	 *
	 * @param string $name
	 *
	 * @return ActionSetting
	 * @throws Exception
	 */
	public function getActionSetting(string $name): ActionSetting
	{
		if (!isset($this->actionSettings[$name])) {
			throw new Exception();
		}
		return $this->actionSettings[$name];
	}

	/**
	 * Get all Action Settings in collection
	 *
	 * @return ActionSetting[]
	 */
	public function getActionSettings(): array
	{
		return $this->actionSettings;
	}

	/**
	 * Return action settings configured for NF Action
	 *
	 * Removes unset/null values to enable default NF settings
	 * @return array
	 */
	public function outputConfiguration(): array
	{

		$array = [];

		foreach ($this->actionSettings as $actionSetting) {
			$array[$actionSetting->getName()] = $actionSetting->outputConfiguration();
		}

		return $array;
	}
}
