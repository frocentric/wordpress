<?php


namespace NFMailchimp\EmailCRM\WpBridge\Http;

use NFMailchimp\EmailCRM\Mailchimp\ApiRequests\MailchimpApiException;
use NFMailchimp\EmailCRM\Mailchimp\Contracts\NfMailchimpHttpClientInterface;
/**
 * Class MailchimpWpHttpClient
 *
 * For the Mailchimp API client -- uses WordPress HTTP API
 */
class MailchimpWpHttpClient implements NfMailchimpHttpClientInterface
{
	/**
	 * @inheritdoc
	 */
	public function handleRequest($method, $uri = '', $options = [], $parameters = [], $returnAssoc = false)
	{
		$options['method'] = $method;

		if (!empty($parameters)) {
			if ($method == 'GET') {
				// Send parameters as query string parameters.
				$uri = add_query_arg($parameters, $uri);
			} else {
				// Send parameters as JSON in request body.
				$options['body'] = json_encode($parameters);
			}
		}

		$response = wp_remote_request($uri, $options);

		if (is_wp_error($response)) {

			throw new MailchimpAPIException($response->get_error_message(),(int) $response->get_error_code());
		} else {

			$status = (string)wp_remote_retrieve_response_code($response);

			$message =(string)wp_remote_retrieve_body($response);
			
			if ('DELETE' === $method) {
				if ('204' === $status) {
					return true;
				}
				return false;
			}
			if (2 == substr($status, 0, 1)) {
				return json_decode(wp_remote_retrieve_body($response));
			}
			throw new MailchimpAPIException($message, $status);
		}
	}
}
