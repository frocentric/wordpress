<?php


namespace NFMailchimp\NinjaForms\Mailchimp\Handlers;

// REST API
use NFMailchimp\EmailCRM\RestApi\Contracts\AuthorizeRequestContract;
use NFMailchimp\EmailCRM\RestApi\Contracts\RequestContract;

/**
 * AuthorizeRequestContract that always accepts the request.
 */
class AuthorizesTrue implements AuthorizeRequestContract
{

		/**
		 * Authorize request contract
		 *
		 * Always returns true; use for testing
		 * @param RequestContract $request
		 * @return bool
		 */
	public function authorizeRequest(RequestContract $request): bool
	{
		return  true;
	}
}
