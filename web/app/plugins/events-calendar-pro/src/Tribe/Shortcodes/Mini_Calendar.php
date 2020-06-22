<?php
/**
 * Implements a shortcode that wraps the existing calendar widget.
 *
 * Basic usage is as follows:
 *
 *     [tribe_mini_calendar]
 *
 * Slightly more advanced usage, demonstrating tag and category filtering, is as follows:
 *
 *     [tribe_mini_calendar tag="black-swan-event, #20, #60" categories="twist,samba, #491, groove"]
 *
 * Note that slugs and numeric IDs are both acceptable within comma separated lists of terms
 * but IDs must be prefixed with a # symbol (this is because a number-only slug is possible, so
 * we need to be able to differentiate between them).
 */
class Tribe__Events__Pro__Shortcodes__Mini_Calendar extends Tribe__Events__Pro__Shortcodes__Filtered_Shortcode {
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
	 * Default arguments expected by the calendar widget.
	 *
	 * @var array
	 */
	protected $default_args = array(
		'before_widget' => '',
		'before_title'  => '',
		'title'         => '',
		'after_title'   => '',
		'after_widget'  => '',

		'tag'  => '',
		'tags' => '',

		'category'   => '',
		'categories' => '',

		'count' => '',
		'limit' => '',
	);

	protected $arguments = array();


	public function __construct( $attributes ) {
		$this->arguments = shortcode_atts( $this->default_args, $attributes );
		$this->taxonomy_filters();
		Tribe__Events__Pro__Widgets::enqueue_calendar_widget_styles();

		// Support both 'count' and 'limit' attributes (the latter overrides the former)
		$count = strlen( $this->arguments['count'] ) ? $this->arguments['count'] : null;
		$count = strlen( $this->arguments['limit'] ) ? $this->arguments['limit'] : $count;

		// Normalize
		$count = trim( $count );

		// If a count was not specified, ensure the count argument is unset (so the default value is used)
		if ( 0 === strlen( $count ) ) {
			unset( $this->arguments['count'] );
		} // Otherwise ensure it is an absolute integer
		else {
			$this->arguments['count'] = absint( $count );
		}

		ob_start();
		the_widget( 'Tribe__Events__Pro__Mini_Calendar_Widget', $this->arguments, $this->arguments );
		$this->output = ob_get_clean();
	}
}
