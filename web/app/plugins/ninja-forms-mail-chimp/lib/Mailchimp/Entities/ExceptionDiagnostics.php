<?php

namespace NFMailchimp\EmailCRM\Mailchimp\Entities;

use NFMailchimp\EmailCRM\Mailchimp\Entities\ExceptionDiagnostic;
use NFMailchimp\EmailCRM\Shared\SimpleEntity;

/**
 * Provides diagnostic support for addressing exceptions
 *
 */
class ExceptionDiagnostics extends SimpleEntity
{
	
	/**
	 * Collection of Exception Diagnostics
	 * @var ExceptionDiagnostic[]
	 */
	protected $exceptionDiagnostics = [];
	
	/** @inheritdoc */
	public static function fromArray(array $items): SimpleEntity
	{
		$obj = new static();
		foreach ($items as $list) {
			if (!is_array($list)) {
				if (is_a($list, ExceptionDiagnostic::class)) {
					$obj->addExceptionDiagnostic($list);
					continue;
				} else {
					$list = (array) $list;
				}
			}

			$obj->addExceptionDiagnostic(ExceptionDiagnostic::fromArray($list));
		}
		return $obj;
	}

	/**
	 * Add an Exception Diagnostic to collection
	 *
	 * @param ExceptionDiagnostic $diagnostic
	 *
	 * @return ExceptionDiagnostics
	 */
	public function addExceptionDiagnostic(ExceptionDiagnostic $diagnostic): ExceptionDiagnostics
	{
		$this->exceptionDiagnostics[] = $diagnostic;
		return $this;
	}


	/**
	 * Get all ExceptionDiagnostics in collection
	 *
	 * @return ExceptionDiagnostic[]
	 */
	public function getExceptionDiagnostics(): array
	{
		return $this->exceptionDiagnostics;
	}
}
