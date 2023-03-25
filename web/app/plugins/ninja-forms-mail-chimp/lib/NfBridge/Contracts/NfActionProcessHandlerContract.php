<?php

namespace NFMailchimp\EmailCRM\NfBridge\Contracts;

use NFMailchimp\EmailCRM\Shared\Contracts\FormActionHandler;

/**
 * Contract by which all NF Action's process callbacks abide
 *
 */
interface NfActionProcessHandlerContract extends FormActionHandler
{

	/**
	 * Return Ninja Forms $data submission after processing
	 * @return array
	 */
	public function getPostProcessData(): array;
		
		/**
		 * Extract processing data from form fields to return key-value pairs
		 *
		 * Some data required by submission action is contained within form
		 * fields.  Given the form fields data upon submission, extract the
		 * required data to return it as key-value pairs such that it can be
		 * added to the submission data and processed
		 * @param array $data Form field process data
		 * @return array
		 */
	public function extractFormFieldProcessingData(array $data):array;
}
