<?php
/**
 * Block: Related Events
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/pro/blocks/related-events.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @version 4.6.1
 *
 */
$events = $this->get( 'events', null );

if ( ! is_array( $events ) || empty( $events ) ) {
	return;
}
?>
<?php $this->template( 'blocks/related-events/title' ); ?>

<ul class="tribe-related-events tribe-clearfix">
	<?php foreach ( $events as $event ) : ?>
		<?php $this->template( 'blocks/related-events/event', array( 'event' => $event ) ); ?>
	<?php endforeach; ?>
</ul>