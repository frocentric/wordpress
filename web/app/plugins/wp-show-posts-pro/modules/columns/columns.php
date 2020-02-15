<?php
add_action( 'butterbean_register', 'wpsp_masonry_register', 25, 2 );
function wpsp_masonry_register( $butterbean, $post_type ) {
	if ( ! function_exists( 'wpsp_get_defaults' ) ) {
		return;
	}

	$defaults = wpsp_get_defaults();

	$manager = $butterbean->get_manager( 'wp_show_posts' );

	$manager->register_control(
		'wpsp_masonry',
		array(
			'type'        => 'checkbox',
			'section'     => 'wpsp_columns',
			'label'       => __( 'Masonry','wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-masonry' )
		)
	);

	$manager->register_setting(
		'wpsp_masonry',
		array(
			'sanitize_callback' => 'butterbean_validate_boolean',
			'default' => $defaults[ 'wpsp_masonry' ] ? $defaults[ 'wpsp_masonry' ] : false
		)
	);

	$manager->register_control(
		'wpsp_featured_post',
		array(
			'type'        => 'checkbox',
			'section'     => 'wpsp_columns',
			'label'       => __( 'Featured post','wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-featured-post' )
		)
	);

	$manager->register_setting(
		'wpsp_featured_post',
		array(
			'sanitize_callback' => 'butterbean_validate_boolean',
			'default' => $defaults[ 'wpsp_featured_post' ] ? $defaults[ 'wpsp_featured_post' ] : false
		)
	);
}