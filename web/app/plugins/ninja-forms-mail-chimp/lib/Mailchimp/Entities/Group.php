<?php


namespace NFMailchimp\EmailCRM\Mailchimp\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;

/**
 * Describes one interest group.
 */
class Group extends MailChimpEntity
{

	/**
	 * @var string
	 */
	protected $groupId;

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var bool
	 */
	protected $shouldJoin;

	/** @inheritDoc */
	public static function fromArray(array $items): SimpleEntity
	{

		$obj = parent::fromArray($items);
		if (isset($items['id']) && ! isset($items['groupId'])) {
			$obj->setGroupId((string) $items['id']);
		}
		if (isset($items['groupId'])) {
			$obj->setGroupId((string) $items['groupId']);
		}
		return $obj;
	}


	/**
	 * @return string
	 */
	public function getGroupId(): string
	{
		return is_string($this->groupId) ? $this->groupId : '';
	}

	/**
	 * @param string $groupId
	 *
	 * @return Group
	 */
	public function setGroupId(string $groupId): Group
	{
		$this->groupId = $groupId;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return is_string($this->title) ? $this->title : '';
	}

	/**
	 * @param string $title
	 *
	 * @return Group
	 */
	public function setTitle(string $title): Group
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return is_string($this->type) ? $this->type : '';
	}

	/**
	 * @param string $type
	 *
	 * @return Group
	 */
	public function setType(string $type): Group
	{
		$this->type = $type;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function getShouldJoin(): bool
	{
		//if not set, default is false
		return (bool)$this->shouldJoin;
	}

	/**
	 * @param bool $shouldJoin
	 *
	 * @return Group
	 */
	public function setShouldJoin(bool $shouldJoin): Group
	{
		$this->shouldJoin = $shouldJoin;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->getGroupId();
	}

	/**
	 * @param string $id
	 * @return Group
	 */
	public function setId(string $id): Group
	{
		return $this->setGroupId($id);
	}
}
