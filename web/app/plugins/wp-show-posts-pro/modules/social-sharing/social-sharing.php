<?php
// Love button
require trailingslashit( dirname( __FILE__ ) ) . 'love-it.php';

add_action( 'butterbean_register', 'wpsp_social_register', 30, 2 );
/**
 * Register our social sharing controls.
 *
 * @since 0.5
 */
function wpsp_social_register( $butterbean, $post_type ) {
	if ( ! function_exists( 'wpsp_get_defaults' ) ) {
		return;
	}

	$defaults = wpsp_get_defaults();

	$manager = $butterbean->get_manager( 'wp_show_posts' );

	$manager->register_section(
        'wpsp_social',
        array(
            'label' => esc_html__( 'Social', 'wp-show-posts-pro' ),
            'icon'  => 'dashicons-share'
        )
    );

	$manager->register_control(
		'wpsp_social_sharing',
		array(
			'type'        => 'checkbox',
			'section'     => 'wpsp_social',
			'label'       => __( 'Show social sharing buttons','wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-social-sharing' )
		)
	);

	$manager->register_setting(
		'wpsp_social_sharing',
		array(
			'sanitize_callback' => 'butterbean_validate_boolean',
			'default' => $defaults[ 'wpsp_social_sharing' ] ? $defaults[ 'wpsp_social_sharing' ] : false
		)
	);

	$manager->register_control(
		'wpsp_twitter',
		array(
			'type'        => 'checkbox',
			'section'     => 'wpsp_social',
			'label'       => __( 'Twitter','wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-twitter' )
		)
	);

	$manager->register_setting(
		'wpsp_twitter',
		array(
			'sanitize_callback' => 'butterbean_validate_boolean',
			'default' => $defaults[ 'wpsp_twitter' ] ? $defaults[ 'wpsp_twitter' ] : false
		)
	);

	$manager->register_control(
		'wpsp_facebook',
		array(
			'type'        => 'checkbox',
			'section'     => 'wpsp_social',
			'label'       => __( 'Facebook','wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-facebook' )
		)
	);

	$manager->register_setting(
		'wpsp_facebook',
		array(
			'sanitize_callback' => 'butterbean_validate_boolean',
			'default' => $defaults[ 'wpsp_facebook' ] ? $defaults[ 'wpsp_facebook' ] : false
		)
	);

	$manager->register_control(
		'wpsp_pinterest',
		array(
			'type'        => 'checkbox',
			'section'     => 'wpsp_social',
			'label'       => __( 'Pinterest','wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-pinterest' )
		)
	);

	$manager->register_setting(
		'wpsp_pinterest',
		array(
			'sanitize_callback' => 'butterbean_validate_boolean',
			'default' => $defaults[ 'wpsp_pinterest' ] ? $defaults[ 'wpsp_pinterest' ] : false
		)
	);

	$manager->register_control(
		'wpsp_love',
		array(
			'type'        => 'checkbox',
			'section'     => 'wpsp_social',
			'label'       => __( 'Love','wp-show-posts-pro' ),
			'description' => __( 'This option stores the user IP in your database to keep count. If enabled, you should mention this in your privacy policy for GDPR reasons.', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-love' )
		)
	);

	$manager->register_setting(
		'wpsp_love',
		array(
			'sanitize_callback' => 'butterbean_validate_boolean',
			'default' => $defaults[ 'wpsp_love' ] ? $defaults[ 'wpsp_love' ] : false
		)
	);

	$manager->register_control(
        'wpsp_social_sharing_alignment', // Same as setting name.
        array(
            'type'    => 'select',
            'section' => 'wpsp_social',
            'label'   => esc_html__( 'Alignment', 'wp-show-posts-pro' ),
            'choices' => array(
				'left' => __( 'Left','wp-show-posts-pro' ),
				'center' => __( 'Center','wp-show-posts-pro' ),
				'right' => __( 'Right','wp-show-posts-pro' ),
			),
			'attr' => array( 'id' => 'wpsp-social-sharing-alignment' )
        )
    );

	$manager->register_setting(
        'wpsp_social_sharing_alignment', // Same as control name.
        array(
            'sanitize_callback' => 'sanitize_text_field',
			'default' => $defaults[ 'wpsp_social_sharing_alignment' ] ? $defaults[ 'wpsp_social_sharing_alignment' ] : 'right'
        )
    );
}

if ( ! function_exists( 'wpsp_social_sharing' ) ) {
	function wpsp_social_sharing( $id, $twitter, $facebook, $pinterest, $love, $social_sharing_alignment ) {
		// Get current page URL
		$url = esc_url( get_permalink( $id ) );

		// Get current page title
		$title = str_replace( ' ', '%20', get_the_title( $id ));

		// Get Post Thumbnail for pinterest
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'full' );

		// Construct sharing URL without using any script
		$twitterURL = 'https://twitter.com/intent/tweet?text='.$title.'&amp;url='.$url;
		$facebookURL = 'https://www.facebook.com/sharer/sharer.php?u='.$url;
		$pinterestURL = 'https://pinterest.com/pin/create/button/?url='.$url.'&amp;media='.$image[0].'&amp;description='.$title;

		// Construct our buttons
		if ( $twitter || $facebook || $pinterest || $love ) {
			echo '<div class="wpsp-social wpsp-social-' . esc_attr( $social_sharing_alignment ) . '">';
		}

			if ( $twitter ) {
				printf(
					'<a title="%1$s" class="wpsp-social-link wpsp-twitter" href="%2$s" %3$s">
						<span class="screen-reader-text">%4$s</span>
					</a>',
					esc_attr__( 'Twitter','wp-show-posts-pro' ),
					esc_url( $twitterURL ),
					'onclick="window.open(this.href, \'twitterwindow\',\'left=20,top=20,width=600,height=300,toolbar=0,resizable=1\'); return false;',
					__( 'Twitter','wp-show-posts-pro' )
				);
			}

			if ( $facebook ) {
				printf(
					'<a title="%1$s" class="wpsp-social-link wpsp-facebook" href="%2$s" %3$s">
						<span class="screen-reader-text">%4$s</span>
					</a>',
					esc_attr__( 'Facebook','wp-show-posts-pro' ),
					esc_url( $facebookURL ),
					'onclick="window.open(this.href, \'facebookwindow\',\'left=20,top=20,width=600,height=700,toolbar=0,resizable=1\'); return false;',
					__( 'Facebook','wp-show-posts-pro' )
				);
			}

			if ( $pinterest ) {
				printf(
					'<a title="%1$s" class="wpsp-social-link wpsp-pinterest" href="%2$s" %3$s">
						<span class="screen-reader-text">%4$s</span>
					</a>',
					esc_attr__( 'Pinterest','wp-show-posts-pro' ),
					esc_url( $pinterestURL ),
					'target="_blank"',
					__( 'Pinterest','wp-show-posts-pro' )
				);
			}

			if ( $love ) {
				echo wpsp_get_love_button( $id );
			}

			do_action( 'wpsp_social_icons' );

		if ( $twitter || $facebook || $pinterest || $love ) {
			echo '</div>';
		}
	}
}

add_filter( 'wpsp_settings', 'wpsp_pro_social_sharing_settings' );
function wpsp_pro_social_sharing_settings( $settings ) {
	$settings[ 'social_sharing' ] 			= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_social_sharing' );
	$settings[ 'social_sharing_alignment' ] = wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_social_sharing_alignment' );
	$settings[ 'twitter' ]		  			= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_twitter' );
	$settings[ 'facebook' ]		  			= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_facebook' );
	$settings[ 'pinterest' ]		  		= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_pinterest' );
	$settings[ 'love' ]			  			= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_love' );

	return $settings;
}

add_action( 'wpsp_after_content', 'wpsp_add_social_sharing', 10 );
function wpsp_add_social_sharing( $settings ) {
	if ( $settings[ 'social_sharing' ] ) {
		echo wpsp_social_sharing( get_the_ID(), $settings[ 'twitter' ], $settings[ 'facebook' ], $settings[ 'pinterest' ], $settings[ 'love' ], $settings[ 'social_sharing_alignment' ] );

		if ( $settings[ 'love' ] ) {
			wp_enqueue_script( 'wpsp-love-it' );
		}
	}
}
