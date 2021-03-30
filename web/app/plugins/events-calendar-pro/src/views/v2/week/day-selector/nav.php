<?php
/**
 * View: Week View - Day Selector Nav
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/day-selector/nav.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @var string $prev_url The URL to the previous page, if any, or an empty string.
 * @var string $next_url The URL to the next page, if any, or an empty string.
 *
 * @version 5.0.0
 *
 */
?>
<nav class="tribe-events-pro-week-day-selector__nav">
	<ul class="tribe-events-pro-week-day-selector__nav-list">

		<?php $this->template( 'week/day-selector/nav/prev', [ 'link' => $prev_url ] ); ?>

		<?php $this->template( 'week/day-selector/nav/next', [ 'link' => $next_url ] ); ?>

	</ul>
</nav>
