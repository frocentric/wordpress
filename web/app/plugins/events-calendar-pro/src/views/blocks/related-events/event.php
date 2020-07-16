<?php
/**
 * Block: Related Events
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/pro/blocks/related-events/event.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @version 4.6.1
 *
 */
?>
<li>
	<?php $this->template( 'blocks/related-events/event-thumbnail', array( 'event' => $event ) ); ?>
	<?php $this->template( 'blocks/related-events/event-info', array( 'event' => $event ) ); ?>
</li>