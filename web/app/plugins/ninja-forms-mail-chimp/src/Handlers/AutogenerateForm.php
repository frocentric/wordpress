<?php

namespace NFMailchimp\NinjaForms\Mailchimp\Handlers;

use NFMailchimp\NinjaForms\Mailchimp\NinjaFormsMailchimp;
use NFMailchimp\EmailCRM\Mailchimp\Interfaces\MailchimpApi;

use NFMailchimp\EmailCRM\Mailchimp\Entities\AudienceDefinition;
use NFMailchimp\EmailCRM\Mailchimp\Handlers\CreateFormBuilder;


use NFMailchimp\EmailCRM\Mailchimp\ApiRequests\GetAudienceDefinitionData;

use NFMailchimp\EmailCRM\Shared\Entities\FormBuilder;
// Ninja Forms
use NF_Abstracts_ModelFactory;
use NFMailchimp\EmailCRM\Mailchimp\Entities\SingleList;

/**
 * Autogenerates a Ninja Form with Subscribe to Mailchimp action
 */
class AutogenerateForm
{

	/**
	 * Top level instance of integrating plugin
	 * @var NinjaFormsMailchimp
	 */
	protected $nfmailchimp;

	/** @var MailchimpApi */
	protected $mailchimpApi;
	/**
	 * Mailchimp Audience Definition entity
	 * @var AudienceDefinition
	 */
	protected $audienceDefinition;

	/**
	 * Entity that contains the data used in autogenerating a form
	 * @var FormBuilder
	 */
	protected $formBuilder;

	/**
	 * Ninja Form
	 * @var NF_Abstracts_ModelFactory
	 */
	protected $nfForm;

	/**
	 * Form Id of newly create Ninja Form
	 * @var int
	 */
	protected $formId=0;
	
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
	 * List Id for the audience definition
	 *
	 * Set whenever audience definition is set
	 * @var string
	 */
	protected $listId = '';

	/**
	 * Title of the Ninja Form
	 * @var string
	 */
	protected $formTitle;
	
	/**
	 * Array of interest categories Ids
	 * Set whenever audience definition is set
	 * @var array
	 */
	protected $interestCategories = [];

	/**
	 * Field order for display in the Ninja Form
	 * @var int
	 */
	protected $order = 0;

		/**
		 * Construct AutogenerateForm with NFMailchimp instance
		 * @param NinjaFormsMailchimp $nfmailchimp
		 */
	public function __construct(MailchimpApi $mailchimpApi)
	{
		$this->mailchimpApi = $mailchimpApi;
	}

		/**
		 * Autogenerate a Ninja Form given a listId and Form Title
		 * @param string $listId
		 * @param string $formTitle
		 */
	public function handle(string $listId, string $formTitle)
	{
		$this->setListId($listId);
		$this->setFormTitle($formTitle);
				$audienceDefinition = $this->getAudienceDefinition();
				$this->setAudienceDefinition($audienceDefinition);
			   
		$formBuilder = $this->constructFormBuilder();
				$this->setFormBuilder($formBuilder);
				
				$this->constructNinjaFormWithAction();
	}

		/**
		 * Set the listId
		 * @param string $listId
		 * @return \NFMailchimp\NinjaForms\Mailchimp\Handlers\AutogenerateForm
		 */
	public function setListId(string $listId): AutogenerateForm
	{
		$this->listId = $listId;
		return $this;
	}
		/**
		 * Set the form title within the class (different than NF form title)
		 * @param string $formTitle
		 * @return \NFMailchimp\NinjaForms\Mailchimp\Handlers\AutogenerateForm
		 */
	public function setFormTitle(string $formTitle): AutogenerateForm
	{
		$this->formTitle = $formTitle;
		return $this;
	}
		/**
		 * Set Audience Definition
		 *
		 * @param AudienceDefinition $audienceDefinition
		 * @return \NFMailchimp\NinjaForms\Mailchimp\Handlers\AutogenerateForm
		 */
	public function setAudienceDefinition(AudienceDefinition $audienceDefinition): AutogenerateForm
	{
		$this->audienceDefinition = $audienceDefinition;
			
		// interest categories are part of audience definition; extract at same time
		$this->interestCategories = array_keys($this->audienceDefinition->interestCategories->getInterestCategories());
			
		return $this;
	}
		/**
		 * Set form builder
		 * @param FormBuilder $formBuilder
		 * @return \NFMailchimp\NinjaForms\Mailchimp\Handlers\AutogenerateForm
		 */
	public function setFormBuilder(FormBuilder $formBuilder):AutogenerateForm
	{
		$this->formBuilder = $formBuilder;
		return $this;
	}
		
		/**
		 * Construct a Ninja Form from the previously set FormBuilder entity
		 * @return void
		 */
	public function constructNinjaFormWithAction():void
	{
			$this->initializeForm();

		$this->setNfFormTitle();
		$this->addEmailField();
		$this->addFormFields();
		$this->addSubmitButton();
		$this->addMailchimpAction();
				$this->addSaveAction();
				$this->addSuccessMessageAction();
		$this->nfForm->save();
	}
		
	/**
	 * Construct the form builder entity
	 */
	protected function constructFormBuilder()
	{
		$createFormBuilder = new CreateFormBuilder($this->audienceDefinition, new FormBuilder());
		$formBuilder = $createFormBuilder->getFormBuilder();
		$formBuilder->setTitle($this->formTitle);
				return $formBuilder;
	}

	/**
	 * Initialize a new form
	 */
	protected function initializeForm()
	{
		$this->nfForm = Ninja_Forms()->form();
		$this->nfForm->save();
		$this->formId=$this->nfForm->get_id();
	}

	/**
	 * Set the NF form title
	 */
	protected function setNfFormTitle()
	{
		
		$this->nfForm->get()->update_settings(
			[
							'title' => $this->formBuilder->getTitle()
			]
		);
	}

	/**
	 * Add email field
	 */
	protected function addEmailField()
	{

		$nfField = $this->nfForm->field()->get();
		// increment order
		$this->order++;
		$type = $this->selectNFFieldType('email');
		$key = 'email_address';
		$settings = array(
			'type' => $type,
			'label' => 'Email Address',
			'label_post' => 'inside',
			'required' => true,
			'key' => $key,
			'order' => $this->order
		);



		$nfField->update_settings($settings)->save();

		$this->addActionFieldMap($key);
	}

	/**
	 * Add form fields
	 */
	protected function addFormFields()
	{

		$collection = $this->formBuilder->getFormFields()->getFields();

		foreach ($collection as $key => $field) {
			$nfField = $this->nfForm->field()->get();
			// increment order
			$this->order++;
			$type = $this->selectNFFieldType($field->getType());

			$settings = array(
				'type' => $type,
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

		$actionMetaKey = $this->listId . '_' . $key;
		$actionMetaValue = '{field:' . $key . '}';

		$mcEntity = $this->determineMailchimpEntity($key);
		$interestsKey = $this->listId . '_interests';
		if ('interestCategory' === $mcEntity) {
			if (isset($this->actionFieldMap[$interestsKey])) {
				$temp = explode(',', $this->actionFieldMap[$interestsKey]);
			} else {
				$temp = [];
			}
			$temp[] = $actionMetaValue;
			$this->actionFieldMap[$interestsKey] = implode(',', $temp);
		} else {
			$this->actionFieldMap[$actionMetaKey] = $actionMetaValue;
		}
	}

	/**
	 * Add the Mailchimp action to the form
	 *
	 * Currently hardcoded; perhaps switch to using delivered action entity
	 */
	protected function addMailchimpAction()
	{
		$action = $this->nfForm->action()->get();

		$this->actionFieldMap['type'] = 'mailchimp';
		$this->actionFieldMap['label'] = 'MyMailchimp';
		$this->actionFieldMap['active'] = '1';
		$this->actionFieldMap['newsletter_list'] = $this->listId;

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

				$actionMeta=[
					'type'=>'save',
					'label'=>'Store Submission',
					'active'=>'1'
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

				$actionMeta=[
					'type'=>'successmessage',
					'label'=>'Success Message',
					'success_msg'=>'<p>Your form has been successfully submitted.</p><p><br></p>',
					'active'=>'1'
				];


				$action->update_settings($actionMeta);

				$action->save();
	}
		
	/**
	 * Select the NF form field type from the Mailchimp field type
	 * @param string $incoming
	 * @return string
	 */
	protected function selectNFFieldType(string $incoming): string
	{

		$outgoing = 'textbox';
		switch ($incoming) {
						// Mailchimp email address uses NF email field type
			case 'email':
				$outgoing = 'email';
				break;
			// MC multiselect uses NF listcheckbox
			case 'multiselect':
			// NF term	
			case 'listcheckbox':
				$outgoing = 'listcheckbox';
				break;
			// Mailchimp term
			case 'radio':
			// NF term	
			case 'listradio':	
				$outgoing = 'listradio';
				break;
			// Mailchimp term	
			case 'dropdown':
			// NF term	
			case 'listselect':
				$outgoing = 'listselect';
				break;
		}
		return $outgoing;
	}

	/**
	 * Determines if form field is for MergeVar, InterestCategory
	 *
	 * values = mergeVar | interestCategory
	 * @param string $key
	 * @return string
	 */
	protected function determineMailchimpEntity(string $key): string
	{
		$mcEntity = 'unknown';

		if ($this->audienceDefinition->hasMergeVar($key)) {
			$mcEntity = 'mergeVar';
		} elseif (in_array($key, $this->interestCategories)) {
			$mcEntity = 'interestCategory';
		}
		return $mcEntity;
	}

	/**
	 * Return a selected audience definition for which to create form
	 *
	 * @return AudienceDefinition
	 */
	protected function getAudienceDefinition(): AudienceDefinition
	{
  
		$singleList = SingleList::fromArray(['id'=>$this->listId]);

		$audienceDefinition = (new GetAudienceDefinitionData($this->mailchimpApi))->handle($singleList);
		return $audienceDefinition;
	}

	/**
	 * Get the newly created form Id
	 * @return int
	 */
	public function getFormId():int
	{
		return $this->formId;
	}
		/**
		 * Get the constructed Ninja Form
		 * @return NF_Abstracts_ModelFactory
		 */
	public function getNinjaForm():NF_Abstracts_ModelFactory
	{
		return $this->nfForm;
	}
}
