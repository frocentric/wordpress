<?php

namespace NFMailchimp\EmailCRM\NfBridge\Actions;

// NF Bridge
use NFMailchimp\EmailCRM\NfBridge\Entities\ActionEntity;

// Shared
use NFMailchimp\EmailCRM\Shared\Entities\FormFields;

/**
 * Autogenerates a Ninja Form
 *
 */
class AutogenerateForm
{

	/**
	 * Integrating Plugin's action as defined by an ActionEntity
	 *
	 * @var ActionEntity
	 */
	protected $actionEntity;

	/**
	 * Standard ApiFormFields
	 *
	 * Standard across all implementations of the Api Module
	 *
	 * @var FormFields
	 */
	protected $standardApiFormFields;

	/**
	 * Custom ApiFormFields
	 *
	 * Specific to a given installation
	 *
	 * @var FormFields
	 */
	protected $customApiFormFields;

	/**
	 * Consolidated standard and custom API FormFields
	 *
	 * ApiModule provides the standard fields.  The integrating plugin provides
	 * the custom fields.  Both are in the shared FormFields structure for
	 * standardized handling
	 *
	 * @var FormFields
	 */
	protected $apiFormFields;

	/**
	 * Ninja Form object
	 *
	 * Instance of the Ninja Form that is newly created here
	 *
	 * @var NF_Abstracts_ModelFactory
	 */
	protected $nfForm;

	/**
	 * Form Id of newly create Ninja Form
	 *
	 * @var int
	 */
	protected $formId = 0;

	/**
	 * Collection of settings for the new action
	 *
	 * Field map values are added in sync with the form fields; after iterating
	 * the fields, the addAction method sets non-field-specific values and
	 * saves the new action
	 * @var array
	 */
	protected $actionFieldMap = [];

	/**
	 * Title of the Ninja Form
	 *
	 * @var string
	 */
	protected $formTitle = '';

	/**
	 * Field order for display in the Ninja Form
	 *
	 * @var int
	 */
	protected $order = 0;

	/**
	 * Construct AutogenerateForm
	 *
	 * @param array $actionEntity
	 * @param FormFields $standardApiFormFields
	 * @param FormFields $customApiFormFields
	 */
	public function __construct(ActionEntity $actionEntity, FormFields $standardApiFormFields, FormFields $customApiFormFields)
	{

		$this->actionEntity = $actionEntity;
		$this->standardApiFormFields = $standardApiFormFields;
		$this->customApiFormFields = $customApiFormFields;

		// Set a default value
		// Probably fully redundant and handle() requires a title,
		// but should something change, we ensure it always has one
		$this->formTitle = $this->actionEntity->getNicename();
	}

	/**
	 * Construct  a Ninja Form per set FormFields, Action Configuration, and title
	 * @param string $listId
	 * @param string $formTitle
	 */
	public function handle(string $formTitle)
	{

		$this->formTitle = $formTitle;

		$this->constructApiFields();

		$this->constructNinjaFormWithAction();
	}

	/**
	 * Combine standard- and custom- ApiFormFields into a single collection
	 */
	protected function constructApiFields()
	{

		$combined = array_merge($this->standardApiFormFields->toArray(), $this->customApiFormFields->toArray());

		$this->apiFormFields = FormFields::fromArray($combined);
	}

	/**
	 * Construct a new NF form per FormField and ActionConfiguration
	 *
	 * @return void
	 */
	public function constructNinjaFormWithAction(): void
	{
		$this->initializeForm();

		$this->setNfFormTitle();

		$this->addFormFields();

		$this->addSubmitButton();

		$this->addCreateEntryAction();

		$this->addSaveAction();

		$this->addSuccessMessageAction();

		$this->nfForm->save();
	}

	/**
	 * Initialize a new form
	 */
	protected function initializeForm()
	{
		$this->nfForm = Ninja_Forms()->form();
		$this->nfForm->save();
		$this->formId = $this->nfForm->get_id();
	}

	/**
	 * Set the NF form title
	 */
	protected function setNfFormTitle()
	{

		$this->nfForm->get()->update_settings(
			[
					'title' => $this->formTitle
				]
		);
	}

	/**
	 * Add form fields
	 */
	protected function addFormFields()
	{

		foreach ($this->apiFormFields->getFields() as $key => $field) {
			$nfField = $this->nfForm->field()->get();
			// increment order
			$this->order++;

			$settings = array(
				'type' => $field->getType(),
				'label' => $field->getLabel(),
				'label_post' => 'inside',
				'required' => $field->getRequired(),
				'key' => $key,
				'order' => $this->order
			);

			if (!empty($field->getOptions()->toArray())) {
				$settings['options'] = $field->getOptions()->toArray();
			}

			if (0 !== $field->getCharacterLimit()) {
				$settings['input_limit'] = $field->getCharacterLimit();
			}

			$nfField->update_settings($settings)->save();

			$this->addActionFieldMap($key);
		}
	}

	/**
	 * Add submit button to the form
	 * @return void
	 */
	protected function addSubmitButton(): void
	{
		$nfField = $this->nfForm->field()->get();

		$settings = array(
			'type' => 'submit',
			'label' => 'Submit',
			'label_post' => 'inside',
			'key' => 'submit'
		);
		$nfField->update_settings($settings)->save();
	}

	/**
	 * Add action field map for given field key
	 * @param string $key
	 * @return void
	 */
	protected function addActionFieldMap(string $key): void
	{

		$actionMetaKey = $key;
		$actionMetaValue = '{field:' . $key . '}';

		$this->actionFieldMap[$actionMetaKey] = $actionMetaValue;
	}

	/**
	 * Add the action to the form
	 */
	protected function addCreateEntryAction()
	{
		$action = $this->nfForm->action()->get();

		$this->actionFieldMap['type'] = $this->actionEntity->getName();
		$this->actionFieldMap['label'] = $this->actionEntity->getNicename();
		$this->actionFieldMap['active'] = '1';

		$action->update_settings($this->actionFieldMap);

		$action->save();
	}

	/**
	 * Add Store Submission (programmatic name: save) to the form
	 *
	 */
	protected function addSaveAction()
	{
		$action = $this->nfForm->action()->get();

		$actionMeta = [
			'type' => 'save',
			'label' => 'Store Submission',
			'active' => '1'
		];


		$action->update_settings($actionMeta);

		$action->save();
	}

	/**
	 * Add Store Submission (programmatic name: save) to the form
	 *
	 */
	protected function addSuccessMessageAction()
	{
		$action = $this->nfForm->action()->get();

		$actionMeta = [
			'type' => 'successmessage',
			'label' => 'Success Message',
			'success_msg' => '<p>Your form has been successfully submitted.</p><p><br></p>',
			'active' => '1'
		];


		$action->update_settings($actionMeta);

		$action->save();
	}

	/**
	 * Get the newly created form Id
	 * @return int
	 */
	public function getFormId(): int
	{
		return $this->formId;
	}

	/**
	 * Get the constructed Ninja Form
	 * @return NF_Abstracts_ModelFactory
	 */
	public function getNinjaForm(): NF_Abstracts_ModelFactory
	{
		return $this->nfForm;
	}
}
