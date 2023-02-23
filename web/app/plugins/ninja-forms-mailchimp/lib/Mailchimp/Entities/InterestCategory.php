<?php

namespace NFMailchimp\EmailCRM\Mailchimp\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;

/**
 * Describes one Mailchimp Interest Category
 */
class InterestCategory extends MailChimpEntity
{

	/** @var string */
	protected $id;

	/** @var string */
	protected $title;

	/** @var int */
	protected $display_order;

	/** @var string */
	protected $type;


	/**
	 * @return string
	 */
	public function getId(): string
	{
		return $this->id;
	}

	/**
	 * Get title of interest category
	 * @return string
	 */
	public function getTitle(): string
	{
		return isset($this->title) ? (string) $this->title : '';
	}

	/**
	 * @param string $id
	 *
	 * @return InterestCategory
	 */
	public function setId(string $id): InterestCategory
	{
		$this->id = $id;
		return $this;
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
