<?php
add_action( 'wp_enqueue_scripts','wpsp_pro_filter_register_scripts' );
function wpsp_pro_filter_register_scripts() {
	wp_register_script( 'wpsp-filterizr', plugin_dir_url( __FILE__ ) . '/js/jquery.filterizr.min.js', array( 'jquery' ), '', true );
}

add_action( 'butterbean_register', 'wpsp_filter_items_register', 20, 2 );
function wpsp_filter_items_register( $butterbean, $post_type ) {
	if ( function_exists( 'wpsp_get_defaults' ) ) $defaults = wpsp_get_defaults();
	$manager = $butterbean->get_manager( 'wp_show_posts' );

	$manager->register_control(
		'wpsp_filter',
		array(
			'type'        => 'checkbox',
			'section'     => 'wpsp_posts',
			'label'       => __( 'Filter items','wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-filter' )
		)
	);

	$manager->register_setting(
		'wpsp_filter',
		array(
			'sanitize_callback' => 'butterbean_validate_boolean',
			'default' => $defaults[ 'wpsp_filter' ] ? $defaults[ 'wpsp_filter' ] : false
		)
	);
}