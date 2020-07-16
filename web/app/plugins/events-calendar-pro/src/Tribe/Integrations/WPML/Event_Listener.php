<?php


/**
 * Class Tribe__Events__Pro__Integrations__WPML__Event_Listener
 *
 * Listens for Tribe Events events (actions and filters) and dispatches
 */
class Tribe__Events__Pro__Integrations__WPML__Event_Listener {

	/**
	 * @var Tribe__Events__Pro__Integrations__WPML__Event_Listener
	 */
	private static $instance;

	/**
	 * @var array
	 */
	private $handlers_map;
	/**
	 * @var Tribe__Logger_Interface
	 */
	private $logger;
	/**
	 * @var string
	 */
	private $name_before_wpml_parse_query;

	/**
	 * The class singleton constructor.
	 *
	 * @return Tribe__Events__Pro__Integrations__WPML__Event_Listener
	 */
	public static function instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Tribe__Events__Pro__Integrations__WPML__Event_Listener constructor.
	 *
	 * @param array|null                               $handlers_map An associative array of event type to handling class instances.
	 * @param Tribe__Log__Logger                       $logger
	 * @param Tribe__Events__Pro__Integrations__WPML__WPML $wpml
	 */
	public function __construct( array $handlers_map = null, Tribe__Log__Logger $logger = null, Tribe__Events__Pro__Integrations__WPML__WPML $wpml = null ) {
		$this->handlers_map = $handlers_map ? $handlers_map : $this->get_handlers_map();
		$this->logger       = $logger ? $logger : tribe( 'logger' )->get_current_logger();
		$this->wpml         = $wpml ? $wpml : Tribe__Events__Pro__Integrations__WPML__WPML::instance();
	}

	/**
	 * @param int      $post_id
	 * @param int|null $post_parent_id
	 */
	public function handle_recurring_event_creation( $post_id, $post_parent_id = null ) {
		try {
			$this->ensure_is_event( $post_id );
			$this->ensure_is_event( $post_parent_id );
			$this->ensure_event_is_parent_to( $post_parent_id, $post_id );
		} catch ( \Exception $e ) {
			if ( null !== $this->logger ) {
				$message = $this->get_log_line_header() . $e->getMessage();
				$this->logger->log( $message, Tribe__Log::DEBUG, __CLASS__ );
			}
		}

		if ( $this->has_handler_for_event( 'event.recurring.created' ) ) {
			/** @var Tribe__Events__Pro__Integrations__WPML__Handler_Interface $handler */
			$handler       = $this->get_handler_for_event( 'event.recurring.created' );
			$handling_exit = $handler->handle( $post_id, $post_parent_id );

			$handling_exit = $this->format_exit_status( $handling_exit );

			if ( null !== $this->logger ) {
				$message = $this->get_log_line_header() . 'handled recurring event instance creation [ID ' . $post_id . '; Parent ID ' . $post_parent_id . '] with exit status "' . $handling_exit . '"';
				$this->logger->log( $message, Tribe__Log::DEBUG, __CLASS__ );
			}
		}
	}

	/**
	 * @return array
	 */
	private function get_handlers_map() {
		return array( 'event.recurring.created' => 'Tribe__Events__Pro__Integrations__WPML__Recurring_Event_Creation_Handler' );
	}

	/**
	 * @param $post_id
	 */
	protected function ensure_is_event( $post_id ) {
		if ( ! tribe_is_event( $post_id ) ) {
			throw new InvalidArgumentException( 'Post ID [' . $post_id . '] is not an int, does not exist or is not that of an event.' );
		}
	}

	/**
	 * @param $event
	 *
	 * @return bool
	 */
	protected function has_handler_for_event( $event ) {
		return isset( $this->handlers_map[ $event ] );
	}

	/**
	 * @param $event
	 *
	 * @return Tribe__Events__Pro__Integrations__WPML__Handler_Interface
	 */
	protected function get_handler_for_event( $event ) {
		if ( ! in_array( 'Tribe__Events__Pro__Integrations__WPML__Handler_Interface', class_parents( $this->handlers_map[ $event ] ) ) ) {
			$this->handlers_map[ $event ] = new $this->handlers_map[ $event ]( $this, $this->wpml );
		}

		return $this->handlers_map[ $event ];
	}

	/**
	 * @param $post_parent_id
	 * @param $post_id
	 */
	private function ensure_event_is_parent_to( $post_parent_id, $post_id ) {
		if ( get_post( $post_id )->post_parent !== $post_parent_id ) {
			throw new InvalidArgumentException( 'Event with ID [' . $post_parent_id . '] is not parent of event with ID [' . $post_id . ']' );
		}
	}

	/**
	 * @return string
	 */
	protected function get_log_line_header() {
		return 'PRO - WPML Event Listener: ';
	}

	/**
	 * @param $handling_exit
	 *
	 * @return mixed|string|void
	 */
	private function format_exit_status( $handling_exit ) {
		if ( is_array( $handling_exit ) ) {
			$handling_exit = json_encode( $handling_exit );

			return $handling_exit;
		}

		return $handling_exit;
	}
}
