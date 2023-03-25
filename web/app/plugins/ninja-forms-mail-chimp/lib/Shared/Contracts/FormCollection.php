<?php


namespace NFMailchimp\EmailCRM\Shared\Contracts;

/**
 * Interface FormCollection
 *
 * A type that collects many forms
 */
interface FormCollection
{
	/**
	 * @param FormContract $form
	 * @return FormCollection
	 */
	public function addForm(FormContract $form): FormCollection;

	/**
	 * Get a form from this collection
	 *
	 * @param string $formId
	 * @return Form
	 * @throws \Exception
	 */
	public function getForm(string $formId): FormContract;
	/**
	 * Does collection have a form of this ID?
	 *
	 * @param string $formId
	 * @return bool
	 */
	public function hasForm(string $formId): bool;

	/**
	 * Remove a form from the collection
	 *
	 * @param string $formId
	 * @return FormCollection
	 * @throws Exception
	 */
	public function removeForm(string $formId): FormCollection;
}
