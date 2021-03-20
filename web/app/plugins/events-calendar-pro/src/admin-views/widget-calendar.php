<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'tribe-events-calendar-pro' ); ?>
		<input type="text" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" value="<?php echo esc_attr( strip_tags( $instance['title'] ) ); ?>" />
	</label>
</p>

<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Number of events to list below the mini calendar:', 'tribe-events-calendar-pro' ); ?>
		<input type="text" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>"
		       id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"
		       value="<?php echo esc_attr( strip_tags( $instance['count'] ) ); ?>" />
	</label>
</p>

<?php
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

	<ul class="calendar-widget-filter-list">
		<?php
		$disabled = array();
		if ( ! empty( $instance['filters'] ) ) {
			foreach ( (array) $instance['filters'] as $tax => $terms ) {
				$tax_obj = get_taxonomy( $tax );

				foreach ( $terms as $term ) {
					if ( empty( $term ) ) {
						continue;
					}
					$term_obj = get_term( $term, $tax );

					// Add to the disabled ones.
					$disabled[] = $term_obj->term_id;
					echo sprintf(
						"<li><p>%s: %s&nbsp;&nbsp;<span><a href='#' class='calendar-widget-remove-filter' data-tax='%s' data-term='%s'>(" . esc_html__( 'remove', 'tribe-events-calendar-pro' ) . ')</a></span></p></li>',
						esc_html( $tax_obj->labels->name ),
						esc_html( $term_obj->name ),
						esc_attr( $tax ),
						esc_attr( $term_obj->term_id )
					);
				}
			}
		}
		?>

	</ul>

	<p class="calendar-widget-filters-operand">
		<label for="<?php echo esc_attr( $this->get_field_name( 'operand' ) ); ?>">
			<input <?php checked( $instance['operand'], 'AND' ); ?> type="radio" name="<?php echo esc_attr( $this->get_field_name( 'operand' ) ); ?>" value="AND">
			<?php esc_html_e( 'Match all', 'tribe-events-calendar-pro' ); ?>
		</label>

		<br />

		<label for="<?php echo esc_attr( $this->get_field_name( 'operand' ) ); ?>">
			<input
				type="radio"
				name="<?php echo esc_attr( $this->get_field_name( 'operand' ) ); ?>"
				value="OR"
				<?php checked( $instance['operand'], 'OR' ); ?>
			>
			<?php esc_html_e( 'Match any', 'tribe-events-calendar-pro' ); ?>
		</label>
	</p>
</div>
<p class="tribe-widget-term-filter">
	<label><?php esc_html_e( 'Add a filter', 'tribe-events-calendar-pro' ); ?>:	</label>
	<select
		type="hidden"
		placeholder="<?php esc_attr_e( 'Select a Taxonomy Term', 'tribe-events-calendar-pro' ); ?>"
		data-source="terms"
		data-prevent-clear
		class="widefat calendar-widget-add-filter tribe-widget-select2"
		id="<?php echo esc_attr( $this->get_field_id( 'selector' ) ); ?>"
		data-disabled="<?php echo esc_attr( json_encode( $disabled ) ); ?>"
	>
		<option selected="selected" value="-1"><?php esc_html_e( 'Select a Taxonomy Term', 'tribe-events-calendar-pro' ); ?></option>
	</select>
</p>
<p>
	<input
		class="checkbox"
		type="checkbox"
		value="1"
		<?php checked( $instance['jsonld_enable'], true ); ?>
		id="<?php echo esc_attr( $this->get_field_id( 'jsonld_enable' ) ); ?>"
		name="<?php echo esc_attr( $this->get_field_name( 'jsonld_enable' ) ); ?>"
	/>
	<label for="<?php echo esc_attr( $this->get_field_id( 'jsonld_enable' ) ); ?>"><?php esc_html_e( 'Generate JSON-LD data', 'tribe-events-calendar-pro' ); ?></label>
</p>
