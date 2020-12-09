<?php
// Don't load directly
defined( 'WPINC' ) or die;

/**
 * Event Submission Form Taxonomy Block
 * Renders the taxonomy field in the submission form.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/taxonomy.php
 *
 * @since  3.1
 * @version 4.6.3
 */

$uses_select_woo = false;
if ( defined( 'Tribe__Events__Main::VERSION' ) && version_compare( constant( 'Tribe__Events__Main::VERSION' ), '5.1.0-dev', '>=' ) ) {
	$uses_select_woo = true;
}

$selected_terms = [];
$taxonomy_obj   = get_taxonomy( $taxonomy );
$ajax_args = [
	'taxonomy' => $taxonomy,
];

$taxonomy_label = $taxonomy_obj->label;

// If we already have Event on the actual Taxonomy Label
if ( false === strpos( $taxonomy_obj->label, tribe_get_event_label_singular() ) ) {
	$taxonomy_label = sprintf( '%s %s', tribe_get_event_label_singular(), $taxonomy_obj->label );
}

// Check we have terms
$has_terms = count(
	get_terms(
		$taxonomy,
		[
			'hide_empty' => false,
			'number' => 1,
			'fields' => 'ids',
			]
	)
) < 1;

// Setup selected tags
$value = ! empty( $_POST['tax_input'][ $taxonomy ] ) ? explode( ',', esc_attr( trim( $_POST['tax_input'][ $taxonomy ] ) ) ) : [];

// if no tags from $_POST then look for saved tags
if ( empty( $value ) ) {
	$terms = wp_get_post_terms( get_the_ID(), $taxonomy );
	$value = wp_list_pluck( $terms, 'term_id' );
}

foreach ( $value as $term_id ) {
	$term = get_term( $term_id, $taxonomy );
	$selected_terms[] = [
		'id'   => $term->term_id,
		'text' => $term->name,
	];
}

if ( is_array( $value ) ) {
	$value = implode( ',', $value );
}

if ( $has_terms ) {
	return;
}

// This will be used for the placeholder attribute of the taxonomy selection.
$taxonomy_placeholder = __( 'terms', 'tribe-events-community' );

// Default taxonomies labels have word "Event", which is redundant for the placeholder attribute.
// That is removed here, along with any extra spaces around the remaining text.
if ( ! empty( $taxonomy_label ) ) {
	$taxonomy_placeholder = str_replace( tribe_get_event_label_singular(), '', $taxonomy_label );
	$taxonomy_placeholder = strtolower( trim( $taxonomy_placeholder ) );
}

?>
<div class="tribe-section tribe-section-taxonomy">
	<div class="tribe-section-header">
		<h3><?php echo esc_html( $taxonomy_label ); ?></h3>
		<?php echo tribe_community_required_field_marker( "tax_input.$taxonomy" ); ?>
	</div>

	<?php
	/**
	 * Allow developers to hook and add content to the beginning of this section
	 * @param string $taxonomy_slug
	 */
	do_action( 'tribe_events_community_section_before_taxonomy', $taxonomy );
	?>

	<div class="tribe-section-content">
		<div class="tribe-section-content-field">
			<?php if ( $uses_select_woo ) : ?>
				<select
			<?php else : ?>
				<input
			<?php endif; ?>
				class="tribe-dropdown"
				data-options="<?php echo esc_attr( json_encode( $selected_terms ) ); ?>"
				data-source="search_terms"
				data-source-args="<?php echo esc_attr( json_encode( $ajax_args ) ); ?>"
				name="tax_input[<?php echo esc_attr( $taxonomy ); ?>]"
				multiple
				data-dropdown-css-width="false"
				data-allow-html
				data-searching-placeholder="<?php echo esc_attr_x( 'Searching…', 'taxonomy selector ajax search placeholder', 'tribe-events-community' ); ?>"
				placeholder="<?php echo esc_attr( sprintf( __( 'Search from existing %s', 'tribe-events-community' ), $taxonomy_placeholder ) ); ?>"
				value="<?php echo esc_attr( $value ); ?>"
			<?php if ( $uses_select_woo ) : ?>
				>
				</select>
			<?php else : ?>
				/>
			<?php endif; ?>
		</div>
	</div>

	<?php
	/**
	 * Allow developers to hook and add content to the end of this section
	 * @param string $taxonomy_slug
	 */
	do_action( 'tribe_events_community_section_after_taxonomy', $taxonomy );
	?>
</div>
