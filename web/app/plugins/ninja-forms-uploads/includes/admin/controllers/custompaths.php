<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NF_FU_Admin_Controllers_CustomPaths {

	public $identifier = '%';

	public $data = array();

	/**
	 * Get all shortcodes and descriptions
	 *
	 * @return array
	 */
	public function get_shortcodes() {
		$shortcodes = array(
			'filename'    => __( 'Puts in the title of the file without any spaces', 'ninja-forms-uploads' ),
			'formtitle'   => __( 'Puts in the title of the current form without any spaces', 'ninja-forms-uploads' ),
			'date'        => __( 'Puts in the date in yyyy-mm-dd (1998-05-23) format', 'ninja-forms-uploads' ),
			'month'       => __( 'Puts in the month in mm (04) format', 'ninja-forms-uploads' ),
			'day'         => __( 'Puts in the day in dd (20) format', 'ninja-forms-uploads' ),
			'year'        => __( 'Puts in the year in yyyy (2011) format', 'ninja-forms-uploads' ),
			'username'    => __( 'Puts in the user\'s username if they are logged in', 'ninja-forms-uploads' ),
			'userid'      => __( 'Puts in the user\'s user ID if they are logged in', 'ninja-forms-uploads' ),
			'displayname' => __( 'Puts in the user\'s display name if they are logged in', 'ninja-forms-uploads' ),
			'firstname'   => __( 'Puts in the user\'s first name if they are logged in', 'ninja-forms-uploads' ),
			'lastname'    => __( 'Puts in the user\'s last name if they are logged in', 'ninja-forms-uploads' ),
			'random'      => __( 'Puts in a random 5 character string', 'ninja-forms-uploads' ),
		);

		return $shortcodes;
	}

	/**
	 * Set data to be used in the replacing of shortcodes
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function set_data( $key, $value ) {
		$this->data[$key] = sanitize_file_name( $value );
	}

	/**
	 * Get shortcode as it appears in a string
	 *
	 * @param $shortcode
	 *
	 * @return string
	 */
	protected function get_shortcode_display( $shortcode ) {
		return $this->identifier . $shortcode . $this->identifier;
	}

	/**
	 * Replace shortcodes with values in a string
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public function replace_shortcodes( $string ) {
		$shortcodes = self::get_shortcodes();
		foreach ( $shortcodes as $shortcode => $descp ) {
			$string = $this->replace_shortcode( $string, $shortcode );
		}

		return $string;
	}

	/**
	 * Replace the custom filename with values of fields submitted using ID and Key
	 *
	 * @param string $string
	 * @param array $fields
	 *
	 * @return string
	 */
	public function replace_field_shortcodes( $string, $fields ) {
		foreach( $fields as $field ) {
			$find   = array();
			$find[] = $this->identifier . 'field_' . $field['id'] . $this->identifier;

			$key = isset( $field['key'] ) ? $field['key'] : false;
			if ( ! $key ) {
				$key = Ninja_Forms()->form()->get_field( $field['id'] )->get_setting( 'key' );
			}

			$find[] = $this->identifier . 'field_' . $key . $this->identifier;

			$user_value = $field['value'];
			if ( is_array( $field['value'] ) ) {
				$user_value = implode( ',', $field['value'] );
			}
			$replace = strtolower( sanitize_file_name( trim( $user_value ) ) );
			$string  = str_replace( $find, $replace, $string );
		}

		return $string;
	}

	/**
	 * Replace a single shortcode
	 *
	 * @param string $string
	 * @param string $shortcode
	 *
	 * @return string
	 */
	public function replace_shortcode( $string, $shortcode ) {
		$find    = $this->get_shortcode_display( $shortcode );
		$replace = $this->get_value( $shortcode );

		$string = str_replace( $find, $replace, $string );

		return $string;
	}

	/**
	 * Get the shortcode value
	 *
	 * @param string $shortcode
	 *
	 * @return bool|string
	 */
	public function get_value( $shortcode ) {
		$user_mapping = array(
			'username'    => 'user_nicename',
			'userid'      => 'ID',
			'displayname' => 'display_name',
			'firstname'   => 'user_firstname',
			'lastname'    => 'user_lastname',
		);

		if ( isset( $user_mapping[ $shortcode ] ) ) {
			if ( ! is_user_logged_in() ) {
				return '';
			}

			$current_user = wp_get_current_user();
			$field        = $user_mapping[ $shortcode ];

			return $current_user->{$field};
		}

		if ( in_array( $shortcode, array( 'formtitle', 'filename' ) ) ) {
			/*
			 * If we haven't set the source data for the replacement, just return the shortcode
			 * to be replaced later
			 */
			if ( ! isset( $this->data[ $shortcode ] ) ) {
				return $this->get_shortcode_display( $shortcode );
			}
		}

		switch ( $shortcode ) {
			case 'date':
				$value = date( 'Y-m-d' );
				break;
			case 'month':
				$value = date( 'm' );
				break;
			case 'day':
				$value = date( 'd' );
				break;
			case 'year':
				$value = date( 'Y' );
				break;
			case 'random':
				$value = NF_FU_Helper::random_string( 5 );
				break;
			case 'formtitle':
				$value = sanitize_file_name( sanitize_title( trim( $this->data[ $shortcode ] ) ) );
				break;
			case 'filename':
				$value = sanitize_file_name( sanitize_title( trim( $this->data[ $shortcode ] ) ) );
				break;
			default:
				$value = '';
		}

		return strtolower( $value );
	}
}