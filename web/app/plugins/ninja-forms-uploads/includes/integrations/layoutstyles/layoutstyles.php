<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NF_FU_Integrations_LayoutStyles_LayoutStyles {

	public function __construct() {
		add_filter( 'ninja_forms_styles_field_type_section', array( $this, 'file_upload_field_styles' ) );
		add_filter( 'ninja_forms_field_load_settings', array( $this, 'add_field_settings' ), 11, 3 );
		add_filter( 'ninja_forms_styles_field_settings', array( $this, 'add_field_type_settings' ), 10, 2 );
		add_filter( 'ninja_forms_styles_field_settings_groups', array( $this, 'add_field_settings_groups' ) );
	}

	/**
	 * Customize the styles for the Form level field type styles.
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	public function file_upload_field_styles( $settings ) {
		// Add custom FU styles
		$styles   = NF_File_Uploads()->config( 'layout-styles' );
		$settings = array_merge( $settings, $styles );

		// Add FU support for button hover
		$settings['submit-hover']['only'][] = NF_FU_File_Uploads::TYPE;

		return $settings;
	}

	/**
	 * Allow the File Input field to have the element hover styles for the upload button.
	 *
	 * @param array  $settings
	 * @param string $field_type
	 * @param string $field_parent_type
	 *
	 * @return array
	 */
	public function add_field_settings( $settings, $field_type, $field_parent_type ) {
		if ( NF_FU_File_Uploads::TYPE !== $field_type) {
			return $settings;
		}

		$style_settings = NF_Styles::config( 'ButtonFieldSettings' );

		foreach( $style_settings as $name => $style_setting ){
			$style_setting[ 'group' ] = 'styles';

			foreach( NF_Styles::config( 'CommonSettings' ) as $common_setting ){

				switch( $name ){
					case 'wrap_styles':
						$blacklist = array( 'color', 'font-size', 'height' );
						break;
					case 'label_styles':
					case 'element_styles':
					case 'submit_element_hover_styles':
						$blacklist = array( 'height' );
						break;
					default:
						$blacklist = array();
				}

				if( in_array( $common_setting[ 'name' ], $blacklist ) ) continue;

				$common_setting[ 'name' ] = $name . '_' . $common_setting[ 'name' ];

				if ( isset ( $common_setting[ 'deps' ] ) ) {
					foreach( $common_setting[ 'deps' ] as $dep_name => $val ) {
						$common_setting[ 'deps' ][ $name . '_' . $dep_name ] = $val;
						unset( $common_setting[ 'deps' ][ $dep_name ] );
					}
				}

				$style_setting[ 'settings' ][] = $common_setting;
			}

			$settings[ $name ] = $style_setting;
		}

		return $settings;
	}

	/**
	 * Customize the field specific styles when rendered.
	 *
	 * @param $style_settings
	 * @param $field_type
	 *
	 * @return array
	 */
	public function add_field_type_settings( $style_settings, $field_type ) {
		if ( NF_FU_File_Uploads::TYPE !== $field_type ) {
			return $style_settings;
		}
		$settings       = NF_File_Uploads()->config( 'styles-field-settings' );
		$style_settings = array_merge( $style_settings, $settings );

		return $this->add_field_settings_groups( $style_settings );
	}

	/**
	 * Customize the field specific styles in the builder.
	 *
	 * @param $settings
	 *
	 * @return array
	 */
	public function add_field_settings_groups( $settings ) {
		$field_settings = NF_File_Uploads()->config( 'styles-field-settings' );

		return array_merge( $settings, $field_settings );
	}
}