<?php
/**
 * The main class for the version 1 of the recurrence backend engine.
 *
 * @since 4.7
 */

class Tribe__Events__Pro__Recurrence__Engines__Version_1 implements Tribe__Events__Pro__Recurrence__Engines__Engine_Interface {

	/**
	 * {@inheritdoc}
	 */
	public function get_slug() {
		return Tribe__Events__Pro__Service_Providers__RBE::VERSION_1;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {
		return __( 'Version 1', 'events-pro' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function preview( $data ) {
		return new Tribe__Events__Pro__Recurrence__Engines__Work_Report();
	}

	/**
	 * {@inheritdoc}
	 */
	public function update( $data ) {
		return new Tribe__Events__Pro__Recurrence__Engines__Work_Report();
	}

	/**
	 * {@inheritdoc}
	 */
	public function hook() {
		$main = Tribe__Events__Pro__Main::instance();

		Tribe__Events__Pro__Recurrence__Meta::init( true );

		// Since the `__construct` method of these classes will also hook them they are built here.
		$main->queue_processor = new Tribe__Events__Pro__Recurrence__Queue_Processor;
		$main->queue_realtime  = new Tribe__Events__Pro__Recurrence__Queue_Realtime;
		$main->aggregator      = new Tribe__Events__Pro__Recurrence__Aggregator;

		add_action( 'tribe_events_pre_get_posts', array( $main, 'pre_get_posts' ) );
		add_filter( 'tribe_enable_recurring_event_queries', '__return_true', 10 );
		add_action( 'tribe_events_pre_get_posts', array( $main, 'setup_hide_recurrence_in_query' ) );

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function unhook() {
		$main = Tribe__Events__Pro__Main::instance();
		remove_action( 'tribe_events_pre_get_posts', array( $main, 'pre_get_posts' ) );
		remove_filter( 'tribe_enable_recurring_event_queries', '__return_true', 10 );
		remove_action( 'tribe_events_pre_get_posts', array( $main, 'setup_hide_recurrence_in_query' ) );

		$children_events = Tribe__Events__Pro__Recurrence__Children_Events::instance();
		remove_filter( 'pre_delete_post', array( $children_events, 'prevent_deletion' ), 10 );
		remove_action( 'shutdown', array( $children_events, 'delete_on_shutdown' ) );

		$queue_realtime = Tribe__Events__Pro__Main::instance()->queue_realtime;
		remove_action( 'admin_head-post.php', array( $queue_realtime, 'post_editor' ) );
		remove_action( 'wp_ajax_tribe_events_pro_recurrence_realtime_update', array( $queue_realtime, 'ajax' ) );
		remove_action( 'admin_notices', array( $queue_realtime, 'add_notice' ) );

		$queue_processor = $main->queue_processor;
		remove_action( 'tribe_events_pro_blog_deactivate', array( $queue_processor, 'clear_scheduled_task' ) );
		remove_action( Tribe__Events__Pro__Recurrence__Queue_Processor::SCHEDULED_TASK, array(
			$queue_processor,
			'process_queue',
		), 20 );

		$aggregator = $main->aggregator;
		remove_filter( 'tribe_aggregator_before_save_event', array( $aggregator, 'generate_recurrence_meta' ), 10 );

		// Unhook the Recurrence__Meta class core functions.
		Tribe__Events__Pro__Recurrence__Meta::unhook( true );

		return true;
	}
}
