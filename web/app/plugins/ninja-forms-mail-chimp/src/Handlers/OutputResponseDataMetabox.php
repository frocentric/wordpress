<?php

namespace NFMailchimp\NinjaForms\Mailchimp\Handlers;

use NFMailchimp\EmailCRM\Shared\Entities\ResponseData;
use NFMailchimp\EmailCRM\Mailchimp\Entities\Subscriber;

final class OutputResponseDataMetabox extends \NF_Abstracts_SubmissionMetabox
{

	/**
	 * HTML markup to output Response Data
	 * @var string
	 */
	protected $markup = '';

	/**
	 * Collection of response data
	 * @var array
	 */
	protected $responseData = [];

	public function __construct()
	{
		parent::__construct();

		$this->_title = 'Mailchimp Response';
	}

	public function render_metabox($post, $metabox)
	{
		if (!$this->sub->get_extra_value('mailchimp')) {
			$this->addNoResponseDataMarkup();
		} else {
			$this->extractResponseData();
			$this->markup = "<dl>";
			foreach ($this->responseData as $responseData) {
				$this->markup .= $this->markupResponseData($responseData);
			}
			$this->markup.="</dl>";
		}
		echo $this->markup;
	}

	/**
	 * Markup response data for HTML output
	 * @param ResponseData $responseData
	 * @return string
	 */
	protected function markupResponseData(ResponseData $responseData): string
	{

		$markup = "<dt>" . esc_html($responseData->getContext()). "</dt>";
		$markup .= "<dd>Result: " . $responseData->getType() . "</dd>";
		$markup .= ('' === $responseData->getMessage()) ? '' :
				"<dd>Exception: " . esc_html($responseData->getMessage()) . "</dd>";
		$markup .= ('' === $responseData->getDiagnostics()) ? '' :
				"<dd>Diagnostics: " . esc_html($responseData->getDiagnostics()) . "</dd>";
		$markup .= $this->prettyPrintResponse($responseData);
		$markup .= ('' === $responseData->getNote()) ? '' :
				"<dd>Notes: " . esc_html($responseData->getNote()) . "</dd>";
		
		return $markup;
	}

		/**
		 * Return reader friendly output conditionally for know response data structures
		 * @param ResponseData $responseData
		 * @return string
		 */
	protected function prettyPrintResponse(ResponseData $responseData):string
	{
		$return = ('' === $responseData->getResponse()) ? '' :
			"<dd>RawResponse: " . esc_html($responseData->getResponse()) . "</dd>";
			
		if ('SubscribeFormActionHandler_subscribeToList'===$responseData->getContext()&&
			'success'=== $responseData->getType()) {
			$subscriber = Subscriber::fromArray(json_decode($responseData->getResponse(), true));
			$return ="<dd>Subscriber Info: </dd>"
					."<dd><strong>Email:</strong>". esc_html($subscriber->email_address) ."</dd>"
					."<dd><strong>Status:</strong>". esc_html($subscriber->status) ."</dd>"
					."<dd><strong>List Id:</strong>". esc_html($subscriber->listId) ."</dd>"
						
					;
		}
		return $return;
	}
	/**
	 * Construct collection of ResponseData entities
	 */
	protected function extractResponseData()
	{
		$mailchimpSubmissionData = $this->sub->get_extra_value('mailchimp');
		if (isset($mailchimpSubmissionData['responseData']) && is_array($mailchimpSubmissionData['responseData'])) {
			foreach ($mailchimpSubmissionData['responseData'] as $responseDataArray) {
				$this->responseData[] = ResponseData::fromArray($responseDataArray);
			}
		}
	}

	/**
	 * Add markup for no response data available
	 */
	protected function addNoResponseDataMarkup()
	{
		$markup = "<div style='text-align: center;'>"
				. "<strong>No response data available for this submission</strong>"
				. "</div>";

		$this->markup .= $markup;
	}
}
