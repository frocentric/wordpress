<?php
/**
 * The Front End Countdown Widget View.
 *
 * @package Tribe\Events\Views\V2\Views\Widgets
 * @since 5.3.0
 */

namespace Tribe\Events\Pro\Views\V2\Views\Widgets;

use DateTime;
use Tribe\Events\Views\V2\Messages;
use Tribe__Context as Context;
use Tribe__Date_Utils as Dates;

/**
 * Class Countdown_View
 *
 * @since   5.3.0
 *
 * @package Tribe\Events\Pro\Views\V2\Views\Widgets
 */
class Countdown_View extends \Tribe\Events\Views\V2\Views\Widgets\Widget_View {

	/**
	 * The slug for this view.
	 *
	 * @since 5.3.0
	 *
	 * @var string
	 */
	protected $slug = 'widget-countdown';

	/**
	 * Sets up the View repository arguments from the View context or a provided Context object.
	 *
	 * @since 5.3.0
	 *
	 * @param  Context|null $context A context to use to setup the args, or `null` to use the View Context.
	 *
	 * @return array<string,mixed> The arguments, ready to be set on the View repository instance.
	 */
	protected function setup_repository_args( Context $context = null ) {
		$context            = null !== $context ? $context : $this->context;
		$args               = parent::setup_repository_args( $context );
		$args['ends_after'] = 'now';

		return $args;
	}

	/**
	 * Overrides the base View method.
	 *
	 * @since 5.3.0
	 *
	 * @return array<string,mixed> The Widget Countdown View template vars, modified if required.
	 */
	protected function setup_template_vars() {
		$template_vars                     = parent::setup_template_vars();
		$template_vars['widget_title']     = $this->context->get( 'widget_title' );
		$template_vars['event']            = $this->context->get( 'event' );
		$template_vars['complete']         = $this->context->get( 'complete' );
		$template_vars['jsonld_enable']    = (int) $this->context->get( 'jsonld_enable' );
		$template_vars['show_seconds']     = (int) $this->context->get( 'show_seconds' );
		$template_vars['show_latest_past'] = false;

		list(
			$template_vars['count_to_date'],
			$template_vars['count_to_stamp'],
			$template_vars['event_done']
		) = $this->calculate_countdown( $template_vars['event'] );

		return $template_vars;
	}

	/**
	 * Calculates countdown data based on a provided event.
	 *
	 * @since 5.3.0
	 *
	 * @param WP_Post $event The event we're calculating for.
	 *
	 * @return array<mixed>
	 */
	public function calculate_countdown( $event ) {
		$default = [ null, null, null ];

		if ( ! $event instanceof \WP_Post ) {
			return $default;
		}

		if ( empty( $event->dates ) ) {
			return $default;
		}

		$now = Dates::build_date_object( tribe_context()->get( 'now', 'now' ) )->setTimezone( new \DateTimeZone( 'UTC' ) );

		$count_to_date  = $event->dates->start_utc->format( 'c' );
		$count_to_stamp = Dates::time_between( $count_to_date, $now->format( 'c' ) );
		$event_done     = $count_to_date < $now->format( 'c' );

		return [
			$count_to_date,
			$count_to_stamp,
			$event_done
		];
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
