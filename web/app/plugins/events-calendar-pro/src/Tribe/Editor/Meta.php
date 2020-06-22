<?php

/**
 * Register the required Meta fields for Blocks Editor API saving
 * Initialize Gutenberg Event Meta fields
 *
 * @since 4.5
 */
class Tribe__Events__Pro__Editor__Meta extends Tribe__Editor__Meta {
	/**
	 * Register the required Meta fields for good Gutenberg saving
	 *
	 * @since 4.5
	 *
	 * @return void
	 */
	public function register() {
		/** @var Tribe__Events__Pro__Editor__Recurrence__Blocks_Meta $blocks_meta */
		$blocks_meta = tribe( 'events-pro.editor.recurrence.blocks-meta' );
		register_meta( 'post', $blocks_meta->get_rules_key(), $this->text() );
		register_meta( 'post', $blocks_meta->get_exclusions_key(), $this->text() );
		register_meta( 'post', $blocks_meta->get_description_key(), $this->text() );
		$this->register_additional_fields();

		$this->hook();
	}

	/**
	 * Register the fields used by dynamic fields into the REST API
	 *
	 * @since 4.5
	 */
	public function register_additional_fields() {
		$additional_fields = array_values( tribe_get_option( 'custom-fields', array() ) );
		foreach ( $additional_fields as $field ) {

			$has_fields = isset( $field['name'], $field['type'], $field['gutenberg_editor'] );
			if ( ! $has_fields ) {
				continue;
			}

			switch ( $field['type'] ) {
				case 'textarea':
					$args = $this->textarea();
					break;
				case 'url':
					$args = $this->url();
					break;
				case 'checkbox':
					$args = $this->text();
					register_meta( 'post', '_' . $field['name'], $this->text_array() );
					break;
				default:
					$args = $this->text();
					break;
			}
			register_meta( 'post', $field['name'], $args );
		}
	}

	/**
	 * Add filters into the Meta class
	 *
	 * @since 4.5
	 */
	public function hook() {
		add_filter( 'get_post_metadata', array( $this, 'fake_blocks_response' ), 15, 4 );
		add_filter( 'get_post_metadata', array( $this, 'fake_recurrence_description' ), 15, 4 );
		add_action( 'deleted_post_meta', array( $this, 'remove_recurrence_meta' ), 10, 3 );
		add_filter( 'tribe_events_pro_show_recurrence_meta_box', array( $this, 'show_recurrence_classic_meta' ), 10, 2 );
		add_filter( 'tribe_events_pro_split_redirect_url', array( $this, 'split_series_link' ), 10, 2 );
	}

	/**
	 * Return a fake response with the data from the old classic meta field into the new meta field keys
	 * used by the new recurrence UI, returns only: rules and exclusions
	 *
	 * @since 4.5
	 *
	 * @param null|array|string $value The value get_metadata() should return a single metadata value, or an
	 *                                    array of values.
	 * @param int               $post_id Post ID.
	 * @param string            $meta_key Meta key.
	 * @param string|array      $single Meta value, or an array of values.
	 *
	 * @return array|null|string The attachment metadata value, array of values, or null.
	 */
	public function fake_blocks_response( $value, $post_id, $meta_key, $single ) {
		/** @var Tribe__Events__Pro__Editor__Recurrence__Blocks_Meta $blocks_meta */
		$blocks_meta = tribe( 'events-pro.editor.recurrence.blocks-meta' );
		$valid_keys  = array(
			$blocks_meta->get_exclusions_key(),
			$blocks_meta->get_rules_key(),
		);

		if ( ! in_array( $meta_key, $valid_keys ) ) {
			return $value;
		}

		$recurrence = get_post_meta( $post_id, '_EventRecurrence', true );
		$result     = $this->get_value( $post_id, $meta_key );
		if ( empty( $recurrence ) || ! empty( $result ) ) {
			return $value;
		}

		$keys = array(
			$blocks_meta->get_rules_key()      => 'rules',
			$blocks_meta->get_exclusions_key() => 'exclusions',
		);
		$key  = $keys[ $meta_key ];
		if ( empty( $recurrence[ $key ] ) ) {
			return $value;
		}

		$types = $recurrence[ $key ];
		$data  = array();
		foreach ( $types as $type ) {
			$blocks = new Tribe__Events__Pro__Editor__Recurrence__Blocks( $type );
			$blocks->parse();
			$data[] = $blocks->get_parsed();
		}
		$encoded = json_encode( $data );

		return $single ? $encoded : array( $encoded );
	}

	/**
	 * Fake the description value from _EventRecurrence into a dynamic meta value that is located at
	 * tribe( 'events-pro.editor.recurrence.blocks-meta' )->get_description_key();
	 *
	 * @since 4.5
	 *
	 * @param $value mixed The original value
	 * @param $post_id int The Id of the post
	 * @param $meta_key string The name of the meta key
	 * @param $single Bool true if a single value should be returned
	 *
	 * @return array|string
	 */
	public function fake_recurrence_description( $value, $post_id, $meta_key, $single ) {
		/** @var Tribe__Events__Pro__Editor__Recurrence__Blocks_Meta $blocks_meta */
		$blocks_meta = tribe( 'events-pro.editor.recurrence.blocks-meta' );

		if ( $meta_key !== $blocks_meta->get_description_key() ) {
			return $value;
		}

		$description = $this->get_value( $post_id, $meta_key );

		if ( empty( $description ) ) {
			$recurrence = get_post_meta( $post_id, '_EventRecurrence', true );
			$description = isset( $recurrence['description'] ) ? $recurrence['description'] : '';
		}

		return $single ? $description : array( $description );
	}

	/**
	 * Return the meta value of a post ID directly from the DB
	 *
	 * @since 4.5
	 *
	 * @param int    $post_id
	 * @param string $meta_key
	 *
	 * @return mixed
	 */
	public function get_value( $post_id = 0, $meta_key = '' ) {
		global $wpdb;
		$query = "SELECT meta_value FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s";

		return $wpdb->get_var( $wpdb->prepare( $query, $post_id, $meta_key ) );
	}

	/**
	 * Removes the meta keys that maps into the classic editor when the `_EventRecurrence` is
	 * removed.
	 *
	 * @since 4.5
	 *
	 * @param $meta_id
	 * @param $object_id
	 * @param $meta_key
	 */
	public function remove_recurrence_meta( $meta_id, $object_id, $meta_key ) {
		if ( '_EventRecurrence' !== $meta_key ) {
			return;
		}
		/** @var Tribe__Events__Pro__Editor__Recurrence__Blocks_Meta $blocks_meta */
		$blocks_meta = tribe( 'events-pro.editor.recurrence.blocks-meta' );
		delete_post_meta( $object_id, $blocks_meta->get_rules_key() );
		delete_post_meta( $object_id, $blocks_meta->get_exclusions_key() );
	}

	/**
	 * Remove the recurrence meta box based on recurrence structure for blocks
	 *
	 * @since 4.5
	 * @since 4.5.3 Added $post_id param
	 *
	 * @param  mixed  $show_meta  Default value to display recurrence or not
	 * @param  int    $post_id    Which post we are dealing with
	 *
	 * @return bool
	 */
	public function show_recurrence_classic_meta( $show_meta, $post_id ) {
		/** @var Tribe__Editor $editor */
		$editor = tribe( 'editor' );

		// Return default on non classic editor
		if ( ! $editor->is_classic_editor() ) {
			return $show_meta;
		}

		// when it doesnt have blocks we return default
		if ( ! has_blocks( absint( $post_id ) ) ) {
			return $show_meta;
		}

		return false;
	}

	/**
	 * Redirect to classic editor if the event does not have any block on it
	 *
	 * @since 4.5
	 *
	 * @param $url
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public function split_series_link( $url, $post_id ) {
		$args = array();
		if ( ! has_blocks( absint( $post_id ) ) ) {
			$args = array( 'classic-editor' => '' );
		}

		return add_query_arg( $args, $url );
	}
}
