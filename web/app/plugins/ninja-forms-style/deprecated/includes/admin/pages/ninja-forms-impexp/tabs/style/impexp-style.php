<?php

add_action( 'init', 'ninja_forms_register_tab_impexp_style', 11 );
function ninja_forms_register_tab_impexp_style(){
	$args = array(
		'name' => __( 'Styles', 'ninja-forms-style' ),
		'page' => 'ninja-forms-impexp',
		'display_function' => '',
		'save_function' => 'ninja_forms_save_impexp_style',
		'show_save' => false,
	);
	if( function_exists( 'ninja_forms_register_tab' ) ){
		ninja_forms_register_tab( 'impexp_style', $args );
	}
}

add_action( 'init', 'ninja_forms_register_imp_style_metabox' );
function ninja_forms_register_imp_style_metabox(){
	$args = array(
		'page' => 'ninja-forms-impexp',
		'tab' => 'impexp_style',
		'slug' => 'imp_style',
		'title' => __( 'Import Default Styles', 'ninja-forms-style' ),
		'settings' => array(
			array(
				'name' => 'userfile',
				'type' => 'file',
				'label' => __( 'Select a file', 'ninja-forms-style' ),
				'desc' => '',
				'max_file_size' => 5000000,
				'help_text' => '',
			),
			array(
				'name' => 'submit',
				'type' => 'submit',
				'label' => __( 'Import Styles', 'ninja-forms-style' ),
				'class' => 'button-secondary',
			),
		),
	);
	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}
}

add_action('init', 'ninja_forms_register_exp_style_metabox');
function ninja_forms_register_exp_style_metabox(){

	$args = array(
		'page' => 'ninja-forms-impexp',
		'tab' => 'impexp_style',
		'slug' => 'exp_style',
		'title' => __( 'Export Styles', 'ninja-forms-style' ),
		'settings' => array(
			array(
				'name' => 'submit',
				'type' => 'submit',
				'label' => __( 'Export Styles', 'ninja-forms-style' ),
				'class' => 'button-secondary',
			),
		),
	);
	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}
}

function ninja_forms_save_impexp_style( $data ){
	if( isset( $data['submit'] ) ){
		switch( $data['submit'] ){
			case __( 'Import Styles', 'ninja-forms-style' ):
				if( $_FILES['userfile']['error'] == UPLOAD_ERR_OK AND is_uploaded_file( $_FILES['userfile']['tmp_name'] ) ){
					$plugin_settings = get_option( 'ninja_forms_settings' );
					$file = file_get_contents($_FILES['userfile']['tmp_name']); 
					$style = unserialize( $file );
					$plugin_settings['style'] = $style;
					update_option( 'ninja_forms_settings', $plugin_settings );
					$update_msg = __( 'Styles Imported', 'ninja-forms-style' );
					return $update_msg;
				}
				break;
			case __( 'Export Styles', 'ninja-forms-style' ):
				$download = ninja_forms_export_style();
				$update_msg = __( 'Styles Exported Successfully', 'ninja-forms-style' );
				if( $download == 1 ){
					$update_msg = __( 'No Styles Found', 'ninja-forms-style' );
				}
				return $update_msg;
				break;
		}
	}
}

function ninja_forms_export_style(){
	$plugin_settings = get_option( 'ninja_forms_settings' );
	if( isset( $plugin_settings['style'] ) ){
		$style = $plugin_settings['style'];
	}else{
		$style = '';
	}

	if( $style == '' ){
		return 1;
	}else{

		if(isset($plugin_settings['date_format'])){
			$date_format = $plugin_settings['date_format'];
		}else{
			$date_format = 'm/d/Y';
		}

		$current_time = current_time('timestamp');
		$today = date($date_format, $current_time);

		$style = serialize( $style );
		header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename=ninja-forms-styles-".$today.".nfs");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $style;
		die();		
	}
}