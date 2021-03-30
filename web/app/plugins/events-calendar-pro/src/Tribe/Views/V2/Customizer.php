<?php
/**
 * Handles Views v2 Customizer settings.
 *
 * @since   5.1.1
 *
 * @package Tribe\Events\Views\V2
 */

namespace Tribe\Events\Pro\Views\V2;

/**
 * Class Customizer
 *
 * @since   5.1.1
 *
 * @package Tribe\Events\Views\V2
 */
class Customizer {
	/**
	 * Filters the currently registered Customizer sections to add or modify them.
	 *
	 * @since 5.1.1
	 *
	 * @param array<string,array<string,array<string,int|float|string>>> $sections   The registered Customizer sections.
	 * @param \Tribe___Customizer                                        $customizer The Customizer object.
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
	 * @since 5.1.1
	 *
	 * @param string                      $css_template The CSS template, as produced by the Global Elements.
	 * @param \Tribe__Customizer__Section $section      The Global Elements section.
	 * @param \Tribe__Customizer          $customizer   The current Customizer instance.
	 *
	 * @return string The filtered CSS template.
	 */
	public function filter_global_elements_css_template( $css_template, $section, $customizer ) {
		if ( $customizer->has_option( $section->ID, 'event_title_color' ) ) {
			// Event Title overrides.
			$css_template .= '
				.tribe-events-pro .tribe-events-pro-photo__event-title-link,
				.tribe-events-pro .tribe-events-pro-photo__event-title-link:active,
				.tribe-events-pro .tribe-events-pro-photo__event-title-link:visited,
				.tribe-events-pro .tribe-events-pro-photo__event-title-link:hover,
				.tribe-events-pro .tribe-events-pro-photo__event-title-link:focus,
				.tribe-events-pro .tribe-events-pro-map__event-title,
				.tribe-events-pro .tribe-events-pro-map__event-tooltip-title-link,
				.tribe-events-pro .tribe-events-pro-map__event-tooltip-title-link:active,
				.tribe-events-pro .tribe-events-pro-map__event-tooltip-title-link:visited,
				.tribe-events-pro .tribe-events-pro-map__event-tooltip-title-link:hover,
				.tribe-events-pro .tribe-events-pro-map__event-tooltip-title-link:focus,
				.tribe-events-pro .tribe-events-pro-week-grid__event-title,
				.tribe-events-pro .tribe-events-pro-week-grid__event-tooltip-title-link,
				.tribe-events-pro .tribe-events-pro-week-grid__event-tooltip-title-link:active,
				.tribe-events-pro .tribe-events-pro-week-grid__event-tooltip-title-link:visited,
				.tribe-events-pro .tribe-events-pro-week-grid__event-tooltip-title-link:hover,
				.tribe-events-pro .tribe-events-pro-week-grid__event-tooltip-title-link:focus,
				.tribe-events-pro .tribe-events-pro-week-grid__multiday-event-bar-title,
				.tribe-events-pro .tribe-events-pro-week-mobile-events__event-title-link,
				.tribe-events-pro .tribe-events-pro-week-mobile-events__event-title-link:active,
				.tribe-events-pro .tribe-events-pro-week-mobile-events__event-title-link:visited,
				.tribe-events-pro .tribe-events-pro-week-mobile-events__event-title-link:hover,
				.tribe-events-pro .tribe-events-pro-week-mobile-events__event-title-link:focus,
				.tribe-theme-twentyseventeen .tribe-events-pro .tribe-events-pro-photo__event-title-link:hover,
				.tribe-theme-twentyseventeen .tribe-events-pro .tribe-events-pro-photo__event-title-link:focus,
				.tribe-theme-twentyseventeen .tribe-events-pro .tribe-events-pro-map__event-tooltip-title-link:hover,
				.tribe-theme-twentyseventeen .tribe-events-pro .tribe-events-pro-map__event-tooltip-title-link:focus,
				.tribe-theme-twentyseventeen .tribe-events-pro .tribe-events-pro-week-grid__event-tooltip-title-link:hover,
				.tribe-theme-twentyseventeen .tribe-events-pro .tribe-events-pro-week-grid__event-tooltip-title-link:focus,
				.tribe-theme-twentyseventeen .tribe-events-pro .tribe-events-pro-week-mobile-events__event-title-link:hover,
				.tribe-theme-twentyseventeen .tribe-events-pro .tribe-events-pro-week-mobile-events__event-title-link:focus,
				.tribe-theme-enfold#top .tribe-events-pro .tribe-events-pro-photo__event-title-link,
				.tribe-theme-enfold#top .tribe-events-pro .tribe-events-pro-map__event-tooltip-title-link,
				.tribe-theme-enfold#top .tribe-events-pro .tribe-events-pro-week-grid__event-tooltip-title-link,
				.tribe-theme-enfold#top .tribe-events-pro .tribe-events-pro-week-mobile-events__event-title-link {
					color: <%= global_elements.event_title_color %>;
				}

				.tribe-events-pro .tribe-events-pro-photo__event-title-link:active,
				.tribe-events-pro .tribe-events-pro-photo__event-title-link:hover,
				.tribe-events-pro .tribe-events-pro-photo__event-title-link:focus,
				.tribe-events-pro .tribe-events-pro-map__event-tooltip-title-link:active,
				.tribe-events-pro .tribe-events-pro-map__event-tooltip-title-link:hover,
				.tribe-events-pro .tribe-events-pro-map__event-tooltip-title-link:focus,
				.tribe-events-pro .tribe-events-pro-week-grid__event-tooltip-title-link:active,
				.tribe-events-pro .tribe-events-pro-week-grid__event-tooltip-title-link:hover,
				.tribe-events-pro .tribe-events-pro-week-grid__event-tooltip-title-link:focus,
				.tribe-events-pro .tribe-events-pro-week-mobile-events__event-title-link:active,
				.tribe-events-pro .tribe-events-pro-week-mobile-events__event-title-link:hover,
				.tribe-events-pro .tribe-events-pro-week-mobile-events__event-title-link:focus {
					border-color: <%= global_elements.event_title_color %>;
				}
			';
		}

		if ( $customizer->has_option( $section->ID, 'event_date_time_color' ) ) {
			// Event Date Time overrides.
			$css_template .= '
				.tribe-events-pro .tribe-events-pro-photo__event-datetime,
				.tribe-events-pro .tribe-events-pro-map__event-datetime-wrapper,
				.tribe-events-pro .tribe-events-pro-map__event-tooltip-datetime-wrapper,
				.tribe-events-pro .tribe-events-pro-week-grid__event-datetime,
				.tribe-events-pro .tribe-events-pro-week-grid__event-tooltip-datetime,
				.tribe-events-pro .tribe-events-pro-week-mobile-events__event-datetime {
					color: <%= global_elements.event_date_time_color %>;
				}
			';
		}

		if ( $customizer->has_option( $section->ID, 'link_color' ) ) {
			// Organizer/Venue Links Overrides.
			$css_template .= '
				.tribe-events-pro .tribe-events-pro-organizer__meta-website-link,
				.tribe-events-pro .tribe-events-pro-organizer__meta-website-link:active,
				.tribe-events-pro .tribe-events-pro-organizer__meta-website-link:visited,
				.tribe-events-pro .tribe-events-pro-organizer__meta-website-link:hover,
				.tribe-events-pro .tribe-events-pro-organizer__meta-website-link:focus,
				.tribe-events-pro .tribe-events-pro-venue__meta-website-link,
				.tribe-events-pro .tribe-events-pro-venue__meta-website-link:active,
				.tribe-events-pro .tribe-events-pro-venue__meta-website-link:visited,
				.tribe-events-pro .tribe-events-pro-venue__meta-website-link:hover,
				.tribe-events-pro .tribe-events-pro-venue__meta-website-link:focus,
				.tribe-theme-twentyseventeen .tribe-events-pro .tribe-events-pro-organizer__meta-website-link:hover,
				.tribe-theme-twentyseventeen .tribe-events-pro .tribe-events-pro-organizer__meta-website-link:focus,
				.tribe-theme-twentyseventeen .tribe-events-pro .tribe-events-pro-venue__meta-website-link:hover,
				.tribe-theme-twentyseventeen .tribe-events-pro .tribe-events-pro-venue__meta-website-link:focus,
				.tribe-theme-enfold .tribe-events-pro .tribe-events-pro-organizer__meta-website-link:active,
				.tribe-theme-enfold .tribe-events-pro .tribe-events-pro-organizer__meta-website-link:visited,
				.tribe-theme-enfold .tribe-events-pro .tribe-events-pro-organizer__meta-website-link:hover,
				.tribe-theme-enfold .tribe-events-pro .tribe-events-pro-organizer__meta-website-link:focus,
				.tribe-theme-enfold .tribe-events-pro .tribe-events-pro-venue__meta-website-link:active,
				.tribe-theme-enfold .tribe-events-pro .tribe-events-pro-venue__meta-website-link:visited,
				.tribe-theme-enfold .tribe-events-pro .tribe-events-pro-venue__meta-website-link:hover,
				.tribe-theme-enfold .tribe-events-pro .tribe-events-pro-venue__meta-website-link:focus {
					color: <%= global_elements.link_color %>;
				}

				.tribe-events-pro .tribe-events-pro-organizer__meta-website-link:active,
				.tribe-events-pro .tribe-events-pro-organizer__meta-website-link:focus,
				.tribe-events-pro .tribe-events-pro-organizer__meta-website-link:hover,
				.tribe-events-pro .tribe-events-pro-venue__meta-website-link:active,
				.tribe-events-pro .tribe-events-pro-venue__meta-website-link:hover,
				.tribe-events-pro .tribe-events-pro-venue__meta-website-link:focus {
					border-color: <%= global_elements.link_color %>;
				}
			';
		}

		if ( $customizer->has_option( $section->ID, 'accent_color' ) ) {
			$css_template .= '
			.tribe-events-widget .tribe-events-widget-featured-venue__view-more-link {
				color: <%= global_elements.accent_color %>;
			}';
		}

		if (
			$customizer->has_option( $section->ID, 'background_color_choice' )
			&& 'custom' === $customizer->get_option( [ $section->ID, 'background_color_choice' ] )
			&& $customizer->has_option( $section->ID, 'background_color' )
		) {
			$css_template .= '
				.tribe-events-pro .tribe-events-pro-week-grid__event-link {
					border-color: <%= global_elements.background_color %>;
				}
			';
		}

		return $css_template;
	}

	/**
	 * Filters the Single Event section CSS template to add Views v2 related style templates to it.
	 *
	 * @since 5.1.1
	 *
	 * @param string                      $css_template The CSS template, as produced by the Global Elements.
	 * @param \Tribe__Customizer__Section $section      The Single Event section.
	 * @param \Tribe__Customizer          $customizer   The current Customizer instance.
	 *
	 * @return string The filtered CSS template.
	 */
	public function filter_single_event_css_template( $css_template, $section, $customizer ) {
		return $css_template;
	}
}
