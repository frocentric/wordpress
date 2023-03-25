<?php

namespace NFMailchimp\EmailCRM\Shared\Contracts;

use NFMailchimp\EmailCRM\Shared\Entities\FormField;
use NFMailchimp\EmailCRM\Shared\Entities\FormFields;

/**
 * Contract for building forms
 */
interface FormBuilder
{

	/**
	 * Set the form title
	 * @param string $title Form Title
	 * @return \NFMailchimp\EmailCRM\Shared\Contracts\FormBuilder
	 */
	public function setTitle(string $title): FormBuilder;

	/**
	 * Add form field to the form
	 * @param FormField $formField
	 * @return \NFMailchimp\EmailCRM\Shared\Contracts\FormBuilder
	 */
	public function addFormField(FormField $formField): FormBuilder;

	/**
	 * Get form builder title
	 * @return string
	 */
	public function getTitle(): string;

	/**
	 * Get form fields
	 * @return FormFields
	 */
	public function getFormFields(): FormFields;

	/**
	 * Return FormBuilder entity as associative array
	 * @return array
	 */
	public function toArray(): array;
}
