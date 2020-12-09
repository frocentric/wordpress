<?php
/**
 * Renders the events part of a series in a list-like layout.
 *
 * @since   4.7.5
 * @package Tribe\Events\PRO\Views\V2\Views
 */

namespace Tribe\Events\Pro\Views\V2\Views;

use Tribe\Events\Views\V2\Messages;
use Tribe\Events\Views\V2\Views\List_View;
use Tribe__Context as Context;
use Tribe__Events__Main as TEC;
use Tribe\Events\Pro\Rewrite\Rewrite as Rewrite;
use Tribe\Events\Views\V2\Utils;

/**
 * Class All_View
 *
 * @since   4.7.5
 *
 * @package Tribe\Events\PRO\Views\V2\Views
 */
class All_View extends List_View {
	/**
	 * Slug for this view
	 *
	 * @since 4.7.5
	 *
	 * @var string
	 */
	protected $slug = 'all';

	/**
	 * Differently from other archives we're using WordPress page-in-post mechanism in this class.
	 *
	 * @since 4.7.5
	 *
	 * @var string
	 */
	protected $page_key = 'page';

	/**
	 * The series parent post name.
	 *
	 * @var string
	 */
	protected $post_name;

	/**
	 * The series parent post ID.
	 *
	 * @var int
	 */
	protected $post_id;

	/**
	 * Visibility for this view.
	 *
	 * @since 4.7.5
	 * @since 4.7.9 Made the property static.
	 *
	 * @var bool
	 */
	protected static $publicly_visible = false;

	/**
	 * Whether the View should display the events bar or not.
	 *
	 * @since 4.7.9
	 *
	 * @var bool
	 */
	protected $display_events_bar = false;

	/**
	 * All_View constructor.
	 *
	 * Overrides the base View constructor to change the rewrite handler.
	 *
	 * @param Messages $messages
	 */
	public function __construct( Messages $messages ) {
		parent::__construct( $messages );
		$this->rewrite = new Rewrite();
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_html() {
		/*
		 * Since this view has historically being rendered with the `list` one let's allow developers to define
		 * templates for the `all` view, but fallback on the `list` one if not found.
		 */
		if ( $this->template->get_base_template_file() === $this->template->get_template_file() ) {
			$this->template_slug = 'list';
		}

		return parent::get_html();
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_past_url( $canonical = false, $page = 1 ) {
		$event_display_key = Utils\View::get_past_event_display_key();
		$query_args        = [ $event_display_key => 'past' ];

		if ( $page > 1 ) {
			$query_args['paged'] = $page;
		}

		return add_query_arg( $query_args, $this->get_url( $canonical ) );
	}

	/**
	 * {@inheritDoc}
	 */
	protected function setup_repository_args( Context $context = null ) {
		$args = parent::setup_repository_args( $context );

		$context = null !== $context ? $context : $this->context;

		$post_name = $context->get( 'name', false );

		if ( false === $post_name ) {
			// This is weird but let's show the user events anyway.
			return $args;
		}

		$post_id = tribe_events()->where( 'name', $post_name )->fields( 'ids' )->first();

		if ( empty( $post_id ) ) {
			// This is weirder but let's show the user events anyway.
			return $args;
		}

		$args['hide_subsequent_recurrences'] = false;
		$args['in_series'] = $post_id;
		$this->post_name   = $post_name;
		$this->post_id     = $post_id;

		return $args;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_show_datepicker_submit() {
		$live_refresh = tribe_get_option( 'liveFiltersUpdate', 'automatic' );
		return 'manual' === $live_refresh;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_url( $canonical = false, $force = false ) {
		$query_args = [
			TEC::POSTTYPE           => $this->post_name,
			'post_type'             => TEC::POSTTYPE,
			'eventDisplay'          => 'all',
			'tribe_recurrence_list' => true,
		];

		$page = $this->url->get_current_page();

		if ( $page > 1 ) {
			$query_args[ $this->page_key ] = $page;
		}

		$url = add_query_arg( array_filter( $query_args ), home_url() );

		if ( $canonical ) {
			$url = tribe( 'events-pro.rewrite' )->get_clean_url( $url, $force );
		}

		$event_display_key = Utils\View::get_past_event_display_key();
		$event_display_mode = $this->context->get( 'event_display_mode', false );
		if ( 'past' === $event_display_mode ) {
			$url = add_query_arg( [ $event_display_key => $event_display_mode ], $url );
		}

		$event_date = $this->context->get( 'event_date', false );
		if ( ! empty( $event_date ) ) {
			// If there's a date set, then add it as a query argument.
			$url = add_query_arg( [ 'tribe-bar-date' => $event_date ], $url );
		}

		$url = $this->filter_view_url( $canonical, $url );

		return $url;
	}

	/**
	 * Setup the breadcrumbs for the "All" view.
	 *
	 * @since 4.7.9
	 *
	 * @param array $breadcrumbs The breadcrumbs array.
	 * @param array $view        The instance of the view being rendered.
	 *
	 * @return array The filtered breadcrums
	 *
	 * @see \Tribe\Events\Views\V2\View::get_breadcrumbs() for where this code is applying.
	 */
	public function setup_breadcrumbs( $breadcrumbs, $view ) {

		$breadcrumbs[] = [
			'link'  => tribe_get_events_link(),
			'label' => tribe_get_event_label_plural(),
		];

		$breadcrumbs[] = [
			'link'  => '',
			'label' => get_the_title( $view->post_id ),
		];

		return $breadcrumbs;
	}
}
