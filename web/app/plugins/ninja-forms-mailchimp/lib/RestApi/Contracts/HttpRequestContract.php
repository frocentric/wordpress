<?php


namespace NFMailchimp\EmailCRM\RestApi\Contracts;

/**
 * Represents any type of HTTP request
 */
interface HttpRequestContract extends HttpContract
{
	/**
	 * Get parameter from request
	 *
	 * @param string $paramName
	 *
	 * @return mixed
	 */
	public function getParam(string $paramName);

	/**
	 * Get all params of request
	 *
	 * @return array
	 */
	public function getParams(): array;

	/**
	 * Set parameter in request
	 *
	 * @param string $paramName
	 * @param mixed $paramValue
	 *
	 * @return HttpRequestContract
	 */
	public function setParam(string $paramName, $paramValue): HttpRequestContract;

	/**
	 * Set parameters of request
	 *
	 * @param array $params
	 *
	 * @return HttpRequestContract
	 */
	public function setParams(array $params): HttpRequestContract;

	/**
	 * Does request have param?
	 *
	 * @param string $paramName
	 *
	 * @return bool
	 */
	public function hasParam(string $paramName): bool;


	/**
	 * Get header from request
	 *
	 * @param string $headerName
	 *
	 * @return mixed
	 */
	public function getHeader(string $headerName);

	/**
	 * Get all headers
	 *
	 * @return array
	 */
	public function getHeaders(): array;

	/**
	 * Set header in request
	 *
	 * @param string $headerName
	 * @param mixed $headerValue
	 *
	 * @return HttpContract
	 */
	public function setHeader(string $headerName, $headerValue): HttpContract;

	/**
	 * Does request have header?
	 *
	 * @param string $headerName
	 *
	 * @return bool
	 */
	public function hasHeader(string $headerName): bool;


	/**
	 * Set the HTTP method for the request or response
	 *
	 * @return string
	 */
	public function getHttpMethod(): string;

	/**
	 * Set the HTTP method for the request or response
	 *
	 * @param string $method
	 *
	 * @return HttpContract
	 */
	public function setHttpMethod(string $method) : HttpContract;

	/**
	 * Set the request headers
	 *
	 * @param array $headers
	 *
	 * @return HttpContract
	 */
	public function setHeaders(array $headers): HttpContract;
}
