<?php

namespace NFMailchimp\EmailCRM\Mailchimp\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;
use NFMailchimp\EmailCRM\Mailchimp\Entities\Segment;

/**
 * Describes a collection of Mailchimp Segments, including tags
 */
class Segments extends MailChimpEntity
{

	/**
	 * @var Segment[]
	 */
	protected $segments;

	public function __construct()
	{
		$this->listId = '';
		$this->segments = [];
	}

	public static function fromArray(array $items): SimpleEntity
	{
		$obj = new static();
		// Entity structure else response structure
		if (isset($items['segments'])) {
			$array = $items['segments'];
		} else {
			$array = $items;
		}

		foreach ($array as $list) {
			if (!is_array($list)) {
				if (is_a($list, Segment::class)) {
					$obj->addSegment($list);
					continue;
				} else {
					$list = (array) $list;
				}
			}

			$obj->addSegment(Segment::fromArray($list));
		}

		// Entity structure contains listId
		if (isset($items['listId'])) {
			$obj->setListId($items['listId']);
		}


		return $obj;
	}

	/**
	 * Add a Segment to collection
	 *
	 * @param Segment $segment
	 *
	 * @return Segments
	 */
	public function addSegment(Segment $segment): Segments
	{
		$this->segments[$segment->getName()] = $segment;
		return $this;
	}

	/**
	 * Add a Tag Segment to collection; keyed on NAME, not Id
	 *
	 * @param Segment $segment
	 *
	 * @return Segments
	 */
	public function addTag(Segment $segment): Segments
	{
		$this->segments[$segment->getName()] = $segment;
		return $this;
	}

	/**
	 * Get a Segment from collection
	 *
	 * @param string $name
	 *
	 * @return Segment
	 * @throws Exception
	 */
	public function getSegment(string $name): Segment
	{
		if (!isset($this->segments[$name])) {
			throw new Exception();
		}
		return $this->segments[$name];
	}

	/**
	 * Get all Segments in collection
	 *
	 * @return Segment[]
	 */
	public function getSegments(): array
	{
		return $this->segments;
	}

	/**
	 * Returns boolean if segment exists
	 * @param string $name
	 * @return bool
	 */
	public function hasSegment($name): bool
	{
		return isset($this->getSegments()[$name]);
	}

	/**
	 * Return all tags in the collection, omitting 'saved' segments with options
	 * @return Segments
	 */
	public function getTags(): Segments
	{
		$obj = new static();

		foreach ($this->segments as $segment) {
			if ($segment->isTag()) {
				$obj->addTag($segment);
			}
		}

		$obj->setListId($this->listId);
		
		return $obj;
	}
}
