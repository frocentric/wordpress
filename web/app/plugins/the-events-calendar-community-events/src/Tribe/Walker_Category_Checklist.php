<?php
class Tribe__Events__Community__Walker_Category_Checklist extends Walker_Category_Checklist {
	public $num_items = 0;

	/**
	 * Start the element output.
	 *
	 * The $args parameter holds additional values that may be used with the child
	 * class methods. Includes the element output also.
	 *
	 * @param string $output            Passed by reference. Used to append additional content.
	 * @param object $object            The data object.
	 * @param int    $depth             Depth of the item.
	 * @param array  $args              An array of additional arguments.
	 * @param int    $current_object_id ID of the current item.
	 */
	public function start_el( &$output, $object, $depth = 0, $args = [], $current_object_id = 0 ) {
		$this->num_items++;
		parent::start_el( $output, $object, $depth, $args, $current_object_id );
	}
}
