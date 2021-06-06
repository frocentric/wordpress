<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Class for our redirect notification type.
 *
 * @package     Ninja Forms - Webhook
 * @subpackage  Classes/Notifications
 * @copyright   Copyright (c) 2015, WPNINJAS
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
class NF_Action_Remote_Get_Post extends NF_Notification_Base_Type {
	/**
	 * Store our current args args
	 */
	var $args = array();
	/**
	 * Get things rolling
	 */
	function __construct() {
		global $pagenow;
		parent::__construct();
		$this->name = __( 'Webhook', 'ninja-forms-wh' );
		add_filter( 'nf_notification_admin_js_vars', array( $this, 'filter_js_vars' ) );
		// Only add these actions if we are actually on the notification tab.
		if ( 'admin.php' == $pagenow && isset ( $_REQUEST['page'] ) && $_REQUEST['page'] == 'ninja-forms' && isset ( $_REQUEST['tab'] ) && $_REQUEST['tab'] == 'notifications' ) {
			add_action( 'admin_init', array( $this, 'add_js' ), 11 );
			add_action( 'admin_init', array( $this, 'add_css' ), 11 );
		}
	}
	/**
	 * Output our edit screen
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function edit_screen( $id = '' ) {
		?>
		<tr>
			<th scope="row"><label for="settings-wh_remote_url"><?php _e( 'Remote Url', 'ninja-forms-wh' ); ?></label></th>
			<td><input type="text" name="settings[wh_remote_url]" id="settings-wh_remote_url" value="<?php echo Ninja_Forms()->notification( $id )->get_setting( 'wh_remote_url' ); ?>" class="regular-text"/></td>
		</tr>
		<tr>
			<th scope="row"><label for="settings-wh_remote_method"><?php _e( 'Remote Method', 'ninja-forms-wh' ); ?></label></th>
			<td>
				<?php $remote_method = Ninja_Forms()->notification( $id )->get_setting( 'wh_remote_method' ); ?>
				<select name="settings[wh_remote_method]" id="wh-remote-method">
					<option value="get" <?php selected( $remote_method, 'get' ); ?>><?php _e( 'Get', 'ninja-forms-wh' ); ?></option>
					<option value="post" <?php selected( $remote_method, 'post' ); ?>><?php _e( 'Post', 'ninja-forms-wh' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e( 'Args', 'ninja-forms-wh' ); ?></th>
			<td>
				<input type="hidden" name="wh_args[]" value="">
				<div id="nf_key_fields" class="nf-wh-argss">
					<div class="nf-wh-argss-title">
						<a href="#" class="nf-wh-args-add button-secondary add"><div class="dashicons dashicons-plus-alt"></div> <?php _e( 'Add Field', 'ninja-forms-wh' ); ?></a>
						<a href="#" class="nf-wh-args-add button-secondary add-all"><div class="dashicons dashicons-plus-alt"></div> <?php _e( 'Add All Fields', 'ninja-forms-wh' ); ?></a>
						<a href="#" class="nf-wh-args-add button-secondary remove-all"><div class="dashicons dashicons-dismiss"></div> <?php _e( 'Remove All Fields', 'ninja-forms-wh' ); ?></a>
					</div>
					<div id="nf_wh_args">

					</div>
				</div>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="wh-json-encode"><?php _e( 'Encode Args as a JSON String', 'ninja-forms-wh' ); ?></label></th>
			<td>
				<?php
				$json_encode = Ninja_Forms()->notification( $id )->get_setting( 'wh_json_encode' );
				?>
				<input type="hidden" value="0" name="settings[wh_json_encode]">
				<input type="checkbox" id="wh-json-encode" value="1" name="settings[wh_json_encode]" <?php checked( $json_encode, 1 ); ?> >
				<span class="howto">{ firstname:"John", lastname:"Doe" }</span>
			</td>
		</tr>
		<?php
		$display_use_arg = ( 1 == $json_encode ) ? '' : 'style="display:none;"';
		?>
		<tr <?php echo $display_use_arg; ?> id="wh-json-use-arg-tr" >
			<th scope="row"><label for="wh-json-use-arg"><?php _e( 'Send JSON String as a Single Variable', 'ninja-forms-wh' ); ?></label></th>
			<td>
				<?php
				$json_use_arg = ( 'get' == $remote_method) ? 1 : Ninja_Forms()->notification( $id )->get_setting( 'wh_json_use_arg' );
				$json_use_arg_disabled = ( 'get' == $remote_method ) ? 'disabled="disabled"' : '';
				?>
				<input type="hidden" value="0" name="settings[wh_json_use_arg]">
				<input type="checkbox" id="wh-json-use-arg" value="1" name="settings[wh_json_use_arg]" <?php checked( $json_use_arg, 1 ); echo $json_use_arg_disabled; ?> >
				<span class="howto">data => { firstname:"John", lastname:"Doe" }</span>
			</td>
		</tr>
		<?php
		$display_arg = ( 1 == $json_encode && 1 == $json_use_arg ) ? '' : 'style="display:none;"';
		?>
		<tr <?php echo $display_arg; ?> id="wh-json-arg-tr">
			<th scope="row"><?php _e( 'JSON Variable Name' ,'ninja-forms-wh' ); ?></th>
			<td>
				<?php
				$json_arg = Ninja_Forms()->notification( $id )->get_setting( 'wh_json_arg' );
				?>
				<input type="text" value="<?php echo $json_arg; ?>" name="settings[wh_json_arg]">
				<span class="howto"><?php _e( 'All the args in the table above will be encoded as a JSON string and passed with this variable name.', 'ninja-forms-wh' ); ?></span>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="wh-debug"><?php _e( 'Run in Debug Mode', 'ninja-forms-wh' ); ?></label></th>
			<td>
				<?php
				$debug = Ninja_Forms()->notification( $id )->get_setting( 'wh_debug' );
				?>
				<input type="hidden" value="0" name="settings[wh_debug]">
				<input type="checkbox" id="wh-debug" value="1" name="settings[wh_debug]" <?php checked( $debug, 1 ); ?> >
				<span class="howto"><?php _e( 'This will terminate the submission before completion and show the data sent to the remote server and the response received.', 'ninja-forms-wh' ); ?></span>
			</td>
		</tr>

		<script type="text/html" id="tmpl-nf-wh-args">
			<#
				if ( 'new' == object_id ) {
				var x = jQuery( '.new-wh-args' ).length;
				var key_name = 'wh_args[new][' + x + '][key]';
				var field_name = 'wh_args[new][' + x + '][field]';
				var class_name = 'new-wh-args';
				} else {
				var key_name = 'wh_args[' + object_id + '][key]';
				var field_name = 'wh_args[' + object_id + '][field]';
				var class_name = '';
				}
				#>
				<div class="single-wh-args nf-wh-args">
				<span style="float:left">
				<a href="#" class="nf-wh-args-delete remove" style=""><div class="dashicons dashicons-dismiss"></div></a>
					<?php _e( 'Key', 'ninja-forms-wh' ); ?> <input type="text" name="<#= key_name #>" value="<#= args.key #>" placeholder="Key" class="nf-wh-args-key <#= class_name #>">
				</span>
					<input name="<#= field_name #>" type="text" id="" value="<#= args.field #>" class="nf-tokenize" placeholder="Field" data-key="wh_args_<#=object_id #>" data-type="all" />
				</div>
		</script>

		<?php
	}
	/**
	 * Add our custom JS
	 *
	 * @access public
	 * @since 1.0
	 * @return false
	 */
	public function add_js( $id = '' ) {
		if ( defined( 'NINJA_FORMS_JS_DEBUG' ) && NINJA_FORMS_JS_DEBUG ) {
			$suffix = '';
			$src = 'dev';
		} else {
			$suffix = '.min';
			$src = 'min';
		}
		wp_enqueue_script( 'nf-wh-admin',
			NF_WH_URL . 'assets/js/' . $src .'/admin' . $suffix . '.js',
			array( 'nf-notifications' ) );
		// Get a list of our key field args and output them as a JSON string.
		wp_localize_script( 'nf-wh-admin', 'nf_wh_args', $this->args );
	}
	/**
	 * Add our custom CSS
	 *
	 * @access public
	 * @since 1.0
	 * @return false
	 */
	public function add_css( $id = '' ) {
		wp_enqueue_style( 'nf-wh-admin',
			NF_WH_URL . 'assets/css/admin.css'
		);
	}
	/**
	 * Save admin edit screen
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function save_admin( $id = '', $data ) {
		if ( isset ( $data['wh_args'] ) && is_array( $data['wh_args'] ) ) {
			$args = nf_get_object_children( $id, 'wh_args' );
			foreach ( $args as $object_id => $vars ) {
				if ( ! isset ( $data['wh_args'][ $object_id ] ) ) {
					nf_delete_object( $object_id );
				}
			}
			if ( isset ( $data['wh_args']['new'] ) ) {
				foreach ( $data['wh_args']['new'] as $vars ) {
					$object_id = nf_insert_object( 'wh_args' );
					nf_update_object_meta( $object_id, 'key', $vars['key'] );
					nf_update_object_meta( $object_id, 'field', $vars['field'] );
					nf_add_relationship( $object_id, 'wh_args', $id, 'notification' );
				}
				unset ( $data['wh_args']['new'] );
			}
			foreach ( $data['wh_args'] as $object_id => $vars ) {
				if ( ! empty ( $object_id ) ) {
					nf_update_object_meta( $object_id, 'key', $vars['key'] );
					nf_update_object_meta( $object_id, 'field', $vars['field'] );
				}
			}
			unset( $data['wh_args'] );
		}
		return $data;
	}
	/**
	 * Filter JS vars to add our tokens.
	 *
	 * @access public
	 * @since  2.9.11
	 * @return $array JS variables
	 */
	public function filter_js_vars( $vars ) {
		$id = isset ( $_REQUEST['id'] ) ? $_REQUEST['id'] : '';
		if ( empty ( $id ) )
			return $vars;
		$this->args = nf_get_object_children( $id, 'wh_args' );
		$form_id = ( '' != $id ) ? Ninja_Forms()->notification( $id )->form_id : '';
		foreach ( $this->args as $object_id => $child ) {
			$field = $this->get_value( $object_id, 'field', $form_id );
			$vars['tokens']['wh_args_' . $object_id ] = $field;
		}
		return $vars;
	}
	/**
	 * Process our Remote Get/Post notification
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function process( $id ) {
		global $ninja_forms_processing;
		$saved_args = nf_get_object_children( $id, 'wh_args' );
		$args = array();
		foreach ( $saved_args as $object_id => $vars ) {
			$value = $this->process_setting( $object_id, 'field' );
			$args[ $vars['key'] ] = $value;
		}
		$json_encode = Ninja_Forms()->notification( $id )->get_setting( 'wh_json_encode' );
		$json_use_arg = Ninja_Forms()->notification( $id )->get_setting( 'wh_json_use_arg' );
		$json_arg = Ninja_Forms()->notification( $id )->get_setting( 'wh_json_arg' );
		$wh_remote_url = Ninja_Forms()->notification( $id )->get_setting( 'wh_remote_url' );
		$remote_method = Ninja_Forms()->notification( $id )->get_setting( 'wh_remote_method' );
		$debug = Ninja_Forms()->notification( $id )->get_setting( 'wh_debug' );
		if ( 1 == $json_encode ) {
			if ( 1 == $json_use_arg || 'get' == $remote_method ) {
				$args = array( $json_arg => json_encode( $args ) );
			} else {
				$args = json_encode( $args );
			}

		}
		if ( 'get' == $remote_method ) {
			$args = apply_filters( 'nf_remote_get_args', $args, $id );
			if ( is_array( $args ) ) {
				$args = http_build_query( $args );
			}

			if ( strpos( $wh_remote_url, '?' ) === false ) {
				$wh_remote_url .= '?';
			} else {
				if ( substr( $wh_remote_url, -1 ) !== '?' ) {
					$wh_remote_url .= '&';
				}
			}
			$wh_remote_url = apply_filters( 'nf_remote_get_url', $wh_remote_url . $args, $id );
			$response = wp_remote_get( $wh_remote_url, apply_filters( 'nf_remote_get_args', array(), $id ) );
			do_action( 'nf_remote_get', $response, $id );

			if ( 1 == $debug ) {
				echo "<pre>";
				echo "<strong>Remote URL:</strong> ";
				print_r( $wh_remote_url );
				echo "</pre>";
			}
		} else {
			$wh_remote_url = apply_filters( 'nf_remote_post_url', $wh_remote_url, $id );
			$response = wp_remote_post( $wh_remote_url, apply_filters( 'nf_remote_post_args', array( 'body' => $args ) ) );
			do_action( 'nf_remote_post', $response, $id );
			if ( 1 == $debug ) {
				echo "<pre>";
				echo "<strong>Remote URL:</strong> ";
				print_r( $wh_remote_url );
				echo "<br /><strong>Args:</strong> ";
				print_r( $args );
				echo "</pre>";
			}
		}
		if ( 1 == $debug ) {
			echo "<pre>";
			echo "<strong>Response:</strong> ";
			print_r( $response );
			echo "</pre>";
			die();
		}
	}
	/**
	 * Get our input value labels
	 *
	 * @access public
	 * @since 1.0
	 * @return string $label
	 */
	public function get_value( $id, $meta_key, $form_id ) {
		$meta_value = nf_get_object_meta_value( $id, $meta_key );
		$meta_value = explode( '`', $meta_value );
		$return = array();
		foreach( $meta_value as $val ) {
			if ( strpos( $val, 'field_' ) !== false ) {
				$val = str_replace( 'field_', '', $val );
				$label = nf_get_field_admin_label( $val, $form_id );
				if ( strlen( $label ) > 30 ) {
					$label = substr( $label, 0, 30 );
				}
				$return[] = array( 'value' => 'field_' . $val, 'label' => $label . ' - ID: ' . $val );
			} else {
				$return[] = array( 'value' => $val, 'label' => $val );
			}
		}
		return $return;
	}
	/**
	 * Explode our settings by ` and extract each value.
	 * Check to see if the setting is a field; if it is, assign the value.
	 * Run shortcodes and return the result.
	 *
	 * @access public
	 * @since 1.0
	 * @return array $setting
	 */
	public function process_setting( $id, $setting, $html = 1 ) {
		global $ninja_forms_processing;
		$setting_name = $setting;
		$setting = explode( '`', Ninja_Forms()->notification( $id )->get_setting( $setting ) );
		for ( $x = 0; $x <= count ( $setting ) - 1; $x++ ) {
			if ( strpos( $setting[ $x ], 'field_' ) !== false ) {
				if ( $ninja_forms_processing->get_field_value( str_replace( 'field_', '', $setting[ $x ] ) ) ) {
					$setting[ $x ] = $ninja_forms_processing->get_field_value( str_replace( 'field_', '', $setting[ $x ] ) );
				} else {
					$setting[ $x ] = '';
				}
			}
			if ( ! is_array ( $setting[ $x] ) ) {
				$setting[ $x ] = str_replace( '[ninja_forms_all_fields]', '[ninja_forms_all_fields html=' . $html . ']', $setting[ $x ] );
				$setting[ $x ] = do_shortcode( $setting[ $x ] );
				$setting[ $x ] = nf_parse_fields_shortcode( $setting[ $x ] );
			}
		}
		if ( 1 == count( $setting ) ) {
			$setting = $setting[0];
		}
		return apply_filters( 'nf_notification_process_setting', $setting, $setting_name, $id );
	}
}
return new NF_Action_Remote_Get_Post();