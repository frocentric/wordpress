<?php
// Don't load directly
defined( 'WPINC' ) || die;

/**
 * Delete Event Module
 * This is used to delete a user submitted event.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/delete.php
 *
 * @since    2.1
 * @version  4.5
 *
 */

$current_user = wp_get_current_user(); ?>

<div id="add-new">
	<a href="<?php echo esc_url( tribe_community_events_add_event_link() ); ?>" class="button"><?php esc_html_e( 'Add New', 'tribe-events-community' ); ?></a>
</div>

<div id="my-events">
	<a href="<?php echo esc_url( tribe_community_events_list_events_link() ); ?>" class="button"><?php printf( __( 'My %s', 'tribe-events-community' ), tribe_get_event_label_plural() ); ?></a>
</div>

<div id="not-user">
	<?php echo __( 'Not', 'tribe-events-community' ) . ' <i>' . $current_user->display_name . '</i>'; ?>
	<a href="<?php echo esc_url( tribe_community_events_logout_url() ); ?>"><?php esc_attr_e( 'Log Out', 'tribe-events-community' ); ?></a>
</div>

<div style="clear:both"></div>

<?php $this->outputMessage();
