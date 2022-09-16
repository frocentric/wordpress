<?php

namespace NFMailchimp\EmailCRM\Shared\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;

/**
 * Class ResponseData
 *
 * Provides standardized data from a response with meta data
 *
 *
 */
class ResponseData extends SimpleEntity
{


	/**
	 * Response data, typically as JSON
	 * @var string
	 */
	protected $response = '';

	/**
	 * Message - typically from Exception ->get_message()
	 * @var string
	 */
	protected $message = '';

	/**
	 * Programmatic word(s) describing what generated this response data
	 * @var string
	 */
	protected $context = '';

	/**
	 * Human-readable instructions to help address any exception issues
	 * @var string
	 */
	protected $diagnostics = '';

	/**
	 * Usually `success` or `failure` to categorize the response
	 * @var string
	 */
	protected $type = '';

	/**
	 * 
	 * @var string
	 */
	protected $note = '';

	/**
	 * Return Response data as ResponseData entity
	 * @param array $items
	 * @return SimpleEntity
	 */
	public static function fromArray(array $items): SimpleEntity
	{
		$obj = new static();

		foreach ($items as $property => $value) {
			if (property_exists($obj, $property)) {
				if (is_object($value)) {
					$value = json_encode($value);
				}
										
							$obj->$property = $value;
			}
		}
			return $obj;
	}

	/**
	 * Return array of ResponseData
	 * @return array
	 */
	public function toArray(): array
	{
		$vars = get_object_vars($this);
		$array = [];
		foreach ($vars as $property => $value) {
			$array[$property] = $value;
		}
		return $array;
	}

	/**
	 * Return Response
	 * @return string
	 */
	public function getResponse(): string
	{
		return $this->response;
	}

	/**
	 * Return Message
	 * @return string
	 */
	public function getMessage(): string
	{
		return $this->message;
	}

	/**
	 * Return Context
	 * @return string
	 */
	public function getContext(): string
	{
		return $this->context;
	}

	/**
	 * Return Diagnostics
	 * @return string
	 */
	public function getDiagnostics(): string
	{
		return $this->diagnostics;
	}

	/**
	 * Return Type
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * Return Note
	 * @return string
	 */
	public function getNote(): string
	{
		return $this->note;
	}

	/**
	 * Set Response
	 * @return SimpleEntity
	 */
	public function setResponse(string $value): SimpleEntity
	{
		$this->response = $value;

		return $this;
	}

	/**
	 * Set Message
	 * @return SimpleEntity
	 */
	public function setMessage(string $value): SimpleEntity
	{
		$this->message = $value;
		return $this;
	}

	/**
	 * Set Context
	 * @return SimpleEntity
	 */
	public function setContext(string $value): SimpleEntity
	{
		$this->context = $value;
		return $this;
	}

	/**
	 * Set Diagnostics
	 * @return SimpleEntity
	 */
	public function setDiagnostics(string $value): SimpleEntity
	{
		$this->diagnostics = $value;
		return $this;
	}

	/**
	 * Set Type
	 * @return SimpleEntity
	 */
	public function setType(string $value): SimpleEntity
	{
		$this->type = $value;
		return $this;
	}

	/**
	 * Set Note
	 * @return SimpleEntity
	 */
	public function setNote(string $value): SimpleEntity
	{
		$this->note = $value;
		return $this;
	}
}
