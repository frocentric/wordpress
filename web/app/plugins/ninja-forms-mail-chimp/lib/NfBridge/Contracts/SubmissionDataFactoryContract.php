<?php

namespace NFMailchimp\EmailCRM\NfBridge\Contracts;

use NFMailchimp\EmailCRM\Shared\Contracts\FormActionFieldCollection;
use NFMailchimp\EmailCRM\Shared\Contracts\SubmissionDataContract;

interface SubmissionDataFactoryContract
{

	/**
	 * Creates submission data from a NF form submission
	 * @param array $formActionSubmissionArray
	 * @param FormActionFieldCollection $actionSettings
	 * @return SubmissionDataContract
	 */
	public function getSubmissionData(
		array $formActionSubmissionArray,
		FormActionFieldCollection $actionSettings
	): SubmissionDataContract;
}
