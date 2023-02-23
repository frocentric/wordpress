<?php


namespace NFMailchimp\EmailCRM\Shared\Contracts;

/**
 * Contract describing a system that stores arrays in database or elsewhere
 */
interface ArrayStore
{

	/**
	 * The name of the key -- WordPress option name, database identifier, etc. -- used to store data
	 *
	 * @return string
	 */
	public function getKey(): string;

	/**
	 * Get saved data
	 *
	 * @return array
	 */
	public function getData() : array;

	/**
	 * Get the saved data
	 *
	 * @param array $data Data to save
	 */
	public function saveData(array $data);
}
