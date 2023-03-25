<?php

namespace NFMailchimp\EmailCRM\Mailchimp;

use NFMailchimp\EmailCRM\Mailchimp\Interfaces\MailchimpApi as InterfacesMailchimpApi;
use NFMailchimp\EmailCRM\Mailchimp\Contracts\NfMailchimpHttpClientInterface;
use NFMailchimp\EmailCRM\WpBridge\Http\MailchimpWpHttpClient;

class MailchimpApi implements InterfacesMailchimpApi
{
  const VERSION = '2.0.0';
  const DEFAULT_DATA_CENTER = 'us1';
  const MEMBER_STATUS_SUBSCRIBED = 'subscribed';
  const MEMBER_STATUS_UNSUBSCRIBED = 'unsubscribed';
  const MEMBER_STATUS_CLEANED = 'cleaned';
  const MEMBER_STATUS_PENDING = 'pending';

  /**
   * The REST API endpoint.
   *
   * @var string $endpoint
   */
  protected $endpoint = 'https://us1.api.mailchimp.com/3.0';

  /** @var NfMailchimpHttpClientInterface */
  protected $client;

  /**
   * Set API key determining account access in Mailchimp
   *
   * @param string $api_key
   * @param NfMailchimpHttpClientInterface|null $client
   * @return void
   */
  public function setApiKey(string $api_key): MailchimpApi
  {
    $this->api_key = $api_key;
    $this->api_user = 'api_key';

    $dc = $this->getDataCenter($this->api_key);

    $this->endpoint = str_replace(self::DEFAULT_DATA_CENTER, $dc, $this->endpoint);

    return $this;
  }

  /**
   * Return HTTP client
   *
   * @return NfMailchimpHttpClientInterface
   */
  private function getClient(): NfMailchimpHttpClientInterface
  {
    if (is_null($this->client)) {

      $this->client = new MailchimpWpHttpClient();
    }

    return $this->client;
  }


  /**
   * Gets the ID of the data center associated with an API key.
   *
   * @param string $api_key
   *   The Mailchimp API key.
   *
   * @return string
   *   The data center ID.
   */
  private function getDataCenter($api_key)
  {
    $api_key_parts = explode('-', $api_key);

    return (isset($api_key_parts[1])) ? $api_key_parts[1] : self::DEFAULT_DATA_CENTER;
  }

  /**
   * Gets information about all lists owned by the authenticated account.
   *
   * @param array $parameters
   *   Associative array of optional request parameters.
   *
   * @return object
   *
   * @see http://developer.mailchimp.com/documentation/mailchimp/reference/lists/#read-get_lists
   */
  public function getLists($parameters = [])
  {
    return $this->request('GET', '/lists', NULL, $parameters);
  }

  /**
   * Gets a Mailchimp list.
   *
   * @param string $list_id
   *   The ID of the list.
   * @param array $parameters
   *   Associative array of optional request parameters.
   *
   * @return object
   *
   * @see http://developer.mailchimp.com/documentation/mailchimp/reference/lists/#read-get_lists_list_id
   */
  public function getList($list_id, $parameters = [])
  {
    $tokens = [
      'list_id' => $list_id,
    ];

    return $this->request('GET', '/lists/{list_id}', $tokens, $parameters);
  }

  /**
   * Gets information about all interest categories associated with a list.
   *
   * @param string $list_id
   *   The ID of the list.
   * @param array $parameters
   *   Associative array of optional request parameters.
   *
   * @return object
   *
   * @see http://developer.mailchimp.com/documentation/mailchimp/reference/lists/interest-categories/#read-get_lists_list_id_interest_categories
   */
  public function getInterestCategories($list_id, $parameters = [])
  {
    $tokens = [
      'list_id' => $list_id,
    ];

    return $this->request('GET', '/lists/{list_id}/interest-categories', $tokens, $parameters);
  }


  /**
   * Gets information about all interests associated with an interest category.
   *
   * @param string $list_id
   *   The ID of the list.
   * @param string $interest_category_id
   *   The ID of the interest category.
   * @param array $parameters
   *   Associative array of optional request parameters.
   *
   * @return object
   *
   * @see http://developer.mailchimp.com/documentation/mailchimp/reference/lists/interest-categories/interests/#read-get_lists_list_id_interest_categories_interest_category_id_interests
   */
  public function getInterests($list_id, $interest_category_id, $parameters = [])
  {
    $tokens = [
      'list_id' => $list_id,
      'interest_category_id' => $interest_category_id,
    ];

    return $this->request('GET', '/lists/{list_id}/interest-categories/{interest_category_id}/interests', $tokens, $parameters);
  }



  /**
   * Gets merge fields associated with a Mailchimp list.
   *
   * @param string $list_id
   *   The ID of the list.
   * @param array $parameters
   *   Associative array of optional request parameters.
   *
   * @return object
   *
   * @see http://developer.mailchimp.com/documentation/mailchimp/reference/lists/merge-fields/#read-get_lists_list_id_merge_fields
   */
  public function getMergeFields($list_id, $parameters = [])
  {
    $tokens = [
      'list_id' => $list_id,
    ];

    return $this->request('GET', '/lists/{list_id}/merge-fields', $tokens, $parameters);
  }



  /**
   * Adds a new or update an existing member of a Mailchimp list.
   *
   * @param string $list_id
   *   The ID of the list.
   * @param string $email
   *   The member's email address.
   * @param array $parameters
   *   Associative array of optional request parameters.
   * @param bool $batch
   *   TRUE to create a new pending batch operation.
   *
   * @return object
   *
   * @see http://developer.mailchimp.com/documentation/mailchimp/reference/lists/members/#edit-put_lists_list_id_members_subscriber_hash
   */
  public function addOrUpdateMember($list_id, $email, $parameters = [])
  {
    $tokens = [
      'list_id' => $list_id,
      'subscriber_hash' => md5(strtolower($email)),
    ];

    $parameters += [
      'email_address' => $email,
    ];

    return $this->request('PUT', '/lists/{list_id}/members/{subscriber_hash}', $tokens, $parameters);
  }



  /**
   * Gets information about segments associated with a Mailchimp list.
   *
   * @param string $list_id
   *   The ID of the list.
   * @param array $parameters
   *   Associative array of optional request parameters.
   *
   * @return object
   *
   * @see http://developer.mailchimp.com/documentation/mailchimp/reference/lists/segments/#read-get_lists_list_id_segments
   */
  public function getSegments($list_id, $parameters = [])
  {
    $tokens = [
      'list_id' => $list_id,
    ];

    return $this->request('GET', '/lists/{list_id}/segments', $tokens, $parameters);
  }


  /**
   * Makes a request to the Mailchimp API.
   *
   * @param string $method
   *   The REST method to use when making the request.
   * @param string $path
   *   The API path to request.
   * @param array $tokens
   *   Associative array of tokens and values to replace in the path.
   * @param array $parameters
   *   Associative array of parameters to send in the request body.
   * @param bool $batch
   *   TRUE if this request should be added to pending batch operations.
   * @param bool $returnAssoc
   *   TRUE to return Mailchimp API response as an associative array.
   *
   * @return mixed
   *   Object or Array if $returnAssoc is TRUE.
   *
   * @throws MailchimpAPIException
   */
  public function request($method, $path, $tokens = NULL, $parameters = [])
  {
    if (!empty($tokens)) {
      foreach ($tokens as $key => $value) {
        $path = str_replace('{' . $key . '}', $value, $path);
      }
    }

    // Set default request options with auth header.
    $options = [
      'headers' => [
        'Authorization' => $this->api_user . ' ' . $this->api_key,
      ],
    ];

    // Add trigger error header if a debug error code has been set.
    if (!empty($this->debug_error_code)) {
      $options['headers']['X-Trigger-Error'] = $this->debug_error_code;
    }

    return $this->getClient()->handleRequest($method, $this->endpoint . $path, $options, $parameters);
  }

  /**
   * Set the value of client
   *
   * @return  MailchimpApi
   */
  public function setClient($client): MailchimpApi
  {
    $this->client = $client;

    return $this;
  }
}
