<?php
namespace ElementorPro\Modules\CustomAttributes;

use Elementor\Controls_Stack;
use Elementor\Controls_Manager;
use Elementor\Element_Base;
use ElementorPro\Base\Module_Base;
use ElementorPro\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Module extends Module_Base {

	// TODO: Remove this flag on version 2.9.0
	private $controls_already_registered;

	public function __construct() {
		parent::__construct();

		// TODO: Remove this flag on version 2.9.0
		$this->controls_already_registered = [
			'section' => false,
			'column' => false,
			'common' => false,
		];

		$this->add_actions();
	}

	public function get_name() {
		return 'custom-attributes';
	}

	private function get_black_list_attributes() {
		static $black_list = null;

		if ( null === $black_list ) {
			$black_list = [ 'id', 'class', 'data-id', 'data-settings', 'data-element_type', 'data-widget_type', 'data-model-cid', 'onload', 'onclick', 'onfocus', 'onblur', 'onchange', 'onresize', 'onmouseover', 'onmouseout', 'onkeydown', 'onkeyup', 'onerror' ];

			/**
			 * Elementor attributes black list.
			 *
			 * Filters the attributes that won't be rendered in the wrapper element.
			 *
			 * By default Elementor don't render some attributes to prevent things
			 * from breaking down. But this list of attributes can be changed.
			 *
			 * @since 2.2.0
			 *
			 * @param array $black_list A black list of attributes.
			 */
			$black_list = apply_filters( 'elementor_pro/element/attributes/black_list', $black_list );
		}

		return $black_list;
	}

	/**
	 * @param Element_Base $element
	 */
	public function replace_go_pro_custom_attributes_controls( Element_Base $element ) {
		Plugin::elementor()->controls_manager->remove_control_from_stack( $element->get_unique_name(), [ 'section_custom_attributes_pro', 'custom_attributes_pro' ] );

		$this->register_custom_attributes_controls( $element );
	}

	public function register_custom_attributes_controls( Element_Base $element ) {
		$element_name = $element->get_name();

		// TODO: Remove this check when on version 2.9.0
		if ( $this->controls_already_registered[ $element_name ] ) {
			return;
		}

		$element->start_controls_section(
			'_section_attributes',
			[
				'label' => __( 'Attributes', 'elementor-pro' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		$element->add_control(
			'_attributes',
			[
				'label' => __( 'Custom Attributes', 'elementor-pro' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'key|value', 'elementor-pro' ),
				'description' => sprintf( __( 'Set custom attributes for the wrapper element. Each attribute in a separate line. Separate attribute key from the value using %s character.', 'elementor-pro' ), '<code>|</code>' ),
				'classes' => 'elementor-control-direction-ltr',
			]
		);

		$element->end_controls_section();

		// TODO: Remove this flag on version 2.9.0
		$this->controls_already_registered[ $element_name ] = true;
	}

	/**
	 * @param $element    Controls_Stack
	 * @param $section_id string
	 */
	public function register_controls( Controls_Stack $element, $section_id ) {
		if ( ! $element instanceof Element_Base ) {
			return;
		}

		// Remove Custom CSS Banner (From free version)
		if ( 'section_custom_attributes_pro' === $section_id ) {
			$this->replace_go_pro_custom_attributes_controls( $element );
		}

		// TODO: Remove this when on version 2.9.0
		if ( '_section_responsive' === $section_id ) {
			$this->register_custom_attributes_controls( $element );
		}
	}

	/**
	 * @param $element Element_Base
	 */
	public function render_attributes( Element_Base $element ) {
		$settings = $element->get_settings_for_display();

		if ( ! empty( $settings['_attributes'] ) ) {
			$attributes = explode( "\n", $settings['_attributes'] );

			$black_list = $this->get_black_list_attributes();

			foreach ( $attributes as $attribute ) {
				if ( ! empty( $attribute ) ) {
					$attr = explode( '|', $attribute, 2 );
					if ( ! isset( $attr[1] ) ) {
						$attr[1] = '';
					}

					if ( ! in_array( strtolower( $attr[0] ), $black_list ) ) {
						$element->add_render_attribute( '_wrapper', trim( $attr[0] ), trim( $attr[1] ) );
					}
				}
			}
		}
	}

	protected function add_actions() {
		add_action( 'elementor/element/after_section_end', [ $this, 'register_controls' ], 10, 2 );
		add_action( 'elementor/element/after_add_attributes', [ $this, 'render_attributes' ] );
	}
}
