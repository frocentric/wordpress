<?php
// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * The Events Calendar Customizer Section Class
 * Photo View
 *
 * @package Events Pro
 * @subpackage Customizer
 * @since 4.0
 */
final class Tribe__Events__Pro__Customizer__Photo_View extends Tribe__Customizer__Section {
	/**
	 * PHP 5.2 method of creating "instances" of an abstract require this
	 *
	 * Note: This is the only required method for a Connector to work
	 *
	 * @return self The dynamic instance of this Class
	 */
	public static function instance( ) {
		return tribe( 'events-pro.customizer.photo-view' );
	}

	/**
	 * Grab the CSS rules template
	 *
	 * @return string
	 */
	public function get_css_template( $template ) {
		$customizer = Tribe__Customizer::instance();

		if ( $customizer->has_option( $this->ID, 'bg_color' ) ) {
			$template .= '
				.type-tribe_events.tribe-events-photo-event .tribe-events-photo-event-wrap {
					background-color: <%= photo_view.bg_color %>;
					color: <%= photo_view.text_color %>;
				}

				.type-tribe_events.tribe-events-photo-event .tribe-events-photo-event-wrap:hover {
					background-color: <%= photo_view.bg_color_light %>;
				}
			';
		}

		return $template;
	}

	public function create_ghost_settings( $settings = array() ) {
		if ( ! empty( $settings['bg_color'] ) ) {
			$bg_color = new Tribe__Utils__Color( $settings['bg_color'] );
			$settings['bg_color_light'] = '#' . $bg_color->lighten();

			if ( $bg_color->isDark() ) {
				$settings['text_color'] = '#f9f9f9';
			} else {
				$settings['text_color'] = '#333333';
			}
		}

		return $settings;
	}

	public function setup() {
		$this->defaults = array(
			'bg_color' => '#eee',
		);

		$this->arguments = array(
			'priority'    => 50,
			'capability'  => 'edit_theme_options',
			'title'       => esc_html__( 'Photo View', 'tribe-events-calendar-pro' ),
			'description' => esc_html__( 'Options selected here will override what was selected in the "General Theme" and "Global Elements" sections', 'tribe-events-calendar-pro' ),
		);
	}

	/**
	 * Create the Fields/Settings for this sections
	 *
	 * @param  WP_Customize_Section $section The WordPress section instance
	 * @param  WP_Customize_Manager $manager [description]
	 *
	 * @return void
	 */
	public function register_settings( WP_Customize_Section $section, WP_Customize_Manager $manager ) {
		$customizer = Tribe__Customizer::instance();

		$manager->add_setting(
			$customizer->get_setting_name( 'bg_color', $section ),
			array(
				'default'              => $this->get_default( 'bg_color' ),
				'type'                 => 'option',

				'sanitize_callback'    => 'sanitize_hex_color',
				'sanitize_js_callback' => 'maybe_hash_hex_color',
			)
		);

		$manager->add_control(
			new WP_Customize_Color_Control(
				$manager,
				$customizer->get_setting_name( 'bg_color', $section ),
				array(
					'label'   => __( 'Photo Background Color', 'tribe-events-calendar-pro' ),
					'section' => $section->id,
				)
			)
		);

		// Introduced to make Selective Refresh have less code duplication
		$customizer->add_setting_name( $customizer->get_setting_name( 'bg_color', $section ) );
	}

}
