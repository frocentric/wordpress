<?php

namespace NFMailchimp\EmailCRM\Mailchimp\Entities;

use NFMailchimp\EmailCRM\Mailchimp\Entities\InterestCategories;
use NFMailchimp\EmailCRM\Mailchimp\Entities\Interests;
use NFMailchimp\EmailCRM\Mailchimp\Entities\MergeVars;
use NFMailchimp\EmailCRM\Shared\SimpleEntity;
use NFMailchimp\EmailCRM\Mailchimp\Entities\Segments;

/**
 * Describes a complete Audience/List with available merge vars/tags/interests
 */
class AudienceDefinition extends MailChimpEntity
{

	/**
	 *
	 * @var string
	 */
	public $name;

	/**
	 *
	 * @var MergeVars
	 */
	public $mergeFields;

	/**
	 *
	 * @var InterestCategories
	 */
	public $interestCategories;

	/**
	 *
	 * @var Interests
	 */
	public $interests;

	/**
	 *
	 * @var Segments
	 */
	public $tags;

	public function __construct()
	{
				$this->listId = '';
				$this->name = '';
		$this->mergeFields = new MergeVars();
				$this->interestCategories = new InterestCategories();
		$this->interests = new Interests();
		$this->tags = new Segments();
	}

	/**
	 * Construct the audience definition beginning with a single list
	 * @param SingleList $list
	 *
	 * @return  AudienceDefinition
	 */
	public function addList(SingleList $list): AudienceDefinition
	{
		$this->listId = $list->getListId();
		$this->name = $list->getName();

		// Interests are appended, not automatically added with entity
		$this->interests->setListId($this->listId);
		return $this;
	}

	/**
	 * Add Merge Fields to the audience definition
	 * @param MergeVars $mergeVars
	 *
	 * @return AudienceDefinition
	 */
	public function addMergeFields(MergeVars $mergeVars): AudienceDefinition
	{
		$this->mergeFields = $mergeVars;
		return $this;
	}

	/**
	 * Add Interest Categories to the audience definition
	 *
	 * Each audience def has a single list of interest categories; each of these
	 * categories has its own collection of interests associated with it
	 *
	 * @param InterestCategories $interestCategories
	 *
	 * @return AudienceDefinition;
	 */
	public function addInterestCategories(InterestCategories $interestCategories): AudienceDefinition
	{
		$this->interestCategories = $interestCategories;
		return $this;
	}

	/**
	 * Adds a collection of interests to the audience definition
	 *
	 * Interests are retrieved by each interest category grouping, thus a separate
	 * request is made to retrieve the interests for each category and then
	 * appended here.  Requests made to add members to the API do not
	 * involve the interest category as the interest Id is unique across all
	 * categories and can be combined into a single interests collection.
	 *
	 * @param Interests $interests
	 * @return  AudienceDefinition
	 */
	public function appendInterests(Interests $interests): AudienceDefinition
	{
		$this->interests->appendInterests($interests);
		return $this;
	}

	/**
	 * Add tags to the audience definition.  Only tag segments are added
	 * @param Segments $segments
	 *
	 * @return  AudienceDefinition
	 */
	public function addTags(Segments $segments): AudienceDefinition
	{
		$this->tags = $segments->getTags();
		return $this;
	}

	/**
	 * Checks if supplied MergeVar exists in Audience Definition, returns boolean
	 * @param string $mergeVar
	 * @return bool
	 */
	public function hasMergeVar(string $mergeVar): bool
	{
		return $this->mergeFields->hasMergeVar($mergeVar);
	}

	/**
	 * Returns size limit for merge var; 0 indicates no size limit specified
	 * @param string $mergeVar
	 * @return int
	 */
	public function mergeVarSizeLimit(string $mergeVar): int
	{
		$return = $this->mergeFields->getMergeVar($mergeVar)->getSize();

		return $return;
	}

	/**
	 * Returns boolean true if given interest Id is in Audience Def
	 * @param string $interestId
	 * @return bool
	 */
	public function hasInterest(string $interestId): bool
	{
		return $this->interests->hasInterest($interestId);
	}

	/**
	 * @param string $categoryId
	 * @return Interest[];
	 */
	public function getCategoryInterests(string $categoryId): array
	{
		$categoryInterests = [];
		$interests = is_array($this->interests) ? $this->interests : $this->interests->getInterests();
		foreach ($interests as $interest) {
			if ($categoryId === $interest->getCategoryId()) {
				$categoryInterests[] = $interest;
			}
		}

		return $categoryInterests;
	}

	/**
	 * Get an interest from collection
	 *
	 * @param string $interestId
	 * @return Interest
	 * @throws Exception
	 */
	public function getInterest(string $interestId): Interest
	{
		if (!$this->hasInterest($interestId)) {
			throw new Exception('Interest Not found');
		}
		return $this->interests->getInterest($interestId);
	}

	/**
	 * Returns boolean true if given tag is in Audience Def
	 * @param string $tag
	 * @return bool
	 */
	public function hasTag(string $tag): bool
	{
		return $this->tags->hasSegment($tag);
	}

	/**
	 * @return AudienceDefinition
	 */
	public static function fromArray(array $items): SimpleEntity
	{
		$obj = new static();

		foreach ($items as $property => $value) {
			switch ($property) {
				case 'mergeFields':
					$mergeFields = MergeVars::fromArray($value);
					$obj->mergeFields = $mergeFields;
					break;
				case 'interestCategories':
					$interestCategories = InterestCategories::fromArray($value);
					$obj->interestCategories = $interestCategories;
					break;
				case 'interests':
					$interests = Interests::fromArray($value);
					$obj->interests = $interests;
					break;
				case 'tags':
					if (! is_array($value)) {
						$tags = new Segments();
					} else {
						$tags = Segments::fromArray($value);
					}
					$obj->tags = $tags;
					break;
				case 'listId':
				case 'list_id':
					$obj->setListId($value);
					break;
				default:
					if (null !== $value) {
						$obj = $obj->__set($property, $value);
					}
			}
		}

		return $obj;
	}
}
