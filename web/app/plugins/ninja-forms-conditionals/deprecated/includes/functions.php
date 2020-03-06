<?php

/**
 * Return conditional object ids from a parent object id.
 * 
 * @since 1.2.8
 * @return array object ids
 */
function nf_cl_get_conditions( $object_id, $full_data = false ) {
	return nf_get_object_children( $object_id, 'condition', $full_data );
}

/**
 * Return criteria object ids from a conditional object id.
 *
 * @since 1.2.8
 * @return array object ids
 */
function nf_cl_get_criteria( $object_id, $full_data = false ) {
	return nf_get_object_children( $object_id, 'criteria', $full_data );
}

/**
 * Add a criteria to a condition
 *
 * @param $cond_id - condition object id
 * @since 1.2.8
 * @return object id
 */
function nf_cl_insert_criteria( $cond_id ) {
	// Insert our new criteria object.
	$cr_id = nf_insert_object( 'criteria' );
	// Create a relationship between this criteria and our condition
	nf_add_relationship( $cr_id, 'criteria', $cond_id, 'condition' );
	return $cr_id;
}

/**
 * Add a condition to an object
 *
 * @param $object_id
 * @since 1.2.8
 * @return object id
 */
function nf_cl_insert_condition( $parent_id ) {
	// Insert our new condition object.
	$cond_id = nf_insert_object( 'condition' );
	// Get our parent type.
	$parent_type = nf_get_object_type( $parent_id );
	// Create a relationship between this condition and its parent object.
	nf_add_relationship( $cond_id, 'condition', $parent_id, $parent_type );
	return $cond_id;
}

/**
 * Delete a condition and all of its criteria
 *
 * @param $cond_id
 * @since 1.2.8
 * @return void
 */
function nf_cl_delete_condition( $cond_id ) {
	// Delete our condition object
	nf_delete_object( $cond_id );
	// Remove all of our criteria
	$criteria = nf_cl_get_criteria( $cond_id );
	foreach ( $criteria as $cr_id ) {
		nf_delete_object( $cr_id );
	}
}

/**
 * Sort our field list by label.
 * 
 * @param $a
 * @param $b
 * @return sorted array
 */
function nf_cl_sort_by_label($a, $b) {
    return strcasecmp( $a['label'], $b['label'] );
}