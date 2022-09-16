<?php

namespace NFMailchimp\EmailCRM\Mailchimp\Handlers;

// Contracts
use NFMailchimp\EmailCRM\Shared\Contracts\FormBuilder;
// Entities
use NFMailchimp\EmailCRM\Mailchimp\Entities\AudienceDefinition;
use NFMailchimp\EmailCRM\Mailchimp\Entities\MergeVar;
use NFMailchimp\EmailCRM\Mailchimp\Entities\InterestCategory;
use NFMailchimp\EmailCRM\Mailchimp\Entities\Interest;
use NFMailchimp\EmailCRM\Shared\Entities\Option;
use NFMailchimp\EmailCRM\Shared\Entities\Options;
use NFMailchimp\EmailCRM\Shared\Entities\FormField;

/**
 * Given an AudienceDefinition, construct a shared FormBuilder entity
 */
class CreateFormBuilder
{

	/**
	 *
	 * @var AudienceDefinition
	 */
	protected $audienceDefinition;

	/**
	 * 
	 * @var FormBuilder
	 */
	protected $formBuilder;

	public function __construct(AudienceDefinition $audienceDefinition, FormBuilder $formBuilder)
	{
		$this->audienceDefinition = $audienceDefinition;
		$this->formBuilder = $formBuilder;
		
		$this->constructFormBuilder();
	}

	/**
	 * Call methods that construct FormBuilder entity
	 */
	protected function constructFormBuilder()
	{
		$this->setFormTitle();
		$this->addMergeVars();
		$this->addInterestCategories();
	}

	/**
	 * Set the form title as Audience Definition name
	 */
	protected function setFormTitle()
	{
		$formTitle = $this->audienceDefinition->name;

		$this->formBuilder->setTitle($formTitle);
	}

	/**
	 * Add MergeVars from AudienceDefinition as FormFields
	 */
	protected function addMergeVars()
	{
		$collection = $this->audienceDefinition->mergeFields->getMergeVars();

		foreach ($collection as $mergeVar) {
			$formField = $this->constructMergeVarField($mergeVar);
			$this->formBuilder->addFormField($formField);
		}
	}

	/**
	 * Add InterestCategories as FormFields
	 */
	protected function addInterestCategories()
	{
		$collection = $this->audienceDefinition->interestCategories->getInterestCategories();
		
		foreach ($collection as $interestCategory) {
			$formField = $this->constructInterestCategoryField($interestCategory);
			$this->formBuilder->addFormField($formField);
		}
	}
	
	
	/**
	 * Constructs a form field from a merge var
	 * @param MergeVar $mergeVar
	 * @return FormField
	 */
	protected function constructMergeVarField(MergeVar $mergeVar): FormField
	{

		$formField = new FormField();

		// Mailchimp `tag` is the programmatic name
		$formField->setId($mergeVar->getTag());

		// Set label from MC name
		$formField->setLabel($mergeVar->getName());

		// Use method to select shared field type from MC field type
		$formField->setType($this->selectFieldType($mergeVar));

		// Set default value only if MC has a value
		if ('' !== $mergeVar->getDefaultValue()) {
			$formField->setDefault($mergeVar->getDefaultValue());
		}

		// Set required
		if ($mergeVar->getRequired()) {
			$formField->setRequired(true);
		}

		// Set character limit if specified in MC
		$options = $mergeVar->getOptions();
		if (isset($options['size'])) {
			$formField->setCharacterLimit(intval($options['size']));
		}

		// If choices is selected, field has list options to select
		// Mailchimp 'choices' <==> Form list options
		if(isset($options['choices'])){

			$optionsEntity = $this->constructMergeFieldOptions($options['choices']);

			$formField->setOptions($optionsEntity);
		}
		return $formField;
	}

	/**
	 * Construct Options collection from array of Mailchimp 'choices'
	 *
	 * @param array $choices
	 * @return Options
	 */
	protected function constructMergeFieldOptions(array $choices): Options
	{
		$options = new Options();
		
		foreach ($choices as $choice) {
			$option = Option::fromArray([
				'label'=>$choice,
				'value'=>$choice
			]);

			$options->addOption($option);
		}

		return $options;
	}

	/**
	 * Construct FormField from an InterestCategory
	 * @param InterestCategory $interestCategory
	 * @return FormField
	 */
	protected function constructInterestCategoryField(InterestCategory $interestCategory):FormField
	{
		$formField = new FormField();
			  // Mailchimp `tag` is the programmatic name
		$formField->setId($interestCategory->getId());

		// Set label from MC name
		$formField->setLabel($interestCategory->getTitle());

		// Use method to select shared field type from MC field type
		$formField->setType('multiselect');

		// Set options
		$formField->setOptions($this->constructInterestCategoryOptions($interestCategory->getId()));

		return $formField;
	}
	
	/**
	 * Construct Options entity for a given InterestCategoryId
	 * @param string $interestCategoryId
	 * @return Options
	 */
	protected function constructInterestCategoryOptions(string $interestCategoryId):Options
	{
		/** @var Interest $interest */
		$options = new Options();
		$collection = $this->audienceDefinition->interests->getInterests();
		foreach ($collection as $interest) {
			if ($interestCategoryId != $interest->getCategoryId()) {
				continue;
			}
			
			$option = new Option();
			$option->setLabel($interest->getName());
			$option->setValue($interest->getId());
			
			$options->addOption($option);
		}
		
		return $options;
	}
	
	/**
	 * Selects a standard field type from Mailchimp's field type
	 *
	 * @param MergeVar $mergeVar
	 * @return string
	 */
	protected function selectFieldType(MergeVar $mergeVar): string
	{

		$mailchimpFieldType = $mergeVar->getType();

		$fieldType = 'textbox';
		switch ($mailchimpFieldType) {
			case 'multiselect':
				$fieldType = 'multiselect';
				break;
			// Mailchimp term	
			case 'radio':
			// NF term	
			case 'listradio':
				$fieldType = 'listradio';
				break;
			// Mailchimp term	
			case 'dropdown':
			// NF term	
			case 'listselect':
				$fieldType = 'listselect';
				break;
		}

		return $fieldType;
	}

	/**
	 * Get the Form Builder
	 * @return FormBuilder
	 */
	public function getFormBuilder(): FormBuilder
	{
		return $this->formBuilder;
	}
}
