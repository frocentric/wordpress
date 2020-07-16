<?php
/**
 * Handles the manipulation of the template title to correctly render it in the context of a PRO Views v2 request.
 *
 * @since   4.7.9
 *
 * @package Tribe\Events\Pro\Views\V2\Template
 */

namespace Tribe\Events\Pro\Views\V2\Template;

use Tribe__Context as Context;

/**
 * Class Title
 *
 * @since   4.7.9
 *
 * @package Tribe\Events\Pro\Views\V2\Template
 */
class Title extends \Tribe\Events\Views\V2\Template\Title {

	/**
	 * Builds the PRO View title based on context.
	 *
	 * @since 4.7.9
	 *
	 * @param bool $depth A flag to indicate how to build the taxonomy archive page title.
	 *
	 * @return string The View title, or an empty string if the rendering View is not a PRO one.
	 */
	public function build_title( $title = '', $depth = true ) {
		$context = $this->context ?: tribe_context();
		$posts   = $this->get_posts();

		$title = '';

		if ( 'all' === $context->get( 'event_display_mode' ) ) {
			$title = sprintf(
				__( 'All %1$s for %2$s', 'tribe-events-calendar-pro' ),
				tribe_get_event_label_plural_lowercase(),
				get_the_title( $context->get( 'post_id' ) )
			);
		} elseif ( 'week' === $context->get( 'event_display' ) ) {
			/**
			 * Filters the date format that should be used to render PRO views title.
			 *
			 * @since 4.7.9
			 *
			 * @param string $date_format The date format, as read from the options.
			 */
			$date_format = apply_filters(
				'tribe_events_pro_page_title_date_format',
				tribe_get_date_format( true )
			);

			$title = sprintf(
				__( '%1$s for week of %2$s', 'tribe-events-calendar-pro' ),
				$this->events_label_plural,
				date_i18n( $date_format, strtotime( tribe_get_first_week_day( $context->get( 'event_date' ) ) ) )
			);
		}

		/**
		 * Filters the view title, specific to PRO Views V2.
		 *
		 * @since 4.7.9
		 *
		 * @param string  $title   The "Events" page title as it's been generated thus far.
		 * @param bool    $depth   Whether to include the linked title or not.
		 * @param Context $context The context used to build the title, it could be the global one, or one externally
		 *                         set.
		 * @param array   $posts   An array of posts fetched by the View.
		 */
		return apply_filters( 'tribe_events_pro_views_v2_view_title', $title, $depth, $context, $posts );
	}
}
