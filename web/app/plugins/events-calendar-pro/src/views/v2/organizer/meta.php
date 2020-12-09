<?php
/**
 * View: Organizer meta
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/organizer/meta.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @version 5.0.0
 *
 * @var WP_Post $organizer The organizer post object.
 *
 */

$classes = [ 'tribe-events-pro-organizer__meta' ];

$content = tribe_get_the_content( null, false, $organizer->ID );
$url     = tribe_get_organizer_website_url( $organizer->ID );
$email   = tribe_get_organizer_email( $organizer->ID );
$phone   = tribe_get_organizer_phone( $organizer->ID );

$has_content = ! empty( $content );
$has_details = ! empty( $url ) || ! empty( $email ) || ! empty( $phone );
?>
<div <?php tribe_classes( $classes ); ?>>

	<?php $this->template( 'organizer/meta/title', [ 'organizer' => $organizer ] ); ?>

	<?php if ( $has_content || $has_details ) : ?>

		<div class="tribe-events-pro-organizer__meta-row tribe-common-g-row tribe-common-g-row--gutters">

			<?php $this->template( 'organizer/meta/details', [ 'organizer' => $organizer, 'has_details' => $has_details ] ); ?>

			<?php $this->template( 'organizer/meta/content', [ 'organizer' => $organizer ] ); ?>

		</div>

	<?php endif; ?>

</div>
