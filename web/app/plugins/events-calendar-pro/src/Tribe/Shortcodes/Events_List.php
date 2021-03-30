<?php

use \Tribe\Events\Views\V2\Widgets\Widget_List;

/**
 * Implements a shortcode that wraps the existing advanced events list widget.
 *
 * Basic usage is as follows:
 *
 *     [tribe_events_list]
 *
 * Slightly more advanced usage, demonstrating tag and category filtering, is as follows:
 *
 *     [tribe_events_list tag="black-swan-event, #20, #60" categories="twist,samba, #491, groove"]
 *
 * Note that slugs and numeric IDs are both acceptable within comma separated lists of terms
 * but IDs must be prefixed with a # symbol (this is because a number-only slug is possible, so
 * we need to be able to differentiate between them).
 *
 * You can also control the amount of information that is displayed per event (just as you might
 * if configuring the advanced list widget through its normal UI). For example, to include the
 * venue city and organizer details, you could do:
 *
 *     [tribe_events_list city="1" organizer="1"]
 *
 * List of optional information attributes:
 *
 *     street, city, cost, country, organizer, phone, region, venue, zip
 *
 */

class Tribe__Events__Pro__Shortcodes__Events_List extends Tribe__Events__Pro__Shortcodes__Filtered_Shortcode {
	public $output = '';

	/**
	 * The shortcode allows filtering by event categories and by post tags,
	 * in line with what the calendar widget itself supports.
	 *
	 * @var array
	 */
	protected $tax_relationships = array(
		'categories' => Tribe__Events__Main::TAXONOMY,
		'tags' => 'post_tag',
	);

	/**
	 * Default arguments expected by the advanced list widget.
	 *
	 * @var array
	 */
	protected $default_args = array(
		// General widget properties
		'before_widget' => '',
		'before_title'  => '',
		'title'         => '',
		'after_title'   => '',
		'after_widget'  => '',

		// Taxonomy properties
		'tag'        => '',
		'tags'       => '',
		'category'   => '',
		'categories' => '',

		// Events to show
		'limit'                => '',
		'no_upcoming_events'   => '',
		'featured_events_only' => false,
		'tribe_is_list_widget' => true,

		// Optional additional information to include per event
		'venue'     => '',
		'country'   => '',
		'address'   => '',
		'street'    => '',
		'city'      => '',
		'region'    => '',
		'zip'       => '',
		'phone'     => '',
		'cost'      => '',
		'organizer' => '',
	);

	protected $arguments = array();


	public function __construct( $attributes ) {
		$this->arguments = shortcode_atts( $this->default_args, $attributes );
		$this->taxonomy_filters();

		/**
		 * Allows hot-swapping the widget class for different versions of the widget.
		 *
		 * @since 5.2.0
		 *
		 * @param string              $widget_class The widget class name we want to implement.
		 * @param array<string,mixed> $arguments    The widget arguments.
		 */
		$widget_class = apply_filters( 'tribe_events_pro_shortcodes_list_widget_class', Tribe__Events__Pro__Advanced_List_Widget::class, $this->arguments );

		if ( Tribe__Events__Pro__Advanced_List_Widget::class === $widget_class ) {
			Tribe__Events__Pro__Widgets::enqueue_calendar_widget_styles();
		}

		if ( ! empty( $this->arguments['category'] ) ) {
			$this->arguments['tribe_events_cat'] = $this->arguments['category'];
		}

		$this->handle_shortcode_widget_taxonomy();

		ob_start();

		the_widget( $widget_class, $this->arguments, $this->arguments );

		$this->output = ob_get_clean();
	}

	/**
	 * Handles adding the taxonomy info from shortcodes
	 * which use different formats for taxonomy params.
	 *
	 * @since 5.2.0
	 *
	 * @return void
	 */
	public function handle_shortcode_widget_taxonomy() {
		$filters    = [];
		$tag        = ! empty( $this->arguments['tag'] ) ? (array) $this->arguments['tag'] : [];
		$tags       = ! empty( $this->arguments['tags'] ) ? implode( ',', (array) $this->arguments['tags'] ) : [];
		$tags       = array_filter( array_unique( array_merge( (array) $tag, (array) $tags ) ) );
		$category   = ! empty( $this->arguments['category'] ) ? (array) $this->arguments['category'] : [];
		$categories = ! empty( $this->arguments['categories'] ) ? implode( ',', $this->arguments['categories'] ) : [];
		$categories = array_filter( array_unique( array_merge( $category, $categories ) ) );

		if ( ! empty( $categories ) ) {
			$cat_ids = array_map(
				function( $param ) {
					return $this->get_term_id( $param, $this->tax_relationships['categories'] );
				},
				$categories
			);

			$filters[ $this->tax_relationships['categories'] ] = array_filter( $cat_ids );
		}

		if ( ! empty( $tags ) ) {
			$tag_ids = array_map(
				function( $param ) {
					return $this->get_term_id( $param, $this->tax_relationships['tags'] );
				},
				$tags
			);

			$filters[ $this->tax_relationships['tags'] ] = array_filter( $tag_ids );
		}

		$this->arguments['filters'] = wp_json_encode( $filters );
	}

	/**
	 * Gets the term ID from a slug or ID in the formats
	 * 'slug'
	 * 123 || '123'|| '#123'
	 *
	 * @since 5.2.0
	 *
	 * @param string|int $param The slug or ID for the term.
	 * @param string     $taxonomy The term taxonomy.
	 *
	 * @return int|false The ID or false if the term is not found.
	 */
	public function get_term_id( $param, $taxonomy ) {
		$param    = preg_replace( '/^#/', '', $param );
		$term_by  = is_numeric( $param ) ? 'ID' : 'slug';
		$term_obj = get_term_by( $term_by, $param, $taxonomy );

		if ( ! $term_obj instanceof \WP_Term ) {
			return false;
		}

		return $term_obj->term_id;
	}
}
