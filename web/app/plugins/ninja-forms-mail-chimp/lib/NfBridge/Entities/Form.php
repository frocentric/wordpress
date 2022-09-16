<?php

namespace NFMailchimp\EmailCRM\NfBridge\Entities;

use NFMailchimp\EmailCRM\Shared\ArrayLike;
use NFMailchimp\EmailCRM\Shared\Contracts\FormContract;

/**
 * Form object enforcing contract for NF Form object
 *
 */
class Form extends ArrayLike implements FormContract
{

	/**
	 * Get the name of the form
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return is_string($this->offsetGet('name')) ? $this->offsetGet('name') : '';
	}


	public function setId(string  $id)
	{
		$this->offsetSet('id', $id);
	}

	/**
	 * Get the id of the form
	 *
	 * @return string
	 */
	public function getId() : string
	{
		$id = $this->offsetGet('id');
		return is_string($id) || is_numeric($id) ? (string) $id : '';
	}
}
