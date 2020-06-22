<?php
/**
 * Provides the rewrite rules suppor for Pro.
 *
 * @since   4.7.5
 * @package Tribe\Events\Pro\Rewrite
 */

namespace Tribe\Events\Pro\Rewrite;

use Tribe__Events__Main as TEC;

/**
 * Class Provider
 *
 * @since   4.7.5
 * @package Tribe\Events\Pro\Rewrite
 */
class Provider extends \tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 4.7.5
	 */
	public function register() {
		$this->container->singleton( 'events-pro.rewrite', Rewrite::class );
		$this->container->singleton( 'events.rewrite', Rewrite::class );
		$this->add_filters();
	}

	/**
	 * Adds the filter required to provide the rewrite support.
	 *
	 * @since 4.7.5
	 */
	protected function add_filters() {
		add_action( 'tribe_events_pre_rewrite', [ $this, 'filter_add_routes' ], 5 );
		add_filter( 'tribe_events_rewrite_base_slugs', [ $this, 'filter_add_base_slugs' ], 11 );
		add_filter( 'tribe_events_rewrite_i18n_domains', [ $this, 'filter_add_i18n_pro_domain' ], 11 );
		add_filter( 'tribe_events_rewrite_matchers_to_query_vars_map', [ $this, 'filter_add_matchers_to_query_vars_map' ], 11, 2 );
	}

	/**
	 * Add rewrite routes for custom PRO stuff and views.
	 *
	 * @since 4.7.5 Moved here from Main file.
	 *
	 * @param \Tribe__Events__Rewrite $rewrite The Tribe__Events__Rewrite object
	 *
	 * @return void
	 */
	public function filter_add_routes( $rewrite ) {
		$rewrite
			->single( [ '(\d{4}-\d{2}-\d{2})' ], [ TEC::POSTTYPE => '%1', 'eventDate' => '%2' ] )
			->single( [ '(\d{4}-\d{2}-\d{2})', '(feed|rdf|rss|rss2|atom)' ], [
				TEC::POSTTYPE => '%1',
				'eventDate'   => '%2',
				'feed'        => '%3'
			] )
			->single( [ '(\d{4}-\d{2}-\d{2})', '(\d+)', '(feed|rdf|rss|rss2|atom)' ], [
				TEC::POSTTYPE   => '%1',
				'eventDate'     => '%2',
				'eventSequence' => '%3',
				'feed'          => '%4'
			] )
			->single( [ '(\d{4}-\d{2}-\d{2})', '(\d+)' ], [
				TEC::POSTTYPE   => '%1',
				'eventDate'     => '%2',
				'eventSequence' => '%3'
			] )
			->single( [ '(\d{4}-\d{2}-\d{2})', 'embed' ], [ TEC::POSTTYPE => '%1', 'eventDate' => '%2', 'embed' => 1 ] )
			->single( [ '{{ all }}', '{{ page }}', '(\d+)' ], [
				TEC::POSTTYPE           => '%1',
				'post_type'             => TEC::POSTTYPE,
				'eventDisplay'          => 'all',
				'tribe_recurrence_list' => true,
				'page'                 => '%2'
			] )
			->single( [ '{{ all }}' ], [
				TEC::POSTTYPE           => '%1',
				'post_type'             => TEC::POSTTYPE,
				'eventDisplay'          => 'all',
				'tribe_recurrence_list' => true
			] )
			->single( [ '(\d{4}-\d{2}-\d{2})', 'ical' ], [ TEC::POSTTYPE => '%1', 'eventDate' => '%2', 'ical' => 1 ] )
			->archive( [ '{{ week }}' ], [ 'eventDisplay' => 'week' ] )
			->archive( [ '{{ week }}', '{{ featured }}' ], [ 'eventDisplay' => 'week', 'featured' => true ] )
			->archive( [ '{{ week }}', '(\d{2})' ], [ 'eventDisplay' => 'week', 'eventDate' => '%1' ] )
			->archive( [ '{{ week }}', '(\d{2})', '{{ featured }}' ], [
				'eventDisplay' => 'week',
				'eventDate'    => '%1',
				'featured'     => true
			] )
			->archive( [ '{{ week }}', '(\d{4}-\d{2}-\d{2})' ], [ 'eventDisplay' => 'week', 'eventDate' => '%1' ] )
			->archive( [ '{{ week }}', '(\d{4}-\d{2}-\d{2})', '{{ featured }}' ], [
				'eventDisplay' => 'week',
				'eventDate'    => '%1',
				'featured'     => true
			] )
			->tax( [ '{{ week }}' ], [ 'eventDisplay' => 'week' ] )
			->tax( [ '{{ week }}', '{{ featured }}' ], [ 'eventDisplay' => 'week', 'featured' => true ] )
			->tax( [ '{{ week }}', '(\d{4}-\d{2}-\d{2})' ], [ 'eventDisplay' => 'week', 'eventDate' => '%2' ] )
			->tax( [ '{{ week }}', '(\d{4}-\d{2}-\d{2})', '{{ featured }}' ], [
				'eventDisplay' => 'week',
				'eventDate'    => '%2',
				'featured'     => true
			] )
			->tag( [ '{{ week }}' ], [ 'eventDisplay' => 'week' ] )
			->tag( [ '{{ week }}', '{{ featured }}' ], [ 'eventDisplay' => 'week', 'featured' => true ] )
			->tag( [ '{{ week }}', '(\d{4}-\d{2}-\d{2})' ], [ 'eventDisplay' => 'week', 'eventDate' => '%2' ] )
			->tag( [ '{{ week }}', '(\d{4}-\d{2}-\d{2})', '{{ featured }}' ], [
				'eventDisplay' => 'week',
				'eventDate'    => '%2',
				'featured'     => true
			] )
			->archive( [ '{{ photo }}' ], [ 'eventDisplay' => 'photo' ] )
			->archive( [ '{{ photo }}', '{{ featured }}' ], [ 'eventDisplay' => 'photo', 'featured' => true ] )
			->archive( [ '{{ photo }}', '(\d{4}-\d{2}-\d{2})' ], [ 'eventDisplay' => 'photo', 'eventDate' => '%1' ] )
			->archive( [ '{{ photo }}', '(\d{4}-\d{2}-\d{2})', '{{ featured }}' ], [
				'eventDisplay' => 'photo',
				'eventDate'    => '%1',
				'featured'     => true
			] )
			->tax( [ '{{ photo }}' ], [ 'eventDisplay' => 'photo' ] )
			->tax( [ '{{ photo }}', '{{ featured }}' ], [ 'eventDisplay' => 'photo', 'featured' => true ] )
			->tag( [ '{{ photo }}' ], [ 'eventDisplay' => 'photo' ] )
			->tag( [ '{{ photo }}', '{{ featured }}' ], [ 'eventDisplay' => 'photo', 'featured' => true ] );
	}

	/**
	 * Add the required bases for the Pro Views
	 *
	 * @since 4.7.5 Moved here from Main file.
	 *
	 * @param array $bases Bases that are already set
	 *
	 * @return array         The modified version of the array of bases
	 */
	public function filter_add_base_slugs( $bases = [] ) {

		// Support the original and translated forms for added robustness
		$bases['all']   = [ 'all', tribe( 'events-pro.main' )->all_slug ];
		$bases['week']  = [ 'week', tribe( 'events-pro.main' )->weekSlug ];
		$bases['photo'] = [ 'photo', tribe( 'events-pro.main' )->photoSlug ];

		return $bases;
	}

	/**
	 * Add the required bases for the Pro Views
	 *
	 * @since 4.7.5 Moved here from Main file.
	 *
	 * @param array $bases Bases that are already set
	 *
	 * @return array         The modified version of the array of bases
	 */
	public function filter_add_matchers_to_query_vars_map( $matchers = [], $rewrite = null ) {

		$matchers['photo'] = 'eventDisplay';
		$matchers['week'] = 'eventDisplay';
		$matchers['map'] = 'eventDisplay';

		return $matchers;
	}


	/**
	 * We add the Pro to the translations domains.
	 *
	 * @since 4.7.5 Moved here from Main file.
	 *
	 * @param array $domains
	 *
	 * @return array         The modified version of the array of domains
	 */
	public function filter_add_i18n_pro_domain( $domains = [] ) {
		$domains['tribe-events-calendar-pro'] = tribe( 'events-pro.main' )->pluginDir . 'lang/';

		return $domains;
	}
}
