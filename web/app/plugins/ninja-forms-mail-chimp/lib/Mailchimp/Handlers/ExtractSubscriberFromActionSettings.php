<?php

namespace NFMailchimp\EmailCRM\Mailchimp\Handlers;

use NFMailchimp\EmailCRM\Mailchimp\Entities\AudienceDefinition;
use NFMailchimp\EmailCRM\Mailchimp\Entities\MergeVar;
use NFMailchimp\EmailCRM\Mailchimp\Entities\Segments;
use NFMailchimp\EmailCRM\Mailchimp\Entities\Subscriber;

use NFMailchimp\EmailCRM\Mailchimp\Handlers\SubscriberBuilder;
use NFMailchimp\EmailCRM\NfBridge\Contracts\ActionSettingsDataHandler;

/**
 * Converts submission data to API construct
 *
 */
class ExtractSubscriberFromActionSettings
{

	/** @var \ActionSettingsDataHandler */
	protected $actionSettingsDataHandler;

	/** @var SubscriberBuilder */
	public $subscriberBuilder;

	/** @var AudienceDefinition */
	protected $audienceDefinition;

	/** @var string */
	protected $listId;

	public function __construct(
		ActionSettingsDataHandler $actionSettingsDataHandler,
		SubscriberBuilder $subscriberBuilder
	) {
		$this->actionSettingsDataHandler = $actionSettingsDataHandler;
		$this->subscriberBuilder = $subscriberBuilder;
		$this->audienceDefinition = $this->subscriberBuilder->getAudienceDefinition();
		$this->listId = $this->audienceDefinition->getListId();
	}

	public function handle(): ExtractSubscriberFromActionSettings
	{
		try {
			$this->extractSubmissionData();
			return $this;
		} catch (\Exception $e) {
			throw $e;
		}
	}
	/**
	 * Extract SubmissionData to construct Subscriber
	 */
	protected function extractSubmissionData()
	{
		$this->addEmailAddress();
		$this->addStatus();
		$this->addMergeFields();
		$this->addInterests();
		$this->addTags();
	}

	/**
	 * Add email address
	 *
	 * Email address key is known from Standard Subscriber Field entity
	 */
	protected function addEmailAddress()
	{
		$emailAddress = $this->actionSettingsDataHandler->getValue($this->listId.'_email_address', '');

		$this->subscriberBuilder->setEmailAddress($emailAddress);
	}

	/**
	 * Add status
	 *
	 * Required field; default value `subscribed` if not set
	 */
	protected function addStatus()
	{
		$doubleOptIn = $this->actionSettingsDataHandler->getValue('double_opt_in', "0");
			
		if ("1"=== $doubleOptIn) {
			$status= 'pending';
		} else {
			$status='subscribed';
		}

		$this->subscriberBuilder->setStatus($status);
	}

	/**
	 * Add MergeFields in Audience Definition, add any values from Submission Data
	 * 
	 */
	protected function addMergeFields()
	{
		$mergeFields = $this->audienceDefinition->mergeFields->getMergeVars();

		/** @var MergeVar $mergeField */
		foreach ($mergeFields as $mergeField) {
			$mergeVarTag = $mergeField->getTag();

			$value = $this->actionSettingsDataHandler->getValue($this->listId.'_'.$mergeVarTag, null);

			$mergeVarType = $mergeField->getType();

			if ('address' === $mergeVarType && \is_string($value)) {
				$value = $this->constructAddressObject($value);
			}

			if (!is_null($value) && !empty($value)) {
				$this->subscriberBuilder->setMergeField($mergeVarTag, $value);
			}
		}
	}

	/**
	 * Construct address array from stringed fieldmap value
	 *
	 * Allows for the following incoming constructs:
	 *   addr1, city, state, zip
	 *   addr1, addr2, city, state, zip
	 *   addr1, addr2, city, state, zip, country
	 * 
	 * @param string $value
	 * @return array
	 */
	protected function constructAddressObject(string $value): array
	{
		$return = [
			'addr1' => '',
			'addr2' => '',
			'city' => '',
			'state' => '',
			'zip' => '',
			'country' => 'US'
		];

		$exploded = \explode(',', $value);
		$trimmed = array_map('trim', $exploded);
		$count = count($exploded);

		switch ($count) {

			case 4:
				$return['addr1'] = $trimmed[0];
				$return['city'] = $trimmed[1];
				$return['state'] = $trimmed[2];
				$return['zip'] = $trimmed[3];
				break;
			case 5:
				$return['addr1'] = $trimmed[0];
				$return['addr2'] = $trimmed[1];
				$return['city'] = $trimmed[2];
				$return['state'] = $trimmed[3];
				$return['zip'] = $trimmed[4];
				break;
			case 6:
				$return['addr1'] = $trimmed[0];
				$return['addr2'] = $trimmed[1];
				$return['city'] = $trimmed[2];
				$return['state'] = $trimmed[3];
				$return['zip'] = $trimmed[4];
				$return['country'] = $trimmed[5];
		}

		return $return;
	}

	/**
	 * Iterate values in SubmissionData interests, add if allowed in AudienceDefinition
	 */
	protected function addInterests()
	{
		// get array keys of interest in the audience definition
		$allowedInterests = array_keys($this->audienceDefinition->interests->getInterests());
		// get all values in comma-delineated string, removing empty values and whitespace
		$values = array_map('trim', array_filter(explode(',', $this->actionSettingsDataHandler->getValue($this->listId.'_interests', ''))));
		foreach ($values as $value) {
			if (in_array($value, $allowedInterests)) {
				$this->subscriberBuilder->addInterest($value);
			}
		}
	}

	/**
	 * Iterate values in SubmissionData tags, add if allowed in AudienceDefinition
	 */
	protected function addTags()
	{
		/** @var Segments $tagSegments */
		// get array keys of interest in the audience definition
		$tagSegments = ($this->audienceDefinition->tags->getTags());
		// get all values in comma-delineated string, removing empty values and whitespace
		$values = array_map('trim', array_filter(explode(',', $this->actionSettingsDataHandler->getValue($this->listId.'_tags', ''))));

		if (!empty($values)) {
			foreach ($values as $value) {
				if ($tagSegments->hasSegment($value)) {
					$this->subscriberBuilder->addTag($value);
				}
			}
		}
	}

	/**
	 * Get constructed subscriber
	 * @return Subscriber
	 */
	public function getSubscriber(): Subscriber
	{
		return $this->subscriberBuilder->getSubscriber();
	}

	/** @inheritdoc */
	public function getRequestBody(): array
	{

		return $this->subscriberBuilder->getRequestBody();
	}

	/** @inheritdoc */
	public function getEmailAddress(): string
	{
		return $this->subscriberBuilder->getEmailAddress();
	}
}
