<?php
/**
 * Copyright 2019 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes
 */

/**
 * Generate AWS signed request URL.
 */
class Feedzy_Rss_Feeds_Pro_AWS_Request_URL {

	/**
	 * API access key.
	 *
	 * @var string $access_key
	 */
	private $access_key = null;

	/**
	 * API secret key.
	 *
	 * @var string $secret_key
	 */
	private $secret_key = null;

	/**
	 * API request path.
	 *
	 * @var string $path
	 */
	private $path = null;

	/**
	 * Region Name.
	 *
	 * @var string $region_name
	 */
	private $region_name = null;

	/**
	 * Region Name.
	 *
	 * @var string $region_name
	 */
	private $service_name = null;

	/**
	 * HTTP request method.
	 *
	 * @var string $http_method_name
	 */
	private $http_method_name = null;

	/**
	 * URL query param.
	 *
	 * @var array $query_parametes
	 */
	private $query_parametes = array();

	/**
	 * Request API headers.
	 *
	 * @var array $headers
	 */
	private $headers = array();

	/**
	 * Request payload data.
	 *
	 * @var string $payload
	 */
	private $payload = '';

	/**
	 * Signed request token algorithm.
	 *
	 * @var string $hmaca_algorithm
	 */
	private $hmaca_algorithm = 'AWS4-HMAC-SHA256';

	/**
	 * Request type.
	 *
	 * @var string $request
	 */
	private $request = 'aws4_request';

	/**
	 * Signed request header.
	 *
	 * @var string $signed_header
	 */
	private $signed_header = null;

	/**
	 * Request date.
	 *
	 * @var string $xamz_date
	 */
	private $xamz_date = null;

	/**
	 * Current date.
	 *
	 * @var string $current_date
	 */
	private $current_date = null;

	/**
	 * Construct method.
	 *
	 * @param string $access_key Access key.
	 * @param string $secret_key Secret key.
	 */
	public function __construct( $access_key, $secret_key ) {
		$this->access_key   = $access_key;
		$this->secret_key   = $secret_key;
		$this->xamz_date    = $this->get_time_stamp();
		$this->current_date = $this->get_date();
	}

	/**
	 * Set request path.
	 *
	 * @param string $path Request path.
	 */
	public function set_path( $path ) {
		$this->path = $path;
	}

	/**
	 * Set service name.
	 *
	 * @param string $service_name Service name.
	 */
	public function set_service_name( $service_name ) {
		$this->service_name = $service_name;
	}

	/**
	 * Set region name.
	 *
	 * @param string $region_name Region name.
	 */
	public function set_region_name( $region_name ) {
		$this->region_name = $region_name;
	}

	/**
	 * Set payload data.
	 *
	 * @param string $payload Region name.
	 */
	public function set_pay_load( $payload ) {
		$this->payload = $payload;
	}

	/**
	 * Set request method type.
	 *
	 * @param string $method Request method type.
	 */
	public function set_request_method( $method ) {
		$this->http_method_name = $method;
	}

	/**
	 * Add header.
	 *
	 * @param string $key   Header key.
	 * @param string $value Header value.
	 */
	public function add_header( $key, $value ) {
		$this->headers[ $key ] = $value;
	}

	/**
	 * Prepare request.
	 *
	 * @return string Canonical URL.
	 */
	private function prepare_canonical_request() {
		$canonical_url  = '';
		$canonical_url .= $this->http_method_name . "\n";
		$canonical_url .= $this->path . "\n" . "\n";
		$signed_headers = '';
		foreach ( $this->headers as $key => $value ) {
			$signed_headers .= $key . ';';
			$canonical_url  .= $key . ':' . $value . "\n";
		}
		$canonical_url      .= "\n";
		$this->signed_header = substr( $signed_headers, 0, - 1 );
		$canonical_url      .= $this->signed_header . "\n";
		$canonical_url      .= $this->generate_hex( $this->payload );
		return $canonical_url;
	}

	/**
	 * Prepare string to sign.
	 *
	 * @param string $canonical_url Canonical URL.
	 * @return string sign request string.
	 */
	private function prepare_str_to_sign( $canonical_url ) {
		$str_to_sign  = '';
		$str_to_sign .= $this->hmaca_algorithm . "\n";
		$str_to_sign .= $this->xamz_date . "\n";
		$str_to_sign .= $this->current_date . '/' . $this->region_name . '/' . $this->service_name . '/' . $this->request . "\n";
		$str_to_sign .= $this->generate_hex( $canonical_url );
		return $str_to_sign;
	}

	/**
	 * Calculate signature.
	 *
	 * @param string $sign_str Sign string.
	 * @return string sign hex.
	 */
	private function calculate_signature( $sign_str ) {
		$signature_key = $this->get_signature_key( $this->secret_key, $this->current_date, $this->region_name, $this->service_name );
		$signature     = hash_hmac( 'sha256', $sign_str, $signature_key, true );
		return strtolower( bin2hex( $signature ) );
	}

	/**
	 * Get request header.
	 *
	 * @return array Request headers.
	 */
	public function get_headers() {
		$this->headers ['x-amz-date'] = $this->xamz_date;
		ksort( $this->headers );
		$canonical_url  = $this->prepare_canonical_request();
		$string_to_sign = $this->prepare_str_to_sign( $canonical_url );
		$signature      = $this->calculate_signature( $string_to_sign );
		if ( $signature ) {
			$this->headers ['Authorization'] = $this->build_authorization_string( $signature );
			return $this->headers;
		}
	}

	/**
	 * Build authorization string.
	 *
	 * @param string $signature Request signature.
	 * @return string Signature.
	 */
	private function build_authorization_string( $signature ) {
		// phpcs:ignore Generic.Strings.UnnecessaryStringConcat.Found
		return $this->hmaca_algorithm . ' ' . 'Credential=' . $this->access_key . '/' . $this->get_date() . '/' . $this->region_name . '/' . $this->service_name . '/' . $this->request . ',' . 'SignedHeaders=' . $this->signed_header . ',' . 'Signature=' . $signature;
	}

	/**
	 * Generate HEX.
	 *
	 * @param string $data Data.
	 * @return string HEX.
	 */
	private function generate_hex( $data ) {
		return strtolower( bin2hex( hash( 'sha256', $data, true ) ) );
	}

	/**
	 * Get signature key.
	 *
	 * @param string $key Key.
	 * @param string $date Date.
	 * @param string $region_name Region name.
	 * @param string $service_name Service name.
	 * @return string
	 */
	private function get_signature_key( $key, $date, $region_name, $service_name ) {
		$key_secret  = 'AWS4' . $key;
		$key_date    = hash_hmac( 'sha256', $date, $key_secret, true );
		$key_region  = hash_hmac( 'sha256', $region_name, $key_date, true );
		$key_service = hash_hmac( 'sha256', $service_name, $key_region, true );
		$key_signing = hash_hmac( 'sha256', $this->request, $key_service, true );
		return $key_signing;
	}

	/**
	 * Get Time stamp.
	 */
	private function get_time_stamp() {
		return gmdate( 'Ymd\THis\Z' );
	}

	/**
	 * Get date.
	 */
	private function get_date() {
		return gmdate( 'Ymd' );
	}
}
