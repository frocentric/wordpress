<?php

namespace NFMailchimp\NinjaForms\Mailchimp\Handlers;

// Mailchimp
use NFMailchimp\EmailCRM\Mailchimp\Handlers\SubscribeFormActionHandler;
use NFMailchimp\EmailCRM\Mailchimp\Actions\GetAudienceDefinitionData;
use NFMailchimp\EmailCRM\Mailchimp\Entities\SingleList;
// NF Bridge
use NFMailchimp\EmailCRM\NfBridge\Contracts\NfActionProcessHandlerContract;
// Shared
use NFMailchimp\EmailCRM\Shared\Contracts\SubmissionDataContract;
use NFMailchimp\EmailCRM\Shared\Contracts\FormContract;

/**
 * Extends Mailchimp's SubscribeFormActionHandler to return response for NF Action
 */
class MailchimpSubscribeActionProcessHandler extends SubscribeFormActionHandler implements NfActionProcessHandlerContract
{

	/**
	 * Selected listId for the submission data
	 * @var string
	 */
	protected $listId;

	/**
	 * Collection of prefixed submission data that matches the listId
	 *
	 * These values will be extracted and reconstructed into Mailchimp's
	 * required format
	 * @var array
	 */
	protected $listData = [];

	/**
	 * Array of interestId
	 * @var array
	 */
	protected $interests = [];

	/**
	 * Array of (string) tags
	 * @var array
	 */
	protected $tags = [];

	/**
	 * Response array
	 * @var array
	 */
	public $response=[];
	
	/**
	 * Process the data
	 *
	 * This method overrides Mailchimp's standard method because Ninja Forms'
	 * standard newsletter data is different than what Mailchimp is
	 * expecting in the Submission data.  This override of handle()
	 * sends the Submission data for additional processing to restructure
	 * the data as needed for Mailchimp
	 * @param SubmissionDataContract $submissionData
	 * @param FormContract $form
	 * @return mixed
	 */
	public function handle(SubmissionDataContract $submissionData, FormContract $form):array
	{
			$optIn = $submissionData->getValue('opt_in', true);
			
		if (!$optIn) {
			return [
							'type'=>'exited',
							'context'=>'MailchimpSubscribeActionProcessHandler_OptOut',
							'message'=>'Submission opted out'
							];
		}
			
		$this->structureSubmissionData($submissionData);

		$this->form = $form;

		$apiKey = $this->submissionData->getValue('apiKey');
		if (isset($this->audienceDefinitions[$this->listId])) {
			$audienceDefinition = $this->audienceDefinitions[$this->listId];
		} else {
			try {
				$audienceDefinition = (
					new GetAudienceDefinitionData(
						$this->mailchimpApiFactory->listsApi($apiKey, $this->httpClient),
						(new SingleList())->setId($this->listId)
					)
					)->handle();
			} catch (\Exception $exception) {
				$this->response = [
								'type'=>'error',
				'context' => 'MailchimpSubscribeActionProcessHandler_GetAudienceDefinitionData',
				'message' => $exception->getMessage(),
				'diagnostics' => ''
									]
				;

				return $this->response;
			}
		}
		$this->addAudienceDefinition($audienceDefinition);
		$this->constructSubscriber();
		$this->subscribeToList();

		if (is_a($this->response, \Exception::class)) {
			return  [
				'type' => 'error',
								'context' => 'MailchimpSubscribeActionProcessHandler_Response',
				'note' => $this->response->getMessage()
			];
		}
		return $this->response;
	}

   /**
     * Extract processing data from form fields to return key-value pairs
     *
     * Some data required by submission action is contained within form
     * fields.  Given the form fields data upon submission, extract the
     * required data to return it as key-value pairs such that it can be
     * added to the submission data and processed
     * @param array $data Form field process data
     * @return array
     */
    public function extractFormFieldProcessingData(array $data): array {
        $formFieldProcessingData = [];

        $formFieldProcessingData['opt_in'] = $this->isOptIn($data['fields']);

        return $formFieldProcessingData;
    }

    /**
     * Return bool - if this sub has an opt-in field, is it checked? If no opt-in field, default to true
     * @param array $fields
     * @return bool
     */
    protected function isOptIn(array $fields):bool
	{
		 // Set true flag for later use.
		$opt_in = true;

		// Loop over the fields from the form data and...
		foreach ($fields as $field) {
			// ...If the field type is equal to Mailchimp Opt continue.
			if ('mailchimp-optin' != $field[ 'type' ]) {
				continue;
			}

			// ...If the field value is the field value is false change the optin flag to false.
			if (! $field[ 'value' ]) {
				$opt_in = false;
			}
		}
		return $opt_in;
	}
		
		
		
	/**
	 * Structure submission data to Mailchimp requirements
		 *
		 * @param SubmissionDataContract $submissionData
		 * @return SubmissionDataContract
		 */
	public function structureSubmissionData(SubmissionDataContract $submissionData): SubmissionDataContract
	{
				$this->submissionData = $submissionData;
		$this->setListId();
				$this->setStatus();
		if (0 === strlen($this->listId)) {
			return $this->submissionData;
		}
		
		$apiKey = $this->getApiKey();
		$this->submissionData->addData('apiKey', $apiKey);
		$this->extractListData();
		$this->extractEmailAddress();
		$this->extractPreselectedInterests();
		$this->appendUserSelectedInterests();
		$this->extractTags();
		$this->implodeTagsAndInterests();
		$this->setMergeVars();
				
				return $this->submissionData;
	}

	/**
	 * Sets listId from NF's newsletter_list value
	 */
	protected function setListId()
	{
		$listId = $this->submissionData->getValue('newsletter_list', '');
		$this->listId = $listId;
		$this->submissionData->addData('listId', $this->listId);
	}

	/**
	 * Extract all data keyed on submitted listId
	 */
	protected function extractListData()
	{
		$existing = $this->submissionData->toArray();

		$prefix = $this->listId . '_';

		foreach ($existing as $key => $value) {
			if (0 === strpos($key, $prefix)) {
				$this->listData[str_replace($prefix, '', $key)] = $value;
			}
		}
	}

	/**
	 * Extract email address for selected list
	 */
	protected function extractEmailAddress()
	{
		$prefix = $this->listId . '_';
		$emailAddress = $this->submissionData->getValue($prefix.'email_address', '');
		$this->submissionData->addData('email_address', $emailAddress);

		// unset from listData
		unset($this->listData['email_address']);
	}

	/**
	 * Extract interest groups manually set in Mailchimp action
	 */
	protected function extractPreselectedInterests()
	{

		$prefix = 'group_';
		foreach ($this->listData as $key => $value) {
			if (0 === strpos($key, $prefix)) {
				$exploded = explode('_', $key);

				if (isset($exploded[1]) && 1===intval($value)) {
					$this->interests[] = $exploded[1];
				}

				// remove from listData after moving to interests
				unset($this->listData[$key]);
			}
		}
	}

	/**
	 * Append user selected interests as array of interest Ids
	 */
	protected function appendUserSelectedInterests(): void
	{
		if (!isset($this->listData['interests'])) {
			return;
		}

		$userSelectedInterests = explode(',', $this->listData['interests']);

		$this->interests = array_merge($this->interests, $userSelectedInterests);

		// remove from listData
		unset($this->listData['interests']);
	}

	/**
	 * Implode interests into expected comma-delimited string
	 */
	protected function implodeTagsAndInterests()
	{
		if (is_array($this->interests)) {
			$this->submissionData->addData('interests', implode(',', $this->interests));
		}
		if (is_array($this->tags)) {
			$this->submissionData->addData('tags', implode(',', $this->tags));
		}
	}
	
	/**
	 * Extract tags
	 *
	 */
	protected function extractTags(): void
	{
		if (!isset($this->listData['tags'])) {
			return;
		}

		$tagArray = explode(',', $this->listData['tags']);

		$this->tags = $tagArray;

		// remove from listData
		unset($this->listData['tags']);
	}

	/**
	 * Set merge tags; these are what's left after extracting other types
	 */
	protected function setMergeVars()
	{
		foreach ($this->listData as $key => $value) {
			$this->submissionData->addData($key, $value);
			unset($this->listData[$key]);
		}
	}

		/**
		 * Set status based on double opt in
		 *
		 * If double opt in set to "1", set status to pending, otherwise set to
		 * "subscribed"
		 */
	protected function setStatus()
	{
			
		$doubleOptIn = $this->submissionData->getValue('double_opt_in', "0");
			
		if ("1"=== $doubleOptIn) {
			$this->submissionData->addData('status', 'pending');
		} else {
			$this->submissionData->addData('status', 'subscribed');
		}
	}
	/**
	 * Get API Key from Ninja Forms settings
		 * @todo: Replace hardcoded with global settings entity version
	 */
	protected function getApiKey():string
	{
		$apiKey = trim(Ninja_Forms()->get_setting('ninja_forms_mc_api'), '');
		return $apiKey;
	}
	/**
	 * Return Ninja Forms $data submission after processing
	 * @return array
	 */
	public function getPostProcessData(): array
	{

		return $this->response;
	}
}
