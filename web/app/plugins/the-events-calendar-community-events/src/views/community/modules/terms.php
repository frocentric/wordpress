<?php
/**
 * Event Submission Form Terms of Submission Block.
 * Renders the website fields in the submission form.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/terms.php
 *
 * @since   4.7.1
 *
 * @version 4.7.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/** @var Tribe__Events__Community__Main $main */
$main = tribe( 'community.main' );

$terms_enabled = $main->getOption( 'termsEnabled' );

if ( empty( $terms_enabled ) ) {
	return;
}

$terms_description = $main->getOption( 'termsDescription' );

if ( empty( $terms_description ) ) {
	return;
}
?>

<div class="tribe-section tribe-section-terms">
	<div class="tribe-section-header">
		<h3><?php esc_html_e( 'Terms of Submission', 'tribe-events-community' ); ?></h3>
	</div>

	<?php
	/**
	 * Allow developers to hook and add content to the beginning of this section
	 */
	do_action( 'tribe_events_community_section_before_terms' );
	?>

	<div class="tribe-section-content">
		<textarea
			rows="5"
			cols="100"
			class="event-terms-description"
			readonly
		><?php echo esc_html( $terms_description ); ?></textarea>
		<br />
		<input
			type="checkbox"
			id="terms"
			name="terms"
			value="true"
			class="<?php tribe_community_events_field_classes( 'terms', [ 'event-terms-agree' ] ); ?>"
		/>
		<?php tribe_community_events_field_label( 'terms', __( 'I agree to the terms of submission', 'tribe-events-community' ) ); ?>
	</div>

	<?php
	/**
	 * Allow developers to hook and add content to the end of this section
	 */
	do_action( 'tribe_events_community_section_after_terms' );
	?>
</div>
