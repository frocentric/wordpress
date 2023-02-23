<?php


namespace NFMailchimp\EmailCRM\Shared;

use NFMailchimp\EmailCRM\Shared\Contracts\ArrayStore;

class CommData
{

	/**
	 * @var ArrayStore
	 */
	protected $arrayStore;

	/**
	 * Array of communication data
	 * @var array
	 */
	protected $comm_data;

	public function __construct(ArrayStore $arrayStore)
	{
		$this->arrayStore = $arrayStore;
	}

	/**
	 * Retrieves existing CommData from db
	 *
	 * If incoming value is not an array, sets CommData as empty array
	 */
	public function initializeCommData()
	{

		$db_comm_data = $this->arrayStore->getData();

		if (!is_array($db_comm_data)) {
			$this->comm_data = array();
		} else {
			$this->comm_data = $db_comm_data;
		}
	}


	/**
	 * Removes existing data in key and initializes empty array
	 * @param string $key
	 */
	public function resetKey($key)
	{

		unset($this->comm_data[$key]);
	}

	/**
	 * Appends an entry to the indexed array in a given key
	 * @param string $key Comm Data key storing the data
	 * @param mixed $entry Value to be appended as array element
	 */
	public function append($key, $entry)
	{

		$this->comm_data[$key][]=$entry;
	}

	/**
	 * Replaces existing value with a new value
	 * @param string $key
	 * @param mixed $entry New value to be stored in key
	 */
	public function set($key, $entry)
	{

		$this->comm_data[$key]=$entry;
	}



	/**
	 * Stores CommData array in WP options table under given key
	 */
	public function storeCommData()
	{

		$this->arrayStore->saveData($this->comm_data);
	}
}
