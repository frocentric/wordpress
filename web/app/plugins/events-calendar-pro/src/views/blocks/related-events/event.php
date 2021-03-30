<?php
/**
 * Block: Related Events
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/blocks/related-events/event.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1ajx
 *
 * @version 4.6.1
 *
 */
?>
<li>
	<?php $this->template( 'blocks/related-events/event-thumbnail', array( 'event' => $event ) ); ?>
	<?php $this->template( 'blocks/related-events/event-info', array( 'event' => $event ) ); ?>
</li>
