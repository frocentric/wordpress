<?php

namespace NFMailchimp\EmailCRM\Mailchimp\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;
use NFMailchimp\EmailCRM\Mailchimp\Entities\Instruction;

/**
 * Describes a collection of instructions for requests thru Mailchimp API
 */
class Instructions extends SimpleEntity
{

	/**
	 * @var Instruction[]
	 */
	protected $instructions;

	public function __construct()
	{
		$this->instructions = [];
	}

		/**
		 * @inheritDoc
		 */
	   
	public static function fromArray(array $items): SimpleEntity
	{
		$obj = new static();
		foreach ($items as $list) {
			if (!is_array($list)) {
				if (is_a($list, Instruction::class)) {
					$obj->addInstruction($list);
					continue;
				} else {
					$list = (array) $list;
				}
			}

			$obj->addInstruction(Instruction::fromArray($list));
		}
		return $obj;
	}

	/**
	 * Add an Instruction to collection
	 *
	 * @param Instruction $instruction
	 *
	 * @return Instructions
	 */
	public function addInstruction(Instruction $instruction): Instructions
	{
		$this->instructions[] = $instruction;
		return $this;
	}


	/**
	 * Get all Instructions in collection
	 *
	 * @return Instruction[]
	 */
	public function getInstructions(): array
	{
		return $this->instructions;
	}
}
