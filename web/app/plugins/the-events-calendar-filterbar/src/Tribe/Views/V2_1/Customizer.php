<?php
/**
 * Handles Views v2 Customizer settings.
 *
 * @since   5.0.0
 *
 * @package Tribe\Events\Filterbar\Views\V2_1
 */

namespace Tribe\Events\Filterbar\Views\V2_1;

/**
 * Class Customizer
 *
 * @since   5.0.0
 * @since   5.0.3 Refactored the `add_styles` method to the `filter_global_elements_css_template` method.j
 *
 * @package Tribe\Events\Filterbar\Views\V2_1
 */
class Customizer {
	/**
	 * Filters the currently registered Customizer sections to add or modify them.
	 *
	 * @since 5.0.3
	 *
	 * @param array<string,array<string,array<string,int|float|string>>> $sections   The registered Customizer sections.
	 * @param \Tribe__Customizer                                         $customizer The Customizer object.
	 *
	 * @return array<string,array<string,array<string,int|float|string>>> The filtered sections.
	 */
	public function filter_sections( array $sections, $customizer ) {
		// TODO Filter the sections.
		return $sections;
	}

	/**
	 * Filters the Global Elements section CSS template to add Views v2 related style templates to it.
	 *
	 * @since 5.0.3
	 *
	 * @param string                      $css_template The CSS template, as produced by the Global Elements.
	 * @param \Tribe__Customizer__Section $section      The Global Elements section.
	 * @param \Tribe__Customizer          $customizer   The current Customizer instance.
	 *
	 * @return string The filtered CSS template.
	 */
	public function filter_global_elements_css_template( $css_template, $section, $customizer ) {
		if ( $customizer->has_option( $section->ID, 'accent_color' ) ) {
			// This changes the Filter Icon underline color to match the global accent color.
			$css_template .= '
				.tribe-events .tribe-events-c-events-bar__filter-button:before {
					background-color: <%= global_elements.accent_color %>;
				}
			';
		}

		if (
			$customizer->has_option( $section->ID, 'background_color_choice' ) &&
			'custom' === $customizer->get_option( [ $section->ID, 'background_color_choice' ] ) &&
			$customizer->has_option( $section->ID, 'background_color' )
		) {
			// Nav background overrides
			$css_template .= '
				.tribe-filter-bar .tribe-filter-bar__filters-slider-nav--overflow-start:before {
					background: linear-gradient(-90deg, transparent 15%, <%= global_elements.background_color %> 70%);
				}

				.tribe-filter-bar .tribe-filter-bar__filters-slider-nav--overflow-end:after {
					background: linear-gradient(90deg, transparent 15%, <%= global_elements.background_color %> 70%);
				}
			';
		}

		return $css_template;
	}

	/**
	 * Filters the Single Event section CSS template to add Views v2 related style templates to it.
	 *
	 * @since 5.0.3
	 *
	 * @param string                      $css_template The CSS template, as produced by the Global Elements.
	 * @param \Tribe__Customizer__Section $section      The Single Event section.
	 * @param \Tribe__Customizer          $customizer   The current Customizer instance.
	 *
	 * @return string The filtered CSS template.
	 */
	public function filter_single_event_css_template( $css_template, $section, $customizer ) {
		// TODO Filter the CSS template.
		return $css_template;
	}
}
