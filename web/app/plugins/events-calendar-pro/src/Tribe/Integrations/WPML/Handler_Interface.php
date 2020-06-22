<?php

interface Tribe__Events__Pro__Integrations__WPML__Handler_Interface {

	/**
	 * @param int      $event_id
	 * @param int|null $parent_event_id
	 *
	 * @return mixed
	 */
	public function handle( $event_id, $parent_event_id = null );

}
