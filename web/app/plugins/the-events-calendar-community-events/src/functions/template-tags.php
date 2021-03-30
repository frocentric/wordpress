<?php
if ( ! function_exists( 'tribe_is_community_my_events_page' ) ) {
	/**
	 * Tests if the current page is the My Events page
	 *
	 * @author Paul Hughes
	 *
	 * @since  1.0.1
	 *
	 * @return bool whether it is the My Events page.
	 */
	function tribe_is_community_my_events_page() {
		/** @var Tribe__Events__Community__Main $community */
		$community = tribe( 'community.main' );

		return $community->isMyEvents;
	}
}

if ( ! function_exists( 'tribe_is_community_edit_event_page' ) ) {
	/**
	 * Tests if the current page is the Edit Event page
	 *
	 * @author Paul Hughes
	 *
	 * @since  1.0.1
	 *
	 * @return bool whether it is the Edit Event page.
	 */
	function tribe_is_community_edit_event_page() {
		/** @var Tribe__Events__Community__Main $community */
		$community = tribe( 'community.main' );

		return $community->isEditPage;
	}
}

/**
 * Test if the current user can edit posts
 *
 * @param int|null $post_id
 * @param string|null $post_type (tribe_events, tribe_venue, or tribe_organizer)
 * @return bool whether the user can edit
 * @author Peter Chester
 * @since 3.1
 * @deprecated since version 3.1
 */
function tribe_community_events_user_can_edit( $post_id = null, $post_type = null ) {
	/** @var Tribe__Events__Community__Main $community */
	$community = tribe( 'community.main' );

	return $community->userCanEdit( $post_id, $post_type );
}

/**
 * Echo the community events form title field
 *
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_form_title() {
	/** @var Tribe__Events__Community__Main $community */
	$community = tribe( 'community.main' );

	$community->formTitle();
}

/**
 * Echo the community events form content editor
 *
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_form_content() {
	/** @var Tribe__Events__Community__Main $community */
	$community = tribe( 'community.main' );

	$community->formContentEditor();
}

/**
 * Echo the community events form image delete button
 *
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_form_image_delete() {
	/** @var Tribe__Events__Community__Main $community */
	$community = tribe( 'community.main' );

	echo $community->getDeleteFeaturedImageButton();
}

/**
 * Echo the community events form image preview
 *
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_form_image_preview() {
	/** @var Tribe__Events__Community__Main $community */
	$community = tribe( 'community.main' );

	echo $community->getDeleteFeaturedImageButton();
}

/**
 * Echo the community events form currency symbol
 *
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_form_currency_symbol() {
	if ( get_post() ) {
		$EventCurrencySymbol = get_post_meta( get_the_ID(), '_EventCurrencySymbol', true );
	}

	if ( ! isset( $EventCurrencySymbol ) || ! $EventCurrencySymbol ) {
		$EventCurrencySymbol = isset( $_POST['EventCurrencySymbol'] ) ? $_POST['EventCurrencySymbol'] : tribe_get_option( 'defaultCurrencySymbol', '$' );
	}

	echo esc_attr( $EventCurrencySymbol );
}

/**
 * Return URL for adding a new event.
 */
function tribe_community_events_add_event_link() {
	/** @var Tribe__Events__Community__Main $community */
	$community = tribe( 'community.main' );

	$url = $community->getUrl( 'add' );

	return apply_filters( 'tribe-community-events-add-event-link', $url );
}

/**
 * Return URL for listing events.
 */
function tribe_community_events_list_events_link() {
	/** @var Tribe__Events__Community__Main $community */
	$community = tribe( 'community.main' );

	$url = $community->getUrl( 'list' );

	return apply_filters( 'tribe-community-events-list-events-link', $url );
}

/**
 * Return URL for editing an event.
 */
function tribe_community_events_edit_event_link( $event_id = null ) {
	/** @var Tribe__Events__Community__Main $community */
	$community = tribe( 'community.main' );

	$url = $community->getUrl( 'edit', $event_id );

	return apply_filters( 'tribe-community-events-edit-event-link', $url, $event_id );
}

/**
 * Return URL for deleting an event.
 */
function tribe_community_events_delete_event_link( $event_id = null ) {
	/** @var Tribe__Events__Community__Main $community */
	$community = tribe( 'community.main' );

	$url = $community->getUrl( 'delete', $event_id );

	return apply_filters( 'tribe-community-events-delete-event-link', $url, $event_id );
}

/**
 * Return the event start date on the Community Events submission form with a default of today.
 *
 * @param null|int $event_id
 * @return string event date
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_get_start_date( $event_id = null ) {
	$event_id          = Tribe__Events__Main::postIdHelper( $event_id );
	$event             = ( $event_id ) ? get_post( $event_id ) : null;
	$datepicker_format = Tribe__Date_Utils::datepicker_formats( tribe_get_option( 'datepickerFormat' ) );

	$date = tribe_get_start_date( $event, false, $datepicker_format );
	$date = $date ? $date : date_i18n( $datepicker_format );

	/**
	 * Filter the event start date value on the Community Events submission form.
	 *
	 * @param string $date The event start date
	 * @param int|null $event_id The ID of this event, or null
	 */
	return apply_filters( 'tribe_community_events_get_start_date', $date, $event_id );
}

/**
 * Return the event end date on the Community Events submission form with a default of today.
 *
 * @param null|int $event_id
 * @return string event date
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_get_end_date( $event_id = null ) {
	$event_id          = Tribe__Events__Main::postIdHelper( $event_id );
	$event             = ( $event_id ) ? get_post( $event_id ) : null;
	$datepicker_format = Tribe__Date_Utils::datepicker_formats( tribe_get_option( 'datepickerFormat' ) );

	$date = tribe_get_end_date( $event, false, $datepicker_format );
	$date = $date ? $date : date_i18n( $datepicker_format );

	/**
	 * Filter the event end date value on the Community Events submission form.
	 *
	 * @param string $date The event end date
	 * @param int|null $event_id The ID of this event, or null
	 */
	return apply_filters( 'tribe_community_events_get_end_date', $date, $event_id );
}

/**
 * Return true if event is an all day event.
 *
 * @param null|int $event_id
 * @return bool event date
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_is_all_day( $event_id = null ) {
	$event_id = Tribe__Events__Main::postIdHelper( $event_id );
	$is_all_day = tribe_event_is_all_day( $event_id );
	$is_all_day = ( $is_all_day == 'Yes' || $is_all_day == true );
	return apply_filters( 'tribe_community_events_is_all_day', $is_all_day, $event_id );
}

/**
 * Return form select fields for event start time.
 *
 * @param null|int $event_id
 * @return string time select HTML
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_form_start_time_selector( $event_id = null ) {

	$event_id = Tribe__Events__Main::postIdHelper( $event_id );
	$is_all_day = tribe_event_is_all_day( $event_id );

	$start_date = null;

	if ( $event_id ) {
		$start_date = tribe_get_start_date( $event_id, true, Tribe__Date_Utils::DBDATETIMEFORMAT );
	}

	$start_minutes 	= Tribe__View_Helpers::getMinuteOptions( $start_date, true );
	$start_hours = Tribe__View_Helpers::getHourOptions( $is_all_day == 'yes' ? null : $start_date, true );
	$start_meridian = Tribe__View_Helpers::getMeridianOptions( $start_date, true );

	$output = '';
	$output .= sprintf( '<select name="EventStartHour" class="tribe-dropdown">%s</select>', $start_hours );
	$output .= sprintf( '<select name="EventStartMinute" class="tribe-dropdown">%s</select>', $start_minutes );
	if ( ! tribe_community_events_use_24hr_format() ) {
		$output .= sprintf( '<select name="EventStartMeridian" class="tribe-dropdown">%s</select>', $start_meridian );
	}
	return apply_filters( 'tribe_community_events_form_start_time_selector', $output, $event_id );
}

/**
 * Return form select fields for event end time.
 *
 * @param null|int $event_id
 * @return string time select HTML
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_form_end_time_selector( $event_id = null ) {

	$event_id = Tribe__Events__Main::postIdHelper( $event_id );
	$is_all_day = tribe_event_is_all_day( $event_id );
	$end_date = null;

	if ( $event_id ) {
		$end_date = tribe_get_end_date( $event_id, true, Tribe__Date_Utils::DBDATETIMEFORMAT );
	}

	$end_minutes = Tribe__View_Helpers::getMinuteOptions( $end_date );
	$end_hours = Tribe__View_Helpers::getHourOptions( $is_all_day == 'yes' ? null : $end_date );
	$end_meridian = Tribe__View_Helpers::getMeridianOptions( $end_date );

	$output = '';
	$output .= sprintf( '<select name="EventEndHour" class="tribe-dropdown">%s</select>', $end_hours );
	$output .= sprintf( '<select name="EventEndMinute" class="tribe-dropdown">%s</select>', $end_minutes );
	if ( ! tribe_community_events_use_24hr_format() ) {
		$output .= sprintf( '<select name="EventEndMeridian" class="tribe-dropdown">%s</select>', $end_meridian );
	}
	return apply_filters( 'tribe_community_events_form_end_time_selector', $output, $event_id );
}

/**
 * Determines if the current time format is 24hrs or not.
 *
 * In future releases this function can be removed and Tribe__View_Helpers::is_24hr_format()
 * can be used directly from calling functions; this is simply an intermediate step/compatibility
 * measure to minimize problems with mismatched dependencies (ie, Community 3.8 and Core 3.7).
 *
 * @deprecated 3.8 - remove in 4.0 and update calling functions as above
 * @return bool
 */
function tribe_community_events_use_24hr_format() {
	if ( method_exists( 'Tribe__View_Helpers', 'is_24hr_format' ) ) {
		return Tribe__View_Helpers::is_24hr_format();
	}
	else {
		return strstr( get_option( 'time_format', Tribe__Date_Utils::TIMEFORMAT ), 'H' );
	}
}

/**
 * Get the error or notice messages for a given form result.
 *
 * @return string error/notice HTML
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_get_messages() {
	/** @var Tribe__Events__Community__Main $community */
	$community = tribe( 'community.main' );

	return $community->outputMessage( null, false );
}

/********************** ORGANIZER TEMPLATE TAGS **********************/

/**
 * Echo Organizer edit form contents
 *
 * @param int|null $organizer_id (optional)
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_organizer_edit_form( $organizer_id = null ) {
	if ( $organizer_id ) {
		$post = get_post( $organizer_id );
		$saved = false;

		if ( isset( $post->post_type ) && $post->post_type == Tribe__Events__Main::ORGANIZER_POST_TYPE ) {

			$postId = $post->ID;

			$saved = ( ( is_admin() && isset( $_GET['post'] ) && $_GET['post'] ) || ( ! is_admin() && isset( $postId ) ) );

			// Generate all the inline variables that apply to Organizers
			$organizer_vars = Tribe__Events__Main::instance()->organizerTags;
			foreach ( $organizer_vars as $var ) {
				if ( $postId && $saved ) { //if there is a post AND the post has been saved at least once.
					$$var = get_post_meta( $postId, $var, true );
				}
			}
		}
		$meta_box_template = apply_filters( 'tribe_events_organizer_meta_box_template', '' );
		if ( ! empty( $meta_box_template ) ) {
			include( $meta_box_template );
		}
	}
}

/**
 * Echo Organizer select menu
 *
 * @param int|null $event_id (optional)
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_organizer_select_menu( $event_id = null ) {
	if ( ! $event_id ) {
		global $post;
		if ( isset( $post->post_type ) && $post->post_type == Tribe__Events__Main::POSTTYPE ) {
			$event_id = $post->ID;
		} elseif ( isset( $post->post_type ) && $post->post_type == Tribe__Events__Main::ORGANIZER_POST_TYPE ) {
			return;
		}
	}
	do_action( 'tribe_organizer_table_top', $event_id );
}

/**
 * Test to see if this is the Organizer edit screen
 *
 * @param int|null $organizer_id (optional)
 * @return bool
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_is_organizer_edit_screen( $organizer_id = null ) {
	$organizer_id = Tribe__Events__Main::postIdHelper( $organizer_id );
	$is_organizer = ( $organizer_id ) ? Tribe__Events__Main::instance()->isOrganizer( $organizer_id ) : false;
	return apply_filters( 'tribe_is_organizer', $is_organizer, $organizer_id );
}

/**
 * Return Organizer Description
 *
 * @param int|null $organizer_id (optional)
 * @return string
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_get_organizer_description( $organizer_id = null ) {
	$organizer_id = tribe_get_organizer_id( $organizer_id );
	$description = ( $organizer_id > 0 ) ? get_post( $organizer_id )->post_content : null;
	return apply_filters( 'tribe_get_organizer_description', $description );
}

/********************** VENUE TEMPLATE TAGS **********************/

/**
 * Echo Venue edit form contents
 *
 * @param int|null $venue_id (optional)
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_venue_edit_form( $venue_id = null ) {
	if ( $venue_id ) {
		$post = get_post( $venue_id );
		$saved = false;

		if ( isset( $post->post_type ) && $post->post_type == Tribe__Events__Main::VENUE_POST_TYPE ) {

			$postId = $post->ID;

			$saved = ( ( is_admin() && isset( $_GET['post'] ) && $_GET['post'] ) || ( ! is_admin() && isset( $postId ) ) );

			// Generate all the inline variables that apply to Venues
			$venue_vars = Tribe__Events__Main::instance()->venueTags;
			foreach ( $venue_vars as $var ) {
				if ( $postId && $saved ) { //if there is a post AND the post has been saved at least once.
					$$var = get_post_meta( $postId, $var, true );
				}
			}
		}

		$meta_box_template = apply_filters( 'tribe_events_venue_meta_box_template', '' );
		if ( ! empty( $meta_box_template ) ) {
			include( $meta_box_template );
		}
	}
}

/**
 * Echo Venue select menu
 *
 * @param int|null $event_id (optional)
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_venue_select_menu( $event_id = null ) {
	if ( ! $event_id ) {
		global $post;
		if ( isset( $post->post_type ) && $post->post_type == Tribe__Events__Main::POSTTYPE ) {
			$event_id = $post->ID;
		} elseif ( isset( $post->post_type ) && $post->post_type == Tribe__Events__Main::VENUE_POST_TYPE ) {
			return;
		}
	}


	do_action( 'tribe_venue_table_top', $event_id );
}

/**
 * Test to see if this is the Venue edit screen
 *
 * @param int|null $venue_id (optional)
 * @return bool
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_is_venue_edit_screen( $venue_id = null ) {
	$venue_id = Tribe__Events__Main::postIdHelper( $venue_id );
	return ( tribe_is_venue( $venue_id ) );
}

/**
 * Return Venue Description
 *
 * @param int|null $venue_id (optional)
 * @return string
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_get_venue_description( $venue_id = null ) {
	$venue_id = tribe_get_venue_id( $venue_id );
	$description = ( $venue_id > 0 ) ? get_post( $venue_id )->post_content : null;
	return apply_filters( 'tribe_get_venue_description', $description );
}


/**
 * Event Website URL
 *
 * @param null|object|int $event
 * @return string The event's website URL
 * @deprecated use tribe_get_event_website_url()
 *
 * This function was added for compatibility reasons. It can be removed once
 * tribe_get_event_website_url() is in the required version of core
 *  -- jbrinley (2013-09-16)
 */
function tribe_community_get_event_website_url( $event = null ) {
	if ( function_exists( 'tribe_get_event_website_url' ) ) {
		return tribe_get_event_website_url();
	}
	$post_id = ( is_object( $event ) && isset( $event->tribe_is_event ) && $event->tribe_is_event ) ? $event->ID : $event;
	$post_id = ( ! empty( $post_id ) || empty( $GLOBALS['post'] ) ) ? $post_id : get_the_ID();
	$url = tribe_get_event_meta( $post_id, '_EventURL', true );
	if ( ! empty( $url ) ) {
		$parseUrl = parse_url( $url );
		if ( empty( $parseUrl['scheme'] ) ) {
			$url = "http://$url";
		}
	}
	return apply_filters( 'tribe_get_event_website_url', $url, $post_id );
}

/**
 * Get the logout URL.
 *
 * @since 3.1
 *
 * @return string The logout URL with appropriate redirect for the current user
 */
function tribe_community_events_logout_url() {
	/** @var Tribe__Events__Community__Main $community */
	$community = tribe( 'community.main' );

	return $community->logout_url();
}

/**
 * @param string $field
 *
 * @return bool
 */
function tribe_community_is_field_required( $field ) {
	/** @var Tribe__Events__Community__Main $community */
	$community = tribe( 'community.main' );

	return in_array( $field, $community->required_fields_for_submission(), true );
}

function tribe_community_is_field_group_required( $field ) {
	/** @var Tribe__Events__Community__Main $community */
	$community = tribe( 'community.main' );

	return in_array( $field, $community->required_field_groups_for_submission(), true );
}

/**
 * @param string $field
 *
 * @return string
 */
function tribe_community_required_field_marker( $field ) {
	if ( tribe_community_is_field_required( $field ) || tribe_community_is_field_group_required( $field ) ) {
		$html = '<span class="req">' . __( '(required)', 'tribe-events-community' ) . '</span>';
		return apply_filters( 'tribe_community_required_field_marker', $html, $field );
	}
	return '';
}

/**
 * Community events field label.
 *
 * @param string $field The field name.
 * @param string $text  The field label.
 *
 * @return void
 */
function tribe_community_events_field_label( $field, $text ) {
	$label_text = apply_filters( 'tribe_community_events_field_label_text', $text, $field );
	$class      = tribe_community_events_field_has_error( $field ) ? 'error' : '';
	$class      = apply_filters( 'tribe_community_events_field_label_class', $class, $field );
	$html       = sprintf(
		'<label for="%s" class="%s">%s %s</label>',
		$field,
		$class,
		$label_text,
		tribe_community_required_field_marker( $field )
	);

	/**
	 * Filter the field label.
	 * `tribe_community_events_field_label`
	 *
	 * @param string $html The label HTML
	 * @param string $field The field name.
	 * @param string $text  The field label.
	 */
	$html = apply_filters( 'tribe_community_events_field_label', $html, $field, $text );

	echo $html;
}

/**
 * Community events field classes.
 *
 * @since 4.7.1
 *
 * @param string  $field   The field name.
 * @param string  $classes The field classes.
 * @param boolean $echo    (Optional) if true we print, else we return.
 *
 * @return mixed
 */
function tribe_community_events_field_classes( $field, $classes = [], $echo = true ) {

	// If we're receiving the classes as string, make it array.
	if ( ! is_array( $classes ) ) {
		$classes = explode( '', $classes );
	}

	// If the field is required, add the `required` class.
	if (
		tribe_community_is_field_required( $field )
		|| tribe_community_is_field_group_required( $field )
	) {
		$classes[] = 'required';
	}

	// Sanitize the $classes.
	$classes = array_map( 'sanitize_html_class', $classes );

	/**
	 * Filter the field classes.
	 * `tribe_community_events_field_label`
	 *
	 * @since 4.7.1
	 *
	 * @param string $field   The field name.
	 * @param string $classes The field classes.
	 */
	$classes = apply_filters( 'tribe_community_events_field_classes', $classes, $field );

	$classes = esc_attr( implode( ' ', $classes ) );

	if ( ! empty( $echo ) ) {
		echo $classes;
	} else {
		return $classes;
	}
}

/**
 * Check if the community event field has error.
 *
 * @since 4.7.1
 *
 * @param string $field The field name.
 *
 * @return boolean
 */
function tribe_community_events_field_has_error( $field ) {
	return apply_filters( 'tribe_community_events_field_has_error', false, $field );
}

/**
 * Indicates if single geography mode is enabled (this typically implies there
 * is no need for country, state/province or timezone options).
 *
 * @return boolean
 */
function tribe_community_events_single_geo_mode() {
	/** @var Tribe__Events__Community__Main $community */
	$community = tribe( 'community.main' );

	return (bool) $community->getOption( 'single_geography_mode' );
}

/**
 * Whether an event is one submitted via the community event submission or not.
 *
 * The check is made on the `_EventOrigin` custom field set when the event is
 * originally submitted; as such later modifications or deletions of that field can
 * cause different return values from this function.
 * Also note that this function will always return `false` for community events submitted
 * before version `4.3`; to have this function return the right value set the
 * `_EventOrigin` custom field to `community-events` on previously created community events.
 * Note that editing a pre `4.3` version community event through the community event
 * edit screen will mark it as a community event.
 *
 * @since 4.3
 *
 * @param WP_Post|int $event Either the `WP_Post` event object or the event post `ID`
 */
function tribe_community_events_is_community_event( $event ) {
	$event_id = Tribe__Main::post_id_helper( $event );

	return get_post_meta( $event_id, '_EventOrigin', true ) === 'community-events';
}


/**
 * Events Lists Menu Items
 *
 * @since 4.5
 *
 * @return array
 */
function tribe_community_events_list_columns() {
	$organizer_label_singular = tribe_get_organizer_label_singular();
	$venue_label_singular     = tribe_get_venue_label_singular();

	$columns = [
		'status'     => esc_html__( 'Status', 'tribe-events-community' ),
		'title'      => esc_html__( 'Title', 'tribe-events-community' ),
		'organizer'  => $organizer_label_singular,
		'venue'      => $venue_label_singular,
		'category'   => esc_html__( 'Category', 'tribe-events-community' ),
	];

	if ( class_exists( 'Tribe__Events__Pro__Main' ) ) {
		$columns['recurring'] = esc_html__( 'Recurring?', 'tribe-events-community' );
	}

	$columns['start_date'] = esc_html__( 'Start Date', 'tribe-events-community' );
	$columns['end_date']   = esc_html__( 'End Date', 'tribe-events-community' );

	/**
	 * Allows developers to add more Columns on the My Events List page
	 * @param array $columns [ $key => $label ]
	 */
	$columns = apply_filters( 'tribe_community_events_list_columns', $columns );

	return $columns;
}

/**
 * Buttons for toggling future or past events
 *
 * @since 4.5
 */
function tribe_community_events_prev_next_nav() {
	/** @var Tribe__Events__Community__Main $community */
	$community = tribe( 'community.main' );

	add_filter( 'get_pagenum_link', [ $community, 'fix_pagenum_link' ] );

	/**
	 * Allows to modify the default link on My Events List nav
	 *
	 * @since 4.6.2
	 *
	 * @param string the link we want to modify
	 */
	$link = apply_filters( 'tribe_events_community_shortcode_nav_link', get_pagenum_link( 1 ) );

	$link = remove_query_arg( 'eventDisplay', $link );

	if ( isset( $_GET['eventDisplay'] ) && 'past' == $_GET['eventDisplay'] ) {
		$upcoming_button_class = 'tribe-button-tertiary';
		$past_button_class     = 'tribe-button-secondary';
	} else {
		$upcoming_button_class = 'tribe-button-secondary';
		$past_button_class     = 'tribe-button-tertiary';
	}
	?>
	<a
		href="<?php echo esc_url( $link . '?eventDisplay=list' ); ?>"
		class="tribe-button tribe-button-small tribe-upcoming <?php echo esc_attr( $upcoming_button_class ); ?>"
	>
		<?php esc_html_e( 'Upcoming events', 'tribe-events-community' ); ?>
	</a>
	<a
		href="<?php echo esc_url( $link . '?eventDisplay=past' ); ?>"
		class="tribe-button tribe-button-small tribe-past <?php echo esc_attr( $past_button_class ); ?>"
	>
		<?php esc_html_e( 'Past events', 'tribe-events-community' ); ?>
	</a>
	<?php
}
