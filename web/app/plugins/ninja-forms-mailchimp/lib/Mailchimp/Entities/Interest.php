<?php

namespace NFMailchimp\EmailCRM\Mailchimp\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;

/**
 * Describes one Mailchimp Interest within a given Interest Category
 */
class Interest extends MailChimpEntity
{

	/**
	 * ID of the Interest category to which interest belongs
	 * @var string
	 */
	protected $category_id;


	/**
	 * ID of the given interest
	 * @var string
	 */
	protected $id;

	/**
	 * Name of the Interest
	 * @var string
	 */
	protected $name;

	/** @var string */
	protected $subscriber_count;

	/** @var int */
	protected $display_order;

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
	public function setId(string $id): Interest
	{
		$this->id = $id;
		return $this;
	}

		/**
		 * Get interest category id
		 * @return string
		 */
	public function getCategoryId():string
	{
		return isset($this->category_id)?$this->category_id:'';
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
