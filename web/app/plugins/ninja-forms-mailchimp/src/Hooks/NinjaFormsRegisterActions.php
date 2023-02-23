<?php

namespace NFMailchimp\NinjaForms\Mailchimp\Hooks;

use NFMailchimp\EmailCRM\Mailchimp\Interfaces\MailchimpApi;
use NFMailchimp\NinjaForms\Mailchimp\Actions\AddToMailchimp;

class NinjaFormsRegisterActions
{

    /** @var MailchimpApi */
    protected $mailchimpApi;

    /** Register hook */
    public function registerHooks(): void
    {
        \add_action('ninja_forms_register_actions', [$this, 'hook'], 10);
    }

    /**
     * Register actions with Ninja Forms
     *
     * @return array
     */
    public function hook($actions): array
    {
        // Ensure we return the existing collection
        $return = $actions;

        // Instantiate our custom action
        $myAction = new AddToMailchimp($this->mailchimpApi);

        // Push our action, keyed on action key
        $return[$myAction->getActionKey()] = $myAction;

        // Register your additional actions by keying an instatiated object here

        // Ensure you RETURN the array
        return $return;
    }

    /**
     * Set the value of mailchimpApi
     *
     * @return  NinjaFormsRegisterActions
     */
    public function setMailchimpApi(MailchimpApi $mailchimpApi): NinjaFormsRegisterActions
    {
        $this->mailchimpApi = $mailchimpApi;
  
        return $this;
    }
}
