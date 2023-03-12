<?php

namespace NFMailchimp\EmailCRM\NfBridge\Actions;

/**
 * Handle NF Process Data
 *
 * Heavily based on NF_Stripe_Checkout_FormData
 *
 */
class HandleProcessData
{

	/**
	 * NF Process Data passed to action
	 * @var array
	 */
	protected $data;

	/**
	 *
	 * @var string
	 */
	protected $actionKey='';
	
	/**
	 * Incoming NF Process data
	 * @param array $data
	 * @param string $actionKey
	 */
	public function __construct(array $data, string $actionKey)
	{
		$this->data = $data;
		$this->actionKey = $actionKey;
	}

	/**
	 * Add a form error
	 * @param string $message
	 * @return \NFMailchimp\EmailCRM\NfBridge\Actions\HandleProcessData
	 */
	public function addFormError(string $message): HandleProcessData
	{
		$this->data['errors']['form'][$this->actionKey] = $message;
		
		return $this;
	}


	/**
	 * Append ResponseData array
	 * @param array $responseData
	 */
	public function appendResponseData($responseData): HandleProcessData
	{
		$this->data['extra'][$this->actionKey]['responseData'][]=$responseData;
		
		return $this;
	}


	/**
	 * Return process $data array
	 * @return array
	 */
	public function toArray():array
	{
		return $this->data;
	}
}
