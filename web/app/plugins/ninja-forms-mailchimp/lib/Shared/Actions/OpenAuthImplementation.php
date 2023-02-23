<?php

namespace NFMailchimp\EmailCRM\Shared\Actions;

use NFMailchimp\EmailCRM\Shard\Contracts\OpenAuthImplementationContract;
use NFMailchimp\EmailCRM\Shard\Contracts\RemoteRequestInterface;
use NFMailchimp\EmailCRM\Shared\Entities\OpenAuthCredentials;
use NFMailchimp\EmailCRM\Shared\Entities\HandledResponse;

/**
 * Makes OpenAuthorization requests meeting OAuth 2 standards
 *
 * Requests for authorization following OAuth 2 follows standards that can be
 * shared among all integrating plugins that require that standard.  This class
 *
 * This class structures the requests per the OAuth standard and standardizes
 * the response into a shared handledResponse entity.  Each integration must
 * provide the incoming credentials and endpoint URLs with which this class
 * will make the authorization request.  The integration must also provide
 * a RemoteRequest object to provide the means with which the requests are
 * sent and received.
 */
class OpenAuthImplementation implements OpenAuthImplementationContract
{

	/**
	 * HTTP request object with methods similar to Wordpress' HTTP API
	 * @var RemoteRequestInterface
	 */
	protected $remoteRequest;

	/**
	 * Standardized entity containing OpenAuthorization incoming credentials
	 * @var OpenAuthCredentials
	 */
	protected $openAuthCredentials;

	/**
	 * Endpoint specified by the external API for making AuthCode authorizations
	 * @var string
	 */
	protected $grantTypeAuthCodeUrl;

	/**
	 * Endpoint specified by the external API for making RefreshToken authorizations
	 * @var string
	 */
	protected $grantTypeRefreshTokenUrl;

	/**
	 * Standardized response entity communicating results
	 * @var HandledResponse
	 */
	protected $handledResponse;

		/**
		 * Construct implementation class with RemoteRequest object for communication
		 * @param RemoteRequestInterface $remoteRequest
		 */
	public function __construct(RemoteRequestInterface $remoteRequest)
	{
		$this->remoteRequest = $remoteRequest;
	}

	/**
	 * Set OpenAuth credentials with which to generate authorization
	 *
	 * @param OpenAuthCredentials $openAuthCredentials
	 * @return \NFMailchimp\EmailCRM\AmoCrm\Abstracts\OpenAuthenticatorContract
	 */
	public function setCredentials(OpenAuthCredentials $openAuthCredentials): OpenAuthImplementationContract
	{
		$this->openAuthCredentials = $openAuthCredentials;
		return $this;
	}

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
	public function getCredentials(): OpenAuthCredentials
	{
		return $this->openAuthCredentials;
	}

	/**
	 * Set URL for making grant_type = authorization_code request
	 *
	 * @param string $grantTypeAuthCodeUrl
	 * @return OpenAuthenticatorContract
	 */
	public function setGrantTypeAuthCodeUrl(string $grantTypeAuthCodeUrl): OpenAuthImplementationContract
	{
		$this->grantTypeAuthCodeUrl = $grantTypeAuthCodeUrl;
		return $this;
	}

	/**
	 * Set URL for making grant_type = refresh_token request
	 *
	 * @param string $grantTypeRefreshTokenUrl
	 * @return OpenAuthenticatorContract
	 */
	public function setGrantTypeRefreshTokenUrl(string $grantTypeRefreshTokenUrl): OpenAuthImplementationContract
	{
		$this->grantTypeRefreshTokenUrl = $grantTypeRefreshTokenUrl;
		return $this;
	}

	/**
	 * Makes a grant_type = Authorization request per OAuth standards
	 * @return HandledResponse
	 */
	public function authorizeFromAuthorizationCode(): HandledResponse
	{
		$this->setRequestSettings();
		$this->remoteRequest->setUrl($this->grantTypeAuthCodeUrl);
		$body = $this->constructGrantTypeAuthCodeBody();

		$this->remoteRequest->setBody(json_encode($body));

		$incomingResponseData = $this->remoteRequest->handle();

		$handledResponse = $this->evaluateResponseData($incomingResponseData);

		return $handledResponse;
	}

	/**
	 * Makes a grant_type = refresh_token request per OAuth standards
	 * @return HandledResponse
	 */
	public function authorizeFromRefreshToken(): HandledResponse
	{
		$this->setRequestSettings();

		$this->remoteRequest->setUrl($this->grantTypeRefreshTokenUrl);

		$body = $this->constructGrantTypeRefreshTokenBody();


		$this->remoteRequest->setBody(json_encode($body));

		$incomingResponseData = $this->remoteRequest->handle();


		$handledResponse = $this->evaluateResponseData($incomingResponseData);

		return $handledResponse;
	}

	/**
	 * Evaluates the response from endpoint to put data in known locations
		 *
		 * **Note** that the credentials are, per OAuth specification, modified
		 * in the making  of a authorization request.
		 * * An authorization token can only be used once
		 * * A refresh token is only used once but, when making an authorization
		 * request, a new token is returned along with the access_token that is
		 * intended for immediate authorization.
		 *
	 * @param HandledResponse $handledResponse
	 * @return HandledResponse
	 */
	protected function evaluateResponseData(HandledResponse $handledResponse): HandledResponse
	{

		if ($handledResponse->isSuccessful()) {
			$body = json_decode($handledResponse->getResponseBody(), true);

			if (isset($body['access_token']) && isset($body['refresh_token'])) {
				$this->openAuthCredentials->setAccessToken($body['access_token']);
				$this->openAuthCredentials->setRefreshToken($body['refresh_token']);

				$handledResponse->setResponseBody(json_encode([
					"accessToken" => $this->openAuthCredentials->getAccessToken(),
					"refreshToken" => $this->openAuthCredentials->getRefreshToken()
						]));
			}
		}
		return $handledResponse;
	}

	/**
	 * Construct request body per OAuth specifications
	 * @return array
	 */
	protected function constructGrantTypeAuthCodeBody(): array
	{

		$body = [
			'client_id' => $this->openAuthCredentials->getClientId(),
			'client_secret' => $this->openAuthCredentials->getClientSecret(),
			'grant_type' => 'authorization_code',
			'code' => $this->openAuthCredentials->getAuthorizationCode(),
			'redirect_uri' => $this->openAuthCredentials->getRedirectUri()
		];

		return $body;
	}

	/**
	 * Construct request body per OAuth specifications
	 * @return array
	 */
	protected function constructGrantTypeRefreshTokenBody(): array
	{
		$body = [
			'client_id' => $this->openAuthCredentials->getClientId(),
			'client_secret' => $this->openAuthCredentials->getClientSecret(),
			'grant_type' => 'refresh_token',
			'refresh_token' => $this->openAuthCredentials->getRefreshToken(),
			'redirect_uri' => $this->openAuthCredentials->getRedirectUri()
		];

		return $body;
	}

	/**
	 * Set HTTP settings required for making HTTP request
	 */
	protected function setRequestSettings()
	{

		$this->remoteRequest->setHeaderParameter('Content-Type', 'application/json');
		$this->remoteRequest->setHttpArg('method', 'POST');
	}
}
