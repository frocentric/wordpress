<?php


namespace NFMailchimp\EmailCRM\WpBridge\RestApi;

use NFMailchimp\EmailCRM\RestApi\Contracts\EndpointContract as Endpoint;
use NFMailchimp\EmailCRM\RestApi\Contracts\RequestContract;
use NFMailchimp\EmailCRM\RestApi\Request;

/**
 * Create and endpoint on the WordPress REST API
 */
class CreateWordPressEndpoints
{

	/**
	 * @var string
	 */
	protected $namespace;

	/**
	 * @var callable
	 */
	protected $registrationFunction;

	/**
	 * CreateEndpoint constructor.
	 *
	 * @param callable $registrationFunction
	 * @param string $namespace
	 */
	public function __construct(callable $registrationFunction, string $namespace)
	{

		$this->namespace = $namespace;
		$this->registrationFunction = $registrationFunction;
	}

	/**
	 * Register an endpoint with WordPress
	 *
	 * @param Endpoint $endpoint
	 */
	public function registerRouteWithWordPress(Endpoint $endpoint)
	{
		call_user_func($this->registrationFunction, $this->namespace, $endpoint->getUri(), $this->wpArgs($endpoint));
	}


	/**
	 * Create arguments for register_rest_route()
	 *
	 * @param Endpoint $endpoint
	 *
	 * @return array
	 */
	public function wpArgs(Endpoint $endpoint)
	{
		$callback = $this->createCallBack([$endpoint,'handleRequest']);
		$permissionsCallback = $this->createAuthCallBack([$endpoint,'authorizeRequest']);
		return [
			'args' => $endpoint->getArgs(),
			'methods' => $endpoint->getHttpMethod(),
			'callback' => $callback,
			'permission_callback' => $permissionsCallback
		];
	}

	/**
	 * Create the callable function for WordPress to use as the callback for this endpoint
	 *
	 * @param callable $handler
	 * @return callable
	 */
	protected function createCallBack(callable $handler) : callable
	{

		return function (\WP_REST_Request $_request) use ($handler) {
			$request = $this->requestFromWp($_request);
			$response = $handler($request);
			return new \WP_REST_Response(
				$response->getData(),
				$response->getStatus(),
				$response->getHeaders()
			);
		};
	}

	/**
	 * Create a callable function WordPress will call to authorize the request
	 *
	 * @param callable $handler
	 * @return callable
	 */
	protected function createAuthCallBack(callable $handler) : callable
	{
		return function ($_request) use ($handler) {
			$request = $this->requestFromWp($_request);
			return $handler($request);
		};
	}

	/**
	 * Create instance of request class from a WordPress request
	 *
	 * @param \WP_REST_Request $_request
	 *
	 * @return Request
	 */
	protected function requestFromWp(\WP_REST_Request $_request): RequestContract
	{
		$request = new Request();
		$request->setParams(array_merge($_request->get_params(), $_request->get_url_params()));
		foreach ($_request->get_headers() as $header => $headerValue) {
			$request->setHeader($header, $headerValue);
		}
		return $request;
	}
}
