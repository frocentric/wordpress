<?php

//Register the Upload field
add_action('init', 'ninja_forms_register_field_upload');

function ninja_forms_register_field_upload(){
	$args = array(
		'name' => 'File Upload', //Required - This is the name that will appear on the add field button.
		'edit_options' => array( //Optional - An array of options to show within the field edit <li>. Should be an array of arrays.
			array(
				'type' => 'text', //What type of input should this be?
				'name' => 'upload_types', //What should it be named. This should always be a programmatic name, not a label.
				'label' => '<strong>' . __('Allowed File Types', 'ninja-forms-uploads') . '</strong><br/>' . __('Comma Separated List of allowed file types. An empty list means all file types are accepted. (i.e. .jpg, .gif, .png, .pdf) This is not fool-proof and can be tricked, please remember that there is always a danger in allowing users to upload files.', 'ninja-forms-uploads'), //Label to be shown before the option.
				'class' => 'widefat', //Additional classes to be added to the input element.
			),
			array(
				'type' => 'text',
				'name' => 'upload_rename',
				'label' => '<strong>' . __('Rename Uploaded File', 'ninja-forms-uploads') . '</strong><br />' . __('Advanced renaming options. If you do not want to rename the files, leave this box blank', 'ninja-forms-uploads').' <a href="#" class="ninja-forms-rename-help">' . __('Get help renaming files', 'ninja-forms-uploads') . '</a>',
				'class' => 'widefat',
			),
			array(
				'type' 		=> 'select',
				'name' 		=> 'upload_location',
				'label' 	=> __('Select where to store the uploaded files', 'ninja-forms-uploads' ),
				'options' 	=> apply_filters(
					'ninja_forms_upload_locations',
					array(
						array (
							'value' => NINJA_FORMS_UPLOADS_DEFAULT_LOCATION,
							'name' => __( 'Server', 'ninja-forms-uploads' )
						),
						array (
							'value' => 'none',
							'name' => __( 'None', 'ninja-forms-uploads' )
						)
					)
				),
				'default_value'	=> NINJA_FORMS_UPLOADS_DEFAULT_LOCATION
			),
			array(
				'type' => 'checkbox',
				'name' => 'media_library',
				'label' => __( 'Add this file to the WordPress Media Library?', 'ninja-forms-uploads' ),
			),

		),
		'edit_function' => 'ninja_forms_field_upload_edit',
		'display_function' => 'ninja_forms_field_upload_display', //Required - This function will be called to create output when a user accesses a form containing this element.
		'group' => 'standard_fields', //Optional
		'edit_label' => true, //True or False
		'edit_label_pos' => true,
		'edit_req' => true,
		'edit_custom_class' => true,
		'edit_help' => true,
		'edit_desc' => true,
		'edit_meta' => false,
		'sidebar' => 'template_fields',
		'edit_conditional' => true,
		'conditional' => array(
			'value' => array(
				'type' => 'text',
			),
		),
		'pre_process' => 'ninja_forms_field_upload_pre_process',
		'edit_sub_pre_process' => 'ninja_forms_field_upload_pre_process',
		'process' => 'ninja_forms_field_upload_process',
		'edit_sub_value' => 'nf_field_upload_edit_sub_value',
		'sub_table_value' => 'nf_field_upload_sub_table_value',
		'req_validation' => 'ninja_forms_field_upload_req_validation',
	);

	$form_row = array();

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset ( $_REQUEST['field_id'] ) ) {
		$form_row = ninja_forms_get_form_by_field_id( $_REQUEST['field_id'] );
	} else if ( isset( $_REQUEST['form_id'] ) ) {
		$form_id = $_REQUEST['form_id'];
		$form_row = ninja_forms_get_form_by_id( $form_id );
	}

	$form_data = isset ( $form_row['data'] ) ? $form_row['data'] : array();
	if( isset( $form_data['create_post'] ) AND $form_data['create_post'] == 1 ){
		$option = array(
			'type' => 'checkbox',
			'name' => 'featured_image',
			'label' => __( 'Set as featured image for the Post.', 'ninja-forms-uploads' ),
			'class' => 'ninja-forms-upload-multi',
			'width' => 'wide',
		);
		array_push( $args['edit_options'], $option );
	}

	if( function_exists( 'ninja_forms_register_field' ) ){
		ninja_forms_register_field('_upload', $args);
	}
}

add_action( 'admin_init', 'ninja_forms_field_upload_register_filters' );
function ninja_forms_field_upload_register_filters(){
	add_filter('ninja_forms_view_sub_td', 'ninja_forms_field_upload_sub_td', 10, 2);
}

function ninja_forms_field_upload_default_value($data, $field_id){
	global $ninja_forms_processing;
	$field_row = ninja_forms_get_field_by_id($field_id);
	$field_type = $field_row['type'];
	if($field_type == '_upload'){
		if( is_object( $ninja_forms_processing) AND $ninja_forms_processing->get_form_setting('doing_save')){
			$current_val = $ninja_forms_processing->get_field_value($field_id);
			$data['default_value'] = $current_val;
		}
	}
	return $data;
}

function ninja_forms_field_upload_edit($field_id, $data){
	if(isset($data['upload_multi'])){
		$upload_multi = $data['upload_multi'];
	}else{
		$upload_multi = '';
	}
	if(isset($data['upload_multi_count'])){
		$upload_multi_count = $data['upload_multi_count'];
	}else{
		$upload_multi_count = '';
	}

	?>
	<div class="description description-thin">
		<input type="hidden" name="ninja_forms_field_<?php echo $field_id;?>[upload_multi]" value="0">
		<input type="checkbox" id="field_<?php echo $field_id;?>_upload_multi" name="ninja_forms_field_<?php echo $field_id;?>[upload_multi]" value="1" class="ninja-forms-upload-multi" <?php checked($upload_multi, 1);?>>
		<label for="field_<?php echo $field_id;?>_upload_multi">
			<?php _e( 'Allow the user to upload multiple files.' , 'ninja-forms-uploads'); ?>
		</label>
	</div>

	<div class="description description-thin" style="" id="field_<?php echo $field_id;?>_upload_multi_count_label">
		<label for="field_<?php echo $field_id;?>_upload_multi_count">
			<input type="text" id="field_<?php echo $field_id;?>_upload_multi_count" name="ninja_forms_field_<?php echo $field_id;?>[upload_multi_count]" value="<?php echo $upload_multi_count;?>" class="widefat">
			<?php _e( 'How many files can be uploaded?' , 'ninja-forms-uploads'); ?><br />
		</label>
	</div>
	<?php
}

/**
 * This is the main display function that will be called on the front-end when a user is filling out a form.
 *
 * @param int $field_id - ID number of the field that is currently being displayed.
 * @param array $data - array of field data as it has been processed to this point.
 */

function ninja_forms_field_upload_display( $field_id, $data ){
	global $ninja_forms_loading, $ninja_forms_processing;

	$plugin_settings = get_option('ninja_forms_settings');
	if(isset($plugin_settings['max_filesize'])){
		$max_filesize = $plugin_settings['max_filesize'] * 1048576;
	}else{
		$max_filesize = 2097152;
	}

	if(isset($data['upload_multi']) AND $data['upload_multi'] == 1){
		$upload_multi = 'multi';
	}else{
		$upload_multi = '';
	}

	if(isset($data['upload_multi_count'])){
		$upload_multi_count = $data['upload_multi_count'];
	}else{
		$upload_multi_count = '';
	}


	$user_file_name = '';
	$file_name = '';
	$file_path = '';
	$file_url = '';
	$prefill = false;

	if( is_object( $ninja_forms_processing) AND $ninja_forms_processing->get_error('upload_'.$field_id)){
		$field_error = true;
	}else{
		$field_error = false;
	}

	$user_value = '';

	if(isset($data['default_value']) AND !empty($data['default_value'])){
		$user_value = $data['default_value'];
		$prefill = true;
	}else if( is_object( $ninja_forms_processing) AND $ninja_forms_processing->get_field_value($field_id) AND $ninja_forms_processing->get_all_errors()){
		$user_value = $ninja_forms_processing->get_field_value($field_id);
		$prefill = true;
	}

	if ( is_array ( $user_value ) ) {
		$tmp = false;
		foreach( $user_value as $key => $val ) {
			if ( isset ( $val['file_name'] ) and !empty ( $val['file_name'] ) ) {
				$tmp = true;
			}
		}
		if ( !$tmp ) {
			$prefill = false;
		}		
	} else if ( is_string ( $user_value ) ) {
		$filename = basename( $user_value );
		$tmp_array = array(
			ninja_forms_random_string(5) => array(
				'complete' => 1,
				'user_file_name' => $filename,
				'file_name' => $filename,
				'file_url' => $user_value,
			),
		);
		$user_value = $tmp_array;
	}

	//var_dump ( $user_value );

	if(count($user_value) > 1){
		$str_label = __('Files', 'ninja-forms-uploads');
	}else{
		$str_label = __('File', 'ninja-forms-uploads');
	}

	if( $prefill AND !$field_error ){

		// Loop through our files array and allow the user to replace each on individually.
		// This code outputs a different file input field for each. Every field is also accompanied by hidden inputs.
		if( is_array( $user_value ) AND !empty( $user_value ) ){
			if( $upload_multi == 'multi'){
				?>
				<ul style="list-style:none;">
				<?php
				foreach( $user_value as $key => $val ){
					if( isset( $val['complete'] ) ){
						$complete = $val['complete'];
					}else{
						$complete = 1;
					}
					
					if ( !isset ( $val['upload_id'] ) ) {
						$val['upload_id'] = '';
					}

					// Output the accompanying file data that will be accessible in the extra_values during processing.
					?>
					<li id="ninja_forms_file_upload_<?php echo $field_id;?>_<?php echo $key;?>_li">
						<input type="hidden" name="_upload_<?php echo $field_id;?>[<?php echo $key;?>][user_file_name]" value="<?php echo $val['user_file_name'];?>">
						<input type="hidden" name="_upload_<?php echo $field_id;?>[<?php echo $key;?>][file_name]" value="<?php echo $val['file_name'];?>">
						<input type="hidden" name="_upload_<?php echo $field_id;?>[<?php echo $key;?>][file_path]" value="<?php echo $val['file_path'];?>">
						<input type="hidden" name="_upload_<?php echo $field_id;?>[<?php echo $key;?>][file_url]" value="<?php echo $val['file_url'];?>">
						<input type="hidden" name="_upload_<?php echo $field_id;?>[<?php echo $key;?>][complete]" value="<?php echo $complete;?>">
						<input type="hidden" name="_upload_<?php echo $field_id;?>[<?php echo $key;?>][changed]" value="0">
						<input type="hidden" name="_upload_<?php echo $field_id;?>[<?php echo $key;?>][upload_id]" value="<?php echo $val['upload_id'];?>">

						<a href="#" class="ninja-forms-delete-file-upload" id="ninja_forms_delete_file_upload_<?php echo $field_id;?>_<?php echo $key;?>">X</a> -
						<a href="<?php echo $val['file_url'];?>" target="_blank"><?php echo $val['user_file_name'];?></a> - <a href="#" name="" id="ninja_forms_change_file_upload_<?php echo $field_id;?>_<?php echo $key;?>" class="ninja-forms-change-file-upload"><?php _e('Change This File', 'ninja-forms-uploads');?></a>
						<span id="ninja_forms_file_upload_<?php echo $field_id;?>_<?php echo $key;?>" style="display:none;">
							<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_filesize;?>">
							<input type="file" name="ninja_forms_field_<?php echo $field_id;?>[<?php echo $key;?>]" id="ninja_forms_field_<?php echo $field_id;?>-<?php echo $key;?>" rel="<?php echo $field_id;?>" >
							<input type="hidden" name="ninja_forms_field_<?php echo $field_id;?>[<?php echo $key;?>]" value=""  rel="<?php echo $field_id;?>" >
						</span>

					</li>
					<?php
				}
					?>
					<li>
					<?php
						_e( 'New Upload', 'ninja-forms-uploads' );
						echo ':';
						?>
						<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_filesize;?>">
						<input type="file" name="ninja_forms_field_<?php echo $field_id;?>[new][]" id="ninja_forms_field_<?php echo $field_id;?>" class="<?php echo $upload_multi;?>" maxlength="<?php echo $upload_multi_count;?>"  rel="<?php echo $field_id;?>" >
						<input type="hidden" name="ninja_forms_field_<?php echo $field_id;?>[new][]" value=""  rel="<?php echo $field_id;?>" >
					<li>
				</ul>
				<?php
			}else{
				?>
				<ul style="list-style:none;">
				<?php

				foreach( $user_value as $key => $val ){
					if( isset( $val['complete'] ) ){
						$complete = $val['complete'];
					}else{
						$complete = 1;
					}
					if ( isset ( $val['upload_id'] ) ) {
						$upload_id = $val['upload_id'];
					} else {
						$upload_id = '';
					}
					?>
					<li>
						<div>
							<a href="<?php echo $val['file_url'];?>"><?php echo $val['user_file_name'];?></a>
						</div>
						<input type="hidden" name="_upload_<?php echo $field_id;?>[<?php echo $key;?>][user_file_name]" value="<?php echo $val['user_file_name'];?>">
						<input type="hidden" name="_upload_<?php echo $field_id;?>[<?php echo $key;?>][file_name]" value="<?php echo $val['file_name'];?>">
						<input type="hidden" name="_upload_<?php echo $field_id;?>[<?php echo $key;?>][file_path]" value="<?php echo $val['file_path'];?>">
						<input type="hidden" name="_upload_<?php echo $field_id;?>[<?php echo $key;?>][file_url]" value="<?php echo $val['file_url'];?>">
						<input type="hidden" name="_upload_<?php echo $field_id;?>[<?php echo $key;?>][complete]" value="<?php echo $complete;?>">
						<input type="hidden" name="_upload_<?php echo $field_id;?>[<?php echo $key;?>][changed]" value="0">
						<input type="hidden" name="_upload_<?php echo $field_id;?>[<?php echo $key;?>][upload_id]" value="<?php echo $upload_id;?>">
					</li>
					<li>
						<?php

						_e( 'Change Upload', 'ninja-forms-uploads' );
						echo ':';
						?>


						<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_filesize;?>">
						<input type="file" name="ninja_forms_field_<?php echo $field_id;?>[<?php echo $key;?>]" id="ninja_forms_field_<?php echo $field_id;?>" class="<?php echo $upload_multi;?>" maxlength="<?php echo $upload_multi_count;?>"  rel="<?php echo $field_id;?>" >
						<input type="hidden" name="ninja_forms_field_<?php echo $field_id;?>[<?php echo $key;?>]" value=""  rel="<?php echo $field_id;?>" >
					</li>
					<?php
				}
				?>
				</ul>
				<?php
			}
		}
	}else{
		?>

		<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_filesize;?>">
		<input type="file" name="ninja_forms_field_<?php echo $field_id;?>[new][]" id="ninja_forms_field_<?php echo $field_id;?>" class="<?php echo $upload_multi;?>" maxlength="<?php echo $upload_multi_count;?>"  rel="<?php echo $field_id;?>" >
		<input type="hidden" name="ninja_forms_field_<?php echo $field_id;?>[new][]" value=""  rel="<?php echo $field_id;?>" >
		<input type="hidden" name="ninja_forms_field_<?php echo $field_id;?>[]" value="" rel="<?php echo $field_id;?>">
		<?php
	}
}

/**
 * Output our field on the edit submission page
 *
 * @since 1.3.4
 * @return void
 */
function nf_field_upload_edit_sub_value( $field_id, $user_value ) {
	if ( is_array ( $user_value ) && ! empty ( $user_value ) ) {
		$user_value = NF_File_Uploads()->normalize_submission_value( $user_value );
		foreach ( $user_value as $key => $file ) {
			$file_url = ninja_forms_upload_file_url( $file );
			?>
			<input type="hidden" name="fields[<?php echo $field_id; ?>][<?php echo $key;?>][user_file_name]" value="<?php echo $file['user_file_name'];?>">
			<input type="hidden" name="fields[<?php echo $field_id; ?>][<?php echo $key;?>][file_name]" value="<?php echo $file['file_name'];?>">
			<input type="hidden" name="fields[<?php echo $field_id; ?>][<?php echo $key;?>][file_path]" value="<?php echo $file['file_path'];?>">
			<input type="hidden" name="fields[<?php echo $field_id; ?>][<?php echo $key;?>][complete]" value="<?php echo $file['complete'];?>">
			<input type="hidden" name="fields[<?php echo $field_id; ?>][<?php echo $key;?>][changed]" value="0">
			<input type="hidden" name="fields[<?php echo $field_id; ?>][<?php echo $key;?>][upload_id]" value="<?php echo $file['upload_id'];?>">
			<input type="hidden" name="fields[<?php echo $field_id; ?>][<?php echo $key;?>][file_url]" value="<?php echo $file['file_url'];?>">
			<?php if( isset( $file['upload_location'] ) ) : ?>
				<input type="hidden" name="fields[<?php echo $field_id; ?>][<?php echo $key;?>][upload_location]" value="<?php echo $file['upload_location'];?>">
			<?php endif; ?>
			<?php if( isset( $file['external_path'] ) ) : ?>
				<input type="hidden" name="fields[<?php echo $field_id; ?>][<?php echo $key;?>][external_path]" value="<?php echo $file['external_path'];?>">
			<?php endif; ?>
			<?php if( isset( $file['external_filename'] ) ) : ?>
				<input type="hidden" name="fields[<?php echo $field_id; ?>][<?php echo $key;?>][external_filename]" value="<?php echo $file['external_filename'];?>">
			<?php endif; ?>

			<a href="<?php echo $file_url; ?>" target="_blank"><?php _e( 'View', 'ninja-forms-uploads' ); ?></a> <input type="text" value="<?php echo $file_url; ?>">
			<br />
			<?php
		}
	}
}

/**
 * Filter the user value for editing submissions with a file upload field.
 *
 * @since 1.3.4
 * @return array $user_value
 */
function nf_field_upload_filter_edit_sub_value( $user_value, $field_id, $sub_id ) {
	$form_id = Ninja_Forms()->sub( $sub_id )->form_id;
	if ( isset ( Ninja_Forms()->form( $form_id )->fields[ $field_id ]['type'] ) && Ninja_Forms()->form( $form_id )->fields[ $field_id ]['type'] == '_upload' ) {
		if ( is_array ( $user_value ) ) {
			$user_value = NF_File_Uploads()->normalize_submission_value( $user_value );
				foreach ( $user_value as $key => $file ) {
				if ( empty ( $user_value[ $key ]['file_url'] ) ) {
					unset ( $user_value[ $key ] );
				}
			}
		}
	}

	return $user_value;
}

add_filter( 'nf_edit_sub_user_value', 'nf_field_upload_filter_edit_sub_value', 10, 3 );

/**
 * Filter the user value for the submissions table.
 *
 * @since 1.3.4
 * @return void
 */
function nf_field_upload_sub_table_value( $field_id, $user_value, $field ) {
	if ( $field['type'] == '_upload' ) {
		if ( is_array ( $user_value ) && ! empty ( $user_value ) ) {
			$x = 0;
			$user_value = NF_File_Uploads()->normalize_submission_value( $user_value );
			foreach ( $user_value as $key => $file ) {
				if ( $x > 4 ) {
					break;
				}
				$file_url = ninja_forms_upload_file_url( $file );
				if ( isset ( $file['file_url'] ) ) {
					?>
					<a href="<?php echo $file_url; ?>" target="_blank"><?php echo basename( $file['file_url'] ); ?></a>
					<br />
					<?php					
				}

				$x++;
			}
		}
	}
}

/**
 * Filter our submission editing table if we are using the front-end editor extension
 *
 * @since 1.3.4
 * @return string $link
 */
function nf_field_upload_fee_sub_table( $user_value, $field_id, $field_row ) {
	if ( $field_row['type'] == '_upload' ) {
		if ( is_array ( $user_value ) && ! empty ( $user_value ) ) {
			$x = 0;
			$return = '';
			foreach ( $user_value as $key => $file ) {
				if ( $x > 4 ) {
					break;
				}
				$file_url = ninja_forms_upload_file_url( $file );
				$return .= '<a href="' . $file_url . '" target="_blank">' . basename( $file['file_url'] ) . '</a><br />';
				$x++;
			}
			$user_value = $return;
		}
	}
	
	return $user_value;
}

add_filter( 'nf_edit_sub_table_value', 'nf_field_upload_fee_sub_table', 10, 3 );