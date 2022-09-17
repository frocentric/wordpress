<?php

namespace NFMailchimp\EmailCRM\Mailchimp\Entities;

use NFMailchimp\EmailCRM\Mailchimp\Entities\MergVar;
use NFMailchimp\EmailCRM\Shared\SimpleEntity;

/**
 * Object describing a collection of MergeVars of a mailchimp list
 */
class MergeVars extends MailChimpEntity
{

	/**
	 * @var MergeVar[]
	 */
	protected $mergeVars;

	/** @inheritDoc */
	public static function fromArray(array $items): SimpleEntity
	{
		$obj = new static();

		// Entity structure else response structure
		if (isset($items['mergeVars'])) {
			$array = $items['mergeVars'];
		} else {
			$array = $items;
		}

		foreach ($array as $property => $mergeVar) {
			if ('list_id' === $property) {
				$obj->setListId($mergeVar);
				continue;
			}
			if (!is_array($mergeVar)) {
				if (is_a($mergeVar, MergeVar::class)) {
					$obj->addMergeVar($mergeVar);
					continue;
				} else {
					$mergeVar = (array) $mergeVar;
				}
			}
			$obj->addMergeVar(MergeVar::fromArray($mergeVar));
		}

		// Entity structure contains listId
		if (isset($items['listId'])) {
			$obj->setListId($items['listId']);
		}

		return $obj;
	}

	/**
	 * @param MergeVar $mergeVar
	 *
	 * @return MergeVars
	 */
	public function addMergeVar(MergeVar $mergeVar): MergeVars
	{
		if (!is_array($this->mergeVars)) {
			$this->mergeVars = $this->getMergeVars();
		}
		$this->mergeVars[$mergeVar->getTag()] = $mergeVar;
		return $this;
	}

	/**
	 * @param string $id
	 *
	 * @return MergeVar
	 * @throws Exception
	 */
	public function getMergeVar(string $id): MergeVar
	{
		if (!$this->hasMergeVar($id)) {
			throw new Exception();
		}
		return $this->getMergeVars()[$id];
	}

	/**
	 * Find a merge var by its tag
	 *
	 * @param string $tag
	 *
	 * @return null|MergeVar
	 */
	public function findMergeVarByTag(string $tag): ?MergeVar
	{
		/** @var MergeVar $mergeVar */
		foreach ($this->mergeVars as $mergeVar) {
			if ($tag === $mergeVar->getTag()) {
				return $mergeVar;
			}
		}
		return null;
	}

	/**
	 * @return array
	 */
	public function getMergeVars(): array
	{
		return is_array($this->mergeVars) ? $this->mergeVars : [];
	}

	/**
	 * Remove merge var from collection
	 *
	 * @param string $id
	 * @return bool
	 */
	public function removeMergeVar(string $id): bool
	{
		if (!$this->hasMergeVar($id)) {
			return false;
		}
		$mergeVars = $this->getMergeVars();
		if (isset($mergeVars[$id])) {
			unset($mergeVars[$id]);
		} else {
			if (!empty($this->getMergeVars())) {
				/** @var MergeVar $mergeVar */
				foreach ($this->getMergeVars() as $mergeVar) {
					if ($id === $mergeVar->getTag()) {
						unset($mergeVars[$mergeVar->getMergeId()]);
						break;
					}
				}
			}
		}
		$this->mergeVars = $mergeVars;
		return true;
	}

	/**
	 * Check if merge var is in collection
	 *
	 * @param string $mergeIdOrMergeTag
	 * @return bool
	 */
	public function hasMergeVar(string $mergeIdOrMergeTag): bool
	{
		if (isset($this->getMergeVars()[$mergeIdOrMergeTag])) {
			return true;
		}
		if (!empty($this->getMergeVars())) {
			/** @var MergeVar $mergeVar */
			foreach ($this->getMergeVars() as $mergeVar) {
				if ($mergeIdOrMergeTag === $mergeVar->getTag()) {
					return true;
				}
			}
		}
		return false;
	}
}
