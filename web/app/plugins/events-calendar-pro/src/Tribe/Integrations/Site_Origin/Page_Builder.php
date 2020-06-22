<?php
/**
 * Compatibility fixes for interoperation with Site Origin's Page Builder plugin.
 *
 * @since 4.4.29
 */
class Tribe__Events__Pro__Integrations__Site_Origin__Page_Builder {
	/**
	 * Sets up fixes required for improved compatibility with Page Builder.
	 *
	 * @since 4.4.29
	 */
	public function hook() {
		// SO hooks
		add_action( 'admin_print_scripts-widgets.php', array( $this, 'load_widget_admin_scripts' ) );
		add_action( 'siteorigin_panel_enqueue_admin_scripts', array( $this, 'load_widget_admin_assets' ) );

		// Our custom hooks
		add_action( 'tribe_events_pro_widget_render', array( $this, 'enqueue_widget_scripts' ), 10, 3 );
		add_filter( 'tribe_allow_widget_on_post_page_edit_screen', array( $this, 'allow_widget_on_post_page_edit_screen' ), 10, 2 );
	}

	/**
	 * Enqueue the scripts for the widgets
	 *
	 * @since 4.4.29
	 *
	 * @return [type] [description]
	 */
	public function load_widget_admin_scripts() {
		Tribe__Events__Pro__Main::instance()->load_widget_assets();

		// For the "This Week Widget"
		wp_enqueue_script( 'underscore' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );
	}

	/**
	 * Enqueue all assets for the widgets
	 *
	 * @since 4.4.29
	 *
	 * @return [type] [description]
	 */
	public function load_widget_admin_assets() {
		$this->load_widget_admin_scripts();
		Tribe__Events__Pro__Main::instance()->admin_enqueue_styles();
	}

	/**
	 * Filter to allow widget on post edit screen
	 *
	 * @since 4.4.29
	 *
	 * @param  bool   $allow
	 * @param  string $hook
	 *
	 * @return bool
	 */
	public function allow_widget_on_post_page_edit_screen( $allow, $hook = '' ) {
		if ( 'post.php' !== $hook ) {
			return $allow;
		}

		return true;
	}

	/**
	 * Optionally enqueue widget scripts (and styles) when the widget is shown
	 * in a Page Builder panel - because is_active_widget() fails if we're not in a sidebar
	 *
	 * @since 4.4.29
	 *
	 * @return bool true/false for testing
	 */
	public function enqueue_widget_scripts( $class, $unused_args, $unused_instance ) {
		if ( ! function_exists( 'siteorigin_panels_is_panel' ) ) {
			return false;
		}

		if (  ! siteorigin_panels_is_panel() ) {
			return false;
		}

		$page = get_queried_object();
		// We're only worried about enqueuing on posts/pages - the typical stuff applies when in a sidebar
		if ( 'post' !== $page->post_type && 'page' !== $page->post_type ) {
			return false;
		}

		$panels_data = get_post_meta( $page->ID, 'panels_data', true );

		// No widget on page - bail
		if ( empty( $panels_data ) || empty( $panels_data[ 'widgets' ] ) ) {
			return false;
		}

		foreach ( $panels_data[ 'widgets' ] as $widget ) {
			// If $widget[ 'panels_info' ][ 'class' ] is the calling class, enqueue styles.
			if (
				empty( $styles_enqueued )
				&& ! empty( $widget['panels_info']['class'] )
				&& $class === $widget['panels_info']['class']
			) {
				Tribe__Events__Pro__Widgets::enqueue_calendar_widget_styles();
				// Only need to enqueue them once
				$styles_enqueued = true;
			} else {
				// Styles already enqueued, class is empty, or it doesn't match the calling widget - skip.
				continue;
			}

			// Specific to only one widget
			switch ( $widget[ 'panels_info' ][ 'class' ] ) {
				case 'Tribe__Events__Pro__Mini_Calendar_Widget':
					tribe_asset_enqueue( 'tribe-mini-calendar' );
					break;
				case 'Tribe__Events__Pro__Countdown_Widget':
					wp_enqueue_script( 'tribe-events-countdown-widget', tribe_events_pro_resource_url( 'widget-countdown.js' ), array( 'jquery' ), apply_filters( 'tribe_events_pro_js_version', Tribe__Events__Pro__Main::VERSION ), true );
					break;
				default:
					break;
			}
		}

		return false;
	}
}
