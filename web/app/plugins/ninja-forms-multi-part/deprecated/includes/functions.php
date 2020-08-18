<?php

function nf_mp_get_pages( $form_id = '' ){
	global $ninja_forms_loading, $ninja_forms_processing, $ninja_forms_fields;
	
	$fields = Ninja_Forms()->form( $form_id )->fields;

	$pages = array();
	$x = 0;
	$y = 0;
	$last_field = '';
	foreach( $fields as $field ){

		if( $field['type'] == '_page_divider' ){
			$x++;
			$y = 0;
			$pages[$x]['id'] = $field['id'];
			if ( isset ( $field['data']['label'] ) ) {
				$page_name = $field['data']['label'];
			} else {
				$page_name = '';
			}
			$pages[$x]['page_title'] = $page_name;
			
		} else {
			if ( ! isset ( $ninja_forms_fields[ $field['type'] ] ) )
				continue;
			// If we don't have a divider, we still want to be on page 1.
			if ( $x == 0 )
				$x++;
			if ( $y == 0 ) {
				if ( ! empty( $field['type'] ) ) {
					$pages[$x]['first_field'] = $field['id'];
					$y++;
				}
			}
		}

		if ( ! empty( $field['type'] ) ) {
			$pages[$x]['fields'][] = $field['id'];
		}
	
		if ( isset ( $ninja_forms_loading ) ) {
			$ninja_forms_loading->update_field_setting( $field['id'], 'page', $x );
		} else if ( isset ( $ninja_forms_processing ) ) {
			$ninja_forms_processing->update_field_setting( $field['id'], 'page', $x );
		}
	}

	foreach ( $pages as $num => $vars ) {
		$last_field = end( $vars['fields'] );
		$pages[$num]['last_field'] = $last_field;
	}

	return $pages;
}

function ninja_forms_mp_get_divider_by_page( $form_id, $current_page ){
	global $ninja_forms_loading, $ninja_forms_processing;

	if ( isset ( $ninja_forms_loading ) ) {
		$pages = $ninja_forms_loading->get_form_setting( 'mp_pages' );
	} else {
		$pages = $ninja_forms_processing->get_form_setting( 'mp_pages' );
	}

	$divider_id = $pages[$current_page]['id'];

	return $divider_id;
}

function ninja_forms_mp_get_page_by_divider( $form_id, $field_id ){
	global $ninja_forms_loading, $ninja_forms_processing;

	if ( isset ( $ninja_forms_loading ) ) {
		$pages = $ninja_forms_loading->get_form_setting( 'mp_pages' );
	} else {
		$pages = $ninja_forms_processing->get_form_setting( 'mp_pages' );
	}

	$x = 1;
	foreach ( $pages as $num => $vars ) {
		if ( $vars['id'] == $field_id ) {
			$page_num = $x;
			break;
		}
		$x++;
	}

	return $page_num;
}

function ninja_forms_mp_get_page_by_field_id( $field_id ) {
	global $ninja_forms_loading, $ninja_forms_processing;

	if ( isset ( $ninja_forms_loading ) ) {
		$page = $ninja_forms_loading->get_field_setting( $field_id, 'page' );
	} else {
		$page = $ninja_forms_processing->get_field_setting( $field_id, 'page' );
	}

	return $page;
}

/*
 *
 * Function that loops through our pages and adds an array with the pages information to our loading/processing classes.
 *
 * @since 1.2.6
 * @return void
 */

function ninja_forms_mp_set_page_array( $form_id ) {
	global $ninja_forms_loading, $ninja_forms_processing;

	if ( isset ( $ninja_forms_loading ) ) {
		$form_id = $ninja_forms_loading->get_form_ID();
	} else {
		$form_id = $ninja_forms_processing->get_form_ID();
	}

	$pages = nf_mp_get_pages( $form_id );

	if ( isset ( $ninja_forms_loading ) ) {
		$ninja_forms_loading->update_form_setting( 'mp_pages', $pages );
	} else {
		$ninja_forms_processing->update_form_setting( 'mp_pages', $pages );
	}
}

add_action( 'ninja_forms_display_init', 'ninja_forms_mp_set_page_array' );
add_action( 'ninja_forms_before_pre_process', 'ninja_forms_mp_set_page_array' );
add_action( 'ninja_forms_edit_sub_pre_process', 'ninja_forms_mp_set_page_array', 3 );

/*
 * Get a page count for a particular form.
 *
 * @since 1.3
 * @return int $count - Number of pages in the given form
 */

function nf_mp_get_page_count( $form_id ) {
	if ( empty ( $form_id ) )
		return false;

	$fields = Ninja_Forms()->form( $form_id )->fields;
	$x = 0;
	foreach ( $fields as $field ) {
		if ( $field['type'] == '_page_divider' ) {
			$x++;
		}
	}

	return $x;
}

/**
 * Output our admin page nav <li>s given a form id.
 * 
 * @since 1.3
 * @return void;
 */
function nf_mp_admin_page_nav( $form_id, $current_page = 1 ) {
	$pages = nf_mp_get_pages( $form_id );
	$page_count = nf_mp_get_page_count( $form_id );
	$offset = 0;
	?>
	<li class="mp-remove-page mp-operation"><span class="symbol"><span class="dashicons dashicons-minus"></span></span><span class="spinner mp-operation-spinner"></span></li>
	<span id="mp-page-list">
	<?php
	if( is_array( $pages ) && !empty( $pages ) ){
		foreach( $pages as $page => $data ){
			if( $page == $current_page ){
				$active = 'active';
			}else{
				$active = '';
			}
			?>
			<li class="<?php echo $active;?> mp-page-nav" data-page="<?php echo $page;?>" id="ninja_forms_mp_page_<?php echo $page;?>"><?php echo $page;?></li>
			<?php
		}
	}
	?>
	</span>
	<li class="mp-add-page mp-operation"><span class="symbol"><span class="dashicons dashicons-plus"></span></span><span class="spinner mp-operation-spinner"></span></li>
	<?php
}

/**
 * Delete all page dividers in a given form.
 *
 * @since 1.3
 * @return void
 */
function nf_mp_delete_dividers( $form_id ) {
	$fields = Ninja_Forms()->form( $form_id )->fields;
	foreach ( $fields as $field_id => $field ) {
		if ( $field['type'] == '_page_divider' ) {
			ninja_forms_delete_field( $field_id );
		}
	}
}