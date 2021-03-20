<?php
/**
 * Handles hooking all the actions and filters used by the module.
 *
 * To remove a filter:
 * remove_filter( 'some_filter', [ tribe( Tribe\Events\Community\Views\V2\Hooks::class ), 'some_filtering_method' ] );
 * remove_filter( 'some_filter', [ tribe( 'community.views.v2.hooks' ), 'some_filtering_method' ] );
 *
 * To remove an action:
 * remove_action( 'some_action', [ tribe( Tribe\Events\Community\Views\V2\Hooks::class ), 'some_method' ] );
 * remove_action( 'some_action', [ tribe( 'community.views.v2.hooks' ), 'some_method' ] );
 *
 * @since   4.8.3
 *
 * @package Tribe\Events\Community\Views\V2
 */

namespace Tribe\Events\Community\Views\V2;

use Tribe__Customizer__Section as Customizer_Section;

/**
 * Class Hooks
 *
 * @since   4.8.3
 *
 * @package Tribe\Events\Community\Views\V2
 */
class Hooks extends \tad_DI52_ServiceProvider {

	/**
	 * Registers the bindings that will be used to hook classe and methods to the required actions and filters.
	 *
	 * @since 4.8.3
	 */
	public function register() {
		$this->container->singleton( Customizer::class, Customizer::class );

		$this->add_filters();
	}

	/**
	 * Hooks the classes and methods that will actually handle filtering to integrate with Views v2.
	 *
	 * @since 4.8.3
	 */
	private function add_filters() {
		// Customizer.
		add_filter( 'tribe_customizer_inline_stylesheets', [ $this, 'filter_inline_sheets' ] );
		add_filter( 'tribe_customizer_pre_sections', [ $this, 'filter_customizer_sections' ], 20, 2 );
		add_filter( 'tribe_customizer_global_elements_css_template', [ $this, 'filter_global_elements_css_template' ], 10, 3 );
		add_filter( 'tribe_customizer_single_event_css_template', [ $this, 'filter_single_event_css_template' ], 10, 3 );
	}

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
	public function filter_inline_sheets( $inline_sheets ) {
		if ( ! is_array( $inline_sheets ) ) {
			return $inline_sheets;
		}

		return $this->container->make( Customizer::class )->filter_inline_sheets( $inline_sheets );
	}

	/**
	 * Filters the currently registered Customizer sections to add or modify them.
	 *
	 * @since 4.8.3
	 *
	 * @param array<string,array<string,array<string,int|float|string>>> $sections   The registered Customizer sections.
	 * @param \Tribe___Customizer                                        $customizer The Customizer object.
	 *
	 * @return array<string,array<string,array<string,int|float|string>>> The filtered sections.
	 */
	public function filter_customizer_sections( $sections, $customizer ) {
		if ( ! ( is_array( $sections ) && $customizer instanceof \Tribe__Customizer ) ) {
			return $sections;
		}

		return $this->container->make( Customizer::class )->filter_sections( $sections, $customizer );
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
		if ( ! ( is_string( $css_template ) && $section instanceof Customizer_Section && $customizer instanceof \Tribe__Customizer ) ) {
			return $css_template;
		}

		return $this->container->make( Customizer::class )->filter_global_elements_css_template( $css_template, $section, $customizer );
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
		if ( ! (
			is_string( $css_template ) && $section instanceof Customizer_Section
			&& $customizer instanceof \Tribe__Customizer
		)
		) {
			return $css_template;
		}

		return $this->container->make( Customizer::class )->filter_single_event_css_template( $css_template, $section, $customizer );
	}
}
