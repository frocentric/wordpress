<?php

namespace NFMailchimp\EmailCRM\Shared\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;

/**
 * Provides standardized response for Requests
 *
 * Enables any source a response to minimally handle the response and pass
 * it on to the requester, which will know how to process it as required
 *
 */
class HandledResponse extends SimpleEntity
{

	/**
	 * Explains why the request was made
	 *
	 * Gives idea of where to look in troubleshooting issues
	 * @var string
	 */
	protected $context = '';

	/**
	 * Unix-based integer timestamp
	 * @var int
	 */
	protected $timestamp = 0;

	/**
	 * Specifies if the intent of the request was satisfied
	 *
	 * @var bool
	 */
	protected $isSuccessful = true;

	/**
	 * Indicates if the response is a WP_Error
	 *
	 * @var bool
	 */
	protected $isWpError = false;

	/**
	 * Indicates if the response is an error from the API
	 * @var bool
	 */
	protected $isApiError = false;

	/**
	 * Indicates if the response was an Exception (non WP_Error)
	 * @var bool
	 */
	protected $isException = false;

	/**
	 * Indicates if the response provided no results
	 * @var bool
	 */
	protected $hasNoData = false;

	/**
	 * Count of the records returned
	 * @var int
	 */
	protected $recordCount = 0;

	/**
	 * Collection of response data
	 * @var array
	 */
	protected $records = [];

	/**
	 * Error code returned
	 * @var int
	 */
	protected $errorCode = 0;

	/**
	 * Collection of error message strings
	 * @var array
	 */
	protected $errorMessages = [];

	/**
	 * Body of response, usually in JSON format
	 * @var string
	 */
	protected $responseBody = '';

	/**
	 * Set the context
	 *
	 * @param string $string
	 * @return \NFMailchimp\EmailCRM\Shared\Entities\HandledResponse
	 */
	public function setContext(?string $string): HandledResponse
	{
		$this->context = $string;
		return $this;
	}

	/**
	 * Get the context
	 *
	 * @return string
	 */
	public function getContext(): string
	{
		return (isset($this->context)&& !is_null($this->context))?$this->context:'';
	}

	/**
	 * Set the record count
	 * @param int $int
	 * @return \NFMailchimp\EmailCRM\Shared\Entities\HandledResponse
	 */
	public function setRecordCount(?int $int): HandledResponse
	{
		$this->recordCount = $int;
		return $this;
	}

	/**
	 * Get the record count
	 * @return int
	 */
	public function getRecordCount(): int
	{
		return (isset($this->recordCount)&& !is_null($this->recordCount))?$this->recordCount:0;
	}

	/**
	 * Set the records collection
	 *
	 * @param array $array
	 * @return \NFMailchimp\EmailCRM\Shared\Entities\HandledResponse
	 */
	public function setRecords(?array $array): HandledResponse
	{
		$this->records = $array;
		return $this;
	}

	/**
	 * Append a single record to the record collection
	 *
	 * @param string $string
	 * @return \NFMailchimp\EmailCRM\Shared\Entities\HandledResponse
	 */
	public function appendRecord(?string $string): HandledResponse
	{
		$this->records[] = $string;
		return $this;
	}

	/**
	 * Append a array of records to the collection
	 *
	 * @param array $array
	 * @return \NFMailchimp\EmailCRM\Shared\Entities\HandledResponse
	 */
	public function appendRecords(?array $array): HandledResponse
	{
		$this->records = array_merge($this->records, $array);
		return $this;
	}

	/**
	 * Get the record collection
	 *
	 * @return array
	 */
	public function getRecords(): array
	{
		return (isset($this->records)&& !is_null($this->records))?$this->records:[];
	}

	/**
	 * Set the timestamp
	 *
	 * @param int $int
	 * @return \NFMailchimp\EmailCRM\Shared\Entities\HandledResponse
	 */
	public function setTimestamp(?int $int): HandledResponse
	{
		$this->timestamp = $int;
		return $this;
	}

	/**
	 * Get the timestamp
	 * @return int
	 */
	public function getTimestamp(): int
	{
		return (isset($this->timestamp)&& !is_null($this->timestamp))?$this->timestamp:0;
	}

	/**
	 * Set IsSuccessful
	 *
	 * @param bool $bool
	 * @return \NFMailchimp\EmailCRM\Shared\Entities\HandledResponse
	 */
	public function setIsSuccessful(?bool $bool): HandledResponse
	{
		$this->isSuccessful = $bool;
		return $this;
	}

	/**
	 * Get IsSuccessful
	 *
	 * @return bool
	 */
	public function isSuccessful(): bool
	{
		return (isset($this->isSuccessful)&& !is_null($this->isSuccessful))?$this->isSuccessful:true;
	}

	/**
	 * Set IsWpError
	 *
	 * @param bool $bool
	 * @return \NFMailchimp\EmailCRM\Shared\Entities\HandledResponse
	 */
	public function setIsWpError(?bool $bool): HandledResponse
	{
		$this->isWpError = $bool;
		return $this;
	}

	/**
	 * Get IsWpError
	 * @return bool
	 */
	public function isWpError(): bool
	{
		return (isset($this->isWpError)&& !is_null($this->isWpError))?$this->isWpError:false;
	}

	/**
	 * Set IsApiError
	 *
	 * @param bool $bool
	 * @return \NFMailchimp\EmailCRM\Shared\Entities\HandledResponse
	 */
	public function setIsApiError(?bool $bool): HandledResponse
	{
		$this->isApiError = $bool;
		return $this;
	}

	/**
	 * Get IsApiError
	 *
	 * @return bool
	 */
	public function isApiError(): bool
	{
		return (isset($this->isApiError)&& !is_null($this->isApiError))?$this->isApiError:false;
	}

	/**
	 * Set IsException
	 *
	 * @param bool $bool
	 * @return \NFMailchimp\EmailCRM\Shared\Entities\HandledResponse
	 */
	public function setIsException(?bool $bool): HandledResponse
	{
		$this->isException = $bool;
		return $this;
	}

	/**
	 * Get IsException
	 *
	 * @return bool
	 */
	public function isException(): bool
	{
		return (isset($this->isException)&& !is_null($this->isException))?$this->isException:false;
	}

	/**
	 * Set HasNoData
	 *
	 * @param bool $bool
	 * @return \NFMailchimp\EmailCRM\Shared\Entities\HandledResponse
	 */
	public function setHasNoData(?bool $bool): HandledResponse
	{
		$this->hasNoData = $bool;
		return $this;
	}

	/**
	 * Get HasNoData
	 *
	 * @return bool
	 */
	public function hasNoData(): bool
	{
		return (isset($this->hasNoData)&& !is_null($this->hasNoData))?$this->hasNoData:false;
	}

	/**
	 * Set ErrorCode
	 *
	 * @param int $int
	 * @return \NFMailchimp\EmailCRM\Shared\Entities\HandledResponse
	 */
	public function setErrorCode(?int $int): HandledResponse
	{
		$this->errorCode = $int;
		return $this;
	}

	/**
	 * Get ErrorCode
	 *
	 * @return int
	 */
	public function getErrorCode(): int
	{
		return (isset($this->errorCode)&& !is_null($this->errorCode))?$this->errorCode:0;
	}

	/**
	 * Get ErrorMessages
	 *
	 * @param array $array
	 * @return \NFMailchimp\EmailCRM\Shared\Entities\HandledResponse
	 */
	public function setErrorMessages(?array $array): HandledResponse
	{
		$this->errorMessages = $array;
		return $this;
	}

	/**
	 * Append a single error message to collection
	 *
	 * @param string $string
	 * @return \NFMailchimp\EmailCRM\Shared\Entities\HandledResponse
	 */
	public function appendErrorMessage(?string $string): HandledResponse
	{
		$this->errorMessages[] = $string;
		return $this;
	}

	/**
	 * Get ErrorMessages
	 *
	 * @return array
	 */
	public function getErrorMessages(): array
	{
		return (isset($this->errorMessages)&& !is_null($this->errorMessages))?$this->errorMessages:[];
	}

	/**
	 * Set ResponseBody
	 *
	 * @param string $string
	 * @return \NFMailchimp\EmailCRM\Shared\Entities\HandledResponse
	 */
	public function setResponseBody(?string $string): HandledResponse
	{
		$this->responseBody = $string;
		return $this;
	}

	/**
	 * Get ResponseBody
	 *
	 * @return string
	 */
	public function getResponseBody(): string
	{
		return (isset($this->responseBody)&& !is_null($this->responseBody))?$this->responseBody:'';
	}
}
