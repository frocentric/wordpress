<?php

namespace NFMailchimp\EmailCRM\Mailchimp\Handlers;

use NFMailchimp\EmailCRM\Mailchimp\Entities\AudienceDefinition;
use NFMailchimp\EmailCRM\Mailchimp\Entities\Subscriber;

/**
 * Constructs Subscriber entity using an AudienceDef object for validation
 */
class SubscriberBuilder
{
	/**
	 *
	 * @var AudienceDefinition
	 */
	protected $audienceDefinition;

	/**
	 *
	 * @var Subscriber
	 */
	protected $subscriber;

	/**
	 * Subscriber body as associative array
	 * @var array
	 */
	protected $requestBody;

	/**
	 * Constructs Subscriber entity using AudienceDef for validation
	 * @param AudienceDefinition $audienceDefinition
	 */
	public function __construct(AudienceDefinition $audienceDefinition)
	{
		$this->audienceDefinition = $audienceDefinition;

		$this->subscriber = new Subscriber();

		$this->subscriber->setListId($this->audienceDefinition->getListId());
	}

	/**
	 * Sets email address
	 * @param string $emailAddress
	 * @return SubscriberBuilder
	 */
	public function setEmailAddress(string $emailAddress): SubscriberBuilder
	{
		$this->subscriber->setEmailAddress($emailAddress);

		return $this;
	}

	/**
	 * Sets email type
	 * @param string $emailType
	 * @return SubscriberBuilder
	 */
	public function setEmailType(string $emailType): SubscriberBuilder
	{
		$this->subscriber->setEmailType($emailType);

		return $this;
	}

	/**
	 * Sets email status
	 * 
	 * @param string $status
	 * @return SubscriberBuilder
	 */
	public function setStatus(string $status): SubscriberBuilder
	{
		$this->subscriber->setStatus($status);

		return $this;
	}

	/**
	 * Adds value for Merge Var after ensuring Merge Var is part of AudienceDef
	 * 
	 * @param string $mergeVarTag
	 * @param mixed $value
	 * @return SubscriberBuilder
	 */
	public function setMergeField(string $mergeVarTag, $value): SubscriberBuilder
	{
		$hasMergeField = $this->audienceDefinition->hasMergeVar($mergeVarTag);

		if ($hasMergeField) {
			$value = $this->validateMergeVarValue($mergeVarTag, $value);

			$this->subscriber->setMergeField($mergeVarTag, $value);
		}

		return $this;
	}

	/**
	 * Forces string length no longer than size specified
	 *
	 * If a size limit is specified for the marge var, ensures value is a string
	 * and no longer than the specified character length
	 *
	 * @param string $mergeVarTag
	 * @param mixed $value
	 * @return mixed
	 */
	protected function validateMergeVarValue(string $mergeVarTag, $value)
	{
		$sizeLimit = $this->audienceDefinition->mergeVarSizeLimit($mergeVarTag);

		if (0 < $sizeLimit) {
			$value = substr((string) $value, 0, $sizeLimit);
		}

		return $value;
	}

	/**
	 * Add an interest to Subscriber entity
	 * @param string $interestId
	 * @return SubscriberBuilder
	 */
	public function addInterest(string $interestId): SubscriberBuilder
	{
		$hasInterest = $this->audienceDefinition->hasInterest($interestId);

		if ($hasInterest) {
			$this->subscriber->addInterest($interestId);
		}

		return $this;
	}

	/**
	 * Add a tag to the Subscriber entity
	 * @param string $tag
	 * @return SubscriberBuilder
	 */
	public function addTag(string $tag): SubscriberBuilder
	{
		$this->subscriber->addTag($tag);

		return $this;
	}

	/**
	 * Constructs the request body for the Subscriber request
	 */
	public function constructRequestBody()
	{
		$requestBodyArray = [];

		$classVars = get_class_vars(get_class($this->subscriber));

		foreach (array_keys($classVars) as $property) {
			if (is_null($this->subscriber->$property)) {
				continue;
			}

			$requestBodyArray[$property] = $this->subscriber->$property;
		}

		// email address is not part of request body
		unset($requestBodyArray['email_address']);
		unset($requestBodyArray['listId']);

		$this->requestBody = $requestBodyArray;

		$this->validateRequestBody();
	}

	/**
	 * Call methods that ensure request body is valid
	 */
	protected function validateRequestBody()
	{

		$this->removeEmptyMergeFields();
		$this->removeEmptyInterests();
	}


	/**
	 * Checks for empty merge_fields and unsets in requestBody
	 *
	 * @return void
	 */
	protected function removeEmptyMergeFields(): void
	{
		if (empty($this->requestBody['merge_fields'])) {
			unset($this->requestBody['merge_fields']);
		}
	}

	/**
	 * Checks for empty interests and unsets in requestBody
	 * @return void
	 */
	protected function removeEmptyInterests(): void
	{
		if (empty($this->requestBody['interests'])) {
			unset($this->requestBody['interests']);
		}
	}

	/**
	 * Returns the Subscriber entity
	 * @return Subscriber
	 */
	public function getSubscriber(): Subscriber
	{
		return $this->subscriber;
	}

	/**
	 * Returns JSON encoded request body for Subscriber entity
	 * @return array
	 */
	public function getRequestBody(): array
	{
		if (!isset($this->requestBody)) {
			$this->constructRequestBody();
		}
		$this->validateRequestBody();

		return $this->requestBody;
	}

	/**
	 * Returns the subscriber list id
	 * @return string
	 */
	public function getListId(): string
	{
		return $this->subscriber->getListId();
	}

	/**
	 * Returns the subscriber email address
	 * @return string
	 */
	public function getEmailAddress(): string
	{
		return $this->subscriber->getEmailAddress();
	}

	/**
	 * Return AudienceDefinition
	 * @return AudienceDefinition
	 */
	public function getAudienceDefinition(): AudienceDefinition
	{
		return $this->audienceDefinition;
	}
}
