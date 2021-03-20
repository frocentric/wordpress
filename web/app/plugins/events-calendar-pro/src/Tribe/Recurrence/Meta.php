<?php


/**
 * Tribe__Events__Pro__Recurrence__Meta
 *
 * WordPress hooks and filters controlling event recurrence
 */
class Tribe__Events__Pro__Recurrence__Meta {

	const UPDATE_TYPE_ALL    = 1;
	const UPDATE_TYPE_FUTURE = 2;
	const UPDATE_TYPE_SINGLE = 3;

	/** @var Tribe__Events__Pro__Recurrence__Scheduler */
	public static $scheduler = null;

	/**
	 * @var Tribe__Events__Pro__Recurrence__Children_Events
	 */
	protected static $children;

	/**
	 * Initializes the Recurrence Meta class and sub-classes and hooks on the filters required for it to work.
	 *
	 * @since 3.1
	 *
	 * @param bool $all Whether to init all the functions and hook all the filters for the class or just the ones
	 *                  common to all the Recurrence Instance Engines.
	 */
	public static function init( $all = true ) {
		$actions = array(
			array( 'tribe_events_date_display', array( __CLASS__, 'loadRecurrenceData' ), 10, 1 ),
			array( 'pre_get_comments', array( __CLASS__, 'set_post_id_for_recurring_event_comment_queries' ), 10, 1 ),
			array( 'comment_post_redirect', array( __CLASS__, 'fix_redirect_after_comment_is_posted' ), 10, 2 ),
			array( 'wp_update_comment_count', array( __CLASS__, 'update_comment_counts_on_child_events' ), 10, 3 ),
			array(
				'manage_' . Tribe__Events__Main::POSTTYPE . '_posts_custom_column',
				array( __CLASS__, 'populate_custom_list_table_columns' ),
				10,
				2,
			),
			array( 'wp_before_admin_bar_render', array( __CLASS__, 'admin_bar_render' ), 10, 1 ),
			array( 'tribe_community_events_enqueue_resources', array( __CLASS__, 'enqueue_recurrence_data' ), 10, 1 ),
			array(
				'tribe_events_community_form_before_template',
				array( __CLASS__, 'output_recurrence_json_data' ),
				10,
				1,
			),
		);

		if ( $all ) {
			array_push( $actions,
				[ 'tribe_events_update_meta', [ __CLASS__, 'updateRecurrenceMeta' ], 20, 2 ],
				[ 'before_delete_post', [ __CLASS__, 'handle_delete_request' ], 10, 1 ],
				[ 'wp_trash_post', [ __CLASS__, 'handle_trash_request' ], 10, 1 ],
				[ 'untrashed_post', [ __CLASS__, 'handle_untrash_request' ], 10, 1 ],
				[ 'admin_notices', [ __CLASS__, 'showRecurrenceErrorFlash' ], 10, 1 ],
				[ 'tribe_recurring_event_error', [ __CLASS__, 'setupRecurrenceErrorMsg' ], 10, 2 ],
				[ 'admin_action_tribe_split', [ __CLASS__, 'handle_split_request' ], 10, 1 ],
				[ 'load-edit.php', [ __CLASS__, 'combineRecurringRequestIds' ], 10, 1 ],
				[ 'updated_post_meta', [ __CLASS__, 'update_child_thumbnails' ], 4, 40 ],
				[ 'added_post_meta', [ __CLASS__, 'update_child_thumbnails' ], 4, 40 ],
				[ 'deleted_post_meta', [ __CLASS__, 'remove_child_thumbnails' ], 4, 40 ], [
					'update_option_' . Tribe__Main::OPTIONNAME,
					[
						Tribe__Events__Pro__Recurrence__Old_Events_Cleaner::instance(),
						'clean_up_old_recurring_events',
					],
					10,
					2,
				] );
		}

		foreach ( $actions as $action ) {
			list( $tag, $callback, $priority, $args ) = $action;
			if ( has_action( $tag, $callback ) ) {
				continue;
			}
			add_action( $tag, $callback, $priority, $args );
		}

		$filters = array(
			array( 'get_edit_post_link', array( __CLASS__, 'filter_edit_post_link' ), 10, 3 ),
			array( 'preprocess_comment', array( __CLASS__, 'set_parent_for_recurring_event_comments' ), 10, 1 ),
			array( 'comments_array', array( __CLASS__, 'set_comments_array_on_child_events' ), 10, 2 ),
			array(
				'manage_' . Tribe__Events__Main::POSTTYPE . '_posts_columns',
				array( __CLASS__, 'list_table_column_headers' ),
				10,
				1,
			),
			array( 'post_class', array( __CLASS__, 'add_recurring_event_post_classes' ), 10, 3 ),
			array( 'post_row_actions', array( __CLASS__, 'edit_post_row_actions' ), 10, 2 ),
			array( 'tribe_settings_tab_fields', array( __CLASS__, 'inject_settings' ), 10, 2 ),
			array(
				'tribe_events_pro_output_recurrence_data',
				array( __CLASS__, 'maybe_fix_datepicker_output' ),
				10,
				2,
			),
		);

		if ( $all ) {
			$filters[] = array( 'posts_request', array( 'Tribe__Events__Pro__Recurrence__Queries', 'collapse_sql' ), 10, 2 );
		}

		foreach ( $filters as $filter ) {
			list( $tag, $callback, $priority, $args ) = $filter;
			if ( has_filter( $tag, $callback ) ) {
				continue;
			}
			add_filter( $tag, $callback, $priority, $args );
		}

		if ( is_admin() ) {
			if ( ! has_filter( 'tribe_events_pro_localize_script', [
				Tribe__Events__Pro__Recurrence__Scripts::instance(),
				'localize',
			] ) ) {
				add_filter( 'tribe_events_pro_localize_script', [
					Tribe__Events__Pro__Recurrence__Scripts::instance(),
					'localize',
				], 10, 3 );
			}
		}

		tribe_asset(
			Tribe__Events__Pro__Main::instance(),
			Tribe__Events__Main::POSTTYPE . '-recurrence',
			'events-recurrence.css',
			[ 'tribe-select2-css', 'tribe-common-admin' ],
			null
		);

		/**
		 * Register Notices
		 */
		if ( $all ) {
			self::reset_scheduler();
			Tribe__Admin__Notices::instance()->register( 'editing-all-recurrences', [
				__CLASS__,
				'render_notice_editing_all_recurrences',
			], 'type=success' );
			Tribe__Admin__Notices::instance()->register( 'created-recurrences', [
				__CLASS__,
				'render_notice_created_recurrences',
			], 'type=success' );
		}
	}

	/**
	 * Displays a message informing the user she is editing all of the recurrences in the series.
	 */
	public static function render_notice_editing_all_recurrences() {
		if ( ! Tribe__Admin__Helpers::instance()->is_post_type_screen( Tribe__Events__Main::POSTTYPE ) ) {
			return false;
		}

		if ( empty( $_REQUEST['post'] ) || ! tribe_is_recurring_event( $_REQUEST['post'] ) ) {
			return false;
		}

		$message = '<p>' . esc_html__( 'You are currently editing all events in a recurring series.', 'tribe-events-calendar-pro' ) . '</p>';

		return Tribe__Admin__Notices::instance()->render( 'editing-all-recurrences', $message );
	}

	public static function render_notice_created_recurrences() {
		if ( ! Tribe__Admin__Helpers::instance()->is_post_type_screen( Tribe__Events__Main::POSTTYPE ) ) {
			return false;
		}

		if ( empty( $_REQUEST['post'] ) || ! tribe_is_recurring_event( $_REQUEST['post'] ) ) {
			return false;
		}

		$pending = get_post_meta( get_the_ID(), '_EventNextPendingRecurrence', true );

		if ( ! $pending ) {
			return false;
		}

		$start_dates     = tribe_get_recurrence_start_dates( get_the_ID() );
		$count           = count( $start_dates );
		$last            = end( $start_dates );
		$pending_message = __( '%d instances of this event have been created through %s. <a href="%s" target="_blank">Learn more.</a>', 'tribe-events-calendar-pro' );
		$pending_message = '<p>' . sprintf( $pending_message, $count, date_i18n( tribe_get_date_format( true ), strtotime( $last ) ), 'https://evnt.is/lq' ) . '</p>';

		return Tribe__Admin__Notices::instance()->render( 'created-recurrences', $pending_message );
	}

	public static function filter_edit_post_link( $url, $post_id, $context ) {
		if ( ! empty( $post_id ) && tribe_is_recurring_event( $post_id ) && $parent = wp_get_post_parent_id( $post_id ) ) {
			return get_edit_post_link( $parent, $context );
		}

		return $url;
	}

	/**
	 * Checks to fix all the required datepicker dates to the correct format
	 *
	 * @param  array $recurrence The Recurrence meta rules
	 *
	 * @return array              Recurrence Meta after maybe fixing the data
	 */
	public static function maybe_fix_datepicker_output( $recurrence, $post_id ) {
		if ( empty( $recurrence['exclusions'] ) ) {
			return $recurrence;
		}

		// Fetch the datepicker current format
		$datepicker_format = Tribe__Date_Utils::datepicker_formats( tribe_get_option( 'datepickerFormat' ) );

		foreach ( $recurrence['exclusions'] as &$exclusion ) {
			// Only do something if the exclusion is custom+date
			if ( empty( $exclusion['custom'] ) || 'Date' !== $exclusion['custom']['type'] ) {
				continue;
			}

			// Actually do the conversion of output
			if ( ! empty( $exclusion['custom']['date']['date'] ) ) {
				$formatted = date( $datepicker_format, strtotime( $exclusion['custom']['date']['date'] ) );
				$exclusion['custom']['date']['date'] = $formatted;
				// set the end too to stick with new format
				$exclusion['end'] = $formatted;
			} else {
				if ( isset( $exclusion['end'] ) ) {
					$exclusion['end'] = date( $datepicker_format, strtotime( $exclusion['end'] ) );
				}
			}

			// If there's no value, clean the output of the custom date
			if ( isset( $exclusion['custom']['date']['date'] ) && ! $exclusion['custom']['date']['date'] ) {
				$exclusion = Tribe__Utils__Array::set( $exclusion, array( 'custom', 'date', 'date' ), '' );
			}
		}

		return $recurrence;
	}

	/**
	 * Change the link for a recurring event to edit its series
	 *
	 * @return void
	 */
	public static function admin_bar_render() {
		/** @var WP_Admin_Bar $wp_admin_bar */
		global $post, $wp_admin_bar;

		if ( ! $post instanceof WP_Post ) {
			return;
		}

		if ( is_admin() || ! tribe_is_recurring_event( $post ) ) {
			return;
		}
		if ( get_query_var( 'eventDisplay' ) == 'all' ) {
			return;
		}
		$menu_parent = $wp_admin_bar->get_node( 'edit' );
		if ( ! $menu_parent ) {
			return;
		}
		// We need to make sure we're editing the correct post (in cases where we show events on venue pages, etc), and the user can do so.
		if ( get_edit_post_link( $post->ID ) === $menu_parent->href && current_user_can( 'edit_post', $post->ID ) ) {
			$wp_admin_bar->add_node( array(
				'id'     => 'edit-series',
				'title'  => __( 'Edit Series', 'tribe-events-calendar-pro' ),
				'parent' => 'edit',
				'href'   => $menu_parent->href,
			) );
			$wp_admin_bar->add_node( array(
				'id'     => 'split-single',
				'title'  => __( 'Break from Series', 'tribe-events-calendar-pro' ),
				'parent' => 'edit',
				'href'   => esc_url( wp_nonce_url( self::get_split_series_url( $post->ID, false, false ), 'tribe_split_' . $post->ID ) ),
				'meta'   => array(
					'class' => 'tribe-split-single',
				),
			) );
			if ( ! empty( $post->post_parent ) ) {
				$wp_admin_bar->add_node( array(
					'id'     => 'split-series',
					'title'  => __( 'Edit Future Events', 'tribe-events-calendar-pro' ),
					'parent' => 'edit',
					'href'   => esc_url( wp_nonce_url( self::get_split_series_url( $post->ID, false, true ), 'tribe_split_' . $post->ID ) ),
					'meta'   => array(
						'class' => 'tribe-split-all',
					),
				) );
			}
		}
	}

	public static function list_table_column_headers( $columns ) {
		$columns['recurring'] = __( 'Recurring', 'tribe-events-calendar-pro' );

		return $columns;
	}

	public static function populate_custom_list_table_columns( $column, $post_id ) {
		if ( $column == 'recurring' ) {
			if ( tribe_is_recurring_event( $post_id ) ) {
				echo esc_html__( 'Yes', 'tribe-events-calendar-pro' );
			} else {
				echo esc_html__( '—', 'tribe-events-calendar-pro' );
			}
		}
	}

	public static function add_recurring_event_post_classes( $classes, $class, $post_id ) {
		if ( get_post_type( $post_id ) == Tribe__Events__Main::POSTTYPE && tribe_is_recurring_event( $post_id ) ) {
			$classes[] = 'tribe-recurring-event';

			$post = get_post( $post_id );
			if ( empty( $post->post_parent ) ) {
				$classes[] = 'tribe-recurring-event-parent';
			} else {
				$classes[] = 'tribe-recurring-event-child';
			}
		}

		return $classes;
	}

	public static function edit_post_row_actions( $actions, $post ) {
		if ( tribe_is_recurring_event( $post ) ) {
			unset( $actions['inline hide-if-no-js'] );
			$post_type_object   = get_post_type_object( Tribe__Events__Main::POSTTYPE );
			$is_first_in_series = empty( $post->post_parent );
			$first_id_in_series = $post->post_parent ? $post->post_parent : $post->ID;
			if ( isset( $actions['edit'] ) && 'trash' != $post->post_status ) {
				if ( current_user_can( 'edit_post', $post->ID ) ) {
					$split_actions          = array();
					$split_actions['split'] = sprintf( '<a href="%s" class="tribe-split tribe-split-single" title="%s">%s</a>',
						esc_url( wp_nonce_url( self::get_split_series_url( $post->ID, false, false ), 'tribe_split_' . $post->ID ) ),
						esc_attr( __( 'Break this event out of its series and edit it independently', 'tribe-events-calendar-pro' ) ),
						__( 'Edit Single', 'tribe-events-calendar-pro' )
					);
					if ( ! $is_first_in_series ) {
						$split_actions['split_all'] = sprintf( '<a href="%s" class="tribe-split tribe-split-all" title="%s">%s</a>',
							esc_url( wp_nonce_url( self::get_split_series_url( $post->ID, false, true ), 'tribe_split_' . $post->ID ) ),
							esc_attr( __( 'Split the series in two at this point, creating a new series out of this and all subsequent events', 'tribe-events-calendar-pro' ) ),
							__( 'Edit Upcoming', 'tribe-events-calendar-pro' )
						);
					}
					$actions = Tribe__Main::array_insert_after_key( 'edit', $actions, $split_actions );
				}
				if ( current_user_can( 'edit_post', $first_id_in_series ) ) {
					$edit_series_url = get_edit_post_link( $first_id_in_series, 'display' );
					$actions['edit'] = sprintf(
						'<a href="%s" title="%s">%s</a>',
						esc_url( $edit_series_url ),
						esc_attr( __( 'Edit all events in this series', 'tribe-events-calendar-pro' ) ),
						__( 'Edit All', 'tribe-events-calendar-pro' )
					);
				}
			}
			if ( $is_first_in_series ) {
				if ( ! empty( $actions['trash'] ) ) {
					$actions['trash'] = "<a class='submitdelete' title='" . esc_attr( __( 'Move all events in this series to the Trash', 'tribe-events-calendar-pro' ) ) . "' href='" . esc_url( get_delete_post_link( $post->ID ) ) . "'>" . esc_html__( 'Trash Series', 'tribe-events-calendar-pro' ) . '</a>';
				}
				if ( ! empty( $actions['delete'] ) ) {
					$actions['delete'] = "<a class='submitdelete' title='" . esc_attr( __( 'Delete all events in this series permanently',
							'tribe-events-calendar-pro' ) ) . "' href='" . esc_url( get_delete_post_link( $post->ID, '', true ) ) . "'>" . esc_html__( 'Delete Series Permanently',
							'tribe-events-calendar-pro' ) . '</a>';
				}
			}
			if ( ! empty( $actions['untrash'] ) ) { // if the whole series is in the trash, restore the whole series together
				$first_event = get_post( $first_id_in_series );
				if ( isset( $first_event->post_status ) && $first_event->post_status == 'trash' ) {
					$actions['untrash'] = "<a title='" . esc_attr( __( 'Restore all events in this series from the Trash',
							'tribe-events-calendar-pro' ) ) . "' href='" . esc_url( wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash',
							$first_id_in_series ) ), 'untrash-post_' . $first_id_in_series ) ) . "'>" . esc_html__( 'Restore Series', 'tribe-events-calendar-pro' ) . '</a>';
				}
			}
		}

		return $actions;
	}

	private static function get_split_series_url( $id, $context = 'display', $all = false ) {
		if ( ! $post = get_post( $id ) ) {
			return;
		}

		if ( 'revision' === $post->post_type ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return;
		}

		$post_type_object = get_post_type_object( $post->post_type );
		if ( ! $post_type_object ) {
			return;
		}

		$args = array( 'action' => 'tribe_split' );
		if ( $all ) {
			$args['split_all'] = 1;
		}
		$url = admin_url( sprintf( $post_type_object->_edit_link, $post->ID ) );
		$url = add_query_arg( $args, $url );
		if ( $context == 'display' ) {
			$url = esc_url( $url );
		}

		return apply_filters( 'tribe_events_get_split_series_link', $url, $post->ID, $context, $all );
	}

	public static function handle_split_request() {
		// TODO: would be nice to have a way to add it back into the series (i.e., an undo)
		$post_id = isset( $_REQUEST['post'] ) ? $_REQUEST['post'] : 0;
		check_admin_referer( 'tribe_split_' . $post_id );
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			wp_die( 'You do not have sufficient capabilities to edit this event', 'tribe-events-calendar-pro' );
			exit();
		}

		$splitter = new Tribe__Events__Pro__Recurrence__Series_Splitter();

		if ( ! empty( $_REQUEST['split_all'] ) ) {
			$splitter->break_remaining_events_from_series( $post_id );
		} else {
			$splitter->break_single_event_from_series( $post_id );
		}

		// TODO: show a message?

		$edit_url = get_edit_post_link( $post_id, false );
		wp_redirect( $edit_url );
		exit();
	}

	public static function handle_trash_request( $post_id ) {
		if ( tribe_is_recurring_event( $post_id ) && ! wp_get_post_parent_id( $post_id ) ) {
			self::children()->trash_all( $post_id );
		}
	}

	public static function handle_untrash_request( $post_id ) {
		if ( tribe_is_recurring_event( $post_id ) && ! wp_get_post_parent_id( $post_id ) ) {
			self::children()->untrash_all( $post_id );
		}
	}

	public static function handle_delete_request( $post_id ) {
		if ( tribe_is_recurring_event( $post_id ) ) {
			$parent = wp_get_post_parent_id( $post_id );
			$parent_exists = ! is_null( get_post( $parent ) );
			if ( empty( $parent ) ) {
				self::children()->permanently_delete_all( $post_id );
			} elseif ( $parent_exists ) {
				$recurrence_meta = get_post_meta( $parent, '_EventRecurrence', true );

				/**
				 * Controls whether exclusions should be generated for deleted child events.
				 *
				 * @param bool $should_generate_exclusion
				 * @param int  $post_id
				 * @param int  $parent
				 */
				if ( apply_filters( 'tribe_events_pro_should_generate_exclusion_for_deleted_event', true, $post_id, $parent ) ) {
					$recurrence_meta = self::add_date_exclusion_to_recurrence(
						$recurrence_meta,
						get_post_meta( $post_id, '_EventStartDate', true )
					);
				}

				update_post_meta( $parent, '_EventRecurrence', $recurrence_meta );
			}
		}
	}

	/**
	 * Given a date, add a date exclusion to the recurrence meta array
	 *
	 * @param array  $recurrence_meta Recurrence meta array that holds recurrence rules/exclusions
	 * @param string $date            Date to add to the exclusions array
	 */
	public static function add_date_exclusion_to_recurrence( $recurrence_meta, $date ) {
		if ( ! isset( $recurrence_meta['exclusions'] ) ) {
			$recurrence_meta['exclusions'] = array();
		}

		$recurrence_meta['exclusions'][] = array(
			'type'   => 'Custom',
			'custom' => array(
				'type' => 'Date',
				'date' => array(
					'date' => $date,
				),
			),
		);

		return $recurrence_meta;
	}

	/**
	 * Comments on recurring events should be kept with the parent event
	 *
	 * @param array $commentdata
	 *
	 * @return array
	 */
	public static function set_parent_for_recurring_event_comments( $commentdata ) {
		if ( isset( $commentdata['comment_post_ID'] ) && tribe_is_recurring_event( $commentdata['comment_post_ID'] ) ) {
			$event = get_post( $commentdata['comment_post_ID'] );
			if ( ! empty( $event->post_parent ) ) {
				$commentdata['comment_post_ID'] = $event->post_parent;
			}
		}

		return $commentdata;
	}

	/**
	 * When displaying comments on a recurring event, get them from the parent
	 *
	 * @param WP_Comment_Query $query
	 *
	 * @return void
	 */
	public static function set_post_id_for_recurring_event_comment_queries( $query ) {
		if ( ! empty( $query->query_vars['post_id'] ) && tribe_is_recurring_event( $query->query_vars['post_id'] ) ) {
			$event = get_post( $query->query_vars['post_id'] );
			if ( ! empty( $event->post_parent ) ) {
				$query->query_vars['post_id'] = $event->post_parent;
			}
		}
	}

	public static function fix_redirect_after_comment_is_posted( $location, $comment ) {
		if ( tribe_is_recurring_event( $comment->comment_post_ID ) ) {
			if ( isset( $_REQUEST['comment_post_ID'] ) && $_REQUEST['comment_post_ID'] != $comment->comment_post_ID ) {
				$child = get_post( $_REQUEST['comment_post_ID'] );
				if ( $child->post_parent == $comment->comment_post_ID ) {
					$location = str_replace( get_permalink( $comment->comment_post_ID ), get_permalink( $child->ID ), $location );
				}
			}
		}

		return $location;
	}

	public static function update_comment_counts_on_child_events( $parent_id, $new_count, $old_count ) {
		if ( tribe_is_recurring_event( $parent_id ) ) {
			$event = get_post( $parent_id );

			if ( ! empty( $event->post_parent ) ) {
				return; // no idea how we got here, but don't update anything
			}

			/** @var wpdb $wpdb */
			global $wpdb;

			$wpdb->update( $wpdb->posts, array( 'comment_count' => $new_count ), array(
				'post_parent' => $parent_id,
				'post_type'   => Tribe__Events__Main::POSTTYPE,
			) );

			$child_ids = self::children()->get_ids( $parent_id );

			foreach ( $child_ids as $child ) {
				clean_post_cache( $child );
			}
		}
	}

	public static function set_comments_array_on_child_events( $comments, $post_id ) {
		if ( empty( $comments ) && tribe_is_recurring_event( $post_id ) ) {
			$event = get_post( $post_id );
			if ( ! empty( $event->post_parent ) ) {
				/** @var wpdb $wpdb */
				global $wpdb, $user_ID;
				$commenter            = wp_get_current_commenter();
				$comment_author       = $commenter['comment_author']; // Escaped by sanitize_comment_cookies()
				$comment_author_email = $commenter['comment_author_email'];  // Escaped by sanitize_comment_cookies()
				if ( $user_ID ) {
					$comments = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND (comment_approved = '1' OR ( user_id = %d AND comment_approved = '0' ) )  ORDER BY comment_date_gmt",
							$event->post_parent, $user_ID
						)
					);
				} elseif ( empty( $comment_author ) ) {
					$comments = get_comments( array(
						'post_id' => $event->post_parent,
						'status'  => 'approve',
						'order'   => 'ASC',
					) );
				} else {
					$comments = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND ( comment_approved = '1' OR ( comment_author = %s AND comment_author_email = %s AND comment_approved = '0' ) ) ORDER BY comment_date_gmt",
							$event->post_parent,
							wp_specialchars_decode( $comment_author, ENT_QUOTES ),
							$comment_author_email
						)
					);
				}
			}
		}

		return $comments;
	}

	/**
	 * Update event recurrence when a recurring event is saved
	 *
	 * @param integer $event_id id of the event to update
	 * @param array   $data     data defining the recurrence of this event
	 *
	 * @return bool `true` on success, `false` on failure
	 */
	public static function updateRecurrenceMeta( $event_id, $data ) {
		if ( ! isset( $data['recurrence'] ) ) {
			return false;
		}

		// do not update recurrence meta on preview
		if ( isset( $data['wp-preview'] ) && $data['wp-preview'] === 'dopreview' ) {
			return false;
		}

		$meta_builder    = new Tribe__Events__Pro__Recurrence__Meta_Builder( $event_id, $data );
		$recurrence_meta = $meta_builder->build_meta();

		$updated = update_post_meta( $event_id, '_EventRecurrence', $recurrence_meta );

		$events_saver = new Tribe__Events__Pro__Recurrence__Events_Saver( $event_id, $updated );

		return $events_saver->save_events();
	}//end updateRecurrenceMeta

	/**
	 * Displays the events recurrence form on the event editor screen
	 *
	 * @param integer $post_id ID of the current event
	 *
	 * @return void
	 */
	public static function loadRecurrenceData( $post_id ) {
		$post = get_post( $post_id );
		if ( ! empty( $post->post_parent ) ) {
			return; // don't show recurrence fields for instances of a recurring event
		}

		/**
		 * Control if the recurrence meta is displayed
		 *
		 * @since 4.4.35
		 *
		 * @param bool $display By default is true
		 * @param int $post_id The ID of the post where the meta box is being included
		 */
		$show_recurrence_meta = apply_filters( 'tribe_events_pro_show_recurrence_meta_box', true, $post_id );

		if ( ! empty( $post->post_parent ) || ! $show_recurrence_meta ) {

			include Tribe__Events__Pro__Main::instance()->pluginPath . 'src/admin-views/event-recurrence-blocks-message.php';

			return; // don't show recurrence fields for instances of a recurring event
		}

		$recurrence = array();
		if ( $post_id ) {
			$recurrence = self::getRecurrenceMeta( $post_id );
		}

		self::enqueue_recurrence_data( $post_id );

		$premium = Tribe__Events__Pro__Main::instance();
		include Tribe__Events__Pro__Main::instance()->pluginPath . 'src/admin-views/event-recurrence.php';
	}

	/**
	 * Localizes recurrence JS data
	 */
	public static function enqueue_recurrence_data( $post_id = null ) {
		wp_enqueue_style( Tribe__Events__Main::POSTTYPE . '-recurrence' );

		if ( $post_id ) {
			// convert array to variables that can be used in the view
			$recurrence = self::getRecurrenceMeta( $post_id );

			/**
			 * Creates a way to filter the output of recurrence meta depending on the ID
			 *
			 * @var $recurrence Meta Info
			 * @var $post_id    the post ID
			 */
			$recurrence = apply_filters( 'tribe_events_pro_output_recurrence_data', $recurrence, $post_id );

			wp_localize_script(
				Tribe__Events__Main::POSTTYPE . '-premium-admin',
				'tribe_events_pro_recurrence_data', $recurrence
			);
		}

		wp_localize_script(
			Tribe__Events__Main::POSTTYPE . '-premium-admin', 'tribe_events_pro_recurrence_strings',
			array(
				'date'       => self::date_strings(),
				'recurrence' => Tribe__Events__Pro__Recurrence__Strings::recurrence_strings(),
				'exclusion'  => array(),
			)
		);
	}

	/**
	 * Outputs recurrence data in JSON format when localize script won't work
	 *
	 * @param int $post_id Post ID of event
	 */
	public static function output_recurrence_json_data( $post_id, $recurrence = array() ) {
		// convert array to variables that can be used in the view
		$recurrence = self::getRecurrenceMeta( $post_id, $recurrence );

		/**
		 * Creates a way to filter the output of recurrence meta depending on the ID
		 *
		 * @var $recurrence Meta Info
		 * @var $post_id    the post ID
		 */
		$recurrence = apply_filters( 'tribe_events_pro_output_recurrence_data', $recurrence, $post_id );
		?>
		<script>
			var tribe_events_pro_recurrence_data = <?php echo json_encode( $recurrence ); ?>;
		</script>
		<?php
	}

	public static function filter_passthrough( $data ) {
		return $data;
	}

	/**
	 * Setup an error message if there is a problem with a given recurrence.
	 * The message is saved in an option to survive the page load and is deleted after being displayed
	 *
	 * @param array $event The event object that is being saved
	 * @param array $msg   The message to display
	 *
	 * @return void
	 */
	public static function setupRecurrenceErrorMsg( $event_id, $msg ) {
		global $current_screen;

		// only do this when editing events
		if ( is_admin() && $current_screen->id == Tribe__Events__Main::POSTTYPE ) {
			update_post_meta( $event_id, 'tribe_flash_message', $msg );
		}
	}

	/**
	 * Display an error message if there is a problem with a given recurrence and clear it from the cache
	 *
	 * @return void
	 */
	public static function showRecurrenceErrorFlash() {
		global $post, $current_screen;

		if ( $current_screen->base == 'post' && $current_screen->post_type == Tribe__Events__Main::POSTTYPE ) {
			$msg = get_post_meta( $post->ID, 'tribe_flash_message', true );

			if ( $msg ) {
				echo '<div class="error"><p>Recurrence not saved: ' . $msg . '</p></div>';
				delete_post_meta( $post->ID, 'tribe_flash_message' );
			}
		}
	}

	/**
	 * Convenience method for turning event meta into keys available to turn into PHP variables
	 *
	 * @param integer $post_id         ID of the event being updated
	 * @param array   $recurrence_data The actual recurrence data
	 *
	 * @return array
	 */
	public static function getRecurrenceMeta( $post_id, $recurrence_data = null ) {
		if ( ! $recurrence_data ) {
			$recurrence_data = get_post_meta( $post_id, '_EventRecurrence', true );
		}

		// Update the recurrence data if needed (useful for rules created pre-4.4)
		$rules_updater = new Tribe__Events__Pro__Recurrence__Rule_Updater( $post_id, $recurrence_data );
		$recurrence_data = $rules_updater->update_if_required();

		if ( is_array( $recurrence_data ) ) {
			if (
				isset( $recurrence_data['rules']['custom']['year']['month-number'] )
				&& $recurrence_data['rules']['custom']['year']['month-number']
			) {
				$month_number = $recurrence_data['rules']['custom']['year']['month-number'];
				$recurrence_data['rules']['custom']['year']['number'] = Tribe__Events__Pro__Recurrence__Series_Rules_Factory::intToOrdinal( $month_number );
				unset( $recurrence_data['rules']['custom']['year']['month-number'] );
			}

			if (
				isset( $recurrence_data['rules']['custom']['year']['month-day'] )
				&& $recurrence_data['rules']['custom']['year']['month-day']
			) {
				$month_day = $recurrence_data['rules']['custom']['year']['month-day'];
				$recurrence_data['rules']['custom']['year']['day'] = $month_day;
				unset( $recurrence_data['rules']['custom']['year']['month-day'] );
			}

			// update legacy data
			if ( $recurrence_data && ! isset( $recurrence_data['rules'] ) ) {
				$recurrence_data = self::get_legacy_recurrence_meta( $post_id, $recurrence_data );
			}
		}

		$recurrence_data = self::recurrenceMetaDefault( $recurrence_data );

		return apply_filters( 'Tribe__Events__Pro__Recurrence_Meta_getRecurrenceMeta', $recurrence_data );
	}

	/**
	 * Convenience method for turning event meta into keys available to turn into PHP variables
	 *
	 * @param integer $post_id         ID of the event being updated
	 * @param         $recurrence_data array The actual recurrence data
	 *
	 * @return array
	 */
	public static function get_legacy_recurrence_meta( $post_id, $recurrence_data = null ) {
		if ( ! $recurrence_data ) {
			$recurrence_data = get_post_meta( $post_id, '_EventRecurrence', true );
		}

		$utils = new Tribe__Events__Pro__Recurrence__Utils;

		$record = array();

		if ( $recurrence_data ) {
			$record['EventStartDate'] = empty( $recurrence_data['EventStartDate'] ) ? tribe_get_start_date( $post_id ) : $recurrence_data['EventStartDate'];
			$record['EventEndDate']   = empty( $recurrence_data['EventEndDate'] ) ? tribe_get_end_date( $post_id ) : $recurrence_data['EventEndDate'];
			$record['type']           = empty( $recurrence_data['type'] ) ? null : $recurrence_data['type'];
			$record['end-type']       = empty( $recurrence_data['end-type'] ) ? null : $recurrence_data['end-type'];
			$record['end']            = empty( $recurrence_data['end'] ) ? null : $recurrence_data['end'];
			$record['end-count']      = empty( $recurrence_data['end-count'] ) ? null : $recurrence_data['end-count'];

			$record['custom']             = array();
			$record['custom']['type']     = empty( $recurrence_data['custom-type'] ) ? null : $recurrence_data['custom-type'];
			$record['custom']['interval'] = empty( $recurrence_data['custom-interval'] ) ? null : $recurrence_data['custom-interval'];

			$record['custom']['same-time'] = 'yes';
			$record['custom']['day']['same-time'] = 'yes';

			$record['custom']['week']              = array();
			$record['custom']['week']['day']       = empty( $recurrence_data['custom-week-day'] ) ? null : $recurrence_data['custom-week-day'];
			$record['custom']['week']['same-time'] = 'yes';

			$record['custom']['month']              = array();
			$record['custom']['month']['number']    = empty( $recurrence_data['custom-month-number'] ) ? null : $recurrence_data['custom-month-number'];
			$record['custom']['month']['day']       = empty( $recurrence_data['custom-month-day'] ) ? null : $recurrence_data['custom-month-day'];
			$record['custom']['month']['same-time'] = 'yes';

			$record['custom']['year']              = array();
			$record['custom']['year']['month']     = empty( $recurrence_data['custom-year-month'] ) ? null : $recurrence_data['custom-year-month'];
			$record['custom']['year']['filter']    = empty( $recurrence_data['custom-year-filter'] ) ? null : $recurrence_data['custom-year-filter'];
			$record['custom']['year']['number']    = empty( $recurrence_data['custom-year-month-number'] ) ? null : Tribe__Events__Pro__Recurrence__Series_Rules_Factory::intToOrdinal( $recurrence_data['custom-year-month-number'] );
			$record['custom']['year']['day']       = empty( $recurrence_data['custom-year-month-day'] ) ? null : $recurrence_data['custom-year-month-day'];
			$record['custom']['year']['same-time'] = 'yes';

			$custom_type_key = null;
			if (
				'Custom' === $record['type']
				&& ! empty( $record['custom']['type'] )
			) {
				$custom_type_key = $utils->to_key( $record['custom']['type'] );
			}

			if (
				$custom_type_key
				&& ! empty( $record['custom'][ $custom_type_key ] )
				&& ! empty( $record['custom'][ $custom_type_key ]['same-time'] )
			) {
				$record['custom']['same-time'] = $record['custom'][ $custom_type_key ]['same-time'];
			}

			$recurrence_meta['rules'][] = $record;

			if ( ! empty( $recurrence_data['excluded-dates'] ) ) {
				foreach ( (array) $recurrence_data['excluded-dates'] as $date ) {
					self::add_date_exclusion_to_recurrence( $recurrence_meta, $date );
				}
			}

			if ( ! empty( $recurrence_data['recurrence-description'] ) ) {
				$recurrence_meta['description'] = $recurrence_data['recurrence-description'];
			}
		}

		return apply_filters( 'tribe_pro_legacy_recurrence_meta', $recurrence_meta );
	}

	/**
	 * Clean up meta array by providing defaults.
	 *
	 * @param  array $meta
	 *
	 * @return array of $meta merged with defaults
	 */
	protected static function recurrenceMetaDefault( $meta = array() ) {
		$default_meta = array(
			'rules'      => array(),
			'exclusions' => array(),
		);

		if ( $meta ) {
			if ( isset( $meta['rules'] ) ) {
				return $meta;
			} else {
				$default_meta['rules'][] = $meta;
			}
		}

		return $default_meta;
	}

	/**
	 * Recurrence validation method.  This is checked after saving an event, but before splitting a series out into multiple occurrences
	 *
	 * @param int   $event_id        The event object that is being saved
	 * @param array $recurrence_meta Recurrence information for this event
	 *
	 * @return bool
	 */
	public static function isRecurrenceValid( $event_id, $recurrence_meta ) {
		$valid    = true;
		$errorMsg = '';

		if ( isset( $recurrence_meta['type'] ) && 'Custom' === $recurrence_meta['type'] ) {
			if ( ! isset( $recurrence_meta['custom']['type'] ) ) {
				$valid    = false;
				$errorMsg = __( 'Custom recurrences must have a type selected.', 'tribe-events-calendar-pro' );
			} elseif (
				! isset( $recurrence_meta['custom']['start-time'] )
				&& ! isset( $recurrence_meta['custom']['day'] )
				&& ! isset( $recurrence_meta['custom']['week'] )
				&& ! isset( $recurrence_meta['custom']['month'] )
				&& ! isset( $recurrence_meta['custom']['year'] )
			) {
				$valid    = false;
				$errorMsg = __( 'Custom recurrences must have all data present.', 'tribe-events-calendar-pro' );
			} elseif (
				'Monthly' === $recurrence_meta['custom']['type']
				&& (
					empty( $recurrence_meta['custom']['month']['day'] )
					|| empty( $recurrence_meta['custom']['month']['number'] )
					|| '-' === $recurrence_meta['custom']['month']['day']
					|| '' === $recurrence_meta['custom']['month']['number']
				)
			) {
				$valid    = false;
				$errorMsg = __( 'Monthly custom recurrences cannot have a dash set as the day to occur on.', 'tribe-events-calendar-pro' );
			} elseif (
				'Yearly' === $recurrence_meta['custom']['type']
				&& (
					empty( $recurrence_meta['custom']['year']['day'] )
					|| '-' === $recurrence_meta['custom']['year']['day']
				)
			) {
				$valid    = false;
				$errorMsg = __( 'Yearly custom recurrences cannot have a dash set as the day to occur on.', 'tribe-events-calendar-pro' );
			}
		}

		if ( ! $valid ) {
			do_action( 'tribe_recurring_event_error', $event_id, $errorMsg );
		}

		return $valid;
	}

	/**
	 * Fetches child event IDs
	 *
	 * @param int
	 * $post_id Post ID
	 * @param array $args    Array of arguments for get_posts
	 *
	 * @deprecated 4.0.1
	 */
	public static function get_child_event_ids( $post_id, $args = array() ) {
		_deprecated_function( __METHOD__, '4.0.1', 'Tribe__Events__Pro__Recurrence__Meta::children()->get_ids()' );

		return self::children()->get_ids( $post_id, $args );
	}

	public static function get_events_by_slug( $slug ) {
		$cache   = new Tribe__Cache();
		$all_ids = $cache->get( 'events_by_slug_' . $slug, 'save_post' );
		if ( is_array( $all_ids ) ) {
			return $all_ids;
		}
		/** @var wpdb $wpdb */
		global $wpdb;
		$parent_sql = "SELECT ID FROM {$wpdb->posts} WHERE post_name=%s AND post_type=%s";
		$parent_sql = $wpdb->prepare( $parent_sql, $slug, Tribe__Events__Main::POSTTYPE );
		$parent_id  = $wpdb->get_var( $parent_sql );
		if ( empty( $parent_id ) ) {
			return array();
		}
		$children_sql = "SELECT ID FROM {$wpdb->posts} WHERE ID=%d OR post_parent=%d AND post_type=%s";
		$children_sql = $wpdb->prepare( $children_sql, $parent_id, $parent_id, Tribe__Events__Main::POSTTYPE );
		$all_ids      = $wpdb->get_col( $children_sql );

		if ( empty( $all_ids ) ) {
			return array();
		}

		$cache->set( 'events_by_slug_' . $slug, $all_ids, Tribe__Cache::NO_EXPIRATION, 'save_post' );

		return $all_ids;
	}

	/**
	 * Get the start dates of all instances of the event,
	 * in ascending order
	 *
	 * @param int $request_post The post ID of the requested post, this might not be the Series parent, but the ID
	 *                          of any post in the Series.
	 *
	 * @return array Start times, as Y-m-d H:i:s
	 */
	public static function get_start_dates( $request_post ) {
		if ( empty( $request_post ) ) {
			return array();
		}

		// We get the parent first to make sure we leverage the cache correctly, that is set for the series parent.
		/** @var wpdb $wpdb */
		global $wpdb;
		$ancestors = get_post_ancestors( $request_post );
		$post_id   = empty( $ancestors ) ? $request_post : end( $ancestors );

		$cache = tribe( 'cache' );
		$dates = $cache->get( 'event_dates_' . $post_id, 'save_post' );
		if ( is_array( $dates ) ) {
			return $dates;
		}

		$sql       = "
			SELECT     meta_value
			FROM       {$wpdb->postmeta} m
			INNER JOIN {$wpdb->posts} p ON p.ID=m.post_id
			           AND ( p.post_parent=%d OR p.ID=%d )

			WHERE meta_key = '_EventStartDate'
			      AND post_type = %s
			      AND post_status NOT IN ( 'inherit', 'auto-draft', 'trash' )

			ORDER BY meta_value ASC
		";


		$sql       = $wpdb->prepare( $sql, $post_id, $post_id, Tribe__Events__Main::POSTTYPE );
		$result    = $wpdb->get_col( $sql );
		$cache->set( 'event_dates_' . $post_id, $result, Tribe__Cache::NO_EXPIRATION, 'save_post' );

		return $result;
	}

	/**
	 * Deletes events when a change in recurrence pattern renders them obsolete.
	 *
	 * This should not be used when removing individual instances from an otherwise unchanged
	 * pattern - wp_delete_post() can be used directly to facilitate that.
	 *
	 * This method takes care of temporarily unhooking our 'before_delete_post' callback
	 * to avoid the deleted instance being added to the exclusion list.
	 *
	 * @param $instance_id
	 * @param $start_date
	 */
	public static function delete_unexcluded_event( $instance_id, $start_date ) {
		do_action( 'tribe_events_deleting_child_post', $instance_id, $start_date );
		remove_action( 'before_delete_post', array( __CLASS__, 'handle_delete_request' ) );
		wp_delete_post( $instance_id, true );
		add_action( 'before_delete_post', array( __CLASS__, 'handle_delete_request' ) );
	}

	public static function save_pending_events( $event_id ) {
		if ( wp_get_post_parent_id( $event_id ) != 0 ) {
			return;
		}
		$next_pending = get_post_meta( $event_id, '_EventNextPendingRecurrence', true );
		if ( empty( $next_pending ) ) {
			return;
		}

		$latest_date = strtotime( self::$scheduler->get_latest_date() );

		$recurrences = self::get_recurrence_for_event( $event_id );
		$exclusions = self::get_excluded_dates( $recurrences, strtotime( $next_pending ), $latest_date );
		$event_timezone_string = Tribe__Events__Timezones::get_event_timezone_string( $event_id );
		$exclusions_obj = Tribe__Events__Pro__Recurrence__Exclusions::instance( $event_timezone_string );

		/** @var Tribe__Events__Pro__Recurrence $recurrence */
		foreach ( $recurrences['rules'] as &$recurrence ) {
			$recurrence->setMinDate( strtotime( $next_pending ) );
			$recurrence->setMaxDate( $latest_date );
			$dates = (array) $recurrence->getDates();

			if ( empty( $dates ) ) {
				return; // nothing to add right now. try again later
			}

			$sequence = new Tribe__Events__Pro__Recurrence__Sequence( $dates, $event_id );

			delete_post_meta( $event_id, '_EventNextPendingRecurrence' );

			if ( $recurrence->constrainedByMaxDate() !== false ) {
				update_post_meta( $event_id, '_EventNextPendingRecurrence',
					date( Tribe__Events__Pro__Date_Series_Rules__Rules_Interface::DATE_FORMAT, $recurrence->constrainedByMaxDate() ) );
			}

			$to_create = $exclusions_obj->remove_exclusions( $sequence->get_sorted_sequence(), $exclusions );

			foreach ( $to_create as $date_duration ) {
				$instance = new Tribe__Events__Pro__Recurrence__Instance( $event_id, $date_duration, 0, $date_duration['sequence'] );
				$instance->save();
			}
		}

	}

	/**
	 * Get Excluded Dates for New Events in Series
	 *
	 * @since 4.4.20
	 *
	 * @param $recurrences object a recurrence object
	 * @param $earliest_date int a unix timestamp
	 * @param $latest_date int a unix timestamp
	 *
	 * @return array
	 */
	private static function get_excluded_dates( $recurrences, $earliest_date, $latest_date ) {

		if ( empty( $recurrences['exclusions'] ) ) {
			return array();
		}

		// find days we should exclude
		$exclusions = array();
		foreach ( $recurrences['exclusions'] as &$recurrence ) {
			if ( ! $recurrence ) {
				continue;
			}

			$recurrence->setMinDate( $earliest_date );
			$recurrence->setMaxDate( $latest_date );

			$exclusions = array_merge( $exclusions, $recurrence->getDates() );
		}

		return tribe_array_unique( $exclusions );

	}

	public static function get_recurrence_for_event( $event_id ) {
		$recurrence_meta = self::getRecurrenceMeta( $event_id );

		$recurrences = array(
			'rules'      => array(),
			'exclusions' => array(),
		);

		// Used in generating all-day durations below.
		$parent_start_date = strtotime( get_post_meta( $event_id, '_EventStartDate', true ) );
		$parent_end_date   = strtotime( get_post_meta( $event_id, '_EventEndDate', true ) );

		if ( ! $recurrence_meta['rules'] ) {
			$recurrences[] = new Tribe__Events__Pro__Null_Recurrence();

			return $recurrences;
		}

		foreach ( array( 'rules', 'exclusions' ) as $rule_type ) {
			foreach ( $recurrence_meta[ $rule_type ] as &$recurrence ) {
				$rule = Tribe__Events__Pro__Recurrence__Series_Rules_Factory::instance()->build_from( $recurrence, $rule_type );

				// recurrence meta entry might be malformed
				if ( is_wp_error( $rule ) ) {
					// let's not process it and let's not try to fix it as it might be a third-party modification
					tribe( 'logger' )->log_debug( "Broken recurrence data detected for event #{$event_id}", __CLASS__ );
					continue;
				}

				$custom_type = 'none';
				$start_time  = null;
				$duration    = (int) get_post_meta( $event_id, '_EventDuration', true );

				if ( isset( $recurrence['custom']['type'] ) ) {
					$custom_type = Tribe__Events__Pro__Recurrence__Custom_Types::to_key( $recurrence['custom']['type'] );
				}

				if (
					empty( $recurrence['custom'][ $custom_type ]['same-time'] )
					&& isset( $recurrence['custom']['start-time'] )
					&& isset( $recurrence['custom']['duration'] )
				) {
					$start_time = "{$recurrence['custom']['start-time']['hour']}:{$recurrence['custom']['start-time']['minute']}:00";
					$start_time .= isset( $recurrence['custom']['start-time']['meridian'] ) ? " {$recurrence['custom']['start-time']['meridian']}" : '';
					$duration = self::get_duration_in_seconds( $recurrence['custom']['duration'] );
				} elseif (
					isset( $recurrence['custom']['start-time'] )
					&& ! is_array( $recurrence['custom']['start-time'] )
					&& isset( $recurrence['custom']['end-time'] )
					&& isset( $recurrence['custom']['end-day'] )
				) {
					$start_time_in_seconds = strtotime( $recurrence['custom']['start-time'] );
					$start_time = date( 'H:i:s', $start_time_in_seconds );

					$end_time_in_seconds = strtotime( $recurrence['custom']['end-time'] );
					$end_time = date( 'H:i:s', $end_time_in_seconds );

					$duration = $end_time_in_seconds - $start_time_in_seconds;

					if ( is_numeric( $recurrence['custom']['end-day'] ) ) {
						$duration += $recurrence['custom']['end-day'] * DAY_IN_SECONDS;
					}
				}

				// Look out for instances inheriting the same time as the parent, where the parent is an all day event
				if (
					isset( $recurrence['custom']['same-time'] )
					&& 'yes' === $recurrence['custom']['same-time']
					&& tribe_event_is_all_day( $event_id )
				) {
					// In the current implementation, events are considered "all day"
					// when their length is 1 second short of being 24hrs in duration.
					// We need to factor this in as follows to preserve duration
					// of multiday all-day events
					$diff       = $parent_end_date - $parent_start_date;
					$num_days   = absint( $diff / DAY_IN_SECONDS ) + 1;
					$duration   = ( $num_days * DAY_IN_SECONDS ) - 1;
				}

				$start = strtotime( get_post_meta( $event_id, '_EventStartDate', true ) . '+00:00' );

				$is_after = false;

				if ( isset( $recurrence['end-type'] ) ) {
					switch ( $recurrence['end-type'] ) {
						case 'On':
							$end = strtotime( tribe_end_of_day( $recurrence['end'] ) );
							break;
						case 'Never':
							$end = Tribe__Events__Pro__Recurrence::NO_END;
							break;
						case 'After':
						default:
							$end      = $recurrence['end-count'] - 1; // subtract one because event is first occurrence
							$is_after = true;
							break;
					}
				} else {
					$end = Tribe__Events__Pro__Recurrence::NO_END;
				}

				$recurrences[ $rule_type ][] = new Tribe__Events__Pro__Recurrence( $start, $end, $rule, $is_after, get_post( $event_id ), $start_time, $duration );
			}
		}

		return $recurrences;
	}

	/**
	 * Returns the total number of seconds represented by an array of three integers
	 * (with the keys "days", "hours" and "seconds" - representing those units of time
	 * respectively).
	 *
	 * @param array $duration
	 *
	 * @return int
	 */
	public static function get_duration_in_seconds( array $duration ) {
		$total = 0;

		$expected = array(
			'days'    => 0,
			'hours'   => 0,
			'minutes' => 0
		);

		$duration = array_merge( $expected, $duration );

		if ( is_numeric( $duration['days'] ) ) {
			$total += absint( $duration['days'] ) * DAY_IN_SECONDS;
		}
		if ( is_numeric( $duration['hours'] ) ) {
			$total += absint( $duration['hours'] ) * HOUR_IN_SECONDS;
		}
		if ( is_numeric( $duration['minutes'] ) ) {
			$total += absint( $duration['minutes'] ) * MINUTE_IN_SECONDS;
		}

		return $total;
	}

	/**
	 * Decide which rule set to use for finding all the dates in an event series
	 *
	 * @todo fix this method!
	 *       - currently returns an array rather than the type declared by the return doc tag
	 *       - always returns an empty array, the result of build_from() isn't actually used
	 *
	 * @param array $postId The event to find the series for
	 *
	 * @return Tribe__Events__Pro__Date_Series_Rules__Rules_Interface
	 */
	public static function getSeriesRules( $postId ) {
		$recurrence_meta = self::getRecurrenceMeta( $postId );
		$rules           = array();

		foreach ( $recurrence_meta['rules'] as &$recurrence ) {
			$rule = Tribe__Events__Pro__Recurrence__Series_Rules_Factory::instance()->build_from( $recurrence );
		}//end foreach

		return $rules;
	}

	/**
	 * Get the recurrence pattern in text format by post id.
	 *
	 * @param int $postId The post id.
	 *
	 * @return string The human readable string.
	 */
	public static function recurrenceToTextByPost( $postId = null ) {
		if ( $postId == null ) {
			global $post;
			$postId = $post->ID;
		}

		$recurrence_rules = self::getRecurrenceMeta( $postId );
		$start_date       = Tribe__Events__Main::get_series_start_date( $postId );

		$output_text = array();

		// if there is a description override for recurrences, return that instead of building it dynamically
		if ( ! empty( $recurrence_rules['description'] ) ) {
			return $recurrence_rules['description'];
		}

		foreach ( $recurrence_rules['rules'] as $rule ) {
			$output_text[] = '<p>' . self::recurrenceToText( $rule, $start_date, $postId ) . '</p>';
		}

		return implode( '', $output_text );
	}

	/**
	 * Build possible date-specific strings for recurrence
	 *
	 * @return array
	 */
	public static function date_strings() {
		$strings = array(
			'weekdays'          => array(
				__( 'Monday' ),
				__( 'Tuesday' ),
				__( 'Wednesday' ),
				__( 'Thursday' ),
				__( 'Friday' ),
				__( 'Saturday' ),
				__( 'Sunday' ),
				_x( 'day', 'placeholder used where the true day of the day is determined dynamically', 'tribe-events-calendar-pro' ),
			),
			'months'            => array(
				__( 'January' ),
				__( 'February' ),
				__( 'March' ),
				__( 'April' ),
				__( 'May' ),
				__( 'June' ),
				__( 'July' ),
				__( 'August' ),
				__( 'September' ),
				__( 'October' ),
				__( 'November' ),
				__( 'December' ),
			),
			'time_spans' => array(
				'day'               => _x( 'day', 'Used when displaying the word "day" in "the last day" or "the first day"', 'tribe-events-calendar-pro' ),
				'days'              => _x( 'days', 'Used when displaying the word "days" in e.g. "every 3 days"', 'tribe-events-calendar-pro' ),
				'week'              => _x( 'week', 'Used when displaying the word "week" in "the last week" or "the first week"', 'tribe-events-calendar-pro' ),
				'weeks'             => _x( 'weeks', 'Used when displaying the word "weeks" in e.g. "every 3 weeks"', 'tribe-events-calendar-pro' ),
				'month'             => _x( 'month', 'Used when displaying the word "month" in e.g. "every month"', 'tribe-events-calendar-pro' ),
				'months'            => _x( 'months', 'Used when displaying the word "months" in e.g. "every 3 months"', 'tribe-events-calendar-pro' ),
				'year'              => _x( 'year', 'Used when displaying the word "year" in e.g. "every year"', 'tribe-events-calendar-pro' ),
				'years'             => _x( 'years', 'Used when displaying the word "years" in e.g. "every 2 years"', 'tribe-events-calendar-pro' ),
			),
			'collection_joiner' => _x( 'and', 'Joins the last item in a list of items (i.e. the "and" in Monday, Tuesday, and Wednesday)', 'tribe-events-calendar-pro' ),
			'day_placeholder'   => _x( '[day]', 'Placeholder text for a day of the week (or days of the week) before the user has selected any', 'tribe-events-calendar-pro' ),
			'month_placeholder' => _x( '[month]', 'Placeholder text for a month (or months) before the user has selected any', 'tribe-events-calendar-pro' ),
			'day_of_month'      => _x( 'day %1$s', 'Describes a day of the month (e.g. "day 5" or "day 27")', 'tribe-events-calendar-pro' ),
			'first_x'           => _x( 'the first %1$s', 'Used when displaying: "the first Monday" or "the first day"', 'tribe-events-calendar-pro' ),
			'second_x'          => _x( 'the second %1$s', 'Used when displaying: "the second Monday" or "the second day"', 'tribe-events-calendar-pro' ),
			'third_x'           => _x( 'the third %1$s', 'Used when displaying: "the third Monday" or "the third day"', 'tribe-events-calendar-pro' ),
			'fourth_x'          => _x( 'the fourth %1$s', 'Used when displaying: "the fourth Monday" or "the fourth day"', 'tribe-events-calendar-pro' ),
			'fifth_x'           => _x( 'the fifth %1$s', 'Used when displaying: "the fifth Monday" or "the fifth day"', 'tribe-events-calendar-pro' ),
			'last_x'            => _x( 'the last %1$s', 'Used when displaying: "the last Monday" or "the last day"', 'tribe-events-calendar-pro' ),
			'day'               => _x( 'day', 'Used when displaying the word "day" in "the last day" or "the first day"', 'tribe-events-calendar-pro' ),
		);

		return $strings;
	}

	/**
	 * Convert the event recurrence meta into a human readable string
	 *
	 * @TODO: there's a great deal of duplication between this method and tribe_events_pro_admin.recurrence.update_rule_recurrence_text (events-recurrence.js)
	 *        let's consider generating once (by JS?) and saving the result for re-use instead
	 *
	 * @param array  $rule
	 * @param string $start_date
	 * @param int    $event_id
	 *
	 * @return string human readable string
	 */
	public static function recurrenceToText( $rule, $start_date, $event_id ) {
		$text               = '';
		$recurrence_strings = Tribe__Events__Pro__Recurrence__Strings::recurrence_strings();
		$date_strings       = self::date_strings();

		$interval         = 1;
		$is_custom        = false;
		$same_time        = true;
		$same_day         = true;
		$year_filtered    = false;
		$rule['type']     = str_replace( ' ', '-', strtolower( $rule['type'] ) );

		// The end-type may not be set in the cast of single-date recurrence rules, so default to an "on"-this-date type
		$rule['end-type'] = isset( $rule['end-type'] ) ? str_replace( ' ', '-', strtolower( $rule['end-type'] ) ) : 'on';
		$count = isset( $rule['end-count'] ) ? intval( $rule['end-count'] ) : 0;

		$type = 'custom' == $rule['type'] ? strtolower( $rule['custom']['type'] ) : $rule['type'];

		$start_date = strtotime( tribe_get_start_date( $event_id, true, Tribe__Date_Utils::DBDATETIMEFORMAT ) );
		$end_date   = strtotime( tribe_get_end_date( $event_id, true, Tribe__Date_Utils::DBDATETIMEFORMAT ) );

		// if the type is "none", then there's no rules to parse
		if ( 'none' === $rule['type'] ) {
			return;
		}

		// If there isn't an end date, then let's assume this means never - unless it is a single date
		// rule in which case 'never' doesn't make sense
		if (
			'on' === $rule['end-type']
			&& empty( $rule['end'] )
			&& 'date' !== $type
		) {
			$rule['end'] = 'never';
			$rule['end-type'] = 'never';
		}

		// Gets the interval, defaulting to 1
		$interval = intval( Tribe__Utils__Array::get( $rule, array( 'custom', 'interval' ), $interval ) );

		$start_date = strtotime( tribe_get_start_date( $event_id, true, Tribe__Date_Utils::DBDATETIMEFORMAT ) );
		$end_date   = strtotime( tribe_get_end_date( $event_id, true, Tribe__Date_Utils::DBDATETIMEFORMAT ) );

		// check if the recurring event time is different than the main event
		if ( isset( $rule['custom'] ) && isset( $rule['same-time'] ) && 'no' === $rule['same-time'] ) {
			$same_time = false;

			$new_start_date = date( 'Y-m-d', $start_date );
			// check if we have a legacy start format stored, 4.3 and older
			if ( is_array( $rule['custom']['start-time'] ) ) {
				$new_start_date .= ' ' . $rule['custom']['start-time']['hour'] . ':' . $rule['custom']['start-time']['minute'];

				if ( isset( $rule['custom']['start-time']['meridian'] ) ) {
					$new_start_date .= ' ' . $rule['custom']['start-time']['meridian'];
				}
			} else {
				$new_start_date = ' ' . $rule['custom']['start-time'];
			}
			$start_date = strtotime( $new_start_date );

			// legacy use of duration, 4.3 and older
			if ( isset( $rule['custom']['duration'] ) ) {
				try {
					$end_date = new DateTime( '@' . $start_date );

					$end_date->modify( '+' . absint( $rule['custom']['duration']['days'] ) . ' days' );
					$end_date->modify( '+' . absint( $rule['custom']['duration']['hours'] ) . ' hours' );
					$end_date->modify( '+' . absint( $rule['custom']['duration']['minutes'] ) . ' minutes' );

					$end_date = $end_date->format( 'U' );
				} catch ( Exception $e ) {
					// $formatted_end will default to the "unspecified end date" text in this case
				}
			} else {
				// @todo - this does not currently support end times on a different day
				//         events that span days are not currently functional, so, will follow up here when that is fixed
				$end_date = strtotime( $start_date . ' ' .  $rule['custom']['end-time'] );
			}
		}

		$formatted_start = date( 'Y-m-d H:i:s', $start_date );

		if (
			isset( $rule['custom']['same-time'] )
			&& 'no' === $rule['custom']['same-time']
		) {
			$start_time = date( get_option( 'time_format' ), strtotime( $rule['custom']['start-time'] ) );
		} else {
			$start_time = date( get_option( 'time_format' ), $start_date );
		}

		$days_of_week = null;
		$month_names = null;
		$month_day_description = null;
		$day_number = date( 'j', $start_date );
		$month_number = $day_number;

		$key = $type;

		if ( 'weekly' === $type ) {
			$weekdays = array();

			foreach ( $rule['custom']['week']['day'] as $day ) {
				$weekdays[] = $date_strings['weekdays'][ $day - 1 ];
			}

			if ( empty( $weekdays ) ) {
				$days_of_week = $date_strings['day_placeholder'];
			} elseif ( 2 >= count( $weekdays ) ) {
				$days_of_week = implode( " {$date_strings['collection_joiner']} ", $weekdays );
			} else {
				$days_of_week = implode( ', ', $weekdays );
				$days_of_week = preg_replace( '/(.*),/', '$1 ' . $date_strings['collection_joiner'], $days_of_week );
			}
		} elseif ( 'monthly' === $type ) {
			$same_day     = empty( $rule['custom']['month']['same-day'] ) || 'yes' === $rule['custom']['month']['same-day'];
			$month_number = Tribe__Utils__Array::get( $rule, array( 'custom', 'month', 'number' ), $month_number );
			$month_day    = Tribe__Utils__Array::get( $rule, array( 'custom', 'month', 'day' ), $day_number );

			// need to check month number is a number
			if ( ( $same_day && 1 == $interval ) || $month_number ) {
				$key .= '-numeric';
			} else {
				$day_number = $month_number;
				$month_day_description = '??????';
			}
		} elseif ( 'yearly' === $type ) {
			// Select saves on a single field, and we have to explode it into an array for manipulation
			if ( isset( $rule['custom']['year']['month'] ) ) {

				if ( ! is_array( $rule['custom']['year']['month'] ) ) {
					$rule['custom']['year']['month'] = (array) explode( ',', $rule['custom']['year']['month'] );
				}

				$rule['custom']['year']['month'] = array_map( 'intval', $rule['custom']['year']['month'] );
			}

			// this variable represents what relative day of the month
			$month_number = isset( $rule['custom']['year']['number'] ) ? strtolower( $rule['custom']['year']['number'] ) : $month_number;
			$same_day     = ! isset( $rule['custom']['year']['filter'] ) || '1' === $rule['custom']['year']['filter'];

			if ( ! empty( $rule['custom']['year']['month'] ) ) {
				foreach ( $rule['custom']['year']['month'] as $month ) {
					$months[] = $date_strings['months'][ $month - 1 ];
				}
			}

			// build a string of month names
			if ( ! $months ) {
				$month_names = $date_strings['month_placeholder'];
			} elseif ( 2 >= count( $months ) ) {
				$month_names = implode( " {$date_strings['collection_joiner']} ", $months );
			} else {
				$month_names = implode( ', ', $months );
				$month_names = preg_replace( '/(.*),/', '$1 ' . $date_strings['collection_joiner'], $month_names );
			}

			$year_day_of_month_numeric = false;
			if ( $same_day && 1 == count( $months ) ) {
				$start_month = date( 'n', $start_date );

				if ( $start_month == $rule['custom']['year']['month'][0] ) {
					$key .= '-numeric';
					$year_day_of_month_numeric = true;
				}
			}

			if ( is_numeric( $month_number ) || $year_day_of_month_numeric ) {
				$day_number = $month_number;
			} else {
				$month_day = $rule['custom']['year']['day'];

				$month_day_description = $date_strings[ $month_number . '_x' ];

				if ( is_numeric( $month_day ) && $month_day > 0 ) {
					$month_day = intval( $month_day );
					$month_day_description = str_replace( '%1$s', $date_strings['weekdays'][ $month_day - 1 ], $month_day_description );
				} elseif ( is_numeric( $month_day ) ) {
					$month_day_description = str_replace( '%1$s', $date_strings['day'], $month_day_description );
				} else {
					$month_day_description = str_replace( '%1$s', $date_strings['day_placeholder'], $month_day_description );
				}
			}
		}

		$key .= '-' . $rule['end-type'];
		$text = '';

		/**
		 * Makes sure legacy Recurrence works as expected
		 * @see C#71907
		 */
		if ( ! empty( $recurrence_strings[ $key ] ) ) {
			$text = $recurrence_strings[ $key ];
		}

		if ( empty( $text ) ) {
			return;
		}

		// Handle different string conversions based on recurring end type.
		if ( empty( $rule['end'] ) ) {

			// If the events are single events, use the dates of those single instances.
			if ( 'date' === $type && isset( $rule['custom']['date']['date'] ) ) {
				$series_end = date_i18n( tribe_get_date_format( true ), strtotime( $rule['custom']['date']['date'] ) );

			// Otherwise there's no end date specified.
			} else {
				$series_end = _x( 'an unspecified date', 'An unspecified end date', 'tribe-events-calendar-pro' );
			}
		} else {
			$series_end = date_i18n( tribe_get_date_format( true ), strtotime( $rule['end'] ) );
		}

		$text = str_replace( array(
				'[count]',
				'[day_number]',
				'[days_of_week]',
				'[month_day_description]',
				'[month_names]',
				'[month_number]',
				'[interval]',
				'[series_end_date]',
				'[start_date]',
				'[start_time]',
				'[single_date]',
				// English-only simplification,
				'1 day(s)',
				'1 week(s)',
				'1 month(s)',
				'1 year(s)',
				'day(s)',
				'week(s)',
				'month(s)',
				'year(s)',
			),
			array(
				$count,
				$day_number,
				$days_of_week,
				$month_day_description,
				$month_names,
				$month_number,
				$interval,
				$series_end,
				$formatted_start,
				$start_time,
				$series_end,
				// English-only simplification,
				'day',
				'week',
				'month',
				'year',
				'days',
				'weeks',
				'months',
				'years',
			),
			$text
		);

		return $text;
	}

	public static function init_non_core() {
	}

	/**
	 * Convert an array of day ids into a human readable string
	 *
	 * @param array $days The day ids
	 *
	 * @return The human readable string
	 */
	private static function daysToText( $days ) {
		$day_words = array(
			__( 'Monday', 'tribe-events-calendar-pro' ),
			__( 'Tuesday', 'tribe-events-calendar-pro' ),
			__( 'Wednesday', 'tribe-events-calendar-pro' ),
			__( 'Thursday', 'tribe-events-calendar-pro' ),
			__( 'Friday', 'tribe-events-calendar-pro' ),
			__( 'Saturday', 'tribe-events-calendar-pro' ),
			__( 'Sunday', 'tribe-events-calendar-pro' ),
		);
		$count     = count( $days );
		$day_text  = '';

		for ( $i = 0; $i < $count; $i ++ ) {
			if ( $count > 2 && $i == $count - 1 ) {
				$day_text .= __( ', and', 'tribe-events-calendar-pro' ) . ' ';
			} elseif ( $count == 2 && $i == $count - 1 ) {
				$day_text .= ' ' . __( 'and', 'tribe-events-calendar-pro' ) . ' ';
			} elseif ( $count > 2 && $i > 0 ) {
				$day_text .= __( ',', 'tribe-events-calendar-pro' ) . ' ';
			}

			$day_text .= $day_words[ $days[ $i ] - 1 ] ? $day_words[ $days[ $i ] - 1 ] : 'day';
		}

		return $day_text;
	}

	/**
	 * Convert an array of month ids into a human readable string
	 *
	 * @param array $months The month ids
	 *
	 * @return The human readable string
	 */
	private static function monthsToText( $months ) {
		$month_words = array(
			__( 'January', 'tribe-events-calendar-pro' ),
			__( 'February', 'tribe-events-calendar-pro' ),
			__( 'March', 'tribe-events-calendar-pro' ),
			__( 'April', 'tribe-events-calendar-pro' ),
			__( 'May', 'tribe-events-calendar-pro' ),
			__( 'June', 'tribe-events-calendar-pro' ),
			__( 'July', 'tribe-events-calendar-pro' ),
			__( 'August', 'tribe-events-calendar-pro' ),
			__( 'September', 'tribe-events-calendar-pro' ),
			__( 'October', 'tribe-events-calendar-pro' ),
			__( 'November', 'tribe-events-calendar-pro' ),
			__( 'December', 'tribe-events-calendar-pro' ),
		);
		$count       = count( $months );
		$month_text  = '';

		for ( $i = 0; $i < $count; $i ++ ) {
			if ( $count > 2 && $i == $count - 1 ) {
				$month_text .= __( ', and ', 'tribe-events-calendar-pro' );
			} elseif ( $count == 2 && $i == $count - 1 ) {
				$month_text .= __( ' and ', 'tribe-events-calendar-pro' );
			} elseif ( $count > 2 && $i > 0 ) {
				$month_text .= __( ', ', 'tribe-events-calendar-pro' );
			}

			$month_text .= $month_words[ $months[ $i ] - 1 ];
		}

		return $month_text;
	}

	/**
	 * Adds setting for hiding subsequent occurrences by default.
	 *
	 *
	 * @param array  $args
	 * @param string $id
	 *
	 * @return array
	 */
	public static function inject_settings( $args, $id ) {

		if ( $id == 'general' ) {

			// we want to inject the hiding subsequent occurrences into the general section directly after "Live update AJAX"
			$args = Tribe__Main::array_insert_after_key( 'postsPerPage', $args, array(
				'hideSubsequentRecurrencesDefault' => array(
					'type'            => 'checkbox_bool',
					'label'           => __( 'Recurring event instances', 'tribe-events-calendar-pro' ),
					'tooltip'         => __( 'Show only the first instance of each recurring event (only affects list-style views).', 'tribe-events-calendar-pro' ),
					'default'         => false,
					'validation_type' => 'boolean',
				),
				'userToggleSubsequentRecurrences'  => array(
					'type'            => 'checkbox_bool',
					'label'           => __( 'Front-end recurring event instances toggle', 'tribe-events-calendar-pro' ),
					'tooltip'         => __( 'Allow users to decide whether to show all instances of a recurring event on list-style views.', 'tribe-events-calendar-pro' ),
					'default'         => false,
					'validation_type' => 'boolean',
				),
				'recurrenceMaxMonthsBefore'        => array(
					'type'            => 'text',
					'size'            => 'small',
					'label'           => __( 'Clean up recurring events after', 'tribe-events-calendar-pro' ),
					'tooltip'         => __( 'Automatically remove recurring event instances older than this', 'tribe-events-calendar-pro' ),
					'validation_type' => 'positive_int',
					'default'         => 24,
				),
				'recurrenceMaxMonthsAfter'         => array(
					'type'            => 'text',
					'size'            => 'small',
					'label'           => __( 'Create recurring events in advance for', 'tribe-events-calendar-pro' ),
					'tooltip'         => __( 'Recurring events will be created this far in advance', 'tribe-events-calendar-pro' ),
					'validation_type' => 'positive_int',
					'default'         => 24,
				),
			) );
			add_filter( 'tribe_field_div_end', array( __CLASS__, 'add_months_to_settings_field' ), 100, 2 );

		}


		return $args;
	}

	/**
	 * @param string       $html
	 * @param Tribe__Field $field
	 *
	 * @return string
	 */
	public static function add_months_to_settings_field( $html, $field ) {
		if ( in_array( $field->name, array( 'recurrenceMaxMonthsBefore', 'recurrenceMaxMonthsAfter' ) ) ) {
			$html = __( ' months', 'tribe-events-calendar-pro' ) . $html;
		}

		return $html;
	}

	/**
	 * Combines the ['post'] piece of the $_REQUEST variable so it only has unique post ids.
	 *
	 *
	 * @return void
	 */
	public static function combineRecurringRequestIds() {
		if ( isset( $_REQUEST['post_type'] ) && $_REQUEST['post_type'] == Tribe__Events__Main::POSTTYPE && ! empty( $_REQUEST['post'] ) && is_array( $_REQUEST['post'] ) ) {
			$_REQUEST['post'] = array_unique( $_REQUEST['post'] );
		}
	}

	/**
	 * @return void
	 */
	public static function reset_scheduler() {
		if ( ! empty( self::$scheduler ) ) {
			self::$scheduler->remove_hooks();
		}
		self::$scheduler = new Tribe__Events__Pro__Recurrence__Scheduler( tribe_get_option( 'recurrenceMaxMonthsBefore', 24 ), tribe_get_option( 'recurrenceMaxMonthsAfter', 24 ) );
		self::$scheduler->add_hooks();
	}

	/**
	 * @return Tribe__Events__Pro__Recurrence__Scheduler
	 */
	public static function get_scheduler() {
		if ( empty( self::$scheduler ) ) {
			self::reset_scheduler();
		}

		return self::$scheduler;
	}

	/**
	 * Placed here for compatibility reasons. This can be removed
	 * when Events Calendar 3.2 or greater is released
	 *
	 * @todo Remove this method
	 *
	 * @param int $post_id
	 *
	 * @return string
	 * @see  Tribe__Events__Main::get_series_start_date()
	 */
	private static function get_series_start_date( $post_id ) {
		if ( method_exists( 'Tribe__Events__Main', 'get_series_start_date' ) ) {
			return Tribe__Events__Main::get_series_start_date( $post_id );
		}
		$start_dates = tribe_get_recurrence_start_dates( $post_id );

		return reset( $start_dates );
	}

	public static function update_child_thumbnails( $meta_id, $post_id, $meta_key, $meta_value ) {
		static $recursing = false;
		if ( $recursing || $meta_key != '_thumbnail_id' || ! tribe_is_recurring_event( $post_id ) ) {
			return;
		}
		$recursing = true; // don't repeat this for child events

		$children = self::children()->get_ids( $post_id );
		foreach ( $children as $child_id ) {
			update_post_meta( $child_id, $meta_key, $meta_value );
		}
		$recursing = false;
	}

	public static function remove_child_thumbnails( $meta_ids, $post_id, $meta_key, $meta_value ) {
		static $recursing = false;
		if ( $recursing || $meta_key != '_thumbnail_id' || ! tribe_is_recurring_event( $post_id ) ) {
			return;
		}
		$recursing = true; // don't repeat this for child events

		$children = self::children()->get_ids( $post_id );
		foreach ( $children as $child_id ) {
			delete_post_meta( $child_id, $meta_key, $meta_value );
		}
		$recursing = false;
	}

	/**
	 * Returns an instance of the Child Events class.
	 *
	 * @return Tribe__Events__Pro__Recurrence__Children_Events
	 */
	private static function children() {
		if ( empty( self::$children ) ) {
			self::$children = Tribe__Events__Pro__Recurrence__Children_Events::instance();
		}

		return self::$children;
	}

	/**
	 * Unhooks the class from all the actions and filters it did, or woruld, hook on
	 * in the `init` method.
	 *
	 * @since 4.7
	 *
	 * @param bool $only_core Whether to remove all actions and filters or only the core ones this specific recurring
	 *                        events implementation uses.
	 */
	public static function unhook( $only_core = true ) {
		// Remove core filters first.

		// Reset the scheduler, it will unhook it too.
		self::reset_scheduler();

		remove_action( 'tribe_events_update_meta', array(
			__CLASS__,
			'updateRecurrenceMeta',
		), 20 );
		remove_action( 'wp_trash_post', array( __CLASS__, 'handle_trash_request' ) );
		remove_action( 'untrashed_post', array( __CLASS__, 'handle_untrash_request' ) );
		remove_action( 'admin_notices', array( __CLASS__, 'showRecurrenceErrorFlash' ) );
		remove_action( 'tribe_recurring_event_error', array( __CLASS__, 'setupRecurrenceErrorMsg' ) );
		remove_action( 'admin_action_tribe_split', array( __CLASS__, 'handle_split_request' ) );
		remove_filter( 'posts_request', array( 'Tribe__Events__Pro__Recurrence__Queries', 'collapse_sql' ) );

		// Unregister the notices.
		Tribe__Admin__Notices::instance()->remove( 'editing-all-recurrences' );
		Tribe__Admin__Notices::instance()->remove( 'created-recurrences' );
		remove_action( 'load-edit.php', [ __CLASS__, 'combineRecurringRequestIds' ] );
		remove_action( 'updated_post_meta', [ __CLASS__, 'update_child_thumbnails' ] );
		remove_action( 'added_post_meta', [ __CLASS__, 'update_child_thumbnails' ] );
		remove_action( 'deleted_post_meta', [ __CLASS__, 'remove_child_thumbnails' ] );
		remove_action( 'update_option_' . Tribe__Main::OPTIONNAME, [
			Tribe__Events__Pro__Recurrence__Old_Events_Cleaner::instance(),
			'clean_up_old_recurring_events',
		], 10 );

		if ( $only_core ) {
			return;
		}

		// Proceed to remove all filters, including the non-core ones.
		remove_action( 'tribe_events_date_display', [ __CLASS__, 'loadRecurrenceData' ] );
		remove_filter( 'get_edit_post_link', [ __CLASS__, 'filter_edit_post_link' ] );
		remove_filter( 'preprocess_comment', [ __CLASS__, 'set_parent_for_recurring_event_comments' ] );
		remove_action( 'pre_get_comments', [ __CLASS__, 'set_post_id_for_recurring_event_comment_queries' ] );
		remove_action( 'comment_post_redirect', [ __CLASS__, 'fix_redirect_after_comment_is_posted' ] );
		remove_action( 'wp_update_comment_count', [ __CLASS__, 'update_comment_counts_on_child_events' ] );
		remove_filter( 'comments_array', [ __CLASS__, 'set_comments_array_on_child_events' ] );
		remove_filter( 'manage_' . Tribe__Events__Main::POSTTYPE . '_posts_columns', [
			__CLASS__,
			'list_table_column_headers',
		] );
		remove_action( 'manage_' . Tribe__Events__Main::POSTTYPE . '_posts_custom_column', [
			__CLASS__,
			'populate_custom_list_table_columns',
		], 10 );
		remove_filter( 'post_class', [ __CLASS__, 'add_recurring_event_post_classes' ] );


		remove_filter( 'post_row_actions', [ __CLASS__, 'edit_post_row_actions' ] );
		remove_action( 'wp_before_admin_bar_render', [ __CLASS__, 'admin_bar_render' ] );

		remove_filter( 'tribe_settings_tab_fields', [ __CLASS__, 'inject_settings' ] );

		remove_action( 'tribe_community_events_enqueue_resources', [ __CLASS__, 'enqueue_recurrence_data' ] );
		remove_action( 'tribe_events_community_form_before_template', [ __CLASS__, 'output_recurrence_json_data' ] );

		remove_filter( 'tribe_events_pro_output_recurrence_data', [ __CLASS__, 'maybe_fix_datepicker_output' ] );
		remove_filter( 'tribe_events_pro_localize_script', [ Tribe__Events__Pro__Recurrence__Scripts::instance(), 'localize' ] );
	}

}
