<?php
/**
 * Handles setting up the configuration data for Filter Bar.
 *
 * @since   5.0.0
 *
 * @package Tribe\Events\Filterbar\Views\V2_1
 */
namespace Tribe\Events\Filterbar\Views\V2_1;

/**
 * Class managing Configuration for the Views V2_1.
 *
 * @package Tribe\Events\Filterbar\Views\V2_1
 * @since   5.0.0
 */
class Configuration {
	/**
	 * Return the variables to be localized.
	 *
	 * @since 5.0.0
	 *
	 * @return array
	 */
	public function localize() {
		$data = [
			'events' => [
				'currency_symbol'           => tribe_get_option( 'defaultCurrencySymbol', '$' ),
				'reverse_currency_position' => tribe_get_option( 'reverseCurrencyPosition', false ),
			],
			'l10n'   => [
				'show_filters'                      => esc_html__( 'Show filters', 'tribe-events-filter-view' ),
				'hide_filters'                      => esc_html__( 'Hide filters', 'tribe-events-filter-view' ),
				'cost_range_currency_symbol_before' => sprintf(
					esc_html_x(
						'%1$s%2$s - %1$s%3$s',
						'Cost range for when currency symbol comes before cost.',
						'tribe-events-filter-view'
					),
					'<%- currency_symbol %>',
					'<%- cost_low %>',
					'<%- cost_high %>'
				),
				'cost_range_currency_symbol_after'  => sprintf(
					esc_html_x(
						'%1$s%2$s - %3$s%2$s',
						'Cost range for when currency symbol comes after cost.',
						'tribe-events-filter-view'
					),
					'<%- cost_low %>',
					'<%- currency_symbol %>',
					'<%- cost_high %>'
				),
			],
		];

		return $data;
	}
}
