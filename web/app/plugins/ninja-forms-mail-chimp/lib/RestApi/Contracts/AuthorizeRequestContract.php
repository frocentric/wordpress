<?php


namespace NFMailchimp\EmailCRM\RestApi\Contracts;

use NFMailchimp\EmailCRM\RestApi\Exception;

/**
 * Interface AuthorizesRequests
 *
 * Interface for use with Endpoints for decoupling authorization logic from WordPress REST API/ non-WordPress APIs
 */
interface AuthorizeRequestContract
{
	/**
	 * Is request authorized or not?
	 *
	 * @param RequestContract $request
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function authorizeRequest(RequestContract $request) : bool;
}
