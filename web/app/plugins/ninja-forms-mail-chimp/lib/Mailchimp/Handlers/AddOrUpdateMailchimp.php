<?php

namespace NFMailchimp\EmailCRM\Mailchimp\Handlers;

use NFMailchimp\EmailCRM\Mailchimp\Interfaces\MailchimpApi;
use NFMailchimp\EmailCRM\NfBridge\Contracts\SubmissionDataDataHandler;
use NFMailchimp\EmailCRM\NfBridge\Contracts\ActionSettingsDataHandler;
use NFMailchimp\EmailCRM\NfBridge\Contracts\ProcessAction;
use NFMailchimp\EmailCRM\Mailchimp\Entities\SingleList;
use NFMailchimp\EmailCRM\Shared\Entities\ResponseData;

use NFMailchimp\EmailCRM\Mailchimp\ApiRequests\GetAudienceDefinitionData;
use NFMailchimp\EmailCRM\Mailchimp\Handlers\SubscriberBuilder;
use NFMailchimp\EmailCRM\Mailchimp\Handlers\ExtractSubscriberFromActionSettings;

use NFMailchimp\EmailCRM\Mailchimp\ApiRequests\MailchimpApiException;

class AddOrUpdateMailchimp implements ProcessAction
{

    public function process(
        ActionSettingsDataHandler $actionSettingsDataHandler,
        string $formId,
        SubmissionDataDataHandler $submissionDataDataHandler,
        array $runtimeData
    ): SubmissionDataDataHandler {
        try {
            $newsletterId = $this->getNewsletterId($actionSettingsDataHandler);
            
            $mailchimpApi = $this->getApi($runtimeData);
            
            $singleList = SingleList::fromArray([
                'id' => $newsletterId
            ]);
            
            $audienceDefinition = (new GetAudienceDefinitionData($mailchimpApi))->handle($singleList);
            $subscriberBuilder = new SubscriberBuilder($audienceDefinition);
            
            $converted = new ExtractSubscriberFromActionSettings($actionSettingsDataHandler, $subscriberBuilder);

            $converted->handle();
            
            $responseObject = $mailchimpApi->addOrUpdateMember($newsletterId, $converted->getEmailAddress(), $converted->getRequestBody());
            
            $responseArray = (ResponseData::fromArray([
                'type' => 'success',
                'response' => $responseObject,
                'context' => 'SubscribeFormActionHandler_subscribeToList'
            ]))->toArray();
                
            $submissionDataDataHandler->pushExtra($responseArray);

            return $submissionDataDataHandler;
        } catch (\UnexpectedValueException $unexpectedValue) {

            \error_log('Caught value exception');
            \error_log($unexpectedValue->getMessage());
            \error_log(self::class . '::' . __FUNCTION__);
        } catch (MailchimpApiException $mailchimpException) {
            $rejectionMessage = (ResponseData::fromArray( [
                'type' => 'error',
                'context' => 'MailchimpSubscribeActionProcessHandler_Response',
                'note' => $mailchimpException->getMessage()
            ]))->toArray();

            $submissionDataDataHandler->pushExtra($rejectionMessage);
        } catch (\Throwable $e) {

            \error_log($e->getMessage());
            \error_log(self::class . '::' . __FUNCTION__);
        } finally {
            return $submissionDataDataHandler;
        }
    }

    /**
     * Get newsletter id from action settings
     *
     * @param ActionSettingsDataHandler $actionSettingsDataHandler
     * 
     * @return string
     * @throws \UnexpectedValueException
     */
    private function getNewsletterId(ActionSettingsDataHandler $actionSettingsDataHandler): string
    {
        if ($actionSettingsDataHandler->getValue('newsletter_list', false)) {

            $return = $actionSettingsDataHandler->getValue('newsletter_list');
        } else {
            throw new \UnexpectedValueException('Newsletter key does not exist in submission process data');
        }
        return $return;
    }

    /**
     * Undocumented function
     *
     * @param array $runtimeData
     * @return MailchimpApi
     * @throws \\UnexpectedValueException
     */
    private function getApi(array $runtimeData): MailchimpApi
    {
        if (isset($runtimeData['mailchimpApi'])) {
            $return  = $runtimeData['mailchimpApi'];
        } else {
            throw new \UnexpectedValueException('Mailchimp API not passed correctly in submission process data');
        }

        return $return;
    }
}
