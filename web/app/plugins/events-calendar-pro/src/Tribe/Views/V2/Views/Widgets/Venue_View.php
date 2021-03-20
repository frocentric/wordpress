<?php
/**
 * The Front End Featured Venue Widget.
 *
 * @package Tribe\Events\Views\V2\Views\Widgets
 * @since 5.3.0
 */

namespace Tribe\Events\Pro\Views\V2\Views\Widgets;

use Tribe\Events\Views\V2\Messages;
use Tribe__Context as Context;
use Tribe\Events\Views\V2\Views\Widgets\Widget_View;

/**
 * Class Venue_View
 *
 * @since   5.3.0
 *
 * @package Tribe\Events\Views\V2\Widgets
 */
class Venue_View extends Widget_View {

	/**
	 * The slug for this view.
	 *
	 * @since 5.3.0
	 *
	 * @var string
	 */
	protected $slug = 'widget-venue';

	/**
	 * Sets up the View repository arguments from the View context or a provided Context object.
	 *
	 * @since 5.3.0
	 *
	 * @param Context|null $context A context to use to setup the args, or `null` to use the View Context.
	 *
	 * @return array<string,mixed> The arguments, ready to be set on the View repository instance.
	 */
	protected function setup_repository_args( Context $context = null ) {
		$context            = null !== $context ? $context : $this->context;
		$args               = parent::setup_repository_args( $context );
		$args['venue']      = $context->get( 'venue', false );
		$args['ends_after'] = 'now';

		return $args;
	}

	/**
	 * Returns the widget "view more" url.
	 *
	 * @since 5.3.0
	 *
	 * @return string The widget "view more" url.
	 */
	public function get_view_more_link() {
		$venue_id  = $this->context->get( 'venue' );
		$venue_obj = tribe_get_venue_object( $venue_id );

		return $venue_obj->permalink;
	}

	/**
	 * Returns the widget "view more" link title attribute.
	 * Adds some context to the link for screen readers.
	 *
	 * @since 5.3.0
	 *
	 * @return string The widget "view more" link title attribute.
	 */
	public function get_view_more_title() {
		$venue_id  = $this->context->get( 'venue' );
		$venue_obj = tribe_get_venue_object( $venue_id );

		$text = sprintf(
			__( /* Translators: 1: lowercase plural event term 2: venue name */ 'View more %1$s at %2$s.', 'tribe-events-calendar-pro' ),
			tribe_get_event_label_plural_lowercase(),
			$venue_obj->post_title
		);

		return $text;
	}

	/**
	 * Overrides the base View method.
	 *
	 * @since 5.3.0
	 *
	 * @return array<string,mixed> The Widget List View template vars, modified if required.
	 */
	protected function setup_template_vars() {
		$template_vars = parent::setup_template_vars();

		$venue_id  = $this->context->get( 'venue' );
		$venue_obj = tribe_get_venue_object( $venue_id );

		// Here update, add and remove from the default template vars.
		$template_vars['hide_if_no_upcoming_events'] = $this->context->get( 'no_upcoming_events' );
		$template_vars['jsonld_enable']              = (int) $this->context->get( 'jsonld_enable' );
		$template_vars['show_latest_past']           = false;
		$template_vars['widget_title']               = $this->context->get( 'widget_title' );
		$template_vars['venue']                      = $venue_obj;

		return $template_vars;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function setup_messages( array $events ) {
		if ( ! empty( $events ) ) {
			return;
		}

		$keyword = $this->context->get( 'keyword', false );
		$this->messages->insert(
			Messages::TYPE_NOTICE,
			Messages::for_key( 'no_upcoming_events', trim( $keyword ) )
		);
	}

	/**
	 * Overrides the base method to return an empty array, since the widget will not use breadcrumbs.
	 *
	 * @since 5.3.0
	 *
	 * @return array<array<string,string>> An empty array, the widget will not use breadcrumbs.
	 */
	protected function get_breadcrumbs() {
		return [];
	}
}
