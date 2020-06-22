<?php
/**
 * View: Week View
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @version  5.0.2
 *
 * @var string   $rest_url             The REST URL.
 * @var string   $rest_nonce           The REST nonce.
 * @var int      $should_manage_url    int containing if it should manage the URL.
 * @var array    $events               An array of the week events, in sequence.
 * @var array    $mobile_days          An array of the week events, formatted to the requirements of the mobile version of the View.
 * @var bool     $disable_event_search Boolean on whether to disable the event search.
 * @var string[] $container_classes    Classes used for the container of the view.
 * @var bool     $hide_weekends        Boolean on whether to hide weekends.
 * @var array    $container_data       An additional set of container `data` attributes.
 * @var string   $breakpoint_pointer   String we use as pointer to the current view we are setting up with breakpoints.
 */

$header_classes = [ 'tribe-events-header' ];
if ( empty( $disable_event_search ) ) {
	$header_classes[] = 'tribe-events-header--has-event-search';
}

$grid_classes = [ 'tribe-events-pro-week-grid', 'tribe-common-a11y-hidden' ];
if ( $hide_weekends ) {
	$grid_classes[] = 'tribe-events-pro-week-grid--hide-weekends';
}
?>
<div
	<?php tribe_classes( $container_classes ); ?>
	data-js="tribe-events-view"
	data-view-rest-nonce="<?php echo esc_attr( $rest_nonce ); ?>"
	data-view-rest-url="<?php echo esc_url( $rest_url ); ?>"
	data-view-manage-url="<?php echo esc_attr( $should_manage_url ); ?>"
	<?php foreach ( $container_data as $key => $value ) : ?>
		data-view-<?php echo esc_attr( $key ) ?>="<?php echo esc_attr( $value ) ?>"
	<?php endforeach; ?>
	<?php if ( ! empty( $breakpoint_pointer ) ) : ?>
		data-view-breakpoint-pointer="<?php echo esc_attr( $breakpoint_pointer ); ?>"
	<?php endif; ?>
>
	<div class="tribe-common-l-container tribe-events-l-container">

		<?php $this->template( 'components/loader', [ 'text' => __( 'Loading...', 'tribe-events-calendar-pro' ) ] ); ?>

		<?php $this->template( 'components/json-ld-data' ); ?>

		<?php $this->template( 'components/data' ); ?>

		<?php $this->template( 'components/before' ); ?>

		<header <?php tribe_classes( $header_classes ); ?>>
			<?php $this->template( 'components/messages' ); ?>

			<?php $this->template( 'components/breadcrumbs' ); ?>

			<?php $this->template( 'components/events-bar' ); ?>

			<?php $this->template( 'week/top-bar' ); ?>
		</header>

		<?php $this->template( 'components/filter-bar' ); ?>

		<?php $this->template( 'week/day-selector' ); ?>

		<?php $this->template( 'week/mobile-events', [ 'days' => $mobile_days ] ); ?>

		<div
			<?php tribe_classes( $grid_classes ); ?>
			role="grid"
			aria-labelledby="tribe-events-pro-week-header"
			aria-readonly="true"
		>

			<?php $this->template( 'week/grid-header' ); ?>

			<?php $this->template( 'week/grid-body' ); ?>

		</div>

		<?php $this->template( 'components/ical-link' ); ?>

		<?php $this->template( 'components/after' ); ?>
	</div>
</div>

<?php $this->template( 'components/breakpoints' ); ?>
