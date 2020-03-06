<?php

add_action( 'init', 'ninja_forms_register_tab_style_field_type_settings' );
function ninja_forms_register_tab_style_field_type_settings(){
	$args = array(
		'name' => __( 'Field Type Styles', 'ninja-forms-style' ),
		'page' => 'ninja-forms-style',
		'display_function' => 'ninja_forms_style_advanced_checkbox_display',
		'save_function' => 'ninja_forms_save_style_field_type_settings',
		'show_save' => true,
	);
	if( !isset( $_REQUEST['field_type'] ) OR $_REQUEST['field_type'] == '' ){
		$args['show_save'] = false;
		unset( $args['display_function'] );
	}
	if( function_exists( 'ninja_forms_register_tab' ) ){
		ninja_forms_register_tab( 'field_type_settings', $args );
	}
}

add_action( 'init', 'ninja_forms_register_style_field_type_metaboxes', 1001 );
function ninja_forms_register_style_field_type_metaboxes(){

	if( is_admin() ){
		if( isset( $_REQUEST['field_type'] ) AND $_REQUEST['field_type'] != '' ) {
			if( $_REQUEST['field_type'] != '_hr' && $_REQUEST['field_type'] != '_desc' ) {
				$args = array(
					'page' => 'ninja-forms-style',
					'tab' => 'field_type_settings',
					'slug' => 'wrap',
					'title' => __( 'Wrap Styles', 'ninja-forms-style' ),
					'state' => 'closed',
					'display_function' => 'ninja_forms_style_field_type_wrap_display',
					'save_page' => 'field_type',
					'css_selector' => '.ninja-forms-form div.[type_slug]-wrap.field-wrap',
				);

				if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
					ninja_forms_register_tab_metabox($args);
				}
				if ( $_REQUEST['field_type'] != '_profile_pass' ) {
					$args = array(
						'page' => 'ninja-forms-style',
						'tab' => 'field_type_settings',
						'slug' => 'label',
						'title' => __( 'Label Styles', 'ninja-forms-style' ),
						'state' => 'closed',
						'display_function' => 'ninja_forms_style_field_type_label_display',
						'save_page' => 'field_type',
						'css_selector' => '.ninja-forms-form div.[type_slug]-wrap.field-wrap label',
					);

					if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
						ninja_forms_register_tab_metabox($args);
					}
				}

				if ( $_REQUEST['field_type'] != '_rating' AND $_REQUEST['field_type'] != '_profile_pass'  ) {
					$args = array(
						'page' => 'ninja-forms-style',
						'tab' => 'field_type_settings',
						'slug' => 'field',
						'title' => __( 'Element Styles', 'ninja-forms-style'),
						'state' => 'closed',
						'display_function' => 'ninja_forms_style_field_type_field_display',
						'save_page' => 'field_type',
						'css_selector' => '.ninja-forms-form div.[type_slug]-wrap.field-wrap .ninja-forms-field',
					);

					if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
						ninja_forms_register_tab_metabox($args);
					}
				}
			}


			if( $_REQUEST['field_type'] == '_hr' ){
				$args = array(
					'page' => 'ninja-forms-style',
					'tab' => 'field_type_settings',
					'slug' => 'hr-element',
					'title' => __( 'HR Element', 'ninja-forms-style'),
					'state' => 'closed',
					'display_function' => 'ninja_forms_style_field_type_field_display',
					'save_page' => 'field_type',
					'css_selector' => 'hr.ninja-forms-field',
				);
				if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
					ninja_forms_register_tab_metabox( $args );
				}
			}

			if( $_REQUEST['field_type'] == '_rating' ){
				$args = array(
					'page' => 'ninja-forms-style',
					'tab' => 'field_type_settings',
					'slug' => 'rating-item',
					'title' => __( 'Rating Item', 'ninja-forms-style'),
					'state' => 'closed',
					'display_function' => 'ninja_forms_style_field_type_field_display',
					'save_page' => 'field_type',
					'css_selector' => 'div.ninja-forms-star-rating a',
				);
				if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
					ninja_forms_register_tab_metabox( $args );
				}
				$args = array(
					'page' => 'ninja-forms-style',
					'tab' => 'field_type_settings',
					'slug' => 'rating-item-hover',
					'title' => __( 'Rating Item Hover', 'ninja-forms-style'),
					'state' => 'closed',
					'display_function' => 'ninja_forms_style_field_type_field_display',
					'save_page' => 'field_type',
					'css_selector' => 'div.ninja-forms-star-rating-hover a',
				);
				if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
					ninja_forms_register_tab_metabox( $args );
				}
				$args = array(
					'page' => 'ninja-forms-style',
					'tab' => 'field_type_settings',
					'slug' => 'rating-item-selected',
					'title' => __( 'Rating Item Selected', 'ninja-forms-style'),
					'state' => 'closed',
					'display_function' => 'ninja_forms_style_field_type_field_display',
					'save_page' => 'field_type',
					'css_selector' => 'div.ninja-forms-star-rating-on a',
				);
				if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
					ninja_forms_register_tab_metabox( $args );
				}
				$args = array(
					'page' => 'ninja-forms-style',
					'tab' => 'field_type_settings',
					'slug' => 'rating-cancel',
					'title' => __( 'Cancel Ratings', 'ninja-forms-style'),
					'state' => 'closed',
					'display_function' => 'ninja_forms_style_field_type_field_display',
					'save_page' => 'field_type',
					'css_selector' => 'div.rating-cancel a',
				);
				if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
					ninja_forms_register_tab_metabox( $args );
				}
				$args = array(
					'page' => 'ninja-forms-style',
					'tab' => 'field_type_settings',
					'slug' => 'rating-cancel-hover',
					'title' => __( 'Cancel Ratings Hover', 'ninja-forms-style'),
					'state' => 'closed',
					'display_function' => 'ninja_forms_style_field_type_field_display',
					'save_page' => 'field_type',
					'css_selector' => 'div.rating-cancel a:hover',
				);
				if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
					ninja_forms_register_tab_metabox( $args );
				}
			}

			if( $_REQUEST['field_type'] == '_profile_pass' ){
				$args = array(
					'page' => 'ninja-forms-style',
					'tab' => 'field_type_settings',
					'slug' => 'password-wrap',
					'title' => __( 'Password Wrap', 'ninja-forms-style'),
					'state' => 'closed',
					'display_function' => 'ninja_forms_style_field_type_field_display',
					'save_page' => 'field_type',
					'css_selector' => 'div.ninja-forms-pass1',
				);
				if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
					ninja_forms_register_tab_metabox( $args );
				}

				$args = array(
					'page' => 'ninja-forms-style',
					'tab' => 'field_type_settings',
					'slug' => 'password-label',
					'title' => __( 'Password Label', 'ninja-forms-style'),
					'state' => 'closed',
					'display_function' => 'ninja_forms_style_field_type_field_display',
					'save_page' => 'field_type',
					'css_selector' => 'div.ninja-forms-pass1 label',
				);
				if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
					ninja_forms_register_tab_metabox( $args );
				}

				$args = array(
					'page' => 'ninja-forms-style',
					'tab' => 'field_type_settings',
					'slug' => 'password-element',
					'title' => __( 'Password Field', 'ninja-forms-style'),
					'state' => 'closed',
					'display_function' => 'ninja_forms_style_field_type_field_display',
					'save_page' => 'field_type',
					'css_selector' => 'div.ninja-forms-pass1 input[type=password]',
				);
				if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
					ninja_forms_register_tab_metabox( $args );
				}
				$args = array(
					'page' => 'ninja-forms-style',
					'tab' => 'field_type_settings',
					'slug' => 'confirm-wrap',
					'title' => __( 'Confirm Password Wrap', 'ninja-forms-style'),
					'state' => 'closed',
					'display_function' => 'ninja_forms_style_field_type_field_display',
					'save_page' => 'field_type',
					'css_selector' => 'div.ninja-forms-pass2',
				);
				if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
					ninja_forms_register_tab_metabox( $args );
				}

				$args = array(
					'page' => 'ninja-forms-style',
					'tab' => 'field_type_settings',
					'slug' => 'confirm-label',
					'title' => __( 'Confirm Password Label', 'ninja-forms-style'),
					'state' => 'closed',
					'display_function' => 'ninja_forms_style_field_type_field_display',
					'save_page' => 'field_type',
					'css_selector' => 'div.ninja-forms-pass2 label',
				);
				if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
					ninja_forms_register_tab_metabox( $args );
				}

				$args = array(
					'page' => 'ninja-forms-style',
					'tab' => 'field_type_settings',
					'slug' => 'confirm-element',
					'title' => __( 'Confirm Password Field', 'ninja-forms-style'),
					'state' => 'closed',
					'display_function' => 'ninja_forms_style_field_type_field_display',
					'save_page' => 'field_type',
					'css_selector' => 'div.ninja-forms-pass2 input[type=password]',
				);
				if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
					ninja_forms_register_tab_metabox( $args );
				}

				$args = array(
					'page' => 'ninja-forms-style',
					'tab' => 'field_type_settings',
					'slug' => 'strength-indicator',
					'title' => __( 'Strength Indicator', 'ninja-forms-style'),
					'state' => 'closed',
					'display_function' => 'ninja_forms_style_field_type_field_display',
					'save_page' => 'field_type',
					'css_selector' => '.ninja-forms-form div.[type_slug]-wrap #pass-strength-result',
				);
				if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
					ninja_forms_register_tab_metabox( $args );
				}

				$args = array(
					'page' => 'ninja-forms-style',
					'tab' => 'field_type_settings',
					'slug' => 'strength-indicator-short',
					'title' => __( 'Strength Indicator - Very Weak', 'ninja-forms-style'),
					'state' => 'closed',
					'display_function' => 'ninja_forms_style_field_type_field_display',
					'save_page' => 'field_type',
					'css_selector' => '.ninja-forms-form div.[type_slug]-wrap #pass-strength-result.short',
				);
				if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
					ninja_forms_register_tab_metabox( $args );
				}

				$args = array(
					'page' => 'ninja-forms-style',
					'tab' => 'field_type_settings',
					'slug' => 'strength-indicator-bad',
					'title' => __( 'Strength Indicator - Weak', 'ninja-forms-style'),
					'state' => 'closed',
					'display_function' => 'ninja_forms_style_field_type_field_display',
					'save_page' => 'field_type',
					'css_selector' => '.ninja-forms-form div.[type_slug]-wrap #pass-strength-result.bad',
				);
				if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
					ninja_forms_register_tab_metabox( $args );
				}

				$args = array(
					'page' => 'ninja-forms-style',
					'tab' => 'field_type_settings',
					'slug' => 'strength-indicator-good',
					'title' => __( 'Strength Indicator - Medium', 'ninja-forms-style'),
					'state' => 'closed',
					'display_function' => 'ninja_forms_style_field_type_field_display',
					'save_page' => 'field_type',
					'css_selector' => '.ninja-forms-form div.[type_slug]-wrap #pass-strength-result.good',
				);
				if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
					ninja_forms_register_tab_metabox( $args );
				}

				$args = array(
					'page' => 'ninja-forms-style',
					'tab' => 'field_type_settings',
					'slug' => 'strength-indicator-strong',
					'title' => __( 'Strength Indicator - Strong', 'ninja-forms-style'),
					'state' => 'closed',
					'display_function' => 'ninja_forms_style_field_type_field_display',
					'save_page' => 'field_type',
					'css_selector' => '.ninja-forms-form div.[type_slug]-wrap #pass-strength-result.strong',
				);
				if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
					ninja_forms_register_tab_metabox( $args );
				}

				$args = array(
					'page' => 'ninja-forms-style',
					'tab' => 'field_type_settings',
					'slug' => 'strength-indicator-hint',
					'title' => __( 'Strength Indicator - Hint', 'ninja-forms-style'),
					'state' => 'closed',
					'display_function' => 'ninja_forms_style_field_type_field_display',
					'save_page' => 'field_type',
					'css_selector' => '.ninja-forms-form div.[type_slug]-wrap .indicator-hint',
				);
				if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
					ninja_forms_register_tab_metabox( $args );
				}
			}

			if( $_REQUEST['field_type'] == '_list-radio' || $_REQUEST['field_type'] == '_list-checkbox' ){
				$args = array(
					'page' => 'ninja-forms-style',
					'tab' => 'field_type_settings',
					'slug' => 'list-item-row',
					'title' => __( 'List Item Row', 'ninja-forms-style'),
					'state' => 'closed',
					'display_function' => 'ninja_forms_style_field_type_field_display',
					'save_page' => 'field_type',
					'css_selector' => 'div.[type_slug]-wrap ul li',
				);
				if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
					ninja_forms_register_tab_metabox($args);
				}

				$args = array(
					'page' => 'ninja-forms-style',
					'tab' => 'field_type_settings',
					'slug' => 'list-item-label',
					'title' => __( 'List Item Label', 'ninja-forms-style'),
					'state' => 'closed',
					'display_function' => 'ninja_forms_style_field_type_field_display',
					'save_page' => 'field_type',
					'css_selector' => 'div.[type_slug]-wrap ul li label',
				);
				if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
					ninja_forms_register_tab_metabox($args);
				}

				$args = array(
					'page' => 'ninja-forms-style',
					'tab' => 'field_type_settings',
					'slug' => 'list-item-element',
					'title' => __( 'List Item Element', 'ninja-forms-style'),
					'state' => 'closed',
					'display_function' => 'ninja_forms_style_field_type_field_display',
					'save_page' => 'field_type',
					'css_selector' => 'div.[type_slug]-wrap ul li input',
				);
				if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
					ninja_forms_register_tab_metabox($args);
				}
			}

			if( $_REQUEST['field_type'] == '_submit' ){
				$args = array(
					'page' => 'ninja-forms-style',
					'tab' => 'field_type_settings',
					'slug' => 'submit-hover',
					'title' => __( 'Element Hover Styles', 'ninja-forms-style'),
					'state' => 'closed',
					'display_function' => 'ninja_forms_style_field_type_field_display',
					'save_page' => 'field_type',
					'css_selector' => 'submit_hover',
				);
				if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
					ninja_forms_register_tab_metabox($args);
				}
			}

			if ( $_REQUEST['field_type'] == '_desc' ) {
					$args = array(
						'page' => 'ninja-forms-style',
						'tab' => 'field_type_settings',
						'slug' => 'desc_field',
						'title' => __( 'Element Styles', 'ninja-forms-style'),
						'state' => 'closed',
						'display_function' => 'ninja_forms_style_field_type_field_display',
						'save_page' => 'field_type',
						'css_selector' => '.ninja-forms-field .nf-[type_slug]',
					);

					if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
						ninja_forms_register_tab_metabox($args);
					}
			}
		}
	}else{
		$args = array(
			'page' => 'ninja-forms-style',
			'tab' => 'field_type_settings',
			'slug' => 'wrap',
			'title' => __( 'Wrap Styles', 'ninja-forms-style' ),
			'state' => 'closed',
			'display_function' => 'ninja_forms_style_field_type_wrap_display',
			'save_page' => 'field_type',
			'css_selector' => '.ninja-forms-form div.[type_slug]-wrap.field-wrap',
		);

		if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
			ninja_forms_register_tab_metabox($args);
		}

		$args = array(
			'page' => 'ninja-forms-style',
			'tab' => 'field_type_settings',
			'slug' => 'label',
			'title' => __( 'Label Styles', 'ninja-forms-style' ),
			'state' => 'closed',
			'display_function' => 'ninja_forms_style_field_type_label_display',
			'save_page' => 'field_type',
			'css_selector' => '.ninja-forms-form div.[type_slug]-wrap.field-wrap label',
		);

		if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
			ninja_forms_register_tab_metabox($args);
		}

		$args = array(
			'page' => 'ninja-forms-style',
			'tab' => 'field_type_settings',
			'slug' => 'field',
			'title' => __( 'Element Styles', 'ninja-forms-style'),
			'state' => 'closed',
			'display_function' => 'ninja_forms_style_field_type_field_display',
			'save_page' => 'field_type',
			'css_selector' => '.ninja-forms-form div.[type_slug]-wrap.field-wrap .ninja-forms-field',
		);

		if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
			ninja_forms_register_tab_metabox($args);
		}

		$args = array(
			'page' => 'ninja-forms-style',
			'tab' => 'field_type_settings',
			'slug' => 'list-item-row',
			'title' => __( 'List Item Row', 'ninja-forms-style'),
			'state' => 'closed',
			'display_function' => 'ninja_forms_style_field_type_field_display',
			'save_page' => 'field_type',
			'css_selector' => 'div.[type_slug]-wrap ul li',
		);
		if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
			ninja_forms_register_tab_metabox($args);
		}

		$args = array(
			'page' => 'ninja-forms-style',
			'tab' => 'field_type_settings',
			'slug' => 'list-item-label',
			'title' => __( 'List Item Label', 'ninja-forms-style'),
			'state' => 'closed',
			'display_function' => 'ninja_forms_style_field_type_field_display',
			'save_page' => 'field_type',
			'css_selector' => 'div.[type_slug]-wrap ul li label',
		);
		if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
			ninja_forms_register_tab_metabox($args);
		}

		$args = array(
			'page' => 'ninja-forms-style',
			'tab' => 'field_type_settings',
			'slug' => 'list-item-element',
			'title' => __( 'List Item Element', 'ninja-forms-style'),
			'state' => 'closed',
			'display_function' => 'ninja_forms_style_field_type_field_display',
			'save_page' => 'field_type',
			'css_selector' => 'div.[type_slug]-wrap ul li input',
		);
		if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
			ninja_forms_register_tab_metabox($args);
		}

		$args = array(
			'page' => 'ninja-forms-style',
			'tab' => 'field_type_settings',
			'slug' => 'submit-hover',
			'title' => __( 'Element Hover Styles', 'ninja-forms-style'),
			'state' => 'closed',
			'display_function' => 'ninja_forms_style_field_type_field_display',
			'save_page' => 'field_type',
			'css_selector' => 'submit_hover',
		);
		if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
			ninja_forms_register_tab_metabox($args);
		}

		$args = array(
			'page' => 'ninja-forms-style',
			'tab' => 'field_type_settings',
			'slug' => 'hr-element',
			'title' => __( 'HR Element', 'ninja-forms-style'),
			'state' => 'closed',
			'display_function' => 'ninja_forms_style_field_type_field_display',
			'save_page' => 'field_type',
			'css_selector' => 'hr.ninja-forms-field',
		);
		if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
			ninja_forms_register_tab_metabox($args);
		}

		$args = array(
			'page' => 'ninja-forms-style',
			'tab' => 'field_type_settings',
			'slug' => 'rating-item',
			'title' => __( 'Rating Item', 'ninja-forms-style'),
			'state' => 'closed',
			'display_function' => 'ninja_forms_style_field_type_field_display',
			'save_page' => 'field_type',
			'css_selector' => 'div.ninja-forms-star-rating a',
		);
		if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
			ninja_forms_register_tab_metabox( $args );
		}
		$args = array(
			'page' => 'ninja-forms-style',
			'tab' => 'field_type_settings',
			'slug' => 'rating-item-hover',
			'title' => __( 'Rating Item Hover', 'ninja-forms-style'),
			'state' => 'closed',
			'display_function' => 'ninja_forms_style_field_type_field_display',
			'save_page' => 'field_type',
			'css_selector' => 'div.ninja-forms-star-rating-hover a',
		);
		if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
			ninja_forms_register_tab_metabox( $args );
		}
		$args = array(
			'page' => 'ninja-forms-style',
			'tab' => 'field_type_settings',
			'slug' => 'rating-item-selected',
			'title' => __( 'Rating Item Selected', 'ninja-forms-style'),
			'state' => 'closed',
			'display_function' => 'ninja_forms_style_field_type_field_display',
			'save_page' => 'field_type',
			'css_selector' => 'div.ninja-forms-star-rating-on a',
		);
		if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
			ninja_forms_register_tab_metabox( $args );
		}
		$args = array(
			'page' => 'ninja-forms-style',
			'tab' => 'field_type_settings',
			'slug' => 'rating-cancel',
			'title' => __( 'Cancel Ratings', 'ninja-forms-style'),
			'state' => 'closed',
			'display_function' => 'ninja_forms_style_field_type_field_display',
			'save_page' => 'field_type',
			'css_selector' => 'div.rating-cancel a',
		);
		if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
			ninja_forms_register_tab_metabox( $args );
		}
		$args = array(
			'page' => 'ninja-forms-style',
			'tab' => 'field_type_settings',
			'slug' => 'rating-cancel-hover',
			'title' => __( 'Cancel Ratings Hover', 'ninja-forms-style'),
			'state' => 'closed',
			'display_function' => 'ninja_forms_style_field_type_field_display',
			'save_page' => 'field_type',
			'css_selector' => 'div.rating-cancel a:hover',
		);
		if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
			ninja_forms_register_tab_metabox( $args );
		}
		$args = array(
			'page' => 'ninja-forms-style',
			'tab' => 'field_type_settings',
			'slug' => 'password-wrap',
			'title' => __( 'Password Wrap', 'ninja-forms-style'),
			'state' => 'closed',
			'display_function' => 'ninja_forms_style_field_type_field_display',
			'save_page' => 'field_type',
			'css_selector' => 'div.ninja-forms-pass1',
		);
		if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
			ninja_forms_register_tab_metabox( $args );
		}

		$args = array(
			'page' => 'ninja-forms-style',
			'tab' => 'field_type_settings',
			'slug' => 'password-label',
			'title' => __( 'Password Label', 'ninja-forms-style'),
			'state' => 'closed',
			'display_function' => 'ninja_forms_style_field_type_field_display',
			'save_page' => 'field_type',
			'css_selector' => 'div.ninja-forms-pass1 label',
		);
		if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
			ninja_forms_register_tab_metabox( $args );
		}

		$args = array(
			'page' => 'ninja-forms-style',
			'tab' => 'field_type_settings',
			'slug' => 'password-element',
			'title' => __( 'Password Field', 'ninja-forms-style'),
			'state' => 'closed',
			'display_function' => 'ninja_forms_style_field_type_field_display',
			'save_page' => 'field_type',
			'css_selector' => 'div.ninja-forms-pass1 input[type=password]',
		);
		if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
			ninja_forms_register_tab_metabox( $args );
		}
		$args = array(
			'page' => 'ninja-forms-style',
			'tab' => 'field_type_settings',
			'slug' => 'confirm-wrap',
			'title' => __( 'Confirm Password Wrap', 'ninja-forms-style'),
			'state' => 'closed',
			'display_function' => 'ninja_forms_style_field_type_field_display',
			'save_page' => 'field_type',
			'css_selector' => 'div.ninja-forms-pass2',
		);
		if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
			ninja_forms_register_tab_metabox( $args );
		}

		$args = array(
			'page' => 'ninja-forms-style',
			'tab' => 'field_type_settings',
			'slug' => 'confirm-label',
			'title' => __( 'Confirm Password Label', 'ninja-forms-style'),
			'state' => 'closed',
			'display_function' => 'ninja_forms_style_field_type_field_display',
			'save_page' => 'field_type',
			'css_selector' => 'div.ninja-forms-pass2 label',
		);
		if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
			ninja_forms_register_tab_metabox( $args );
		}

		$args = array(
			'page' => 'ninja-forms-style',
			'tab' => 'field_type_settings',
			'slug' => 'confirm-element',
			'title' => __( 'Confirm Password Field', 'ninja-forms-style'),
			'state' => 'closed',
			'display_function' => 'ninja_forms_style_field_type_field_display',
			'save_page' => 'field_type',
			'css_selector' => 'div.ninja-forms-pass2 input[type=password]',
		);
		if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
			ninja_forms_register_tab_metabox( $args );
		}

		$args = array(
			'page' => 'ninja-forms-style',
			'tab' => 'field_type_settings',
			'slug' => 'strength-indicator',
			'title' => __( 'Strength Indicator', 'ninja-forms-style'),
			'state' => 'closed',
			'display_function' => 'ninja_forms_style_field_type_field_display',
			'save_page' => 'field_type',
			'css_selector' => '.ninja-forms-form div.[type_slug]-wrap #pass-strength-result',
		);
		if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
			ninja_forms_register_tab_metabox( $args );
		}

		$args = array(
			'page' => 'ninja-forms-style',
			'tab' => 'field_type_settings',
			'slug' => 'strength-indicator-short',
			'title' => __( 'Strength Indicator - Very Weak', 'ninja-forms-style'),
			'state' => 'closed',
			'display_function' => 'ninja_forms_style_field_type_field_display',
			'save_page' => 'field_type',
			'css_selector' => '.ninja-forms-form div.[type_slug]-wrap #pass-strength-result.short',
		);
		if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
			ninja_forms_register_tab_metabox( $args );
		}

		$args = array(
			'page' => 'ninja-forms-style',
			'tab' => 'field_type_settings',
			'slug' => 'strength-indicator-bad',
			'title' => __( 'Strength Indicator - Weak', 'ninja-forms-style'),
			'state' => 'closed',
			'display_function' => 'ninja_forms_style_field_type_field_display',
			'save_page' => 'field_type',
			'css_selector' => '.ninja-forms-form div.[type_slug]-wrap #pass-strength-result.bad',
		);
		if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
			ninja_forms_register_tab_metabox( $args );
		}

		$args = array(
			'page' => 'ninja-forms-style',
			'tab' => 'field_type_settings',
			'slug' => 'strength-indicator-good',
			'title' => __( 'Strength Indicator - Medium', 'ninja-forms-style'),
			'state' => 'closed',
			'display_function' => 'ninja_forms_style_field_type_field_display',
			'save_page' => 'field_type',
			'css_selector' => '.ninja-forms-form div.[type_slug]-wrap #pass-strength-result.good',
		);
		if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
			ninja_forms_register_tab_metabox( $args );
		}

		$args = array(
			'page' => 'ninja-forms-style',
			'tab' => 'field_type_settings',
			'slug' => 'strength-indicator-strong',
			'title' => __( 'Strength Indicator - Strong', 'ninja-forms-style'),
			'state' => 'closed',
			'display_function' => 'ninja_forms_style_field_type_field_display',
			'save_page' => 'field_type',
			'css_selector' => '.ninja-forms-form div.[type_slug]-wrap #pass-strength-result.strong',
		);
		if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
			ninja_forms_register_tab_metabox( $args );
		}

		$args = array(
			'page' => 'ninja-forms-style',
			'tab' => 'field_type_settings',
			'slug' => 'strength-indicator-hint',
			'title' => __( 'Strength Indicator - Hint', 'ninja-forms-style'),
			'state' => 'closed',
			'display_function' => 'ninja_forms_style_field_type_field_display',
			'save_page' => 'field_type',
			'css_selector' => '.ninja-forms-form div.[type_slug]-wrap .indicator-hint',
		);
		if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
			ninja_forms_register_tab_metabox( $args );
		}

		$args = array(
			'page' => 'ninja-forms-style',
			'tab' => 'field_type_settings',
			'slug' => 'desc_field',
			'title' => __( 'Element Styles', 'ninja-forms-style'),
			'state' => 'closed',
			'display_function' => 'ninja_forms_style_field_type_field_display',
			'save_page' => 'field_type',
			'css_selector' => '.nf-[type_slug]',
		);

		if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
			ninja_forms_register_tab_metabox($args);
		}
	}
}

function ninja_forms_style_field_type_wrap_display( $metabox ){
	if( isset( $_REQUEST['field_type'] ) AND $_REQUEST['field_type'] != '' ){
		$field_type = $_REQUEST['field_type'];
		$metabox['field_type'] = $field_type;
		ninja_forms_style_metabox_output( $metabox );
	}else{
		$field_type = '';
	}
}

function ninja_forms_style_field_type_label_display( $metabox ){
	if( isset( $_REQUEST['field_type'] ) AND $_REQUEST['field_type'] != '' ){
		$field_type = $_REQUEST['field_type'];
		$metabox['field_type'] = $field_type;
		ninja_forms_style_metabox_output( $metabox );
	}else{
		$field_type = '';
	}
}

function ninja_forms_style_field_type_field_display( $metabox ){
	if( isset( $_REQUEST['field_type'] ) AND $_REQUEST['field_type'] != '' ){
		$field_type = $_REQUEST['field_type'];
		$metabox['field_type'] = $field_type;
		ninja_forms_style_metabox_output( $metabox );
	}else{
		$field_type = '';
	}
}

function ninja_forms_save_style_field_type_settings( $data ){
	$plugin_settings = get_option( 'ninja_forms_settings' );
	$tmp_array = array();
	$field_type = $data['field_type'];
	unset( $data['field_type'] );

	$plugin_settings['style']['field_type'][$field_type] = $data;

	update_option( 'ninja_forms_settings', $plugin_settings);
}
