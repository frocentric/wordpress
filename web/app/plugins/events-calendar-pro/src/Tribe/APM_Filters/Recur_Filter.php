<?php
class Tribe__Events__Pro__APM_Filters__Recur_Filter {

	protected $key  = 'ecp_recur';
	protected $type = 'recur';
	protected $meta = '_EventRecurrence';

	protected $opts;
	protected $not_recur = 's:5:"rules";a:0:{}s:10:"exclusions";a:0:{}';
	protected $not_recur_legacy = 's:4:"type";s:4:"None";';

	public function __construct() {

		$this->opts = array(
			'is'  => esc_html__( 'Yes', 'tribe-events-calendar-pro' ),
			'not' => esc_html__( 'No', 'tribe-events-calendar-pro' ),
		);

		$type = $this->type;
		add_filter( 'tribe_custom_row' . $type, array( $this, 'form_row' ), 10, 4 );
		add_filter( 'tribe_maybe_active' . $type, array( $this, 'maybe_set_active' ), 10, 3 );
		add_action( 'tribe_after_parse_query', array( $this, 'parse_query' ), 10, 2 );
	}

	public function parse_query( $query, $active ) {
		if ( empty( $active[ $this->key ] ) ) {
			return;
		}
		$query->apm_ecp_recur = $active[ $this->key ];

		add_filter( 'posts_join', array( $this, 'join_recur' ), 10, 2 );
		add_filter( 'posts_where', array( $this, 'where_recur' ), 10, 2 );
	}

	public function join_recur( $join, $wp_query ) {
		// bail if this is not a query for event post type
		if ( $wp_query->get( 'post_type' ) !== Tribe__Events__Main::POSTTYPE ) {
			return $join;
		}

		if ( ! empty( $wp_query->apm_ecp_recur ) ) {
			global $wpdb;
			$join .= " LEFT JOIN {$wpdb->postmeta} AS recur_meta ON({$wpdb->posts}.ID = recur_meta.post_id AND recur_meta.meta_key='{$this->meta}') ";
		}

		return $join;
	}

	public function where_recur( $where, $wp_query ) {
		// bail if this is not a query for event post type
		if ( $wp_query->get( 'post_type' ) !== Tribe__Events__Main::POSTTYPE ) {
			return $where;
		}

		if ( empty( $wp_query->apm_ecp_recur ) ) {
			return $where;
		}

		global $wpdb;


		if ( 'is' === $wp_query->apm_ecp_recur ) {
			$where .= " AND ( recur_meta.meta_value NOT LIKE '%$this->not_recur%' AND recur_meta.meta_value NOT LIKE '%$this->not_recur_legacy%' AND recur_meta.meta_value <> '' )  ";
		} else {
			$where .= " AND ( recur_meta.meta_value LIKE '%$this->not_recur%' OR recur_meta.meta_value LIKE '%$this->not_recur_legacy%' OR recur_meta.meta_value = '' )  ";
		}

		return $where;
	}

	public function maybe_set_active( $return, $key, $filter ) {
		global $ecp_apm;

		if ( ! empty( $_POST[ $this->key ] ) ) {
			return $_POST[ $this->key ];
		}

		$active_filters = $ecp_apm->filters->get_active();

		if ( ! empty( $active_filters[ $this->key ] ) ) {
			return $active_filters[ $this->key ];
		}

		return $return;
	}

	public function form_row( $return, $key, $value, $filter ) {
		// in case we have a blank row
		$value = (string) $value;

		return tribe_select_field( $this->key, $this->opts, $value );
	}
}
