<?php
/**
 * Single Event Meta (Additional Fields) Template
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe-events/pro/modules/meta/additional-fields.php
 *
 * @package TribeEventsCalendarPro
 * @version 4.4.28
 */

if ( ! isset( $fields ) || empty( $fields ) || ! is_array( $fields ) ) {
	return;
}

?>

<div class="tribe-events-meta-group tribe-events-meta-group-other">
	<h2 class="tribe-events-single-section-title"> <?php esc_html_e( 'Other', 'tribe-events-calendar-pro' ) ?> </h2>
	<dl>
		<?php foreach ( $fields as $name => $value ): ?>
			<dt> <?php echo esc_html( $name );  ?> </dt>
			<dd class="tribe-meta-value">
				<?php
				// This can hold HTML. The values are cleansed upstream
				echo $value;
				?>
			</dd>
		<?php endforeach ?>
	</dl>
</div>
