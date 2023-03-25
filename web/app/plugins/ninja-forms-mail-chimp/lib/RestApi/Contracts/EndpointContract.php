<?php


namespace NFMailchimp\EmailCRM\RestApi\Contracts;

use NFMailchimp\EmailCRM\RestApi\Exception;

/**
 * Represents WP REST API endpoints
 */
interface EndpointContract extends HttpContract, AuthorizeRequestContract
{

	/**
	 * Get route URI
	 *
	 * @return string
	 */
	public function getUri(): string;

	/**
	 * Get route arguments
	 *
	 * @return array
	 */
	public function getArgs() : array;

	/**
	 * Get HTTP method for endpoint
	 *
	 * @return string
	 */
	public function getHttpMethod(): string;

	/**
	 * @param string $uri
	 *
	 * @return EndpointContract
	 */
	public function setUri(string $uri): EndpointContract;

	/**
	 * @param array $args
	 *
	 * @return EndpointContract
	 */
	public function setArgs(array $args): EndpointContract;

	/**
	 * @param string $httpMethod
	 *
	 * @return HttpContract|EndpointContract
	 */
	public function setHttpMethod(string $httpMethod): HttpContract;

	/**
	 * Handle request
	 *
	 * @param RequestContract $request
	 *
	 * @return ResponseContract
	 */
	public function handleRequest(RequestContract $request) : ResponseContract;

	/**
	 * Get CSFR/JWT token string from request
	 *
	 * @param RequestContract $request
	 *
	 * @return TokenContract
	 */
	public function getToken(RequestContract $request) : string;
}
