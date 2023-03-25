<?php
namespace NFMailchimp\NinjaForms\Mailchimp\Handlers;

use NFMailchimp\EmailCRM\Mailchimp\Interfaces\MailchimpApi;
use NFMailchimp\EmailCRM\NfBridge\Contracts\SubmissionDataDataHandler;
use NFMailchimp\EmailCRM\NfBridge\Contracts\ActionSettingsDataHandler;
use NFMailchimp\EmailCRM\NfBridge\Contracts\ProcessAction;
use NFMailchimp\EmailCRM\Mailchimp\Entities\SingleList;
use NFMailchimp\EmailCRM\Shared\Entities\ResponseData;

use NFMailchimp\EmailCRM\Mailchimp\ApiRequests\GetAudienceDefinitionData;
use NFMailchimp\EmailCRM\Mailchimp\Handlers\SubscriberBuilder;
use NFMailchimp\NinjaForms\Mailchimp\Handlers\ConstructApiSubscriberFromActionSettings;

use NFMailchimp\EmailCRM\Mailchimp\ApiRequests\MailchimpApiException;

class AddOrUpdateMailchimp implements ProcessAction
{

    public function process(
        ActionSettingsDataHandler $actionSettingsDataHandler,
        string $formId,
        SubmissionDataDataHandler $submissionDataDataHandler,
        array $runtimeData
    ): SubmissionDataDataHandler {

        $data = $submissionDataDataHandler->getData();

        $fieldsData = $data['fields'];
        
        $opt_in = $this->isOptIn($fieldsData);

        if(!$opt_in){
            return $submissionDataDataHandler;
        }

        try {
            $newsletterId = $this->getNewsletterId($actionSettingsDataHandler);
            
            $mailchimpApi = $this->getApi($runtimeData);
            
            $apiReadySubscriber = $this->getApiReadySubscriber($runtimeData, $newsletterId, $actionSettingsDataHandler);
            
            $responseObject = $mailchimpApi->addOrUpdateMember($newsletterId, $apiReadySubscriber->getEmailAddress(), $apiReadySubscriber->getRequestBody());
            
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
     * Return bool - if this sub has an opt-in field, is it checked? If no opt-in field, default to true
     * @param array[] $fields
     * @return bool
     */
    protected function isOptIn(array $fields): bool
    {
        // Set true flag for later use.
        $opt_in = true;

        // Loop over the fields from the form data and...
        foreach ($fields as $field) {
            // ...If the field type is equal to Mailchimp Opt continue.
            if ('mailchimp-optin' != $field['type']) {
                continue;
            }

            // ...If the field value is the field value is false change the optin flag to false.
            if (!$field['value']) {
                $opt_in = false;
            }
        }

        return $opt_in;
    }

    /**
     * Construct object structured for Add/Update subscriber
     *
     * @param array $runtimeData
     * @param string $newsletterId
     * @param ActionSettingsDataHandler $actionSettingsDataHandler
     * @return ConstructApiSubscriberFromActionSettings
     */
    protected function getApiReadySubscriber(array $runtimeData, string $newsletterId,  ActionSettingsDataHandler $actionSettingsDataHandler): ConstructApiSubscriberFromActionSettings
    {
        $mailchimpApi = $this->getApi($runtimeData);

        $singleList = SingleList::fromArray([
            'id' => $newsletterId
        ]);

        $audienceDefinition = (new GetAudienceDefinitionData($mailchimpApi))->handle($singleList);

        $subscriberBuilder = new SubscriberBuilder($audienceDefinition);

        $return = (new ConstructApiSubscriberFromActionSettings($actionSettingsDataHandler, $subscriberBuilder))->handle();

        return $return;
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
