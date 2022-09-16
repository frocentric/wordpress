<?php


namespace NFMailchimp\EmailCRM\RestApi\Traits;

use NFMailchimp\EmailCRM\RestApi\Contracts\HttpContract;

trait ProvidesHttpHeaders
{
	/**
	 * @var array
	 */
	protected $headers;

	/**
	 * Get header from request
	 *
	 * @param string $headerName
	 *
	 * @return string|null
	 */
	public function getHeader(string $headerName)
	{
		return $this->hasHeader($headerName) ? $this->headers[ $headerName ] : null;
	}

	/**
	 * @param string $headerName
	 *
	 * @return bool
	 */
	public function hasHeader(string $headerName): bool
	{

		if (array_key_exists($headerName, $this->getHeaders())) {
			return true;
		}
		return false;
	}


	/**
	 * Set header in request
	 *
	 * @param string $headerName
	 * @param mixed $headerValue
	 *
	 * @return HttpContract
	 */
	public function setHeader(string $headerName, $headerValue): HttpContract
	{
		$this->headers[ $headerName ] = $headerValue;
		return $this;
	}

	/** @inheritdoc */
	public function getHeaders(): array
	{
		return is_array($this->headers) ? $this->headers : [];
	}

	/**
	 * Bulk assign headers
	 *
	 * @param array $headers
	 * @return HttpContract
	 */
	public function setHeaders(array $headers): HttpContract
	{
		foreach ($headers as $header => $headerValue) {
			$this->setHeader($header, $headerValue);
		}
		return $this;
	}
}
