<?php

/**
 * Class Tribe__Events__Filterbar__Settings
 */
class Tribe__Events__Filterbar__Settings {
	const OPTION_ACTIVE_FILTERS = 'tribe_events_filters_current_active_filters';

	public function set_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'addAdminScriptsAndStyles' ) );
		add_action( 'tribe_settings_do_tabs', array( $this, 'addSettingsTab' ), 10, 0 );
		add_action( 'tribe_settings_after_content_tab_filter-view', array( $this, 'addSettingsContent' ) );
		add_action( 'tribe_settings_save_tab_filter-view', array( $this, 'save_settings_tab' ), 10, 0 );
		add_action( 'wp_before_admin_bar_render', array( $this, 'add_toolbar_item' ), 12 );
		add_filter( 'tribe_events_liveupdate_automatic_label_text', [ $this, 'liveupdate_automatic_label_text' ] );
		add_filter( 'tribe_events_liveupdate_manual_label_text', [ $this, 'liveupdate_manual_label_text' ] );

		$setting_action = 'plugin_action_links_' . trailingslashit( basename( TRIBE_EVENTS_FILTERBAR_DIR ) ) . 'the-events-calendar-filter-view.php';
		add_action( $setting_action, [ $this, 'add_links_to_plugin_actions' ] );
	}

	public function addAdminScriptsAndStyles() {
		global $current_screen;

		// settings screen
		if ( isset( $current_screen->id ) && $current_screen->id == 'tribe_events_page_' . Tribe__Settings::$parent_slug ) {
			wp_enqueue_style(
				'TribeEventsFilterAdmin-css',
				Tribe__Events__Filterbar__View::instance()->pluginUrl . 'src/resources/css/filter-admin.css',
				array(),
				apply_filters( 'tribe_events_filters_css_version', Tribe__Events__Filterbar__View::VERSION )
			);

			tribe_asset_enqueue( 'tribe-events-filterbar-admin-settings' );
		}

	}

	/**
	 * Add the Filters settings tab.
	 *
	 * @return void
	 */
	public function addSettingsTab() {
		$fields = $this->get_field_definitions();
		new Tribe__Settings_Tab( 'filter-view', __( 'Filters', 'tribe-events-filter-view' ), array( 'priority' => 36, 'fields' => $fields ) );
	}

	/**
	 * Add the content to the settings tab.
	 *
	 * @return void
	 */
	public function addSettingsContent() {
		require_once( Tribe__Events__Filterbar__View::plugin_path( 'src/admin-views/tribe-filter-view-options.php' ) );
	}

	public function render_available_filters_box() {
		$filters = Tribe__Events__Filterbar__View::instance()->get_registered_filters();
		$current_filters = Tribe__Events__Filterbar__View::instance()->get_active_filters();
		include( Tribe__Events__Filterbar__View::plugin_path( 'src/admin-views/settings-field-available-filters.php' ) );
	}

	public function render_active_filters_box() {
		$filters = Tribe__Events__Filterbar__View::instance()->get_registered_filters();
		$sorted_filters = array();
		foreach ( Tribe__Events__Filterbar__View::instance()->get_active_filters() as $slug ) {
			if ( isset( $filters[ $slug ] ) ) {
				$sorted_filters[ $slug ] = $filters[ $slug ];
				unset( $filters[ $slug ] );
			}
		}
		$filters = array_merge( $sorted_filters, $filters );
		include( Tribe__Events__Filterbar__View::plugin_path( 'src/admin-views/settings-field-active-filters.php' ) );
	}

	/**
	 * Save submitted settings from the filters tab
	 * @return void
	 */
	public function save_settings_tab() {
		$active_filters = array();
		$active_filter_slugs = (array) ( isset( $_POST['tribe_active_filters'] ) ? $_POST['tribe_active_filters'] : array() );
		foreach ( $active_filter_slugs as $filter_slug ) {
			$filter_options = (array) ( isset( $_POST['tribe_filter_options'][ $filter_slug ] ) ? $_POST['tribe_filter_options'][ $filter_slug ] : array() );
			$active_filters[ $filter_slug ] = $filter_options;
		}
		uasort( $active_filters, array( $this, 'compare_filters_by_priority' ) );
		update_option( self::OPTION_ACTIVE_FILTERS, $active_filters );
	}

	private function compare_filters_by_priority( $a, $b ) {
		if ( $a['priority'] == $b['priority'] ) {
			return 0;
		}
		return ( $a['priority'] < $b['priority'] ) ? -1 : 1;
	}

	private function get_field_definitions() {
		$fields = array(
			'events_filters_section_title' => array(
				'type' => 'html',
				'html' => '<h3>' . __( 'Filters', 'tribe-events-filter-view' ) . '</h3>',
			),
			'events_filters_section_description' => array(
				'type' => 'html',
				'html' => '<p class="description">' .  sprintf(
					esc_html__( 'The settings below allow you to enable or disable front-end %s filters. Uncheck the box to hide the filter. Drag and drop active filters to re-arrange them.', 'tribe-events-filter-view' ),
					tribe_get_event_label_singular()
				) . '</p>
						   <p class="description">' . __( 'Expand an active filter to edit the label and choose from a subset of input types (dropdown, select, range slider, checkbox and radio).', 'tribe-events-filter-view' ) . '</p>',
			),
			'events_filters_available_filters' => array(
				'type' => 'html',
				'display_callback' => array( $this, 'render_available_filters_box' ),
			),
			'events_filters_active_filters' => array(
				'type' => 'html',
				'display_callback' => array( $this, 'render_active_filters_box' ),
			),
			'events_filters_layout' => array(
				'type' => 'radio',
				'label'           => __( 'Filters Layout', 'tribe-events-filter-view' ),
				'default'         => 'vertical',
				'options' => array(
					'vertical' => __( 'Vertical', 'tribe-events-filter-view' ),
					'horizontal' => __( 'Horizontal', 'tribe-events-filter-view' ),
				),
				'validation_type' => 'options',
			),
			'events_filters_default_state' => array(
				'type' => 'radio',
				'label'           => __( 'Filter Bar default state', 'tribe-events-filter-view' ),
				'default'         => 'closed',
				'options' => array(
					'closed' => __( 'Stay collapsed until visitors open it', 'tribe-events-filter-view' ),
					'open' => __( 'Show on initial page load', 'tribe-events-filter-view' ),
				),
				'validation_type' => 'options',
			),
		);
		$fields = apply_filters( 'tribe-event-filters-settings-fields', $fields );
		return $fields;
	}

	/**
	 * Adds a link to the Filter Settings tab in the admin toolbar.
	 */
	public function add_toolbar_item() {
		global $wp_admin_bar;
		$parent = 'tribe-events-settings';
		$link = Tribe__Settings::instance()->get_url( array( 'tab' => 'filter-view' ) );

		if ( ! current_user_can( 'manage_options' ) || null === $wp_admin_bar->get_node( $parent ) ) return;

		$wp_admin_bar->add_menu( array(
			'id' => 'tribe-events-filter-settings',
			'parent' => $parent,
			'href' => $link,
			'title' => __( 'Filter Bar', 'tribe-events-filter-view' ),
		) );
	}

	/**
	 * Modify the live update label when Filterbar Is active and is automatic.
	 *
	 * @since 4.9.3
	 *
	 * @param string $text Previous text used for label.
	 *
	 * @return string New label based on filterbar.
	 */
	public function liveupdate_automatic_label_text( $text ) {
		return __( 'Enabled: datepicker and filter selections automatically update calendar views', 'tribe-events-filter-view' );
	}

	/**
	 * Modify the live update label when Filterbar Is active and is manual.
	 *
	 * @since 4.9.3
	 *
	 * @param string $text Previous text used for label.
	 *
	 * @return string New label based on filterbar.
	 */
	public function liveupdate_manual_label_text( $text ) {
		return __( 'Disabled: users must manually submit date search and Filter Bar', 'tribe-events-filter-view' );
	}

	/**
	 * Add Filter Bar settings link the the plugin admin list.
	 *
	 * @since 5.0.0
	 *
	 * @param array $actions An array of links to add to the plugin admin list.
	 *
	 * @return array An array of links to add to the plugin admin list.
	 */
	public function add_links_to_plugin_actions( $actions ) {

		$settings_url = Tribe__Settings::instance()->get_url( [ 'tab' => 'filter-view' ] );

		$actions['settings'] = '<a href="' . esc_url( $settings_url ) . '">' . esc_html__( 'Settings', 'tribe-events-filter-view' ) . '</a>';

		return $actions;
	}
}
