<?php
/**
 * View: Organizer meta details
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/organizer/meta/details.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 * @var WP_Post $organizer   The organizer post object.
 * @var bool    $has_details Boolean on whether details exist or not.
 */

if ( ! $has_details ) {
	return;
}
?>
<div class="tribe-events-pro-organizer__meta-details tribe-common-g-col">

	<?php $this->template( 'organizer/meta/details/phone', [ 'organizer' => $organizer ] ); ?>

	<?php $this->template( 'organizer/meta/details/email', [ 'organizer' => $organizer ] ); ?>

	<?php $this->template( 'organizer/meta/details/website', [ 'organizer' => $organizer ] ); ?>

</div>
