<?php


namespace NFMailchimp\EmailCRM\Shared\Contracts;

/**
 * Interface FormContract
 *
 * Contract that any class that represents a form SHOULD implement
 */
interface FormContract
{
	/**
	 * Get the name of the form
	 *
	 * @return string
	 */
	public function getName() : string;

	/**
	 * Get the id of the form
	 *
	 * @return string
	 */
	public function getId() : string;
}
