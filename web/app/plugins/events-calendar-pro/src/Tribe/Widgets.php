<?php


/**
 * Common helper methods for PRO widgets.
 */
class Tribe__Events__Pro__Widgets {

	/**
	 * @param $filters
	 * @param $operand
	 *
	 * @return array|null
	 */
	public static function form_tax_query( $filters, $operand ) {
		if ( empty( $filters ) ) {
			return null;
		}

		$tax_query = array();

		foreach ( $filters as $tax => $terms ) {
			if ( empty( $terms ) ) {
				continue;
			}

			$tax_operand = 'AND';
			if ( $operand == 'OR' ) {
				$tax_operand = 'IN';
			}

			if ( 'AND' === $tax_operand && is_taxonomy_hierarchical( $tax ) ) {
				/*
				 * When making and AND query on a hierarchical taxonomy where 'include_children'
				 * is true (the default), WP requires all matches to have ALL child terms,
				 * not just one child of each of the supplied terms. By breaking this up
				 * into multiple tax queries ANDed together, you get the results that
				 * are more naturally expected.
				 */
				foreach ( $terms as $term ) {
					$tax_query[] = array(
						'taxonomy' => $tax,
						'field' => 'id',
						'terms' => array( $term ),
					);
				}
			} else {
				$tax_query[] = array(
					'taxonomy' => $tax,
					'field' => 'id',
					'operator' => $tax_operand,
					'terms' => $terms,
				);
			}
		}

		if ( count( $tax_query ) > 1 ) {
			$tax_query['relation'] = $operand;
		}

		return $tax_query;
	}

	/**
	 * Enqueue the appropriate CSS for the calendar/advanced list widgets, which share
	 * the same basic appearance.
	 */
	public static function enqueue_calendar_widget_styles() {

		tribe_asset_enqueue( 'widget-calendar-pro-style' );

		tribe_asset_enqueue( Tribe__Events__Main::POSTTYPE . '-widget-calendar-pro-override-style' );

	}
}
