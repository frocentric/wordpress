<?php

namespace NFMailchimp\EmailCRM\Mailchimp\ApiRequests;

use NFMailchimp\EmailCRM\Mailchimp\Entities\AudienceDefinition;
use NFMailchimp\EmailCRM\Mailchimp\Entities\SingleList;

use NFMailchimp\EmailCRM\Mailchimp\MailchimpApi;

use NFMailchimp\EmailCRM\Mailchimp\ApiRequests\GetMergeFields;
use NFMailchimp\EmailCRM\Mailchimp\ApiRequests\GetInterestCategories;
use NFMailchimp\EmailCRM\Mailchimp\ApiRequests\GetSegments;


/**
 * Constructs Audience Definition with data retrieved through API
 *
 */
class GetAudienceDefinitionData
{
	/** @var AudienceDefinition */
	private $audienceDefinition;

	/** @var MailchimpApi */
	protected $mailchimpApi;

	/**
	 * List Id
	 * @var string
	 */
	protected $listId;

	/**
	 * Construct NfMailchimpLists action
	 * @param MailchimpApi $mailchimpApi
	 */
	public function __construct(MailchimpApi $mailchimpApi)
	{
		$this->mailchimpApi = $mailchimpApi;
	}


	/**
	 * Construct AudienceDefinition from retrieved data
	 * @return AudienceDefinition
	 */
	public function handle(SingleList $singleList): AudienceDefinition
	{
		
		$this->audienceDefinition = new AudienceDefinition();
		
		$this->audienceDefinition->addList($singleList);
		$this->getMergeFields($singleList->getListId());
		$this->getInterestCategories($singleList->getListId());
		$this->addAllInterestCategories($singleList->getListId());
		$this->getSegmentsViaRemoteApi($singleList->getListId());
		return $this->audienceDefinition;
	}

	/**
	 * Adds merge fields retrieved through API to Audience Def
	 */
	protected function getMergeFields(string $listId):void
	{
		$mergeFieldsAction = new GetMergeFields($this->mailchimpApi);
		$mergeFields = $mergeFieldsAction->requestMergeFields($listId);
		$this->audienceDefinition->addMergeFields($mergeFields);
	}

	/**
	 * Adds interest categories retrieved through API to Audience Def
	 */
	protected function getInterestCategories(string $listId):void
	{
		$interestCategoriesAction = new GetInterestCategories($this->mailchimpApi);
		$interestCategories = $interestCategoriesAction->requestInterestCategories($listId);
		$this->audienceDefinition->addInterestCategories($interestCategories);
	}

	/**
	 * Iterate through interest categories and append interests from each category
	 */
	protected function addAllInterestCategories(string $listId)
	{
		foreach ($this->audienceDefinition->interestCategories->getInterestCategories() as $interestCategory) {
			$interestCategoryId = $interestCategory->getId();
			$this->appendInterests($listId, $interestCategoryId);
		}
	}

	/**
	 * Add interests retrieved throcugh API to Audience Definition
	 *
	 * @param string $listId
	 * @param string $interestCategoryId
	 * @return void
	 */
	protected function appendInterests(string $listId, string $interestCategoryId)
	{
		$interestsAction = new GetInterests($this->mailchimpApi);
		$interests = $interestsAction->requestInterests($listId, $interestCategoryId);
		$this->audienceDefinition->appendInterests($interests);
	}

	/**
	 * Adds Tags retrieved through API to Audience Definition
	 */
	protected function getSegmentsViaRemoteApi(string $listId)
	{
		$getSegmentsAction = new GetSegments($this->mailchimpApi);
		$segments = $getSegmentsAction->requestSegments($listId);
		$this->audienceDefinition->addTags($segments);
	}
}
