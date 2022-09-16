<?php

namespace NFMailchimp\EmailCRM\Shared\Contracts;

// Shared
use NFMailchimp\EmailCRM\Shared\Entities\OpenAuthCredentials;
use NFMailchimp\EmailCRM\Shard\Contracts\RemoteRequestInterface;
use NFMailchimp\EmailCRM\AmoCrm\Entities\HandledResponse;

/**
 * Provides OpenAuth access through the ApiModule to a specific user's account
 *
 * This class provides public methods to receive the incoming credentials,
 * which it uses to generate the access token needed for access. In
 * addition, it also generates and provides a refresh token, which the
 * integrating plugin must store, to be used in making the next request.
 *
 */
interface OpenAuthImplementationContract
{

	/**
	 * Set OpenAuth credentials with which to generate authorization
	 *
	 * @param OpenAuthCredentials $credentials
	 * @return OpenAuthenticatorContract
	 */
	public function setCredentials(OpenAuthCredentials $credentials): OpenAuthImplementationContract;
	
	/**
	 * Return OpenAuthCredentials
	 *
	 * The authentication process generates new refresh_ and access_ tokens.
	 *
	 * These values are updated in the originally supplied credentials.  The
	 * requesting class can retrieve the updated credentials, store the
	 * refresh token, and use the access token for immediate authorization.
	 *
	 * @return OpenAuthCredentials
	 */
	public function getCredentials(): OpenAuthCredentials;
	

	/**
	 * Set URL for making grant_type = authorization_code request
	 *
	 * @param string $grantTypeAuthCodeUrl
	 * @return OpenAuthenticatorContract
	 */
	public function setGrantTypeAuthCodeUrl(string $grantTypeAuthCodeUrl): OpenAuthImplementationContract;
	
	/**
	 * Set URL for making grant_type = refresh_token request
	 *
	 * @param string $grantTypeRefreshTokenUrl
	 * @return OpenAuthenticatorContract
	 */
	public function setGrantTypeRefreshTokenUrl(string $grantTypeRefreshTokenUrl): OpenAuthImplementationContract;
	

	/**
	 *
	 * @return HandledResponse
	 */
	public function authorizeFromAuthorizationCode(): HandledResponse;

	/**
	 *
	 * @return HandledResponse
	 */
	public function authorizeFromRefreshToken(): HandledResponse;
}
