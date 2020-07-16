<?php

class Tribe__Events__Pro__Recurrence__Aggregator {
	/**
	 * Singleton for self
	 *
	 * @var self
	 */
	public static $instance;

	/**
	 * Returns a singleton of this class
	 *
	 * @return Tribe__Events__Venue
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {
		add_filter( 'tribe_aggregator_before_save_event', array( $this, 'generate_recurrence_meta' ), 10, 2 );
	}

	public function generate_recurrence_meta( $event, $record ) {
		if ( empty( $event['recurrence']->rrule ) ) {
			return $event;
		}

		$event['EventRecurrenceRRULE'] = $event['recurrence']->rrule;

		unset( $event['recurrence'] );
		// commenting out the following because we don't want to enable recurrence just yet
		//$recurrence_meta = new Tribe__Events__Pro__Recurrence__RRule_Converter( $event['recurrence']->rrule, $event );
		//$event['recurrence'] = array(
			//'rules'      => $recurrence_meta->get_meta(),
			//'exclusions' => array(),
		//);

		return $event;
	}
}
