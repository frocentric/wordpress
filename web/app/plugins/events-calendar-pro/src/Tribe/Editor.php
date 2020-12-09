<?php

/**
 * Class Tribe__Events__Pro__Editor
 *
 * @since 4.5
 */
class Tribe__Events__Pro__Editor extends Tribe__Editor {

	/**
	 * Attach hooks into the editor
	 *
	 * @since 4.5
	 */
	public function hook() {
		add_action( 'tribe_events_pro_after_custom_field_content', array( $this, 'after_custom_field_content' ), 10, 3 );
		add_filter( 'tribe_settings_after_save_additional-fields', array( $this, 'save_custom_field_values' ) );
		add_action( 'block_categories', array( $this, 'register_additional_fields_category' ), 10, 2 );
		add_filter( 'tribe_events_editor_default_template', array( $this, 'add_additional_fields_in_editor' ) );
		add_filter( 'tribe_events_editor_default_classic_template', array( $this, 'add_additional_fields_in_editor' ) );
		add_filter( 'tribe_blocks_editor_update_classic_content_params', array( $this, 'migrate_additional_fields_params_to_blocks' ), 10, 3 );
		add_filter( 'tribe_events_editor_default_classic_template', array( $this, 'add_related_events_in_editor' ), 50 );
		add_filter( 'tribe_events_editor_default_template', array( $this, 'add_related_events_in_editor' ), 50 );

		$this->assets();
	}

	/**
	 * Attach a new input after each custom field input is rendered, the value is being stored in a hidden field and
	 * creates a fake <button> to have A11y benefits like focus and so on. The "fake checkbox" is used as we need to send
	 * to the request the value of this operation regardless of is true / false so using a native checkbox send only
	 * the value when the checkbox is mark as "checked", with this approach the "hidden" field is always being send into
	 * the request regardless of the state of it so we can have a valid reference all the time to the value of each custom
	 * field.
	 *
	 * @since 4.5
	 *
	 * @param $ticket
	 * @param $index
	 * @param $count
	 *
	 * @return mixed
	 */
	public function after_custom_field_content( $ticket, $index, $count ) {
		$value = '1';
		if ( isset( $ticket['gutenberg_editor'] ) ) {
			$value = $ticket['gutenberg_editor'] ? '1' : '0';
		}
		$args  = array(
			'input_id'   => 'gutenberg_editor_' . esc_attr( $index ),
			'value'      => $value,
			'index'      => $index,
			'class_name' => $value === '1' ? 'tribe-custom-field-gutenberg-checkbox--checked' : '',
			'count'      => $count,
		);

		$html = tribe( 'events-pro.editor.admin.template' )->template( array( 'custom-fields', 'gutenberg' ), $args, false );
		echo $html;
	}

	/**
	 * Update the options after the additional fields tabs is saved
	 *
	 * @since 4.5
	 *
	 * @return mixed
	 */
	public function save_custom_field_values() {
		$options = Tribe__Settings_Manager::get_options();
		if ( empty( $options['custom-fields'] ) || ! is_array( $options['custom-fields'] ) ) {
			return $options;
		}
		$gutenberg_fields = $this->gutenberg_custom_fields_canonical_keys(
			tribe_get_request_var( 'custom-field-gutenberg-editor', array() )
		);
		foreach ( $options['custom-fields'] as $index => $field ) {
			$checked = isset( $gutenberg_fields[ $index ] ) && '1' === $gutenberg_fields[ $index ];
			$options['custom-fields'][ $index ]['gutenberg_editor'] = $checked;
		}
		Tribe__Settings_Manager::set_options( $options, false );
	}

	/**
	 * Make sure the keys of the gutenberg custom fields match the same logic as the custom fields, this logic is
	 * basically if the key or index of a gutenberg field has `_` at the start it means it belongs to an existing
	 * meta field and in order to have the right key we just need to remove the '_'  from the start on the other hand
	 * if does not have one it means it's a new created field which requires to grab the highest max value available
	 * at this point and increase from there every time this scenario is presented.
	 *
	 * @since 4.5
	 *
	 * @param array $gutenberg_custom_fields An array with the gutenberg custom fields
	 *
	 * @return array An array with only number as index representing the location of the custom field block
	 */
	public function gutenberg_custom_fields_canonical_keys( $gutenberg_custom_fields ) {
		$max_index = $this->get_custom_fields_max_index();
		$mapped = array();
		foreach ( $gutenberg_custom_fields as $index => $field ) {
			if ( 0 === strpos( $index, '_' ) ) {
				$assigned_index = substr( $index, 1 );
			} else {
				$assigned_index = ++$max_index;
			}
			$mapped[ $assigned_index ] = $field;
		}
		return $mapped;
    }

	/**
	 * Return the highest number for the custom fields, this value is created and updated by PRO. Fallback to a zero
	 * value if is not present on the settings or there are no custom fields present yet.
	 *
	 * @since 4.5
	 *
	 * @return int
	 */
	private function get_custom_fields_max_index() {
		$current_options = Tribe__Settings_Manager::get_options();
		if ( isset( $current_options['custom-fields-max-index'] ) ) {
			return $current_options['custom-fields-max-index'];
		} else if ( isset( $current_options['custom-fields'] ) ) {
			return count( $current_options['custom-fields'] ) + 1;
		} else {
			return 0;
		}
	}

	/**
	 * Register and Load styles and JS behavior into the admin views
	 *
	 * @since 4.5
	 */
	public function assets() {
		$events_pro = Tribe__Events__Pro__Main::instance();

		tribe_asset(
			$events_pro,
			'gutenberg-events-pro-admin-additional-fields-admin-style',
			'app/admin/additional-fields.css',
			array(),
			'admin_enqueue_scripts',
			array(
				'conditionals' => array( $this, 'maybe_load_custom_field_assets' ),
			)
		);

		tribe_asset(
			$events_pro,
			'gutenberg-events-pro-admin-additional-fields-behavior',
			'app/admin-additional-fields.js',
			array(),
			'admin_enqueue_scripts',
			array(
				'conditionals' => array( $this, 'maybe_load_custom_field_assets' ),
			)
		);
	}

	/**
	 * Callback used to load the assets only when the Additional Fields tab is selected
	 *
	 * @since 4.5
	 *
	 * @return bool
	 */
	public function maybe_load_custom_field_assets() {
		$screen = get_current_screen();

		if ( ! $screen instanceof WP_Screen || $screen->id !== 'tribe_events_page_tribe-common' ) {
			return false;

		}
		$tab = tribe_get_request_var( 'tab' );
		return 'additional-fields' === $tab;
	}

	/**
	 * Add the event custom fields on post that are events only
	 *
	 * @since 4.5
	 *
	 * @param $categories
	 * @param $post
	 *
	 * @return array
	 */
	public function register_additional_fields_category( $categories, $post ) {
		if ( ! tribe_is_event( $post ) ) {
			return $categories;
		}

		return array_merge(
			$categories,
			array(
				array(
					'slug'  => 'tribe-events-pro-additional-fields',
					'title' => __( 'Additional Fields', 'events-pro' ),
				),
			)
		);
	}

	/**
	 * Add additional fields templates for new events
	 *
	 * @since 4.5
	 *
	 * @param array $templates
	 *
	 * @return array An array with the templates
	 */
	public function add_additional_fields_in_editor( $templates ) {
		$additional_fields_templates = tribe( 'events-pro.editor.fields' )->get_block_names( true );
		if ( empty( $additional_fields_templates ) ) {
			return $templates;
		}

		$blocks           = array();
		$inserted         = false;
		$insertion_points = array( 'tribe/classic-event-details', 'tribe/event-venue' );
		foreach ( $templates as $template ) {
			$blocks[] = $template;
			if (
				! $inserted
				&& is_array( $template )
				&& in_array( $template[0], $insertion_points, true )
			) {
				foreach ( $additional_fields_templates as $additional_field ) {
					$blocks[] = array( $additional_field );
				}
				$inserted = true;
			}
		}
		return $blocks;
	}

	/**
	 * Generate the attributes from additional fields that are doing the migration into the new blocks
	 * UI, it sets the attributes of the additional fields that are marked
	 * as "Include this field on all new events in the Gutenberg block editor" it sets 4
	 * attributes based on the meta value associated with the additional field
	 *
	 * @since 4.5
	 *
	 * @param $params
	 * @param $slug
	 * @param $post
	 *
	 * @return array
	 */
	public function migrate_additional_fields_params_to_blocks( $params, $slug, $post ) {
		/** @var Tribe__Events__Pro__Editor__Additional_Fields $editor */
		$editor                      = tribe( 'events-pro.editor.fields' );
		$additional_fields_templates = $editor->get_block_names( true, true );
		$block_names                 = array_keys( $additional_fields_templates );

		if (
			empty( $block_names )
			|| empty( $additional_fields_templates )
			|| empty( $additional_fields_templates[ $slug ] )
			|| ! ( $post instanceof WP_Post )
			|| $slug !== $params // if $slug !== $params it means params has been set already as s
		) {
			return $params;
		}

		$field = $additional_fields_templates[ $slug ];
		$value = strval( get_post_meta( $post->ID, $field['name'], true ) );
		$output = $value;

		if ( 'checkbox' === $field['type'] ) {
			$output = $this->format_checkbox_field( $value );
		}

		return array(
			'isPristine' => empty( $value ),
			'output'     => $output,
			'value'      => $value,
			'label'      => $field['label'],
		);
	}

	/**
	 * Format a string using the new blocks UI to shape the values of a meta
	 *
	 * @since 4.5
	 *
	 * @param string $value
	 * @param string $initial_separator
	 * @param string $end_separator
	 *
	 * @return string
	 */
	public function format_checkbox_field( $value = '', $initial_separator = ', ', $end_separator = ' & ' ) {
		$pieces = explode( '|', $value );
		$last   = '';
		if ( count( $pieces ) >= 3 ) {
			$last = trim( array_pop( $pieces ) );
		}

		$output = implode( $initial_separator, $pieces );
		if ( '' === $last ) {
			return $output;
		}
		return implode( $end_separator, array( $output, $last ) );
	}

	/**
	 * Filters and adds the related events block into the default classic blocks
	 *
	 * @since 4.6.2
	 *
	 * @param  array $template
	 *
	 * @return array
	 */
	public function add_related_events_in_editor( $template = array() ) {

		$hide_related_events = tribe_get_option( 'hideRelatedEvents', false );

		if ( $hide_related_events ) {
			return $template;
		}

		$template[] = array( 'tribe/related-events' );
		return $template;

	}
}
