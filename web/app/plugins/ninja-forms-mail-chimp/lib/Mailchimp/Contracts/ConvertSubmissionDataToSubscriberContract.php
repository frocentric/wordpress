<?php

namespace NFMailchimp\EmailCRM\Mailchimp\Contracts;

use NFMailchimp\EmailCRM\Mailchimp\Entities\Subscriber;

/**
 * Contract requirements for ConvertSubmissionDataToSubscriber
 */
interface ConvertSubmissionDataToSubscriberContract
{

	/**
	 * Get constructed subscriber
	 * @return Subscriber
	 */
	public function getSubscriber(): Subscriber;

	/**
	 * Return request body for subscriber
	 * @return array
	 */
	public function getRequestBody(): array;

	/**
	 * Return email address
	 * @return string
	 */
	public function getEmailAddress(): string;
}
