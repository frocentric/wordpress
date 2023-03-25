<?php

namespace NFMailchimp\NinjaForms\Mailchimp\Handlers;

use NFMailchimp\EmailCRM\Mailchimp\Interfaces\MailchimpApi;
use NFMailchimp\EmailCRM\Mailchimp\Entities\SingleList;

use NFMailchimp\EmailCRM\Mailchimp\ApiRequests\GetLists;
use NFMailchimp\EmailCRM\Mailchimp\ApiRequests\GetInterestCategories;
use NFMailchimp\EmailCRM\Mailchimp\ApiRequests\GetInterests;
use NFMailchimp\EmailCRM\Mailchimp\ApiRequests\GetMergeFields;

class GetNfStructuredLists
{

    /** @var MailchimpApi */
    protected $mailchimpApi;

    public function __construct(MailchimpApi $mailchimpApi)
    {
        $this->mailchimpApi = $mailchimpApi;
    }

    /**
     * Get Mailchimp lists structure for NF use
     *
     * @return array
     */
    public function getLists(): array
    {
        $getLists = new GetLists($this->mailchimpApi);

        $listCollection = $getLists->requestLists()->getLists();

        $return = $this->extractListData($listCollection);

        return $return;
    }

    /**
     * Extract list data as used by Ninja Forms Newsletter action
     * @param array $listCollection
     * @return array
     */
    protected function extractListData(array $listCollection): array
    {
        /** @var SingleList $list */
        $nfMailchimpLists = [];
        foreach ($listCollection as $list) {
            // Create/update a setting with the the ID and name of the list.
            Ninja_Forms()->update_setting('mail_chimp_list_' . $list->getId(), $list->getName());

            // Build the array of lists.
            $nfMailchimpLists[] = array(
                'value' => $list->getId(),
                'label' => $list->getName(),
                'groups' => $this->getListInterestCategories($list->getId()),
                'fields' => $this->getMergeVars($list->getId())
            );
        }

        return $nfMailchimpLists;
    }


    /**
     * Get Interest Categories for a given list id
     * @param string $listId
     * @return array
     */
    protected function getListInterestCategories($listId): array
    {

        $getInterestCategoriesAction = new GetInterestCategories($this->mailchimpApi);

        $interestCategoriesCollection = $getInterestCategoriesAction->requestInterestCategories($listId)->getInterestCategories();

        $categories = $this->consolidateInterestsAcrossCategories($listId, $interestCategoriesCollection);

        Ninja_Forms()->update_setting('nf_mailchimp_categories_' . $listId, $categories);

        return $categories;
    }

    /**
     * Consolidate interests across all interest categories, structured for NF Newsletter Action
     * @param string $listId
     * @param array $interestCategoriesCollection
     * @return array
     */
    protected function consolidateInterestsAcrossCategories(string $listId, array $interestCategoriesCollection): array
    {
        $interestsAllCategories = [];
        // Loop over the categories we get back from the API.
        foreach ($interestCategoriesCollection as $category) {
            // Gets our interests lists.
            $interests = $this->getInterests($listId, $category->getId());

            // Loops over interests and builds interest list.
            $addedInterests = $this->constructInterestsActionStructure($listId, $interests);

            $interestsAllCategories = array_merge($interestsAllCategories, $addedInterests);
        }
        return $interestsAllCategories;
    }


    /**
     * Get interests for a given list id and interest category id
     * @param string $listId
     * @param string $interestCategoryId
     * @return array
     */
    protected function getInterests($listId, $interestCategoryId): array
    {

        $getInterests = new GetInterests($this->mailchimpApi);

        $interestsCollection = $getInterests->requestInterests($listId, $interestCategoryId)->getInterests();

        $interests = $this->constructInterestsArray($interestsCollection);
        return $interests;
    }

    /**
     * Return indexed array of name/id pairs for interests collection
     * @param array $interestsCollection
     * @return array
     */
    protected function constructInterestsArray(array $interestsCollection): array
    {
        $interests = [];
        foreach ($interestsCollection as $interest) {
            // Build our array.
            $interests[] = array(
                'name' => $interest->getName(),
                'id' => $interest->getId()
            );
        }
        return $interests;
    }

    /**
     * Construct array of interests into NF standard structure
     *
     * Glues the listId, interest Id, and interest name in an underscore-
     * delineated structure, parsed to construct Action Settings
     * @param string $listId
     * @param array $interests
     * @return array
     */
    protected function constructInterestsActionStructure(string $listId, array $interests): array
    {
        $categories = [];
        foreach ($interests as $interest) {
            $categories[] = array(
                'value' => $listId . '_group_' . $interest['id'] . '_' . $interest['name'],
                'label' => $interest['name'],
            );
        }
        return $categories;
    }

    /**
     * Get Merge Vars for a given list id
     * @param string $listId
     * @return array
     */
    protected function getMergeVars(string $listId): array
    {
        $getMergeVars = new GetMergeFields($this->mailchimpApi);

        $mergeVarsCollection = $getMergeVars->requestMergeFields($listId)->getMergeVars();

        $mergeVars = $this->buildMergeVars($mergeVarsCollection, $listId);

        return $mergeVars;
    }

        /**
     * Build MergeVars array from a collection
     * @param array $mergeVarsCollection
     * @param string $listId
     * @return array
     */
    protected function buildMergeVars($mergeVarsCollection, $listId): array
    {

        /** @var MergeVar $mergeVar */
        // Email field is required for all new mailing list sign ups,
        // but is not pulled in through the api so we need to build it ourselves.
        $mergeVars[] = array(
            'value' => $listId . '_email_address',
            'label' => 'Email' . ' <small style="color:red">(required)</small>',
        );

        // Loop over the fields and...
        foreach ($mergeVarsCollection as $mergeVar) {
            // If the has required text...
            if (true == $mergeVar->getRequired()) {
                // ...add html to apply a required tag.
                $required_text = ' <small style="color:red">(required)</small>';
            } else {
                // ...otherwise leave this variable empty.
                $required_text = '';
            }

            // Build our fields array.
            $mergeVars[] = array(
                'value' => $listId . '_' . $mergeVar->getTag(),
                'label' => $mergeVar->getName() . $required_text
            );
        }

        // Added by SRS
        // @todo: deliver this value externally - it is shared with form autogeneration
        // so use a single source for better maintained code  see AutogenerateForm
        $mergeVars[] = array(
            'value' => $listId . '_interests',
            'label' => 'User Selected Interests'
        );

        $mergeVars[] = array(
            'value' => $listId . '_tags',
            'label' => 'Tags, comma-separated'
        );

        return $mergeVars;
    }
}
