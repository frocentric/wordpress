<?php 
namespace NFMailchimp\EmailCRM\Mailchimp\ApiRequests;

use Exception;
/*
 * Custom Mailchimp API exception.
 *
 * @package Mailchimp
 */
class MailchimpApiException extends Exception {

  /**
   * @inheritdoc
   */
  public function __construct($message = "", $code = 0, Exception $previous = NULL) {
    // Construct message from JSON if required.
    if (substr($message, 0, 1) == '{') {
      $message_obj = json_decode($message);
      $message = $message_obj->status . ': ' . $message_obj->title;
      if (!empty($message_obj->detail)) {
        $message .= ' - ' . $message_obj->detail;
      }
      if (!empty($message_obj->errors) && is_array($message_obj->errors)) {
        foreach($message_obj->errors as $errorObject){
          $message .= ' ' . serialize($errorObject);
        }
      }
    }
    parent::__construct($message, $code, $previous);
  }

}