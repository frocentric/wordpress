<?php

namespace NFMailchimp\EmailCRM\Mailchimp\Entities;

use NFMailchimp\EmailCRM\Mailchimp\Entities\MailChimpEntity;

/**
 * Entity from which an Add Member request can be made
 *
 * NOTE: uses Mailchimp property names
 */
class Subscriber extends MailChimpEntity
{

	/**
	 * Email address
	 * @var string
	 */
	public $email_address = '';

	/**
	 *
	 * @var string
	 */
	public $email_type;

	/**
	 *
	 * @var string
	 */
	public $status;

	/**
	 *
	 * @var array
	 */
	public $merge_fields = [];

	/**
	 *
	 * @var array
	 */
	public $interests = [];

	/**
	 *
	 * @var string
	 */
	public $language;

	/**
	 *
	 * @var bool
	 */
	public $vip;

	/**
	 * @var array
	 */
	public $location;

	/**
	 *
	 * @var array
	 */
	public $marketing_permissions;

	/**
	 *
	 * @var string
	 */
	public $ip_signup;

	/**
	 *
	 * @var string
	 */
	public $timestamp_signup;

	/**
	 *
	 * @var array
	 */
	public $tags = [];

	/**
	 * Set the email address
	 * @param string $email
	 * @return \NFMailchimp\EmailCRM\Mailchimp\Entities\Subscriber
	 */
	public function setEmailAddress(string $email): Subscriber
	{
		$this->email_address = $email;

		return $this;
	}

	/**
	 * Set the email type
	 *
	 * Allowed values html, text
	 * @param string $emailType
	 * @return \NFMailchimp\EmailCRM\Mailchimp\Entities\Subscriber
	 */
	public function setEmailType(string $emailType): Subscriber
	{

		$allowedValues = array('html', 'text');

		if (in_array($emailType, $allowedValues)) {
			$this->email_type = $emailType;
		}

		return $this;
	}

	/**
	 * Set the email type
	 *
	 * Allowed values subscribed, unsubscribed, cleaned, pending, transactional
	 * @param string $status
	 * @return \NFMailchimp\EmailCRM\Mailchimp\Entities\Subscriber
	 */
	public function setStatus(string $status): Subscriber
	{

		$allowedValues = array('subscribed', 'unsubscribed', 'cleaned', 'pending', 'transactional');

		if (in_array($status, $allowedValues)) {
			$this->status = $status;
		}

		return $this;
	}

	/**
	 * Add a merge var to request using merge var tag
	 *
	 * Confirmed through testing that merge var tag must be used, not id
	 * @param string $mergeVarTag
	 * @param mixed $value
	 * @return \NFMailchimp\EmailCRM\Mailchimp\Entities\Subscriber
	 */
	public function setMergeField(string $mergeVarTag, $value): Subscriber
	{
		$this->merge_fields[$mergeVarTag] = $value;

		return $this;
	}

	/**
	 * Add an interest to the Subscriber entity
	 * @param string $interestId
	 * @return \NFMailchimp\EmailCRM\Mailchimp\Entities\Subscriber
	 */
	public function addInterest(string $interestId): Subscriber
	{
		$this->interests[$interestId] = true;

		return $this;
	}

	/**
	 * Add a tag to the Subscriber entity
	 * @param string $tag
	 * @return \NFMailchimp\EmailCRM\Mailchimp\Entities\Subscriber
	 */
	public function addTag(string $tag): Subscriber
	{

		$this->tags[] = $tag;

		return $this;
	}

	/**
	 * Return the Subscriber email address
	 * @return string
	 */
	public function getEmailAddress(): string
	{
		return $this->email_address;
	}
}
