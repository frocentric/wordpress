<?php

add_action('admin_init', 'ninja_forms_register_tab_upload_settings');
function ninja_forms_register_tab_upload_settings(){
	$args = array(
		'name' => 'Upload Settings',
		'page' => 'ninja-forms-uploads',
		'display_function' => '',
		'save_function' => 'ninja_forms_save_upload_settings',
		'tab_reload' => true,
	);
	if( function_exists( 'ninja_forms_register_tab' ) ){
		ninja_forms_register_tab('upload_settings', $args);
	}
}

function nf_return_mb($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'k':
            $val /= 1024;
    }

    return $val;
}

add_action( 'admin_init', 'ninja_forms_register_upload_settings_metabox');
function ninja_forms_register_upload_settings_metabox(){
	$max_filesize = nf_return_mb( ini_get( 'upload_max_filesize' ) );

	$args = array(
		'page' => 'ninja-forms-uploads',
		'tab' => 'upload_settings',
		'slug' => 'upload_settings',
		'title' => __('Upload Settings', 'ninja-forms-uploads'),
		'settings' => array(
			array(
				'name' => 'max_filesize',
				'type' => 'text',
				'label' => __( 'Max File Size (in MB)', 'ninja-forms-uploads' ),
				'desc' => sprintf( __( 'Your server\'s maximum file size is set to %s. This setting cannot be increased beyond this value. To increase your server file size limit, please contact your host.', 'ninja-forms-uploads' ), $max_filesize ),
			),
			array(
				'name' => 'upload_error',
				'type' => 'text',
				'label' => __('File upload error message', 'ninja-forms-uploads'),
				'desc' => '',
			),
			array(
				'name' => 'adv_settings',
				'type' => '',
				'display_function' => 'ninja_forms_upload_settings_adv',
			),
		),
	);
	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}
}

function ninja_forms_upload_settings_adv(){

	$plugin_settings = nf_get_settings();

	if(isset($plugin_settings['base_upload_dir'])){
		$base_upload_dir = stripslashes($plugin_settings['base_upload_dir']);
	}else{	
		$base_upload_dir = wp_upload_dir();
		$base_upload_dir = $base_upload_dir['basedir'];
		$plugin_settings['base_upload_dir'] = $base_upload_dir;
		update_option( 'ninja_forms_settings', $plugin_settings );
	}	

	if(isset($plugin_settings['base_upload_url'])){
		$base_upload_url = stripslashes($plugin_settings['base_upload_url']);
	}else{
		$base_upload_url = wp_upload_dir();
		$base_upload_url = $base_upload_url['baseurl'];
		$plugin_settings['base_upload_url'] = $base_upload_url;
		update_option( 'ninja_forms_settings', 'ninja-forms-uploads' );
	}

	if(isset($plugin_settings['custom_upload_dir'])){
		$custom_upload_dir = stripslashes($plugin_settings['custom_upload_dir']);
	}else{
		$custom_upload_dir = '';
	}

	if(isset($plugin_settings['max_filesize'])){
		$max_filesize = $plugin_settings['max_filesize'];
	}else{
		$max_filesize = '';
	}



?>
	<div class="">
        <?php /*
		<h4><?php _e('Base Directory', 'ninja-forms-uploads');?> <img id="" class='ninja-forms-help-text' src="<?php echo NINJA_FORMS_URL;?>/images/question-ico.gif" title=""></h4>
		<label for="">
			<input type="text" class="widefat code" name="base_upload_dir" id="base_upload_dir" value="<?php echo $base_upload_dir;?>" />
		</label>		
		<span class="howto">Where should Ninja Forms place uploaded files? This should be the "first part" of the directory, including trailing slash. i.e. /var/html/wp-content/plugins/ninja-forms/uploads/</span>
		<h4><?php _e('Base URL', 'ninja-forms-uploads');?> <img id="" class='ninja-forms-help-text' src="<?php echo NINJA_FORMS_URL;?>/images/question-ico.gif" title=""></h4>
		<label for="">
			<input type="text" class="widefat code" name="base_upload_url" id="base_upload_url" value="<?php echo $base_upload_url;?>" />
		</label>

		<span class="howto">What is the URL to the base directory given above? This will be used on the backend to link to the files that have been uploaded.</span>
		<br />
		<span class="howto"><b>Please note that Ninja Forms will attempt to determine this directory, but you may need to overwrite it based on your server settings.</b></span>
		<input type="hidden" id="ninja_forms_default_base_upload_dir" value="<?php echo $default_base_upload_dir;?>">
		<input type="hidden" id="ninja_forms_default_base_upload_url" value="<?php echo $default_base_upload_url;?>">
		<input type="button" id="ninja_forms_reset_base_upload_dir" value="Reset Upload Directory">
		<br />
		*/ ?>
		<h4><?php _e('Custom Directory', 'ninja-forms-uploads');?> <img id="" class='ninja-forms-help-text' src="<?php echo NINJA_FORMS_URL;?>/images/question-ico.gif" title=""></h4>
		<label for="">
			<input type="text" class="widefat code" name="custom_upload_dir" id="" value="<?php echo $custom_upload_dir;?>" />
		</label>
		<span class="howto">
			<?php _e( 'If you want to create dynamic directories, you can put folder names in this box. You can use the following shortcodes, please include a slash at the beginning and a trailing slash', 'ninja-forms-uploads' );?>:<br /><br />
			<?php _e( 'For example: /custom/director/structure/', 'ninja-forms-uploads' );?><br><br>
			<li>%formtitle% - <?php _e('Puts in the title of the current form without any spaces', 'ninja-forms-uploads');?></li>
			<li>%username% - <?php _e('Puts in the user\'s username if they are logged in', 'ninja-forms-uploads');?>.</li>
			<li>%date% - <?php _e('Puts in the date in yyyy-mm-dd (1998-05-23) format', 'ninja-forms-uploads');?>.</li>
			<li>%month% - <?php _e('Puts in the month in mm (04) format', 'ninja-forms-uploads');?>.</li>
			<li>%day% - <?php _e('Puts in the day in dd (20) format', 'ninja-forms-uploads');?>.</li>
			<li>%year% - <?php _e('Puts in the year in yyyy (2011) format', 'ninja-forms-uploads');?>.</li>
			<li>For Example: /%formtitle%/%month%/%year%/ &nbsp;&nbsp;&nbsp; would be &nbsp;&nbsp;&nbsp; /MyFormTitle/04/2012/</li>
		</span>

		<h4><?php _e('Full Directory', 'ninja-forms-uploads');?> <img id="" class='ninja-forms-help-text' src="<?php echo NINJA_FORMS_URL;?>/images/question-ico.gif" title=""></h4>
			<span class="code"><?php echo $base_upload_dir;?><b><?php echo $custom_upload_dir;?></b></span>
		<br />
	</div>
<?php
}

function ninja_forms_save_upload_settings( $data ){
	$plugin_settings = nf_get_settings();
	foreach( $data as $key => $val ){
		if ( 'max_filesize' == $key ) {
			if ( $val > preg_replace( "/[^0-9]/", "", nf_return_mb( ini_get( 'upload_max_filesize' ) ) ) ) {
				$val = preg_replace( "/[^0-9]/", "", nf_return_mb( ini_get( 'upload_max_filesize' ) ) );
			}
			$val = preg_replace( "/[^0-9]/", "", $val );
		}
		$plugin_settings[$key] = $val;
	}
	update_option( 'ninja_forms_settings', $plugin_settings );
	$update_msg = __( 'Settings Saved', 'ninja-forms-uploads' );
	return $update_msg;
}