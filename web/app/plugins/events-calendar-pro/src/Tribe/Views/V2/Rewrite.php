<?php
/**
 * Handles rewrite rules added or modified by PRO Views v2.
 *
 * @since   4.7.9
 *
 * @package Tribe\Events\Pro\Views\V2
 */

namespace Tribe\Events\Pro\Views\V2;

use Tribe__Events__Main as TEC;
use Tribe__Events__Organizer as Organizer;
use Tribe__Events__Rewrite as TEC_Rewrite;
use Tribe__Events__Venue as Venue;

/**
 * Class Rewrite
 *
 * @since   4.7.9
 *
 * @package Tribe\Events\Pro\Views\V2
 */
class Rewrite {
	/**
	 * Filters the base rewrite rules to add venue and organizer as translate-able pieces.
	 *
	 * @since 5.0.0
	 *
	 * @param array  $bases  Original set of bases used in TEC.
	 *
	 * @return array         Bases after adding Venue and Organizer
	 */
	public function add_base_rewrites( $bases ) {
		$bases['venue'] = [ 'venue', esc_html_x( 'venue', 'The archive for events, "/venue/" URL string component.', 'tribe-events-calendar-pro' ) ];
		$bases['organizer'] = [ 'organizer', esc_html_x( 'organizer', 'The archive for events, "/organizer/" URL string component.', 'tribe-events-calendar-pro' ) ];

		return $bases;
	}

	/**
	 * Filters the `redirect_canonical` to prevent any redirects on venue and organizer URLs.
	 *
	 * @since 5.0.0
	 *
	 * @param mixed $redirect_url URL which we will redirect to.
	 *
	 * @return string             Original URL redirect or False to prevent canonical redirect.
	 */
	public function filter_prevent_canonical_redirect( $redirect_url = null ) {
		// When dealing with admin urls bail early.
		if ( is_admin() ) {
			return $redirect_url;
		}

		$context = tribe_context();

		if ( $context->get( 'organizer_post_type', false ) ) {
			return false;
		}

		if ( $context->get( 'venue_post_type', false ) ) {
			return false;
		}

		return $redirect_url;
	}

	/**
	 * Add rewrite routes for PRO Views v2.
	 *
	 * @since 4.7.9
	 *
	 * @param TEC_Rewrite $rewrite The Events Calendar rewrite handler object.
	 */
	public function add_rewrites( TEC_Rewrite $rewrite ) {
		$rewrite->add(
			[
				'{{ venue }}',
				'([^/]+)',
				'{{ page }}',
				'(\d+)',
			],
			[
				'eventDisplay' => 'venue',
				Venue::POSTTYPE => '%1',
				'paged'=> '%2',
			]
		);
		$rewrite->add(
			[
				'{{ venue }}',
				'([^/]+)',
			],
			[
				'eventDisplay' => 'venue',
				Venue::POSTTYPE => '%1',
			]
		);

		$rewrite->add(
			[
				'{{ organizer }}',
				'([^/]+)',
				'{{ page }}',
				'(\d+)',
			],
			[
				'eventDisplay' => 'organizer',
				Organizer::POSTTYPE => '%1',
				'paged'=> '%2',
			]
		);
		$rewrite->add(
			[
				'{{ organizer }}',
				'([^/]+)',
			],
			[
				'eventDisplay' => 'organizer',
				Organizer::POSTTYPE => '%1',
			]
		);

		$rewrite->archive(
			[
				'{{ photo }}',
				'{{ page }}',
				'(\d+)',
			],
			[
				'eventDisplay' => 'photo',
				'paged'        => '%1',
			]
		);
	}

	/**
	 * Filters the geocode based rewrite rules to add rules to paginate the Map View..
	 *
	 * @since 4.7.9
	 * @since 5.1.4 Update the method to add the support for the completely localized version of the rules.
	 *
	 * @param array<string,string> $rules The geocode based rewrite rules.
	 * @param array<string,string> $bases The geocode rewrite bases.
	 *
	 * @return array The filtered geocode based rewrite rules.
	 */
	public function add_map_pagination_rules( array $rules, array $bases ) {
		/*
		 * We use this "hidden" dependency here and now because that's when we're sure the object was correctly built
		 * and ready to provide the information we need.
		 */
		$tec_bases = TEC_Rewrite::instance()->bases;
		$page_base = isset( $tec_bases->page ) ? $tec_bases->page : false;

		// Create the fully localized version of the rules.
		$updated_rules = [];
		foreach ( $rules as $rule => $query_string ) {
			// Remove the leading `(.*)`.
			$updated_rule = str_replace( '(.*)', '', $rule );
			// Replace `events/` with the regular expression that will capture all its translations, incl. English.
			$updated_rule = str_replace( $bases['base'], $tec_bases->archive . '/', $updated_rule );

			/*
			 * In the 2 following lines of code we use the bases, themselves regular expression, to match the fragment
			 * we need to replace in the regular expression that is part of the rewrite rule.
			 * We can do this as the rule, as built from the `Geo_Loc` class, will use the slugs as literal matches, not
			 * regular expression, hence we can find and replace them using the base regular expressions.
			 */

			// Replace the `category` part with the regular expression that will capture all its translations, incl. En.
			$tax          = $tec_bases->tax;
			$updated_rule = preg_replace( '~' . $tax . '~', $tax, $updated_rule, 1, $is_tax_rule );

			if ( $is_tax_rule ) {
				/*
				 * Map view category match would be the 2nd one, we need to make it become the first one as we've
				 * removed the leading `(.*)`.
				 */
				$query_string = str_replace( TEC::TAXONOMY . '=$matches[2]', TEC::TAXONOMY . '=$matches[1]', $query_string );
			}

			// Replace the `tag` part with the regular expression that will capture all its translations, incl. En.
			$tag          = $tec_bases->tag;
			$updated_rule = preg_replace( '~' . $tag . '~', $tag, $updated_rule, 1, $is_tag_rule );

			if ( $is_tag_rule ) {
				/*
				 * Map view tag match would be the 2nd one, we need to make it become the first one as we've
				 * removed the leading `(.*)`.
				 */
				$query_string = str_replace( 'tag=$matches[2]', 'tag=$matches[1]', $query_string );
			}

			$updated_rules[ $updated_rule ] = $query_string;
		}

		if ( false === $page_base ) {
			return $rules;
		}

		$pagination_rules = [];
		foreach ( $updated_rules as $regex => $rewrite ) {
			$key            = rtrim( $regex, '/?$' ) . '/' . $page_base . '/(\\d+)/?$';
			$is_tax_rule    = preg_match( '/(' . TEC::TAXONOMY . '|tag)=\$matches/', $rewrite );
			$page_match_pos = $is_tax_rule ? 2 : 1;
			$value          = false !== strpos( $rewrite, '?' ) ?
				$rewrite . '&paged=$matches[' . $page_match_pos . ']'
				: '?paged=$matches[' . $page_match_pos . ']';

			$pagination_rules[ $key ] = $value;
		}

		// It's important these rules are prepended to the pagination ones, not appended.
		return $pagination_rules + $updated_rules + $rules;
	}

	/**
	 * Filters the handled rewrite rules, the one used to parse plain links into permalinks, to add the ones
	 * managed by PRO.
	 *
	 * @since 5.0.1
	 *
	 * @param array<string,string> $handled_rules The handled rules, as produced by The Events Calendar base code; in
	 *                                            the same format used by WordPress to store and manage rewrite rules.
	 * @param array<string,string> $all_rules     All the rewrite rules handled by WordPress.
	 *
	 * @return array<string,string> The filtered rewrite rules, including the ones handled by Events PRO; in the same
	 *                              format used by WordPress to handle rewrite rules.
	 */
	public function filter_handled_rewrite_rules( array $handled_rules = [], array $all_rules = [] ) {
		$venue_rules = array_filter( $all_rules, static function ( $rewrite ) {
			return false !== strpos( $rewrite, Venue::POSTTYPE . '=$matches' );
		} );

		$organizer_rules = array_filter( $all_rules, static function ( $rewrite ) {
			return false !== strpos( $rewrite, Organizer::POSTTYPE . '=$matches' );
		} );

		return array_unique( array_merge( $handled_rules, $venue_rules, $organizer_rules ) );
	}

	/**
	 * Filters the query vars map used by the Rewrite component to parse plain links into permalinks to add the elements
	 * needed to support PRO components.
	 *
	 * @since 5.0.1
	 *
	 * @param array<string,string> $query_vars_map The query variables map, as produced by The Events Calendar code.
	 *                                             Shape is `[ <pattern> => <query_var> ].
	 *
	 * @return array<string,string> The query var map, filtered to add the query vars handled by PRO.
	 */
	public function filter_rewrite_query_vars_map( array $query_vars_map = [] ) {
		$query_vars_map['venue']     = Venue::POSTTYPE;
		$query_vars_map['organizer'] = Organizer::POSTTYPE;

		return $query_vars_map;
	}

	/**
	 * Filters The Events Calendar custom rewrite rules to fix the order and relative position of some and play
	 * nice with Views v2 canonical URL needs.
	 *
	 * The operations performed by this method are pretty expensive (filtering and sorting preserving keys) and is
	 * meant to be used once when rewrite rules are generated. It's NOT the kind of method that should run on each
	 * request.
	 *
	 * @since 5.0.3
	 *
	 * @param array<string,string> $rewrite_rules An array of The Events Calendar custom rewrite rules, in the same
	 *                                            format used by WordPress: a map of each rule regular expression to the
	 *                                            corresponding query string.
	 *
	 * @return array<string,string> The input map of The Events Calendar rewrite rules, updated to satisfy the needs
	 *                              of Views v2 canonical URL building.
	 */
	public function filter_events_rewrite_rules_custom( array $rewrite_rules ) {
		$rules_by_type = [ 'map' => [], 'week' => [], 'other' => [] ];

		// Divide the rules by type. Using the view slug is fine as it's in the query var, thus not translated.
		foreach ( $rewrite_rules as $regex => $query_string ) {
			if ( false !== strpos( $query_string, 'eventDisplay=map' ) ) {
				$rules_by_type['map'][ $regex ] = $query_string;
				continue;
			}

			if ( false !== strpos( $query_string, 'eventDisplay=week' ) ) {
				if ( false !== strpos( $regex, '/(\d{2})/?$' ) ) {
					// Discard week number rules: we do not support them in Views v2.
					continue;
				}
				$rules_by_type['week'][ $regex ] = $query_string;
				continue;
			}

			$rules_by_type['other'][ $regex ] = $query_string;
		}

		// Sort the rules to be map, week and others.
		$rewrite_rules = $rules_by_type['map'] + $rules_by_type['week'] + $rules_by_type['other'];

		return $rewrite_rules;
	}
}
