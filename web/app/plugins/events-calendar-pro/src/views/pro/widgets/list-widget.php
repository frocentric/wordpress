<?php
/**
 * Events Pro List Widget Template
 * This is the template for the output of the events list widget.
 * All the items are turned on and off through the widget admin.
 * There is currently no default styling, which is highly needed.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/pro/widgets/list-widget.php
 *
 * When the template is loaded, the following vars are set:
 *
 * @version 4.4.21
 *
 * @var string $start
 * @var string $end
 * @var string $venue
 * @var string $street
 * @var string $city
 * @var string $state
 * @var string $province
 * @var string $zip
 * @var string $country
 * @var string $phone
 * @var string $cost
 * @var array  $instance
 *
 * @package TribeEventsCalendarPro
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Retrieves the posts used in the List Widget loop.
$posts = tribe_get_list_widget_events();

// The URL for this widget's "View More" link.
$link_to_all = tribe_events_get_list_widget_view_all_link( $instance );

// Check if any posts were found.
if ( isset( $posts ) && $posts ) :

	foreach ( $posts as $post ) :
		setup_postdata( $post );
		do_action( 'tribe_events_widget_list_inside_before_loop' ); ?>

		<!-- Event  -->
		<div class="<?php tribe_events_event_classes() ?>">
			<?php tribe_get_template_part( 'pro/widgets/modules/single-event', null, $instance ) ?>
		</div>

		<?php do_action( 'tribe_events_widget_list_inside_after_loop' ) ?>

	<?php endforeach ?>

	<?php if ( ! empty( $link_to_all ) ) : ?>
	<p class="tribe-events-widget-link">
		<a href="<?php esc_attr_e( esc_url( $link_to_all ) ) ?>" rel="bookmark">
			<?php esc_html_e( 'View More&hellip;', 'tribe-events-calendar-pro' ) ?>
		</a>
	</p>
	<?php endif; ?>

<?php
// No Events were found.
else:
?>
	<p><?php printf( __( 'There are no upcoming %s at this time.', 'tribe-events-calendar-pro' ), tribe_get_event_label_plural_lowercase() ); ?></p>
<?php
endif;

// Cleanup. Do not remove this.
wp_reset_postdata();
