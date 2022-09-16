<?php

namespace NFMailchimp\EmailCRM\Shared\Entities;

// Entities
use NFMailchimp\EmailCRM\Shared\Entities\FormField;
use NFMailchimp\EmailCRM\Shared\SimpleEntity;

/**
 * Collection of form field entities shared by CF and NF
 *
 */
class FormFields extends SimpleEntity
{

	/**
	 * Collection of FormField
	 * @var FormField[]
	 */
	protected $formFields=[];

	/**
	 * Add a form field the fields collection
	 * @param FormField $field
	 * @return \NFMailchimp\EmailCRM\Shared\Entities\FormFields
	 */
	public function addFormField(FormField $field): FormFields
	{
		$this->formFields[$field->getId()] = $field;
		return $this;
	}

	/**
	 * Get form field specified by Id
	 * @param string $id Id of field
	 * @return FormField
	 * @throws Exception
	 */
	public function getField(string $id): FormField
	{

		if (!isset($this->formFields[$id])) {
			throw new \Exception();
		}

		return $this->formFields[$id];
	}

	/**
	 * Return collection of all form fields
	 * @return array
	 */
	public function getFields(): array
	{
		return $this->formFields;
	}

	/**
	 * Return array of all form fields
	 * @return array
	 */
	public function toArray(): array
	{

		$array = [];
		foreach ($this->formFields as $property => $field) {
			$array[$property] = $field->toArray();
		}
		return $array;
	}

	/**
	 * Return simple entity constructed from array of FormFields
	 * @param array $items
	 * @return SimpleEntity
	 */
	public static function fromArray(array $items): SimpleEntity
	{
		$obj = new static();

		// Entity structure else response structure
		if (isset($items['formFields'])) {
			$array = $items['formFields'];
		} else {
			$array = $items;
		}
		foreach ($array as $item) {
			if (!is_array($item)) {
				if (is_a($item, FormField::class)) {
					$obj->addFormField($item);
					continue;
				} else {
					$item = (array) $item;
				}
			}

			$entity = FormField::fromArray($item);

			$obj->addFormField($entity);
		}

		return $obj;
	}
}
