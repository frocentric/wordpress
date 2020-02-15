<?php
namespace ElementorPro\Modules\Forms\Actions;

use Elementor\Controls_Manager;
use Elementor\Settings;
use ElementorPro\Modules\Forms\Classes\Form_Record;
use ElementorPro\Modules\Forms\Controls\Fields_Map;
use ElementorPro\Modules\Forms\Classes;
use ElementorPro\Classes\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Activecampaign extends Classes\Integration_Base {

	const OPTION_NAME_API_KEY = 'pro_activecampaign_api_key';
	const OPTION_NAME_API_URL = 'pro_activecampaign_api_url';

	private function get_global_api_key() {
		return get_option( 'elementor_' . self::OPTION_NAME_API_KEY, '' );
	}

	private function get_global_api_url() {
		return get_option( 'elementor_' . self::OPTION_NAME_API_URL, '' );
	}

	public function get_name() {
		return 'activecampaign';
	}

	public function get_label() {
		return __( 'ActiveCampaign', 'elementor-pro' );
	}

	public function register_settings_section( $widget ) {
		$widget->start_controls_section(
			'section_activecampaign',
			[
				'label' => __( 'ActiveCampaign', 'elementor-pro' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		self::global_api_control(
			$widget,
			$this->get_global_api_key(),
			'ActiveCampaign API credentials',
			[
				'activecampaign_api_credentials_source' => 'default',
			],
			$this->get_name()
		);

		$widget->add_control(
			'activecampaign_api_credentials_source',
			[
				'label' => __( 'API Credentials', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'default' => 'Default',
					'custom' => 'Custom',
				],
				'default' => 'default',
			]
		);

		$widget->add_control(
			'activecampaign_api_key',
			[
				'label' => __( 'API Key', 'elementor-pro' ),
				'type' => Controls_Manager::TEXT,
				'description' => __( 'Use this field to set a custom API Key for the current form', 'elementor-pro' ),
				'condition' => [
					'activecampaign_api_credentials_source' => 'custom',
				],
			]
		);

		$widget->add_control(
			'activecampaign_api_url',
			[
				'label' => __( 'API URL', 'elementor-pro' ),
				'type' => Controls_Manager::TEXT,
				'description' => __( 'Use this field to set a custom API URL for the current form', 'elementor-pro' ),
				'condition' => [
					'activecampaign_api_credentials_source' => 'custom',
				],
			]
		);

		$widget->add_control(
			'activecampaign_list',
			[
				'label' => __( 'List', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'options' => [],
				'render_type' => 'none',
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'activecampaign_api_credentials_source',
							'operator' => '=',
							'value' => 'default',
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'activecampaign_api_url',
									'operator' => '!==',
									'value' => '',
								],
								[
									'name' => 'activecampaign_api_key',
									'operator' => '!==',
									'value' => '',
								],
							],
						],
					],
				],
			]
		);

		$widget->add_control(
			'activecampaign_fields_map',
			[
				'label' => __( 'Field Mapping', 'elementor-pro' ),
				'type' => Fields_Map::CONTROL_TYPE,
				'separator' => 'before',
				'fields' => [
					[
						'name' => 'remote_id',
						'type' => Controls_Manager::HIDDEN,
					],
					[
						'name' => 'local_id',
						'type' => Controls_Manager::SELECT,
					],
				],
				'condition' => [
					'activecampaign_list!' => '',
				],
			]
		);

		$widget->add_control(
			'activecampaign_tags',
			[
				'label' => __( 'Tags', 'elementor-pro' ),
				'type' => Controls_Manager::TEXT,
				'description' => __( 'Add as many tags as you want, comma separated.', 'elementor-pro' ),
				'condition' => [
					'activecampaign_list!' => '',
				],
			]
		);

		$widget->end_controls_section();
	}

	public function on_export( $element ) {
		unset(
			$element['settings']['activecampaign_api_credentials_source'],
			$element['settings']['activecampaign_api_key'],
			$element['settings']['activecampaign_api_url'],
			$element['settings']['activecampaign_list'],
			$element['settings']['activecampaign_fields_map'],
			$element['settings']['activecampaign_tags']
		);

		return $element;
	}

	public function run( $record, $ajax_handler ) {
		$form_settings = $record->get( 'form_settings' );
		$subscriber = $this->create_subscriber_object( $record );

		if ( ! $subscriber ) {
			$ajax_handler->add_admin_error_message( __( 'ActiveCampaign Integration requires an email field and a selected list', 'elementor-pro' ) );

			return;
		}

		if ( 'default' === $form_settings['activecampaign_api_credentials_source'] ) {
			$api_key = $this->get_global_api_key();
			$api_url = $this->get_global_api_url();
		} else {
			$api_key = $form_settings['activecampaign_api_key'];
			$api_url = $form_settings['activecampaign_api_url'];
		}

		try {
			$handler = new Classes\Activecampaign_Handler( $api_key, $api_url );
			$handler->create_subscriber( $subscriber );
		} catch ( \Exception $exception ) {
			$ajax_handler->add_admin_error_message( 'ActiveCampaign ' . $exception->getMessage() );
		}
	}

	/**
	 * Create subscriber array from submitted data and form settings
	 * returns a subscriber array or false on error
	 *
	 * @param Form_Record $record
	 *
	 * @return array|bool
	 */
	private function create_subscriber_object( Form_Record $record ) {
		$form_settings = $record->get( 'form_settings' );
		$subscriber = $this->map_fields( $record );

		if ( ! isset( $subscriber['email'] ) ) {
			return false;
		}

		if ( ! isset( $form_settings['activecampaign_list'] ) ) {
			return false;
		}

		$subscriber['ip4'] = Utils::get_client_ip();
		$list_id = $form_settings['activecampaign_list'];
		$subscriber[ 'p[' . $list_id . ']' ] = $list_id;

		if ( isset( $form_settings['activecampaign_tags'] ) && ! empty( $form_settings['activecampaign_tags'] ) ) {
			$subscriber['tags'] = $form_settings['activecampaign_tags'];
		}

		if ( isset( $form_settings['form_id'] ) && ! empty( $form_settings['form_id'] ) ) {
			$subscriber['form'] = $form_settings['form_id'];
		}

		return $subscriber;
	}

	/**
	 * @param Form_Record $record
	 *
	 * @return array
	 */
	private function map_fields( Form_Record $record ) {
		$subscriber = [];
		$fields = $record->get( 'fields' );

		// Other form has a field mapping
		foreach ( $record->get_form_settings( 'activecampaign_fields_map' ) as $map_item ) {
			if ( empty( $fields[ $map_item['local_id'] ]['value'] ) ) {
				continue;
			}

			$value = $fields[ $map_item['local_id'] ]['value'];
			$subscriber[ $map_item['remote_id'] ] = $value;
		}

		return $subscriber;
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function handle_panel_request( array $data ) {
		if ( ! empty( $data['api_cred'] ) && 'default' === $data['api_cred'] ) {
			$api_key = $this->get_global_api_key();
			$api_url = $this->get_global_api_url();
		} elseif ( ! empty( $data['api_key'] ) && ! empty( $data['api_url'] ) ) {
			$api_key = $data['api_key'];
			$api_url = $data['api_url'];
		}

		if ( empty( $api_key ) ) {
			throw new \Exception( '`api_key` is required', 400 );
		}

		if ( empty( $api_url ) ) {
			throw new \Exception( '`api_url` is required', 400 );
		}

		$handler = new Classes\Activecampaign_Handler( $api_key, $api_url );

		return $handler->get_lists();
	}

	public function ajax_validate_api_token() {
		check_ajax_referer( self::OPTION_NAME_API_KEY, '_nonce' );
		if ( ! isset( $_POST['api_key'] ) || ! isset( $_POST['api_url'] ) ) {
			wp_send_json_error();
		}
		try {
			new Classes\Activecampaign_Handler( $_POST['api_key'], $_POST['api_url'] );
		} catch ( \Exception $exception ) {
			wp_send_json_error();
		}
		wp_send_json_success();
	}

	public function register_admin_fields( Settings $settings ) {
		$settings->add_section( Settings::TAB_INTEGRATIONS, 'activecampign', [
			'callback' => function() {
				echo '<hr><h2>' . esc_html__( 'ActiveCampaign', 'elementor-pro' ) . '</h2>';
			},
			'fields' => [
				self::OPTION_NAME_API_KEY => [
					'label' => __( 'API Key', 'elementor-pro' ),
					'field_args' => [
						'type' => 'text',
					],
				],
				self::OPTION_NAME_API_URL => [
					'label' => __( 'API URL', 'elementor-pro' ),
					'field_args' => [
						'type' => 'url',
						'desc' => sprintf( __( 'To integrate with our forms you need an <a href="%s" target="_blank">API Key</a>.', 'elementor-pro' ), 'https://help.activecampaign.com/hc/en-us/articles/207317590-Getting-started-with-the-API' ),
					],
				],
				'validate_api_data' => [
					'field_args' => [
						'type' => 'raw_html',
						'html' => sprintf( '<button data-action="%s" data-nonce="%s" class="button elementor-button-spinner" id="elementor_pro_activecampaign_api_key_button">%s</button>', self::OPTION_NAME_API_KEY . '_validate', wp_create_nonce( self::OPTION_NAME_API_KEY ), __( 'Validate API Key', 'elementor-pro' ) ),
					],
				],
			],
		] );
	}

	public function __construct() {
		if ( is_admin() ) {
			add_action( 'elementor/admin/after_create_settings/' . Settings::PAGE_ID, [ $this, 'register_admin_fields' ], 15 );
		}
		add_action( 'wp_ajax_' . self::OPTION_NAME_API_KEY . '_validate', [ $this, 'ajax_validate_api_token' ] );
	}
}
