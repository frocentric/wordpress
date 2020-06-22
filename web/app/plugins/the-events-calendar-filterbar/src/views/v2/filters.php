<?php
/**
 * View: Events Bar Filters
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-filterbar/v2/filters.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @version 4.9.0
 *
 */
?>
<div class="tribe-events-c-events-bar__filters">
	<h3 class="tribe-common-a11y-visual-hide">
		<?php printf( esc_html__( '%s Filters', 'events-filterbar' ), tribe_get_event_label_singular() ); ?>
	</h3>

	<?php $this->template( 'filters/button' ); ?>

	<?php $this->template( 'filters/content' ); ?>
</div>
