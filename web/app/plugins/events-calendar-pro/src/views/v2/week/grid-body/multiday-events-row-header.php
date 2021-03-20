<?php
/**
 * View: Week View - Multiday Events Row Header
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/grid-body/multiday-events-row-header.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 * @var bool $multiday_display_toggle bool containing if we should display the multiday toggle.
 *
 */

?>
<div class="tribe-events-pro-week-grid__multiday-events-row-header" role="rowheader">
	<?php if ( $multiday_display_toggle ) : ?>
		<?php $this->template( 'week/grid-body/multiday-events-row-header/multiday-events-row-header-toggle' ); ?>
	<?php endif; ?>
</div>
