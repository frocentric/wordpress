<?php

namespace NFMailchimp\EmailCRM\Shared\Contracts;

/**
 * Make an HTTP request
 *
 */
interface RemoteRequestContract
{


	/**
	 * Set HTTP request URL
	 *
	 * @param string $url
	 * @return \NFMailchimp\EmailCRM\WpBridge\Contracts\RemoteRequestContract
	 */
	public function setUrl(string $url): RemoteRequestContract;

	/**
	 * Set an HTTP argument
	 *
	 *
	 * @param string $arg
	 * @param type $value
	 * @return \NFMailchimp\EmailCRM\WpBridge\Contracts\RemoteRequestContract
	 */
	public function setHttpArg(string $arg, $value): RemoteRequestContract;


	/**
	 * Add a querystring argument
	 *
	 *
	 * @param string $arg
	 * @param type $value
	 * @return \NFMailchimp\EmailCRM\WpBridge\Contracts\RemoteRequestContract
	 */
	public function addQueryArg(string $arg, $value): RemoteRequestContract;

	/**
	 * Set HTTP request body
	 *
	 * @param type $body
	 * @return \NFMailchimp\EmailCRM\WpBridge\Contracts\RemoteRequestContract
	 */
	public function setBody($body): RemoteRequestContract;
	
	/**
	 * Set an HTTP header parameter
	*
	* @param type $arg
	* @param type $value
	* @return \NFMailchimp\EmailCRM\WpBridge\Contracts\RemoteRequestContract
	*/
	public function setHeaderParameter($arg, $value): RemoteRequestContract;
	

	public function handle();
}
