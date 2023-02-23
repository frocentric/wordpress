<?php

namespace NFMailchimp\EmailCRM\Mailchimp\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;

/**
 * Describes one Mailchimp Segment, which includes tags
 */
class Segment extends MailChimpEntity
{

	/**
	 * Id of the segment
	 * @var int
	 */
	protected $id;

	/**
	 * Name of the segment
	 * @var string
	 */
	protected $name;

	/**
	 * Member count
	 * @var int
	 */
	protected $member_count;

	/**
	 * Type of the segment (tag is of type static)
	 * @var string
	 */
	protected $type;

	/**
	 * @return string
	 */
	public function getId(): string
	{
		return $this->id;
	}

	/**
	 * Get name of interest
	 * @return string
	 */
	public function getName(): string
	{
		return isset($this->name) ? (string) $this->name : '';
	}

	/**
	 * @param string $id
	 *
	 * @return InterestCategory
	 */
	public function setId(string $id): Segment
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * Is this segment a tag
	 * @return bool
	 */
	public function isTag(): bool
	{
		if( 'static' === $this->type || 'saved'===$this->type){
			$return = true;
		}else{
			$return = false;
		}
		return $return;
	}

	/**
	 * @inheritdoc
	 */
	public static function fromArray(array $items): SimpleEntity
	{
		$obj = new static();
		foreach ($items as $property => $value) {
			if (null !== $value) {
				$obj = $obj->__set($property, $value);
			}
		}
		if (isset($items['list_id'])) {
			$obj->setListId($items['list_id']);
		}
		return $obj;
	}
}
