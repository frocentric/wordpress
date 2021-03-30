<?php
/**
 * Events Pro Featured Venue Widget V2
 * This is the template for the output of the event featured venue widget.
 * All the items are turned on and off through the widget admin.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/widgets/widget-featured-venue.php
 *
 * @link    https://evnt.is/1aiy
 *
 * @version 5.3.0
 *
 * @var string               $widget_title               The User-supplied widget title.
 * @var string               $rest_url                   The REST URL.
 * @var string               $rest_nonce                 The REST nonce.
 * @var int                  $should_manage_url          Int indicating if it should manage the URL.
 * @var array<string>        $compatibility_classes      Classes used for the compatibility container.
 * @var array<string>        $container_classes          Classes used for the container of the view.
 * @var array<string,mixed>  $container_data             An additional set of container `data` attributes.
 * @var string               $breakpoint_pointer         String we use as pointer to the current view we are setting up with breakpoints.
 * @var array<string,string> $messages                   An array of user-facing messages, managed by the View.
 * @var boolean              $hide_if_no_upcoming_events Hide widget if no events.
 * @var string               $json_ld_data               The JSON-LD for widget events, if enabled.
 * @var WP_Post              $venue                      The venue post object with properties added by the `tribe_get_venue_object` function.
 * @var array<WP_Post>       $events                     An array of events to display.
 *
 * @see tribe_get_event() For the format of the $events objects.
 * @see tribe_get_venue_object() For the format of the $venue object.
 */

// Hide widget if no events and widget only displays with events is checked.
if ( empty( $events ) && $hide_if_no_upcoming_events ) {
	return;
}

?>
<div <?php tribe_classes( $compatibility_classes ); ?>>
	<div
		<?php tribe_classes( $container_classes ); ?>
		data-js="tribe-events-view"
		data-view-rest-nonce="<?php echo esc_attr( $rest_nonce ); ?>"
		data-view-rest-url="<?php echo esc_url( $rest_url ); ?>"
		data-view-manage-url="<?php echo esc_attr( $should_manage_url ); ?>"
		<?php foreach ( $container_data as $key => $value ) : ?>
			data-view-<?php echo esc_attr( $key ); ?>="<?php echo esc_attr( $value ); ?>"
		<?php endforeach; ?>
		<?php if ( ! empty( $breakpoint_pointer ) ) : ?>
			data-view-breakpoint-pointer="<?php echo esc_attr( $breakpoint_pointer ); ?>"
		<?php endif; ?>
	>
		<div class="tribe-events-widget-featured-venue">

			<?php $this->template( 'components/json-ld-data' ); ?>

			<?php $this->template( 'components/data' ); ?>

			<?php $this->template( 'widgets/widget-featured-venue/widget-title' ); ?>

			<?php $this->template( 'widgets/widget-featured-venue/venue' ); ?>

			<?php if ( ! empty( $events ) ) : ?>

				<?php $this->template( 'widgets/widget-featured-venue/events-list', [ 'events' => $events ] ); ?>

				<?php $this->template( 'widgets/widget-featured-venue/view-more' ); ?>

			<?php else : ?>

				<?php $this->template( 'components/messages' ); ?>

			<?php endif; ?>

		</div>
	</div>
</div>
