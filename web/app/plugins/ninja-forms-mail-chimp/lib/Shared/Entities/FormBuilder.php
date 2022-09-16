<?php

namespace NFMailchimp\EmailCRM\Shared\Entities;

// Contracts
use NFMailchimp\EmailCRM\Shared\Contracts\FormBuilder as FormBuilderContract;
// Entities
use NFMailchimp\EmailCRM\Shared\Entities\FormField;
use NFMailchimp\EmailCRM\Shared\Entities\FormFields;

/**
 * Shared form entity that can be transported across bridges
 */
class FormBuilder implements FormBuilderContract
{

	/**
	 * Form title
	 * @var string
	 */
	protected $title = '';

	/**
	 *
	 * @var FormFields
	 */
	protected $formFields;

	/**
	 * Construct a new FormBuilder object
	 */
	public function __construct()
	{
		$this->formFields = new FormFields();
	}

	/**
	 * Set form title
	 * @param string $title
	 * @return FormBuilder
	 */
	public function setTitle(string $title): FormBuilderContract
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * Add form field to the form builder
	 * @param FormField $formField
	 * @return FormBuilder
	 */
	public function addFormField(FormField $formField): FormBuilderContract
	{
		$this->formFields->addFormField($formField);
		return $this;
	}

	/**
	 * Get form builder title
	 * @return string
	 */
	public function getTitle(): string
	{

		return isset($this->title) ? $this->title : '';
	}

	/**
	 * Get form fields
	 * @return FormFields
	 */
	public function getFormFields(): FormFields
	{
		return $this->formFields;
	}

	/**
	 * Return FormBuilder entity as associative array
	 * @return array
	 */
	public function toArray(): array
	{
		/** @var FormField $field */
		$array = [];

		$array['title'] = $this->getTitle();

		$collection = $this->getFormFields()->getFields();

		foreach ($collection as $field) {
			$array['formFields'][$field->getId()] = $field->toArray();
		}

		return $array;
	}
}
