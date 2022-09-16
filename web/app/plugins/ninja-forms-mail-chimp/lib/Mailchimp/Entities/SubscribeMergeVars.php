<?php


namespace NFMailchimp\EmailCRM\Mailchimp\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;

/**
 * Describes which merge var values to add a subscriber with
 */
class SubscribeMergeVars extends MailChimpEntity
{

	/**
	 * @var MergeVars
	 */
	protected $mergeVars;

	/**
	 * @var array
	 */
	protected $data;

	/**
	 * @return MergeVars
	 */
	public function getMergeVars(): MergeVars
	{
		return $this->mergeVars;
	}


	/**
	 * Reduce object to the form needed for API request
	 *
	 * @return array
	 */
	public function toArray(): array
	{
		if (! is_array($this->data)) {
			$this->resetData();
		}
		return $this->data;
	}


	/**
	 * Set a value for one merge field
	 *
	 * @param string $mergeTag Merge field to set value of
	 * @param mixed $value Value to set
	 *
	 * @return SubscribeMergeVars
	 */
	public function setMergeValue(string $mergeTag, $value) : SubscribeMergeVars
	{
		if (! is_array($this->data)) {
			$this->resetData();
		}
		if (array_key_exists($mergeTag, $this->data)) {
			$this->data[$mergeTag] = $value;
		}
		return $this;
	}

	/**
	 * Set all values for all merge fields
	 *
	 * @param array $values Array of values, keyed by tag or merge Id
	 *
	 * @return SubscribeMergeVars
	 * @throws \something\Mailchimp\Exception
	 */
	public function setMergeValues(array $values) : SubscribeMergeVars
	{
		foreach ($values as $mergeTagOrId => $value) {
			if (! $mergeVar = $this->getMergeVars()->findMergeVarByTag($mergeTagOrId)) {
				try {
					$mergeVar = $this->getMergeVars()->getMergeVar($mergeTagOrId);
				} catch (\Exception $e) {
					continue;
				}
			}


			if ($mergeVar) {
				$this->setMergeValue($mergeVar->getTag(), $value);
			}
		}

		return $this;
	}


	/**
	 * @param MergeVars $mergeVars
	 *
	 * @return SubscribeMergeVars
	 */
	public function setMergeVars(MergeVars $mergeVars): SubscribeMergeVars
	{
		$this->mergeVars = $mergeVars;
		return $this;
	}

	protected function resetData()
	{
		if (! empty($this->mergeVars->getMergeVars())) {
			/** @var MergeVar $mergeVar */
			foreach ($this->mergeVars->getMergeVars() as $mergeVar) {
				$this->data[$mergeVar->getTag()] = $mergeVar->getDefaultValue();
			}
		}
	}
}
