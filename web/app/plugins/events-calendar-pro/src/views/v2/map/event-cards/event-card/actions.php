<?php
/**
 * View: Map View - Single Event Actions
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/map/event-cards/event-card/actions.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 * @var WP_Post $event        The event post object with properties added by the `tribe_get_event` function.
 * @var int     $index        The index of the event card, starting from 0.
 * @var object  $map_provider Object with data of map provider.
 * @var bool    $linked       Boolean of whether the actions are linked or not.
 *
 * @see tribe_get_event() For the format of the event object.
 *
 */
if ( $linked ) {
	$is_active   = ! $map_provider->is_premium && ( 0 === $index );
	$aria_hidden = $is_active ? 'false' : 'true';
}

$classes   = [ 'tribe-events-pro-map__event-actions', 'tribe-common-b3', 'tribe-events-c-small-cta' ];
$classes[] = $linked ? 'tribe-events-pro-map__event-actions--linked' : 'tribe-events-pro-map__event-actions--spacer';
?>
<div
	<?php tribe_classes( $classes ); ?>
	<?php if ( $linked ) : ?>
		id="tribe-events-pro-map-event-actions-<?php echo esc_attr( $event->ID );?>"
		aria-hidden="<?php echo esc_attr( $aria_hidden ); ?>"
		<?php if ( $is_active ) : ?>
			<?php /* Setting this active via accordion.js sets a style of "display: block", this is to keep consistent */ ?>
			style="display:block"
		<?php endif; ?>
	<?php endif; ?>
>
	<?php if ( empty( $linked ) ) : ?>
		<?php $this->template( 'map/event-cards/event-card/actions/cost-spacer', [ 'event' => $event ] ); ?>
		<?php $this->template( 'map/event-cards/event-card/actions/details-spacer' ); ?>
		<?php $this->template( 'map/event-cards/event-card/actions/directions-spacer', [ 'event' => $event ] ); ?>
	<?php else : ?>
		<?php $this->template( 'map/event-cards/event-card/actions/cost', [ 'event' => $event ] ); ?>
		<?php $this->template( 'map/event-cards/event-card/actions/details', [ 'event' => $event ] ); ?>
		<?php $this->template( 'map/event-cards/event-card/actions/directions', [ 'event' => $event ] ); ?>
	<?php endif; ?>
</div>
