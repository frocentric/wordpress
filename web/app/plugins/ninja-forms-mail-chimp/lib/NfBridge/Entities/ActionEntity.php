<?php

namespace NFMailchimp\EmailCRM\NfBridge\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;
use NFMailchimp\EmailCRM\NfBridge\Entities\ActionSettings;
use NFMailchimp\EmailCRM\NfBridge\Entities\ApiSettings;
use NFMailchimp\EmailCRM\Shared\Contracts\FormAction;

/**
 * Entity to construct NF action
 *
 */
class ActionEntity extends SimpleEntity implements FormAction
{

	/**
	 * @var string
	 */
	protected $name = ''; // Add to API

	/**
	 *
	 * @var string
	 */
	protected $nicename = '';

	/**
	 * @var array
	 */
	protected $tags = array();

	/**
	 * @var string
	 */
	protected $timing = 'normal';

	/**
	 * @var int
	 */
	protected $priority = 10;

	/**
	 *
	 * @var ActionSettings
	 */
	protected $actionSettings;

	/**
	 *
	 * @var ApiSettings
	 */
	protected $apiSettings;

	/**
	 * Constructs object from given array
	 * @param array $items
	 * @return SimpleEntity
	 */
	public static function fromArray(array $items): SimpleEntity
	{
		$obj = new static();
		foreach ($items as $property => $value) {
			if ('actionSettings' === $property) {
				if (is_array($value)) {
					$actionSettingsEntity = ActionSettings::fromArray($value);
					$obj->setActionSettings($actionSettingsEntity);
				} else {
					if (is_a($value, ActionSettings::class)) {
						$obj->setActionSettings($value);
					}
				}

				continue;
			}
			if ('apiSettings' === $property) {
				if (is_array($value)) {
					$apiSettingsEntity = ApiSettings::fromArray($value);
					$obj->setApiSettings($apiSettingsEntity);
				} else {
					if (is_a($value, ApiSettings::class)) {
						$obj->setApiSettings($value);
					}
				}

				continue;
			}

			$obj = $obj->__set($property, $value);
		}

		return $obj;
	}

	/**
	 * Return action name
	 * @return string
	 */
	public function getName(): string
	{
		return isset($this->name) ? (string) $this->name : '';
	}

	/**
	 * Return action nicename
	 * @return string
	 */
	public function getNicename(): string
	{
		return isset($this->nicename) ? (string) $this->nicename : '';
	}

	/**
	 * Return action tags
	 * @return string
	 */
	public function getTags(): array
	{
		return isset($this->tags) ? (array) $this->tags : array();
	}

	/**
	 * Return action timing
	 * @return string
	 */
	public function getTiming(): string
	{
		return isset($this->timing) ? (string) $this->timing : '';
	}

	/**
	 * Return action priority
	 * @return int
	 */
	public function getPriority(): int
	{
		return isset($this->priority) ? (int) $this->priority : 10;
	}

	/**
	 * Return action settings
	 * @return ActionSettings
	 */
	public function getActionSettings(): ActionSettings
	{
		return isset($this->actionSettings) ? $this->actionSettings : new ActionSettings();
	}

	/**
	 * Return API settings
	 * @return ApiSettings
	 */
	public function getApiSettings(): ApiSettings
	{
		return isset($this->apiSettings) ? $this->apiSettings : new ApiSettings();
	}

	/**
	 * Set action name
	 * @param string $stringValue
	 * @return ActionEntity
	 */
	public function setName(string $stringValue): ActionEntity
	{
		$this->name = $stringValue;

		return $this;
	}

	/**
	 * Set action nice name
	 * @param string $stringValue
	 * @return ActionEntity
	 */
	public function setNicename(string $stringValue): ActionEntity
	{
		$this->nicename = $stringValue;

		return $this;
	}

	/**
	 * Set action type
	 * @param array $arrayValue
	 * @return ActionEntity
	 */
	public function setTags(array $arrayValue): ActionEntity
	{
		$this->tags = $arrayValue;

		return $this;
	}

	/**
	 * Set action timing
	 * @param string $stringValue
	 * @return ActionEntity
	 */
	public function setTiming(string $stringValue): ActionEntity
	{
		$this->timing = $stringValue;

		return $this;
	}

	/**
	 * Set action priority
	 * @param int $intValue
	 * @return ActionEntity
	 */
	public function setPriority(int $intValue): ActionEntity
	{
		$this->priority = $intValue;

		return $this;
	}

	/**
	 * Set action settings
	 * @param array $keyedSettingsArray
	 * @return ActionEntity
	 */
	public function setActionSettings(ActionSettings $keyedSettingsArray): ActionEntity
	{
		$this->actionSettings = $keyedSettingsArray;

		return $this;
	}

	/**
	 * Set API settings settings
	 * @param array $keyedSettingsArray
	 * @return ActionEntity
	 */
	public function setApiSettings(ApiSettings $keyedSettingsArray): ActionEntity
	{
		$this->apiSettings = $keyedSettingsArray;

		return $this;
	}
}
