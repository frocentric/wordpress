<?php

/**
 * Class Tribe__Events__Filterbar__Filters__Tag
 */
class Tribe__Events__Filterbar__Filters__Tag extends Tribe__Events__Filterbar__Filter{
	public $type = 'select';

	public function get_admin_form() {
		$title = $this->get_title_field();
		$type = $this->get_multichoice_type_field();
		return $title.$type;
	}

	protected function get_values() {
		$tags_array = [];

		$tags = get_tags();
		foreach ( $tags as $tag ) {
			$tags_array[ $tag->term_id ] = [
				'name' => $tag->name,
				'value' => $tag->term_id,
			];
		}

		return $tags_array;
	}

	protected function setup_query_args() {
		$this->queryArgs = [ 'tag__in' => $this->currentValue ];
	}
}
