<?php
use Tribe__Context as Context;
use Tribe__Utils__Array as Arr;
/**
 * Class Filterbar_Filter_Taxonomy
 */
abstract class Filterbar_Filter_Taxonomy extends Tribe__Events__Filterbar__Filters__Category {
	use Tribe\Events\Filterbar\Views\V2\Filters\Context_Filter;

	protected $taxonomy;

	/**
	 * Get the taxonomy name for this filter
	 *
	 * @return string
	 */
	abstract protected function get_taxonomy();

	protected function settings() {
		$this->title = $this->get_title();
		$this->type = $this->get_type();
		$this->taxonomy = $this->get_taxonomy();
	}

	protected function get_values() {
		$terms = [];

		// Load all available event categories
		$source = $this->get_taxonomy_terms();
		if ( empty( $source ) || is_wp_error( $source ) ) {
			return [];
		}

		// Preprocess the terms
		foreach ( $source as $term ) {
			$terms[ (int) $term->term_id ] = $term;
			$term->parent                  = (int) $term->parent;
			$term->depth                   = 0;
			$term->children                = [];
		}

		// Initially copy the source list of terms to our ordered list
		$ordered_terms = $terms;

		// Re-order!
		foreach ( $terms as $id => $term ) {
			// Skip root elements
			if ( 0 === $term->parent ) {
				continue;
			}

			// Reposition child terms within the ordered terms list
			unset( $ordered_terms[ $id ] );
			$term->depth                             = $this->get_depth( $term );
			$terms[ $term->parent ]->children[ $id ] = $term;
		}

		// Finally flatten out and return
		return parent::flattened_term_list( $ordered_terms );
	}

	protected function get_taxonomy_terms() {
		$args = [
			'fields' => 'ids',
			'post_type' => Tribe__Events__Main::POSTTYPE,
			'posts_per_page' => 100,
			'start_date' => 'now',
		];
		// build an array of post IDs
		$postids = tribe_get_events( $args );
		$key = sprintf( '%1$s_%2$s', $this->taxonomy, md5( implode( ',', $postids ) ) );
		// get taxonomy values based on array of IDs
		$terms = get_transient( $key );

		if ( $terms === false ) {
			$terms = wp_get_object_terms( $postids, $this->taxonomy, [ 'orderby' => 'name', 'order' => 'ASC' ] );
			set_transient( $key, $terms, 300 );
		}

		return $terms;
	}

	/**
	 * Get Term Depth
	 *
	 * @since 4.5
	 *
	 * @param     $term
	 * @param int $level
	 *
	 * @return int
	 */
	protected function get_depth( $term, $level = 0 ) {
		if ( ! $term instanceof WP_Term ) {
			return 0;
		}

		if ( 0 === $term->parent ) {
			return $level;
		} else {
			$level++;
			$term = get_term_by( 'id', $term->parent, $this->taxonomy );

			return $this->get_depth( $term, $level );
		}

	}

	/**
	 * This method will only be called when the user has applied the filter (during the
	 * tribe_events_pre_get_posts action) and sets up the taxonomy query, respecting any
	 * other taxonomy queries that might already have been setup (whether by The Events
	 * Calendar, another plugin or some custom code, etc).
	 *
	 * @see Tribe__Events__Filterbar__Filter::pre_get_posts()
	 *
	 * @param WP_Query $query
	 */
	// phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded
	protected function pre_get_posts( WP_Query $query ) {
		$new_rules      = [];
		$existing_rules = (array) $query->get( 'tax_query' );
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$values         = (array) $this->currentValue;

		// if select display and event category has children get all those ids for query
		if ( 'select' === $this->type ) {

			$categories = get_categories(
				[
					'taxonomy' => 'tribe_events_cat',
					'child_of' => current( $values ),
				]
			);

			if ( ! empty( $categories ) ) {
				foreach ( $categories as $category ) {
					$values[] = $category->term_id;
				}
			}
		} elseif ( 'multiselect' === $this->type ) {
			// Any value that will evaluate to empty, we drop.
			$values = array_filter( Arr::list_to_array( $values ) );
		}

		$new_rules[] = [
			'taxonomy' => $this->taxonomy,
			'operator' => 'IN',
			'terms'    => array_map( 'absint', $values ),
		];

		/**
		 * Controls the relationship between different taxonomy queries.
		 *
		 * If set to an empty value, then no attempt will be made by the additional field filter
		 * to set the meta_query "relation" parameter.
		 *
		 * @var string $relation "AND"|"OR"
		 */
		$relationship = apply_filters( 'tribe_events_filter_taxonomy_relationship', 'AND' );

		/**
		 * If taxonomy filter meta queries should be nested and grouped together.
		 *
		 * The default is true in WordPress 4.1 and greater, which allows for greater flexibility
		 * when combined with taxonomy queries added by other filters/other plugins.
		 *
		 * @var bool $group
		 */
		$nest = apply_filters( 'tribe_events_filter_nest_taxonomy_queries', version_compare( $GLOBALS['wp_version'], '4.1', '>=' ) );

		if ( $nest ) {
			$new_rules = [
				__CLASS__ => $new_rules,
			];
		}

		$tax_query = array_merge_recursive( $existing_rules, $new_rules );

		// Apply the relationship (we leave this late, or the recursive array merge would potentially cause duplicates)
		if ( ! empty( $relationship ) && $nest ) {
			$tax_query[ __CLASS__ ]['relation'] = $relationship;
		} elseif ( ! empty( $relationship ) ) {
			$tax_query['relation'] = $relationship;
		}

		// Apply our new meta query rules
		$query->set( 'tax_query', $tax_query );
	}

	/**
	 * Parses the raw value from the context to the format used by the filter.
	 *
	 * @since 1.15.2
	 *
	 * @param array|string $raw_value A category term ID, or an array of category term IDs.
	 *
	 * @return array An array of time of category term ids.
	 */
	protected function parse_value( $raw_value ) {
		return array_filter( (array) $raw_value );
	}

	/**
	 * Builds the value that should be set in the query argument for the Category filter.
	 *
	 * @since 4.9.0
	 *
	 * @param string|array $value       The value, as received from the context.
	 * @param string       $context_key The key used to fetch the `$value` from the Context.
	 * @param Context      $context     The context instance.
	 *
	 * @return array An array of term IDs.
	 */
	public static function build_query_arg_value( $value, $context_key, Context $context ) {
		return Arr::list_to_array( $value, ',' );
	}
}
