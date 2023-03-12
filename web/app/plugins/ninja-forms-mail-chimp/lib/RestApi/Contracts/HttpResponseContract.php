<?php


namespace NFMailchimp\EmailCRM\RestApi\Contracts;

/**
 * Represents HTTP responses
 */
interface HttpResponseContract extends HttpContract
{
	/**
	 * @param $items
	 *
	 * @return HttpResponseContract
	 */
	public static function fromArray($items) : HttpResponseContract;
	/**
	 * Get response data
	 *
	 * @return array
	 */
	public function getData(): array;

	/**
	 * Get response headers
	 *
	 * @return array
	 */
	public function getHeaders(): array;


	/**
	 * Get the HTTP status code
	 *
	 * @return int
	 */
	public function getStatus(): int;

	/**
	 * Set the HTTP status code
	 *
	 * @param int $code
	 *
	 * @return HttpResponseContract
	 */
	public function setStatus(int $code): HttpResponseContract;

	/**
	 * Set the request headers
	 *
	 * @param array $headers
	 *
	 * @return HttpContract
	 */
	public function setHeaders(array $headers): HttpContract;

	/**
	 * Set the response body data
	 *
	 * @param array $data
	 *
	 * @return HttpResponseContract
	 */
	public function setData(array $data): HttpResponseContract;

	/**
	 * Set the HTTP method for the request or response
	 *
	 * @return string
	 */
	public function getHttpMethod() : string;

	/**
	 * Set the HTTP method for the request or response
	 *
	 * @param string $method
	 *
	 * @return HttpResponseContract
	 */
	public function setHttpMethod(string  $method);
}
