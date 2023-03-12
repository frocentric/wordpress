<?php


namespace NFMailchimp\EmailCRM\Mailchimp\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;

/**
 * Describes one merge variable for a Mailchimp list
 */
class MergeVar extends MailChimpEntity
{

	/**
	 * @var string
	 */
	protected $mergeId;

	/**
	 * @var string
	 */
	protected $tag;
	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var array
	 */
	protected $options;

	/**
	 * @var string
	 */
	protected $value;

	/**
	 * @var bool
	 */
	protected $required;

	protected $defaultValue;


	/**
	 * @return mixed
	 */
	public function getDefaultValue()
	{
		return $this->defaultValue;
	}

	/**
	 * @param mixed $defaultValue
	 *
	 * @return MergeVar
	 */
	public function setDefaultValue($defaultValue)
	{
		$this->defaultValue = $defaultValue;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 *
	 * @return MergeVar
	 */
	public function setName(string $name): MergeVar
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return isset($this->type)?$this->type:'';
	}

	/**
	 * @param string $type
	 *
	 * @return MergeVar
	 */
	public function setType(string $type): MergeVar
	{
		$this->type = $type;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getOptions(): array
	{
		return isset($this->options)?$this->options:[];
	}

		/**
		 * Returns size option, default is 0
		 * @return int
		 */
	public function getSize(): int
	{
		return isset($this->options['size'])? (int)$this->options['size']:0;
	}
	/**
	 * @param array $options
	 *
	 * @return MergeVar
	 */
	public function setOptions(array $options): MergeVar
	{
		$this->options = $options;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getValue(): string
	{
		return isset($this->value)?$this->value:'';
	}

	/**
	 * @param string $value
	 *
	 * @return MergeVar
	 */
	public function setValue(string $value): MergeVar
	{
		$this->value = $value;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMergeId(): string
	{
		return isset($this->mergeId)?$this->mergeId:'';
	}

	/**
	 * @param string $mergeId
	 *
	 * @return MergeVar
	 */
	public function setMergeId(string $mergeId): MergeVar
	{
		$this->mergeId = $mergeId;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTag()
	{
		return isset($this->tag)?$this->tag:'';
	}

	/**
	 * @param mixed $tag
	 *
	 * @return MergeVar
	 */
	public function setTag($tag)
	{
		$this->tag = $tag;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function getRequired(): bool
	{
		return isset($this->required)?$this->required:false;
	}

	/**
	 * @param bool $required
	 *
	 * @return MergeVar
	 */
	public function setRequired(bool $required): MergeVar
	{
		$this->required = $required;
		return $this;
	}

	/** @inheritDoc */
	public static function fromArray(array $items): SimpleEntity
	{
		if (isset($items['options'])) {
			$items['options'] = (array)$items['options'];
		}
		$obj = parent::fromArray($items);
		if (isset($items['merge_id'])) {
			$obj->setMergeId($items['merge_id']);
		}
		if (isset($items['mergeId'])) {
			$obj->setMergeId($items['mergeId']);
		}
		if (isset($items['default_value'])) {
			$obj->setDefaultValue($items['default_value']);
		}
		return $obj;
	}
}
