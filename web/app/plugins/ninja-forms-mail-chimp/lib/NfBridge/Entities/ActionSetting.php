<?php

namespace NFMailchimp\EmailCRM\NfBridge\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;
use NFMailchimp\EmailCRM\Shared\Contracts\FormActionField;
use NFMailchimp\EmailCRM\NfBridge\Entities\Options;

/**
 * Single action setting in form design
 *
 * Field map connect NF form submission data to a specific location by passing
 * the form submission value keyed on the FormActionField's programmatic
 * name
 */
class ActionSetting extends SimpleEntity implements FormActionField
{

	/**
	 * Programmatic Name
	 * @var string
	 */
	protected $name;

	/**
	 *
	 * @var string
	 */
	protected $type;

	/**
	 *
	 * @var string
	 */
	protected $label;

	/**
	 *
	 * @var string
	 */
	protected $width;

	/**
	 *
	 * @var string
	 */
	protected $group;

	/**
	 *
	 * @var string
	 */
	protected $value;

	/**
	 *
	 * @var string
	 */
	protected $help;

	/**
	 *
	 * @var Options
	 */
	protected $options;

	/**
	 *
	 * @var string
	 */
	protected $mask;

	/**
	 *
	 * @var array
	 */
	protected $deps;

	/**
	 * useMergeTags action setting
	 *
	 * Uses ninja forms property name style
	 * @var string|array
	 */
	protected $useMergeTags;

	/**
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 *
	 * @var array
	 */
	protected $columns;

	/**
	 * @var string
	 */
	protected $templateRow;

	/**
	 * Constructs object from given array
	 * @param array $items
	 * @return SimpleEntity
	 */
	public static function fromArray(array $items): SimpleEntity
	{
		$obj = new static();
		foreach ($items as $property => $value) {
			$obj = $obj->__set($property, $value);
		}
		return $obj;
	}

	/**
	 * Return action settings name
	 * @return string
	 */
	public function getName(): string
	{
		return isset($this->name) ? (string) $this->name : '';
	}

	/**
	 * Return action settings type
	 * @return string
	 */
	public function getType(): string
	{
		return isset($this->type) ? (string) $this->type : '';
	}

	/**
	 * Return action settings label
	 * @return string
	 */
	public function getLabel(): string
	{
		return isset($this->label) ? (string) $this->label : '';
	}

	/**
	 * Return action settings width
	 * @return string
	 */
	public function getWidth(): string
	{
		return isset($this->width) ? (string) $this->width : '';
	}

	/**
	 * Return action settings group
	 * @return string
	 */
	public function getGroup(): string
	{
		return isset($this->group) ? (string) $this->group : '';
	}

	/**
	 * Return action settings value
	 * @return string
	 */
	public function getValue(): string
	{
		return isset($this->value) ? (string) $this->value : '';
	}

	/**
	 * Return action settings help
	 * @return string
	 */
	public function getHelp(): string
	{
		return isset($this->help) ? (string) $this->help : '';
	}

	/**
	 * Return action settings options
	 * @return Options
	 */
	public function getOptions(): Options
	{
		return isset($this->options) ?  $this->options : new Options();
	}

	/**
	 * Return action settings mask
	 * @return string
	 */
	public function getMask(): string
	{
		return isset($this->mask) ? (string) $this->mask : '';
	}

	/**
	 * Return action settings dependencies
	 * @return array
	 */
	public function getDeps(): array
	{
		return isset($this->deps) ? (array) $this->deps : array();
	}

	/**
	 * Return action settings useMergeTags
	 * @return string|array
	 */
	public function getUseMergeTags()
	{
		return isset($this->useMergeTags) ? $this->useMergeTags : '';
	}

	/**
	 * Return action settings settings
	 * @return array
	 */
	public function getSettings(): array
	{
		return isset($this->settings) ? (array) $this->settings : array();
	}

	/**
	 * Return action settings columns
	 * @return array
	 */
	public function getColumns(): array
	{
		return isset($this->columns) ? (array) $this->columns : array();
	}

	/**
	 * Return action settings template row
	 * @return string
	 */
	public function getTmplRow(): string
	{
		return isset($this->templateRow) ? (string) $this->templateRow : array();
	}

	/**
	 * Set action settings name
	 * @param string $stringValue
	 * @return ActionSetting
	 */
	public function setName(string $stringValue): ActionSetting
	{
		$this->name = $stringValue;

		return $this;
	}

	/**
	 * Set action settings type
	 * @param string $stringValue
	 * @return ActionSetting
	 */
	public function setType(string $stringValue): ActionSetting
	{
		$this->type = $stringValue;

		return $this;
	}

	/**
	 * Set action settings label
	 * @param string $stringValue
	 * @return ActionSetting
	 */
	public function setLabel(string $stringValue): ActionSetting
	{
		$this->label = $stringValue;

		return $this;
	}

	/**
	 * Set action settings width
	 * @param string $stringValue
	 * @return ActionSetting
	 */
	public function setWidth(string $stringValue): ActionSetting
	{
		$this->width = $stringValue;

		return $this;
	}

	/**
	 * Set action settings group
	 * @param string $stringValue
	 * @return ActionSetting
	 */
	public function setGroup(string $stringValue): ActionSetting
	{
		$this->group = $stringValue;

		return $this;
	}

	/**
	 * Set action settings value
	 * @param string $stringValue
	 * @return ActionSetting
	 */
	public function setValue(string $stringValue): ActionSetting
	{
		$this->value = $stringValue;

		return $this;
	}

	/**
	 * Set action settings help
	 * @param string $stringValue
	 * @return ActionSetting
	 */
	public function setHelp(string $stringValue): ActionSetting
	{
		$this->help = $stringValue;

		return $this;
	}

	/**
	 * Set action settings options
	 * @param Options $options
	 * @return ActionSetting
	 */
	public function setOptions(Options $options): ActionSetting
	{
		$this->options = $options;

		return $this;
	}

	/**
	 * Set action settings mask
	 * @param string $stringValue
	 * @return ActionSetting
	 */
	public function setMask(string $stringValue): ActionSetting
	{
		$this->mask = $stringValue;

		return $this;
	}

	/**
	 * Set action settings dependencies
	 * @param array $arrayValue
	 * @return ActionSetting
	 */
	public function setDeps(array $arrayValue): ActionSetting
	{
		$this->deps = $arrayValue;

		return $this;
	}

	/**
	 * Set action settings dependencies
	 * @param string|array $Value
	 * @return ActionSetting
	 */
	public function setUseMergeTags(array $Value): ActionSetting
	{
		$this->useMergeTags = $Value;

		return $this;
	}

	/**
	 * Set action settings settings
	 * @param array $arrayValue
	 * @return ActionSetting
	 */
	public function setSettings(array $arrayValue): ActionSetting
	{
		$this->settings = $arrayValue;

		return $this;
	}

	/**
	 * Set action settings columns
	 * @param array $arrayValue
	 * @return ActionSetting
	 */
	public function setColumns(array $arrayValue): ActionSetting
	{
		$this->columns = $arrayValue;

		return $this;
	}

	/**
	 * Return action setting configured for NF Action
	 *
	 * Removes unset/null values to enable default NF settings
	 * @return array
	 */
	public function outputConfiguration(): array
	{
		$array = [];

		$props = get_object_vars($this);

		foreach ($props as $prop => $value) {
			if (is_null($value)) {
				continue;
			}

			if ('options'===$prop) {
				$value = $value->toArray();
			}
						
			$array[$prop] = $value;
		}

		return $array;
	}
}
