<?php


namespace NFMailchimp\EmailCRM\Mailchimp\Entities;

/**
 * Describes which groups to add a subscriber to
 */
class SubscribeGroups
{

	/**
	 * @var Groups
	 */
	protected $groups;

	/**
	 * @var array
	 */
	protected $data;

	/**
	 * Reduce object to the form needed for API request
	 *
	 * @return array
	 */
	public function toArray()
	{
		if (!is_array($this->data)) {
			$this->resetData();
		}

		if (!empty($this->data)) {
			foreach ($this->groups->getGroups() as $group) {
				if (array_key_exists($this->data, $group->getGroupId())) {
					unset($this->data[$group->getGroupId()]);
				}
			}
		}

		return $this->data;
	}

	/**
	 * Set interest groups that are allowed
	 *
	 * @param Groups $groups
	 *
	 * @return $this
	 */
	public function setGroups(Groups $groups)
	{
		$this->groups = $groups;
		$this->resetData();
		return $this;
	}

	/**
	 * Set the value for one interest group - join or not
	 *
	 * @param string $groupId ID of group to join/ leave
	 * @param bool $join Set true to join or false to leave
	 *
	 * @return SubscribeGroups
	 */
	public function setGroupJoin(string $groupId, bool $join): SubscribeGroups
	{
		if (is_array($this->data) && array_key_exists($groupId, $this->data)) {
			$this->data[$groupId] = $join;
		}
		return $this;
	}

	/**
	 * Set the values for all interest groups - join or not
	 *
	 * @param array $options
	 *
	 * @return SubscribeGroups
	 *
	 * @throws Exception
	 */
	public function setGroupsJoins(array $options): SubscribeGroups
	{
		foreach ($options as $groupId => $join) {
			if ($this->groups->getGroup($groupId)) {
				if (is_array($join)) {
					foreach ($join as $interest) {
						$this->setGroupJoin($interest, true);
					}
				} else {
					if (!is_null($join)) {
						$this->setGroupJoin($join, true);
					}
				}
			}
		}

		return $this;
	}


	/**
	 * Reset join group data
	 */
	protected function resetData()
	{
		$this->data = [];
		if (isset($this->groups)) {
			/** @var Group $group */
			foreach ($this->groups->getGroups() as $group) {
				if (in_array($group->getType(), ['checkboxes', 'radio', 'dropdown'])) {
					try {
						$categories = $this->groups->getCategoriesForGroup($group->getId());
					} catch (Exception $e) {
						$categories = false;
					}
					if ($categories) {
						foreach ($categories->toArray() as $interests) {
							if (is_array($interests)) {
								foreach ($interests as $interest) {
									$this->data[$interest['id']] = false;
								}
							} elseif (is_string($interests)) {
								$this->data[$interests] = false;
							}
						}
					}
				}
			}
		}
	}
}
