<?php

namespace NFMailchimp\EmailCRM\NfBridge\Entities;

use NFMailchimp\EmailCRM\NfBridge\Exception;
use NFMailchimp\EmailCRM\Shared\ArrayLike;
use NFMailchimp\EmailCRM\Shared\Contracts\FormCollection;
use NFMailchimp\EmailCRM\Shared\Contracts\FormContract;

/**
 * Collection of NF forms objects
 *
 */
class Forms extends ArrayLike implements FormCollection
{
	/**
	 * @var Form[]
	 */
	protected $forms;

	/**
	 * Add a form to this collection
	 *
	 * @param FormContract $form
	 * @return FormCollection
	 */
	public function addForm(FormContract $form): FormCollection
	{
		$this->forms[$form->getId()] = $form;
		return $this;
	}

	/**
	 * Get a form from this collection
	 *
	 * @param string $formId
	 * @return Form
	 * @throws Exception
	 */
	public function getForm(string $formId): FormContract
	{
		if (!$this->hasForm($formId)) {
			throw new Exception("No form with ID $formId found");
		}
		return $this->forms[$formId];
	}

	/**
	 * Does collection have a form of this ID?
	 *
	 * @param string $formId
	 * @return bool
	 */
	public function hasForm(string $formId): bool
	{
		return array_key_exists($formId, $this->forms);
	}

	/**
	 * Remove a form from the collection
	 *
	 * @param string $formId
	 * @return FormCollection
	 * @throws Exception
	 */
	public function removeForm(string $formId): FormCollection
	{
		if (!$this->hasForm($formId)) {
			throw new Exception("No form with ID $formId found");
		}
		unset($this->forms[$formId]);
		return $this;
	}
}
