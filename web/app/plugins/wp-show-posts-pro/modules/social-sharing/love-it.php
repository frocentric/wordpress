<?php
add_action( 'wp_enqueue_scripts', 'wpsp_pro_love_it_enqueue_scripts' );
/**
 * Register the stylesheets for the public-facing side of the site.
 *
 * @since    0.5
 */
function wpsp_pro_love_it_enqueue_scripts() {
	wp_register_script( 'wpsp-love-it', plugin_dir_url( __FILE__ ) . '/js/love-it.js', array( 'jquery' ), '', true );
	wp_localize_script( 'wpsp-love-it', 'wpspLoveIt', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'like' => __( 'Like', 'wp-show-posts-pro' ),
		'unlike' => __( 'Unlike', 'wp-show-posts-pro' )
	) );
}

add_action( 'wp_ajax_nopriv_wpsp_pro_process_simple_like', 'wpsp_pro_process_simple_like' );
add_action( 'wp_ajax_wpsp_pro_process_simple_like', 'wpsp_pro_process_simple_like' );
/**
 * Processes like/unlike
 *
 * @since    0.5
 */
function wpsp_pro_process_simple_like() {
	$nonce = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( $_REQUEST['nonce'] ) : 0;

	if ( ! wp_verify_nonce( $nonce, 'wpsp-li-nonce' ) ) {
		exit();
	}

	// Test if javascript is disabled
	$disabled = ( isset( $_REQUEST['disabled'] ) && $_REQUEST['disabled'] == true ) ? true : false;

	// Base variables
	$post_id = ( isset( $_REQUEST['post_id'] ) && is_numeric( $_REQUEST['post_id'] ) ) ? intval( $_REQUEST['post_id'] ) : '';
	$result = array();
	$post_users = NULL;
	$like_count = 0;

	// Get plugin options
	if ( $post_id != '' ) {
		$count = get_post_meta( $post_id, "_wpsp_post_like_count", true ); // like count
		$count = ( isset( $count ) && is_numeric( $count ) ) ? $count : 0;

		if ( ! wpsp_pro_already_liked( $post_id ) ) {

			if ( is_user_logged_in() ) {
				$user_id = get_current_user_id();
				$post_users = wpsp_pro_post_user_likes( $user_id, $post_id );

				// Update User & Post
				$user_like_count = get_user_option( "_wpsp_user_like_count", $user_id );
				$user_like_count =  ( isset( $user_like_count ) && is_numeric( $user_like_count ) ) ? $user_like_count : 0;

				update_user_option( $user_id, "_wpsp_user_like_count", ++$user_like_count );

				if ( $post_users ) {
					update_post_meta( $post_id, "_wpsp_user_liked", $post_users );
				}
			} else {
				$user_ip = wpsp_pro_love_it_get_ip();
				$post_users = wpsp_pro_post_ip_likes( $user_ip, $post_id );

				// Update Post
				if ( $post_users ) {
					update_post_meta( $post_id, "_wpsp_user_IP", $post_users );
				}
			}

			$like_count = ++$count;
			$response['status'] = "liked";
			$response['icon'] = wpsp_pro_get_liked_icon();

		} else { // Unlike the post.

			if ( is_user_logged_in() ) {
				$user_id = get_current_user_id();
				$post_users = wpsp_pro_post_user_likes( $user_id, $post_id );

				$user_like_count = get_user_option( "_wpsp_user_like_count", $user_id );
				$user_like_count =  ( isset( $user_like_count ) && is_numeric( $user_like_count ) ) ? $user_like_count : 0;

				if ( $user_like_count > 0 ) {
					update_user_option( $user_id, '_wpsp_user_like_count', --$user_like_count );
				}

				// Update Post
				if ( $post_users ) {
					$uid_key = array_search( $user_id, $post_users );
					unset( $post_users[$uid_key] );
					update_post_meta( $post_id, "_wpsp_user_liked", $post_users );
				}
			} else {
				$user_ip = wpsp_pro_love_it_get_ip();
				$post_users = wpsp_pro_post_ip_likes( $user_ip, $post_id );

				// Update Post
				if ( $post_users ) {
					$uip_key = array_search( $user_ip, $post_users );
					unset( $post_users[$uip_key] );
					update_post_meta( $post_id, "_wpsp_user_IP", $post_users );
				}
			}

			$like_count = ( $count > 0 ) ? --$count : 0; // Prevent negative number
			$response['status'] = "unliked";
			$response['icon'] = wpsp_pro_get_unliked_icon();
		}

		update_post_meta( $post_id, "_wpsp_post_like_count", $like_count );
		update_post_meta( $post_id, "_wpsp_post_like_modified", date( 'Y-m-d H:i:s' ) );

		$response['count'] = wpsp_pro_get_like_count( $like_count );

		if ( $disabled == true ) {
			wp_redirect( get_permalink( $post_id ) );
			exit();
		} else {
			wp_send_json( $response );
		}
	}
}

/**
 * Utility to test if the post is already liked.
 *
 * @since 0.5
 */
function wpsp_pro_already_liked( $post_id ) {
	$post_users = NULL;
	$user_id = NULL;

	if ( is_user_logged_in() ) {
		$user_id = get_current_user_id();
		$post_meta_users = get_post_meta( $post_id, "_wpsp_user_liked" );

		if ( count( $post_meta_users ) != 0 ) {
			$post_users = $post_meta_users[0];
		}
	} else {
		$user_id = wpsp_pro_love_it_get_ip();
		$post_meta_users = get_post_meta( $post_id, "_wpsp_user_IP" );

		if ( count( $post_meta_users ) != 0 ) { // meta exists, set up values
			$post_users = $post_meta_users[0];
		}
	}

	if ( is_array( $post_users ) && in_array( $user_id, $post_users ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Output the like button.
 *
 * @since 0.5
 */
function wpsp_get_love_button( $post_id ) {
	$output = '';
	$nonce = wp_create_nonce( 'wpsp-li-nonce' );
	$post_id_class = esc_attr( ' wpsp-li-button-' . $post_id );
	$like_count = get_post_meta( $post_id, "_wpsp_post_like_count", true );
	$like_count = ( isset( $like_count ) && is_numeric( $like_count ) ) ? $like_count : 0;

	$count = wpsp_pro_get_like_count( $like_count );
	$icon_empty = wpsp_pro_get_unliked_icon();
	$icon_full = wpsp_pro_get_liked_icon();

	if ( wpsp_pro_already_liked( $post_id ) ) {
		$class = esc_attr( ' wpsp-liked' );
		$title = __( 'Unlike', 'wp-show-posts-pro' );
		$icon = $icon_full;
	} else {
		$class = '';
		$title = __( 'Like', 'wp-show-posts-pro' );
		$icon = $icon_empty;
	}

	$output = '<span class="wpsp-li-wrapper"><a title="' . __( 'Love it','wp-show-posts-pro' ) . '" href="' . admin_url( 'admin-ajax.php?action=wpsp_pro_process_simple_like' . '&post_id=' . $post_id . '&nonce=' . $nonce . '&disabled=true' ) . '" class="wpsp-li-button' . $post_id_class . $class . '" data-nonce="' . $nonce . '" data-post-id="' . $post_id . '" title="' . $title . '">' . $icon . $count . '</a></span>';

	return $output;
}

/**
 * Utility retrieves post meta user likes (user id array),
 * then adds new user id to retrieved array
 *
 * @since 0.5
 */
function wpsp_pro_post_user_likes( $user_id, $post_id ) {
	$post_users = '';
	$post_meta_users = get_post_meta( $post_id, "_wpsp_user_liked" );

	if ( count( $post_meta_users ) != 0 ) {
		$post_users = $post_meta_users[0];
	}

	if ( ! is_array( $post_users ) ) {
		$post_users = array();
	}

	if ( ! in_array( $user_id, $post_users ) ) {
		$post_users['user-' . $user_id] = $user_id;
	}

	return $post_users;
}

/**
 * Utility retrieves post meta ip likes (ip array),
 * then adds new ip to retrieved array
 *
 * @since 0.5
 */
function wpsp_pro_post_ip_likes( $user_ip, $post_id ) {
	$post_users = '';
	$post_meta_users = get_post_meta( $post_id, "_wpsp_user_IP" );

	if ( count( $post_meta_users ) != 0 ) {
		$post_users = $post_meta_users[0];
	}

	if ( ! is_array( $post_users ) ) {
		$post_users = array();
	}

	if ( ! in_array( $user_ip, $post_users ) ) {
		$post_users['ip-' . $user_ip] = $user_ip;
	}

	return $post_users;
}

/**
 * Utility to retrieve IP address
 *
 * @since 0.5
 */
function wpsp_pro_love_it_get_ip() {
	if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) && ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = ( isset( $_SERVER['REMOTE_ADDR'] ) ) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
	}

	$ip = filter_var( $ip, FILTER_VALIDATE_IP );

	if ( apply_filters( 'wpsp_pro_love_icon_disable_ip_store', false ) ) {
		$ip = false;
	}

	$ip = ( $ip === false ) ? '0.0.0.0' : $ip;

	return $ip;
}

/**
 * Utility returns the button icon for "like" action
 *
 * @since 0.5
 */
function wpsp_pro_get_liked_icon() {
	$icon = '<span class="wpsp-love-icon"></span>';

	return $icon;
}

/**
 * Utility returns the button icon for "unlike" action
 *
 * @since 0.5
 */
function wpsp_pro_get_unliked_icon() {
	$icon = '<span class="wpsp-love-icon"></span>';

	return $icon;
}

/**
 * Utility function to format the button count,
 * appending "K" if one thousand or greater,
 * "M" if one million or greater,
 * and "B" if one billion or greater (unlikely).
 * $precision = how many decimal points to display (1.25K)
 *
 * @since 0.5
 */
function wpsp_pro_love_it_format_count( $number ) {
	$precision = 2;

	if ( $number >= 1000 && $number < 1000000 ) {
		$formatted = number_format( $number/1000, $precision ).'K';
	} else if ( $number >= 1000000 && $number < 1000000000 ) {
		$formatted = number_format( $number/1000000, $precision ).'M';
	} else if ( $number >= 1000000000 ) {
		$formatted = number_format( $number/1000000000, $precision ).'B';
	} else {
		$formatted = $number; // Number is less than 1000
	}

	$formatted = str_replace( '.00', '', $formatted );

	return $formatted;
}

/**
 * Utility retrieves count plus count options,
 * returns appropriate format based on options
 *
 * @since 0.5
 */
function wpsp_pro_get_like_count( $like_count ) {
	if ( is_numeric( $like_count ) && $like_count > 0 ) {
		$number = wpsp_pro_love_it_format_count( $like_count );
	} else {
		$number = '';
	}

	$count = '<span class="wpsp-love-count">' . $number . '</span>';

	return $count;
}
