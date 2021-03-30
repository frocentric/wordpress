<?php
/**
 * Block: Related Events
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/blocks/related-events/event-thumbnail.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1ajx
 *
 * @version 4.6.1
 *
 */
$display_images = $this->attr( 'displayImages' );

if ( is_bool( $display_images ) && ! $display_images ) {
	return;
}

$thumb = ( has_post_thumbnail( $event->ID ) )
	? get_the_post_thumbnail( $event->ID, 'large' )
	: '<img src="' . esc_url( trailingslashit( Tribe__Events__Pro__Main::instance()->pluginUrl ) . 'src/resources/images/tribe-related-events-placeholder.png' ) . '" alt="' . esc_attr( get_the_title( $event->ID ) ) . '" />'; ?>

<div class="tribe-related-events-thumbnail">
	<a href="<?php echo esc_url( tribe_get_event_link( $event ) ); ?>" class="url" rel="bookmark" tabindex="-1"><?php echo $thumb ?></a>
</div>
