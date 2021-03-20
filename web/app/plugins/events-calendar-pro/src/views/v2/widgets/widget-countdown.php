<?php
/**
 * Events Pro Countdown Widget V2
 * This is the template for the output of the event countdown widget.
 * All the items are turned on and off through the widget admin.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/widgets/widget-countdown.php
 *
 * @link    https://event.is/1aiy
 *
 * @version 5.3.0
 *
 * @var boolean              $event_done         A boolean of whether the event has already started.
 * @var boolean              $show_seconds       Whether the widget should display seconds in the countdown.
 * @var string               $count_to_date      A date string of the event start date in ISO 8601 date format.
 * @var string               $count_to_stamp     Timestamp of the event start date.
 * @var string               $widget_title       The User-supplied widget title.
 * @var string               $complete           The User-supplied event completion message.
 * @var WP_Post              $event              The event post object with properties added by the `tribe_get_event` function.
 * @var string               $rest_url           The REST URL.
 * @var string               $rest_nonce         The REST nonce.
 * @var int                  $should_manage_url  int containing if it should manage the URL.
 * @var array<string>        $compatibility_classes      Classes used for the compatibility container.
 * @var array<string>        $container_classes  Classes used for the container of the view.
 * @var array<string,mixed>  $container_data     An additional set of container `data` attributes.
 * @var string               $breakpoint_pointer String we use as pointer to the current view we are setting up with breakpoints.
 * @var array<string,string> $messages           An array of user-facing messages, managed by the View.
 *
 * @see     tribe_get_event() For the format of the event object.
 */

if ( empty( $event ) ) {
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
		<div class="tribe-events-widget-countdown">
			<?php $this->template( 'components/json-ld-data' ); ?>

			<?php $this->template( 'components/data' ); ?>

			<?php $this->template( 'widgets/widget-countdown/widget-title' ); ?>

			<?php $this->template( 'widgets/widget-countdown/event-title' ); ?>

			<?php $this->template( 'widgets/widget-countdown/timer' ); ?>

			<?php $this->template( 'widgets/widget-countdown/complete' ); ?>
		</div>
	</div>
</div>
