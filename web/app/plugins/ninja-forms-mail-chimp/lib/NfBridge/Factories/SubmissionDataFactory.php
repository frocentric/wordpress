<?php

namespace NFMailchimp\EmailCRM\NfBridge\Factories;

use NFMailchimp\EmailCRM\NfBridge\Contracts\SubmissionDataFactoryContract;
use NFMailchimp\EmailCRM\Shared\Contracts\FormActionFieldCollection;
use NFMailchimp\EmailCRM\Shared\Contracts\SubmissionDataContract;
use NFMailchimp\EmailCRM\NfBridge\Entities\SubmissionData;

class SubmissionDataFactory implements SubmissionDataFactoryContract
{

	/** @inheritdoc */
	public function getSubmissionData(array $formActionSubmissionArray, FormActionFieldCollection $actionSettings): SubmissionDataContract
	{
		return new SubmissionData($formActionSubmissionArray, $actionSettings);
	}
}
