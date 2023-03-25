<?php


namespace NFMailchimp\EmailCRM\Mailchimp\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;

/**
 * Describes one Mailchimp list
 */
class SingleList extends MailChimpEntity
{

	/** @var string */
	protected $id;
	/**
	 * @var MergeVars
	 */
	protected $mergeFields;

	/**
	 * @var Groups
	 */
	protected $groupFields;

	/** @var string */
	protected $name;

	/**
	 * @var array
	 */
	protected $segments;

	/** @var array  */
	protected $mergeFieldIds;
	/** @var array  */
	protected $groupFieldIds;


	/** @var int */
	protected $accountId;



	/**
	 * SingleList constructor.
	 */
	public function __construct()
	{
		$this->mergeFieldIds = [];
		$this->groupFieldIds = [];
	}

	/**
	 * Get ids of all merge fields
	 *
	 * @return array
	 */
	public function getMergeFieldIds(): array
	{
		return $this->mergeFieldIds;
	}

	/**
	 * Get ids of all group fields
	 *
	 * @return array
	 */
	public function getGroupFieldIds(): array
	{
		return $this->groupFieldIds;
	}




	public function hasMergeFields() : bool
	{
		return ! empty($this->getMergeFieldsArray());
	}
	protected function getMergeFieldsArray() : array
	{
		if (!  $this->mergeFields || empty($this->getMergeFields()->toArray()['mergeVars'])) {
			return [];
		}
		return $this->getMergeFields()->toArray();
	}

	protected function getGroupFieldsArray() : array
	{
		if (! $this->groupFields) {
			return [];
		}
		return $this->getGroupFields()->toArray();
	}

	/**
	 *
	 * @param array $items
	 *
	 * @return SimpleEntity
	 */
	public static function fromArray(array $items): SimpleEntity
	{
		if (isset($items[ 'groupFields' ]) && is_array($items[ 'groupFields' ])) {
			$items[ 'groupFields' ] = Groups::fromArray($items[ 'groupFields' ]);
		}

		if (isset($items[ 'groups' ]) && is_array($items[ 'groups' ])) {
			$items[ 'groupFields' ] = Groups::fromArray($items[ 'groups' ]);
		}
		if (isset($items[ 'mergeFields' ]) && is_array($items[ 'mergeFields' ])) {
			if (isset($items[ 'mergeFields' ]['mergeVars'])&& is_array($items[ 'mergeFields' ]['mergeVars'])) {
				$items['mergeFields'] = MergeVars::fromArray($items['mergeFields'][ 'mergeVars' ]);
			} else {
				$items['mergeFields'] = MergeVars::fromArray($items['mergeFields']);
			}
		}
		return parent::fromArray($items);
	}


	/**
	 * @return MergeVars
	 */
	public function getMergeFields(): MergeVars
	{
		return $this->mergeFields;
	}

	/**
	 * @param MergeVars $mergeFields
	 *
	 * @return SingleList
	 */
	public function setMergeFields(MergeVars $mergeFields): SingleList
	{
		$this->mergeFields = $mergeFields;
		return $this;
	}

	/**
	 * @return Groups
	 */
	public function getGroupFields(): Groups
	{
		return $this->groupFields;
	}

	/**
	 * @param Groups $groupFields
	 *
	 * @return SingleList
	 */
	public function setGroupFields(Groups $groupFields): SingleList
	{
		$this->groupFields = $groupFields;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return is_string($this->name) ? $this->name : '';
	}

	/**
	 * @param string $name
	 *
	 * @return SingleList
	 */
	public function setName(string $name): SingleList
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getSegments(): array
	{
		return is_array($this->segments) ? $this->segments : [];
	}

	/**
	 * @param array $segments
	 *
	 * @return SingleList
	 */
	public function setSegments(array $segments): SingleList
	{
		$this->segments = $segments;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getAccountId(): int
	{
		return $this->accountId;
	}

	/**
	 * @param int $accountId
	 *
	 * @return SingleList
	 */
	public function setAccountId(int $accountId = null): SingleList
	{
		$this->accountId = $accountId;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getId(): string
	{
		return $this->id;
	}

	/**
	 * @param string $id
	 *
	 * @return SingleList
	 */
	public function setId(string $id): SingleList
	{
		$this->id = $id;
		return $this;
	}

	/** @inheritDoc */
	public function getListId(): string
	{
		return $this->getId();
	}
}
