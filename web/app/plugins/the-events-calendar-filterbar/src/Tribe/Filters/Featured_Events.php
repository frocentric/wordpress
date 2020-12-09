<?php


/**
 * Class Tribe__Events__Filterbar__Filters__Featured_Events
 *
 * Handles filtering events by their featured status.
 */
class Tribe__Events__Filterbar__Filters__Featured_Events extends Tribe__Events__Filterbar__Filter {

	/**
	 * @var string The default type for this filter.
	 */
	public $type = 'checkbox';

	/**
	 * @var string The table alias that will be used for the postmeta table.
	 */
	protected $table_alias = 'featured_events_filterbar_alias';

	/**
	 * Returns the admin form HTML.
	 *
	 * @return string
	 */
	public function get_admin_form() {
		$title = $this->get_title_field();
		$type  = $this->get_multichoice_type_field();

		return $title . $type;
	}

	/**
	 * Returns the value supported by this filter.
	 *
	 * One actually.
	 *
	 * @return array
	 */
	protected function get_values() {
		return array(
			'featured' => array(
				'name'  => sprintf(
					esc_html__( 'Show featured %s only', 'tribe-events-filter-view' ),
					tribe_get_event_label_plural_lowercase()
				),
				'value' => '1',
			),
		);
	}

	protected function setup_join_clause() {
		/** @var \wpdb $wpdb */
		global $wpdb;

		$clause = "INNER JOIN {$wpdb->postmeta} AS {$this->table_alias}
			ON ({$wpdb->posts}.ID = {$this->table_alias}.post_id
			AND {$this->table_alias}.meta_key = %s)";

		$this->joinClause = $wpdb->prepare( $clause, Tribe__Events__Featured_Events::FEATURED_EVENT_KEY );
	}

	protected function setup_where_clause() {
		$this->whereClause = " AND {$this->table_alias}.meta_value = '1' ";
	}

	/**
	 * Helper function: generates a list of field types that can be selected for those filters
	 * which support a choice of dropdown, checkbox and radio modes.
	 *
	 * @return string
	 */
	protected function get_multichoice_type_field() {
		return '';
	}
}
