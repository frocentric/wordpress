<?php
namespace ElementorPro\Modules\ThemeBuilder\Documents;

use Elementor\Controls_Manager;
use Elementor\Modules\Library\Documents\Library_Document;
use ElementorPro\Modules\QueryControl\Module as QueryModule;
use ElementorPro\Modules\ThemeBuilder\Module;
use ElementorPro\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class Theme_Document extends Library_Document {

	const LOCATION_META_KEY = '_elementor_location';

	public function get_location_label() {
		$location = $this->get_location();
		$locations_settings = Module::instance()->get_locations_manager()->get_location( $location );
		$label = '';
		$is_section_doc_type = 'section' === $this->get_name();

		if ( $location ) {
			if ( $is_section_doc_type ) {
				$label .= isset( $locations_settings['label'] ) ? $locations_settings['label'] : $location;
			}
		}

		$supported = true;

		if ( $is_section_doc_type ) {
			if ( $location && ! $locations_settings ) {
				$supported = false;
			}
		} elseif ( ! $location || ! $locations_settings ) {
			$supported = false;
		}

		if ( ! $supported ) {
			$label .= ' (' . __( 'Unsupported', 'elementor-pro' ) . ')';
		}

		return $label;
	}

	public function before_get_content() {
		$preview_manager = Module::instance()->get_preview_manager();
		$preview_manager->switch_to_preview_query();
	}

	public function after_get_content() {
		$preview_manager = Module::instance()->get_preview_manager();
		$preview_manager->restore_current_query();
	}

	public function get_content( $with_css = false ) {
		$this->before_get_content();

		$content = parent::get_content( $with_css );

		$this->after_get_content();

		return $content;
	}

	public function print_content() {
		$plugin = Plugin::elementor();

		if ( $plugin->preview->is_preview_mode( $this->get_main_id() ) ) {
			echo $plugin->preview->builder_wrapper( '' );
		} else {
			echo $this->get_content();
		}
	}

	public static function get_preview_as_default() {
		return '';
	}

	public static function get_preview_as_options() {
		return [];
	}

	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['admin_tab_group'] = 'theme';

		return $properties;
	}

	public function get_container_attributes() {
		$attributes = parent::get_container_attributes();

		$location = Module::instance()->get_locations_manager()->get_current_location();

		if ( $location ) {
			$attributes['class'] .= ' elementor-location-' . $location;
		}

		return $attributes;
	}

	/**
	 * @static
	 * @since  2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_edit_url() {
		$url = parent::get_edit_url();

		if ( isset( $_GET['action'] ) && 'elementor_new_post' === $_GET['action'] ) {
			$url .= '#library';
		}

		return $url;

	}

	protected function _register_controls() {
		parent::_register_controls();

		$this->start_controls_section(
			'preview_settings',
			[
				'label' => __( 'Preview Settings', 'elementor-pro' ),
				'tab' => Controls_Manager::TAB_SETTINGS,
			]
		);

		$this->add_control(
			'preview_type',
			[
				'label' => __( 'Preview Dynamic Content as', 'elementor-pro' ),
				'label_block' => true,
				'type' => Controls_Manager::SELECT,
				'default' => $this::get_preview_as_default(),
				'groups' => $this::get_preview_as_options(),
				'export' => false,
			]
		);

		$this->add_control(
			'preview_id',
			[
				'type' => QueryModule::QUERY_CONTROL_ID,
				'label_block' => true,
				'autocomplete' => [
					'object' => QueryModule::QUERY_OBJECT_JS,
				],
				'separator' => 'none',
				'export' => false,
				'condition' => [
					'preview_type!' => [
						'',
						'search',
					],
				],
			]
		);

		$this->add_control(
			'preview_search_term',
			[
				'label' => __( 'Search Term', 'elementor-pro' ),
				'export' => false,
				'condition' => [
					'preview_type' => 'search',
				],
			]
		);

		$this->add_control(
			'apply_preview',
			[
				'type' => Controls_Manager::BUTTON,
				'label' => __( 'Apply & Preview', 'elementor-pro' ),
				'label_block' => true,
				'show_label' => false,
				'text' => __( 'Apply & Preview', 'elementor-pro' ),
				'separator' => 'none',
				'event' => 'elementorThemeBuilder:ApplyPreview',
			]
		);

		$this->end_controls_section();
	}

	public function get_elements_raw_data( $data = null, $with_html_content = false ) {
		$preview_manager = Module::instance()->get_preview_manager();

		$preview_manager->switch_to_preview_query();

		$editor_data = parent::get_elements_raw_data( $data, $with_html_content );

		$preview_manager->restore_current_query();

		return $editor_data;
	}

	public function render_element( $data ) {
		$preview_manager = Module::instance()->get_preview_manager();

		$preview_manager->switch_to_preview_query();

		$render_html = parent::render_element( $data );

		$preview_manager->restore_current_query();

		return $render_html;
	}

	public function get_wp_preview_url() {
		$preview_id = (int) $this->get_settings( 'preview_id' );
		$post_id = $this->get_main_id();

		list( $preview_category, $preview_object_type ) = array_pad( explode( '/', $this->get_settings( 'preview_type' ) ), 2, '' );

		$home_url = trailingslashit( home_url() );

		switch ( $preview_category ) {
			case 'archive':
				switch ( $preview_object_type ) {
					case 'author':
						if ( empty( $preview_id ) ) {
							$preview_id = get_current_user_id();
						}
						$preview_url = get_author_posts_url( $preview_id );
						break;
					case 'date':
						$preview_url = add_query_arg( 'year', gmdate( 'Y' ), $home_url );
						break;
				}
				break;
			case 'search':
				$preview_url = add_query_arg( 's', $this->get_settings( 'preview_search_term' ), $home_url );
				break;
			case 'taxonomy':
				$term = get_term( $preview_id );

				if ( $term && ! is_wp_error( $term ) ) {
					$preview_url = get_term_link( $preview_id );
				}

				break;
			case 'page':
				switch ( $preview_object_type ) {
					case 'home':
						$preview_url = get_post_type_archive_link( 'post' );
						break;
					case 'front':
						$preview_url = $home_url;
						break;
					case '404':
						$preview_url = add_query_arg( 'p', '-1', $home_url );
						break;
				}
				break;
			case 'post_type_archive':
				$post_type = $preview_object_type;
				if ( post_type_exists( $post_type ) ) {
					$preview_url = get_post_type_archive_link( $post_type );
				}
				break;
			case 'single':
				$post = get_post( $preview_id );
				if ( $post ) {
					$preview_url = get_permalink( $post );
				}
				break;
		} // End switch().

		if ( empty( $preview_url ) ) {
			$preview_url = $this->get_permalink();
		}

		$query_args = [
			'preview' => true,
			'preview_nonce' => wp_create_nonce( 'post_preview_' . $post_id ),
			'theme_template_id' => $post_id,
		];

		$preview_url = set_url_scheme( add_query_arg( $query_args, $preview_url ) );

		/**
		 * Document "WordPress preview" URL.
		 *
		 * Filters the WordPress preview URL.
		 *
		 * @since 2.0.0
		 *
		 * @param Theme_Document $this An instance of the theme document.
		 */
		$preview_url = apply_filters( 'elementor/document/wp_preview_url', $preview_url, $this );

		return $preview_url;
	}

	public function get_preview_as_query_args() {
		$preview_id = (int) $this->get_settings( 'preview_id' );

		list( $preview_category, $preview_object_type ) = array_pad( explode( '/', $this->get_settings( 'preview_type' ) ), 2, '' );

		switch ( $preview_category ) {
			case 'archive':
				switch ( $preview_object_type ) {
					case 'author':
						if ( empty( $preview_id ) ) {
							$preview_id = get_current_user_id();
						}

						$query_args = [
							'author' => $preview_id,
						];
						break;
					case 'date':
						$query_args = [
							'year' => gmdate( 'Y' ),
						];
						break;
					case 'recent_posts':
						$query_args = [
							'post_type' => 'post',
						];
						break;
				}
				break;
			case 'search':
				$query_args = [
					's' => $this->get_settings( 'preview_search_term' ),
				];
				break;
			case 'taxonomy':
				$term = get_term( $preview_id );

				if ( $term && ! is_wp_error( $term ) ) {
					$query_args = [
						'tax_query' => [
							[
								'taxonomy' => $term->taxonomy,
								'terms' => [ $preview_id ],
								'field' => 'id',
							],
						],
					];
				}
				break;
			case 'page':
				switch ( $preview_object_type ) {
					case 'home':
						$query_args = [];
						break;
					case 'front':
						$query_args = [
							'p' => get_option( 'page_on_front' ),
							'post_type' => 'page',
						];
						break;
					case '404':
						$query_args = [
							'p' => -1,
						];
						break;
				}
				break;
			case 'post_type_archive':
				$post_type = $preview_object_type;
				if ( post_type_exists( $post_type ) ) {
					$query_args = [
						'post_type' => $post_type,
					];
				}
				break;
			case 'single':
				$post = get_post( $preview_id );
				if ( ! $post ) {
					break;
				}

				$query_args = [
					'p' => $post->ID,
					'post_type' => $post->post_type,
				];
		} // End switch().

		if ( empty( $query_args ) ) {
			$query_args = [
				'p' => $this->get_main_id(),
				'post_type' => $this->get_main_post()->post_type,
			];
		}

		return $query_args;
	}

	public function after_preview_switch_to_query() {
		global $wp_query;
		if ( 'archive/recent_posts' === $this->get_settings( 'preview_type' ) ) {
			$wp_query->is_archive = true;
		}
	}

	public function get_location() {
		$value = self::get_property( 'location' );
		if ( ! $value ) {
			$value = $this->get_main_meta( self::LOCATION_META_KEY );
		}

		return $value;
	}
}
