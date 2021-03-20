
<?php
/**
 * View: Map View - Google Maps
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/map/map/google-maps.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 * @var array  $events       An array of the week events, in sequence.
 * @var object $map_provider Object with data of map provider.
 */
?>
<?php if ( empty( $map_provider->is_premium ) ) : ?>
	<?php if ( ! empty( $events ) ) : ?>
		<?php $this->template( 'map/map/google-maps/default' ); ?>
	<?php endif; ?>
<?php else : ?>
	<?php $this->template( 'map/map/google-maps/premium' ); ?>
<?php endif; ?>
