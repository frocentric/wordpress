<?php
/**
 * Handles the plugin Views v2 Customizer settings.
 *
 * @since   4.8.3
 *
 * @package Tribe\Events\Community\Views\V2
 */

namespace Tribe\Events\Community\Views\V2;

/**
 * Class Customizer
 *
 * @since   4.8.3
 *
 * @package Tribe\Events\Community\Views\V2
 */
class Customizer {
	/**
	 * Filters the Customizer sheets that are target of a possible inline style print if enqueued
	 * to add the plugin ones.
	 *
	 * @since 4.8.3
	 *
	 * @param array<string> $inline_sheets The list of style sheet handles that are currently candidates
	 *                                     for inline style print if enqueued.
	 *
	 * @return array<string> The filtered list of style sheet handles candidate for inline style print.
	 */
	public function filter_inline_sheets( array $inline_sheets ) {
		$inline_sheets[] = 'tribe-events-community';
		$inline_sheets[] = 'tribe-events-community-list';

		return $inline_sheets;
	}

	/**
	 * Filters the currently registered Customizer sections to add or modify them.
	 *
	 * @since 4.8.3
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
	 * @since 4.8.3
	 *
	 * @param string                      $css_template The CSS template, as produced by the Global Elements.
	 * @param \Tribe__Customizer__Section $section      The Global Elements section.
	 * @param \Tribe__Customizer          $customizer   The current Customizer instance.
	 *
	 * @return string The filtered CSS template.
	 */
	public function filter_global_elements_css_template( $css_template, $section, $customizer ) {
		// TODO Filter the CSS template.
		return $css_template;
	}

	/**
	 * Filters the Single Event section CSS template to add Views v2 related style templates to it.
	 *
	 * @since 4.8.3
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
