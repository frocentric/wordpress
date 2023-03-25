<?php


namespace NFMailchimp\EmailCRM\RestApi;

use NFMailchimp\EmailCRM\RestApi\Contracts\EndpointContract;
use NFMailchimp\EmailCRM\RestApi\Contracts\Request;
use NFMailchimp\EmailCRM\RestApi\Contracts\RequestContract;
use NFMailchimp\EmailCRM\RestApi\Contracts\ResponseContract;
use NFMailchimp\EmailCRM\RestApi\Contracts\TokenContract;
use NFMailchimp\EmailCRM\RestApi\Traits\ProvidesHttpMethod;

abstract class Endpoint implements EndpointContract
{
	use ProvidesHttpMethod;

	/**
	 * @var string
	 */
	protected $uri;

	/**
	 * @var array
	 */
	protected $args;

	/**
	 * @inheritDoc
	 */
	public function getUri(): string
	{
		return  $this->uri;
	}

	/**
	 * @inheritDoc
	 */
	public function setUri(string $uri): EndpointContract
	{
		$this->uri = $uri;
		return  $this;
	}

	/**
	 * @inheritDoc
	 */
	public function getArgs(): array
	{
		return  $this->args;
	}

	/**
	 * @inheritDoc
	 */
	public function setArgs(array $args): EndpointContract
	{
		$this->args = $args;
		return  $this;
	}


	/**
	 * @inheritDoc
	 */
	public function getToken(RequestContract $request): string
	{
		$header = $request->getHeader('Authorization');
		if ($header && 0 === strpos($header, 'Bearer')) {
			return trim(substr($header, 7));
		}
		return  $header ? $header : '';
	}
}
