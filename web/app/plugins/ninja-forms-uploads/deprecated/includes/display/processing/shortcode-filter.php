<?php

add_action( 'init', 'ninja_forms_register_uploads_shortcode' );
function ninja_forms_register_uploads_shortcode(){
	add_filter( 'ninja_forms_field_shortcode', 'ninja_forms_uploads_shortcode', 10, 2 );
}

function ninja_forms_uploads_shortcode( $value, $atts ){
	global $ninja_forms_processing;

	if( isset( $atts['method'] ) ){
		$method = $atts['method'];
	}else{
		$method = 'link';
	}
	
	$field_settings = $ninja_forms_processing->get_field_settings( $atts['id'] );

	if( $field_settings['type'] == '_upload' ){
		$tmp_value = '';

		if( is_array( $value ) AND !empty( $value ) ){
			$x = 0;
			foreach( $value as $val ){

				if ( ! isset ( $val['file_url'] ) )
					continue;

				// Make sure we get link to external file if necessary
				$url = ninja_forms_upload_file_url( $val );

				$filename = $val['user_file_name'];
				switch( $method ){
					case 'embed':
						$tmp_value .= "<img src='".$url."'>";
						break;
					case 'link':
						if( $x > 0 ){
							$tmp_value .= ", ";
						}
						$tmp_value .= "<a href='".$url."'>".$filename."</a>";

						break;
					case 'url':
						$tmp_value .= $url;
						break;
				}
				$x++;				
			}
		}
	}else{
		$tmp_value = $value;
	}

	return $tmp_value;
}