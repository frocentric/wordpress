<?php


namespace NFMailchimp\EmailCRM\Mailchimp\Contracts;

use NFMailchimp\EmailCRM\Mailchimp\Actions\SubscribeToList;
use NFMailchimp\EmailCRM\Mailchimp\Entities\Responses\SubscriptionSuccessful;
use NFMailchimp\EmailCRM\Mailchimp\Entities\SubscribeGroups;
use NFMailchimp\EmailCRM\Mailchimp\Entities\SubscribeMergeVars;
use NFMailchimp\EmailCRM\Mailchimp\Entities\Subscriber;

interface SubscribesViaApi
{

	/**
	 * Add a subscriber to list
	 *
	 * MUST set subscribe merge vars first.
	 *
	 * @param Subscriber $subscriber
	 * @return SubscriptionSuccessful
	 * @throws \Exception
	 */
	public function subscribe(Subscriber $subscriber) : SubscriptionSuccessful;

	/**
	 * @param SubscribeMergeVars $subscribeVars
	 * @return SubscribeToList
	 */
	public function setMergeFieldsToSubscribeTo(SubscribeMergeVars $subscribeVars): SubscribeToList;
	public function setGroupsToSubscribeTo(SubscribeGroups $subscribeGroups): SubscribeToList;
}
