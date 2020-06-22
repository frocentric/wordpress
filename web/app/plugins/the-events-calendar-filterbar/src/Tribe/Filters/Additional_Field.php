<?php
use Tribe__Utils__Array as Arr;

/**
 * Represents an additional field filter: can be used for all the standard
 * additional field types supported by Events Calendar PRO.
 *
 * This can also be instantiated for non-ECP custom fields - that is, regular
 * WP post meta fields not managed by ECP - if desired.
 */
class Tribe__Events__Filterbar__Filters__Additional_Field extends Tribe__Events__Filterbar__Filter {
	public $type = 'select';

	/**
	 * The actual meta key to reference in queries.
	 *
	 * @var string
	 */
	protected $meta_key = '';

	/**
	 * Default logic setting (match any).
	 *
	 * @var string
	 */
	protected $logic = 'or';


	/**
	 * @param string $name title used for the filter
	 * @param string $slug public slug exposed in filter queries
	 * @param string $key  meta key of the custom field
	 */
	public function __construct( $name, $slug, $key ) {
		parent::__construct( $name, $slug );
		$this->meta_key = $key;
	}

	/**
	 * Load the filter settings - including the logic mode.
	 */
	protected function settings() {
		parent::settings();
		$this->logic = $this->get_logic();
	}

	/**
	 * Return the filter settings form.
	 *
	 * @return string
	 */
	public function get_admin_form() {
		return $this->get_title_field()
		     . $this->get_multichoice_type_field()
		     . $this->get_logic_field();
	}

	/**
	 * Produces a logic control for the filter settings: used to dictate if AND or OR
	 * logic should be used by this filter.
	 *
	 * @return string
	 */
	protected function get_logic_field() {
		$name = $this->get_admin_field_name( 'logic' );

		$and_button = sprintf( '<label><input type="radio" name="%s" value="and" %s /> %s</label>',
			$name,
			checked( $this->logic, 'and', false ),
			_x( 'Match all', 'additional fields filter logic setting', 'tribe-events-filter-view' )
		);

		$or_button = sprintf( '<label><input type="radio" name="%s" value="or" %s /> %s</label>',
			$name,
			checked( $this->logic, 'or', false ),
			_x( 'Match any', 'additional fields filter logic setting', 'tribe-events-filter-view' )
		);

		return '<div class="tribe_events_active_filter_logic_options">'
		       . _x( 'Logic:', 'additional fields filter logic setting', 'tribe-events-filter-view' )
		       . $and_button
		       . $or_button
		       . '</div>';
	}

	/**
	 * Return the logic setting ("and"|"or").
	 *
	 * @return string
	 */
	protected function get_logic() {
		$current_active_filters = Tribe__Events__Filterbar__View::instance()->get_filter_settings();

		$logic = isset( $current_active_filters[ $this->slug ][ 'logic' ] )
			? $current_active_filters[ $this->slug ][ 'logic' ]
			: $this->logic;

		return apply_filters( 'tribe_events_filter_additional_field_logic', $logic, $this->slug );
	}

	/**
	 * Return a list of possible values for this filter. This should be an array of arrays,
	 * with each inner array structured as follows:
	 *
	 *     [ 'name'  => 'some_name'
	 *       'value' => 'actual_value' ]
	 *
	 * @since 4.10.0 - Add support for Multiselect and Select Values
	 *
	 * @return array
	 */
	protected function get_values() {
		$values = Tribe__Events__Filterbar__Additional_Fields__Values::fetch( $this->meta_key );
		$delimiter = '|';

		// Filter out any empty/null values that have crept in
		$values = array_filter( (array) $values );

		// If value is no equal to 0 and has no values return the empty array or filtered values.
		if ( 0 !== $values && empty( $values ) ) {
			/**
			 * Filters the values returned for the current additional field filter.
			 *
			 * @since 4.10.0
			 *
			 * @param array  $values   An array of possible values to use as filters.
			 * @param string $meta_key The name of the additional field.
			 */
			return (array) apply_filters( 'tribe_events_filters_additional_field_values', $values, $this->meta_key );
		}

		// When a checkbox, multiselect, or select is used it can also have multiple values stored in a single post.
		if ( 'checkbox' === $this->type || 'multiselect' === $this->type || 'select' === $this->type ) {
			$values = array_map( static function( $value ) use ( $delimiter ) {
				return (array) Arr::list_to_array( $value, $delimiter );
			}, $values );

			// Flatten the array of values.
			$values = array_unique( array_merge( ...$values ) );
		}

		natcasesort( $values );

		// Convert each element into a name/value array as expected by the calling method
		foreach ( $values as &$single_value ) {
			$single_value = array(
				'name'  => $single_value,
				'value' => $single_value
			);
		}

		/**
		 * Dictate the values returned for the current additional field filter.
		 *
		 * @var array  $values
		 * @var string $meta_key
		 */
		return (array) apply_filters( 'tribe_events_filters_additional_field_values', $values, $this->meta_key );
	}

	/**
	 * This method will only be called when the user has applied the filter (during the
	 * tribe_events_pre_get_posts action) and sets up the meta query, respecting any
	 * meta query params that have already been set up by The Events Calendar, other
	 * filter objects etc.
	 *
	 * @see Tribe__Events__Filterbar__Filter::pre_get_posts()
	 *
	 * @param WP_Query $query
	 */
	protected function pre_get_posts( WP_Query $query ) {
		$new_rules      = array();
		$existing_rules = (array) $query->get( 'meta_query' );
		$values         = (array) $this->currentValue;
		$meta_key       = 'checkbox' === $this->type ? '_' . $this->meta_key : $this->meta_key;

		// AND logic: match posts where all of the supplied values have been applied
		if ( 'and' === $this->logic ) {
			foreach ( $values as $single_value ) {
				$new_rules[] = array(
					'key'   => $meta_key,
					'value' => $single_value,
				);
			}
		}
		// OR logic: match any posts so long as at least one value has been applied
		else {
			$new_rules[] = array(
				'key'     => $meta_key,
				'value'   => $values,
				'compare' => 'IN'
			);
		}

		/**
		 * Controls the relationship between different additional field meta queries.
		 *
		 * If set to an empty value, then no attempt will be made by the additional field filter
		 * to set the meta_query "relation" parameter.
		 *
		 * @var string $relation "AND"|"OR"
		 */
		$relationship = apply_filters( 'tribe_events_filter_additional_fields_relationship', 'AND' );

		/**
		 * If additional field filter meta queries should be nested and grouped together.
		 *
		 * The default is true in WordPress 4.1 and greater, which allows for greater flexibility
		 * when combined with meta queries added by other filters/other plugins.
		 *
		 * @var bool $group
		 */
		$nest = apply_filters( 'tribe_events_filter_additional_fields_nest_meta_queries',
			version_compare( $GLOBALS['wp_version'], '4.1', '>=' )
		);

		if ( $nest ) {
			$new_rules = array(
				__CLASS__ => $new_rules,
			);
		}

		$meta_query = array_merge_recursive( $existing_rules, $new_rules );

		// Apply the relationship (we leave this late, or the recursive array merge would potentially cause duplicates)
		if ( ! empty( $relationship ) && $nest ) {
			$meta_query[ __CLASS__ ][ 'relation' ] = $relationship;
		} elseif ( ! empty( $relationship ) ) {
			$meta_query[ 'relation' ] = $relationship;
		}

		// Apply our new meta query rules
		$query->set( 'meta_query', $meta_query );
	}
}
