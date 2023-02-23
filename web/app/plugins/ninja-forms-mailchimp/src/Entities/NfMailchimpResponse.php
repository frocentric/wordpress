<?php

namespace NFMailchimp\NinjaForms\Mailchimp\Entities;

// REST API
use NFMailchimp\EmailCRM\RestApi\Contracts\ResponseContract;
use NFMailchimp\EmailCRM\RestApi\Contracts\HttpContract;
use NFMailchimp\EmailCRM\RestApi\Contracts\HttpResponseContract;

/**
 * NF Mailchimp Standardized Response
 *
 * Ensures known data structure is returned from any NF Mailchimp request
 *
 */
class NfMailchimpResponse implements ResponseContract
{

	/**
	 * Response data from request
	 * @var array
	 */
	protected $data = [];

	/**
	 *
	 * @var array
	 */
	protected $headers = [];

	/**
	 * HTTP status or equivalent
	 * @var int
	 */
	protected $status = 0;

	/**
	 *
	 * @var string
	 */
	protected $httpMethod = '';

	/**
	 * Constructs NF Mailchimp response from array
	 * @param $items
	 * @return HttpResponseContract
	 */
	public static function fromArray($items): HttpResponseContract
	{

				$obj = new static();
				
		if (isset($items['data']) && is_array($items['data'])) {
			$obj->setData($items['data']);
		}

		if (isset($items['headers']) && is_array($items['headers'])) {
			$obj->setHeaders($items['headers']);
		}

		if (isset($items['status']) && is_int($items['status'])) {
			$obj->setStatus($items['status']);
		}

		if (isset($items['httpMethod']) && is_string($items['httpMethod'])) {
			$obj->setHttpMethod($items['httpMethod']);
		}
		
		return $obj;
	}

	/**

	 * Get response data
	 *
	 * @return array
	 */
	public function getData(): array
	{
		return $this->data;
	}

	/**
	 * Get response headers
	 *
	 * @return array
	 */
	public function getHeaders(): array
	{
		return $this->headers;
	}

	/**
	 * Get the HTTP status code
	 *
	 * @return int
	 */
	public function getStatus(): int
	{
		return $this->status;
	}

	/**
	 * Set the HTTP status code
	 *
	 * @param int $code
	 *
	 * @return HttpResponseContract
	 */
	public function setStatus(int $code): HttpResponseContract
	{
		$this->status = $code;
		return $this;
	}

	/**
	 * Set the request headers
	 *
	 * @param array $headers
	 *
	 * @return HttpContract
	 */
	public function setHeaders(array $headers): HttpContract
	{
		$this->headers = $headers;

		return $this;
	}

	/**
	 * Set the response body data
	 *
	 * @param array $data
	 *
	 * @return HttpResponseContract
	 */
	public function setData(array $data): HttpResponseContract
	{

		$this->data = $data;

		return $this;
	}

	/**
	 * Get the HTTP method for the response
	 *
	 * @return string
	 */
	public function getHttpMethod(): string
	{

		return $this->httpMethod;
	}

	/**
	 * Set the HTTP method for the response
	 *
	 * @param string $method
	 *
	 * @return HttpResponseContract
	 */
	public function setHttpMethod(string $method)
	{

		$this->httpMethod = $method;

		return $this;
	}
}
