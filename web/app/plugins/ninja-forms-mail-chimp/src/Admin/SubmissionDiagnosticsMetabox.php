<?php

namespace NFMailchimp\NinjaForms\Mailchimp\Admin;

use NFMailchimp\EmailCRM\Shared\Entities\ResponseData;
use NinjaForms\Includes\Entities\MetaboxOutputEntity;
use NFMailchimp\EmailCRM\Mailchimp\Entities\Subscriber;

class SubmissionDiagnosticsMetabox
{

    public function handle($extraValue, $nfSub): ?MetaboxOutputEntity
    {
        $return = null;

        $labelValueCollection = self::extractResponses($extraValue);

        if (!empty($labelValueCollection)) {

            $array = [
                'title' => __('Mailchimp Subscribe', 'ninja-forms-authorize-net'),
                'labelValueCollection' => $labelValueCollection

            ];

            $return = MetaboxOutputEntity::fromArray($array);
        }

        return $return;
    }

    protected  static function extractResponses($mailchimpSubmissionData): array
    {
        $return = [];

        if (isset($mailchimpSubmissionData['responseData']) && is_array($mailchimpSubmissionData['responseData'])) {
            foreach ($mailchimpSubmissionData['responseData'] as $responseDataArray) {
                $responses[] = ResponseData::fromArray($responseDataArray);
            }
        } else {
            $return[] = [
                'label' => 'No Data',
                'value' => 'Submission could not communicate with Mailchimp'
            ];
            return $return;
        }

        /** @var ResponseData $responseData */
        foreach ($responses as $responseData) {

            if (
                'error' !== $responseData->getType()
            ) {
                $subscriber = Subscriber::fromArray(json_decode($responseData->getResponse(), true));
                $return[] = [
                    'label' => __('Subscriber Info', 'ninja-forms-mail-chimp'),
                    'value' => __('Successfully subscribed', 'ninja-forms-mail-chimp')
                ];
                $return[] = [
                    'label' => __('Email', 'ninja-forms-mail-chimp'),
                    'value' => esc_html($subscriber->email_address)
                ];
                $return[] = [
                    'label' => __('Status', 'ninja-forms-mail-chimp'),
                    'value' => esc_html($subscriber->status)
                ];
                $return[] = [
                    'label' => __('List Id', 'ninja-forms-mail-chimp'),
                    'value' => esc_html($subscriber->listId)
                ];
            } else {
                $return[] = [
                    'label' => __('Notice', 'ninja-forms-mail-chimp'),
                    'value' => __('Request rejected', 'ninja-forms-mail-chimp')
                ];
                $return[] = [
                    'label' => __('Rejection', 'ninja-forms-mail-chimp'),
                    'value' => $responseData->getNote()
                ];
            }
        }

        return $return;
    }
}
