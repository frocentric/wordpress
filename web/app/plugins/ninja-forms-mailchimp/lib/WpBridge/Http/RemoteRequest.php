<?php

namespace NFMailchimp\EmailCRM\WpBridge\Http;

// Shared
use NFMailchimp\EmailCRM\Shared\Contracts\RemoteRequestContract;
use NFMailchimp\EmailCRM\Shared\Entities\HandledResponse;

/**
 * Make an HTTP request using wp_remote_request
 *
 */
class RemoteRequest implements RemoteRequestContract
{

	/**
	 * URL for request
	 * @var string
	 */
	protected $url = '';
	
	/**
	 * Optional headers to be sent
	 *
	 * Null default in case empty array is required (?)
	 * @var array|null
	 */
	protected $headers = null;
	
	/**
	 * HTTP settings for request
	 *
	 * @var array
	 * @see $allowedArgs
	 */
	protected $httpArgs = [];
	
	/**
	 * Query string requests as key-value pairs
	 * @var array
	 */
	protected $queryArgs = [];
	
	/**
	 * Body of request
	 *
	 * @var mixed|null
	 */
	protected $body = null;
	
	/**
	 * Allowed arguments that can be used in HTTP request
	 *
	 * @var array
	 */
	protected $allowedArgs = [
		'timeout', 'redirection', 'httpversion', 'sslverify', 'method'
	];
	
	/**
	 * Response from request
	 *
	 * @var HandledResponse
	 */
	protected $handledResponse;

	/**
	 * Make an HTTP request to return a response array
	 */
	public function __construct()
	{
		$this->setDefaultHttpArgs();
	}

	/**
	 * Set HTTP request URL
	 *
	 * @param string $url
	 * @return \NFMailchimp\EmailCRM\AmoCrm\Handlers\RemoteRequest
	 */
	public function setUrl(string $url): RemoteRequestContract
	{
		$this->url = $url;
		return $this;
	}

	/**
	 * Set an HTTP argument
	 *
	 * @param string $arg
	 * @param mixed $value
	 * @return \NFMailchimp\EmailCRM\AmoCrm\Handlers\RemoteRequest
	 */
	public function setHttpArg(string $arg, $value): RemoteRequestContract
	{

		if (in_array($arg, $this->allowedArgs)) {
			$this->httpArgs[$arg] = $value;
		}

		return $this;
	}

	/**
	 * Set an HTTP argument
	 *
	 * @param string $arg
	 * @param mixed $value
	 * @return \NFMailchimp\EmailCRM\AmoCrm\Handlers\RemoteRequest
	 */
	public function addQueryArg(string $arg, $value): RemoteRequestContract
	{

		$this->queryArgs[$arg] = $value;

		return $this;
	}

	/**
	 * Set HTTP request body
	 * @param mixed $body
	 * @return \NFMailchimp\EmailCRM\AmoCrm\Handlers\RemoteRequest
	 */
	public function setBody($body): RemoteRequestContract
	{
		$this->body = $body;
		return $this;
	}

	/**
	 * Set an HTTP header parameter
	 * @param string $arg
	 * @param mixed $value
	 * @return \NFMailchimp\EmailCRM\AmoCrm\Handlers\RemoteRequest
	 */
	public function setHeaderParameter($arg, $value): RemoteRequestContract
	{
		$this->headers[$arg] = $value;
		return $this;
	}

	protected function finalizeRequest()
	{
		if (!is_null($this->headers)) {
			$this->httpArgs['headers'] = $this->headers;
		}
		if (!is_null($this->body)) {
			$this->httpArgs['body'] = $this->body;
		}

		if (!empty($this->queryArgs)) {
			$this->url = add_query_arg($this->queryArgs, $this->url);
		}
	}
	/**
	 *
	 * @return HandledResponse
	 */
	public function handle()
	{
		$this->finalizeRequest();

				$this->handledResponse= new HandledResponse();
				
		$rawResponse = wp_remote_request($this->url, $this->httpArgs);

		if (is_wp_error($rawResponse)) {
			$this->handledResponse->setIsWpError(true);
			$this->handledResponse->setIsSuccessful(false);
			$this->handledResponse->setErrorMessages($rawResponse->get_error_messages());
		} else {
			$this->handledResponse->setResponseBody($rawResponse['body']);
		}

		$this->handledResponse->setTimestamp(time());

		return $this->handledResponse;
	}

	/**
	 * Set default arguments
	 */
	protected function setDefaultHttpArgs()
	{
		$this->httpArgs = [
			'timeout' => 45,
			'redirection' => 0,
			'httpversion' => '1.0',
			'sslverify' => true,
			'method' => 'GET'
		];
	}
}
