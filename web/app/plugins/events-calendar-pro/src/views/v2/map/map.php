<?php
/**
 * View: Map View - Map
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/map/map.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 */
?>
<div class="tribe-events-pro-map__map tribe-common-g-col" data-js="tribe-events-pro-map-map">
	<?php $this->template( 'map/map/google-maps' ); ?>
	<?php $this->template( 'map/map/no-venue-modal' ); ?>
</div>
