<?php


namespace NFMailchimp\EmailCRM\RestApi\Traits;

use NFMailchimp\EmailCRM\RestApi\Contracts\HttpRequestContract;

trait ProvidesRestParams
{

	/** @var array */
	protected $params;

	/**
	 * Get parameter from request
	 *
	 * @param string $paramName
	 *
	 * @return mixed
	 */
	public function getParam(string $paramName)
	{
		return $this->hasParam($paramName) ? $this->params[ $paramName ] : null;
	}

	/**
	 * Set parameter in request
	 *
	 * @param string $paramName
	 * @param mixed $paramValue
	 *
	 * @return $this
	 */
	public function setParam(string $paramName, $paramValue): HttpRequestContract
	{
		$this->params[ $paramName ] = $paramValue;
		return $this;
	}

	/**
	 * Does request have param?
	 *
	 * @param string $paramName
	 *
	 * @return bool
	 */
	public function hasParam(string $paramName): bool
	{
		return array_key_exists($paramName, $this->getParams());
	}

	/**
	 * Get all params of request
	 *
	 * @return array
	 */
	public function getParams(): array
	{
		return is_array($this->params) ? $this->params : [];
	}

	/**
	 * Set parameters of request
	 *
	 * @param array $params
	 *
	 * @return HttpRequestContract
	 */
	public function setParams(array $params): HttpRequestContract
	{
		$this->params = $params;
		return $this;
	}
}
