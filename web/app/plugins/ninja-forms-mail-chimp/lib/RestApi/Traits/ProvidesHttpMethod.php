<?php


namespace NFMailchimp\EmailCRM\RestApi\Traits;

use NFMailchimp\EmailCRM\RestApi\Contracts\HttpContract;

trait ProvidesHttpMethod
{

	/**
	 * @var string
	 */
	protected $httpMethod;
	/**
	 * Set the HTTP method for the request or response
	 *
	 * @return string
	 */
	public function getHttpMethod() : string
	{
		return is_string($this->httpMethod) ? $this->httpMethod : 'GET';
	}

	/**
	 * Set the HTTP method for the request or response
	 *
	 * @param string $method
	 *
	 * @return HttpContract
	 */
	public function setHttpMethod(string  $method) : HttpContract
	{
		$this->httpMethod = $method;
		return  $this;
	}
}
