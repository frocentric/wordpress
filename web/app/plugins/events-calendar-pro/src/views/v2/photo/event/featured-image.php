<?php
/**
 * View: Photo View - Single Event Featured Image
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/photo/event/featured-image.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 * @var string $placeholder_url The url for the placeholder image if a featured image does not exist.
 *
 * @see tribe_get_event() For the format of the event object.
 */
$image_url = $event->thumbnail->exists ? $event->thumbnail->full->url : $placeholder_url;

?>
<div class="tribe-events-pro-photo__event-featured-image-wrapper">
	<a
		href="<?php echo esc_url( $event->permalink ); ?>"
		title="<?php echo esc_attr( get_the_title( $event ) ); ?>"
		rel="bookmark"
		class="tribe-events-pro-photo__event-featured-image-link"
	>
		<img
			src="<?php echo esc_url( $image_url ); ?>"
			<?php if ( ! empty( $event->thumbnail->srcset ) ) : ?>
				srcset="<?php echo esc_attr( $event->thumbnail->srcset ); ?>"
			<?php endif; ?>
			<?php if ( ! empty( $event->thumbnail->alt ) ) : ?>
				alt="<?php echo esc_attr( $event->thumbnail->alt ); ?>"
			<?php endif; ?>
			<?php if ( ! empty( $event->thumbnail->title ) ) : ?>
				title="<?php echo esc_attr( $event->thumbnail->title ); ?>"
			<?php endif; ?>
			class="tribe-events-pro-photo__event-featured-image"
		/>
	</a>
</div>
