<?php

namespace NFMailchimp\EmailCRM\Shared\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;

/**
 * Entity to accept and provide data for Open Authorization protocol
 */
class OpenAuthCredentials extends SimpleEntity
{

	/**
	 * Client Id
	 * @var string
	 */
	protected $clientId = '';

	/**
	 * Client Secret
	 * @var string
	 */
	protected $clientSecret = '';

	/**
	 * Authorization Code
	 *
	 * Optional - used for grant_type authorization_code
	 * @var string
	 */
	protected $authorizationCode = '';

	/**
	 * Redirect URI
	 *
	 * Optional - only some implementations use this
	 *
	 * @var string
	 */
	protected $redirectUri='';

	/**
	 * Refresh token
	 *
	 * Optional - must be generated during authorization process
	 *
	 * @var string
	 */
	protected $refreshToken = '';

	/**
	 * Access token
	 *
	 * Optional - must be generated during authorization process
	 *
	 * @var string
	 */
	protected $accessToken = '';

	/**
	 * Set client id
	 *
	 * @param string $clientId
	 * @return SimpleEntity
	 */
	public function setClientId(string $clientId): SimpleEntity
	{
		$this->clientId = $clientId;
		return $this;
	}

	/**
	 * Get client id
	 * @return string
	 */
	public function getClientId(): string
	{
		return $this->clientId;
	}

	/**
	 * Set client secret
	 *
	 * @param string $clientSecret
	 * @return SimpleEntity
	 */
	public function setClientSecret(string $clientSecret): SimpleEntity
	{
		$this->clientSecret = $clientSecret;
		return $this;
	}

	/**
	 * Get client secret
	 *
	 * @return string
	 */
	public function getClientSecret(): string
	{
		return $this->clientSecret;
	}

	/**
	 * Set authorization code
	 *
	 * @param string|null $authorizationCode
	 * @return SimpleEntity
	 */
	public function setAuthorizationCode(?string $authorizationCode): SimpleEntity
	{
		$this->authorizationCode = $authorizationCode;
		return $this;
	}

	/**
	 * Get authorization code
	 *
	 * @return string
	 */
	public function getAuthorizationCode(): string
	{
		return (isset($this->authorizationCode)&& !is_null($this->authorizationCode))?$this->authorizationCode:'';
	}

	/**
	 * Set redirect URI
	 *
	 * @param string|null $redirectUri
	 * @return SimpleEntity
	 */
	public function setRedirectUri(?string $redirectUri): SimpleEntity
	{
		$this->redirectUri = $redirectUri;
		return $this;
	}

	/**
	 * Get redirect URI
	 *
	 * @return string
	 */
	public function getRedirectUri(): string
	{
		return (isset($this->redirectUri)&& !is_null($this->redirectUri))?$this->redirectUri:'';
	}

	/**
	 * Set refresh token
	 *
	 * @param string|null $refreshToken
	 * @return SimpleEntity
	 */
	public function setRefreshToken(?string $refreshToken): SimpleEntity
	{
		$this->refreshToken = $refreshToken;
		return $this;
	}

	/**
	 * Get refresh token
	 *
	 * @return string
	 */
	public function getRefreshToken(): string
	{
		return (isset($this->refreshToken)&& !is_null($this->refreshToken))?$this->refreshToken:'';
	}

	/**
	 * Set access token
	 *
	 * @param string|null $accesstoken
	 * @return SimpleEntity
	 */
	public function setAccessToken(?string $accesstoken): SimpleEntity
	{
		$this->accessToken = $accesstoken;
		return $this;
	}

	/**
	 * Get access token
	 *
	 * @return string
	 */
	public function getAccessToken(): string
	{
		return (isset($this->accessToken)&& !is_null($this->accessToken))?$this->accessToken:'';
	}
}
