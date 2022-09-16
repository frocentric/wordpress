<?php


namespace NFMailchimp\EmailCRM\Shared\Contracts;

use NFMailchimp\EmailCRM\CfBridge\Entities\Form;
use NFMailchimp\EmailCRM\CfBridge\Entities\ProcessorData;
use NFMailchimp\EmailCRM\CfBridge\Processor;
use NFMailchimp\EmailCRM\Shared\Contracts\SubmissionDataContract as SubmissionData;

/**
 * Interface FormActionHandler
 *
 * A callback function for Ninja Forms action or Caldera Forms processor
 */
interface FormActionHandler
{

	/**
	 * Process the data
	 *
	 * @param SubmissionData $submissionData
	 * @param FormContract $form
	 * @return mixed
	 */
	public function handle(SubmissionData $submissionData, FormContract $form) :array;
}
