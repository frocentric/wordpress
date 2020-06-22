<?php


class Tribe__Events__Pro__Integrations__WPML__Recurring_Event_Creation_Handler implements Tribe__Events__Pro__Integrations__WPML__Handler_Interface {


	/**
	 * @var Tribe__Events__Pro__Integrations__WPML__Event_Listener
	 */
	protected $event_listener;

	/**
	 * @var Tribe__Events__Pro__Integrations__WPML__WPML
	 */
	protected $wpml;

	/**
	 * Tribe__Events__Pro__Integrations__WPML__Recurring_Event_Creation_Handler constructor.
	 *
	 * @param Tribe__Events__Pro__Integrations__WPML__Event_Listener $event_listener
	 * @param Tribe__Events__Pro__Integrations__WPML__WPML           $wpml
	 */
	public function __construct( Tribe__Events__Pro__Integrations__WPML__Event_Listener $event_listener, Tribe__Events__Pro__Integrations__WPML__WPML $wpml ) {
		$this->event_listener = $event_listener;
		$this->wpml           = $wpml;
	}

	/**
	 * @param int      $event_id
	 * @param int|null $parent_event_id
	 *
	 * @return mixed
	 */
	public function handle( $event_id, $parent_event_id = null ) {
		$language_code = $this->wpml->get_parent_language_code( $parent_event_id );

		if ( empty( $language_code ) ) {
			return - 1;
		}
		
		$trid          = $this->wpml->get_master_series_instance_trid( $event_id, $parent_event_id);

		return $this->wpml->insert_event_translation_for_language_code( $event_id, $language_code, $trid, true );
	}
}
