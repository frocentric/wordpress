<?php
/**
 * Tribe (The Events Calendar) Hooks
 *
 * @package     Frocentric/Customizations
 * @version     1.0.0
 */

namespace Frocentric\Customizations;

use Frocentric\Constants;
use Frocentric\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tribe Class.
 */
class Tribe {

	const EVENT_TAXONOMIES = array(
		'audience' => Tribe\Filterbar_Filter_Audience::class,
		'discipline' => Tribe\Filterbar_Filter_Discipline::class,
		'interest' => Tribe\Filterbar_Filter_Interest::class,
	);

	/**
	 * Prints an error message and ensures that we don't hit bugs on Select2
	 *
	 * @since  4.6
	 *
	 * @param string $message
	 *
	 * @return void
	 */
	private static function ajax_error( $message ) {
		$data = array(
			'message' => $message,
			'results' => array(),
		);

		wp_send_json_error( $data );
	}

	private static function get_eventbrite_event_id( $url ) {
		$regex = '/^https?:\/\/(?:www\.)?eventbrite(?:\.[a-z]{2,3}){1,2}\/e\/.*-(\d+)(?:\/|\?)?.*/';
		$matches = array();
		// Capture event ID from URL.
		preg_match( $regex, $url, $matches );

		if ( $matches && count( $matches ) > 1 ) {
			return $matches[1];
		}

		return false;
	}

	/**
	 * Hook in methods.
	 *
	 * @return void
	 */
	public static function hooks() {
		if ( ! Utils::is_request( Constants::LOGIN_REQUEST ) ) {
			// Actions.
			add_action( 'plugins_loaded', array( __CLASS__, 'plugins_loaded' ) );
			add_action( 'tribe_events_community_form_before_template', array( __CLASS__, 'tribe_events_community_form_before_template' ) );
			add_action( 'tribe_events_event_update_args', array( __CLASS__, 'tribe_events_event_update_args' ), 10, 3 );
			add_action( 'tribe_events_single_event_after_the_meta', array( __CLASS__, 'tribe_events_single_event_after_the_meta' ) );
			add_action( 'wp_ajax_aggregator_fetch_import', array( __CLASS__, 'wp_ajax_aggregator_fetch_import' ) );

			// Filters.
			add_filter( 'tribe_aggregator_before_save_event', array( __CLASS__, 'tribe_aggregator_before_save_event' ), 10, 2 );
			add_filter( 'tribe_aggregator_find_matching_organizer', array( __CLASS__, 'tribe_aggregator_find_matching_organizer' ), 10, 2 );
			add_filter( 'tribe_aggregator_new_event_post_status_before_import', array( __CLASS__, 'tribe_aggregator_new_event_post_status_before_import' ), 10, 3 );
			add_filter( 'tribe_context_locations', array( __CLASS__, 'tribe_context_locations' ) );
			add_filter( 'tribe_dropdown_search_terms', array( __CLASS__, 'tribe_dropdown_search_terms' ), 10, 5 );
			add_filter( 'tribe_events_community_allowed_event_fields', array( __CLASS__, 'tribe_events_community_allowed_event_fields' ), 10, 1 );
			add_filter( 'tribe_events_community_submission_message', array( __CLASS__, 'tribe_events_community_submission_message' ), 10, 2 );
			add_action( 'tribe_events_filters_create_filters', array( __CLASS__, 'tribe_events_filters_create_filters' ) );
			add_filter( 'tribe_events_filter_bar_context_to_filter_map', array( __CLASS__, 'tribe_events_filter_bar_context_to_filter_map' ) );
			add_filter( 'tribe_get_cost', array( __CLASS__, 'tribe_get_cost' ), 10, 3 );
			add_filter( 'tribe_tickets_get_ticket_max_purchase', array( __CLASS__, 'tribe_tickets_get_ticket_max_purchase' ), 10, 3 );
		}
	}

	/**
	 * Overrides parse_request event hook in The Events Calendar Community Events plugin
	 */
	public static function plugins_loaded() {
		if ( class_exists( '\WP_Router' ) ) {
			remove_action( 'parse_request', array( \WP_Router::get_instance(), 'parse_request' ), 10, 1 );
			add_action( 'parse_request', array( __CLASS__, 'shim_parse_request' ), 10, 1 );
		}

		if ( function_exists( 'wpmus_maybesync_newuser' ) ) {
			// phpcs:ignore
			global $wpmus_newUserSync;

			//phpcs:ignore
			if ( $wpmus_newUserSync === 'yes' ) {
				remove_action( 'wp_login', 'wpmus_maybesync_newuser', 10, 1 );
				remove_action( 'social_connect_login', 'wpmus_maybesync_newuser', 10, 1 );
				add_action( 'wp_login', array( __CLASS__, 'wpmus_maybesync_newuser' ), 10, 1 );
				add_action( 'social_connect_login', array( __CLASS__, 'wpmus_maybesync_newuser' ), 10, 1 );
			}
		}
	}

	/**
	 * Filters Eventbrite aggregator event content before saving
	 *
	 * @param array                                       $event  Event data to save
	 * @param Tribe__Events__Aggregator__Record__Abstract $record Importer record
	 */
	public static function tribe_aggregator_before_save_event( $event, $record ) {
		if ( $record->origin === 'eventbrite' ) {
			$event['post_content'] = self::fix_eventbrite_event_markup( $event['post_content'] );
		}

		return $event;
	}

	/**
	 * Fixes funky Eventbrite formatting of event description field, e.g.
	 *
	 * <div>Join us on November 8th for a collaboration between BGIT and SOHOHOUSE, as we explore "The Intersection of Fashion and Tech."</div>
	 * <div style="margin-top: 20px;">
	 *   <div style="margin: 20px 10px; font-size: 15px; line-height: 22px; font-weight: 400; text-align: left;">
	 *     Join us on November 8th for a collaboration between BGIT and SOHOHOUSE, as we explore "The Intersection of Fashion and Tech." This unique event will delve into the dynamic fusion of two worlds, where innovation meets style, and technology ignites creativity.
	 *     ...
	 *   </div>
	 *   ...
	 * </div>
	 * becomes:
	 *
	 * <div>Join us on November 8th for a collaboration between BGIT and SOHOHOUSE, as we explore "The Intersection of Fashion and Tech."</div>
	 * Join us on November 8th for a collaboration between BGIT and SOHOHOUSE, as we explore "The Intersection of Fashion and Tech." This unique event will delve into the dynamic fusion of two worlds, where innovation meets style, and technology ignites creativity.
	 *
	 */
	public static function fix_eventbrite_event_markup( $markup ) {
		$dom = new \DOMDocument();
		libxml_use_internal_errors( true );
		$encoded_markup = mb_encode_numericentity(
			htmlspecialchars_decode(
				htmlentities( '<html>' . $markup . '</html>', ENT_NOQUOTES, 'UTF-8', false ),
				ENT_NOQUOTES
			),
			array( 0x80, 0x10FFFF, 0, ~0 ),
			'UTF-8'
		);
		$dom->loadHTML( $encoded_markup, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
		libxml_clear_errors();

		// Find all <div> elements with a "style" attribute
		$xpath = new \DOMXPath( $dom );
		$styled_divs = $xpath->query( '//div[@style]' );

		/**
		 * Strips the tags from an HTML node but retains its content
		 *
		 * @param DOMNode $node Document node to strip tags from
		 */
		function strip_tags( $node ) {
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$parent_node = $node->parentNode;

			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			while ( $node->firstChild ) {
				// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$parent_node->insertBefore( $node->firstChild, $node );
			}

			$parent_node->removeChild( $node );
		}

		for ( $count = 2; $count >= 0; $count-- ) {
			$current_node = $styled_divs->item( $count);
			$style = $current_node->attributes->getNamedItem( 'style' )->nodeValue;
			// Strip tags from the styled <div> elements unless it's the image container
			if ( $style === 'margin-top: 20px;' || strpos( $style, 'font-size:' ) !== false ) {
				strip_tags( $current_node );
			}
		}

		return str_replace( array( '<html>', '</html>' ), '', $dom->saveHTML() );
	}

	/**
	 * Allows filtering the organizer ID while searching for it.
	 *
	 * Use this filter to define custom ways to find a matching Organizer provided the EA
	 * record information; returning a non `null` value here will short-circuit the
	 * check Event Aggregator would make.
	 *
	 * @since 4.6.15
	 *
	 * @param int|null $organizer_id The matching organizer ID if any
	 * @param string   $organizer    The organizer name from the record.
	 */
	public static function tribe_aggregator_find_matching_organizer( $organizer_id, $organizer ) {
		$organizer = get_page_by_title( $organizer, 'OBJECT', \Tribe__Events__Organizer::POSTTYPE );

		return empty( $organizer ) ? $organizer_id : $organizer->ID;
	}

	/**
	 * Sets all imported events as "Pending Review".
	 *
	 * @since 4.8.2
	 *
	 * @param string                                      $post_status The event's post status before being filtered.
	 * @param array                                       $event       The WP event data about to imported and saved to the DB.
	 * @param Tribe__Events__Aggregator__Record__Abstract $record      The import's EA Import Record.
	 */
	public static function tribe_aggregator_new_event_post_status_before_import( $post_status, $event, $record ) {
		return 'pending';
	}

	/**
	 * Filters the Context locations to let the Context know how to fetch the value of the filter from a request.
	 *
	 * Here we add the taxonomy filters as read-only Context locations: we'll not need to write it.
	 *
	 * @param array<string,array> $locations A map of the locations the Context supports and is able to read from and write
	 *                                                                               to.
	 *
	 * @return array<string,array> The filtered map of Context locations, with the one required from the filter added to it.
	 */
	public static function tribe_context_locations( array $locations ) {
		$get_fb_val_from_view_data = static function ( $key ) {
			return static function ( $view_data ) use ( $key ) {
				return ! empty( $view_data[ 'tribe_filterbar_' . $key ] ) ? $view_data[ 'tribe_filterbar_' . $key ] : null;
			};
		};

		$taxonomy_locations = array();

		foreach ( array_keys( self::EVENT_TAXONOMIES ) as $taxonomy ) {
			$taxonomy_locations[ 'filterbar_' . $taxonomy ] = array(
				'read' => array(
					\Tribe__Context::QUERY_VAR     => array( ( 'tribe_filterbar_' . $taxonomy ) ),
					\Tribe__Context::REQUEST_VAR   => array( ( 'tribe_filterbar_' . $taxonomy ) ),
					\Tribe__Context::LOCATION_FUNC => array( 'view_data', $get_fb_val_from_view_data( $taxonomy ) ),
				),
			);
		}
		// Read the filter selected values, if any, from the URL request vars.
		$locations = array_merge( $locations, $taxonomy_locations );

		// Return the modified $locations.
		return $locations;
	}

	/**
	 * Flattens the taxonomy array passed back to a Select2 dropdown
	 *
	 * @param array<object>              $data   Array of results.
	 * @param string|array<string|mixed> $search Search string from Select2
	 * @param int                        $page   When we deal with pagination
	 * @param array<string|mixed>        $args   Which arguments we got from the Template
	 * @param string                     $source What source it is
	 *
	 * @return array<string|mixed>
	 */
	// phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded, Generic.Metrics.NestingLevel.MaxExceeded
	public static function tribe_dropdown_search_terms( $data, $search, $page, $args, $source ) {
		if ( empty( $args['taxonomy'] ) ) {
			self::ajax_error( esc_attr__( 'Cannot look for Terms without a taxonomy', 'tribe-common' ) );
		}

		// We always want all the fields so we overwrite it
		$args['fields']     = isset( $args['fields'] ) ? $args['fields'] : 'all';
		$args['hide_empty'] = isset( $args['hide_empty'] ) ? $args['hide_empty'] : false;

		if ( ! empty( $search ) ) {
			if ( ! is_array( $search ) ) {
				// For older pieces that still use Select2 format.
				$args['search'] = $search;
			} else {
				// Newer SelectWoo uses a new search format.
				$args['search'] = $search['term'];
			}
		}

		// On versions older than 4.5 taxonomy goes as an Param
		if ( version_compare( $GLOBALS['wp_version'], '4.5', '<' ) ) {
			$terms = get_terms( $args['taxonomy'], $args );
		} else {
			$terms = get_terms( $args );
		}

		$results = array();

		if ( empty( $args['search'] ) ) {
			foreach ( $terms as $i => $term ) {
				// Prep for Select2
				$term->id   = $term->term_id;
				$term->text = $term->name;

				$results[ $term->term_id ] = $term;
				unset( $terms[ $i ] );
			}
		} else {
			foreach ( $terms as $term ) {
				// Prep for Select2
				$term->id          = $term->term_id;
				$term->text        = $term->name;
				$term->breadcrumbs = array();

				if ( (int) $term->parent !== 0 ) {
					$ancestors = get_ancestors( $term->id, $term->taxonomy );
					$ancestors = array_reverse( $ancestors );
					foreach ( $ancestors as $ancestor ) {
						$ancestor            = get_term( $ancestor );
						$term->breadcrumbs[] = $ancestor->name;
					}
				}

				$results[] = $term;
			}
		}

		foreach ( $results as $result ) {
			$result->text = wp_specialchars_decode( wp_kses( $result->text, array() ) );
		}

		$data['results']    = array_values( (array) $results );
		$data['taxonomies'] = get_taxonomies();

		return $data;
	}

	/**
	 * Get markup for Eventbrite (non-modal) checkout.
	 * Adapted from WP Event Aggregator.
	 *
	 * @return string
	 */
	public static function tribe_eventbrite_checkout_markup( $eventbrite_id ) {
		ob_start();
		?>
		<div id="tec-eventbrite-checkout-widget"></div>
		<?php // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript ?>
		<script src="https://www.eventbrite.com/static/widgets/eb_widgets.js"></script>
		<script type="text/javascript">
			window.EBWidgets.createWidget({
				widgetType: "checkout",
				eventId: "<?php echo $eventbrite_id; ?>",
				iframeContainerId: "tec-eventbrite-checkout-widget",
				iframeContainerHeight: <?php echo apply_filters( 'tec_embedded_checkout_height', 530 ); ?>,
				onOrderComplete: () => {console.log("Order complete!");}
			});
		</script>
		<?php
		return ob_get_clean();
	}

	/**
	 * Adds 'admin_post_status' to list of event fields allowed by the scrubber
	 * to enable front-end control of post status.
	 *
	 * @param array $allowed_fields The allowed fields.
	 */
	public static function tribe_events_community_allowed_event_fields( $allowed_fields ) {
		if ( current_user_can( 'manage_options' ) ) {
			$allowed_fields[] = 'admin_post_status';
		}

		return $allowed_fields;
	}

	/**
	 * Renders the event import form
	 */
	public static function tribe_events_community_form_before_template() {
		$post_id      = get_the_ID();

		if ( class_exists( '\Tribe__Events__Aggregator__Records' ) && ( ! $post_id || ! tribe_is_event( $post_id ) ) ) {
			require_once Utils::plugin_path() . '/templates/import-event-form.php';
		}
	}

	/**
	 * Enables 'post_status' field to be set from proxy field.
	 *
	 * @param array   $args The fields we want saved.
	 * @param int     $event_id The event ID we are modifying.
	 * @param WP_Post $post The event itself.
	 *
	 * @since 4.9.4
	 */
	public static function tribe_events_event_update_args( $args, $event_id, $post ) {
		if ( current_user_can( 'manage_options' ) && isset( $args['admin_post_status'] ) ) {
			$args['post_status'] = $args['admin_post_status'];
			unset( $args['admin_post_status'] );
		}

		return $args;
	}

	/**
	 * Edits the event submission message to be more friendly
	 */
	// phpcs:ignore Generic.Metrics.NestingLevel.MaxExceeded, Generic.Metrics.CyclomaticComplexity.MaxExceeded
	public static function tribe_events_community_submission_message( $message, $type ) {
		if ( $type === 'update' ) {
			$events_label_singular = tribe_get_event_label_singular();
			$events_label_singular_lowercase = tribe_get_event_label_singular_lowercase();

			// translators: %s is the singular event label.
			if ( strpos( $message, sprintf( __( '%s updated.', 'tribe-events-community' ), $events_label_singular ) ) === 0 ) {
				$suffix = 'updated.';

				if ( isset( $_REQUEST['post_ID'] ) ) {
					$post = get_post( sanitize_key( wp_unslash( $_REQUEST['post_ID'] ) ) );
					switch ( $post->post_status ) {
						case 'draft':
							$suffix = 'saved.';
							break;
						case 'pending':
							$suffix = 'submitted and is awaiting review before being published. Thank you for contributing, we truly appreciate it!';
							break;
						case 'publish':
							$suffix = 'published.';
							break;
					}
				}

				// translators: %1$s is the lower-case singular event label.
				$message = sprintf( __( 'Your %1$s has been %2$s', 'tribe-events-community' ), $events_label_singular_lowercase, $suffix );
			}
		}

		return $message;
	}

	/**
	 * Includes the custom taxonomy filter classes and creates instances of them.
	 */
	public static function tribe_events_filters_create_filters() {
		if ( ! class_exists( '\Tribe__Events__Filterbar__Filter' ) ) {
			return;
		}

		// Instantiate custom taxonomy filter classes
		foreach ( self::EVENT_TAXONOMIES as $taxonomy => $class_name ) {
			$ref = new \ReflectionClass( $class_name );
			$obj = $ref->newInstanceArgs( array( ucfirst( $taxonomy ), ( 'filterbar_' . $taxonomy ) ) );
		}
	}

	/**
	 * Filters the map of filters available on the front-end to include the custom one.
	 *
	 * @param array<string,string> $map A map relating the filter slugs to their respective classes.
	 *
	 * @return array<string,string> The filtered slug to filter class map.
	 */
	public static function tribe_events_filter_bar_context_to_filter_map( array $map ) {
		if ( ! class_exists( '\Tribe__Events__Filterbar__Filter' ) ) {
			// This would not make much sense, but let's be cautious.
			return $map;
		}

		// Add the filter classes to our filters map.
		foreach ( self::EVENT_TAXONOMIES as $taxonomy => $class_name ) {
			$map[ ( 'filterbar_' . $taxonomy ) ] = $class_name;
		}

		// Return the modified $map.
		return $map;
	}

	/**
	 * Display Ticket Section after eventbrite events.
	 *
	 * @since 1.0.0
	 */
	public static function tribe_events_single_event_after_the_meta() {
		global $importevents;
		$event_id = get_the_ID();
		$event_url = get_post_meta( $event_id, '_EventURL', true );
		$eventbrite_event_id = self::get_eventbrite_event_id( $event_url );

		if ( $event_id > 0 && $eventbrite_event_id && is_numeric( $eventbrite_event_id ) && $eventbrite_event_id > 0 ) {
			$ticket_section = self::tribe_get_ticket_section( $eventbrite_event_id );
			echo $ticket_section;
		}
	}

	/**
	 * Get ticket section markup for Eventbrite events.
	 *
	 * @since  1.1.0
	 * @return html
	 */
	public static function tribe_get_ticket_section( $eventbrite_id = 0 ) {
		if ( $eventbrite_id > 0 ) {
			ob_start();

			if ( is_ssl() ) {
				echo self::tribe_eventbrite_checkout_markup( $eventbrite_id );
			} else {
				?>
				<div class="eventbrite-ticket-section" style="width:100%; text-align:left;">
					<iframe id="eventbrite-tickets-<?php echo $eventbrite_id; ?>" src="//www.eventbrite.com/tickets-external?eid=<?php echo $eventbrite_id; ?>" style="width:100%;height:300px; border: 0px;"></iframe>
				</div>
				<?php
			}

			$ticket = ob_get_clean();

			return $ticket;
		} else {
			return '';
		}
	}

	/**
	 * Get an event's cost
	 *
	 * @param string   $cost                 Current cost value
	 * @param null|int $post_id              (optional)
	 * @param bool     $with_currency_symbol Include the currency symbol
	 *
	 * @return string Cost of the event.
	 * @category Cost
	 */
	public static function tribe_get_cost( $cost, $post_id, $with_currency_symbol ) {
		if ( empty( $cost ) ) {
			$cost = '0';
		}

		return $cost;
	}

	/**
	 * Allows filtering the quantity available displayed below the ticket
	 * quantity input for purchase of this one ticket.
	 *
	 * If less than the maximum quantity available, will restrict that as well.
	 *
	 * @since 4.8.1
	 *
	 * @param int                           $available_at_a_time Max purchase quantity, as restricted by Max At A Time.
	 * @param Tribe__Tickets__Ticket_Object $ticket              Ticket object.
	 * @param WP_Post                       $event               Event post.
	 */
	public static function tribe_tickets_get_ticket_max_purchase( $available_at_a_time, $ticket, $event ) {
		$key = 'ticket_max_purchase';
		$values = get_post_custom_values( $key, $event->ID );

		if ( is_array( $values ) && is_numeric( $values[0] ) ) {
			 $available_at_a_time = intval( $values[0] );
		}

		return $available_at_a_time;
	}

	// phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded
	public static function wp_ajax_aggregator_fetch_import() {
		if ( isset( $_GET['import_id'] ) ) {
			$import_id = sanitize_key( wp_unslash( $_GET['import_id'] ) );
		} else {
			$import_id = -1;
		}

		$record = \Tribe__Events__Aggregator__Records::instance()->get_by_import_id( $import_id );

		if ( tribe_is_error( $record ) ) {
			wp_send_json_error( $record );
		}

		$result = $record->get_import_data();

		if ( isset( $result->data ) ) {
			$result->data->origin = $record->origin;
		}

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result );
		}

		// if we've received a source name, let's set that in the record as soon as possible
		if ( ! empty( $result->data->source_name ) ) {
			$record->update_meta( 'source_name', $result->data->source_name );

			if ( ! empty( $record->post->post_parent ) ) {
				$parent_record = \Tribe__Events__Aggregator__Records::instance()->get_by_post_id( $record->post->post_parent );

				if ( tribe_is_error( $parent_record ) ) {
					$parent_record->update_meta( 'source_name', $result->data->source_name );
				}
			}
		}

		// if there is a warning in the data let's localize it
		if ( ! empty( $result->warning_code ) ) {
			/** @var Tribe__Events__Aggregator__Service $service */
			$service         = tribe( 'events-aggregator.service' );
			$default_warning = ! empty( $result->warning ) ? $result->warning : null;
			$result->warning = $service->get_service_message( $result->warning_code, array(), $default_warning );
		}

		// Retrieve the WP post ID for a single-event import
		if ( isset( $result->data ) && isset( $result->data->events ) && is_array( $result->data->events ) && count( $result->data->events ) === 1 ) {
			$global_id = $result->data->events[0]->global_id;
			$event_post = \Tribe__Events__Aggregator__Event::get_post_by_meta( 'global_id', $global_id );

			if ( ! empty( $event_post ) ) {
				$result->data->post_id = $event_post->ID;
			}
		}

		wp_send_json_success( $result );
	}
}
