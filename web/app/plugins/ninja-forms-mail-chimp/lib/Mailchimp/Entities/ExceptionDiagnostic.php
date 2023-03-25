<?php

namespace NFMailchimp\EmailCRM\Mailchimp\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;

/**
 * Diagnostic data for a given exception string
 *
 */
class ExceptionDiagnostic extends SimpleEntity
{

	/**
	 * String with which to match exception
	 * @var string
	 */
	protected $exceptionStringMatch;

	/**
	 * Context describing condition under which exception was triggered
	 * @var string
	 */
	protected $context;

	/**
	 * Collection of string diagnostic data
	 * @var array
	 */
	protected $diagnostic = [];

	/**
	 * Set Exception String Match
	 * @param string $string
	 * @return \NFMailchimp\EmailCRM\Mailchimp\Entities\ExceptionDiagnostic
	 */
	public function setExceptionStringMatch(string $string): ExceptionDiagnostic
	{
		$this->exceptionStringMatch = $string;
		return $this;
	}

	/**
	 * Set Context
	 * @param string $string
	 * @return \NFMailchimp\EmailCRM\Mailchimp\Entities\ExceptionDiagnostic
	 */
	public function setContext(string $string): ExceptionDiagnostic
	{
		$this->context = $string;
		return $this;
	}

	/**
	 * Add diagnostic string instruction
	 * @param string $string
	 * @return \NFMailchimp\EmailCRM\Mailchimp\Entities\ExceptionDiagnostic
	 */
	public function addDiagnostic(string $string): ExceptionDiagnostic
	{
		$this->diagnostic[] = $string;
		return $this;
	}

	/**
	 * Return ExceptionStringMatch
	 * @return string
	 */
	public function getExceptionStringMatch(): string
	{
		return isset($this->exceptionStringMatch) ? $this->exceptionStringMatch : '';
	}

	/**
	 * Return Context
	 * @return string
	 */
	public function getContext(): string
	{
		return isset($this->context) ? $this->context : '';
	}

	/**
	 * Return diagnostic collection
	 * @return array
	 */
	public function getDiagnosticCollection(): array
	{
		return $this->diagnostic;
	}

	/**
	 * @inheritdoc
	 */
	public static function fromArray(array $items): SimpleEntity
	{
		$obj = new static();
		foreach ($items as $property => $value) {
			if ('diagnostic' === $property && is_array($value)) {
				$obj = $obj->__set($property, $value);
			} elseif (!is_null($value)) {
				$obj = $obj->__set($property, $value);
			}
		}

		return $obj;
	}
}
