<?php


class Tribe__Events__Pro__Templates__Mods__List_View {

	public static function print_all_events_link() {
		$wp_query = tribe_get_global_query_object();
		if ( null === $wp_query ) {
			return;
		}

		// Only add this link if we are viewing all instances of a recurring event (ie, '/event/some-slug/all/')
		if ( 'all' !== $wp_query->get( 'eventDisplay' ) && ! $wp_query->tribe_is_recurrence_list ) {
			return;
		}
		?>
            <p class="tribe-events-back tribe-events-loop">
                <a href="<?php echo esc_url( tribe_get_events_link() ); ?>">
					<?php printf( '&laquo; ' . esc_html__( 'All %s', 'tribe-events-calendar-pro' ), tribe_get_event_label_plural() ); ?>
                </a>
            </p>
        <?php
	}
}
