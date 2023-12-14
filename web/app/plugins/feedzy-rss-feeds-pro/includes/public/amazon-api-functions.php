<?php
/**
 * Amazon product advertising API functions that can help in fetch data.
 *
 * @package feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes/public
 */

if ( ! function_exists( 'feedzy_amazon_get_locale_hosts' ) ) {

	/**
	 * Amazon locale hosts.
	 *
	 * @return array.
	 */
	function feedzy_amazon_get_locale_hosts() {
		$hosts = array(
			'US' => 'webservices.amazon.com',
			'AU' => 'webservices.amazon.com.au',
			'BR' => 'webservices.amazon.com.br',
			'CA' => 'webservices.amazon.ca',
			'FR' => 'webservices.amazon.fr',
			'DE' => 'webservices.amazon.de',
			'IN' => 'webservices.amazon.in',
			'IT' => 'webservices.amazon.it',
			'JP' => 'webservices.amazon.co.jp',
			'MX' => 'webservices.amazon.com.mx',
			'NL' => 'webservices.amazon.nl',
			'PL' => 'webservices.amazon.pl',
			'SG' => 'webservices.amazon.sg',
			'SA' => 'webservices.amazon.sa',
			'ES' => 'webservices.amazon.es',
			'SE' => 'webservices.amazon.se',
			'TR' => 'webservices.amazon.com.tr',
			'AE' => 'webservices.amazon.ae',
			'UK' => 'webservices.amazon.co.uk',
		);

		return apply_filters( 'feedzy_amazon_locale_hosts', $hosts );
	}
}

if ( ! function_exists( 'feedzy_amazon_get_get_locale_regions' ) ) {

	/**
	 * Amazon locale regions.
	 *
	 * @return array.
	 */
	function feedzy_amazon_get_get_locale_regions() {
		$regions = array(
			'US' => 'us-east-1',
			'AU' => 'us-west-2',
			'BR' => 'us-east-1',
			'CA' => 'us-east-1',
			'FR' => 'eu-west-1',
			'DE' => 'eu-west-1',
			'IN' => 'eu-west-1',
			'IT' => 'eu-west-1',
			'JP' => 'us-west-2',
			'MX' => 'us-east-1',
			'NL' => 'eu-west-1',
			'PL' => 'eu-west-1',
			'SG' => 'us-west-2',
			'SA' => 'eu-west-1',
			'ES' => 'eu-west-1',
			'SE' => 'eu-west-1',
			'TR' => 'eu-west-1',
			'AE' => 'eu-west-1',
			'UK' => 'eu-west-1',
		);

		return apply_filters( 'feedzy_amazon_locale_regions', $regions );
	}
}
