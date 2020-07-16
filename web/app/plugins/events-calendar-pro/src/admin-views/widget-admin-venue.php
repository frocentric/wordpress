<?php
/**
 * Widget admin for the related events widget.
 */

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<div class="tribe featured-venue widget-settings">
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'tribe-events-calendar-pro' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( strip_tags( $instance['title'] ) ); ?>" />
	</p>
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Venue:', 'tribe-events-calendar-pro' ); ?></label>
		<select class="venue-dropdown" id="<?php echo esc_attr( $this->get_field_id( 'venue_ID' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'venue_ID' ) ); ?>" value="<?php echo esc_attr( $instance['venue_ID'] ); ?>">
			<?php
			foreach ( $venues as $venue ) {
				?>
				<option value="<?php echo esc_attr( $venue->ID ); ?>" <?php selected( $venue->ID == $instance['venue_ID'] ) ?>> <?php echo tribe_get_venue( $venue->ID ); ?></option>
			<?php } ?>
		</select>
	</p>
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Number of events to show:', 'tribe-events-calendar-pro' ); ?></label>
		<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" value="<?php echo esc_attr( $instance['count'] ); ?>">
			<?php for ( $i = 1; $i <= 10; $i ++ ) {
				?>
				<option <?php selected( $i == $instance['count'] ) ?>> <?php echo $i; ?> </option>
			<?php } ?>
		</select>
	</p>
	<p>
		<input class="checkbox" type="checkbox" value="1" <?php checked( $instance['hide_if_empty'], true ); ?> id="<?php echo esc_attr( $this->get_field_id( 'hide_if_empty' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'hide_if_empty' ) ); ?>" />
		<label for="<?php echo esc_attr( $this->get_field_id( 'hide_if_empty' ) ); ?>"><?php esc_html_e( 'Hide this widget if there are no upcoming events', 'tribe-events-calendar-pro' ); ?></label>
	</p>
	<p>
		<input class="checkbox" type="checkbox" value="1" <?php checked( $instance['jsonld_enable'], true ); ?>
		       id="<?php echo esc_attr( $this->get_field_id( 'jsonld_enable' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'jsonld_enable' ) ); ?>"/>
		<label for="<?php echo esc_attr( $this->get_field_id( 'jsonld_enable' ) ); ?>"><?php esc_html_e( 'Generate JSON-LD data', 'tribe-events-calendar-pro' ); ?></label>
	</p>
</div>

