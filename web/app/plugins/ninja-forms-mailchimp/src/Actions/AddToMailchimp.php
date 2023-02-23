<?php

namespace NFMailchimp\NinjaForms\Mailchimp\Actions;

use NFMailchimp\EmailCRM\NfBridge\Contracts\ProcessAction as InterfacesProcessAction;
use NFMailchimp\EmailCRM\Mailchimp\Interfaces\MailchimpApi;

use NFMailchimp\EmailCRM\NfBridge\Actions\NewsletterAction;
use NFMailchimp\NinjaForms\Mailchimp\Handlers\GetNfStructuredLists;

use NFMailchimp\NinjaForms\Mailchimp\Handlers\AddOrUpdateMailchimp;

/**
 * Action to subscribe form submission to Mailchimp
 */
class AddToMailchimp extends NewsletterAction
{
    /** @var MailchimpApi */
    protected $mailchimpApi;

    /** @param MailchimpApi $mailchimpApi */
    public function __construct(MailchimpApi $mailchimpApi)
    {
        $this->mailchimpApi = $mailchimpApi;
        parent::__construct();
    }

    /** @inheritDoc */
    protected function setActionProperties(): void
    {
        // This should be a slug format
        $this->_name = 'mailchimp';

        // Ensure your name is short, and translatable
        $this->_nicename = 'Mailchimp';

        // future documentation
        $this->_tags = ['newsletter'];

        // These two are the coarse and fine settings for putting your action in the action queue
        $this->_timing = 'normal'; // can be `early` `normal` `late`
        $this->_priority = 10; // can be a string version of an integer
    }

    /** @inheritDoc */
    protected function setActionSettings( ): void
    {
        $this->_settings['double_opt_in'] = [
            'name' => 'double_opt_in',
            'type' => 'toggle',
            'label' => __('Require subscribers to confirm their subscription', 'ninja-forms-mail-chimp'),
            'group' => 'advanced',
            'width' => 'full',
            'value' => 0
        ];

        $this->_settings['enable_new_tags'] = [
            'name' => 'enable_new_tags',
            'type' => 'toggle',
            'label' => __('Allow creation of NEW tags from submitted values', 'ninja-forms-mail-chimp'),
            'group' => 'advanced',
            'width' => 'full',
            'value' => 0
        ];
    }
    
    /** @inheritDoc */
    protected function instantiateActionProcessor(): InterfacesProcessAction
    {
        return new AddOrUpdateMailchimp();
    }

    /** @inheritDoc */
    protected function constructRuntimeData(array $actionSettings, $formId, array $data):void{
        $this->runtimeData['mailchimpApi']=$this->mailchimpApi;
    }

    /** @inheritDoc */
    public function get_lists()
    {
        $return = $this->getLists();

        return $return;
    }

    /**
     * Make API calls to construct array of lists
     * 
     * @return array
     */
    protected function getLists(): array
    {
        $getNfStructuredLists = new GetNfStructuredLists($this->mailchimpApi);
        $lists = $getNfStructuredLists->getLists();
           
        // Make/update an option every time the list is updated.
        // @todo: Verify if needed - doesn't appear to be used - SRS
        update_option('ninja_forms_mailchimp_interests', $lists);
        return $lists;
    }

}
