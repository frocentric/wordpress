<?php
/**
 * Widget admin for the event list widget.
 */

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'tribe-events-calendar-pro' ); ?></label>
	<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
</p>
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php esc_html_e( 'Number of events to show:', 'tribe-events-calendar-pro' ); ?></label>
	<select id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" class="widefat">
		<?php for ( $i = 1; $i <= 10; $i ++ ) {
			?>
			<option <?php if ( $i == $instance['limit'] ) {
				echo 'selected="selected"';
			} ?> > <?php echo $i; ?> </option>
		<?php } ?>
	</select>
</p>

<p><?php esc_html_e( 'Display:', 'tribe-events-calendar-pro' ); ?><br />

	<?php $displayoptions = array(
		'cost'      => __( 'Price', 'tribe-events-calendar-pro' ),
		'venue'     => __( 'Venue', 'tribe-events-calendar-pro' ),
		'street'    => __( 'Street', 'tribe-events-calendar-pro' ),
		'city'      => __( 'City', 'tribe-events-calendar-pro' ),
		'region'    => __( 'State (US) Or Province (Int)', 'tribe-events-calendar-pro' ),
		'zip'       => __( 'Postal Code', 'tribe-events-calendar-pro' ),
		'country'   => __( 'Country', 'tribe-events-calendar-pro' ),
		'phone'     => __( 'Phone', 'tribe-events-calendar-pro' ),
		'organizer' => __( 'Organizer', 'tribe-events-calendar-pro' ),
	);

	/**
	 * Enable the street checkbox if address was checked on version 4.4.26 or earlier.
	 *
	 * @todo remove after 4.6.0 (added for continuity when users transition from 4.4.26 or earlier to this release)
	 *
	 * @version 4.4.27
	 */
	if ( isset( $instance['address'] ) && tribe_is_truthy( $instance['address'] ) ) {
		$instance['street']  = true;
		$instance['address'] = false;
	}

	foreach ( $displayoptions as $option => $label ) {
		?>
		<input class="checkbox" type="checkbox" value="1" <?php checked( $instance[ $option ], true ); ?> id="<?php echo esc_attr( $this->get_field_id( $option ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $option ) ); ?>" style="margin-left:5px" />
		<label for="<?php echo esc_attr( $this->get_field_id( $option ) ); ?>"><?php echo $label ?></label>
		<br />
	<?php } ?>
</p>

<?php
/**
 * Filters
 */

if ( is_string( $instance['filters'] ) ) {
	$instance['filters'] = json_decode( maybe_unserialize( $instance['filters'] ) );
}

$class = '';
if ( empty( $instance['filters'] ) ) {
	$class = 'display:none;';
}
?>

<div class="calendar-widget-filters-container" style="<?php echo esc_attr( $class ); ?>">

	<h3 class="calendar-widget-filters-title"><?php esc_html_e( 'Filters', 'tribe-events-calendar-pro' ); ?>:</h3>

	<input type="hidden" name="<?php echo esc_attr( $this->get_field_name( 'filters' ) ); ?>"
	       id="<?php echo esc_attr( $this->get_field_id( 'filters' ) ); ?>" class="calendar-widget-added-filters"
	       value='<?php echo esc_attr( wp_json_encode( $instance['filters'] ) ); ?>' />
	<div class="calendar-widget-filter-list">
		<?php
		$disabled = array();
		if ( ! empty( $instance['filters'] ) ) {

			echo '<ul>';

			foreach ( $instance['filters'] as $tax => $terms ) {
				$tax_obj = get_taxonomy( $tax );

				foreach ( $terms as $term ) {
					if ( empty( $term ) ) {
						continue;
					}
					$term_obj = get_term( $term, $tax );

					if ( empty( $term_obj ) || is_wp_error( $term_obj ) ) {
						continue;
					}

					// Add to the disabled ones.
					$disabled[] = $term_obj->term_id;
					echo sprintf( "<li><p>%s: %s&nbsp;&nbsp;<span><a href='#' class='calendar-widget-remove-filter' data-tax='%s' data-term='%s'>(" . __( 'remove', 'tribe-events-calendar-pro' ) . ')</a></span></p></li>', $tax_obj->labels->name, $term_obj->name, $tax, $term_obj->term_id );
				}
			}

			echo '</ul>';
		}
		?>

	</div>

	<p class="calendar-widget-filters-operand">
		<label for="<?php echo esc_attr( $this->get_field_name( 'operand' ) ); ?>">
			<input <?php checked( $instance['operand'], 'AND' ); ?> type="radio" name="<?php echo esc_attr( $this->get_field_name( 'operand' ) ); ?>" value="AND">
			<?php esc_html_e( 'Match all', 'tribe-events-calendar-pro' ); ?></label><br />
		<label for="<?php echo esc_attr( $this->get_field_name( 'operand' ) ); ?>">
			<input <?php checked( $instance['operand'], 'OR' ); ?> type="radio" name="<?php echo esc_attr( $this->get_field_name( 'operand' ) ); ?>" value="OR">
			<?php esc_html_e( 'Match any', 'tribe-events-calendar-pro' ); ?></label>
	</p>
</div>
<p class="tribe-widget-term-filter">
	<label><?php esc_html_e( 'Add a filter', 'tribe-events-calendar-pro' ); ?>:	</label>
	<select
		type="hidden"
		placeholder="<?php esc_attr_e( 'Select a Taxonomy Term', 'tribe-events-calendar-pro' ); ?>"
		data-source="terms"
		data-hide-search
		data-prevent-clear
		class="widefat calendar-widget-add-filter tribe-widget-select2"
		id="<?php echo esc_attr( $this->get_field_id( 'selector' ) ); ?>"
		data-disabled="<?php echo esc_attr( json_encode( $disabled ) ); ?>"
	>
		<option selected="selected" value="-1"><?php esc_html_e( 'Select a Taxonomy Term', 'tribe-events-calendar-pro' ); ?></option>
	</select>
</p>

<p>
	<input id="<?php echo esc_attr( $this->get_field_id( 'no_upcoming_events' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'no_upcoming_events' ) ); ?>" type="checkbox" <?php checked( $instance['no_upcoming_events'], 1 ); ?> value="1" />
	<label for="<?php echo esc_attr( $this->get_field_id( 'no_upcoming_events' ) ); ?>"><?php esc_html_e( 'Hide this widget if there are no upcoming events', 'tribe-events-calendar-pro' ); ?></label>
</p>
<p>
	<input id="<?php echo esc_attr( $this->get_field_id( 'featured_events_only' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'featured_events_only' ) ); ?>" type="checkbox" <?php checked( $instance['featured_events_only'], 1 ); ?> value="1" />
	<label for="<?php echo esc_attr( $this->get_field_id( 'featured_events_only' ) ); ?>"><?php echo esc_html_x( 'Limit to featured events only', 'events list widget setting', 'tribe-events-calendar-pro' ); ?></label>
</p>
<p>
	<input class="checkbox" type="checkbox" value="1" <?php checked( $instance['jsonld_enable'], true ); ?>
	       id="<?php echo esc_attr( $this->get_field_id( 'jsonld_enable' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'jsonld_enable' ) ); ?>"/>
	<label for="<?php echo esc_attr( $this->get_field_id( 'jsonld_enable' ) ); ?>"><?php esc_html_e( 'Generate JSON-LD data', 'tribe-events-calendar-pro' ); ?></label>
</p>
