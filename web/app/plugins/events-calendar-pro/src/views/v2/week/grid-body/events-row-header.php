<?php
/**
 * View: Week View - Events Row Header
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/grid-body/events-row-header.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 * @var array<string,string> $formatted_grid_times A map from the AM/PM format of each time to its localized and formatted version.
 *                                                 E.g. `[ '00:00' => '12 am', '01:00' => '1 am', ... ]`.
 */

?>
<div class="tribe-events-pro-week-grid__events-row-header" role="rowheader">
	<time
		class="tribe-events-pro-week-grid__events-time-tag tribe-events-pro-week-grid__events-time-tag--first tribe-common-a11y-visual-hide"
		datetime="0:00"
	>
		<?php echo esc_html( $formatted_grid_times['00:00'] ); ?>
	</time>
	<time class="tribe-events-pro-week-grid__events-time-tag" datetime="1:00"><?php echo esc_html( $formatted_grid_times['01:00'] ); ?></time>
	<time class="tribe-events-pro-week-grid__events-time-tag" datetime="2:00"><?php echo esc_html( $formatted_grid_times['02:00'] ); ?></time>
	<time class="tribe-events-pro-week-grid__events-time-tag" datetime="3:00"><?php echo esc_html( $formatted_grid_times['03:00'] ); ?></time>
	<time class="tribe-events-pro-week-grid__events-time-tag" datetime="4:00"><?php echo esc_html( $formatted_grid_times['04:00'] ); ?></time>
	<time class="tribe-events-pro-week-grid__events-time-tag" datetime="5:00"><?php echo esc_html( $formatted_grid_times['05:00'] ); ?></time>
	<time class="tribe-events-pro-week-grid__events-time-tag" datetime="6:00"><?php echo esc_html( $formatted_grid_times['06:00'] ); ?></time>
	<time class="tribe-events-pro-week-grid__events-time-tag" datetime="7:00"><?php echo esc_html( $formatted_grid_times['07:00'] ); ?></time>
	<time class="tribe-events-pro-week-grid__events-time-tag" datetime="8:00"><?php echo esc_html( $formatted_grid_times['08:00'] ); ?></time>
	<time class="tribe-events-pro-week-grid__events-time-tag" datetime="9:00"><?php echo esc_html( $formatted_grid_times['09:00'] ); ?></time>
	<time class="tribe-events-pro-week-grid__events-time-tag" datetime="10:00"><?php echo esc_html( $formatted_grid_times['10:00'] ); ?></time>
	<time class="tribe-events-pro-week-grid__events-time-tag" datetime="11:00"><?php echo esc_html( $formatted_grid_times['11:00'] ); ?></time>
	<time class="tribe-events-pro-week-grid__events-time-tag" datetime="12:00"><?php echo esc_html( $formatted_grid_times['12:00'] ); ?></time>
	<time class="tribe-events-pro-week-grid__events-time-tag" datetime="13:00"><?php echo esc_html( $formatted_grid_times['13:00'] ); ?></time>
	<time class="tribe-events-pro-week-grid__events-time-tag" datetime="14:00"><?php echo esc_html( $formatted_grid_times['14:00'] ); ?></time>
	<time class="tribe-events-pro-week-grid__events-time-tag" datetime="15:00"><?php echo esc_html( $formatted_grid_times['15:00'] ); ?></time>
	<time class="tribe-events-pro-week-grid__events-time-tag" datetime="16:00"><?php echo esc_html( $formatted_grid_times['16:00'] ); ?></time>
	<time class="tribe-events-pro-week-grid__events-time-tag" datetime="17:00"><?php echo esc_html( $formatted_grid_times['17:00'] ); ?></time>
	<time class="tribe-events-pro-week-grid__events-time-tag" datetime="18:00"><?php echo esc_html( $formatted_grid_times['18:00'] ); ?></time>
	<time class="tribe-events-pro-week-grid__events-time-tag" datetime="19:00"><?php echo esc_html( $formatted_grid_times['19:00'] ); ?></time>
	<time class="tribe-events-pro-week-grid__events-time-tag" datetime="20:00"><?php echo esc_html( $formatted_grid_times['20:00'] ); ?></time>
	<time class="tribe-events-pro-week-grid__events-time-tag" datetime="21:00"><?php echo esc_html( $formatted_grid_times['21:00'] ); ?></time>
	<time class="tribe-events-pro-week-grid__events-time-tag" datetime="22:00"><?php echo esc_html( $formatted_grid_times['22:00'] ); ?></time>
	<time class="tribe-events-pro-week-grid__events-time-tag" datetime="23:00"><?php echo esc_html( $formatted_grid_times['23:00'] ); ?></time>
	<time
		class="tribe-events-pro-week-grid__events-time-tag tribe-events-pro-week-grid__events-time-tag--last tribe-common-a11y-visual-hide"
		datetime="24:00"
	>
		<?php echo esc_html( $formatted_grid_times['00:00'] ); ?>
	</time>
</div>
