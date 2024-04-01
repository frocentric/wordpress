<?php
/**
 * The amazon product advertising service functionality. The extended methods for PRO.
 *
 * @link       http://themeisle.com
 * @since      1.7.1
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes/admin
 */

use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\api\DefaultApi;
use Amazon\ProductAdvertisingAPI\v1\ApiException;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsResource;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetItemsResource;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\PartnerType;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\ProductAdvertisingAPIClientException;
use Amazon\ProductAdvertisingAPI\v1\ObjectSerializer;

/**
 * Class Feedzy_Rss_Feeds_Pro_Amazon_Product_Advertising
 */
class Feedzy_Rss_Feeds_Pro_Amazon_Product_Advertising implements Feedzy_Rss_Feeds_Pro_Services_Interface {

	const API_URL = 'webservices.#host#';

	/**
	 * The API URL
	 *
	 * @var string The URL with keys replaced from const API_URL.
	 */
	private $url = '';

	/**
	 * The API options.
	 *
	 * @since   1.7.1
	 * @access  private
	 * @var     array $options The API options.
	 */
	private $options = array();

	/**
	 * The API errors.
	 *
	 * @since   1.7.1
	 * @access  private
	 * @var     array $errors The API errors.
	 */
	private $errors = array();

	/**
	 * Amazon items.
	 *
	 * @var array $items Items.
	 */
	private $items = array();

	/**
	 * Init the API.
	 *
	 * @since 1.7.1
	 * @access  public
	 * @param string $access_key The amazon access key.
	 * @param string $secret_key The amazon secret key.
	 * @param string $partner_tag The amazon partner tag.
	 * @param string $host_url The amazon host URl.
	 */
	public function init( $access_key = '', $secret_key = '', $partner_tag = '', $host_url = '' ) {
		$this->set_api_option( 'access_key', $access_key );
		$this->set_api_option( 'secret_key', $secret_key );
		$this->set_api_option( 'partner_tag', $partner_tag );
		$this->url = str_replace( array( '#host#' ), is_array( $host_url ) ? $host_url : array( $host_url ), self::API_URL );
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return ! empty( $this->options );
		}
	}

	/**
	 * Set an option key and value.
	 *
	 * @access  public
	 * @param   string $key The option key.
	 * @param   string $value The option value.
	 */
	public function set_api_option( $key, $value ) {
		$this->options[ $key ] = $value;
	}

	/**
	 * Get an option by key.
	 *
	 * @access  public
	 * @param   string $key The option key.
	 * @return bool|mixed
	 */
	public function get_api_option( $key ) {
		if ( isset( $this->options[ $key ] ) ) {
			return $this->options[ $key ];
		}
		return false;
	}

	/**
	 * Verify API status.
	 *
	 * @access  public
	 * @param array $post_data Post data.
	 * @param array $settings Settings.
	 */
	public function check_api( &$post_data = '', $settings = array() ) {
		if ( ( empty( $post_data['amazon_access_key'] ) || empty( $post_data['amazon_secret_key'] )
				|| empty( $post_data['amazon_partner_tag'] ) )
		) {
			return;
		}

		$locale_hosts = feedzy_amazon_get_locale_hosts();
		$region       = feedzy_amazon_get_get_locale_regions();
		$this->url    = $post_data['amazon_host'];
		$locale_hosts = array_search( $this->url, $locale_hosts, true );
		// Parse query param.
		parse_str( wp_parse_url( $this->url, PHP_URL_QUERY ), $query_param );
		// Get region from host URL.
		if ( false !== $locale_hosts ) {
			$region = isset( $region[ $locale_hosts ] ) ? $region[ $locale_hosts ] : reset( $region );
		} else {
			$region = reset( $region );
		}
		$query_param = array_change_key_case( $query_param, CASE_LOWER );
		$resource    = new SearchItemsResource();
		$payload     = array(
			'PartnerType' => PartnerType::ASSOCIATES,
			'PartnerTag'  => $post_data['amazon_partner_tag'],
			'Keywords'    => 'Laptop',
			'SearchIndex' => 'All',
			'Resources'   => array(
				$resource::ITEM_INFOTITLE,
			),
		);

		$host      = wp_parse_url( $this->url, PHP_URL_PATH );
		$host      = rtrim( $host, '/' );
		$host_path = pathinfo( $host, PATHINFO_EXTENSION );

		$host      = wp_parse_url( $this->url, PHP_URL_PATH );
		$host      = rtrim( $host, '/' );
		$host_path = pathinfo( $host, PATHINFO_EXTENSION );

		// Get product using keyword search.
		$path       = '/paapi5/searchitems';
		$amz_target = $host_path . '.amazon.paapi5.v1.ProductAdvertisingAPIv1.SearchItems';

		$payload = wp_json_encode( $payload );
		$awsv4   = new \Feedzy_Rss_Feeds_Pro_AWS_Request_URL( $post_data['amazon_access_key'], $post_data['amazon_secret_key'] );
		$awsv4->set_region_name( $region );
		$awsv4->set_service_name( 'ProductAdvertisingAPI' );
		$awsv4->set_path( $path );
		$awsv4->set_pay_load( $payload );
		$awsv4->set_request_method( 'POST' );
		$awsv4->add_header( 'content-encoding', 'amz-1.0' );
		$awsv4->add_header( 'content-type', 'application/json; charset=utf-8' );
		$awsv4->add_header( 'host', $host );
		$awsv4->add_header( 'x-amz-target', $amz_target );
		$headers = $awsv4->get_headers();

		$request_url = 'https://' . $host . $path;
		$response    = wp_remote_post(
			$request_url,
			array(
				'timeout' => 100,
				'headers' => $headers,
				'body'    => $payload,
			)
		);

		$status_code = wp_remote_retrieve_response_code( $response );
		$data        = json_decode( wp_remote_retrieve_body( $response ) );
		if ( ! is_wp_error( $response ) && 200 === $status_code ) {
			// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
			$post_data['aws_last_check'] = date( 'd/m/Y H:i:s' );
			$post_data['aws_message']    = '';
			$post_data['aws_licence']    = 'yes';
		} else {
			$message = '';
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			if ( $data && $data->Errors ) {
				// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$errors = reset( $data->Errors );
				// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$message = $errors->Message ? $errors->Message : '';
			}
			$post_data['aws_message'] = $message;
			$post_data['aws_licence'] = 'no';
		}
	}

	/**
	 * Set config option.
	 *
	 * @access  public
	 * @param array $host_url Amazon host URL.
	 * @param array $settings Settings.
	 */
	public function set_config_option( &$host_url, $settings ) {
		if ( ! empty( $settings['amazon_access_key'] ) && ! empty( $settings['amazon_secret_key'] ) && ! empty( $settings['amazon_partner_tag'] ) ) {
				$this->init( $settings['amazon_access_key'], $settings['amazon_secret_key'], $settings['amazon_partner_tag'], $host_url );
		}
	}

	/**
	 * Call API.
	 *
	 * @access  public
	 * @param   array  $access_key API access key.
	 * @param   string $secret_key API secret key.
	 * @param   string $partner_tag Amazon partner tag.
	 * @param   array  $additional Additional parameters.
	 * @return bool|mixed
	 */
	public function call_api( $access_key, $secret_key, $partner_tag, $additional = array() ) {
		// If check is valid license plan or not.
		if ( ! $this->feedzy_is_business() ) {
			$this->errors[] = sprintf( __( 'Invalid license plan, <a href="%1$s" target="_blank">%2$s</a>.', 'feedzy-rss-feeds' ), FEEDZY_UPSELL_LINK, esc_html__( 'Please upgrade plan', 'feedzy-rss-feeds' ) );
			return $this;
		}
		// if license does not exist, use the site url
		// this should obviously never happen unless on dev instances.
		$license = apply_filters( 'product_feedzy_license_key', sprintf( 'n/a - %s', get_site_url() ) );

		$number_of_item = ! empty( $additional['number_of_item'] ) ? (int) $additional['number_of_item'] : 10;
		$cache_time     = ! empty( $additional['refresh'] ) ? $additional['refresh'] : '12_hours';
		$locale_hosts   = feedzy_amazon_get_locale_hosts();
		$region         = feedzy_amazon_get_get_locale_regions();
		$locale_hosts   = array_search( $this->url, $locale_hosts, true );
		$cache_time     = $this->get_cache_time( $cache_time );
		$cache_key      = sanitize_key( $license . '_' . $access_key . '_' . $this->url ); // Cache key.
		$unique_key     = 'feed_aws_' . $number_of_item . wp_hash( $cache_key );

		$cache_data = get_transient( $unique_key );
		if ( ! empty( $additional['no-cache'] ) && false !== $cache_data ) {
			delete_transient( $unique_key );
			$cache_data = false;
		}

		// Check cache data exists or not.
		if ( ! empty( $cache_data ) ) {
			$this->set_items( $cache_data );
			return $this;
		}

		// Parse query param.
		parse_str( wp_parse_url( $this->url, PHP_URL_QUERY ), $query_param );
		// Get region from host URL.
		if ( false !== $locale_hosts ) {
			$region = isset( $region[ $locale_hosts ] ) ? $region[ $locale_hosts ] : reset( $region );
		} else {
			$region = reset( $region );
		}

		$query_param  = array_change_key_case( $query_param, CASE_LOWER );
		$keyword      = ! empty( $query_param['keyword'] ) ? $query_param['keyword'] : '';
		$product_asin = ! empty( $query_param['asin'] ) ? explode( '|', $query_param['asin'] ) : array();
		$product_asin = array_map( 'trim', $product_asin );
		$search_index = 'All';
		$payload      = array(
			'PartnerType' => PartnerType::ASSOCIATES,
			'PartnerTag'  => $partner_tag,
			'Keywords'    => $keyword,
			'SearchIndex' => 'All',
		);
		if ( ! empty( $product_asin ) ) {
			unset( $payload['Keywords'] );
			unset( $payload['SearchIndex'] );
			$number_of_item = count( $product_asin );
		}

		$host      = wp_parse_url( $this->url, PHP_URL_PATH );
		$host      = rtrim( $host, '/' );
		$host_path = pathinfo( $host, PATHINFO_EXTENSION );
		// Get product using keyword search.
		$path        = '/paapi5/searchitems';
		$amz_target  = $host_path . '.amazon.paapi5.v1.ProductAdvertisingAPIv1.SearchItems';
		$return_type = '\Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsResponse';
		$resource    = new SearchItemsResource();
		// Get product using ASIN.
		if ( ! empty( $product_asin ) ) {
			$path        = '/paapi5/getitems';
			$amz_target  = $host_path . '.amazon.paapi5.v1.ProductAdvertisingAPIv1.GetItems';
			$return_type = '\Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetItemsResponse';
			$resource    = new GetItemsResource();
		}

		$items        = array();
		$product_asin = array_chunk( $product_asin, 10 );
		foreach ( range( 1, ceil( $number_of_item / 10 ) ) as $request ) {
			$payload['Resources'] = array(
				$resource::ITEM_INFOTITLE,
				$resource::OFFERSLISTINGSPRICE,
				$resource::ITEM_INFOPRODUCT_INFO,
				$resource::ITEM_INFOCONTENT_INFO,
				$resource::IMAGESPRIMARYLARGE,
				$resource::ITEM_INFOFEATURES,
				$resource::ITEM_INFOBY_LINE_INFO,
				$resource::ITEM_INFOCONTENT_RATING,
				$resource::BROWSE_NODE_INFOBROWSE_NODES,
			);

			if ( ! empty( $product_asin ) ) {
				$payload['ItemIds'] = array_shift( $product_asin );
			}

			$payload = wp_json_encode( $payload );
			$awsv4   = new \Feedzy_Rss_Feeds_Pro_AWS_Request_URL( $access_key, $secret_key );
			$awsv4->set_region_name( $region );
			$awsv4->set_service_name( 'ProductAdvertisingAPI' );
			$awsv4->set_path( $path );
			$awsv4->set_pay_load( $payload );
			$awsv4->set_request_method( 'POST' );
			$awsv4->add_header( 'content-encoding', 'amz-1.0' );
			$awsv4->add_header( 'content-type', 'application/json; charset=utf-8' );
			$awsv4->add_header( 'host', $host );
			$awsv4->add_header( 'x-amz-target', $amz_target );
			$headers = $awsv4->get_headers();

			$request_url = 'https://' . $host . $path;
			$post_data   = array(
				'request_url' => $request_url,
				'headers'     => $headers,
				'payload'     => $payload,
				'license'     => $license,
				'unique_key'  => $cache_key,
			);
			$response    = wp_remote_post(
				FEEDZY_PRO_AWS_PRODUCT_API,
				array(
					'timeout' => 100,
					'body'    => $post_data,
				)
			);

			$status_code = wp_remote_retrieve_response_code( $response );

			if ( is_wp_error( $response ) ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'error in response = %s', print_r( $response, true ) ), 'error', __FILE__, __LINE__ );
				$this->errors[] = $response->get_error_message();
				return $this;
			}

			// Check API response code.
			if ( 200 !== $status_code ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'error in response = %s', print_r( $response, true ) ), 'error', __FILE__, __LINE__ );
				return $this;
			}
			// Get API response.
			$response = json_decode( wp_remote_retrieve_body( $response ) );
			$response = isset( $response->aws_products ) ? $response->aws_products : array();
			$result   = ObjectSerializer::deserialize( $response, $return_type, array() );

			try {
				if ( empty( $result->getErrors() ) ) {
					if ( method_exists( $result, 'getItemsResult' ) ) {
						if ( $result->getItemsResult() ) {
							$items[] = $result->getItemsResult()->getItems();
						}
					} else {
						if ( $result->getSearchResult() ) {
							$items[] = $result->getSearchResult()->getItems();
						}
					}
				} else {
					$this->errors[] = $result->getErrors();
				}
			} catch ( ApiException $exception ) {
				if ( $exception->getResponseObject() instanceof ProductAdvertisingAPIClientException ) {
					$this->errors[] = $exception->getResponseObject()->getErrors()[0]->getMessage();
				} else {
					if ( $exception->getResponseBody() ) {
						$this->errors[] = $exception->getResponseBody();
					}
				}
				if ( empty( $this->errors ) ) {
					$this->errors[] = $exception->getMessage();
				}
			} catch ( Exception $exception ) {
				$this->errors[] = $exception->getMessage();
			}
		}

		$items = empty( $this->errors ) ? array_reduce( $items, 'array_merge', array() ) : array();
		$this->set_items( $items );

		if ( empty( $additional['no-cache'] ) ) {
			set_transient( $unique_key, $this->items, $cache_time );
		}
		return $this;
	}

	/**
	 * Get cache time.
	 *
	 * @param string $cache Cache Time.
	 * @return int
	 */
	public function get_cache_time( $cache = '12_hours' ) {
		$unit_defaults = array(
			'mins'  => MINUTE_IN_SECONDS,
			'hours' => HOUR_IN_SECONDS,
			'days'  => DAY_IN_SECONDS,
		);
		$cache_time    = 12 * HOUR_IN_SECONDS;
		$cache         = trim( $cache );
		if ( isset( $cache ) && '' !== $cache ) {
			list( $value, $unit ) = explode( '_', $cache );
			if ( isset( $value ) && is_numeric( $value ) && $value >= 1 && $value <= 100 ) {
				if ( isset( $unit ) && in_array( strtolower( $unit ), array( 'mins', 'hours', 'days' ), true ) ) {
					$cache_time = $value * $unit_defaults[ $unit ];
				}
			}
		}
		return $cache_time;
	}

	/**
	 * Return erros.
	 *
	 * @since   1.7.1
	 * @access  public
	 * @return array|bool
	 */
	public function get_api_errors() {
		if ( count( $this->errors ) > 0 ) {
			return $this->errors;
		}
		return false;
	}

	/**
	 * Returns the service name.
	 *
	 * @access  public
	 */
	public function get_service_slug() {
		return 'amazon-product-advertising';
	}

	/**
	 * Returns the proper service name.
	 *
	 * @access  public
	 */
	public function get_service_name_proper() {
		return __( 'Amazon Product Advertising', 'feedzy-rss-feeds' );
	}

	/**
	 * Get items.
	 *
	 * @param int $offset Offset.
	 * @return array
	 */
	public function get_items( $offset = 0 ) {
		$items = array();
		if ( ! empty( $this->items ) ) {
			$this->items = array_slice( $this->items, $offset );
			foreach ( $this->items as $key => $item ) {
				$source_loader = new Feedzy_Rss_Feeds_Pro_Source_Loader();
				$items[]       = $source_loader->get_source( $item );
			}
		}
		return $items;
	}

	/**
	 * Set items.
	 *
	 * @param array $items products.
	 * @return void
	 */
	public function set_items( $items = array() ) {
		$this->items = array_merge( $this->items, $items );
	}

	/**
	 * Get API errors.
	 *
	 * @return array
	 */
	public function get_errors() {
		return $this->errors;
	}

	/**
	 * Get source URL.
	 *
	 * @return string URL.
	 */
	public function get_permalink() {
		return $this->url;
	}

	/**
	 * Get source language code.
	 */
	public function get_language() {
		if ( ! empty( $this->items[0] ) ) {
			$this->items[0]->getItemInfo()->getByLineInfo()->getBrand()->getLocale();
		}
		return get_locale();
	}

	/**
	 * Get source title.
	 *
	 * @return string Title.
	 */
	public function get_title() {
		return __( 'Amazon', 'feedzy-rss-feeds' );
	}

	/**
	 * Get source URL.
	 */
	public function subscribe_url() {
		return null;
	}

	/**
	 * Get source description.
	 *
	 * @return mix
	 */
	public function get_description() {
		return '';
	}

	/**
	 * Get source error.
	 *
	 * @return array Errors.
	 */
	public function error() {
		$errors = $this->get_errors();
		$errors = reset( $errors );
		return wp_sprintf( __( '[Amazon Product Advertising API] %s', 'feedzy-rss-feeds' ), $errors );
	}

	/**
	 * Check is valid license plan.
	 */
	public function feedzy_is_business() {
		return apply_filters( 'feedzy_is_license_of_type', false, 'business' );
	}
}
