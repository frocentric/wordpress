<?php
/**
 * Smooths integration of Community Events and the Divi theme.
 *
 * @since 4.5.10
 */
class Tribe__Events__Community__Integrations__Divi {
	/**
	 * Setup the required hooks to provide smooth, unblemished integration with
	 * the Divi theme.
	 *
	 * @since 4.5.10
	 */
	public function hooks() {
		add_filter(
			'pre_get_document_title',
			[ $this, 'on_pre_get_document_title' ],
			5
		);
	}

	/**
	 * Fires when the `pre_get_document_title` filter hook is triggered.
	 *
	 * Does not actually act on the filtered value, it's passed through without being
	 * modified: this is just a convenient point in time to decide whether we need to
	 * take further corrective action or not.
	 *
	 * @since 4.5.10
	 *
	 * @param string $unused_filter_value
	 *
	 * @return string
	 */
	public function on_pre_get_document_title( $unused_filter_value ) {
		// Protect against a notice level error that can be triggered when Divi is active
		// and /community/events/add/ is accessed
		if ( tribe_is_community_edit_event_page() ) {
			$this->override_divi_seo_single_field_title_option();
		}

		return $unused_filter_value;
	}

	/**
	 * Alters Divi's "seo_single_field_title" option value.
	 *
	 * The intention is for this to only ever happen when the Community Events edit
	 * page is requested. The problem is solves is that it stops some interesting
	 * logic in the elegant_titles_filter() from trying to grab a non-existent post
	 * meta field for our virtual page.
	 *
	 * @since 4.5.10
	 *
	 * @see elegant_titles_filter()
	 * @see https://central.tri.be/issues/72700
	 */
	protected function override_divi_seo_single_field_title_option() {
		// Both of the following globals belong to Divi
		global $et_theme_options;
		global $shortname;

		// We only need to introduce a value to this option if it is not already set
		if ( ! isset( $et_theme_options[ $shortname . '_seo_single_field_title' ] ) ) {
			$et_theme_options[ $shortname . '_seo_single_field_title' ] = '_community_events_divi_single_field_title';
		}
	}
}
