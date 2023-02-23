<?php

namespace NFMailchimp\EmailCRM\Shared\Entities;

// Entities
use NFMailchimp\EmailCRM\Shared\SimpleEntity;
use NFMailchimp\EmailCRM\Shared\Entities\Options;

/**
 * Form field entity shared by CF and NF
 *
 */
class FormField extends SimpleEntity
{

	/**
	 * Unique identifier for field
	 * `key` in NF
	 * Required argument
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Provides text to be used in the <label> element
	 *
	 * Required argument -- can be empty string for hidden fields.
	 *
	 * @var string
	 */
	protected $label;

	/**
	 * What type of field.
	 *
	 * Optional argument.
	 * Default is simple which is an <input>
	 * CF Options: simple|checkbox|advanced|dropdown
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Default value for the field
	 *
	 * Optional argument
	 * Default value is null
	 *
	 * @var mixed
	 */
	protected $default;

	/**
	 * Makes field required or not.
	 *
	 * Optional argument
	 * Default is not required.
	 *
	 * @var bool
	 */
	protected $required;

	/**
	 * The description of the field.
	 *
	 * If used the field HTML __should__ have an `aria-describedby` attribute referring to the element displaying this description.
	 *
	 * Optional argument
	 *
	 * @var string
	 */
	protected $description;

	/**
	 *
	 * @var Options
	 */
	protected $options;

		/**
		 * Character limit
		 * @var int
		 */
		protected $characterLimit;
		
	/**
	 * Set the field Id
	 * @param string $id Field Id
	 * @return SimpleEntity
	 */
	public function setId(string $id): SimpleEntity
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * Set the field label
	 * @param string $label Field label
	 * @return SimpleEntity
	 */
	public function setLabel(string $label): SimpleEntity
	{

		$this->label = $label;
		return $this;
	}

	/**
	 * Set the field type
	 * @param string $type Field type
	 * @return SimpleEntity
	 */
	public function setType(string $type): SimpleEntity
	{
		$this->type = $type;
		return $this;
	}

	/**
	 * Set the default value
	 * @param type $default Default value
	 * @return SimpleEntity
	 */
	public function setDefault($default): SimpleEntity
	{
		$this->default = $default;
		return $this;
	}

	/**
	 * Set Required parameter
	 * @param bool $required Boolean of `is required?`
	 * @return SimpleEntity
	 */
	public function setRequired(?bool $required): SimpleEntity
	{
		$this->required = $required;
		return $this;
	}

	/**
	 * Set the field description
	 * @param string $description Field description
	 * @return SimpleEntity
	 */
	public function setDescription(?string $description): SimpleEntity
	{
		$this->description = $description;
		return $this;
	}

		/**
		 * Set the character limit for the field
		 * @param int $limit
		 * @return SimpleEntity
		 */
	public function setCharacterLimit(?int $limit): SimpleEntity
	{
		$this->characterLimit = $limit;
		return $this;
	}
		
	/**
	 * Set field options
	 * @param Options $options
	 * @return SimpleEntity
	 */
	public function setOptions(?Options $options): SimpleEntity
	{
		$this->options = $options;
		return $this;
	}

	/**
	 * Get field id
	 * @return string
	 */
	public function getId():string
	{
		return isset($this->id) ? $this->id : '';
	}

	/**
	 * Get field label
	 * @return string
	 */
	public function getLabel():string
	{
		return isset($this->label) ? $this->label : '';
	}

	/**
	 * Get field type
	 * @return string
	 */
	public function getType():string
	{
		return isset($this->type) ? $this->type : '';
	}

	/**
	 * Get field default value
	 * @return mixed
	 */
	public function getDefault():string
	{
		return isset($this->default) ? $this->default : '';
	}

	/**
	 * Get field required boolean
	 * @return bool
	 */
	public function getRequired():bool
	{
		return isset($this->required) ? $this->required : false;
	}

	/**
	 * Get field description
	 * @return string
	 */
	public function getDescription():string
	{
		return isset($this->description) ? $this->description : '';
	}

	/**
	 * Get options
	 * @return Options
	 */
	public function getOptions(): Options
	{
		return isset($this->options) ? $this->options : new Options();
	}

		/**
		 * Get character limit for the field; 0 indicates no limit
		 *
		 * @return int
		 */
	public function getCharacterLimit():int
	{
		return isset($this->characterLimit)? $this->characterLimit:0;
	}
		/** @inheritdoc */
	public static function fromArray(array $items): SimpleEntity
	{
		$obj = new static();
		foreach ($items as $property => $value) {
			if ('options'===$property && is_array($value)) {
				$options = Options::fromArray($value);
				
				$obj->setOptions($options);
				continue;
			}
			
			$obj = $obj->__set($property, $value);
		}
		return $obj;
	}
}
